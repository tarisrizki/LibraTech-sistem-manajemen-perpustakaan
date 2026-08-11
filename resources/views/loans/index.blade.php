@extends('layouts.app')
@section('content')
<div class="max-w-[900px] mx-auto">
    <h1 class="text-xl font-semibold tracking-tight">Peminjaman saya</h1>
    <p class="text-sm text-zinc-600 mt-1">Riwayat dan status pengajuan Anda.</p>
    <div class="mt-6 space-y-3">
        @forelse($loans as $loan)
            <div class="bg-white border border-zinc-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-sm">{{ $loan->book->title ?? '-' }}</p>
                    <p class="text-xs text-zinc-500">{{ $loan->book->author ?? '-' }} &middot; Diajukan {{ $loan->requested_at?->format('d M Y') }}</p>
                    @if($loan->due_at)<p class="text-xs text-zinc-500">Jatuh tempo {{ $loan->due_at->format('d M Y') }}</p>@endif
                    @if($loan->rejection_reason)<p class="text-xs text-red-600 mt-1">Alasan: {{ $loan->rejection_reason }}</p>@endif
                </div>
                @php $map=['pending'=>'bg-amber-50 text-amber-700 border-amber-200','approved'=>'bg-emerald-50 text-emerald-700 border-emerald-200','rejected'=>'bg-red-50 text-red-700 border-red-200','returned'=>'bg-zinc-50 text-zinc-600 border-zinc-200','overdue'=>'bg-orange-50 text-orange-700 border-orange-200']; @endphp
                <span class="self-start sm:self-auto inline-flex text-xs font-medium border rounded-full px-3 py-1 {{ $map[$loan->status->value] ?? $map['pending'] }}">{{ ucfirst($loan->status->value) }}</span>
            </div>
        @empty
            <div class="bg-white border border-dashed border-zinc-300 rounded-2xl p-8 text-center text-sm text-zinc-600">Belum ada peminjaman. <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:underline">Jelajahi katalog</a></div>
        @endforelse
    </div>
    <div class="mt-6 flex justify-center">{{ $loans->links() }}</div>
</div>
@endsection
