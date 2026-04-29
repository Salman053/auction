<x-guest-layout :title="$auction->title">
    <div class="relative overflow-hidden rounded-3xl m-5 bg-brand-navy-light text-white shadow-2xl lg:p-12 mb-8">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-brand-gold/10 blur-3xl"></div>
        <div class="absolute left-0 bottom-0 -ml-16 -mb-16 h-64 w-64 rounded-full bg-brand-gold/5 blur-3xl"></div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            {{-- Image Gallery --}}
            <div class="space-y-4">
                <div class="group relative aspect-video overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10">
                    <img id="mainImage" src="{{ $auction->thumbnail_url }}" alt="{{ $auction->title }}"
                        class="h-full w-full object-contain transition duration-500 group-hover:scale-105">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-brand-navy/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <span
                            class="rounded-lg bg-brand-gold/20 px-3 py-1 text-xs font-bold uppercase tracking-widest text-brand-gold-light backdrop-blur-md">
                            {{ $auction->status }}
                        </span>
                    </div>
                </div>

                @if (!empty($auction->image_urls))
                    <div title="Scroll to see more"
                        class="flex gap-4 overflow-x-auto hover:cursor-all-scroll pb-2 no-scrollbar">
                        @foreach ($auction->image_urls as $index => $url)
                            <button onclick="document.getElementById('mainImage').src='{{ $url }}'"
                                class="relative h-20 w-20 flex-shrink-0 overflow-hidden  rounded-lg bg-white/5 ring-1 ring-white/10 transition-all duration-200 hover:ring-2 hover:ring-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-gold group">
                                <img src="{{ $url }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                                <div
                                    class="absolute inset-0 bg-brand-gold/0 group-hover:bg-brand-gold/20 transition-colors duration-200">
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info & Action --}}
            <div class="flex flex-col justify-center">
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="inline-flex items-center rounded-full bg-brand-gold/20 px-3 py-1 text-xs font-medium text-brand-gold-light backdrop-blur-sm">
                            {{ $auction->status }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-white lg:text-4xl">{{ $auction->title }}</h1>
                    <p class="mt-2 font-mono text-sm text-brand-gold-light/60">ID: {{ $auction->yahoo_auction_id }}</p>
                </div>

                <div class="grid grid-cols-2 gap-8 py-8 border-y border-white/10">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-brand-gold-light/50">Current Bid</p>
                        <p
                            class="mt-2 text-3xl font-bold bg-gradient-to-r from-brand-gold to-brand-gold-light bg-clip-text text-transparent">
                            ¥{{ number_format($auction->current_bid_yen) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-brand-gold-light/50">Time Remaining
                        </p>
                        <p class="mt-2 text-xl font-medium text-white" id="timeRemaining">
                            {{ $auction->ends_at?->diffForHumans() ?? 'Ended' }}</p>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    @auth('user')
                        <a href="{{ route('user.auctions.show', $auction) }}"
                            class="group relative flex w-full items-center justify-center overflow-hidden rounded-xl bg-brand-gold px-8 py-4 text-center text-sm font-bold uppercase tracking-widest text-brand-navy transition-all duration-300 hover:scale-[1.02] hover:shadow-lg hover:shadow-brand-gold/25 active:scale-[0.98]">
                            <span class="relative z-10">Go to Bidding Platform</span>
                            <div
                                class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-300">
                            </div>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="group relative flex w-full items-center justify-center overflow-hidden rounded-xl bg-brand-gold px-8 py-4 text-center text-sm font-bold uppercase tracking-widest text-brand-navy transition-all duration-300 hover:scale-[1.02] hover:shadow-lg hover:shadow-brand-gold/25 active:scale-[0.98]">
                            <span class="relative z-10">Sign in to Bid</span>
                            <div
                                class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-300">
                            </div>
                        </a>
                    @endauth
                    <p
                        class="text-center text-xs text-brand-gold-light/40 italic flex items-center justify-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Global shipping and customs handling included in our service.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 m-5 mb-12">
        {{-- Technical Specifications --}}
        <div class="lg:col-span-2 space-y-8">
            <div
                class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <span class="h-1 w-6 bg-brand-gold rounded-full"></span>
                    Item Specifications
                </h3>
                <dl class="mt-8 grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                    <div class="flex flex-col p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <dt class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Seller
                            Identity</dt>
                        <dd class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $auction->seller_name ?? 'Confidential' }}</dd>
                    </div>
                    <div class="flex flex-col p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <dt class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Seller
                            Rating</dt>
                        <dd class="mt-2 text-sm font-medium text-zinc-900 dark:text-white flex items-center gap-1">
                            <span class="text-brand-gold">★</span> {{ $auction->seller_rating ?? '—' }} / 5.0
                        </dd>
                    </div>
                    <div class="flex flex-col p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <dt class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                            Condition</dt>
                        <dd class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $auction->condition ?? 'Pre-owned / Professional Checked' }}</dd>
                    </div>
                    <div class="flex flex-col p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <dt class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Total
                            Bidders</dt>
                        <dd class="mt-2 text-sm font-medium text-brand-gold-dark dark:text-brand-gold">
                            {{ $auction->bids_count ?? $auction->bids->count() }} active biddings</dd>
                    </div>
                </dl>
            </div>

            <div
                class="rounded-3xl bg-gradient-to-br from-brand-gold/5 to-brand-gold/10 p-8 border border-brand-gold/20 hover:border-brand-gold/40 transition-all duration-300">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-brand-gold/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-gold uppercase tracking-widest mb-2">The WatchHub
                            Guarantee</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            All auctions on our platform are sourced directly from verified Yahoo Japan Auction
                            partners. Our Japanese logistics hub performs a secondary authentication check before
                            dispatching to your global address. Your funds are secured in escrow until the item passes
                            inspection.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bidding History Sidebar --}}
        <div class="space-y-8">
            <div
                class="rounded-3xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="bg-gradient-to-r from-brand-navy to-brand-navy/90 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-widest">Live Bidding Activity</h3>
                        <div class="flex items-center gap-1">
                            <div class="h-1.5 w-1.5 rounded-full bg-brand-gold animate-pulse"></div>
                            <span class="text-xs text-brand-gold-light/60">LIVE</span>
                        </div>
                    </div>
                </div>
                <div class="p-2 max-h-96 overflow-y-auto">
                    @forelse($auction->bids()->latest()->take(10)->get() as $bid)
                        <div
                            class="flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:bg-zinc-50 dark:hover:bg-white/5 group">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-2 w-2 rounded-full bg-brand-gold animate-pulse group-hover:scale-125 transition-transform">
                                </div>
                                <span
                                    class="text-xs font-medium text-zinc-500 group-hover:text-brand-gold transition-colors">{{ $bid->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-zinc-400">¥</span>
                                <span
                                    class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-brand-gold transition-colors">{{ number_format($bid->amount_yen) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div
                                class="w-12 h-12 mx-auto mb-3 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-sm text-zinc-500 italic">No activity recorded yet for this piece.</p>
                            <p class="text-xs text-zinc-400 mt-1">Be the first to place a bid!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Stats Card --}}
            <div
                class="rounded-3xl bg-gradient-to-br from-brand-gold/10 to-transparent p-6 border border-brand-gold/20">
                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-gold mb-4">Quick Statistics</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Start Price</span>
                        <span
                            class="text-sm font-semibold text-zinc-900 dark:text-white">¥{{ number_format($auction->start_price_yen ?? $auction->current_bid_yen) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Bids</span>
                        <span
                            class="text-sm font-semibold text-brand-gold">{{ $auction->bids_count ?? $auction->bids->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Views</span>
                        <span
                            class="text-sm font-semibold text-zinc-900 dark:text-white">{{ number_format($auction->views_count ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Optional: Add real-time countdown timer
            @if ($auction->ends_at && $auction->ends_at->isFuture())
                function updateTimeRemaining() {
                    const endTime = new Date('{{ $auction->ends_at->toIso8601String() }}').getTime();
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        document.getElementById('timeRemaining').innerHTML = 'Auction Ended';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    let timeString = '';
                    if (days > 0) timeString += `${days}d `;
                    if (hours > 0 || days > 0) timeString += `${hours}h `;
                    if (minutes > 0 || hours > 0 || days > 0) timeString += `${minutes}m `;
                    timeString += `${seconds}s`;

                    document.getElementById('timeRemaining').innerHTML = timeString;
                }

                updateTimeRemaining();
                setInterval(updateTimeRemaining, 1000);
            @endif
        </script>
    @endpush
</x-guest-layout>
