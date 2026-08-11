{{-- ponytail: single source for cover rendering; prefers cover_webp_url (R2 400w webp Cache-Control 604800), fallback to storage/openlibrary. Blade reads DB, no hardcode. --}}
@php
    $webp = $coverWebpUrl ?? null;
    $local = $coverPath ?? null;
    $storageUrl = $local ? \Illuminate\Support\Facades\Storage::url($local) : null;
    $olUrl = $isbn ? 'https://covers.openlibrary.org/b/isbn/'.e($isbn).'-M.jpg?default=false' : null;
    $coverId = $coverId ?? null;
    $idUrl = $coverId ? 'https://covers.openlibrary.org/b/id/'.(int) $coverId.'-M.jpg' : null;
    $loading = $eager ? 'eager' : 'lazy';
    $fetchPriority = $eager ? 'high' : 'auto';
@endphp
@if($webp)
    <div class="relative w-full h-full min-h-[112px] bg-[#1b1b24] overflow-hidden grid place-items-center p-4 text-center" style="min-height:112px">
        <p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3 text-[#fcf8ff] px-2 relative z-0">{{ $alt }}</p>
        <img src="{{ $webp }}" srcset="{{ $webp }} 1x, {{ $webp }} 2x" sizes="{{ $sizes }}" alt="{{ $alt }}" class="{{ $class }} absolute inset-0 w-full h-full object-cover z-10" loading="{{ $loading }}" decoding="async" fetchpriority="{{ $fetchPriority }}" onerror="this.style.display='none'">
    </div>
@elseif($storageUrl)
    <img src="{{ $storageUrl }}" alt="{{ $alt }}" class="{{ $class }}" loading="{{ $loading }}" decoding="async" fetchpriority="{{ $fetchPriority }}">
@elseif($olUrl)
    <div class="relative w-full h-full min-h-[112px] bg-[#1b1b24] overflow-hidden grid place-items-center p-4 text-center" style="min-height:112px">
        <p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3 text-[#fcf8ff] px-2 relative z-0">{{ $alt }}</p>
        <img src="{{ $olUrl }}" alt="{{ $alt }}" class="{{ $class }} absolute inset-0 w-full h-full object-cover z-10 bg-white" loading="{{ $loading }}" decoding="async" fetchpriority="{{ $fetchPriority }}" onerror="if(this.dataset.triedId!=='1' && '{{ $idUrl }}'){this.dataset.triedId='1';this.src='{{ $idUrl }}';}else{this.style.display='none'}">
    </div>
@else
    @if($idUrl)
        <div class="relative w-full h-full min-h-[112px] bg-[#1b1b24] overflow-hidden grid place-items-center p-4 text-center" style="min-height:112px">
            <p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3 text-[#fcf8ff] px-2 relative z-0">{{ $alt }}</p>
            <img src="{{ $idUrl }}" alt="{{ $alt }}" class="{{ $class }} absolute inset-0 w-full h-full object-cover z-10 bg-white" loading="{{ $loading }}" decoding="async" fetchpriority="{{ $fetchPriority }}" onerror="this.style.display='none'">
        </div>
    @else
        <div class="w-full h-full min-h-[112px] grid place-items-center bg-[#1b1b24] text-[#fcf8ff] p-4 text-center leading-tight" style="min-height:112px"><p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3">{{ $alt }}</p></div>
    @endif
@endif
