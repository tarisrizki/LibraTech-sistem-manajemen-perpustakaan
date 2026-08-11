<div class="flex flex-col gap-6" x-data="{ saved:false }" x-init="if(window.motion){ motion.animate($el.querySelector('[data-animate]'), {opacity:[0,1], y:[6,0]}, {duration:0.18, easing:'ease-out'}) }">
    <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm text-zinc-600 hover:text-zinc-900 w-fit transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        Kembali ke katalog
    </a>

    <div data-animate class="grid lg:grid-cols-[1.15fr_0.85fr] gap-6 items-start">
        <flux:card class="!rounded-[16px] !p-6 lg:!p-8">
            <p class="text-[11px] tracking-[0.14em] uppercase text-zinc-500 font-medium">{{ $book->category->name ?? '-' }}</p>
            <h1 class="font-[Literata,ui-serif,Georgia,serif] text-[26px] lg:text-[32px] font-semibold tracking-[-0.03em] leading-[1.05] mt-1 text-[#1b1b24]">{{ $book->title }}</h1>
            <p class="text-zinc-600 mt-1.5">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">{{ $book->published_year }}</span>@endif</p>
            <p class="text-xs text-zinc-500 mt-1 font-mono">ISBN {{ $book->isbn }}</p>

            <div class="mt-4 flex flex-wrap gap-2">
                @if($book->stock > 0)
                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1.5 text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Tersedia · {{ $book->stock }} eksemplar
                    </span>
                @else
                    <span class="inline-flex bg-zinc-100 text-zinc-700 border border-zinc-200 rounded-full px-3 py-1.5 text-xs font-medium">Stok habis</span>
                @endif
                <span class="inline-flex bg-zinc-50 border border-[#e2e8f0] rounded-full px-3 py-1.5 text-xs text-zinc-600">{{ $book->category->name ?? '-' }}</span>
            </div>

            <div class="mt-6 max-w-[360px] rounded-[16px] overflow-hidden border border-[#e2e8f0] bg-zinc-50 aspect-[2/3]">
                @if($book->cover_path)
                    <img src="{{ Storage::url($book->cover_path) }}" alt="Cover {{ $book->title }}" class="w-full h-full object-cover transform transition-transform duration-300 ease-out hover:scale-[1.02]">
                @else
                    <div class="w-full h-full grid place-items-center text-zinc-400 text-sm font-medium p-6 text-center leading-tight bg-zinc-100">{{ $book->title }}</div>
                @endif
            </div>

            @if($book->description)
                <div class="mt-6 pt-6 border-t border-zinc-100">
                    <p class="text-[11px] tracking-[0.12em] uppercase text-zinc-500 font-medium">Deskripsi</p>
                    <p class="mt-2 text-[15px] leading-relaxed text-zinc-700 max-w-[65ch]">{{ $book->description }}</p>
                </div>
            @endif

            <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3 bg-[#fcf8ff] border border-[#e2e8f0] rounded-[16px] p-4">
                <div><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-500 font-medium">Kategori</p><p class="text-sm font-medium text-[#1b1b24] mt-0.5">{{ $book->category->name ?? '-' }}</p></div>
                <div><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-500 font-medium">Tahun</p><p class="text-sm font-medium text-[#1b1b24] mt-0.5">{{ $book->published_year ?? '-' }}</p></div>
                <div><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-500 font-medium">Stok</p><p class="text-sm font-medium text-[#1b1b24] mt-0.5">{{ $book->stock }}</p></div>
                <div><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-500 font-medium">Dipinjam</p><p class="text-sm font-medium text-[#1b1b24] mt-0.5">{{ $activeLoans }}</p></div>
            </div>
        </flux:card>

        <div class="bg-zinc-900 text-zinc-100 rounded-[16px] p-6 lg:p-7 lg:sticky lg:top-[80px]">
            <p class="text-[11px] tracking-[0.14em] uppercase text-zinc-400 font-medium">Peminjaman</p>
            <h2 class="text-lg font-semibold mt-1">Pinjam buku ini</h2>
            <p class="text-sm text-zinc-400 mt-1 leading-relaxed">Durasi 7 hari sejak disetujui admin. Maksimal 3 pinjaman aktif per anggota.</p>
            <div class="mt-5 flex flex-col gap-2">
                @auth
                    @if($book->stock > 0)
                        <form method="POST" action="{{ route('loans.store') }}">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <flux:button type="submit" variant="primary" class="w-full !rounded-full !bg-white !text-zinc-900 hover:!bg-zinc-100 justify-center">Ajukan peminjaman</flux:button>
                        </form>
                        <p class="text-xs text-zinc-500 text-center">Admin akan menyetujui dalam 1x24 jam.</p>
                    @else
                        <div class="bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-sm text-zinc-300 text-center">Stok habis · tidak dapat diajukan saat ini.</div>
                        <flux:button variant="ghost" class="w-full !rounded-full !text-zinc-400 !border-white/15" disabled>Join Waitlist</flux:button>
                    @endif
                    <flux:button variant="ghost" x-on:click="saved=!saved" class="w-full !rounded-full !text-zinc-300 hover:!bg-white/10">
                        <span x-text="saved ? 'Disimpan' : 'Simpan untuk nanti'"></span>
                    </flux:button>
                @else
                    <flux:button href="{{ route('login') }}" variant="primary" class="w-full !rounded-full !bg-white !text-zinc-900 hover:!bg-zinc-100 justify-center">Masuk untuk meminjam</flux:button>
                    <p class="text-xs text-zinc-500 text-center">Belum punya akun? <a href="{{ route('register') }}" class="underline text-zinc-300 hover:text-white">Daftar</a></p>
                @endauth
            </div>
            <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-2 gap-3 text-center">
                <div class="bg-white/5 rounded-xl py-3"><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-400 font-medium">Stok</p><p class="text-lg font-semibold">{{ $book->stock }}</p></div>
                <div class="bg-white/5 rounded-xl py-3"><p class="text-[11px] uppercase tracking-[0.08em] text-zinc-400 font-medium">Dipinjam</p><p class="text-lg font-semibold">{{ $activeLoans }}</p></div>
            </div>
        </div>
    </div>

    @if($related->count())
    <section>
        <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[18px] font-semibold tracking-[-0.02em] text-[#1b1b24]">Buku terkait</h2>
        <div class="mt-3 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($related as $rb)
                <a href="{{ route('catalog.show', $rb) }}" wire:navigate class="group">
                    <flux:card class="!p-3 !rounded-[16px] h-full hover:border-zinc-300 hover:shadow-sm transition duration-180">
                        <div class="aspect-[2/3] rounded-xl overflow-hidden bg-zinc-100">
                            @if($rb->cover_path)
                                <img src="{{ Storage::url($rb->cover_path) }}" alt="{{ $rb->title }}" class="w-full h-full object-cover transform transition-transform duration-300 ease-out group-hover:scale-105" loading="lazy" decoding="async">
                            @else
                                <div class="w-full h-full grid place-items-center bg-zinc-100 text-zinc-400 text-[11px] font-medium p-3 text-center leading-tight">{{ $rb->title }}</div>
                            @endif
                        </div>
                        <p class="mt-2.5 font-semibold text-[13px] leading-tight line-clamp-1 group-hover:text-[#4f46e5] transition">{{ $rb->title }}</p>
                        <p class="text-xs text-zinc-600 line-clamp-1">{{ $rb->author }}</p>
                    </flux:card>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
