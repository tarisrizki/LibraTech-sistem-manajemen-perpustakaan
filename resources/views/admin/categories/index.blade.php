@extends('layouts.app')
@section('content')
<div class="max-w-[820px] mx-auto">
    <div class="flex items-center justify-between"><h1 class="text-xl font-semibold tracking-tight">Kategori</h1><a href="{{ route('admin.categories.create') }}" class="bg-zinc-900 text-white rounded-full px-4 py-2 text-sm">Tambah kategori</a></div>
    <div class="mt-6 bg-white border border-zinc-200 rounded-2xl overflow-hidden">
        <table class="w-full text-sm"><thead class="bg-zinc-50 text-zinc-600 text-xs uppercase tracking-[0.08em]"><tr><th class="text-left px-4 py-3">Nama</th><th class="text-left px-4 py-3">Slug</th><th class="text-right px-4 py-3">Aksi</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($categories as $cat)<tr class="hover:bg-zinc-50/60"><td class="px-4 py-3 font-medium">{{ $cat->name }}</td><td class="px-4 py-3 mono text-xs text-zinc-500">{{ $cat->slug }}</td><td class="px-4 py-3 text-right"><div class="inline-flex gap-1.5"><a href="{{ route('admin.categories.edit', $cat) }}" class="border border-zinc-200 rounded-full px-3 py-1 text-xs">Edit</a><form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button type="submit" class="bg-red-50 text-red-700 border border-red-200 rounded-full px-3 py-1 text-xs">Hapus</button></form></div></td></tr>
                @empty<tr><td colspan="3" class="text-center py-8 text-zinc-500">Belum ada kategori.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
