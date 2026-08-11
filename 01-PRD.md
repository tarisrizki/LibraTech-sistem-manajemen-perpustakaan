# Product Requirements Document (PRD)
## LibraTech — Sistem Manajemen Perpustakaan

| | |
|---|---|
| **Versi Dokumen** | 1.0 |
| **Status** | Final — siap untuk eksekusi |
| **Tujuan** | Project portofolio developer Laravel (Fresh Graduate level) |
| **Target Pembaca** | AI coding agent (orchestrator + subagents), reviewer teknis |

---

## 1. Ringkasan Eksekutif

LibraTech adalah sistem manajemen perpustakaan berbasis web yang memungkinkan admin mengelola koleksi buku dan anggota mengajukan peminjaman secara online. Project ini dibangun untuk mendemonstrasikan kompetensi teknis sesuai requirement posisi Laravel Developer (Fresh Graduate), dengan standar kode dan arsitektur setara aplikasi production, bukan tutorial/demo project.

**Prinsip non-negosiasi:**
- Tidak ada kode placeholder, dummy logic yang dibiarkan, atau komentar generik yang tidak menjelaskan apa-apa.
- Setiap keputusan arsitektur harus bisa dijelaskan alasannya (bukan "karena AI bilang begitu").
- Kode harus lulus static analysis dan code style check sebelum dianggap selesai.
- Setiap fitur punya test yang membuktikan behavior-nya, bukan sekadar "app tidak crash".

---

## 2. Pemetaan Requirement → Bukti Teknis

| Requirement Lowongan | Implementasi di Project |
|---|---|
| Laravel: MVC, routing, middleware, Eloquent ORM, migrations, RESTful API | Seluruh arsitektur aplikasi + modul API terpisah |
| Basic relational database (PostgreSQL/MySQL), schema design, query, ORM | Lihat `02-DATABASE-DESIGN.md` |
| HTML, CSS, JS, basic FE framework | Livewire + Alpine.js (included di starter kit) + Tailwind CSS |
| Caching (Redis) & session management | Cache layer di modul katalog buku; session-based web auth |
| Version control Git | Conventional Commits, branch per fitur, PR-style history |
| Komunikasi & teamwork | Dibuktikan lewat dokumentasi (README, PRD, ADR) yang jelas dan bisa dibaca orang lain |

---

## 3. Tech Stack Final

| Layer | Pilihan | Justifikasi |
|---|---|---|
| Framework | Laravel 13 (PHP 8.3+) | Versi stable terbaru per Maret 2026 |
| Starter Kit | `laravel/livewire-starter-kit` (resmi) | Pengganti Breeze/Jetstream lama; Blade + Livewire + Sanctum sudah terintegrasi |
| Frontend | Livewire 3, Alpine.js, Tailwind CSS | Reactive UI tanpa build SPA terpisah |
| Database | PostgreSQL (hosted di Supabase) | Managed, gratis untuk portofolio, kompatibel penuh dengan Eloquent |
| API Auth | Laravel Sanctum | Token-based, sudah bawaan starter kit |
| Cache & Queue | Redis | Cache query, dan queue untuk job async (opsional) |
| API Docs | Scramble (`dedoc/scramble`) | Auto-generate OpenAPI 3.1 dari kode, tidak perlu anotasi manual, output Swagger UI di `/docs/api` |
| Code Style | Laravel Pint | Formatter resmi Laravel, berbasis PSR-12 |
| Static Analysis | Larastan (PHPStan untuk Laravel) | Level minimal 5, target level 8 |
| Testing | Pest | Standar testing framework Laravel modern |
| Version Control | Git + GitHub | Conventional Commits |

---

## 4. User Roles & Permission Matrix

| Aksi | Guest | Member | Admin |
|---|:---:|:---:|:---:|
| Browse katalog buku | ✅ | ✅ | ✅ |
| Search & filter buku | ✅ | ✅ | ✅ |
| Register / Login | ✅ | — | — |
| Ajukan peminjaman | ❌ | ✅ | ✅ |
| Lihat riwayat peminjaman sendiri | ❌ | ✅ | ✅ |
| CRUD buku & kategori | ❌ | ❌ | ✅ |
| Approve/reject peminjaman | ❌ | ❌ | ✅ |
| Tandai buku dikembalikan | ❌ | ❌ | ✅ |
| Lihat semua peminjaman (semua user) | ❌ | ❌ | ✅ |
| Akses API dengan token | ❌ | ✅ | ✅ |

Implementasi: Laravel Policy (`BookPolicy`, `LoanPolicy`) + Gate, bukan hardcode `if ($user->role === 'admin')` di controller.

---

## 5. Functional Requirements (Detail)

### FR-1: Autentikasi & Manajemen Profil
- Register dengan email unik, password minimal 8 karakter (validasi via Form Request)
- Login/logout berbasis session
- Update profil (nama, email, avatar opsional)
- Ganti password dengan verifikasi password lama

### FR-2: Manajemen Buku (Admin)
- Create/Read/Update/Delete buku dengan field: `title`, `author`, `isbn` (unique), `category_id`, `stock`, `description`, `cover_path`, `published_year`
- Upload cover buku (validasi tipe file & ukuran max 2MB)
- Soft delete (buku yang pernah dipinjam tidak boleh hard-delete demi integritas riwayat)

### FR-3: Manajemen Kategori (Admin)
- CRUD kategori dengan `name`, `slug` (auto-generate dari name)
- Tidak bisa hapus kategori yang masih punya buku terkait (validasi FK constraint + pesan error yang jelas)

### FR-4: Katalog Publik
- List buku dengan pagination (12 buku/halaman)
- Search berdasarkan judul/penulis (case-insensitive, `ILIKE` di PostgreSQL)
- Filter berdasarkan kategori dan status ketersediaan (stok > 0)
- Sort: terbaru, judul A-Z, paling banyak dipinjam

### FR-5: Peminjaman Buku (Member)
- Ajukan peminjaman → status awal `pending`
- Validasi: stok tersedia, user tidak sedang meminjam buku yang sama, maksimal 3 peminjaman aktif per user
- Durasi pinjam default 7 hari (dihitung dari `approved_at`, bukan dari `requested_at`)
- Riwayat peminjaman dengan status: `pending`, `approved`, `rejected`, `returned`, `overdue`

### FR-6: Manajemen Peminjaman (Admin)
- Dashboard daftar pengajuan pending
- Approve → stok buku otomatis berkurang 1, set `due_at`
- Reject → dengan alasan (field `rejection_reason`)
- Tandai dikembalikan → stok bertambah 1, set `returned_at`
- Auto-flag `overdue` untuk peminjaman yang lewat `due_at` (via Scheduled Command, jalan harian)

### FR-7: RESTful API
Base path: `/api/v1`. Response format konsisten (lihat §7). Endpoint detail di `03-API-DOCUMENTATION-PLAN.md`.

### FR-8: Caching
- Cache response `GET /api/v1/books` dan `GET /api/v1/categories` (TTL 15 menit, key termasuk query params)
- Cache di-invalidate otomatis lewat Model Observer saat ada create/update/delete pada `Book`/`Category`
- Session driver: `redis` (bukan `file`), demi demonstrasi penggunaan Redis yang riil, bukan cosmetic

---

## 6. Non-Functional Requirements

| Kategori | Requirement |
|---|---|
| **Performance** | Endpoint API yang di-cache harus response < 200ms pada cache-hit; N+1 query harus 0 (dicek pakai Laravel Debugbar/Telescope saat development) |
| **Security** | CSRF protection aktif di semua form web; API pakai Sanctum token; semua input tervalidasi via Form Request; mass-assignment protection via `$fillable` eksplisit |
| **Code Quality** | Pint pass 100%, Larastan minimal level 5 tanpa error, tidak ada method > 30 baris tanpa alasan kuat |
| **Testability** | Coverage minimal untuk business logic kritikal: validasi stok, approve/reject flow, cache invalidation |
| **Observability** | Logging terstruktur untuk aksi admin (approve/reject/return) via Laravel Log channel khusus `audit` |
| **Aksesibilitas dokumentasi** | Semua endpoint API terdokumentasi otomatis di `/docs/api` (Scramble), README lengkap dengan setup instructions |

---

## 7. API Response Contract (Standar)

Semua response API HARUS mengikuti format ini (via API Resource, bukan return array manual):

**Success (single/collection):**
```json
{
  "data": { "...": "..." },
  "meta": { "...": "pagination info jika collection" }
}
```

**Error:**
```json
{
  "message": "Human readable error message",
  "errors": { "field_name": ["Detail error validasi"] }
}
```

HTTP status code harus akurat: `200` OK, `201` Created, `204` No Content (delete), `401` Unauthenticated, `403` Unauthorized (policy gagal), `404` Not Found, `422` Validation Error, `429` Too Many Requests.

---

## 8. Out of Scope

- Payment/denda online
- Notifikasi email/SMS real-time
- Multi-cabang perpustakaan
- Native mobile app
- Supabase Auth/RLS (kita pakai Laravel Auth native, Supabase murni hosting Postgres)

---

## 9. Definition of Done (Project Level)

- [ ] Semua Functional Requirement (FR-1 s/d FR-8) berfungsi sesuai spesifikasi
- [ ] `./vendor/bin/pint --test` lulus tanpa error
- [ ] `./vendor/bin/phpstan analyse` lulus di level minimal 5
- [ ] `php artisan test` seluruh test hijau
- [ ] Dokumentasi API bisa diakses dan akurat di `/docs/api`
- [ ] README berisi: deskripsi, tech stack, cara install lokal, cara migrasi, link demo, screenshot alur utama
- [ ] Aplikasi ter-deploy dan bisa diakses publik
- [ ] Git history granular, message ikut Conventional Commits, tidak ada commit "fix", "update", "wip" tanpa konteks
