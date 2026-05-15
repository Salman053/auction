@props(['auction'])

<div x-show="tab === 'images'"
    class="mt-4 rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
    @if (!empty($auction->image_urls) && count($auction->image_urls) > 0)
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($auction->image_urls as $idx => $imageUrl)
                <a href="{{ $imageUrl }}" target="_blank" rel="noopener"
                    class="group relative overflow-hidden rounded-lg ring-1 ring-black/10 transition hover:ring-rose-500 dark:ring-white/10 dark:hover:ring-rose-500">
                    <img src="{{ $imageUrl }}" alt="Image {{ $idx + 1 }}" loading="lazy"
                        class="aspect-square w-full object-cover transition group-hover:scale-105">
                    <div
                        class="absolute inset-0 flex items-end justify-center bg-gradient-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100">
                        <span
                            class="mb-3 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                            Open full size ↗
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center gap-2 py-10">
            <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No images available for this auction.</p>
        </div>
    @endif
</div>
