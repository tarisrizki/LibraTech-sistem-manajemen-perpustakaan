<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LibraTech - Manajemen Perpustakaan</title>
    @fluxAppearance
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#fdfcf8] text-[#0f172a] min-h-[100dvh] antialiased">
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-zinc-200">
        <nav class="max-w-[1280px] mx-auto px-4 lg:px-6 h-[64px] flex items-center justify-between gap-6">
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="w-8 h-8 rounded-lg bg-[#3525cd] text-white grid place-items-center font-bold text-sm">Lt</span>
                <span class="font-semibold tracking-tight">LibraTech</span>
                <span class="hidden sm:inline text-[11px] tracking-[0.14em] uppercase text-zinc-500 border-l border-zinc-200 pl-2 ml-1">Perpustakaan</span>
            </a>
            <div class="flex items-center gap-1 sm:gap-2 text-[13.5px]">
                <a href="{{ route('catalog.index') }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-100 {{ request()->routeIs('catalog.*') ? 'bg-zinc-900 text-white hover:bg-zinc-900' : 'text-zinc-600' }}">Katalog</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.books.index') }}" class="hidden md:inline px-3 py-1.5 rounded-full hover:bg-zinc-100 {{ request()->routeIs('admin.books.*') ? 'bg-zinc-900 text-white' : 'text-zinc-600' }}">Buku</a>
                        <a href="{{ route('admin.categories.index') }}" class="hidden md:inline px-3 py-1.5 rounded-full hover:bg-zinc-100 {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-900 text-white' : 'text-zinc-600' }}">Kategori</a>
                        <a href="{{ route('admin.loans.index') }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-100 {{ request()->routeIs('admin.loans.*') ? 'bg-zinc-900 text-white' : 'text-zinc-600' }}">Peminjaman</a>
                    @else
                        <a href="{{ route('loans.index') }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-100 {{ request()->routeIs('loans.*') ? 'bg-zinc-900 text-white' : 'text-zinc-600' }}">Pinjaman Saya</a>
                    @endif
                    <span class="hidden sm:inline w-px h-4 bg-zinc-200 mx-1"></span>
                    <a href="{{ route('profile.edit') }}" class="hidden sm:inline text-zinc-600 hover:text-zinc-900 truncate max-w-[120px]">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="px-3 py-1.5 rounded-full border border-zinc-200 hover:bg-zinc-50 text-zinc-700">Keluar</button></form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-full hover:bg-zinc-100 text-zinc-600">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-full bg-[#3525cd] text-white hover:bg-[#2b1bb5] font-medium">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    @if(session('success'))
        <div class="max-w-[1280px] mx-auto px-4 lg:px-6 mt-4"><div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div></div>
    @endif
    @if(session('error'))
        <div class="max-w-[1280px] mx-auto px-4 lg:px-6 mt-4"><div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div></div>
    @endif
    @if($errors->any())
        <div class="max-w-[1280px] mx-auto px-4 lg:px-6 mt-4"><div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-xl text-sm"><ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif

    <main class="max-w-[1280px] mx-auto px-4 lg:px-6 py-6 lg:py-8">{{ $slot ?? '' }}@yield('content')</main>
    <footer class="border-t border-zinc-200 mt-12 py-6 text-center text-xs text-zinc-500">LibraTech · Sistem Manajemen Perpustakaan · {{ date('Y') }}</footer>
    @fluxScripts
    @livewireScripts
</body>
</html>
