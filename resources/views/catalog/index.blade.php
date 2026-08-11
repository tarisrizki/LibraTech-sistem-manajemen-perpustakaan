@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-6">
    <div class="rounded-[20px] bg-white border border-zinc-200 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <p class="mono text-[11px] tracking-[0.16em] uppercase text-zinc-500">Katalog</p>
            <h1 class="text-[28px] lg:text-[34px] font-semibold tracking-[-0.03em] leading-none mt-1">Temukan bacaan<br>berikutnya.</h1>
            <p class="text-sm text-zinc-600 mt-2 max-w-[48ch]">Jelajahi koleksi LibraTech. Cari judul atau penulis, saring kategori, dan pinjam langsung ketika stok tersedia.</p>
        </div>
        <form method="GET" class="flex flex-wrap gap-2 items-center bg-zinc-50 border border-zinc-200 rounded-2xl p-2 w-full lg:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis" class="flex-1 min-w-[180px] bg-white border border-zinc-200 rounded-full px-4 py-2 text-sm outline-none focus:border-indigo-300">
            <select name="category_id" class="bg-white border border-zinc-200 rounded-full px-3 py-2 text-sm">
                <option value="">Semua kategori</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>{{ $cat->name }}</option>@endforeach
            </select>
            <label class="flex items-center gap-1.5 text-sm bg-white border border-zinc-200 rounded-full px-3 py-2 cursor-pointer"><input type="checkbox" name="available" value="1" @checked(request('available')) class="accent-indigo-600"> Tersedia</label>
            <select name="sort" class="bg-white border border-zinc-200 rounded-full px-3 py-2 text-sm">
                <option value="">Terbaru</option>
                <option value="title" @selected(request('sort')==='title')>Judul A-Z</option>
                <option value="popular" @selected(request('sort')==='popular')>Populer</option>
            </select>
            <button type="submit" class="bg-zinc-900 text-white rounded-full px-5 py-2 text-sm font-medium hover:bg-black">Terapkan</button>
            @if(request()->hasAny(['search','category_id','available','sort']))<a href="{{ route('catalog.index') }}" class="text-sm text-zinc-600 hover:text-zinc-900 px-2">Reset</a>@endif
        </form>
    </div>

    @if($books->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($books as $book)
                <a href="{{ route('catalog.show', $book) }}" class="group bg-white border border-zinc-200 rounded-2xl p-4 hover:border-zinc-300 hover:shadow-sm transition flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <span class="mono text-[11px] tracking-[0.1em] uppercase text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-full px-2.5 py-1">{{ $book->category->name ?? '-' }}</span>
                        @if($book->stock > 0)<span class="text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2 py-1">Tersedia</span>@else<span class="text-[11px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200 rounded-full px-2 py-1">Habis</span>@endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold leading-tight line-clamp-2 group-hover:text-indigo-700">{{ $book->title }}</h3>
                        <p class="text-sm text-zinc-600 mt-1">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">{{ $book->published_year }}</span>@endif</p>
                        <p class="mono text-[11px] text-zinc-500 mt-2">ISBN {{ $book->isbn }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-zinc-100 text-xs">
                        <span class="text-zinc-600">Stok <b class="text-zinc-900">{{ $book->stock }}</b></span>
                        <span class="text-indigo-600 font-medium group-hover:underline">Lihat detail</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="flex justify-center">{{ $books->links() }}</div>
    @else
        <div class="bg-white border border-dashed border-zinc-300 rounded-2xl p-10 text-center">
            <p class="font-medium">Tidak ada buku ditemukan</p>
            <p class="text-sm text-zinc-600 mt-1">Coba ubah kata kunci atau filter kategori.</p>
            <a href="{{ route('catalog.index') }}" class="inline-block mt-4 px-4 py-2 rounded-full bg-zinc-900 text-white text-sm">Lihat semua buku</a>
        </div>
    @endif
</div>
@endsection
