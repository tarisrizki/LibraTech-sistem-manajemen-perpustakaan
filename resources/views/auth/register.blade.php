@extends('layouts.app')
@section('content')
<div class="max-w-[420px] mx-auto">
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 lg:p-7">
        <p class="mono text-[11px] tracking-[0.14em] uppercase text-zinc-500">Daftar</p>
        <h1 class="text-xl font-semibold tracking-tight mt-1">Buat akun anggota</h1>
        <p class="text-sm text-zinc-600 mt-1">Akses katalog dan ajukan peminjaman.</p>
        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-3">@csrf
            <label class="block text-sm"><span class="text-zinc-700">Nama</span><input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <label class="block text-sm"><span class="text-zinc-700">Email</span><input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <label class="block text-sm"><span class="text-zinc-700">Password (min 8 karakter)</span><input type="password" name="password" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <label class="block text-sm"><span class="text-zinc-700">Konfirmasi password</span><input type="password" name="password_confirmation" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <button type="submit" class="w-full bg-zinc-900 text-white rounded-full py-2.5 text-sm font-medium hover:bg-black mt-2">Daftar</button>
        </form>
        <p class="text-sm text-zinc-600 mt-4 text-center">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Masuk</a></p>
    </div>
</div>
@endsection
