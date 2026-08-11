# API Documentation Plan
## LibraTech RESTful API — via Scramble (OpenAPI 3.1)

---

## 1. Tooling

**Package:** `dedoc/scramble`

```bash
composer require dedoc/scramble
php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag=scramble-config
```

Scramble membaca route, Form Request validation rules, API Resource, dan type hint langsung dari kode — dokumentasi otomatis sinkron dengan implementasi, tidak butuh anotasi PHPDoc verbose ala L5-Swagger. Dokumentasi live di `/docs/api` (Stoplight Elements UI), spec mentah bisa diexport ke `openapi.yaml`.

**Wajib dilakukan agar dokumentasi berkualitas (bukan default kosong):**
- Tambahkan PHPDoc singkat di setiap method controller API (`@` tags standar: deskripsi 1 baris + `@response` contoh jika perlu kasus khusus).
- Set title, deskripsi, dan versi API di `config/scramble.php`.
- Grouping endpoint berdasarkan tag (`Books`, `Categories`, `Loans`, `Auth`) via `#[Group]` attribute atau konvensi folder controller.
- Restrict akses `/docs/api` ke environment local + staging saja (gate `viewApiDocs`), jangan expose ke production tanpa auth.

---

## 2. Konvensi Umum

- Base URL: `/api/v1`
- Format response: JSON, ikuti contract di `01-PRD.md` §7 (selalu lewat API Resource, tidak pernah `return $model` mentah)
- Auth: Bearer token (Sanctum) di header `Authorization: Bearer {token}`
- Pagination: Laravel default paginator, response menyertakan `meta.current_page`, `meta.last_page`, `meta.total`
- Rate limiting: `throttle:api` default (60 req/menit), endpoint login lebih ketat (`throttle:5,1`)
- Versioning: prefix `/v1` di route, disiapkan untuk breaking change di masa depan tanpa mengubah kontrak lama

---

## 3. Daftar Endpoint

### Auth
| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| POST | `/api/v1/auth/login` | Guest | Login, return Sanctum token |
| POST | `/api/v1/auth/logout` | Sanctum | Revoke token aktif |
| GET | `/api/v1/auth/me` | Sanctum | Data user yang sedang login |

### Categories
| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| GET | `/api/v1/categories` | Public | List semua kategori (cached) |

### Books
| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| GET | `/api/v1/books` | Public | List buku, support `?search=`, `?category_id=`, `?available=true`, `?sort=`, pagination (cached per query combination) |
| GET | `/api/v1/books/{book}` | Public | Detail buku |

### Loans
| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| GET | `/api/v1/loans` | Sanctum (member) | Riwayat peminjaman user yang login |
| POST | `/api/v1/loans` | Sanctum (member) | Ajukan peminjaman baru, body: `{ "book_id": int }` |
| GET | `/api/v1/admin/loans` | Sanctum (admin) | Semua peminjaman, filter `?status=` |
| PATCH | `/api/v1/admin/loans/{loan}/approve` | Sanctum (admin) | Approve peminjaman |
| PATCH | `/api/v1/admin/loans/{loan}/reject` | Sanctum (admin) | Reject, body: `{ "rejection_reason": string }` |
| PATCH | `/api/v1/admin/loans/{loan}/return` | Sanctum (admin) | Tandai buku dikembalikan |

### Admin — Books & Categories (opsional API, web sudah cukup lewat Livewire)
| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| POST | `/api/v1/admin/books` | Sanctum (admin) | Create buku |
| PUT | `/api/v1/admin/books/{book}` | Sanctum (admin) | Update buku |
| DELETE | `/api/v1/admin/books/{book}` | Sanctum (admin) | Soft delete buku |

---

## 4. Contoh Kontrak Response

**`GET /api/v1/books` — 200:**
```json
{
  "data": [
    {
      "id": 12,
      "title": "Laskar Pelangi",
      "author": "Andrea Hirata",
      "isbn": "9789793062792",
      "category": { "id": 3, "name": "Fiksi" },
      "stock": 4,
      "is_available": true,
      "cover_url": "https://.../covers/laskar-pelangi.jpg"
    }
  ],
  "meta": { "current_page": 1, "last_page": 5, "total": 52 }
}
```

**`POST /api/v1/loans` — 422 (contoh error):**
```json
{
  "message": "Peminjaman gagal diajukan.",
  "errors": {
    "book_id": ["Stok buku ini sedang habis."]
  }
}
```

---

## 5. Definition of Done — Dokumentasi API

- [ ] `/docs/api` bisa diakses dan menampilkan semua endpoint di atas
- [ ] Setiap endpoint punya contoh request & response yang benar (bukan skeleton kosong)
- [ ] Response error tervalidasi konsisten dengan contract di PRD
- [ ] File `openapi.yaml` hasil export tersedia di root repo untuk dipakai import ke Postman/Insomnia
