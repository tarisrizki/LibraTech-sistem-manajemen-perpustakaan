# DESIGN.md — LibraTech — Source tunggal (jangan duplikasi di worker)

## Referensi
- Stitch project 7503948753495764153 — screens 4fa Catalog Home Standardized, 56d Masuk, 787 Manajemen Buku Standardized
- Stack: Laravel + Livewire v4 + Flux UI v2 + Tailwind v4 + Alpine.js + Motion
- Sun wukong: basis 4fa; disiplin lengkap di stitch `DESIGN.md` Stitch

## Token (satu tempat — `resources/css/app.css` — mirror Stitch `7503948753495764153` `namedColors`)
- primary #3525cd (Stitch `primary`; `#4f46e5` adalah `primary_container` — jangan pakai `#4f46e5` sebagai primary), surface #fcf8ff, ink #1b1b24 (`on_background`/`on_surface`), muted #464555 (`on_surface_variant`), line #c7c4d8 (`outline_variant`), accent2 #82f5c1 (`secondary_container`), error #ba1a1a (`error`; `#93000a` = `on_error_container`), outline #777587, surface_container #f0ecf9
- font headline Literata (400/600/700), body/label Hanken Grotesk (400/500/600) — import via bunny.net
- roundness 8px (card 16, input 10, pill 999), shadow 0 1px 24px rgba(27 27 36 / .07), border 1px #e2e8f0 variant
- space 4/8/12/16/24, max-w 1280, header 64px sticky blurred

## Komponen — wajib Flux di view
- Button: flux:button variant primary|ghost|outline — jangan tailwind button custom
- Input: flux:input / flux:select / flux:textarea / flux:field
- Card: flux:card / manual div rounded-[16px] border line bg-surface shadow-[...]
- Badge: flux:badge / pill text 11 uppercase tracking
- Nav/shell: flux:header + flux:sidebar (admin) — app.blade extends otomatis
- Motion: data-flux-animate atau motion.div — stagger 40ms list, fade 180 easeOut, hover lift y-2
- Alpine: x-data untuk ringan (search debounce, sheet open, tab) — wire untuk data

## Layout — `resources/views/layouts/app.blade.php` (satu)
- bg #fdfcf8 (#fcf8ff tint), text #0f172a/#1b1b24, min-h 100dvh, antialiased
- header sticky 64 blurred, nav pill (active bg-zinc-900 text-white), role gate admin vs member
- main max-w 1280 px6 py-8, yield content, footer mono xs
- Claude: jangan hardcode warna/font di screen — pakai var/kelas token

## Domain
- Catalog: hero Discover + browse + Featured 2-col + New Arrivals 4-col — search pill, place hold / join waitlist
- Auth: split kiri quote + kanan form (flux:input, flux:button) — Literata quote, Hanken form
- Admin: table Buku | Kategori | Stok | Aksi — filter pill, tambah Buku sheet slide (flux:modal), upload drag
- Motion: hero fade-up, card stagger, modal spring scale .98→1
