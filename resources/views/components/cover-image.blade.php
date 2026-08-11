{{-- ponytail: single source for cover rendering; prefers cover_webp_url (R2 400w webp Cache-Control 604800), fallback to storage/openlibrary. Blade reads DB, no hardcode. --}}
@php
    $webp = $coverWebpUrl ?? null;
    $local = $coverPath ?? null;
    $storageUrl = $local ? \Illuminate\Support\Facades\Storage::url($local) : null;
    $olUrl = $isbn ? 'https://covers.openlibrary.org/b/isbn/'.e($isbn).'-M.jpg?default=false' : null;
    $loading = $eager ? 'eager' : 'lazy';
    $fetchPriority = $eager ? 'high' : 'auto';
@endphp
@if($webp)
    <img
        src="{{ $webp }}"
        srcset="{{ $webp }} 1x, {{ $webp }} 2x"
        sizes="{{ $sizes }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchPriority }}"
        onerror="this.style.display='none';this.nextElementSibling?.style?.setProperty('display','grid')"
    ><div class="hidden w-full h-full place-items-center bg-[#1b1b24] text-[#fcf8ff] p-4 text-center leading-tight" style="display:none"><p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3">{{ $alt }}</p></div>
@elseif($storageUrl)
    <img
        src="{{ $storageUrl }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchPriority }}"
    >
@elseif($olUrl)
    <img
        src="{{ $olUrl }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchPriority }}"
        onerror="this.style.display='none';this.nextElementSibling?.style?.setProperty('display','grid')"
    ><div class="hidden w-full h-full place-items-center bg-[#1b1b24] text-[#fcf8ff] p-4 text-center leading-tight" style="display:none"><p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3">{{ $alt }}</p></div>
@else
    <div class="{{ $class }} grid place-items-center bg-[#1b1b24] text-[#fcf8ff] p-4 text-center leading-tight"><p class="font-[Literata,ui-serif,Georgia,serif] font-semibold text-sm leading-tight line-clamp-3">{{ $alt }}</p></div>
@endif
