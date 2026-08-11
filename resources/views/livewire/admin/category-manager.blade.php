<div class="max-w-[1100px] mx-auto">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink" style="font-family: Literata, ui-serif, Georgia, serif">Kategori</h1>
            <p class="text-sm text-muted">Kelola kategori buku perpustakaan.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreate" class="!bg-[#4f46e5] !rounded-[8px]">Tambah kategori</flux:button>
    </div>

    <div class="mt-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kategori..." icon="magnifying-glass" class="!rounded-[10px] max-w-[360px]" />
    </div>

    <div class="mt-6 bg-white border border-line-soft rounded-[16px] overflow-hidden shadow-[0_1px_24px_rgba(27,27,36,.07)]">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-muted text-xs uppercase tracking-[0.06em]">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold">Nama</th>
                        <th class="text-left px-6 py-4 font-semibold">Slug</th>
                        <th class="text-center px-6 py-4 font-semibold">Buku</th>
                        <th class="text-right px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-[#f5f2ff]/50 transition-colors group">
                            <td class="px-6 py-4 font-medium text-ink">{{ $cat->name }}</td>
                            <td class="px-6 py-4"><span class="mono text-xs text-muted">{{ $cat->slug }}</span></td>
                            <td class="px-6 py-4 text-center"><flux:badge size="sm" class="!rounded-full">{{ $cat->books_count }}</flux:badge></td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $cat->id }})" class="!rounded-full" icon="pencil-square" aria-label="Edit {{ $cat->name }}" />
                                    <flux:button size="xs" variant="ghost" wire:click="delete({{ $cat->id }})" wire:confirm="Hapus kategori ini?" class="!text-red-600 hover:!bg-red-50 !rounded-full" icon="trash" aria-label="Hapus {{ $cat->name }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-8 text-zinc-500 text-sm">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-4 py-3 border-t border-line-soft bg-surface/60 flex justify-center">{{ $categories->links() }}</div>
        @endif
    </div>

    {{-- Modal kategori — Flux --}}
    <flux:modal wire:model.self="showForm" class="md:!w-[480px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="!font-display" style="font-family: Literata, ui-serif, Georgia, serif">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
                <flux:subheading>Kategori digunakan untuk mengorganisir katalog buku.</flux:subheading>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nama kategori <span class="text-[#93000a]">*</span></flux:label>
                    <flux:input wire:model="name" placeholder="Contoh: Fiksi, Sains & Teknologi" required />
                    <flux:error name="name" />
                </flux:field>
                <flux:field>
                    <flux:label>Slug <span class="text-zinc-400 font-normal">(opsional, auto dari nama)</span></flux:label>
                    <flux:input wire:model="slug" placeholder="fiksi" class="font-mono text-sm" />
                    <flux:description>Kosongkan untuk generate otomatis.</flux:description>
                    <flux:error name="slug" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="ghost" type="button" wire:click="closeForm" class="!rounded-[8px]">Batal</flux:button>
                    <flux:button variant="primary" type="submit" class="!bg-[#4f46e5] !rounded-[8px]">{{ $editingId ? 'Perbarui' : 'Simpan' }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
