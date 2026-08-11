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
    ><div class="hidden w-full h-full place-items-center bg-zinc-100 text-zinc-400 text-xs font-medium p-3 text-center leading-tight" style="display:none">{{ $alt }}</div>
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
    ><div class="hidden w-full h-full place-items-center bg-zinc-100 text-zinc-400 text-xs font-medium p-3 text-center leading-tight" style="display:none">{{ $alt }}</div>
@else
    <div class="{{ $class }} grid place-items-center bg-zinc-100 text-zinc-400 text-xs font-medium p-3 text-center leading-tight">{{ $alt }}</div>
@endif
