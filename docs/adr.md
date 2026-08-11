# Architecture Decision Records — LibraTech

Tanggal: 2026-08-11. Penulis wawancara nanti: jelaskan trade-off, bukan rangkuman.

## ADR 1 — Kenapa Action class, bukan Service class

**Konteks:** Pinjaman punya aturan berlapis (stok > 0, tidak ada pinjaman aktif duplikat, max 3 approved, decrement/increment atomik). Dua permukaan memanggil aturan yang sama: web controller dan API controller.

**Alternatif dipertimbangkan:**
- Logic langsung di controller (awal termudah) — ditolak karena duplikasi: web `LoanController@store` dan API `V1\LoanController@store` akan copy validasi yang sama; bug fix di satu tempat tidak ikut di tempat lain. PRD juga melarang hardcode di controller.
- Service class `LoanService` dengan method banyak (`request`, `approve`, `reject`, `return`) — ditolak karena god object: satu class memegang 4 tanggung jawab dengan transaksi/locking berbeda; test menjadi berat (harus mock service utuh untuk satu flow).

**Keputusan:** Satu **Action class per aksi** — `RequestLoanAction`, `ApproveLoanAction`, `RejectLoanAction`, `ReturnLoanAction`. Masing-masing single-purpose, stateless, di-inject via DI di kedua controller.

**Konsekuensi:**
- Positif: reuse terbukti (audit poin 3 — 4 controller API+web semuanya inject Action yang sama, zero duplicated stock logic), test per aksi bisa fokus (happy path + edge stock habis + limit 3), `lockForUpdate` + `DB::transaction` terisolasi di `Approve`/`Return`.
- Negatif: 4 file vs 1 file — ditangani dengan folder `app/Actions/Loans/` dan `declare(strict_types=1)` konsisten; Larastan level 5 memverifikasi semua return type.

**Kapan revisi:** Kalau aturan pinjaman butuh orchestrasi event (notifikasi, histori audit terpisah), Action tetap, tapi bungkus dengan event/job; jangan pindah ke Service god object.

## ADR 2 — Kenapa Supabase (Postgres hosted), bukan MySQL lokal / SQLite prod

**Konteks:** PRD mensyaratkan Postgres/Supabase untuk portofolio, tapi dev berjalan di SQLite. FR-4 butuh `ILIKE` case-insensitive (Postgres) dan riwayat pinjaman harus survive (soft delete buku).

**Alternatif:**
- MySQL lokal / PlanetScale — MySQL `LIKE` case-insensitive tergantung collation, `RETURNING` dan `search_path` Postgres tidak tersedia; portofolio kurang menonjol untuk role yang menyebut Postgres.
- Tetap SQLite di produksi — ringan tapi tidak managed, backup/PoP lemah, tidak demonstrasi managed Postgres; Render + Supabase free tier justru lebih mudah diwawancara.

**Keputusan:** SQLite untuk dev/test (`DB_CONNECTION=sqlite`, `phpunit.xml` `:memory:`) — cepat tanpa docker; Postgres Supabase untuk produksi via `DB_CONNECTION=pgsql` (Render `DB_HOST/…` atau `DATABASE_URL`). Code tidak branching: query pakai `LIKE` yang di SQLite case-insensitive by default dan di Postgres diganti `ILIKE` jika perlu; migration FK (`restrictOnDelete` buku, `cascadeOnDelete` loan user) kompatibel di kedua engine; `search_path` default `public`.

**Konsekuensi & blocker saat ini:** `.env.example` sudah mem-template `DATABASE_URL`/`DB_*` untuk Render, tapi kredensial Supabase belum ada di repo (by design). Deploy blokir sampai user membuat project Supabase dan mengisi env di Render — dicatat eksplisit di laporan Agent 12, bukan di-skip. Migrasi di prod memakai `php artisan migrate --force` dari `render.yaml`.

## ADR 3 — Kenapa Scramble (`dedoc/scramble`), bukan L5-Swagger (`darkaonline/l5-swagger`)

**Konteks:** Butuh OpenAPI untuk 14 route `/api/v1` + `/docs/api` yang hidup. Bayangan umum: "pakai swagger annotation" — tapi anotasi manual sering busuk setelah refactor.

**Alternatif:**
- L5-Swagger — `@OA\*` annotation di setiap controller. Kelebihan: Swagger UI familiar. Kekurangan: setiap ubah Request/Resource harus ingat ubah anotasi; 71 file harus diaudit anotasi vs code; PRD menuntut docs akurat di `/docs/api` tanpa anotasi manual.
- Scramble — generate dari reflection controller, FormRequest, dan Resource; output OpenAPI 3.1 tanpa anotasi; `GET /docs/api` (Elements UI) dan `GET /docs/api.json` + `php artisan scramble:export --path=openapi.json`.

**Keputusan:** Scramble `^0.13.39`. Config `config/scramble.php` `api_path => 'api'`, title `LibraTech API Docs` v1.0.0. Verifikasi: `php artisan serve` lalu `GET /docs/api` 200 (16KB HTML) dan `GET /docs/api.json` 60KB, `openapi.json` 60KB ter-export — dibuktikan via curl/browser di poin 7.

**Konsekuensi:**
- Positif: docs tidak drift dari code; `BookResource`/`LoanResource` yang dipakai response sudah terdokumentasi otomatis; tidak ada OpenAPI annotation yang tertinggal `TODO`.
- Negatif: kustomisasi Swagger UI lebih terbatas dibanding L5-Swagger; diterima karena untuk portofolio akurasi > kustomisasi. Kalau butuh redoc/Swagger UI theme khusus, bisa expose `openapi.json` ke Scalar/Redoc terpisah.

---
Keputusan di atas harus bisa dipertahankan saat wawancara teknis: sebut konteks -> alternatif -> trade-off -> bukti verifikasi (command output) — bukan "AI merekomendasikan".
