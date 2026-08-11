@extends('layouts.app')
@section('content')
<div class="max-w-[1100px] mx-auto">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><h1 class="text-xl font-semibold tracking-tight">Manajemen peminjaman</h1><p class="text-sm text-zinc-600 mt-1">Setujui, tolak, atau tandai pengembalian.</p></div>
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="border border-zinc-200 rounded-full px-3 py-2 text-sm bg-white" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
                <option value="">Semua status</option>
                @foreach(['pending','approved','rejected','returned','overdue'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </form>
    </div>
    <div class="mt-6 bg-white border border-zinc-200 rounded-2xl overflow-hidden" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-600 text-xs uppercase tracking-[0.08em]"><tr><th class="text-left px-4 py-3">Buku</th><th class="text-left px-4 py-3">Anggota</th><th class="text-left px-4 py-3">Status</th><th class="text-left px-4 py-3">Tanggal</th><th class="text-right px-4 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-zinc-50/60">
                            <td class="px-4 py-3"><span class="font-medium">{{ $loan->book->title ?? '-' }}</span><br><span class="text-xs text-zinc-500">{{ $loan->book->author ?? '-' }}</span></td>
                            <td class="px-4 py-3"><span class="font-medium">{{ $loan->user->name ?? '-' }}</span><br><span class="text-xs text-zinc-500">{{ $loan->user->email ?? '-' }}</span></td>
                            <td class="px-4 py-3">@php $map=['pending'=>'bg-amber-50 text-amber-700 border-amber-200','approved'=>'bg-emerald-50 text-emerald-700 border-emerald-200','rejected'=>'bg-red-50 text-red-700 border-red-200','returned'=>'bg-zinc-50 text-zinc-600 border-zinc-200','overdue'=>'bg-orange-50 text-orange-700 border-orange-200']; @endphp<span class="inline-flex text-xs font-medium border rounded-full px-2.5 py-1 {{ $map[$loan->status->value] ?? '' }}">{{ ucfirst($loan->status->value) }}</span></td>
                            <td class="px-4 py-3 text-xs text-zinc-600">{{ $loan->requested_at?->format('d M Y') }} @if($loan->due_at)<br><span class="text-zinc-500">Tempo {{ $loan->due_at->format('d M Y') }}</span>@endif</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex gap-1.5 flex-wrap justify-end">
                                    @if($loan->status->value==='pending')
                                        <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">@csrf<button type="submit" class="bg-emerald-600 text-white rounded-full px-3 py-1 text-xs">Setujui</button></form>
                                        <form method="POST" action="{{ route('admin.loans.reject', $loan) }}" class="inline-flex gap-1">@csrf<input type="text" name="rejection_reason" placeholder="Alasan" required class="border border-zinc-200 rounded-full px-2 py-1 text-xs w-28" focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none><button type="submit" class="bg-zinc-900 text-white rounded-full px-3 py-1 text-xs">Tolak</button></form>
                                    @elseif(in_array($loan->status->value, ['approved','overdue'], true))
                                        <form method="POST" action="{{ route('admin.loans.return', $loan) }}">@csrf<button type="submit" class="bg-indigo-600 text-white rounded-full px-3 py-1 text-xs">Tandai kembali</button></form>
                                    @else<span class="text-xs text-zinc-400">&mdash;</span>@endif
                                </div>
                            </td>
                        </tr>
                    @empty<tr><td colspan="5" class="text-center py-8 text-zinc-500">Tidak ada data.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4 flex justify-center">{{ $loans->withQueryString()->links() }}</div>
</div>
@endsection
