<x-user-layout :title="'Placing Bid: ' . Str::limit($auction->title, 50)" :back-url="route('user.auctions.index')">

    <div x-data="biddingConsole({
        currentBid: {{ $auction->current_bid_yen }},
        availableCapacity: {{ $availableCapacityYen }},
        capacity: {{ $capacityYen ?? 0 }},
        shippingFee: {{ $userShippingRate?->fee_yen ?? 0 }},
        auctionId: {{ $auction->id }},
        minBid: {{ (int) $auction->current_bid_yen + 1 }},
        images: {{ json_encode($auction->image_urls ?: ($auction->thumbnail_url ? [$auction->thumbnail_url] : []), JSON_UNESCAPED_SLASHES) }}
    })" class="mx-auto px-4 py-8 lg:px-8">

        {{-- Main Auction Grid --}}
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 items-start">

            {{-- Left Column: Gallery & History --}}
            <div class="space-y-8 lg:col-span-7 xl:col-span-8">
                {{-- Hero Gallery --}}
                <div
                    class="relative overflow-hidden rounded-[2.5rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 shadow-sm">
                    <div class="aspect-[4/3] relative flex items-center justify-center p-8 bg-zinc-50 dark:bg-black/20">
                        <template x-for="(image, index) in images" :key="index">
                            <img x-show="activeImage === index" :src="image" alt="{{ $auction->title }}"
                                class="max-h-full max-w-full object-contain drop-shadow-2xl transition-opacity duration-500">
                        </template>

                        {{-- Arrows --}}
                        <button @click="prevImage()"
                            class="absolute left-4 p-3 bg-white/80 dark:bg-zinc-900/80 rounded-full shadow-lg backdrop-blur hover:scale-110">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click="nextImage()"
                            class="absolute right-4 p-3 bg-white/80 dark:bg-zinc-900/80 rounded-full shadow-lg backdrop-blur hover:scale-110">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    {{-- Thumbnails --}}
                    <div class="flex gap-4 p-6 overflow-x-auto">
                        <template x-for="(image, index) in images" :key="index">
                            <button @click="activeImage = index"
                                :class="activeImage === index ? 'ring-2 ring-blue-600' : ''"
                                class="w-20 h-20 shrink-0 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                                <img :src="image" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Bid History --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between">
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-900 dark:text-white">Bid
                            History</h2>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto">
                        @include('user.auctions._bid_history', ['bids' => $auction->bids])
                    </div>
                </div>
            </div>

            {{-- Right Column: Actions --}}
            <div class="space-y-8 lg:col-span-5 xl:col-span-4">

                {{-- Auction Info --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 p-8 shadow-sm border border-zinc-200 dark:border-white/10">
                    <h1 class="text-2xl font-black mb-6">{{ $auction->title }}</h1>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl">
                            <p class="text-[10px] font-black uppercase text-zinc-500">Current Bid</p>
                            <p class="text-xl font-black text-blue-600">¥{{ number_format($auction->current_bid_yen) }}
                            </p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl">
                            <p class="text-[10px] font-black uppercase text-zinc-500">Bid Count</p>
                            <p class="text-xl font-black">{{ $auction->bid_count }}</p>
                        </div>
                        <div class="col-span-2 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-2xl">
                            <p class="text-[10px] font-black uppercase text-zinc-500">Ends</p>
                            <p class="text-lg font-black text-rose-500">
                                {{ $auction->ends_at?->format('M d, H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Bidding Console --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 p-8 shadow-lg border border-zinc-200 dark:border-white/10 relative overflow-hidden">
                    <h2 class="text-xl font-black mb-8">Place Bid</h2>
                    <form id="bid-form" method="POST" action="{{ route('user.auctions.bids.store', $auction) }}"
                        class="space-y-6">
                        @csrf
                        <div
                            class="relative bg-zinc-50 dark:bg-black/20 rounded-[2rem] p-2 flex items-center border border-zinc-200 dark:border-zinc-800">
                            <button type="button" @click="bidAmount = Math.max(minBid, bidAmount - 500)"
                                class="px-6 py-4 font-black text-2xl">−</button>
                            <input x-model.number="bidAmount" name="amount_yen" type="number"
                                class="w-full bg-transparent border-none text-center text-3xl font-black" />
                            <button type="button" @click="bidAmount += 500"
                                class="px-6 py-4 font-black text-2xl">+</button>
                        </div>
                        <select x-model.number="shippingFee" name="shipping_rate_id"
                            class="w-full rounded-2xl bg-zinc-50 dark:bg-black/20 px-6 py-4 font-black text-sm">
                            @foreach ($shippingRates as $rate)
                                <option value="{{ $rate->id }}" data-fee="{{ $rate->fee_yen }}">
                                    {{ $rate->name }} (+¥{{ number_format($rate->fee_yen) }})</option>
                            @endforeach
                        </select>
                        <div class="p-6 rounded-[2rem] bg-blue-50 dark:bg-blue-900/10 space-y-2">
                            <div class="flex justify-between font-bold"><span>Total</span> <span>¥<span
                                        x-text="(bidAmount + shippingFee).toLocaleString()"></span></span></div>
                        </div>
                        <button type="button" data-confirm data-confirm-title="Confirm Bid"
                            data-confirm-text="Place Bid" data-confirm-type="info" data-confirm-on-confirm="#bid-form"
                            :data-confirm-message="`Are you sure you want to place a proxy bid of ¥${(bidAmount + shippingFee).toLocaleString()}?`"
                            :disabled="(bidAmount + shippingFee) > availableCapacity"
                            class="w-full rounded-full bg-blue-600 py-5 font-black text-white hover:bg-blue-500 disabled:opacity-50">
                            Place Proxy Bid
                        </button>
                    </form>
                </div>
                {{-- Wallet Summary --}}
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-900 p-8 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 mb-8">Market Standing
                    </h3>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[10px] font-black uppercase text-zinc-500">Available</p>
                            <p class="text-lg font-black text-zinc-900 dark:text-white">
                                ¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-zinc-500">Max Capacity</p>
                            <p class="text-lg font-black text-zinc-900 dark:text-white">
                                ¥{{ number_format($capacityYen ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Similar Auctions --}}
        @if (isset($similarAuctions) && $similarAuctions->count() > 0)
            <section class="mt-16">
                <h2 class="text-xl font-black mb-8 text-zinc-900 dark:text-white">Similar Auctions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($similarAuctions as $similar)
                        <a href="{{ route('user.auctions.show', $similar) }}"
                            class="rounded-[2rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 transition hover:shadow-lg">
                            <img src="{{ $similar->thumbnail_url }}"
                                class="aspect-square rounded-2xl mb-4 mx-auto object-cover min-h-[200px] min-w-[200px]">
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-white line-clamp-2">
                                {{ $similar->title }}</h3>
                            <p class="text-blue-600 font-black mt-2">¥{{ number_format($similar->current_bid_yen) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('biddingConsole', (config) => ({
                bidAmount: {{ (int) old('amount_yen', $auction->current_bid_yen + 500) }},
                shippingFee: config.shippingFee,
                availableCapacity: config.availableCapacity,
                minBid: config.minBid,
                images: config.images,
                activeImage: 0,
                prevImage() {
                    this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length;
                },
                nextImage() {
                    this.activeImage = (this.activeImage + 1) % this.images.length;
                }
            }));
        });
    </script>
</x-user-layout>
