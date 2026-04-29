<x-user-layout :title="'Placing Bid: ' . Str::limit($auction->title, 50)">
    <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                {{ Str::limit($auction->title, 50) }}
            </h1>

            <div
                class="mt-3 flex flex-wrap items-center gap-4 text-xs font-bold uppercase tracking-widest text-zinc-500">
                <span
                    class="rounded-full bg-brand-navy px-3 py-1 text-white dark:bg-brand-gold dark:text-brand-navy">{{ $auction->status }}</span>
                <span class="flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
                    Current Price: ¥{{ number_format($auction->current_bid_yen) }}
                </span>
                @if ($highestActiveBid && $highestActiveBid->max_amount_yen !== $auction->current_bid_yen)
                    <span class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
                        Highest Max Bid: ¥{{ number_format($highestActiveBid->max_amount_yen) }}
                    </span>
                @endif
                @if ($auction->ends_at)
                    <span class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
                        Ends: {{ $auction->ends_at->diffForHumans() }}
                    </span>
                @else
                    <span class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
                        Ends: Not specified
                    </span>
                @endif
            </div>
            @if ($highestActiveBid && $highestActiveBid->max_amount_yen > $auction->current_bid_yen)
                <div class="mt-4 rounded-2xl bg-zinc-50 p-4 text-xs text-zinc-600 dark:bg-white/5 dark:text-zinc-300">
                    @if ($userHighestActiveBid && $highestActiveBid->id === $userHighestActiveBid->id)
                        You are the current top bidder with a maximum proxy bid of ¥{{ number_format($highestActiveBid->max_amount_yen) }}.
                    @else
                        The current price is ¥{{ number_format($auction->current_bid_yen) }}, while the highest active proxy bid is ¥{{ number_format($highestActiveBid->max_amount_yen) }}.
                    @endif
                    The live price only increases when another bidder exceeds the current level.
                </div>
            @endif
        </div>

        <div
            class="relative overflow-hidden rounded-3xl min-w-[300px] bg-brand-navy p-6 shadow-xl dark:bg-brand-navy/50">
            <div class="relative z-10">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-gold/60">Active Bidding Wallet</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span
                        class="text-2xl font-bold text-white">¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</span>
                    <span class="text-xs font-medium text-brand-gold-light/60">Balance</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4 border-t border-white/10 pt-4">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-white/40">Committed</p>
                        <p class="text-sm font-bold text-brand-gold-light">
                            ¥{{ number_format((int) ($wallet?->locked_balance_yen ?? 0)) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-white/40">Multiplier</p>
                        <p class="text-sm font-bold text-white">{{ $user->bidding_multiplier_percent ?? 500 }}%</p>
                    </div>
                </div>
                <a href="{{ route('user.wallet.index') }}"
                    class="mt-4 block text-center text-[10px] font-bold uppercase tracking-widest text-brand-gold hover:text-white transition">Add
                    Funds</a>
            </div>
        </div>
    </div>

    @if (session('status') === 'bid-placed')
        <div
            class="mb-8 flex items-center gap-4 rounded-3xl border border-green-500/10 bg-green-500/5 p-6 text-green-700 dark:text-green-400">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Bid Successfully Processed</p>
                <p class="text-sm opacity-80">Your request has been transmitted to Yahoo Auctions Japan. We will notify
                    you if you are outbid.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-8">
            {{-- Bid Placement Card --}}
            {{-- Image Carousel --}}
            @php
$carouselImages = array_slice($auction->image_urls ?: ($auction->thumbnail_url ? [$auction->thumbnail_url] : []), 2);
            @endphp
            @if (count($carouselImages) > 0)
                <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                    <div class="relative group h-[500px] bg-zinc-100 dark:bg-black/20">
                        <div id="carousel-container" class="flex h-full transition-transform duration-500 ease-out">
                            @foreach ($carouselImages as $image)
                                <div class="w-full h-full shrink-0 flex items-center justify-center p-8">
                                    <img src="{{ $image }}" alt="{{ $auction->title }}" class="max-h-full max-w-full object-contain drop-shadow-2xl">
                                </div>
                            @endforeach
                        </div>

                        {{-- Navigation Arrows --}}
                        @if (count($carouselImages) > 1)
                            <button id="prev-slide" class="absolute left-6 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-3 text-zinc-900 shadow-lg opacity-0 group-hover:opacity-100 transition hover:bg-white dark:bg-zinc-800/80 dark:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button id="next-slide" class="absolute right-6 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-3 text-zinc-900 shadow-lg opacity-0 group-hover:opacity-100 transition hover:bg-white dark:bg-zinc-800/80 dark:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Indicators --}}
                            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                                @foreach ($carouselImages as $index => $image)
                                    <button class="carousel-dot h-1.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-brand-gold' : 'w-2 bg-zinc-300 dark:bg-zinc-700' }}" data-index="{{ $index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($carouselImages) > 1)
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const container = document.getElementById('carousel-container');
                            const dots = document.querySelectorAll('.carousel-dot');
                            const prev = document.getElementById('prev-slide');
                            const next = document.getElementById('next-slide');
                            let current = 0;
                            const total = {{ count($carouselImages) }};

                            function update() {
                                container.style.transform = `translateX(-${current * 100}%)`;
                                dots.forEach((dot, i) => {
                                    if (i === current) {
                                        dot.classList.remove('w-2', 'bg-zinc-300', 'dark:bg-zinc-700');
                                        dot.classList.add('w-8', 'bg-brand-gold');
                                    } else {
                                        dot.classList.add('w-2', 'bg-zinc-300', 'dark:bg-zinc-700');
                                        dot.classList.remove('w-8', 'bg-brand-gold');
                                    }
                                });
                            }

                            prev.onclick = () => { current = (current - 1 + total) % total; update(); };
                            next.onclick = () => { current = (current + 1) % total; update(); };
                            dots.forEach(dot => {
                                dot.onclick = () => { current = parseInt(dot.dataset.index); update(); };
                            });
                        });
                    </script>
                @endif
            @endif

            @if ($canBid)
                <div
                    class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                    <div
                        class="bg-zinc-50 px-8 py-6 dark:bg-white/5 flex items-center justify-between border-b border-zinc-100 dark:border-white/5">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-white">
                            Professional Bidding Console</h2>
                        <span class="text-[10px] font-bold italic text-brand-gold animate-pulse">Waiting for your
                            input...</span>
                    </div>
                    <div class="p-8">
                        <form id="bid-form" method="POST" action="{{ route('user.auctions.bids.store', $auction) }}"
                            class="space-y-8">
                            @csrf
                            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-widest text-zinc-500 mb-3"
                                        for="amount_yen">Target Bid (JPY)</label>
                                    <div class="relative">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-brand-gold font-bold">¥</span>
                                        </div>
                                        <input id="amount_yen" name="amount_yen" type="number"
                                            min="{{ (int) $auction->current_bid_yen + 1 }}"
                                            value="{{ old('amount_yen', (int) $auction->current_bid_yen + 500) }}"
                                            class="w-full rounded-2xl border-none bg-zinc-50 pl-10 pr-4 py-4 text-lg font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white" />
                                    </div>
                                    @error('amount_yen')
                                        <p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>
                                    @enderror

                                    <div class="mt-6">
                                        <label for="shipping_rate_id" class="block text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Shipping Destination</label>
                                        <select id="shipping_rate_id" name="shipping_rate_id" class="w-full rounded-2xl border-none bg-zinc-50 px-4 py-3 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                                            @foreach($shippingRates as $rate)
                                                <option value="{{ $rate->id }}" data-fee="{{ $rate->fee_yen }}" {{ (old('shipping_rate_id', $userShippingRate?->id) == $rate->id) ? 'selected' : '' }}>
                                                    {{ $rate->name }} (¥{{ number_format($rate->fee_yen) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mt-4 flex flex-col gap-2 rounded-2xl bg-zinc-50 p-4 dark:bg-black/20 border border-zinc-100 dark:border-white/5">
                                        <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-zinc-500">
                                            <span>Your Bid</span>
                                            <span class="text-zinc-900 dark:text-white">¥<span id="summary_bid_amount">{{ number_format(old('amount_yen', $auction->current_bid_yen + 500)) }}</span></span>
                                        </div>
                                        <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-zinc-500">
                                            <span>Destination Fee</span>
                                            <span class="text-zinc-900 dark:text-white">¥<span id="destination_fee_display">{{ number_format($userShippingRate?->fee_yen ?? 0) }}</span></span>
                                        </div>
                                        <div class="mt-2 flex justify-between border-t border-zinc-200 pt-2 dark:border-white/10">
                                            <span class="text-xs font-black uppercase tracking-widest text-brand-gold">Total Estimated</span>
                                            <span class="text-sm font-black text-brand-navy dark:text-brand-gold">
                                                ¥<span id="total_estimated_display">{{ number_format(old('amount_yen', $auction->current_bid_yen + 500) + ($userShippingRate?->fee_yen ?? 0)) }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-end">
                                    <button type="button"
                                        data-confirm
                                        data-confirm-title="Confirm Bid Placement"
                                        data-confirm-text="Place Bid"
                                        data-confirm-type="info"
                                        data-confirm-on-confirm="#bid-form"
                                        data-confirm-message="You are about to place a bid of ¥{amount} for this timepiece. This action will lock your wallet balance and cannot be undone."
                                        data-confirm-amount-selector="#amount_yen"
                                        data-confirm-shipping="{{ $auction->shipping_fee_yen ?? 0 }}"
                                        class="w-full rounded-2xl bg-brand-navy px-8 py-4 text-center text-sm font-bold uppercase tracking-widest text-brand-gold transition hover:scale-[1.02] hover:bg-black dark:bg-brand-gold dark:text-brand-navy dark:hover:bg-brand-gold-light">
                                        Confirm & Place Bid
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-brand-gold/5 p-4 border border-brand-gold/10">
                                <p class="text-[10px] text-zinc-500 leading-relaxed italic">
                                    <strong
                                        class="text-brand-gold-dark uppercase tracking-widest font-black">Note:</strong>
                                    By placing this bid, your wallet capacity will be locked until the auction end or a
                                    higher bid is registered. This action cannot be undone once confirmed on the
                                    Japanese market.
                                </p>
                            </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const amountInput = document.getElementById('amount_yen');
                        const rateSelect = document.getElementById('shipping_rate_id');
                        const bidDisplay = document.getElementById('summary_bid_amount');
                        const feeDisplay = document.getElementById('destination_fee_display');
                        const totalDisplay = document.getElementById('total_estimated_display');

                        function updateTotals() {
                            const amount = parseInt(amountInput.value) || 0;
                            const selectedOption = rateSelect.options[rateSelect.selectedIndex];
                            const fee = selectedOption ? (parseInt(selectedOption.dataset.fee) || 0) : 0;
                            
                            if (bidDisplay) bidDisplay.textContent = amount.toLocaleString();
                            if (feeDisplay) feeDisplay.textContent = fee.toLocaleString();
                            if (totalDisplay) totalDisplay.textContent = (amount + fee).toLocaleString();
                        }

                        if (amountInput && rateSelect && totalDisplay) {
                            amountInput.addEventListener('input', updateTotals);
                            rateSelect.addEventListener('change', updateTotals);
                            
                            // Initialize on load
                            updateTotals();
                        }
                    });
                </script>
            @else
                <div
                    class="rounded-3xl bg-zinc-50 p-12 text-center dark:bg-white/5 ring-1 ring-zinc-200 dark:ring-white/10">
                    <h3 class="text-lg font-bold text-zinc-400 uppercase tracking-widest">Bidding Conclusion</h3>
                    <p class="mt-2 text-sm text-zinc-500">
                        @if ($auction->status === 'finished' && $auction->winner_user_id === auth()->id())
                            <div class="mt-6 rounded-2xl bg-brand-gold/10 p-8 border border-brand-gold/20">
                                <h4 class="text-xl font-bold text-brand-navy dark:text-brand-gold uppercase tracking-tighter">Congratulations, You Won!</h4>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    Please confirm your shipping details to proceed with the delivery.
                                </p>
                                
                                <div class="mt-6 flex flex-col gap-4">
                                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-zinc-500">
                                        <span>Current Status</span>
                                        <span class="text-brand-gold font-black">{{ strtoupper($auction->shipment_status) }}</span>
                                    </div>

                                    @if($auction->shipment_status === 'pending')
                                        <form method="POST" action="{{ route('user.auctions.confirm-shipment', $auction) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-2xl bg-brand-navy px-8 py-4 text-center text-sm font-bold uppercase tracking-widest text-brand-gold transition hover:scale-[1.02] hover:bg-black dark:bg-brand-gold dark:text-brand-navy">
                                                Confirm Shipment Details
                                            </button>
                                        </form>
                                    @elseif($auction->shipment_status === 'bidder_confirmed')
                                        <div class="rounded-xl bg-zinc-100 p-4 text-center dark:bg-white/5">
                                            <span class="text-xs font-bold text-zinc-500 italic">Awaiting Admin Approval</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            @if ($auction->status !== 'active')
                                This auction is {{ $auction->status }}.
                            @elseif($auction->ends_at && $auction->ends_at->isPast())
                                This auction has ended.
                            @else
                                This piece is no longer open for active bidding.
                            @endif
                        @endif
                    </p>
                </div>
            @endif

            {{-- Live History --}}
            <div
                class="rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
                <div class="px-8 py-6 border-b border-zinc-100 dark:border-white/5">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-900 dark:text-white">Chronological
                        Activity</h2>
                </div>
                @php
                    $bids = $auction->bids()->with('user')->latest()->get();
                @endphp
                @forelse ($bids as $bid)
                    <div
                        class="flex items-center justify-between gap-4 px-8 py-5 transition hover:bg-zinc-50 dark:hover:bg-white/5">
                        <div class="flex items-center gap-4">
                            <div
                                class="h-2 w-2 rounded-full {{ $bid->status === 'active' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-red-500' }}">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">
                                    @if ($bid->user_id === auth()->id())
                                        <span class="text-brand-gold">You</span>
                                    @else
                                        @if ($bid->user)
                                            Collector #{{ substr($bid->user->id, 0, 4) }}
                                        @else
                                            System
                                        @endif
                                    @endif
                                </p>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">
                                    {{ $bid->status }} · {{ $bid->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-zinc-900 dark:text-white">
                                ¥{{ number_format($bid->amount_yen) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-zinc-500 italic">
                        No bids have been placed yet. Be the first to bid!
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Meta Sidebar --}}
        <div class="space-y-8">
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 mb-6">Provenance</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Origin ID</p>
                        <p class="mt-1 font-mono text-xs text-zinc-900 dark:text-white">
                            {{ $auction->yahoo_auction_id }}
                        </p>
                        <a href="https://page.auctions.yahoo.co.jp/jp/auction/{{ $auction->yahoo_auction_id }}" target="_blank" class="mt-2 inline-flex items-center text-[10px] font-black uppercase tracking-widest text-brand-gold hover:text-brand-gold-light transition">
                            View Original on Yahoo
                            <svg class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Seller</p>
                        <p class="mt-1 text-sm font-bold text-zinc-900 dark:text-white">
                            {{ $auction->seller_name ?? 'Japan Authentic' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Authenticity</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
                            <p class="text-[11px] font-bold text-brand-gold uppercase tracking-widest">Verified Piece
                            </p>
                        </div>
                    </div>
                    @if ($auction->ends_at)
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">End Time</p>
                            <p class="mt-1 text-sm text-zinc-900 dark:text-white">
                                {{ $auction->ends_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl bg-brand-navy p-8 text-white">
                <h3 class="text-xs font-bold uppercase tracking-widest text-brand-gold/60 mb-4">Concierge Support</h3>
                <p class="text-xs leading-relaxed text-white/60">Need assistance with this particular reference? Our
                    horology experts are standing by to verify shipping logistics and condition reports.</p>
                <a href="{{ route('contact') }}"
                    class="mt-6 block text-center text-[10px] font-bold uppercase tracking-widest text-brand-gold border border-brand-gold/30 rounded-xl py-3 hover:bg-brand-gold hover:text-brand-navy transition">Request
                    Consultation</a>
            </div>
        </div>
    </div>
</x-user-layout>
