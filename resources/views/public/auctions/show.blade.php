<x-guest-layout :title="$auction->title">
    <div class="mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Hero Section --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-brand-navy-light text-white shadow-2xl p-6 sm:p-10 lg:p-16 mb-12">
            <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-brand-gold/10 blur-3xl"></div>
            <div class="absolute left-0 bottom-0 -ml-16 -mb-16 h-64 w-64 rounded-full bg-brand-gold/5 blur-3xl"></div>

            <div class="relative z-10 grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">
                {{-- Image Gallery --}}
                <div class="space-y-6">
                    <div
                        class="group relative aspect-[4/3] sm:aspect-video overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10">
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
                            class="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
                            @foreach ($auction->image_urls as $index => $url)
                                <button onclick="document.getElementById('mainImage').src='{{ $url }}'"
                                    class="relative h-16 w-16 sm:h-20 sm:w-20 flex-shrink-0 overflow-hidden rounded-xl bg-white/5 ring-1 ring-white/10 transition-all duration-200 hover:ring-2 hover:ring-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-gold group">
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
                <div class="flex flex-col">
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span
                                class="inline-flex items-center rounded-full bg-brand-gold/20 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-brand-gold-light backdrop-blur-sm">
                                {{ $auction->status }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl leading-tight">
                            {{ $auction->title }}</h1>
                        <p class="mt-3 font-mono text-xs text-brand-gold-light/60 tracking-wider">REF:
                            {{ $auction->yahoo_auction_id }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-8 py-8 border-y border-white/10">
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-gold-light/40">
                                Current Bid</p>
                            <p
                                class="mt-2 text-3xl sm:text-4xl font-black bg-gradient-to-r from-brand-gold to-brand-gold-light bg-clip-text text-transparent">
                                ¥{{ number_format($auction->current_bid_yen) }}</p>
                        </div>
                        <div class="flex-1 sm:border-l sm:border-white/10 sm:pl-8">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-gold-light/40">Ends
                                In</p>
                            <p class="mt-2 text-lg sm:text-xl font-bold text-white" id="timeRemaining">
                                {{ $auction->ends_at?->diffForHumans() ?? 'Ended' }}</p>
                        </div>
                    </div>

                    <div class="mt-10 space-y-4">
                        @auth('user')
                            <a href="{{ route('user.auctions.show', $auction) }}"
                                class="group relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-brand-gold px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-brand-navy transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-brand-gold/20 active:scale-[0.98]">
                                <span class="relative z-10">Enter Bidding Console</span>
                                <div
                                    class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-300">
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="group relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-brand-gold px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-brand-navy transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-brand-gold/20 active:scale-[0.98]">
                                <span class="relative z-10">Sign in to Participate</span>
                                <div
                                    class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-300">
                                </div>
                            </a>
                        @endauth
                        <div class="flex items-center justify-center gap-2 pt-2">
                            <svg class="w-4 h-4 text-brand-gold/60" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-brand-gold-light/40 italic">
                                Worldwide secure delivery & insurance
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            {{-- Technical Specifications --}}
            <div class="lg:col-span-2 space-y-10">
                <div
                    class="rounded-3xl bg-white p-6 sm:p-10 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                    <h3
                        class="text-sm font-black uppercase tracking-[0.2em] text-zinc-900 dark:text-white flex items-center gap-3">
                        <span class="h-1 w-8 bg-brand-gold rounded-full"></span>
                        Technical Provenance
                    </h3>
                    <dl class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div
                            class="group flex flex-col p-5 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-transparent hover:border-brand-gold/20 transition-colors">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Seller Origin
                            </dt>
                            <dd class="mt-3 text-sm font-bold text-zinc-900 dark:text-white">
                                {{ $auction->seller_name ?? 'Japan Authentic' }}</dd>
                        </div>
                        <div
                            class="group flex flex-col p-5 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-transparent hover:border-brand-gold/20 transition-colors">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Merchant
                                Reputation</dt>
                            <dd class="mt-3 text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                <span class="text-brand-gold">★</span> {{ $auction->seller_rating ?? '4.9' }}
                            </dd>
                        </div>
                        <div
                            class="group flex flex-col p-5 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-transparent hover:border-brand-gold/20 transition-colors">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Object Condition
                            </dt>
                            <dd class="mt-3 text-sm font-bold text-zinc-900 dark:text-white leading-relaxed">
                                {{ $auction->condition ?? 'Authenticated / Pre-owned Luxury' }}</dd>
                        </div>
                        <div
                            class="group flex flex-col p-5 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-transparent hover:border-brand-gold/20 transition-colors">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Market Intensity
                            </dt>
                            <dd class="mt-3 text-sm font-bold text-brand-gold-dark dark:text-brand-gold">
                                {{ $auction->bids_count ?? $auction->bids->count() }} active competitive bids</dd>
                        </div>
                    </dl>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-brand-gold/5 via-transparent to-brand-gold/5 p-8 sm:p-10 border border-brand-gold/20">
                    <div class="flex flex-col sm:flex-row items-start gap-6">
                        <div class="flex-shrink-0">
                            <div
                                class="w-14 h-14 rounded-2xl bg-brand-gold/10 flex items-center justify-center border border-brand-gold/20">
                                <svg class="w-7 h-7 text-brand-gold" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-brand-gold uppercase tracking-[0.2em] mb-3">WatchHub
                                Security Escrow</h3>
                            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                                All acquisitions are protected by our Japanese logistics network. We verify the
                                authenticity
                                at our Tokyo hub before international dispatch. Funds remain in secure escrow until
                                technical verification is finalized.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-10">
                <div
                    class="rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
                    <div class="bg-brand-navy p-6 text-white">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-brand-gold-light">Activity
                                Log</h3>
                            <div class="flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 rounded-full bg-brand-gold animate-pulse"></div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-brand-gold/60">LIVE</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 max-h-[400px] overflow-y-auto no-scrollbar">
                        @forelse($auction->bids()->latest()->take(10)->get() as $bid)
                            <div
                                class="flex items-center justify-between p-4 rounded-xl transition-all hover:bg-zinc-50 dark:hover:bg-white/5 border-b border-zinc-50 last:border-0 dark:border-white/5">
                                <div class="flex items-center gap-3">
                                    <div class="h-1.5 w-1.5 rounded-full bg-brand-gold"></div>
                                    <span
                                        class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $bid->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                                    ¥{{ number_format($bid->amount_yen) }}
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <p class="text-xs text-zinc-400 italic font-medium uppercase tracking-widest">No bids
                                    recorded</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Stats Card --}}
                <div
                    class="rounded-3xl bg-gradient-to-br from-brand-gold/5 to-transparent p-8 border border-brand-gold/10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-gold mb-6">Market Metrics
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-brand-gold/5 pb-4">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Initial
                                Offer</span>
                            <span
                                class="text-sm font-black text-zinc-900 dark:text-white">¥{{ number_format($auction->start_price_yen ?? $auction->starting_bid_yen) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-brand-gold/5 pb-4">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Total
                                Interest</span>
                            <span
                                class="text-sm font-black text-brand-gold">{{ $auction->bids_count ?? $auction->bids->count() }}
                                bids</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Engagements</span>
                            <span
                                class="text-sm font-black text-zinc-900 dark:text-white">{{ number_format($auction->view_count ?? 0) }}
                                views</span>
                        </div>
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
