# Database Design Document
## LibraTech — PostgreSQL Schema (via Supabase)

---

## 1. Prinsip Desain

- Semua tabel pakai `id` bigint auto-increment sebagai primary key (bukan UUID — tidak perlu kompleksitas UUID untuk skala project ini; jika butuh non-guessable ID publik, pakai kolom `slug`/`public_id` terpisah).
- Semua tabel wajib punya `created_at`, `updated_at` (Eloquent timestamps bawaan).
- Tabel yang datanya historis-kritikal (`books`, `loans`) pakai `deleted_at` (soft delete).
- Foreign key selalu didefinisikan eksplisit di migration dengan `constrained()` dan `onDelete` yang jelas — tidak dibiarkan default.
- Nama tabel: plural, snake_case. Nama kolom: snake_case.
- Schema Postgres yang dipakai: **bukan `public`** (ubah ke `app` di `search_path`), karena Supabase mengekspos schema `public` sebagai data API mereka sendiri — kita tidak pakai fitur itu, jadi harus dipisah untuk menghindari exposure tidak sengaja.

---

## 2. Entity Relationship Diagram (Teks)

```
users ||--o{ loans        : "meminjam"
books ||--o{ loans         : "dipinjam"
categories ||--o{ books    : "mengkategorikan"
users ||--o{ books (as created_by, opsional audit)
```

**Kardinalitas:**
- `categories 1—N books` (satu kategori punya banyak buku, satu buku hanya satu kategori)
- `users N—N books` melalui `loans` (many-to-many dengan atribut tambahan → pivot table beratribut, bukan pivot polos)

---

## 3. Skema Tabel

### 3.1 `users`
| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK, auto-increment |
| name | varchar(255) | not null |
| email | varchar(255) | not null, unique |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | not null (hashed) |
| role | varchar(20) | not null, default `'member'`, check in (`'member'`,`'admin'`) |
| remember_token | varchar(100) | nullable |
| created_at, updated_at | timestamp | |

> Catatan: `role` sebagai enum string dengan DB check constraint, bukan tabel `roles` terpisah — over-engineering untuk 2 role saja. Jika kebutuhan role berkembang, migrasi ke tabel `roles` + `role_user` pivot adalah langkah lanjutan yang wajar, bukan sekarang.

### 3.2 `categories`
| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| name | varchar(100) | not null, unique |
| slug | varchar(120) | not null, unique |
| created_at, updated_at | timestamp | |

### 3.3 `books`
| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| category_id | bigint | FK → `categories.id`, `restrict` on delete (tidak boleh hapus kategori yang masih dipakai) |
| title | varchar(255) | not null, index |
| author | varchar(255) | not null, index |
| isbn | varchar(20) | not null, unique |
| stock | integer | not null, default 0, check >= 0 |
| description | text | nullable |
| cover_path | varchar(255) | nullable |
| published_year | smallint | nullable |
| created_at, updated_at, deleted_at | timestamp | soft delete |

**Index tambahan:** composite index `(title, author)` untuk mempercepat search; index di `category_id` (otomatis dari FK).

### 3.4 `loans` (pivot beratribut — bukan pivot polos, jadi model Eloquent sendiri)
| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| user_id | bigint | FK → `users.id`, cascade on delete |
| book_id | bigint | FK → `books.id`, restrict on delete |
| status | varchar(20) | not null, default `'pending'`, check in (`pending`,`approved`,`rejected`,`returned`,`overdue`) |
| requested_at | timestamp | not null, default now() |
| approved_at | timestamp | nullable |
| due_at | timestamp | nullable |
| returned_at | timestamp | nullable |
| rejection_reason | varchar(255) | nullable |
| approved_by | bigint | FK → `users.id`, nullable, set null on delete (admin yang approve) |
| created_at, updated_at | timestamp | |

**Index:** `(user_id, status)`, `(book_id, status)`, `(status, due_at)` — dipakai query dashboard admin & job overdue-checker.

**Business rule yang HARUS ditegakkan di level aplikasi (bukan cuma DB):**
- User tidak boleh punya lebih dari 1 loan aktif (`pending`/`approved`) untuk buku yang sama → unique partial index atau validasi di service layer.
- Maksimal 3 loan berstatus `approved` per user secara bersamaan → validasi di service layer (tidak feasible sebagai DB constraint sederhana).

---

## 4. Migration Order (Wajib Berurutan)

1. `create_users_table` (dari starter kit, tambahkan kolom `role`)
2. `create_categories_table`
3. `create_books_table` (bergantung ke `categories`)
4. `create_loans_table` (bergantung ke `users` dan `books`)
5. `add_role_check_constraint_to_users_table` (jika pakai raw SQL check constraint terpisah)

---

## 5. Contoh Definisi Migration (Referensi, bukan copy-paste blind)

```php
// database/migrations/xxxx_create_books_table.php
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->restrictOnDelete();
    $table->string('title');
    $table->string('author');
    $table->string('isbn', 20)->unique();
    $table->unsignedInteger('stock')->default(0);
    $table->text('description')->nullable();
    $table->string('cover_path')->nullable();
    $table->smallInteger('published_year')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['title', 'author']);
});
```

```php
// database/migrations/xxxx_create_loans_table.php
Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('book_id')->constrained()->restrictOnDelete();
    $table->string('status', 20)->default('pending');
    $table->timestamp('requested_at')->useCurrent();
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('due_at')->nullable();
    $table->timestamp('returned_at')->nullable();
    $table->string('rejection_reason')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['user_id', 'status']);
    $table->index(['book_id', 'status']);
    $table->index(['status', 'due_at']);
});
```

---

## 6. Seeder Strategy

- `RoleAwareUserSeeder`: 1 admin (`admin@libratech.test`), 5 member dummy — pakai Faker tapi dengan data Indonesia yang masuk akal (nama, bukan `Lorem Ipsum User 1`)
- `CategorySeeder`: minimal 6 kategori realistis (Fiksi, Non-Fiksi, Teknologi, Sains, Sejarah, Biografi)
- `BookSeeder`: minimal 40 buku dengan judul/penulis nyata (boleh pakai data buku publik yang benar-benar ada, bukan string acak) — tersebar di semua kategori, sebagian stok 0 untuk uji kasus "buku habis"
- `LoanSeeder`: kombinasi status (`pending`, `approved`, `returned`, `overdue`) untuk memastikan semua state UI dan API bisa didemokan tanpa perlu manual testing manual dari nol

---

## 7. Supabase-Specific Setup Notes

1. Buat project baru di Supabase Dashboard.
2. Ambil **Session Pooler connection string** dari halaman "Connect".
3. Di `.env`:
```
DB_CONNECTION=pgsql
DB_URL=postgres://postgres.[PROJECT-REF]:[PASSWORD]@aws-[REGION].pooler.supabase.com:5432/postgres
```
4. Di `config/database.php`, set `search_path` koneksi `pgsql` ke schema custom, contoh `'app'`, bukan `public`.
5. Sebelum migrate pertama kali, buat schema tersebut lewat SQL Editor Supabase: `CREATE SCHEMA IF NOT EXISTS app;`
6. Jalankan `php artisan migrate --seed`.
