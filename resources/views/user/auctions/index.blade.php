<x-user-layout :title="'Live Auctions'">
    <div class="mb-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Live Market</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Direct integration with Yahoo Japan Auctions.
                Verified luxury horology.</p>
        </div>

        <x-auction-filters :filters="$filters" :route="route('user.auctions.index')" />
    </div>

    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($auctions as $auction)
            @php($isWatchlisted = in_array($auction->id, $watchlistedAuctionIds, true))

            <div
                class="group relative flex flex-col overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-white/10">
                <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $auction->thumbnail_url ?? 'https://placehold.co/400x300/1e293b/d4af37?text=WatchHub' }}"
                        alt="{{ $auction->title }}"
                        class="h-full w-full object-contain transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    <div class="absolute right-4 top-4">
                        <form method="POST"
                            action="{{ $isWatchlisted ? route('user.watchlist.destroy', $auction) : route('user.watchlist.store', $auction) }}">
                            @csrf
                            @if ($isWatchlisted)
                                @method('DELETE')
                            @endif
                            <button type="submit"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 shadow-lg backdrop-blur-md transition hover:scale-110 dark:bg-zinc-800/90">
                                <svg class="h-5 w-5 {{ $isWatchlisted ? 'fill-brand-gold text-brand-gold' : 'text-zinc-600 dark:text-zinc-300' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex h-2 w-2 rounded-full bg-brand-gold"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                            Ends {{ $auction->ends_at?->diffForHumans(null, true) ?? '—' }}
                        </span>
                    </div>

                    <h3
                        class="line-clamp-2 min-h-[2.5rem] text-sm font-bold text-zinc-900 group-hover:text-brand-gold transition dark:text-white">
                        <a href="{{ route('user.auctions.show', $auction) }}">
                            <span class="absolute inset-0"></span>
                            {{ $auction->title }}
                        </a>
                    </h3>

                    <div class="mt-6 flex items-end justify-between border-t border-zinc-100 pt-4 dark:border-white/5">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Current Bid</p>
                            <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-brand-gold-light">
                                ¥{{ number_format($auction->current_bid_yen) }}</p>
                            @if ($auction->shipping_fee_yen === 0)
                                {{-- <p class="text-[9px] font-bold text-green-500 uppercase tracking-tighter">+ Free Shipping</p>/ --}}
                            @elseif($auction->shipping_fee_yen > 0)
                                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-tighter">+
                                    ¥{{ number_format($auction->shipping_fee_yen) }} Shipping</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Intensity</p>
                            <p class="mt-1 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                {{ number_format($auction->bid_count) }} bids</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-full flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-sm dark:bg-zinc-900">
                <div class="h-16 w-16 text-zinc-300 dark:text-zinc-700">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">No pieces found</h3>
                <p class="mt-2 text-sm text-zinc-500">Try adjusting your search criteria or contact our concierge.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $auctions->links() }}
    </div>
</x-user-layout>
