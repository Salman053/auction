@props(['auction'])

<div {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-3xl bg-white p-3 shadow-sm ring-1 ring-zinc-100 transition-all hover:shadow-2xl hover:ring-blue-500/20 dark:bg-zinc-900 dark:ring-white/5']) }}>
    <a href="{{ route('auctions.show', $auction) }}" class="flex-1">
        <div class="relative aspect-square overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-800">
            <img src="{{ $auction->thumbnail_url }}" alt="" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" />
            
            {{-- Status Badge --}}
            <div class="absolute left-3 top-3">
                <span @class([
                    'inline-flex items-center rounded-full px-2.5 py-1 text-[8px] font-black uppercase tracking-widest shadow-lg backdrop-blur-sm',
                    'bg-white/90 text-zinc-900 dark:bg-zinc-900/90 dark:text-white' => $auction->status === 'active',
                    'bg-zinc-900/90 text-white dark:bg-white/90 dark:text-zinc-900' => $auction->status !== 'active',
                ])>
                    {{ $auction->status === 'active' ? 'Live' : $auction->status }}
                </span>
            </div>

            {{-- Watchlist Button (Simplified for component) --}}
            <div class="absolute right-3 top-3">
                <button type="button" 
                        onclick="event.preventDefault(); window.dispatchEvent(new CustomEvent('toggle-watchlist', { detail: { id: {{ $auction->id }} } }))"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-zinc-400 shadow-lg backdrop-blur-sm transition hover:bg-white hover:text-rose-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </button>
            </div>
        </div>

        <div class="mt-4 px-2">
            <div class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.4)]"></span>
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                    @if ($auction->ends_at)
                        {{ $auction->ends_at->isPast() ? 'Ended' : 'Closing' }} {{ $auction->ends_at->diffForHumans() }}
                    @else
                        Live Now
                    @endif
                </p>
            </div>
            <h4 class="mt-1 line-clamp-1 text-sm font-bold text-zinc-900 transition-colors group-hover:text-blue-600 dark:text-zinc-100">
                {{ $auction->title }}
            </h4>

            <div class="mt-6 flex items-center justify-between border-t border-zinc-50 pt-4 dark:border-white/5">
                <div>
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Current Price</p>
                    <p class="text-xl font-black tracking-tighter text-blue-600 dark:text-blue-400">
                        <span class="text-xs">¥</span>{{ number_format($auction->current_bid_yen) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Activity</p>
                    <p class="text-[10px] font-black text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-white/5 rounded-md px-2 py-0.5">{{ $auction->bid_count ?? 0 }} BIDS</p>
                </div>
            </div>
        </div>
    </a>
</div>
