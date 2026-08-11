@if ($paginator->hasPages())
    {{-- Stitch palette: primary #3525cd / container #4f46e5 / surface #fcf8ff / outline_variant #c7c4d8 / on_surface #1b1b24 --}}
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-3 text-sm">
        <p class="text-sm text-[#464555] order-2 sm:order-1">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-[#1b1b24]">{{ $paginator->firstItem() }}</span> - <span class="font-semibold text-[#1b1b24]">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            dari <span class="font-semibold text-[#1b1b24]">{{ $paginator->total() }}</span> buku
        </p>

        <div class="order-1 sm:order-2 flex items-center gap-1.5">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#c7c4d8] bg-white text-[#777587] cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#c7c4d8] bg-white text-[#1b1b24] hover:border-[#3525cd] hover:text-[#3525cd] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#3525cd]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-[#464555]">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#3525cd] text-white font-semibold shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#c7c4d8] bg-white text-[#1b1b24] hover:border-[#3525cd] hover:text-[#3525cd] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#3525cd]">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#c7c4d8] bg-white text-[#1b1b24] hover:border-[#3525cd] hover:text-[#3525cd] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#3525cd]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#c7c4d8] bg-white text-[#777587] cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
