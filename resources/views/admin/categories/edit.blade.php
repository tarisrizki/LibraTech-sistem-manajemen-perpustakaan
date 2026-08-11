@extends('layouts.app')
@section('content')
<div class="max-w-[520px] mx-auto bg-white border border-zinc-200 rounded-2xl p-6" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
    <h1 class="font-semibold">Edit kategori</h1>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mt-4 space-y-3">@csrf @method('PUT')
        <label class="block text-sm"><span class="text-zinc-700">Nama kategori</span><input type="text" name="name" value="{{ old('name', $category->name) }}" required class="mt-1 w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-sm" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none></label>
        <button type="submit" class="w-full bg-zinc-900 text-white rounded-full py-2.5 text-sm">Perbarui</button>
    </form>
</div>
@endsection
