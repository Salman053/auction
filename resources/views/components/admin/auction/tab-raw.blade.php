@props(['auction'])

<div x-show="tab === 'raw'" x-cloak class="mt-4 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
    <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Raw Scraped Data (JSON)</h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">The original data received from the Yahoo Japan scraper.</p>
    </div>
    <div class="p-4">
        @if ($auction->raw)
            <pre class="max-h-[500px] overflow-auto rounded-xl bg-zinc-950 p-4 text-xs leading-relaxed text-emerald-400 font-mono dark:bg-black">{{ json_encode($auction->raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <div class="flex flex-col items-center gap-2 py-10">
                <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No raw data stored for this auction.</p>
            </div>
        @endif
    </div>
</div>
