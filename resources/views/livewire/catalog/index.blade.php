<div
    x-data="{
        localSearch: @entangle('search').live,
        init() {
            const hero = this.$refs.hero;
            if (hero && window.motion) motion.animate(hero, {opacity:[0,1], y:[8,0]}, {duration:0.45, easing:'ease-out'});
            this.$nextTick(() => {
                const els = this.$refs.grid ? this.$refs.grid.querySelectorAll('[data-stagger]') : [];
                if (els.length && window.motion) motion.animate(els, {opacity:[0,1], y:[10,0]}, {delay: motion.stagger(0.04), duration:0.32, easing:'ease-out'});
            });
        }
    }"
    class="flex flex-col gap-8"
>
    {{-- Hero: Discover your next read — Stitch 4fa --}}
    <section x-ref="hero" class="rounded-[20px] bg-white border border-zinc-200 overflow-hidden">
        <div class="grid lg:grid-cols-2 items-center">
            <div class="p-6 lg:p-8 space-y-4">
                <p class="mono text-[11px] tracking-[0.16em] uppercase text-zinc-500">LibraTech — Katalog</p>
                <h1 class="font-[Literata,ui-serif,Georgia,serif] text-[28px] lg:text-[40px] font-bold tracking-[-0.02em] leading-none text-ink">Discover your next read.</h1>
                <p class="text-[15px] leading-relaxed text-zinc-600 max-w-[52ch]">Jelajahi koleksi kurasi LibraTech — fiksi, nonfiksi, dan ruang hening untuk pikiran. Cari judul/penulis, saring kategori, dan pinjam ketika stok tersedia.</p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <flux:button variant="primary" href="#browse" class="!rounded-full !bg-indigo-600 hover:!bg-indigo-700" wire:ignore>Browse Full Catalog</flux:button>
                    <flux:button variant="outline" class="!rounded-full" wire:ignore>View Curated Lists</flux:button>
                </div>
            </div>
            <div class="hidden lg:block h-[280px] bg-zinc-50 border-l border-zinc-100 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=1200&auto=format&fit=crop" alt="Library reading room" class="w-full h-full object-cover" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-l from-transparent to-white/10"></div>
            </div>
        </div>

        {{-- Search pill — Flux + Alpine debounce, URL-synced via livewire entangle --}}
        <div id="browse" class="border-t border-zinc-100 bg-zinc-50/60 p-3 lg:p-4">
            <div class="flex flex-wrap gap-2 items-center">
                <flux:field class="flex-1 min-w-[220px]">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        x-model="localSearch"
                        placeholder="Cari judul, penulis…"
                        icon="magnifying-glass"
                        class="!rounded-full"
                    />
                </flux:field>
                <flux:select wire:model.live="categoryId" placeholder="Semua kategori" class="min-w-[180px] !rounded-full">
                    <flux:select.option value="">Semua kategori</flux:select.option>
                    @foreach($categories as $cat)
                        <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <label class="inline-flex items-center gap-2 bg-white border border-zinc-200 rounded-full px-3 py-2 text-sm cursor-pointer select-none">
                    <input type="checkbox" wire:model.live="available" class="accent-indigo-600 rounded"> Tersedia
                </label>
                <flux:select wire:model.live="sort" class="min-w-[150px] !rounded-full">
                    <flux:select.option value="">Terbaru</flux:select.option>
                    <flux:select.option value="title">Judul A-Z</flux:select.option>
                    <flux:select.option value="popular">Populer</flux:select.option>
                </flux:select>
                @if($search !== '' || $categoryId !== '' || $available || $sort !== '')
                    <flux:button variant="ghost" size="sm" wire:click="resetFilters" class="!rounded-full">Reset</flux:button>
                @endif
            </div>
        </div>
    </section>

    {{-- Featured 2-col — Stitch bento: The Architecture of Silence etc, queried --}}
    @if($featured->count())
    <section>
        <div class="flex items-end justify-between gap-4">
            <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[22px] lg:text-[28px] font-semibold tracking-[-0.02em]">Featured Books</h2>
            <span class="hidden sm:inline text-xs text-zinc-500 mono">{{ $featured->count() }} pilihan kurator</span>
        </div>
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4 auto-rows-[300px]">
            @foreach($featured as $idx => $book)
                @if($idx === 0)
                <flux:card class="lg:col-span-2 !p-5 !rounded-[16px] hover:shadow-[0_8px_20px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition flex flex-col lg:flex-row gap-5 overflow-hidden group">
                    <div class="w-full lg:w-[38%] h-48 lg:h-full rounded-xl overflow-hidden bg-zinc-100 shrink-0">
                        @if($book->cover_path)
                            <img src="{{ Storage::url($book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition">
                        @else
                            <div class="w-full h-full grid place-items-center text-zinc-400 text-xs border border-dashed border-zinc-200 rounded-xl">cover</div>
                        @endif
                    </div>
                    <div class="flex flex-col flex-1 min-w-0">
                        <span class="inline-flex items-center gap-1.5 w-fit text-[11px] font-medium rounded-full px-2.5 py-1 border {{ $book->stock > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-600 border-zinc-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $book->stock > 0 ? 'bg-emerald-600' : 'bg-zinc-400' }}"></span>{{ $book->stock > 0 ? 'Available' : 'Habis' }}
                        </span>
                        <a href="{{ route('catalog.show', $book) }}" class="mt-2 font-semibold leading-tight line-clamp-2 hover:text-indigo-700">{{ $book->title }}</a>
                        <p class="text-sm text-zinc-600">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">· {{ $book->published_year }}</span>@endif</p>
                        <p class="text-[13px] leading-relaxed text-zinc-600 line-clamp-3 mt-2">{{ $book->description ?: 'Koleksi pilihan — pinjam untuk membaca.' }}</p>
                        <div class="mt-auto pt-3">
                            @if($book->stock > 0)
                                <flux:button variant="primary" size="sm" href="{{ route('catalog.show', $book) }}" class="!rounded-full !bg-indigo-600 hover:!bg-indigo-700">Place Hold</flux:button>
                            @else
                                <flux:button variant="outline" size="sm" href="{{ route('catalog.show', $book) }}" class="!rounded-full">Join Waitlist</flux:button>
                            @endif
                        </div>
                    </div>
                </flux:card>
                @else
                <flux:card class=" !p-5 !rounded-[16px] hover:shadow-[0_8px_20px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition flex flex-col group">
                    <div class="w-full h-40 rounded-xl overflow-hidden bg-zinc-100 shrink-0">
                        @if($book->cover_path)
                            <img src="{{ Storage::url($book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition">
                        @else
                            <div class="w-full h-full grid place-items-center text-zinc-400 text-xs border border-dashed border-zinc-200 rounded-xl">cover</div>
                        @endif
                    </div>
                    <span class="mt-3 inline-flex w-fit text-[11px] font-medium rounded-full px-2.5 py-1 border {{ $book->stock > 0 ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-zinc-100 text-zinc-600 border-zinc-200' }}">{{ $book->stock > 0 ? 'Available' : 'Borrowed' }}</span>
                    <a href="{{ route('catalog.show', $book) }}" class="mt-2 font-semibold leading-tight line-clamp-1 hover:text-indigo-700">{{ $book->title }}</a>
                    <p class="text-sm text-zinc-600">{{ $book->author }}</p>
                    <flux:button variant="outline" size="sm" href="{{ route('catalog.show', $book) }}" class="mt-3 !rounded-full w-full justify-center">{{ $book->stock > 0 ? 'Place Hold' : 'Join Waitlist' }}</flux:button>
                </flux:card>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    {{-- New Arrivals 4-col — queried latest 4 --}}
    @if($newArrivals->count())
    <section>
        <div class="flex items-end justify-between">
            <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[22px] lg:text-[28px] font-semibold tracking-[-0.02em]">New Arrivals</h2>
            <a href="#browse" class="hidden sm:inline text-sm text-indigo-600 hover:underline">View All New</a>
        </div>
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($newArrivals as $book)
                <a href="{{ route('catalog.show', $book) }}" class="group bg-white border border-zinc-200 rounded-[16px] p-3 hover:border-zinc-300 hover:shadow-sm transition flex flex-col">
                    <div class="aspect-[2/3] rounded-xl overflow-hidden bg-zinc-100 relative">
                        @if($book->cover_path)
                            <img src="{{ Storage::url($book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition">
                        @else
                            <div class="w-full h-full grid place-items-center text-zinc-400 text-xs">cover</div>
                        @endif
                        <span class="absolute top-2 right-2 w-6 h-6 grid place-items-center rounded-full text-[11px] border shadow-sm {{ $book->stock > 0 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-zinc-500 border-zinc-200' }}">{{ $book->stock > 0 ? '✓' : '◷' }}</span>
                    </div>
                    <p class="mt-3 font-semibold text-[13px] leading-tight line-clamp-1 group-hover:text-indigo-700">{{ $book->title }}</p>
                    <p class="text-xs text-zinc-600 line-clamp-1">{{ $book->author }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Browse grid — Livewire paginated, Alpine motion stagger --}}
    <section>
        <p class="mono text-[11px] tracking-[0.14em] uppercase text-zinc-500">Browse — {{ $books->total() }} buku</p>
        @if($books->count())
            <div x-ref="grid" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($books as $book)
                    <a data-stagger href="{{ route('catalog.show', $book) }}" wire:key="book-{{ $book->id }}" class="group bg-white border border-zinc-200 rounded-[16px] p-4 hover:border-zinc-300 hover:shadow-sm transition flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <span class="mono text-[11px] tracking-[0.1em] uppercase text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-full px-2.5 py-1">{{ $book->category->name ?? '-' }}</span>
                            @if($book->stock > 0)<span class="text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2 py-1">Tersedia</span>@else<span class="text-[11px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200 rounded-full px-2 py-1">Habis</span>@endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold leading-tight line-clamp-2 group-hover:text-indigo-700">{{ $book->title }}</h3>
                            <p class="text-sm text-zinc-600 mt-1">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">{{ $book->published_year }}</span>@endif</p>
                            <p class="mono text-[11px] text-zinc-500 mt-2">ISBN {{ $book->isbn }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-zinc-100 text-xs">
                            <span class="text-zinc-600">Stok <b class="text-zinc-900">{{ $book->stock }}</b></span>
                            <span class="text-indigo-600 font-medium group-hover:underline">Lihat detail</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6 flex justify-center">{{ $books->links() }}</div>
        @else
            <div class="mt-3 bg-white border border-dashed border-zinc-300 rounded-[16px] p-10 text-center">
                <p class="font-medium">Tidak ada buku ditemukan</p>
                <p class="text-sm text-zinc-600 mt-1">Coba ubah kata kunci atau filter.</p>
                <flux:button variant="primary" size="sm" wire:click="resetFilters" class="mt-4 !rounded-full !bg-zinc-900 hover:!bg-black">Lihat semua buku</flux:button>
            </div>
        @endif
    </section>
</div>
