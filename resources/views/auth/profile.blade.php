@extends('layouts.app')
@section('content')
<div class="max-w-[720px] mx-auto space-y-6">
    <div class="bg-white border border-zinc-200 rounded-2xl p-6" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
        <h1 class="font-semibold">Profil</h1>
        <p class="text-sm text-zinc-600">Perbarui informasi akun Anda.</p>
        <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-3">@csrf @method('PATCH')
            <label class="block text-sm"><span class="text-zinc-700">Nama</span><input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
            <label class="block text-sm"><span class="text-zinc-700">Email</span><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
            <button type="submit" class="bg-zinc-900 text-white rounded-full px-5 py-2 text-sm">Simpan</button>
        </form>
    </div>
    <div class="bg-white border border-zinc-200 rounded-2xl p-6" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
        <h2 class="font-semibold">Ganti password</h2>
        <form method="POST" action="{{ route('profile.password') }}" class="mt-4 space-y-3">@csrf @method('PATCH')
            <label class="block text-sm"><span class="text-zinc-700">Password saat ini</span><input type="password" name="current_password" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
            <label class="block text-sm"><span class="text-zinc-700">Password baru</span><input type="password" name="password" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
            <label class="block text-sm"><span class="text-zinc-700">Konfirmasi password baru</span><input type="password" name="password_confirmation" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
            <button type="submit" class="bg-zinc-900 text-white rounded-full px-5 py-2 text-sm">Ubah password</button>
        </form>
    </div>
</div>
@endsection
