# LibraTech — Paket Dokumen Project

Urutan baca yang disarankan:

1. **01-PRD.md** — apa yang dibangun dan kenapa
2. **02-DATABASE-DESIGN.md** — struktur data
3. **03-API-DOCUMENTATION-PLAN.md** — kontrak API
4. **04-STYLE-GUIDE.md** — standar kode (wajib dipatuhi)
5. **05-TASK-BREAKDOWN.md** — pembagian kerja 12 subagent
6. **06-ORCHESTRATOR-PROMPT.md** — prompt siap pakai untuk agent orchestrator kamu

## Cara Pakai dengan Agent Kamu

1. Taruh semua file ini di folder `/docs` di root repo project.
2. Buka sesi baru dengan agent orchestrator kamu.
3. Copy isi blok kode di `06-ORCHESTRATOR-PROMPT.md` sebagai instruksi awal.
4. Agent akan membaca kelima dokumen sumber, konfirmasi kredensial yang dibutuhkan (Supabase, target hosting), lalu mulai eksekusi Agent 01 s/d Agent 12 sesuai dependency graph.
5. Kamu tetap perlu jadi "mata" untuk hal visual — agent tidak punya vision model, jadi validasi tampilan akhir tetap manual olehmu (screenshot + feedback), sisanya (logic, struktur, kualitas kode) sudah dijaga oleh aturan di style guide.

## Yang Membedakan Paket Ini dari Template Generik

- Semua keputusan teknis (starter kit, Scramble vs Swagger, struktur Action class) sudah diriset dan dijustifikasi, bukan tebakan.
- Ada aturan eksplisit "Anti-AI-Slop" karena project ini dikerjakan AI agent — risiko kode generik/asal jadi lebih tinggi kalau tidak dipagari dari awal.
- Task breakdown sudah disesuaikan dengan kapasitas 12 subagent dan punya dependency graph yang jelas, bukan cuma daftar fitur datar.
