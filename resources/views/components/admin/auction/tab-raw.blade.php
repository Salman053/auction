@props(['auction'])

<div x-show="tab === 'raw'" class="mt-4 space-y-4">
    <div class="rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Structured Scraper Data</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Parsed attributes directly from the source.</p>
        </div>
        <div class="p-6">
            @if ($auction->raw && is_array($auction->raw))
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($auction->raw as $key => $value)
                        <div class="space-y-1">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ str_replace('_', ' ', $key) }}</span>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white break-words">
                                @if (is_array($value))
                                    <pre
                                        class="mt-1 max-h-[200px] overflow-auto rounded-lg bg-zinc-50 p-2 text-[10px] font-mono text-zinc-600 dark:bg-black/20 dark:text-zinc-400">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif (is_bool($value))
                                    <span
                                        class="rounded-full px-2 py-0.5 text-[10px] {{ $value ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $value ? 'TRUE' : 'FALSE' }}
                                    </span>
                                @else
                                    {{ $value ?: '—' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center gap-2 py-10">
                    <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No raw data stored for this auction.
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if ($auction->raw)
        <div class="rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Full Source JSON</h3>
            </div>
            <div class="p-4">
                <pre
                    class="max-h-[500px] overflow-auto rounded-lg bg-zinc-950 p-4 text-[10px] leading-relaxed text-emerald-400 font-mono dark:bg-black">{{ json_encode($auction->raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif
</div>
