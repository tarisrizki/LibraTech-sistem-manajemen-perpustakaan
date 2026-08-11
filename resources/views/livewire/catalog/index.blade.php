<div
    x-data="{
        localSearch: @entangle('search').live,
        init() {
            const hero = this.$refs.hero;
            if (hero && window.motion) motion.animate(hero, {opacity:[0,1], y:[6,0]}, {duration:0.18, easing:'ease-out'});
            this.$nextTick(() => {
                const els = this.$refs.grid ? this.$refs.grid.querySelectorAll('[data-stagger]') : [];
                if (els.length && window.motion) motion.animate(els, {opacity:[0,1], y:[8,0]}, {delay: motion.stagger(0.04), duration:0.18, easing:'ease-out'});
                const feat = this.$refs.featured ? this.$refs.featured.querySelectorAll('[data-stagger]') : [];
                if (feat.length && window.motion) motion.animate(feat, {opacity:[0,1], y:[8,0]}, {delay: motion.stagger(0.04), duration:0.18, easing:'ease-out'});
                const arrivals = this.$refs.arrivals ? this.$refs.arrivals.querySelectorAll('[data-stagger]') : [];
                if (arrivals.length && window.motion) motion.animate(arrivals, {opacity:[0,1], y:[8,0]}, {delay: motion.stagger(0.04), duration:0.18, easing:'ease-out'});
            });
        }
    }"
    class="flex flex-col gap-6"
>
    {{-- Hero --}}
    <section x-ref="hero" class="rounded-[16px] bg-white border border-[#e2e8f0] shadow-[0_1px_24px_rgba(27,27,36,0.07)] overflow-hidden">
        <div class="grid lg:grid-cols-[1.08fr_0.92fr] items-stretch">
            <div class="p-6 lg:p-7 flex flex-col justify-center gap-3">
                <h1 class="font-[Literata,ui-serif,Georgia,serif] text-[28px] lg:text-[38px] font-semibold tracking-[-0.02em] leading-[0.98] text-[#1b1b24]">Cari, pinjam, baca.</h1>
                <p class="text-[14px] leading-relaxed text-zinc-600 max-w-[48ch]">Koleksi terkurasi, cover asli per ISBN, saringan cepat. Pinjam saat stok ada.</p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <flux:button variant="primary" href="#browse" class="!rounded-full !bg-[#3525cd] hover:!bg-[#2b1bb5]">Jelajahi katalog</flux:button>
                    <flux:button variant="ghost" href="#featured" class="!rounded-full">Pilihan editor</flux:button>
                </div>
            </div>
            <div class="hidden lg:block min-h-[260px] bg-[#f0ecf9] border-l border-[#e2e8f0] relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=1200&auto=format&fit=crop" alt="" class="w-full h-full object-cover transform transition duration-300 hover:scale-105" loading="lazy" decoding="async" sizes="(max-width:1024px) 100vw, 40vw">
            </div>
        </div>

        {{-- Search pill --}}
        <div id="browse" class="border-t border-[#e2e8f0] bg-[#fcf8ff]/80 p-3 lg:p-4">
            <div class="flex flex-wrap gap-2 items-center">
                <flux:field class="flex-1 min-w-[220px]">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        x-model="localSearch"
                        placeholder="Cari judul, penulis"
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
                <label class="inline-flex items-center gap-2 bg-white border border-[#e2e8f0] rounded-full px-3.5 py-2 text-sm cursor-pointer select-none hover:border-zinc-300 transition">
                    <input type="checkbox" wire:model.live="available" class="accent-[#4f46e5] rounded w-4 h-4"> Tersedia
                </label>
                <flux:select wire:model.live="sort" class="min-w-[150px] !rounded-full">
                    <flux:select.option value="">Terbaru</flux:select.option>
                    <flux:select.option value="title">Judul A to Z</flux:select.option>
                    <flux:select.option value="popular">Populer</flux:select.option>
                </flux:select>
                @if($search !== '' || $categoryId !== '' || $available || $sort !== '')
                    <flux:button variant="ghost" size="sm" wire:click="resetFilters" class="!rounded-full">Reset</flux:button>
                @endif
            </div>
        </div>
    </section>

    {{-- Featured 2 col bento --}}
    @if($featured->count())
    <section id="featured">
        <div class="flex items-baseline justify-between gap-4">
            <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[22px] lg:text-[26px] font-semibold tracking-[-0.02em] text-[#1b1b24]">Featured Books</h2>
            <span class="text-xs text-zinc-500">{{ $featured->count() }} pilihan kurator</span>
        </div>
        <div x-ref="featured" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($featured as $book)
                <flux:card data-stagger class="group !p-0 !rounded-[16px] overflow-hidden hover:shadow-[0_8px_20px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 transition duration-180 flex flex-col">
                    <div class="flex gap-0 flex-1">
                        <div class="w-[42%] shrink-0 bg-zinc-100 relative overflow-hidden">
                            <x-cover-image :coverWebpUrl="$book->cover_webp_url" :coverPath="$book->cover_path" :isbn="$book->isbn" :alt="$book->title" class="w-full h-full object-cover aspect-[3/4] transform transition duration-300 ease-out group-hover:scale-105" sizes="(max-width:768px) 100vw, 42vw" />
                        </div>
                        <div class="flex-1 min-w-0 p-4 lg:p-5 flex flex-col">
                            <span class="inline-flex items-center gap-1.5 w-fit text-[11px] font-medium rounded-full px-2.5 py-1 border {{ $book->stock > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-zinc-100 text-zinc-600 border-zinc-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $book->stock > 0 ? 'bg-emerald-600' : 'bg-zinc-400' }}"></span>{{ $book->stock > 0 ? 'Available' : 'Habis' }}
                            </span>
                            <a href="{{ route('catalog.show', $book) }}" wire:navigate class="mt-2.5 font-semibold leading-tight line-clamp-2 hover:text-[#4f46e5] transition text-[15px]">{{ $book->title }}</a>
                            <p class="text-sm text-zinc-600 line-clamp-1 mt-1">{{ $book->author }} @if($book->published_year)<span class="text-zinc-400">{{ $book->published_year }}</span>@endif</p>
                            <p class="text-[13px] leading-relaxed text-zinc-600 line-clamp-2 mt-2">{{ $book->description ?: 'Koleksi pilihan kurator LibraTech.' }}</p>
                            <div class="mt-auto pt-4">
                                @if($book->stock > 0)
                                    <flux:button variant="primary" size="sm" href="{{ route('catalog.show', $book) }}" wire:navigate class="!rounded-full !bg-[#4f46e5] hover:!bg-[#4338ca]">Place Hold</flux:button>
                                @else
                                    <flux:button variant="outline" size="sm" href="{{ route('catalog.show', $book) }}" wire:navigate class="!rounded-full">Join Waitlist</flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </section>
    @endif

    {{-- New Arrivals 4 col --}}
    @if($newArrivals->count())
    <section>
        <div class="flex items-baseline justify-between">
            <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[22px] lg:text-[26px] font-semibold tracking-[-0.02em] text-[#1b1b24]">New Arrivals</h2>
            <a href="#browse" class="text-sm text-[#4f46e5] hover:underline">View all</a>
        </div>
        <div x-ref="arrivals" class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($newArrivals as $book)
                <a data-stagger href="{{ route('catalog.show', $book) }}" wire:navigate class="group">
                    <flux:card class="!p-3 !rounded-[16px] h-full hover:border-zinc-300 hover:shadow-sm transition duration-180 flex flex-col">
                        <div class="aspect-[2/3] rounded-xl overflow-hidden bg-zinc-100 relative">
                            <x-cover-image :coverWebpUrl="$book->cover_webp_url" :coverPath="$book->cover_path" :isbn="$book->isbn" :alt="$book->title" class="w-full h-full object-cover transform transition duration-300 ease-out group-hover:scale-105" sizes="(max-width:768px) 50vw, 25vw" />
                            <span class="absolute top-2 right-2 w-6 h-6 grid place-items-center rounded-full text-[11px] border shadow-sm {{ $book->stock > 0 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-zinc-500 border-zinc-200' }}">{{ $book->stock > 0 ? '✓' : '·' }}</span>
                        </div>
                        <p class="mt-3 font-semibold text-[13px] leading-tight line-clamp-1 group-hover:text-[#4f46e5] transition">{{ $book->title }}</p>
                        <p class="text-xs text-zinc-600 line-clamp-1">{{ $book->author }}</p>
                        <p class="text-[11px] text-zinc-500 mt-1">{{ $book->category->name ?? '-' }}</p>
                    </flux:card>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Browse 12 --}}
    <section>
        <div class="flex items-baseline justify-between gap-4">
            <h2 class="font-[Literata,ui-serif,Georgia,serif] text-[18px] font-semibold tracking-[-0.02em] text-[#1b1b24]">Browse</h2>
            <span class="text-xs text-zinc-500">{{ $books->total() }} buku</span>
        </div>
        @if($books->count())
            <div x-ref="grid" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($books as $book)
                    <a data-stagger href="{{ route('catalog.show', $book) }}" wire:key="book-{{ $book->id }}" wire:navigate class="group">
                        <flux:card class="!p-4 !rounded-[16px] h-full flex flex-col gap-3 hover:border-zinc-300 hover:shadow-sm transition duration-180">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-[11px] tracking-wide uppercase text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-full px-2.5 py-1 line-clamp-1">{{ $book->category->name ?? '-' }}</span>
                                @if($book->stock > 0)
                                    <span class="shrink-0 text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2 py-1">Tersedia</span>
                                @else
                                    <span class="shrink-0 text-[11px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200 rounded-full px-2 py-1">Habis</span>
                                @endif
                            </div>
                            <div class="flex gap-3 flex-1">
                                <div class="w-16 h-20 rounded-xl overflow-hidden bg-zinc-100 shrink-0 border border-zinc-100">
                                    <x-cover-image :coverWebpUrl="$book->cover_webp_url" :coverPath="$book->cover_path" :isbn="$book->isbn" :alt="$book->title" class="w-full h-full object-cover transform transition duration-300 ease-out group-hover:scale-105" sizes="(max-width:768px) 100vw, 25vw" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[14px] leading-tight line-clamp-2 group-hover:text-[#4f46e5] transition">{{ $book->title }}</h3>
                                    <p class="text-sm text-zinc-600 mt-1 line-clamp-1">{{ $book->author }}</p>
                                    @if($book->published_year)<p class="text-xs text-zinc-500 mt-0.5">{{ $book->published_year }}</p>@endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-zinc-100 text-xs">
                                <span class="text-zinc-600">Stok <b class="text-zinc-900">{{ $book->stock }}</b></span>
                                <span class="inline-flex items-center gap-1 text-[#4f46e5] font-medium group-hover:gap-1.5 transition-all">
                                    Lihat detail
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            </div>
                        </flux:card>
                    </a>
                @endforeach
            </div>
            <div class="mt-6 flex justify-center">{{ $books->links() }}</div>
        @else
            <flux:card class="mt-4 !rounded-[16px] border-dashed text-center !p-10">
                <div class="w-10 h-10 mx-auto grid place-items-center rounded-full bg-zinc-50 border border-zinc-200 text-zinc-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 21l-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                </div>
                <p class="font-medium mt-3 text-[#1b1b24]">Tidak ada buku ditemukan</p>
                <p class="text-sm text-zinc-600 mt-1">Coba ubah kata kunci atau filter.</p>
                <flux:button variant="primary" size="sm" wire:click="resetFilters" class="mt-4 !rounded-full !bg-zinc-900 hover:!bg-black">Lihat semua buku</flux:button>
            </flux:card>
        @endif
    </section>
</div>
