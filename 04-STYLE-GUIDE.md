# Style Guide & Engineering Standards
## LibraTech

Dokumen ini adalah kontrak wajib untuk semua kode yang masuk ke repository ini — baik ditulis manusia maupun AI agent. Tidak ada pengecualian "karena buru-buru" atau "nanti dibenerin belakangan".

---

## 1. Code Style — PHP

- **Formatter:** Laravel Pint, konfigurasi default (`preset: laravel`). Jalankan `./vendor/bin/pint` sebelum setiap commit. `./vendor/bin/pint --test` harus lulus di CI.
- **Static Analysis:** Larastan, minimal level 5 di awal project, naik ke level 8 begitu modul inti selesai. Tidak boleh ada `@phpstan-ignore` tanpa komentar alasan di baris yang sama.
- **Strict types:** Setiap file PHP baru wajib `declare(strict_types=1);` di baris pertama setelah `<?php`.
- **Type hint:** Semua parameter method dan return type WAJIB dideklarasikan eksplisit. Tidak ada `mixed` kecuali benar-benar tidak bisa dihindari (dan harus dikomentari kenapa).

---

## 2. Konvensi Penamaan

| Elemen | Konvensi | Contoh |
|---|---|---|
| Class | PascalCase | `LoanController`, `BookPolicy` |
| Method & variable | camelCase | `approveLoan()`, `$activeLoan` |
| Migration | snake_case, deskriptif | `create_loans_table`, bukan `update1` |
| Route name | kebab-case dengan dot notation | `admin.loans.approve` |
| Blade/Livewire view | kebab-case | `books.show`, `loans.index` |
| Env variable | SCREAMING_SNAKE_CASE | `REDIS_CACHE_TTL` |
| DB table | snake_case, plural | `books`, `loan_histories` (jika ada) |
| DB column | snake_case | `published_year`, `approved_at` |

**Larangan penamaan:**
- Tidak ada nama generik: `data`, `temp`, `handler2`, `Helper`, `Utils` sebagai nama class tanpa konteks.
- Tidak ada nama yang menyesatkan: variable `$books` harus benar-benar collection of `Book`, bukan array campuran.

---

## 3. Struktur Folder (App Layer)

```
app/
├── Actions/          # Single-purpose action classes untuk business logic kompleks
│   └── Loans/
│       ├── ApproveLoanAction.php
│       ├── RejectLoanAction.php
│       └── RequestLoanAction.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/           # Controller khusus API
│   │   └── Web/              # Controller khusus web (jika tidak full Livewire)
│   ├── Requests/              # Form Request per aksi, bukan digabung
│   ├── Resources/             # API Resource untuk transformasi response
│   └── Middleware/
├── Livewire/
│   ├── Books/
│   ├── Loans/
│   └── Admin/
├── Models/
├── Policies/
├── Observers/                 # Cache invalidation, audit log
└── Enums/                     # LoanStatus, UserRole (native PHP enum, bukan string const)
```

**Aturan:**
- Business logic yang lebih dari validasi sederhana (approve loan, request loan dengan cek stok+limit) HARUS masuk ke **Action class**, bukan ditulis panjang di controller atau Livewire component. Controller/Livewire component hanya orkestrasi: terima input → panggil action → return response.
- Gunakan native PHP `enum` untuk `LoanStatus` dan `UserRole`, bukan string magic di banyak tempat.

```php
enum LoanStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Returned = 'returned';
    case Overdue = 'overdue';
}
```

---

## 4. Aturan Anti-"AI Slop" (Wajib Dipatuhi Semua Agent)

Ini bagian paling penting. Kode yang melanggar ini dianggap **belum selesai**, bukan "selesai tapi kurang rapi".

1. **Tidak ada comment yang menjelaskan hal yang sudah jelas dari kode itu sendiri.**
   - ❌ `// increment stock by 1` di atas `$book->increment('stock');`
   - ✅ Komentar hanya untuk *kenapa*, bukan *apa*: `// Stock dikembalikan sebelum status diubah, supaya jika update status gagal, stok tidak inkonsisten`

2. **Tidak ada TODO/FIXME/placeholder yang dibiarkan di kode final.** Kalau ada scope yang sengaja belum dikerjakan, catat di `TASKS.md`/issue tracker, bukan comment ditinggal di kode.

3. **Tidak ada nama variable/method hasil auto-generate yang tidak masuk akal**, contoh: `$data1`, `$result2`, `handleStuff()`. Setiap nama harus deskriptif terhadap domain (Loan, Book, bukan generic CRUD term semata).

4. **Tidak ada duplikasi logic yang di-copy-paste antar Controller/Livewire component.** Kalau logic yang sama dipakai 2+ tempat, ekstrak ke Action class atau method di Model/Service.

5. **Tidak ada over-abstraction untuk masalah yang simpel.** Jangan bikin Repository pattern + Interface untuk setiap Model kalau Eloquent langsung sudah cukup jelas dan testable. Kompleksitas harus proporsional dengan kebutuhan riil project ini — bukan menunjukkan "tahu banyak pattern".

6. **Tidak ada validasi yang di-skip dengan asumsi "pasti aman".** Semua input dari user (form, API body, query param) wajib lewat Form Request atau validasi eksplisit, tidak ada exception untuk "cuma internal admin panel".

7. **Setiap method publik yang mengandung business rule (bukan getter/setter sederhana) harus punya test yang membuktikan behavior-nya** — bukan test kosong (`assertTrue(true)`) atau test yang cuma mengecek halaman tidak 500.

8. **Commit message harus deskriptif dan mengikuti Conventional Commits** (lihat §6). Tidak ada commit dengan pesan `"update"`, `"fix bug"`, `"wip"`, `"asd"`.

9. **Tidak boleh mengarang nama method/class dari package eksternal.** Jika ragu API sebuah package (Sanctum, Scramble, Livewire, dll), agent WAJIB verifikasi lewat Context7 atau dokumentasi resmi sebelum menulis kode — bukan menebak berdasarkan pola umum yang mungkin sudah deprecated.

---

## 5. Testing Standard

- Framework: **Pest**
- Struktur: `tests/Feature` untuk behavior end-to-end (HTTP request → response/DB state), `tests/Unit` untuk logic terisolasi (Action class, Enum, Policy)
- Setiap Action class kritikal wajib punya test untuk: **happy path**, minimal **1 edge case kegagalan** (stok habis, limit tercapai, dsb)
- Naming test: deskriptif dalam bahasa yang jelas, contoh:
```php
it('rejects loan request when book stock is zero', function () {
    // ...
});

it('limits member to maximum 3 active loans', function () {
    // ...
});
```
- Tidak ada test yang di-skip (`->skip()`) tanpa catatan alasan dan siapa yang akan follow up.

---

## 6. Git & Commit Convention

**Format:** [Conventional Commits](https://www.conventionalcommits.org/)

```
<type>(<scope>): <deskripsi singkat, present tense>

[optional body — kenapa perubahan ini dibuat, bukan cuma apa]
```

**Type yang dipakai:** `feat`, `fix`, `refactor`, `test`, `docs`, `chore`, `style`, `perf`

**Contoh baik:**
```
feat(loans): add stock validation before approving loan request

Prevent race condition where two pending loans for the same book
could both be approved when stock is only 1.
```

**Contoh yang DILARANG:**
```
update
fix
wip loan stuff
```

**Branch naming:** `feature/loan-approval-flow`, `fix/cache-invalidation-on-book-update`

**Aturan commit granularity:** 1 commit = 1 perubahan logis yang bisa dijelaskan dalam 1 kalimat. Jangan gabung "tambah fitur X + fix bug Y + rapikan formatting" dalam 1 commit.

---

## 7. Definition of "Selesai" untuk Setiap Task

Sebuah task/fitur baru dianggap selesai HANYA jika semua ini terpenuhi:
- [ ] Kode lulus `pint --test` dan `phpstan analyse`
- [ ] Ada test yang membuktikan behavior utama (bukan cuma "tidak error")
- [ ] Tidak ada pelanggaran §4 (Anti-AI-Slop) di atas
- [ ] Commit message sesuai §6
- [ ] Jika melibatkan API endpoint baru: sudah terdokumentasi otomatis lewat Scramble dan dicek manual tampilannya di `/docs/api`
