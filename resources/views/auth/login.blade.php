@extends('layouts.app')
@section('content')
<div class="max-w-[420px] mx-auto">
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 lg:p-7">
        <p class="mono text-[11px] tracking-[0.14em] uppercase text-zinc-500">Masuk</p>
        <h1 class="text-xl font-semibold tracking-tight mt-1">Selamat datang kembali</h1>
        <p class="text-sm text-zinc-600 mt-1">Masuk untuk mengelola peminjaman Anda.</p>
        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-3">@csrf
            <label class="block text-sm"><span class="text-zinc-700">Email</span><input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <label class="block text-sm"><span class="text-zinc-700">Password</span><input type="password" name="password" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-300"></label>
            <button type="submit" class="w-full bg-zinc-900 text-white rounded-full py-2.5 text-sm font-medium hover:bg-black mt-2">Masuk</button>
        </form>
        <p class="text-sm text-zinc-600 mt-4 text-center">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Daftar</a></p>
        <div class="mt-4 bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-xs text-zinc-600"><span class="font-medium">Demo:</span> admin@libratech.test / password &middot; member: andi@libratech.test / password</div>
    </div>
</div>
@endsection
