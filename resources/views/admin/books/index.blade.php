@extends('layouts.app')
@section('content')
<div class="max-w-[1100px] mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold tracking-tight">Kelola buku</h1>
        <a href="{{ route('admin.books.create') }}" class="bg-zinc-900 text-white rounded-full px-4 py-2 text-sm hover:bg-black">Tambah buku</a>
    </div>
    <div class="mt-6 bg-white border border-zinc-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-600 text-xs uppercase tracking-[0.08em]"><tr><th class="text-left px-4 py-3">Buku</th><th class="text-left px-4 py-3">Kategori</th><th class="text-left px-4 py-3">Stok</th><th class="text-right px-4 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($books as $book)
                        <tr class="hover:bg-zinc-50/60">
                            <td class="px-4 py-3"><span class="font-medium">{{ $book->title }}</span><br><span class="text-xs text-zinc-500">{{ $book->author }} &middot; {{ $book->isbn }}</span></td>
                            <td class="px-4 py-3 text-xs"><span class="bg-zinc-50 border border-zinc-200 rounded-full px-2.5 py-1">{{ $book->category->name ?? '-' }}</span></td>
                            <td class="px-4 py-3"><span class="text-xs font-medium px-2.5 py-1 rounded-full border {{ $book->stock>0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">{{ $book->stock }}</span></td>
                            <td class="px-4 py-3 text-right"><div class="inline-flex gap-1.5"><a href="{{ route('admin.books.edit', $book) }}" class="border border-zinc-200 rounded-full px-3 py-1 text-xs hover:bg-zinc-50">Edit</a><form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')">@csrf @method('DELETE')<button type="submit" class="bg-red-50 text-red-700 border border-red-200 rounded-full px-3 py-1 text-xs">Hapus</button></form></div></td>
                        </tr>
                    @empty<tr><td colspan="4" class="text-center py-8 text-zinc-500">Belum ada buku.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4 flex justify-center">{{ $books->links() }}</div>
</div>
@endsection
