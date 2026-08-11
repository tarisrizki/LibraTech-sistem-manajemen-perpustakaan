@extends('layouts.app')
@section('content')
<a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1 text-sm text-zinc-600 hover:text-zinc-900"><span>&larr;</span> Kembali ke katalog</a>
<div class="mt-4 grid lg:grid-cols-[1.15fr_0.85fr] gap-6 items-start">
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 lg:p-8" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
        <p class="mono text-[11px] tracking-[0.14em] uppercase text-zinc-500">{{ $book->category->name ?? '-' }}</p>
        <h1 class="text-[26px] lg:text-[32px] font-semibold tracking-[-0.03em] leading-[1.05] mt-1">{{ $book->title }}</h1>
        <p class="text-zinc-600 mt-1">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">&middot; {{ $book->published_year }}</span>@endif</p>
        <p class="mono text-xs text-zinc-500 mt-2">ISBN {{ $book->isbn }}</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @if($book->stock > 0)<span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1.5 text-xs font-medium">Tersedia &mdash; {{ $book->stock }} eksemplar</span>@else<span class="inline-flex bg-zinc-100 text-zinc-700 border border-zinc-200 rounded-full px-3 py-1.5 text-xs font-medium" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>Stok habis</span>@endif
            <span class="inline-flex bg-zinc-50 border border-zinc-200 rounded-full px-3 py-1.5 text-xs text-zinc-600" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>Kategori {{ $book->category->name ?? '-' }}</span>
        </div>
        @if($book->description)
            <div class="mt-6 pt-6 border-t border-zinc-100">
                <p class="mono text-[11px] tracking-[0.12em] uppercase text-zinc-500">Deskripsi</p>
                <p class="mt-2 text-[15px] leading-relaxed text-zinc-700 max-w-[65ch]">{{ $book->description }}</p>
            </div>
        @endif
    </div>
    <div class="bg-zinc-900 text-zinc-100 rounded-2xl p-6 lg:p-7">
        <p class="mono text-[11px] tracking-[0.14em] uppercase text-zinc-400">Peminjaman</p>
        <h2 class="text-lg font-semibold mt-1">Pinjam buku ini</h2>
        <p class="text-sm text-zinc-400 mt-1">Durasi 7 hari sejak disetujui admin. Maksimal 3 pinjaman aktif per anggota.</p>
        <div class="mt-5">
            @auth
                @if($book->stock > 0)
                    <form method="POST" action="{{ route('loans.store') }}">@csrf<input type="hidden" name="book_id" value="{{ $book->id }}"><button type="submit" class="w-full bg-white text-zinc-900 rounded-full py-3 text-sm font-semibold hover:bg-zinc-100">Ajukan peminjaman</button></form>
                    <p class="text-xs text-zinc-500 mt-3 text-center">Admin akan menyetujui dalam 1x24 jam.</p>
                @else
                    <div class="bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-sm text-zinc-300 text-center">Stok habis &mdash; tidak dapat diajukan saat ini.</div>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full text-center bg-white text-zinc-900 rounded-full py-3 text-sm font-semibold hover:bg-zinc-100">Masuk untuk meminjam</a>
                <p class="text-xs text-zinc-500 mt-3 text-center">Belum punya akun? <a href="{{ route('register') }}" class="underline text-zinc-300">Daftar</a></p>
            @endauth
        </div>
        <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-2 gap-3 text-center">
            <div class="bg-white/5 rounded-xl py-3"><p class="mono text-[11px] uppercase tracking-[0.1em] text-zinc-400">Stok</p><p class="text-lg font-semibold">{{ $book->stock }}</p></div>
            <div class="bg-white/5 rounded-xl py-3"><p class="mono text-[11px] uppercase tracking-[0.1em] text-zinc-400">Dipinjam</p><p class="text-lg font-semibold">{{ $book->loans()->whereIn('status', ['approved','overdue'])->count() }}</p></div>
        </div>
    </div>
</div>
@endsection
