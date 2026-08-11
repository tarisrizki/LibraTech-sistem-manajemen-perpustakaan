<div class="max-w-[1100px] mx-auto">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink" style="font-family: Literata, ui-serif, Georgia, serif">Manajemen Peminjaman</h1>
            <p class="text-sm text-muted mt-1">Setujui, tolak, atau tandai pengembalian.</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari buku / anggota..." icon="magnifying-glass" class="!rounded-[10px] min-w-[220px]" />
        </div>
    </div>

    {{-- Filter pills status (Flux-consistent) --}}
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach($statuses as $val => $label)
            <button
                wire:click="setStatusFilter('{{ $val }}')"
                class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition {{ $statusFilter === $val ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-line hover:bg-zinc-50' }}"
            >{{ $label }}</button>
        @endforeach
    </div>

    <div class="mt-6 bg-white border border-line-soft rounded-[16px] overflow-hidden shadow-[0_1px_24px_rgba(27,27,36,.07)]">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-muted text-xs uppercase tracking-[0.06em]">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Buku</th>
                        <th class="text-left px-4 py-3 font-semibold">Anggota</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                        <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
                        <th class="text-right px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-[#f5f2ff]/50">
                            <td class="px-4 py-3">
                                <span class="font-medium text-ink">{{ $loan->book->title ?? '-' }}</span><br>
                                <span class="text-xs text-muted">{{ $loan->book->author ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-ink">{{ $loan->user->name ?? '-' }}</span><br>
                                <span class="text-xs text-muted">{{ $loan->user->email ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $map = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                        'returned' => 'bg-zinc-50 text-zinc-600 border-zinc-200',
                                        'overdue' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    ];
                                    $statusVal = $loan->status instanceof \App\Enums\LoanStatus ? $loan->status->value : (string)$loan->status;
                                @endphp
                                <flux:badge size="sm" class="border !rounded-full {{ $map[$statusVal] ?? '' }}">{{ ucfirst($statusVal) }}</flux:badge>
                                @if($loan->rejection_reason)
                                    <div class="text-[11px] text-muted mt-1 max-w-[180px] truncate" title="{{ $loan->rejection_reason }}">Alasan: {{ $loan->rejection_reason }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-muted">
                                {{ $loan->requested_at?->format('d M Y') }}
                                @if($loan->due_at)<br><span class="text-zinc-500">Tempo {{ $loan->due_at->format('d M Y') }}</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex gap-1.5 flex-wrap justify-end">
                                    @if($statusVal === 'pending')
                                        <flux:button size="xs" variant="primary" wire:click="approve({{ $loan->id }})" class="!rounded-full !bg-emerald-600">Setujui</flux:button>
                                        <flux:button size="xs" variant="ghost" wire:click="openReject({{ $loan->id }})" class="!rounded-full">Tolak</flux:button>
                                    @elseif(in_array($statusVal, ['approved','overdue'], true))
                                        <flux:button size="xs" variant="primary" wire:click="markReturned({{ $loan->id }})" class="!rounded-full !bg-[#4f46e5]">Tandai kembali</flux:button>
                                    @else
                                        <span class="text-xs text-zinc-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-zinc-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($loans->hasPages())
            <div class="px-4 py-3 border-t border-line-soft bg-surface/60 flex justify-center">{{ $loans->links() }}</div>
        @endif
    </div>

    {{-- Reject modal — Flux --}}
    <flux:modal wire:model.self="rejectId" class="md:!w-[420px]" :dismissible="false">
        @if($rejectId)
            <div class="space-y-5">
                <div>
                    <flux:heading>Konfirmasi penolakan</flux:heading>
                    <flux:subheading>Tuliskan alasan penolakan untuk anggota.</flux:subheading>
                </div>
                <flux:field>
                    <flux:label>Alasan <span class="text-[#93000a]">*</span></flux:label>
                    <flux:textarea wire:model="rejectionReason" rows="3" placeholder="Contoh: Stok tidak tersedia / data tidak valid" />
                    <flux:error name="rejectionReason" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" wire:click="cancelReject" class="!rounded-[8px]">Batal</flux:button>
                    <flux:button variant="primary" wire:click="confirmReject" class="!rounded-[8px] !bg-zinc-900">Tolak peminjaman</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
