<div
    x-data="{}"
    class="max-w-[1280px] mx-auto"
    {{-- motion spring handled by Alpine + flux:modal --}}
>
    {{-- Header — Stitch 787 Manajemen Buku Standardized --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-[28px] md:text-[32px] font-semibold tracking-tight text-ink leading-tight" style="font-family: Literata, ui-serif, Georgia, serif">Manajemen Buku</h1>
            <p class="text-sm text-muted mt-1">Kelola katalog literatur perpustakaan Anda.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreate" class="self-start md:self-auto !bg-[#4f46e5] !rounded-[8px]">
            Tambah Buku
        </flux:button>
    </div>

    {{-- Filter pill + search — Stitch 787 --}}
    <div class="mt-6 flex flex-col md:flex-row gap-3 md:items-center">
        <div class="relative flex-1 max-w-[420px]">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari judul, penulis, atau ISBN..."
                icon="magnifying-glass"
                class="!rounded-[10px]"
            />
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button
                wire:click="setFilterPill('semua')"
                class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition {{ $this->filterPill === 'semua' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-line hover:bg-zinc-50' }}"
            >Semua</button>
            @foreach($categories as $cat)
                <button
                    wire:click="setFilterPill('{{ $cat->id }}')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition {{ $this->filterPill === (string)$cat->id ? 'bg-[#4f46e5] text-white border-[#4f46e5]' : 'bg-white text-zinc-600 border-line hover:bg-zinc-50' }}"
                >{{ $cat->name }}</button>
            @endforeach
            <button
                wire:click="setFilterPill('habis')"
                class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition {{ $this->filterPill === 'habis' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-zinc-600 border-line hover:bg-zinc-50' }}"
            >Habis</button>
        </div>
        <div class="md:ml-auto">
            <flux:select wire:model.live="categoryFilter" placeholder="Semua Kategori" class="!rounded-[10px] min-w-[180px]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                @endforeach
                <flux:select.option value="habis">Stok habis</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Table Card — Buku | Kategori | Stok | Aksi --}}
    <div
        class="mt-6 bg-white border border-line-soft rounded-[16px] overflow-hidden shadow-[0_1px_24px_rgba(27,27,36,.07)]"
        x-data="{ stagger() { if(window.motion){ window.motion.animate('[data-row]', {opacity:[0,1], y:[6,0]}, {delay: window.motion.stagger(0.04), duration:0.18, easing:'ease-out'}) } } }"
        x-init="stagger()"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b border-line-soft bg-surface text-muted text-xs uppercase tracking-[0.06em]">
                        <th class="text-left px-6 py-4 font-semibold whitespace-nowrap">Buku</th>
                        <th class="text-left px-6 py-4 font-semibold whitespace-nowrap">Kategori</th>
                        <th class="text-left px-6 py-4 font-semibold whitespace-nowrap">Stok</th>
                        <th class="text-right px-6 py-4 font-semibold whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($books as $book)
                        <tr data-row class="hover:bg-[#f5f2ff]/60 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if($book->cover_path)
                                        <img src="{{ Storage::url($book->cover_path) }}" alt="cover {{ $book->title }}" class="w-12 h-16 object-cover rounded-[8px] border border-line-soft shadow-sm shrink-0" />
                                    @else
                                        <div class="w-12 h-16 rounded-[8px] border border-line-soft bg-zinc-50 grid place-items-center shrink-0 text-zinc-400">
                                            <flux:icon.photo class="w-5 h-5" />
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-medium text-ink truncate">{{ $book->title }}</div>
                                        <div class="text-xs text-muted truncate">{{ $book->author }}</div>
                                        <div class="text-[11px] tracking-wide text-zinc-400 font-mono mt-0.5">ISBN: {{ $book->isbn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="zinc" class="!rounded-full !bg-[#4f46e5]/10 !text-[#4f46e5] !border-[#4f46e5]/15 border">
                                    {{ $book->category->name ?? '-' }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                @if($book->stock > 0)
                                    <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">{{ $book->stock }}</span>
                                @else
                                    <div class="inline-flex flex-col gap-0.5">
                                        <span class="inline-flex text-xs font-medium px-2.5 py-1 rounded-full border bg-red-50 text-red-700 border-red-200">0</span>
                                        <span class="text-[11px] text-red-600">Habis</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="openEdit({{ $book->id }})" class="!rounded-full" aria-label="Edit {{ $book->title }}" />
                                    <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $book->id }})" wire:confirm="Hapus buku ini?" class="!text-red-600 hover:!bg-red-50 !rounded-full" aria-label="Hapus {{ $book->title }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-10 text-zinc-500 text-sm">Belum ada buku. Klik Tambah Buku untuk menambah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($books->hasPages())
            <div class="px-4 py-3 border-t border-line-soft bg-surface/60 flex justify-center">
                {{ $books->links() }}
            </div>
        @endif
    </div>

    {{-- Sheet slide flux:modal — Stitch 787 slide-over kanan --}}
    <flux:modal wire:model.self="showForm" variant="flyout" class="md:!w-[520px] !p-0 overflow-hidden" :dismissible="false">
        <div class="flex flex-col h-full max-h-[100dvh]">
            {{-- header --}}
            <div class="px-6 py-5 border-b border-line-soft flex items-center justify-between bg-surface shrink-0">
                <h2 class="font-display text-xl font-semibold text-ink" style="font-family: Literata, ui-serif, Georgia, serif">
                    {{ $editingId ? 'Edit Buku' : 'Tambah Buku Baru' }}
                </h2>
                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="closeForm" class="!rounded-full" aria-label="Tutup" />
            </div>

            {{-- body --}}
            <form wire:submit="save" class="flex-1 overflow-y-auto p-6 space-y-5 bg-white">
                <flux:field>
                    <flux:label>Judul Buku <span class="text-[#93000a]">*</span></flux:label>
                    <flux:input wire:model="title" placeholder="Masukkan judul buku" required />
                    <flux:error name="title" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Penulis <span class="text-[#93000a]">*</span></flux:label>
                        <flux:input wire:model="author" placeholder="Nama penulis" required />
                        <flux:error name="author" />
                    </flux:field>
                    <flux:field>
                        <flux:label>ISBN <span class="text-[#93000a]">*</span></flux:label>
                        <flux:input wire:model="isbn" placeholder="Mis. 978-..." class="font-mono text-sm" required />
                        <flux:error name="isbn" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-[1fr_130px] gap-4">
                    <flux:field>
                        <flux:label>Kategori <span class="text-[#93000a]">*</span></flux:label>
                        <flux:select wire:model="category_id" placeholder="Pilih kategori" required>
                            @foreach($categories as $cat)
                                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="category_id" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Stok <span class="text-[#93000a]">*</span></flux:label>
                        <flux:input type="number" wire:model="stock" min="0" required />
                        <flux:error name="stock" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Tahun terbit</flux:label>
                    <flux:input type="number" wire:model="published_year" placeholder="2024" />
                    <flux:error name="published_year" />
                </flux:field>

                <flux:field>
                    <flux:label>Deskripsi Singkat</flux:label>
                    <flux:textarea wire:model="description" rows="4" placeholder="Sinopsis atau catatan admin..." />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>Gambar Sampul</flux:label>
                    @if($existingCover && !$cover)
                        <div class="mb-2">
                            <img src="{{ Storage::url($existingCover) }}" alt="cover preview" class="w-20 h-28 object-cover rounded-[10px] border border-line-soft" />
                            <p class="text-xs text-muted mt-1">Sampul saat ini</p>
                        </div>
                    @endif
                    @if($cover)
                        <div class="mb-2 text-xs text-emerald-700">File terpilih: {{ $cover->getClientOriginalName() }}</div>
                    @endif
                    <div
                        class="border-2 border-dashed border-line rounded-[12px] p-6 flex flex-col items-center justify-center bg-surface hover:bg-[#f5f2ff] transition-colors cursor-pointer group"
                        x-data="{ dragover:false }"
                        @dragover.prevent="dragover=true"
                        @dragleave="dragover=false"
                        @drop.prevent="dragover=false"
                        :class="dragover ? 'border-[#4f46e5] bg-[#4f46e5]/5' : ''"
                    >
                        <div class="w-12 h-12 rounded-full bg-zinc-100 grid place-items-center mb-3 group-hover:bg-[#4f46e5]/10 transition-colors">
                            <flux:icon.cloud-arrow-up class="w-6 h-6 text-zinc-400 group-hover:text-[#4f46e5]" />
                        </div>
                        <label class="text-sm text-ink text-center cursor-pointer">
                            <span class="font-semibold text-[#4f46e5]">Klik untuk unggah</span> atau seret dan lepas
                            <input type="file" wire:model="cover" accept="image/*" class="hidden" />
                        </label>
                        <p class="text-xs text-muted mt-1">PNG, JPG atau WEBP (Maks. 2MB)</p>
                    </div>
                    <flux:error name="cover" />
                    {{-- native file input fallback for Livewire --}}
                    <flux:input type="file" wire:model="cover" accept="image/jpeg,image/png,image/jpg,image/webp" class="mt-3" />
                </flux:field>
            </form>

            {{-- footer --}}
            <div class="px-6 py-4 border-t border-line-soft bg-surface flex justify-end gap-3 shrink-0">
                <flux:button variant="ghost" wire:click="closeForm" class="!rounded-[8px]">Batal</flux:button>
                <flux:button variant="primary" wire:click="save" class="!bg-[#4f46e5] !rounded-[8px]">
                    <span wire:loading.remove wire:target="save,cover">{{ $editingId ? 'Perbarui Buku' : 'Simpan Buku' }}</span>
                    <span wire:loading wire:target="save">Menyimpan…</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
