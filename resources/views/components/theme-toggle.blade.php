@props(['class' => ''])

<button
    type="button"
    data-theme-toggle
    aria-label="Toggle theme"
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-full border border-white/20 px-3 py-2 text-sm font-semibold text-white hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 " . ($attributes->has('class') && str_contains($attributes->get('class'), 'bg-') ? '' : 'bg-white/10')]) }}
>
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05L5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z" />
    </svg>
    <span data-theme-label>Light</span>
</button>
