<x-user-layout :title="'Placing Bid: ' . Str::limit($auction->title, 50)" :back-url="route('user.auctions.index')">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('biddingConsole', (config) => ({
                auctionId: config.auctionId,
                currentBid: config.currentBid,
                bidAmount: config.bidAmount,
                shippingFee: config.shippingFee,
                availableCapacity: config.availableCapacity,
                multiplier: config.multiplier,
                endsAt: config.endsAt,
                updatesUrl: config.updatesUrl,
                lastBidId: config.lastBidId,

                initPolling() {
                    setInterval(() => this.fetchUpdates(), 10000);
                },

                async fetchUpdates() {
                    try {
                        const response = await fetch(this.updatesUrl);
                        if (!response.ok) return;

                        const data = await response.json();

                        this.currentBid = data.current_bid_yen;
                        if (data.ends_at_human) {
                            this.endsAt = data.ends_at_human;
                        }

                        if (data.highest_active_bid_id != this.lastBidId) {
                            this.lastBidId = data.highest_active_bid_id;
                            const historyContainer = document.getElementById(
                                'bid-history-container');
                            if (historyContainer) {
                                historyContainer.innerHTML = data.bids_html;
                            }
                        }
                    } catch (error) {
                        console.error('Failed to fetch auction updates:', error);
                    }
                }
            }));
        });
    </script>

    <div x-data="biddingConsole({
        auctionId: {{ $auction->id }},
        currentBid: {{ $auction->current_bid_yen }},
        bidAmount: {{ (int) old('amount_yen', $auction->current_bid_yen + 500) }},
        shippingFee: {{ $userShippingRate?->fee_yen ?? 0 }},
        availableCapacity: {{ $availableCapacityYen }},
        multiplier: {{ $multiplierPercent }},
        endsAt: '{{ $auction->ends_at?->diffForHumans() ?? 'Ended' }}',
        updatesUrl: '{{ route('user.auctions.updates', $auction) }}',
        lastBidId: '{{ $auction->bids->first()?->id }}'
    })" x-init="initPolling()" class="mx-auto  px-4 py-8 lg:px-8">

        {{-- Main Auction Grid --}}
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 items-start">

            {{-- Left Column: Gallery & History --}}
            <div class="space-y-8 lg:col-span-7 xl:col-span-8">
                @php
                    $carouselImages =
                        $auction->image_urls ?: ($auction->thumbnail_url ? [$auction->thumbnail_url] : []);
                    if (is_string($carouselImages)) {
                        $carouselImages = json_decode($carouselImages, true) ?: [$carouselImages];
                    }
                @endphp

                {{-- Hero Gallery --}}
                <div
                    class="relative overflow-hidden rounded-[2.5rem] bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-white/5 shadow-2xl">
                    <div class="aspect-[4/3] group relative">
                        <div id="carousel-container" class="flex h-full transition-transform duration-700 ease-in-out">
                            @foreach ($carouselImages as $image)
                                <div class="w-full h-full shrink-0 flex items-center justify-center p-4">
                                    <img loading="lazy" src="{{ $image }}" alt="{{ $auction->title }}"
                                        class="max-h-full max-w-full object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-105">
                                </div>
                            @endforeach
                        </div>

                        {{-- Navigation Arrows --}}
                        @if (count($carouselImages) > 1)
                            <div
                                class="absolute inset-x-6 top-1/2 -translate-y-1/2 flex justify-between pointer-events-none">
                                <button id="prev-slide"
                                    class="pointer-events-auto flex h-14 w-14 items-center justify-center rounded-full bg-white/90 dark:bg-zinc-900/90 text-zinc-900 dark:text-white shadow-2xl backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all hover:scale-110 active:scale-95">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button id="next-slide"
                                    class="pointer-events-auto flex h-14 w-14 items-center justify-center rounded-full bg-white/90 dark:bg-zinc-900/90 text-zinc-900 dark:text-white shadow-2xl backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all hover:scale-110 active:scale-95">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Progress Indicators --}}
                            <div
                                class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2.5 px-4 py-2 bg-black/10 backdrop-blur-md rounded-full">
                                @foreach ($carouselImages as $index => $image)
                                    <button
                                        class="carousel-dot h-1.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-blue-600' : 'w-2 bg-white/40' }}"
                                        data-index="{{ $index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Image Thumbnails --}}
                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-4">
                    @foreach (array_slice($carouselImages, 0, 8) as $index => $image)
                        <button onclick="document.querySelectorAll('.carousel-dot')[{{ $index }}].click()"
                            class="aspect-square rounded-2xl overflow-hidden border-2 border-transparent hover:border-blue-600 transition p-1 bg-white dark:bg-zinc-900 shadow-sm ring-1 ring-zinc-200 dark:ring-white/5">
                            <img src="{{ $image }}" class="w-full h-full object-contain" />
                        </button>
                    @endforeach
                </div>

                {{-- Chronological Activity --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 overflow-hidden mt-8">
                    <div
                        class="px-8 py-6 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400">Bid History</h2>
                        <span
                            class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ count($auction->bids) }}
                            Events</span>
                    </div>
                    <div id="bid-history-container" class="max-h-[500px] overflow-y-auto no-scrollbar">
                        @include('user.auctions._bid_history', ['bids' => $auction->bids])
                    </div>
                </div>
            </div>

            {{-- Right Column: Actions & Details --}}
            <div class="space-y-8 lg:col-span-5 xl:col-span-4">

                {{-- Success Banner --}}
                @if ($auction->status === 'finished' && $auction->winner_user_id == auth('user')->id())
                    <div
                        class="rounded-[2.5rem] bg-blue-600 p-8 text-white shadow-2xl shadow-blue-600/20 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
                        <div class="relative z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">Victory</span>
                            <h2 class="mt-2 text-3xl font-black tracking-tight leading-tight">Congratulations!</h2>
                            <p class="mt-2 text-sm text-blue-100">You are the winning bidder for this item.</p>

                            @if ($auction->shipment_status === 'pending')
                                <form id="confirm-shipment-form" method="POST"
                                    action="{{ route('user.auctions.confirm-shipment', $auction) }}" class="mt-8">
                                    @csrf
                                    <button type="button" data-confirm data-confirm-title="Confirm Logistics"
                                        data-confirm-text="Confirm Now" data-confirm-type="success"
                                        data-confirm-on-confirm="#confirm-shipment-form"
                                        data-confirm-message="Ready to proceed with delivery? This will notify our logistics team."
                                        class="w-full rounded-full bg-white px-8 py-4 text-[11px] font-black uppercase tracking-widest text-blue-600 shadow-xl transition hover:scale-105 active:scale-95">
                                        Confirm Shipment
                                    </button>
                                </form>
                            @else
                                <div
                                    class="mt-8 flex items-center justify-between p-4 rounded-2xl bg-white/10 border border-white/20">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest text-blue-100">Logistics
                                        Status</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest bg-white px-3 py-1 rounded-full text-blue-600">
                                        {{ str_replace('_', ' ', $auction->shipment_status) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Header & Core Price Info --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between gap-4">
                        <span
                            class="px-4 py-1.5 rounded-full bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-600/20 ring-1 ring-blue-400/30">
                            {{ strtoupper($auction->status) }}
                        </span>
                        <form
                            action="{{ $isWatched ? route('user.watchlist.destroy', $auction) : route('user.watchlist.store', $auction) }}"
                            method="POST">
                            @csrf
                            @if ($isWatched)
                                @method('DELETE')
                            @endif
                            <button type="submit"
                                class="group flex h-12 w-12 items-center justify-center rounded-full border transition-all {{ $isWatched ? 'bg-rose-500 border-rose-500 text-white shadow-lg shadow-rose-500/20' : 'bg-white border-zinc-200 text-zinc-400 hover:text-rose-500 dark:bg-zinc-800 dark:border-zinc-700' }}">
                                <svg class="h-5 w-5 {{ $isWatched ? 'fill-current' : 'fill-none group-hover:fill-rose-500' }}"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <h1 class="text-4xl font-black tracking-tighter text-zinc-900 dark:text-white leading-[1.1]">
                        {{ $auction->title }}
                    </h1>

                    <div class="grid grid-cols-2 gap-8 py-8 border-y border-zinc-100 dark:border-white/5">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Current Bid</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-blue-600 tracking-tighter">¥</span>
                                <span class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter"
                                    x-text="currentBid.toLocaleString()">
                                    {{ number_format($auction->current_bid_yen) }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Time Remaining</p>
                            <p class="text-2xl font-black text-rose-500 tracking-tighter" x-text="endsAt">
                                {{ $auction->ends_at?->diffForHumans() ?? 'Ended' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bidding Console --}}
                @if ($canBid)
                    <div
                        class="rounded-[2.5rem] bg-zinc-950 p-8 text-white shadow-2xl border border-zinc-800 relative overflow-hidden group">
                        <div
                            class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-blue-600/10 blur-3xl transition-all group-hover:scale-150">
                        </div>

                        <div class="relative z-10 space-y-8">
                            <div class="flex items-center justify-between border-b border-white/5 pb-6">
                                <div>
                                    <h2 class="text-xl font-black tracking-tight text-white uppercase tracking-tighter">
                                        Place Proxy Bid</h2>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500 mt-1">
                                        Transaction Node: #{{ $auction->id }}</p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-block rounded-full bg-blue-600/20 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 ring-1 ring-blue-400/30">
                                        Live Pulse
                                    </span>
                                </div>
                            </div>

                            <form id="bid-form" method="POST"
                                action="{{ route('user.auctions.bids.store', $auction) }}" class="space-y-8">
                                @csrf

                                <!-- STEP 1: Amount Selection -->
                                <div class="space-y-4">
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">1.
                                        Set Maximum Bid</label>

                                    <!-- Main Stepper Input Field -->
                                    <div
                                        class="relative flex items-center bg-zinc-900 rounded-[2rem] border-2 border-zinc-800 focus-within:border-blue-600 transition-all overflow-hidden shadow-inner">
                                        <button type="button" id="btn-decrement"
                                            class="px-6 py-6 text-zinc-500 hover:bg-zinc-800 hover:text-white transition font-black text-2xl select-none border-r border-zinc-800/50">−</button>

                                        <div class="relative flex-1 flex items-center justify-center">
                                            <span
                                                class="absolute left-6 text-blue-600 font-black text-2xl select-none tracking-tighter">¥</span>
                                            <input id="amount_yen" name="amount_yen" type="number"
                                                min="{{ (int) $auction->current_bid_yen + 1 }}"
                                                value="{{ old('amount_yen', (int) $auction->current_bid_yen + 500) }}"
                                                class="w-full bg-transparent border-none pl-12 pr-6 py-6 text-center text-3xl font-black text-white focus:ring-0 tracking-tighter [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                                        </div>

                                        <button type="button" id="btn-increment"
                                            class="px-6 py-6 text-zinc-500 hover:bg-zinc-800 hover:text-white transition font-black text-2xl select-none border-l border-zinc-800/50">+</button>
                                    </div>

                                    <!-- Instant Fast-Increment Pill Buttons -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <button type="button" data-add="500"
                                            class="py-3 text-[10px] font-black uppercase tracking-widest bg-zinc-900 hover:bg-blue-600 border border-zinc-800 hover:border-blue-600 rounded-2xl text-zinc-400 hover:text-white transition-all active:scale-95">
                                            +¥500
                                        </button>
                                        <button type="button" data-add="1000"
                                            class="py-3 text-[10px] font-black uppercase tracking-widest bg-zinc-900 hover:bg-blue-600 border border-zinc-800 hover:border-blue-600 rounded-2xl text-zinc-400 hover:text-white transition-all active:scale-95">
                                            +¥1,000
                                        </button>
                                        <button type="button" data-add="5000"
                                            class="py-3 text-[10px] font-black uppercase tracking-widest bg-zinc-900 hover:bg-blue-600 border border-zinc-800 hover:border-blue-600 rounded-2xl text-zinc-400 hover:text-white transition-all active:scale-95">
                                            +¥5,000
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2: Destination -->
                                <div class="space-y-4">
                                    <label for="shipping_rate_id"
                                        class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">2.
                                        Logistics Node</label>
                                    <div class="relative group/select">
                                        <select id="shipping_rate_id" name="shipping_rate_id"
                                            class="w-full rounded-2xl border-2 border-zinc-800 bg-zinc-900 px-6 py-5 text-[11px] font-black uppercase tracking-widest text-white focus:ring-0 focus:border-blue-600 appearance-none cursor-pointer transition-all">
                                            @foreach ($shippingRates as $rate)
                                                <option value="{{ $rate->id }}" data-fee="{{ $rate->fee_yen }}"
                                                    class="bg-zinc-950"
                                                    {{ old('shipping_rate_id', $userShippingRate?->id) == $rate->id ? 'selected' : '' }}>
                                                    {{ $rate->name }} (Shipping:
                                                    +¥{{ number_format($rate->fee_yen) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-6 flex items-center text-zinc-500 group-hover/select:text-blue-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 3: Clear Financial Summary -->
                                <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Maximum
                                            Bid</span>
                                        <span class="text-sm font-black text-zinc-200 tracking-tight">¥<span
                                                id="summary_bid">0</span></span>
                                    </div>
                                    <div class="flex justify-between items-center pb-4 border-b border-white/5">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Logistics
                                            Fee</span>
                                        <span class="text-sm font-black text-zinc-200 tracking-tight">¥<span
                                                id="summary_shipping">0</span></span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-[0.2em] text-white">Estimated
                                            Total</span>
                                        <span class="text-2xl font-black text-blue-400 tracking-tighter">¥<span
                                                id="total_estimated_display">0</span></span>
                                    </div>
                                </div>

                                <!-- Live Wallet Status Safeguard -->
                                <div id="wallet-status"
                                    class="flex items-center gap-3 px-5 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    <!-- Dynamic state injected by JS -->
                                </div>

                                <!-- Action Button -->
                                <button type="button" id="submit-bid-btn" data-confirm
                                    data-confirm-title="Confirm Auction Entry" data-confirm-text="Place Bid"
                                    data-confirm-type="info" data-confirm-on-confirm="#bid-form"
                                    data-confirm-message="This action will commit ¥{amount} from your bidding capacity."
                                    data-confirm-amount-selector="#amount_yen"
                                    class="w-full rounded-full bg-blue-600 px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-blue-600/30 transition-all hover:bg-blue-500 hover:scale-[1.02] active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
                                    Commit Proxy Bid
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Wallet Summary Card --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 p-8 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-zinc-100 dark:bg-white/5 blur-2xl group-hover:scale-150 transition-transform">
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 mb-8">Market
                            Standing</h3>
                        <div class="grid grid-cols-2 gap-12">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Available
                                    Cash</p>
                                <p class="text-2xl font-black text-zinc-900 dark:text-white mt-2 tracking-tighter">
                                    ¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Leverage
                                    Factor</p>
                                <p class="text-2xl font-black text-blue-600 mt-2 tracking-tighter">
                                    x{{ $multiplierPercent / 100 }}</p>
                            </div>
                        </div>

                        <div class="pt-8 mt-8 border-t border-zinc-100 dark:border-white/5">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Current
                                    Bidding Power</p>
                                <span
                                    class="text-[11px] font-black text-blue-600">¥{{ number_format($availableCapacityYen) }}</span>
                            </div>
                            <div class="h-2.5 w-full bg-zinc-100 dark:bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.5)] transition-all duration-1000"
                                    style="width: {{ $capacityYen > 0 ? ($availableCapacityYen / $capacityYen) * 100 : 0 }}%">
                                </div>
                            </div>
                            <a href="{{ route('user.wallet.index') }}"
                                class="mt-8 flex w-full items-center justify-center rounded-2xl bg-zinc-50 dark:bg-white/5 py-4 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-blue-600 transition-all border border-transparent hover:border-blue-600/20">Manage
                                Funds</a>
                        </div>
                    </div>
                </div>

                {{-- Meta Info --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 p-8 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 space-y-8">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Item Specifications
                    </h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-baseline group/meta">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover/meta:text-blue-600 transition-colors">Auction
                                ID</span>
                            <span
                                class="text-xs font-mono font-black text-zinc-900 dark:text-white tracking-tighter">{{ $auction->yahoo_auction_id }}</span>
                        </div>
                        <div class="flex justify-between items-baseline group/meta">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover/meta:text-blue-600 transition-colors">Verified
                                Seller</span>
                            <span
                                class="text-xs font-black text-zinc-900 dark:text-white">{{ $auction->seller_name ?? 'Premium Merchant' }}</span>
                        </div>
                        <div class="flex justify-between items-baseline group/meta">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover/meta:text-blue-600 transition-colors">Authenticity</span>
                            <span
                                class="inline-flex items-center gap-1.5 text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full ring-1 ring-blue-600/20">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Verified
                            </span>
                        </div>
                    </div>
                    <a href="https://page.auctions.yahoo.co.jp/jp/auction/{{ $auction->yahoo_auction_id }}"
                        target="_blank"
                        class="group flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-zinc-50 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-zinc-900 transition-all border border-transparent hover:border-zinc-200 dark:hover:border-white/10">
                        Explore Original Listing
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Categories Section --}}
    <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">
                    Explore Categories</h2>
                <p class="text-sm text-zinc-500 mt-1">Find what you're looking for across 1,000+ niches</p>
            </div>
            <a href="{{ route('user.auctions.index') }}"
                class="group flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest">
                All Categories
                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-4 lg:grid-cols-8">
            @php
                $categories = [
                    [
                        'name' => 'Automotive',
                        'img' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=200&h=200&fit=crop',
                        'id' => '26318',
                    ],
                    [
                        'name' => 'Fashion',
                        'img' => 'https://images.unsplash.com/photo-1539109132382-381bb3f1cff6?w=200&h=200&fit=crop',
                        'id' => '23000',
                    ],
                    [
                        'name' => 'Watches',
                        'img' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=200&h=200&fit=crop',
                        'id' => '23140',
                    ],
                    [
                        'name' => 'Electronics',
                        'img' => 'https://images.unsplash.com/photo-1526733151923-85973c147ed5?w=200&h=200&fit=crop',
                        'id' => '23632',
                    ],
                    [
                        'name' => 'Antiques',
                        'img' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=200&h=200&fit=crop',
                        'id' => '20000',
                    ],
                    [
                        'name' => 'Toys',
                        'img' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=200&h=200&fit=crop',
                        'id' => '25464',
                    ],
                    [
                        'name' => 'Home',
                        'img' => 'https://images.unsplash.com/photo-1616489953149-80860734e62a?w=200&h=200&fit=crop',
                        'id' => '24198',
                    ],
                    [
                        'name' => 'Luxury',
                        'img' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=200&h=200&fit=crop',
                        'id' => '23000',
                    ],
                ];
            @endphp
            @foreach ($categories as $cat)
                <a href="{{ route('user.auctions.index', ['category' => $cat['id']]) }}"
                    class="flex flex-col items-center gap-4 group">
                    <div
                        class="h-24 w-24 overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-zinc-200 transition-all duration-300 group-hover:scale-105 group-hover:ring-blue-500 group-hover:shadow-xl dark:bg-zinc-800 dark:ring-white/5">
                        <img src="{{ $cat['img'] }}"
                            class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition-all duration-300" />
                    </div>
                    <span
                        class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] transition-colors group-hover:text-blue-600 dark:text-zinc-500">{{ $cat['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carousel Logic
            const container = document.getElementById('carousel-container');
            if (container) {
                const dots = document.querySelectorAll('.carousel-dot');
                const prev = document.getElementById('prev-slide');
                const next = document.getElementById('next-slide');
                let current = 0;
                const total = dots.length;

                function update() {
                    container.style.transform = `translateX(-${current * 100}%)`;
                    dots.forEach((dot, i) => {
                        if (i === current) {
                            dot.classList.remove('w-2', 'bg-white/40');
                            dot.classList.add('w-8', 'bg-blue-600');
                        } else {
                            dot.classList.add('w-2', 'bg-white/40');
                            dot.classList.remove('w-8', 'bg-blue-600');
                        }
                    });
                }

                if (prev) prev.onclick = () => {
                    current = (current - 1 + total) % total;
                    update();
                };
                if (next) next.onclick = () => {
                    current = (current + 1) % total;
                    update();
                };
                dots.forEach(dot => {
                    dot.onclick = () => {
                        current = parseInt(dot.dataset.index);
                        update();
                    };
                });
            }

            // Price Calculation Logic
            const amountInput = document.getElementById('amount_yen');
            const shippingSelect = document.getElementById('shipping_rate_id');
            const totalDisplay = document.getElementById('total_estimated_display');
            const summaryBid = document.getElementById('summary_bid');
            const summaryShipping = document.getElementById('summary_shipping');
            const walletStatus = document.getElementById('wallet-status');
            const submitBtn = document.getElementById('submit-bid-btn');

            // Values from backend variables
            const availableCapacity = parseInt("{{ $availableCapacityYen }}") || 0;
            const minBidRequired = parseInt("{{ (int) $auction->current_bid_yen + 1 }}") || 1;

            function calculateTotal() {
                let bidAmount = parseInt(amountInput.value) || 0;

                // Hard clamping to avoid illegal input submissions
                if (bidAmount < minBidRequired) {
                    bidAmount = minBidRequired;
                    amountInput.value = bidAmount;
                }

                const selectedOption = shippingSelect.options[shippingSelect.selectedIndex];
                const shippingFee = selectedOption ? parseInt(selectedOption.getAttribute('data-fee')) || 0 : 0;
                const total = bidAmount + shippingFee;

                // Update Text Fields
                summaryBid.textContent = bidAmount.toLocaleString();
                summaryShipping.textContent = shippingFee.toLocaleString();
                totalDisplay.textContent = total.toLocaleString();

                // Wallet Guard System Validation
                if (total > availableCapacity) {
                    walletStatus.className =
                        "flex items-center gap-3 px-5 py-4 rounded-2xl text-[9px] font-black uppercase tracking-widest bg-rose-500/10 text-rose-500 border border-rose-500/20";
                    walletStatus.innerHTML = `⚠️ Over Capacity (Limit: ¥${availableCapacity.toLocaleString()})`;
                    submitBtn.disabled = true;
                    submitBtn.innerText = "Insufficient Liquidity";
                } else {
                    walletStatus.className =
                        "flex items-center gap-3 px-5 py-4 rounded-2xl text-[9px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20";
                    walletStatus.innerHTML =
                        `✅ Verified (¥${availableCapacity.toLocaleString()} Avail.)`;
                    submitBtn.disabled = false;
                    submitBtn.innerText = "Commit Proxy Bid";
                }
            }

            // Input Listeners
            amountInput.addEventListener('change', calculateTotal);
            shippingSelect.addEventListener('change', calculateTotal);

            // Stepper Buttons Logic (- / +)
            document.getElementById('btn-decrement').addEventListener('click', () => {
                amountInput.value = Math.max(minBidRequired, (parseInt(amountInput.value) || 0) - 100);
                calculateTotal();
            });

            document.getElementById('btn-increment').addEventListener('click', () => {
                amountInput.value = (parseInt(amountInput.value) || 0) + 100;
                calculateTotal();
            });

            // Pill Quick Macro Additions
            document.querySelectorAll('[data-add]').forEach(button => {
                button.addEventListener('click', (e) => {
                    const addedValue = parseInt(e.currentTarget.getAttribute('data-add'));
                    amountInput.value = (parseInt(amountInput.value) || 0) + addedValue;
                    calculateTotal();
                });
            });

            // Run calculation once component mounts
            calculateTotal();
        });
    </script>

</x-user-layout>
