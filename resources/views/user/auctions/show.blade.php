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
                timeLeft: '',
                timeRemainingInterval: null,

                initPolling() {
                    // Update every 10 seconds for auction data
                    setInterval(() => this.fetchUpdates(), 10000);
                    // Update time remaining every second
                    this.updateTimeRemaining();
                    this.timeRemainingInterval = setInterval(() => this.updateTimeRemaining(), 1000);
                },

                updateTimeRemaining() {
                    if (!this.endsAt || this.endsAt === 'Ended') {
                        this.timeLeft = 'Auction Ended';
                        return;
                    }

                    // Parse the endsAt date if it's a string
                    let endDate;
                    if (typeof this.endsAt === 'string') {
                        endDate = new Date(this.endsAt);
                        if (isNaN(endDate.getTime())) {
                            this.timeLeft = this.endsAt;
                            return;
                        }
                    } else {
                        this.timeLeft = this.endsAt;
                        return;
                    }

                    const now = new Date();
                    const diff = endDate - now;

                    if (diff <= 0) {
                        this.timeLeft = 'Auction Ended';
                        return;
                    }

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    if (hours > 0) {
                        this.timeLeft = `${hours}h ${minutes}m ${seconds}s`;
                    } else if (minutes > 0) {
                        this.timeLeft = `${minutes}m ${seconds}s`;
                    } else {
                        this.timeLeft = `${seconds}s`;
                    }
                },

                async fetchUpdates() {
                    try {
                        const response = await fetch(this.updatesUrl);
                        if (!response.ok) return;

                        const data = await response.json();

                        if (data.current_bid_yen > this.currentBid) {
                            this.currentBid = data.current_bid_yen;
                            // Show notification for outbid
                            if (typeof showNotification === 'function') {
                                showNotification('You have been outbid!', 'warning');
                            }
                        } else {
                            this.currentBid = data.current_bid_yen;
                        }

                        if (data.ends_at) {
                            this.endsAt = data.ends_at;
                        }

                        if (data.highest_active_bid_id != this.lastBidId) {
                            this.lastBidId = data.highest_active_bid_id;
                            const historyContainer = document.getElementById(
                                'bid-history-container');
                            if (historyContainer) {
                                historyContainer.innerHTML = data.bids_html;
                                // Auto-scroll to show latest bid
                                const historyScroll = document.querySelector('.bid-history-scroll');
                                if (historyScroll) {
                                    historyScroll.scrollTop = 0;
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Failed to fetch auction updates:', error);
                    }
                },

                cleanup() {
                    if (this.timeRemainingInterval) {
                        clearInterval(this.timeRemainingInterval);
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
        endsAt: '{{ $auction->ends_at?->toISOString() ?? '' }}',
        updatesUrl: '{{ route('user.auctions.updates', $auction) }}',
        lastBidId: '{{ $auction->bids->first()?->id }}'
    })" x-init="initPolling()" x-on:unload.window="cleanup()"
        class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">

        <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">

            {{-- Success Banner --}}
            @if ($auction->status === 'finished' && $auction->winner_user_id == auth('user')->id())
                <div
                    class="mb-6 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white shadow-xl animate-slide-down">
                    <div class="flex items-center gap-4 flex-wrap sm:flex-nowrap">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold">Congratulations! 🎉</h3>
                            <p class="text-green-100">You are the winning bidder for this item.</p>
                        </div>
                        @if ($auction->shipment_status === 'pending')
                            <form id="confirm-shipment-form" method="POST"
                                action="{{ route('user.auctions.confirm-shipment', $auction) }}">
                                @csrf
                                <button type="button"
                                    onclick="if(confirm('Ready to proceed with delivery? This will notify our logistics team.')) document.getElementById('confirm-shipment-form').submit()"
                                    class="px-6 py-3 bg-white text-green-600 rounded-xl font-bold hover:shadow-lg transition transform hover:scale-105">
                                    Confirm Shipment →
                                </button>
                            </form>
                        @else
                            <div class="px-4 py-2 bg-white/20 rounded-lg">
                                <span class="text-sm font-semibold">Status:
                                    {{ str_replace('_', ' ', $auction->shipment_status) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Left Column: Gallery & History --}}
                <div class="lg:col-span-7 space-y-6">
                    @php
                        $carouselImages =
                            $auction->image_urls ?: ($auction->thumbnail_url ? [$auction->thumbnail_url] : []);
                        if (is_string($carouselImages)) {
                            $carouselImages = json_decode($carouselImages, true) ?: [$carouselImages];
                        }
                    @endphp

                    {{-- Main Image Gallery --}}
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden">
                        <div class="relative">
                            <div id="carousel-container" class="flex transition-transform duration-300 ease-out">
                                @foreach ($carouselImages as $image)
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="aspect-square flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-800">
                                            <img src="{{ $image }}" alt="{{ $auction->title }}"
                                                class="max-w-full max-h-full object-contain cursor-pointer hover:scale-105 transition-transform duration-300"
                                                onclick="window.open(this.src, '_blank')">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (count($carouselImages) > 1)
                                <button id="prev-slide"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 backdrop-blur-sm transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button id="next-slide"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 backdrop-blur-sm transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Thumbnails --}}
                        @if (count($carouselImages) > 1)
                            <div class="flex gap-2 p-4 overflow-x-auto border-t dark:border-gray-800">
                                @foreach ($carouselImages as $index => $image)
                                    <button onclick="goToSlide({{ $index }})"
                                        class="carousel-dot flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all hover:border-blue-500"
                                        data-index="{{ $index }}">
                                        <img src="{{ $image }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Bid History --}}
                    <div class="bg-slate-100 dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b dark:border-gray-800 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Bid History</h2>
                            <span class="text-sm text-gray-500">{{ count($auction->bids) }} bids</span>
                        </div>
                        <div id="bid-history-container" class="bid-history-scroll max-h-96 overflow-y-auto">
                            @include('user.auctions._bid_history', ['bids' => $auction->bids])
                        </div>
                    </div>
                </div>

                {{-- Right Column: Bidding Interface --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Auction Header --}}
                    <div class="bg-slate-100 dark:bg-gray-900 rounded-2xl shadow-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full">
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
                                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    <svg class="w-6 h-6 {{ $isWatched ? 'text-red-500 fill-current' : 'text-gray-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 line-clamp-2">
                            {{ $auction->title }}
                        </h1>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t dark:border-gray-800">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Current Bid</p>
                                <p class="text-3xl font-bold text-blue-600">
                                    ¥<span
                                        x-text="currentBid.toLocaleString()">{{ number_format($auction->current_bid_yen) }}</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Time Remaining</p>
                                <p class="text-2xl font-bold text-orange-500" x-text="timeLeft">
                                    Loading...
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Bidding Console --}}
                    @if ($canBid)
                        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-xl p-6 text-white">
                            <div class="mb-6">
                                <h2 class="text-xl font-bold mb-1">Place Your Bid</h2>
                                <p class="text-sm text-gray-400">Set your maximum bid amount</p>
                            </div>

                            <form id="bid-form" method="POST"
                                action="{{ route('user.auctions.bids.store', $auction) }}" class="space-y-6">
                                @csrf

                                {{-- Bid Amount Input --}}
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Your Maximum Bid (¥)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-gray-400">¥</span>
                                        <input type="number" name="amount_yen" id="amount_yen"
                                            min="{{ (int) $auction->current_bid_yen + 1 }}"
                                            value="{{ old('amount_yen', (int) $auction->current_bid_yen + 500) }}"
                                            step="100"
                                            class="w-full pl-10 pr-4 py-4 text-2xl font-bold text-center bg-gray-700 border-2 border-gray-600 rounded-xl focus:border-blue-500 focus:outline-none transition">
                                    </div>

                                    {{-- Quick Bid Suggestions --}}
                                    <div class="grid grid-cols-4 gap-2 mt-3">
                                        @php
                                            $currentBid = (int) $auction->current_bid_yen;
                                            $suggestions = [
                                                $currentBid + 500,
                                                $currentBid + 1000,
                                                $currentBid + 2000,
                                                $currentBid + 5000,
                                            ];
                                        @endphp
                                        @foreach ($suggestions as $suggestion)
                                            <button type="button"
                                                onclick="document.getElementById('amount_yen').value = {{ $suggestion }}; calculateTotal();"
                                                class="py-2 text-sm font-semibold bg-gray-700 hover:bg-blue-600 rounded-lg transition">
                                                +¥{{ number_format($suggestion - $currentBid) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Shipping Selection --}}
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Shipping Method</label>
                                    <select name="shipping_rate_id" id="shipping_rate_id"
                                        class="w-full px-4 py-3 bg-gray-700 border-2 border-gray-600 rounded-xl focus:border-blue-500 focus:outline-none transition">
                                        @foreach ($shippingRates as $rate)
                                            <option value="{{ $rate->id }}" data-fee="{{ $rate->fee_yen }}"
                                                {{ old('shipping_rate_id', $userShippingRate?->id) == $rate->id ? 'selected' : '' }}>
                                                {{ $rate->name }} - ¥{{ number_format($rate->fee_yen) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Cost Breakdown --}}
                                <div class="bg-gray-800 rounded-xl p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Maximum Bid</span>
                                        <span class="font-semibold">¥<span id="summary_bid">0</span></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Shipping Fee</span>
                                        <span class="font-semibold">¥<span id="summary_shipping">0</span></span>
                                    </div>
                                    <div class="border-t border-gray-700 pt-2 mt-2">
                                        <div class="flex justify-between text-lg">
                                            <span class="font-bold">Total</span>
                                            <span class="font-bold text-blue-400">¥<span
                                                    id="total_estimated_display">0</span></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Wallet Status --}}
                                <div id="wallet-status" class="text-center text-sm font-semibold p-3 rounded-lg">
                                    <!-- Dynamic content -->
                                </div>

                                {{-- Submit Button --}}
                                <button type="button" id="submit-bid-btn" onclick="confirmBid()"
                                    class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition transform hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Place Bid
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Your Capacity --}}
                    <div class="bg-salte-100 dark:bg-gray-900 rounded-2xl shadow-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-500 mb-4">Your Bidding Capacity</h3>
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Available</span>
                                <span
                                    class="font-bold text-blue-600">¥{{ number_format($availableCapacityYen) }}</span>
                            </div>
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full transition-all duration-500"
                                    style="width: {{ $capacityYen > 0 ? min(100, ($availableCapacityYen / $capacityYen) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center text-sm">
                            <div>
                                <p class="text-gray-500">Wallet Balance</p>
                                <p class="font-bold">¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Leverage</p>
                                <p class="font-bold text-blue-600">x{{ $multiplierPercent / 100 }}</p>
                            </div>
                        </div>
                        <a href="{{ route('user.wallet.index') }}"
                            class="mt-4 block text-center py-2 text-sm text-blue-600 hover:text-blue-700 font-semibold">
                            Manage Funds →
                        </a>
                    </div>

                    {{-- Item Details --}}
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-500">Item Details</h3>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Auction ID</span>
                            <span class="font-mono text-sm">{{ $auction->yahoo_auction_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Seller</span>
                            <span>{{ $auction->seller_name ?? 'Premium Merchant' }}</span>
                        </div>
                        <a href="https://page.auctions.yahoo.co.jp/jp/auction/{{ $auction->yahoo_auction_id }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 mt-2">
                            View on Yahoo Auctions
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Categories Section --}}
            @if (isset($categories) && count($categories) > 0)
                <div class="mt-12">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Explore Categories</h2>
                        <a href="{{ route('user.categories.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                            View All →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        @foreach ($categories->take(8) as $cat)
                            <a href="{{ route('user.auctions.index', ['category' => $cat->yahoo_category_id]) }}"
                                class="group text-center">
                                <div
                                    class="aspect-square rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 p-4 mb-2 group-hover:shadow-lg transition">
                                    <div class="w-full h-full flex items-center justify-center text-3xl">
                                        {{ substr($cat->name, 0, 2) }}
                                    </div>
                                </div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-blue-600 transition">
                                    {{ Str::limit($cat->name, 15) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }

        /* Hide number input spinners */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Custom scrollbar */
        .bid-history-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .bid-history-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .bid-history-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .bid-history-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Dark mode scrollbar */
        .dark .bid-history-scroll::-webkit-scrollbar-track {
            background: #2d2d2d;
        }

        .dark .bid-history-scroll::-webkit-scrollbar-thumb {
            background: #555;
        }

        .dark .bid-history-scroll::-webkit-scrollbar-thumb:hover {
            background: #777;
        }
    </style>

    <script>
        let currentSlide = 0;
        const totalSlides = {{ count($carouselImages) }};

        function goToSlide(index) {
            if (totalSlides <= 1) return;
            currentSlide = Math.max(0, Math.min(index, totalSlides - 1));
            const container = document.getElementById('carousel-container');
            if (container) {
                container.style.transform = `translateX(-${currentSlide * 100}%)`;
            }
            // Update active state of thumbnails
            document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
                if (i === currentSlide) {
                    dot.classList.add('border-blue-500');
                } else {
                    dot.classList.remove('border-blue-500');
                }
            });
        }

        function nextSlide() {
            goToSlide(currentSlide + 1);
        }

        function prevSlide() {
            goToSlide(currentSlide - 1);
        }

        // Carousel initialization
        document.addEventListener('DOMContentLoaded', function() {
            const prevBtn = document.getElementById('prev-slide');
            const nextBtn = document.getElementById('next-slide');

            if (prevBtn) prevBtn.onclick = prevSlide;
            if (nextBtn) nextBtn.onclick = nextSlide;

            // Initialize first slide
            goToSlide(0);

            // Initialize bidding calculator
            calculateTotal();
        });

        // Bidding calculator functions
        const availableCapacity = parseInt("{{ $availableCapacityYen }}") || 0;
        const minBidRequired = parseInt("{{ (int) $auction->current_bid_yen + 1 }}") || 1;

        function calculateTotal() {
            const amountInput = document.getElementById('amount_yen');
            const shippingSelect = document.getElementById('shipping_rate_id');
            const totalDisplay = document.getElementById('total_estimated_display');
            const summaryBid = document.getElementById('summary_bid');
            const summaryShipping = document.getElementById('summary_shipping');
            const walletStatus = document.getElementById('wallet-status');
            const submitBtn = document.getElementById('submit-bid-btn');

            if (!amountInput || !shippingSelect) return;

            let bidAmount = parseInt(amountInput.value) || 0;

            // Validate minimum bid
            if (bidAmount < minBidRequired) {
                bidAmount = minBidRequired;
                amountInput.value = bidAmount;
            }

            const selectedOption = shippingSelect.options[shippingSelect.selectedIndex];
            const shippingFee = selectedOption ? parseInt(selectedOption.getAttribute('data-fee')) || 0 : 0;
            const total = bidAmount + shippingFee;

            // Update displays
            if (summaryBid) summaryBid.textContent = bidAmount.toLocaleString();
            if (summaryShipping) summaryShipping.textContent = shippingFee.toLocaleString();
            if (totalDisplay) totalDisplay.textContent = total.toLocaleString();

            // Check capacity
            if (walletStatus && submitBtn) {
                if (total > availableCapacity) {
                    walletStatus.className =
                        "text-center text-sm font-semibold p-3 rounded-lg bg-red-500/20 text-red-600 dark:text-red-400";
                    walletStatus.innerHTML =
                        `⚠️ Insufficient capacity (Need: ¥${total.toLocaleString()}, Available: ¥${availableCapacity.toLocaleString()})`;
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Insufficient Funds";
                } else {
                    walletStatus.className =
                        "text-center text-sm font-semibold p-3 rounded-lg bg-green-500/20 text-green-600 dark:text-green-400";
                    walletStatus.innerHTML = `Ready to bid (Available: ¥${availableCapacity.toLocaleString()})`;
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Place Bid";
                }
            }
        }

        function confirmBid() {
            const amountInput = document.getElementById('amount_yen');
            const totalSpan = document.getElementById('total_estimated_display');

            if (!amountInput || !totalSpan) return;

            const bidAmount = parseInt(amountInput.value) || 0;
            const totalAmount = parseInt(totalSpan.textContent) || 0;

            if (confirm(
                    `Confirm bid of ¥${bidAmount.toLocaleString()} (Total with shipping: ¥${totalAmount.toLocaleString()})?\n\nThis is a binding proxy bid.`
                )) {
                document.getElementById('bid-form').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount_yen');
            const shippingSelect = document.getElementById('shipping_rate_id');

            if (amountInput) {
                amountInput.addEventListener('change', calculateTotal);
                amountInput.addEventListener('input', calculateTotal);
            }
            if (shippingSelect) {
                shippingSelect.addEventListener('change', calculateTotal);
            }
        });

        function showNotification(message, type = 'info') {
            if (type === 'warning') {
                console.warn(message);
                const historySection = document.querySelector('.bid-history-scroll');
                if (historySection) {
                    historySection.style.borderLeft = '3px solid #f59e0b';
                    setTimeout(() => {
                        historySection.style.borderLeft = '';
                    }, 2000);
                }
            }
        }
    </script>

</x-user-layout>
