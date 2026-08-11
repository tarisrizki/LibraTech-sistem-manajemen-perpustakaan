# Task Breakdown — 12 Subagent Orchestration
## LibraTech

Dokumen ini memecah pekerjaan menjadi 12 unit kerja untuk dieksekusi subagent, dengan dependency yang jelas supaya orchestrator tahu urutan/paralelisasi yang aman.

**Legenda status dependency:**
- 🔴 **Blocking** — tidak boleh mulai sebelum dependency selesai
- 🟡 **Soft dependency** — bisa mulai paralel tapi ada titik integrasi yang butuh sinkronisasi

---

## Dependency Graph (Ringkas)

```
Agent 01 (Setup & Environment)
        │
        ▼
Agent 02 (Database & Migration)
        │
        ├──────────────┬──────────────┐
        ▼              ▼              ▼
Agent 03           Agent 04       Agent 05
(Auth & Roles)    (Book/Category) (Loan Business Logic)
        │              │              │
        └──────┬───────┴──────┬───────┘
               ▼               ▼
        Agent 06            Agent 07
        (Livewire UI)     (RESTful API)
               │               │
               ▼               ▼
        Agent 08 (Caching — Redis)
               │
               ▼
        Agent 09 (API Docs — Scramble)
               │
    ┌──────────┼──────────┐
    ▼          ▼          ▼
Agent 10   Agent 11    Agent 12
(Testing) (Code QA)  (Deploy & Docs)
```

---

## Agent 01 — Project Setup & Environment

**Dependency:** Tidak ada (task pertama)

**Scope:**
- Install Laravel 13 dengan `laravel new libratech --using=laravel/livewire-starter-kit`
- Setup koneksi Supabase PostgreSQL sesuai `02-DATABASE-DESIGN.md` §7 (termasuk buat schema `app`, set `search_path`)
- Setup Redis lokal (untuk cache & session driver)
- Install & konfigurasi: `dedoc/scramble`, Larastan (`nunomaduro/larastan`), Pint (sudah bawaan Laravel), Pest (biasanya sudah bawaan starter kit — verifikasi)
- Buat `.env.example` yang lengkap dan akurat (bukan copy `.env` asli yang berisi credential asli)
- Setup `config/database.php` untuk `search_path` custom
- Commit awal: struktur project bersih, `composer.json`/`package.json` rapi

**Output yang divalidasi orchestrator:**
- `php artisan about` jalan tanpa error
- Koneksi ke Supabase berhasil (`php artisan migrate` — walau migration masih kosong/default — sukses)
- `redis-cli ping` merespons `PONG`

---

## Agent 02 — Database Schema & Migration

**Dependency:** 🔴 Agent 01

**Scope:**
- Implementasi seluruh migration sesuai `02-DATABASE-DESIGN.md` §3–§5 (users role column, categories, books, loans)
- Implementasi seluruh Seeder sesuai §6 (data realistis, bukan Lorem Ipsum)
- Buat Eloquent Model dengan relasi eksplisit (`Category::books()`, `Book::category()`, `Book::loans()`, `User::loans()`), termasuk `$fillable`, `$casts` (terutama `status` sebagai enum cast, timestamp columns)
- Buat native PHP Enum `LoanStatus` dan `UserRole` sesuai `04-STYLE-GUIDE.md` §3

**Output yang divalidasi orchestrator:**
- `php artisan migrate:fresh --seed` sukses tanpa error
- Query manual (`php artisan tinker`) membuktikan relasi berfungsi (`Book::first()->category`, `User::first()->loans`)

---

## Agent 03 — Auth, Roles & Authorization

**Dependency:** 🔴 Agent 02

**Scope:**
- Sesuaikan auth flow starter kit (register/login/logout) agar `role` ter-set default `member` saat register
- Buat `BookPolicy` dan `LoanPolicy` sesuai permission matrix di `01-PRD.md` §4 — TIDAK boleh hardcode role check di controller/Livewire
- Middleware/route grouping untuk area admin (`/admin/*`) yang cek Gate/Policy, bukan cek string role manual di tiap route
- Halaman profil: update nama/email, ganti password dengan verifikasi password lama

**Output yang divalidasi orchestrator:**
- User dengan role `member` mendapat 403 saat akses route admin
- Policy test (lihat Agent 10) membuktikan setiap kombinasi role×aksi di permission matrix

---

## Agent 04 — Modul Buku & Kategori (Admin CRUD)

**Dependency:** 🔴 Agent 02, 🟡 Agent 03 (butuh policy untuk proteksi akses)

**Scope:**
- Livewire component CRUD kategori (dengan validasi tidak bisa hapus kategori yang punya buku)
- Livewire component CRUD buku (dengan upload cover, validasi ISBN unique, soft delete)
- Form Request terpisah untuk create dan update (`StoreBookRequest`, `UpdateBookRequest`) — bukan 1 request digabung dengan validasi kondisional yang membingungkan
- Katalog publik: list, search, filter, sort sesuai FR-4

**Output yang divalidasi orchestrator:**
- CRUD buku & kategori berfungsi penuh dari UI
- Percobaan hapus kategori yang masih punya buku menghasilkan pesan error yang jelas, bukan 500 error

---

## Agent 05 — Modul Peminjaman (Business Logic Inti)

**Dependency:** 🔴 Agent 02, 🟡 Agent 03

**Scope — INI MODUL PALING KRITIKAL, HARUS PALING HATI-HATI:**
- Action class `RequestLoanAction`: validasi stok tersedia, validasi user tidak sedang meminjam buku sama, validasi limit 3 loan aktif — sesuai `01-PRD.md` FR-5
- Action class `ApproveLoanAction`: decrement stock, set `due_at` (+7 hari dari waktu approve), set status `approved`
- Action class `RejectLoanAction`: set status `rejected` dengan `rejection_reason`
- Action class `ReturnLoanAction`: increment stock, set `returned_at`, status `returned`
- Scheduled Command `MarkOverdueLoans`: jalan harian, cari loan `approved` dengan `due_at` lewat, ubah status jadi `overdue`
- Livewire component: dashboard member (ajukan pinjam, lihat riwayat), dashboard admin (approve/reject/return)

**Output yang divalidasi orchestrator:**
- Skenario penuh bisa didemokan: member ajukan → admin approve → stok berkurang → admin tandai kembali → stok bertambah
- Race condition dasar tertangani: 2 approve simultan untuk stok=1 tidak menghasilkan stok minus (pakai DB transaction + lock jika perlu, `lockForUpdate()`)

---

## Agent 06 — Livewire Frontend / UI

**Dependency:** 🔴 Agent 04, 🔴 Agent 05

**Scope:**
- Layout utama (navbar dengan menu sesuai role, footer)
- Styling Tailwind konsisten (tidak asal-asalan campur warna/spacing tanpa sistem)
- Halaman katalog publik yang enak dipakai (empty state saat search tidak ketemu, loading state Livewire)
- Dashboard member & admin yang jelas secara UX (status peminjaman pakai badge warna berbeda per status)
- **Karena Hermes tidak punya vision model:** semua styling harus divalidasi lewat deskripsi teks/DOM structure yang jelas, bukan "lihat hasilnya dan sesuaikan" — pastikan class Tailwind yang dipakai konsisten dengan design token yang didefinisikan di awal (lihat catatan di §Prompt Orchestrator terkait ini)

**Output yang divalidasi orchestrator:**
- Semua halaman FR-1 s/d FR-6 punya UI fungsional, tidak ada halaman kosong/placeholder "Coming Soon"

---

## Agent 07 — RESTful API

**Dependency:** 🔴 Agent 05

**Scope:**
- Setup Sanctum untuk API token auth
- Controller API di `app/Http/Controllers/Api/V1/` sesuai daftar endpoint `03-API-DOCUMENTATION-PLAN.md` §3
- API Resource untuk setiap response (`BookResource`, `LoanResource`, `CategoryResource`) — response mengikuti contract di PRD §7, tidak pernah expose kolom internal (`deleted_at`, dll)
- Reuse Action class dari Agent 05 untuk endpoint loan (jangan duplikasi logic approve/reject antara web dan API)
- Rate limiting sesuai `03-API-DOCUMENTATION-PLAN.md` §2

**Output yang divalidasi orchestrator:**
- Semua endpoint di §3 bisa dites via `curl`/Postman dan mengembalikan response sesuai contract
- Endpoint admin mengembalikan 403 untuk token member

---

## Agent 08 — Caching Layer (Redis)

**Dependency:** 🔴 Agent 04, 🔴 Agent 07

**Scope:**
- Set `SESSION_DRIVER=redis`, `CACHE_STORE=redis` di konfigurasi
- Implementasi cache untuk `GET /api/v1/books` dan `GET /api/v1/categories` (key harus unik per kombinasi query param, TTL 15 menit — sesuai FR-8)
- Model Observer (`BookObserver`, `CategoryObserver`) yang invalidate cache terkait saat create/update/delete
- Verifikasi tidak ada stale cache: setelah admin update buku, `GET /api/v1/books` langsung reflect data baru tanpa tunggu TTL habis

**Output yang divalidasi orchestrator:**
- `redis-cli KEYS "*"` menunjukkan cache key yang sesuai pola setelah endpoint diakses
- Test yang membuktikan cache invalidation (lihat Agent 10)

---

## Agent 09 — API Documentation (Scramble)

**Dependency:** 🔴 Agent 07

**Scope:**
- Install & konfigurasi Scramble sesuai `03-API-DOCUMENTATION-PLAN.md` §1
- Tambahkan PHPDoc singkat di setiap method controller API yang belum cukup deskriptif otomatis
- Grouping endpoint per tag (Auth, Books, Categories, Loans)
- Export `openapi.yaml` ke root repo
- Restrict akses `/docs/api` di production (gate `viewApiDocs`)

**Output yang divalidasi orchestrator:**
- `/docs/api` menampilkan semua endpoint dengan contoh request/response yang benar, bukan skeleton kosong

---

## Agent 10 — Automated Testing (Pest)

**Dependency:** 🟡 Berjalan paralel sejak Agent 03 selesai, terus berlanjut sampai semua modul jadi — bukan task di akhir

**Scope:**
- Feature test untuk setiap Policy (permission matrix penuh)
- Unit test untuk setiap Action class di Agent 05 (happy path + edge case kegagalan)
- Feature test untuk setiap endpoint API (status code, response shape, auth requirement)
- Test cache invalidation (Agent 08)
- Test race condition approve loan (stok=1, 2 request bersamaan)

**Output yang divalidasi orchestrator:**
- `php artisan test` semua hijau
- Tidak ada test yang cuma `expect(true)->toBeTrue()` — setiap test membuktikan behavior nyata

---

## Agent 11 — Code Quality & Static Analysis

**Dependency:** 🟡 Berjalan sebagai gate berkala, final pass setelah semua modul selesai

**Scope:**
- Jalankan `./vendor/bin/pint` di seluruh codebase, fix semua deviasi
- Jalankan `./vendor/bin/phpstan analyse`, perbaiki semua error di level yang ditargetkan (lihat `04-STYLE-GUIDE.md` §1)
- Audit manual terhadap checklist Anti-AI-Slop di `04-STYLE-GUIDE.md` §4 — cari sisa TODO, comment generik, duplikasi logic, nama variable tidak jelas
- Review N+1 query di semua Livewire component & API controller (pakai `DB::listen` atau Laravel Debugbar saat development, eager loading yang kurang harus diperbaiki)

**Output yang divalidasi orchestrator:**
- Laporan singkat: jumlah issue ditemukan, jumlah diperbaiki, sisa (jika ada, harus dijustifikasi kenapa tidak diperbaiki)

---

## Agent 12 — Deployment & Final Documentation

**Dependency:** 🔴 Semua agent di atas selesai

**Scope:**
- Deploy aplikasi ke platform hosting (Railway/Render, sesuaikan ketersediaan)
- Setup environment variable production (koneksi Supabase, Redis production/managed, `APP_ENV=production`, `APP_DEBUG=false`)
- Tulis `README.md` final: deskripsi project, screenshot alur utama (ambil dari hasil aplikasi berjalan, bukan mockup), tech stack, cara install lokal step-by-step, link demo live, link dokumentasi API
- Tulis singkat **Architecture Decision Record (ADR)** untuk 2-3 keputusan penting (kenapa Action class bukan Service class, kenapa Supabase, kenapa Scramble bukan L5-Swagger) — bukti kemampuan justifikasi teknis, bukan cuma eksekusi
- Final check terhadap seluruh Definition of Done di `01-PRD.md` §9

**Output yang divalidasi orchestrator:**
- Aplikasi live dan bisa diakses publik
- README bisa diikuti orang lain dari nol untuk menjalankan project secara lokal
