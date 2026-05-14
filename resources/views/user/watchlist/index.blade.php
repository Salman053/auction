<x-user-layout :title="'My Watchlist'">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">My Watchlist</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Auctions you're tracking — {{ $items->total() }} {{ Str::plural('item', $items->total()) }}
            </p>
        </div>
        <a href="{{ route('user.auctions.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-5 py-3 text-[10px] font-black uppercase tracking-widest text-zinc-600 shadow-sm transition hover:bg-zinc-50 hover:text-blue-600 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-white/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Browse Catalog
        </a>
    </div>

    @if ($items->isEmpty())
        {{-- Empty State --}}
        <div
            class="flex flex-col items-center justify-center rounded-lg bg-white py-20 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/5">
                <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="mt-6 text-lg font-bold text-zinc-900 dark:text-white">Your watchlist is empty</h3>
            <p class="mt-2 max-w-sm text-center text-sm text-zinc-500 dark:text-zinc-400">
                Browse the live market and tap the heart icon on any auction to start tracking it here.
            </p>
            <a href="{{ route('user.auctions.index') }}"
                class="mt-8 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:scale-[1.02] hover:bg-blue-700 active:scale-95">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Explore Live Market
            </a>
        </div>
    @else
        {{-- Watchlist Grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                @php
                    $auction = $item->auction;
                    $statusColor = match ($auction?->status) {
                        'active' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                        'ending_soon' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'finished' => 'bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-400',
                        'closed' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                        default => 'bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-400',
                    };
                    $dotColor = match ($auction?->status) {
                        'active' => 'bg-emerald-500',
                        'ending_soon' => 'bg-amber-500 animate-pulse',
                        'finished' => 'bg-zinc-400',
                        'closed' => 'bg-rose-500',
                        default => 'bg-zinc-400',
                    };
                @endphp

                @if ($auction)
                    <div
                        class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-white/10">

                        {{-- Auction Thumbnail --}}
                        <a href="{{ route('user.auctions.show', $auction) }}"
                            class="relative block aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $auction->thumbnail_url ?? 'https://placehold.co/400x300/1e293b/d4af37?text=WatchHub' }}"
                                alt="{{ $auction->title }}"
                                class="h-full w-full object-contain transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

                            {{-- Status badge --}}
                            <div class="absolute left-4 top-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[8px] font-black uppercase tracking-widest {{ $statusColor }}">
                                    <span class="h-1 w-1 rounded-full {{ $dotColor }}"></span>
                                    {{ ucfirst(str_replace('_', ' ', $auction->status)) }}
                                </span>
                            </div>

                            {{-- Ends badge --}}
                            @if ($auction->ends_at)
                                <div class="absolute bottom-4 left-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-black/50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-sm">
                                        {{ $auction->ends_at->isPast() ? 'Ended' : 'Ends' }}
                                        {{ $auction->ends_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endif
                        </a>

                        {{-- Card Body --}}
                        <div class="flex flex-1 flex-col p-6">
                            <h3
                                class="line-clamp-2 min-h-10 text-sm font-bold text-zinc-900 transition group-hover:text-blue-600 dark:text-white">
                                <a href="{{ route('user.auctions.show', $auction) }}"
                                    class="after:absolute after:inset-0">
                                    {{ $auction->title }}
                                </a>
                            </h3>

                            <div
                                class="mt-6 flex items-end justify-between border-t border-zinc-100 pt-4 dark:border-white/5">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Current
                                        Bid</p>
                                    <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">
                                        ¥{{ number_format($auction->current_bid_yen) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Bids</p>
                                    <p class="mt-1 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        {{ number_format($auction->bid_count) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Remove from watchlist --}}
                            <form method="POST" action="{{ route('user.watchlist.destroy', $auction) }}"
                                class="relative z-10 mt-4">
                                @csrf
                                @method('DELETE')
                                <button onclick="return stopEvent(event)" type="submit"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-rose-200 bg-rose-50 py-3 text-[10px] font-black uppercase tracking-widest text-rose-600 transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20">
                                    <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Untrack Item
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="mt-12">
                {{ $items->links() }}
            </div>
        @endif
    @endif



    <script>
        function stopEvent(e) {
            e.stopPropagation();
            return true;
        }
    </script>
</x-user-layout>
