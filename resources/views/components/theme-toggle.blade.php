@props(['class' => ''])

<button type="button" data-theme-toggle aria-label="Toggle theme"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-full border border-zinc-200 px-2 py-2 sm:px-3 text-sm font-semibold text-zinc-600 bg-transparent hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-300 dark:border-white/10 dark:text-zinc-300 dark:hover:bg-white/10 dark:focus:ring-white/20 transition-colors']) }}>
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05L5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z" />
    </svg>
    <span data-theme-label class="hidden sm:inline">Light</span>
</button>
