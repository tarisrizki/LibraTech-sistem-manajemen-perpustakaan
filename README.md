# LibraTech — Sistem Manajemen Perpustakaan

Perpustakaan digital untuk portofolio Laravel Developer: katalog publik, peminjaman dengan aturan bisnis, panel admin, API v1, dan dokumentasi otomatis.

Dibangun sesuai paket dokumen `00-INDEX.md` s/d `06-ORCHESTRATOR-PROMPT.md` (PRD, desain DB, style guide, task breakdown).

## Demo & Dokumentasi

- **Demo live:** *(belum live — baca [Deploy ke Render](#deploy) di bawah; setelah deploy isi URL di sini, contoh `https://libratech.onrender.com`)*
- **Dokumentasi API (Scramble):** `GET /docs/api` (UI) dan `GET /docs/api.json` / file `openapi.json` (OpenAPI 3.1). Live setelah deploy di `https://<app>.onrender.com/docs/api`.
- **Screenshot alur utama:** karena proses ini tanpa akses desktop user, screenshot diambil manual:
  1. Jalankan `php artisan serve`, buka `http://127.0.0.1:8000` (katalog), filter buku, klik salah satu buku.
  2. Daftar/login, ajukan peminjaman, cek Riwayat Saya (`/loans`).
  3. Login sebagai admin (`admin@libratech.test` / `password`), buka Kelola Buku, Kategori, Peminjaman (approve/reject/return).
  4. Buka `/docs/api` — lalu screenshot. Simpan sebagai `docs/screenshots/01-catalog.png`, `02-book-detail.png`, `03-loans-member.png`, `04-admin-loans.png`, `05-docs-api.png` dan tautkan di README (jangan pakai placeholder `via.placeholder.com`).

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 13.24 (PHP 8.4) |
| Frontend | Blade + Tailwind CSS + Livewire 4 |
| Database | SQLite (dev), PostgreSQL via Supabase (prod) |
| API Auth | Laravel Sanctum — token Bearer |
| Cache & Session | `database` (dev), `redis` (prod — Render/Upstash) — di-invalidate oleh Model Observer |
| API Docs | Scramble `dedoc/scramble` — OpenAPI 3.1 |
| Quality | Laravel Pint + Larastan level 5 (target level 8) |
| Testing | Pest 4 |
| Deploy | Render (php web service) — lihat `render.yaml` |

## Cara Install Lokal (dari nol, sudah diverifikasi)

Prasyarat: PHP 8.3+ (disarankan 8.4), Composer, Node (opsional untuk `npm run build`), tools di `C:\tools\php\php.exe` untuk Windows.

```bash
# 1. Clone (atau copy folder ini)
git clone <repo-url> libratech && cd libratech

# 2. Install dependensi — gunakan composer dari bundle (atau composer global)
php C:/tools/php/composer.phar install
# atau: composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database (SQLite dev — default)
# Pastikan file DB ada, lalu migrasi + seed (42 buku, 6 kategori, 20 loan, 6 user)
touch database/database.sqlite   # atau: New-Item database/database.sqlite -ItemType File (PowerShell)
php artisan migrate:fresh --seed --force

# 5. (Opsional) Build asset frontend — hanya untuk produksi; dev tidak wajib karena CDN Tailwind
# npm install --ignore-scripts && npm run build

# 6. Jalankan
php artisan serve
# Buka http://127.0.0.1:8000 — katalog, login/register di navbar
```

Akun demo setelah seed:

- Admin: `admin@libratech.test` / `password`
- Member: `andi@libratech.test` / `password` (5 member lain juga tersedia)

## Perintah Berguna

```bash
php artisan migrate:fresh --seed --force   # reset DB
php artisan loans:mark-overdue             # tandai approved yang lewat due_at jadi overdue (terjadwal harian)
php artisan scramble:export --path=openapi.json  # ekspor OpenAPI JSON
php vendor/bin/pint --test                 # cek style (harus PASS — 80 file)
php vendor/bin/phpstan analyse --memory-limit=-1  # static analysis level 5 — harus [OK] No errors
php vendor/bin/phpstan analyse --level=8 --memory-limit=-1  # target L8 (opsional)
php vendor/pestphp/pest/bin/pest           # 19 passed (44 assertions) + LoanConcurrencyTest
```

## API v1

Base ` /api/v1`. Auth: header `Authorization: Bearer <token>` dari `POST /api/v1/auth/login`.

| Method | Path | Auth | Deskripsi |
|--------|------|------|-----------|
| POST | `/api/v1/auth/login` | — | `{email,password}` -> `{data:{token,user}}` |
| POST | `/api/v1/auth/logout` | token | hapus current token |
| GET | `/api/v1/auth/me` | token | profil user |
| GET | `/api/v1/categories` | — | daftar kategori |
| GET | `/api/v1/books?search=&category_id=&available=1&sort=title|popular` | — | katalog (paginated 12) |
| GET | `/api/v1/books/{book}` | — | detail buku |
| GET | `/api/v1/loans` | token (own) | riwayat peminjaman user |
| POST | `/api/v1/loans` | token | ajukan pinjaman `{book_id}` |
| GET | `/api/v1/admin/loans?status=` | admin | daftar semua loan |
| PATCH | `/api/v1/admin/loans/{loan}/approve` | admin | set approved, decrement stock, due_at +7d |
| PATCH | `/api/v1/admin/loans/{loan}/reject` | admin | `{rejection_reason}` |
| PATCH | `/api/v1/admin/loans/{loan}/return` | admin | increment stock, returned_at |

Coba cepat (setelah `php artisan serve`):

```bash
curl -s http://127.0.0.1:8000/api/v1/books | head -c 500
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" -d '{"email":"admin@libratech.test","password":"password"}' | python3 -c "import sys,json; print(json.load(sys.stdin)['data']['token'])")
curl -s http://127.0.0.1:8000/api/v1/auth/me -H "Authorization: Bearer $TOKEN"
```

## Struktur

- `app/Enums/{UserRole,LoanStatus}.php` — enum native.
- `app/Actions/Loans/{Request,Approve,Reject,Return}LoanAction.php` — bisnis pinjaman (web + API reuse).
- `app/Http/Controllers/{Catalog,LoanController,Admin/*,Api/V1/*}` — web dan API (`with('category')`, `with(['user','book.category'])` eager).
- `app/Http/Requests/*`, `app/Http/Resources/*`, `app/Observers/{Book,Category}Observer.php` (cache flush), `app/Policies/*`, `app/Console/Commands/MarkOverdueLoans.php`.
- `database/migrations/*`, `database/factories/*`, `database/seeders/{RoleAwareUser,Category,Book,Loan}Seeder.php`.
- `resources/views/{layouts,catalog,loans,admin,auth}` — Blade editorial (Instrument Sans, zinc/paper).
- `routes/{web,api,console}.php`, `config/scramble.php` (title LibraTech API Docs), `phpstan.neon` (level 5), `render.yaml`.

## Deploy

Lihat [Deploy](#deploy) — Render + Supabase + Redis managed. File blueprint: `render.yaml`; template env: `.env.example` bagian Production.

## Catatan Antipattern yang Sudah Dibersihkan

- `$data -> $validated` rename (Anti-AI-Slop 4.3), `declare(strict_types=1)` di semua file, Pint/PHPStan hijau.

## Arsitektur

Lihat `docs/adr.md` untuk ADR singkat (Action vs Service, Supabase, Scramble).

## <a id="deploy"></a>Deploy ke Render (Render + Supabase + Redis)

Status: **blocker — menunggu kredensial production dari user.**

Yang sudah disiapkan tanpa kredensial:
- `render.yaml` (buildCommand `composer install --no-dev --optimize-autoloader && php artisan migrate --force && npm run build`, startCommand `php artisan serve --host=0.0.0.0 --port=$PORT`, healthCheck `/up`).
- `.env.example` tambahan blok Production (Supabase Postgres + Redis URL).

Yang harus diisi user di dashboard Render (Environment, sync:false):
- `APP_KEY` (`php artisan key:generate --show`), `APP_URL` (`https://<app>.onrender.com`), `DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD` atau `DATABASE_URL` (ambil dari Supabase Project Settings -> Database -> Connection string), `REDIS_HOST/REDIS_PORT/REDIS_PASSWORD` atau `REDIS_URL` (Upstash/Render Redis).

Langkah setelah kredensial terisi:
1. Push repo ke GitHub.
2. Render -> New Web Service -> connect repo -> envVars otomatis dari `render.yaml` -> isi yang sync:false -> Deploy.
3. Setelah live: verifikasi `GET /` 200, `GET /docs/api` 200, `GET /api/v1/books` paginated, `POST /api/v1/auth/login` -> `GET /api/v1/auth/me` Bearer.
4. Tempel URL live ke bagian Demo di README.

Catatan produksi: `APP_ENV=production`, `APP_DEBUG=false`, `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis` sudah di `render.yaml`; `phpstan.neon` level 5 tetap berlaku.
