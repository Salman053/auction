@props(['auctions'])

<div x-data="{
    active: 0,
    auctions: {{ json_encode($auctions) }},
    next() { this.active = (this.active + 1) % this.auctions.length },
    prev() { this.active = (this.active - 1 + this.auctions.length) % this.auctions.length }
}" x-init="setInterval(() => next(), 6000)" class="relative h-full w-full overflow-hidden group">

    <template x-for="(auction, index) in auctions" :key="index">
        <div x-show="active === index" x-transition:enter="transition ease-in-out duration-1000"
            x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
            class="absolute inset-0">

            <!-- Dynamic Background -->
            <img :src="auction.thumbnail_url" loading="lazy"
                class="absolute inset-0 h-full w-full object-cover scale-105 transition-transform duration-[7000ms] ease-linear"
                :class="active === index ? 'scale-100' : ''" />

            <!-- Atmospheric Overlays -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20"></div>
            <div class="absolute inset-0 bg-blue-900/20 mix-blend-overlay"></div>

            <!-- Content -->
            <div class="absolute bottom-0 left-0 p-12 w-full lg:w-2/3">
                <div class="flex items-center space-x-3 mb-4">
                    <span
                        class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-white border border-white/20">Live
                        Auction</span>
                    <span class="text-white/60 text-xs font-medium tracking-wide"
                        x-text="'ID: ' + auction.yahoo_auction_id"></span>
                </div>

                <h2 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-[0.9] mb-6"
                    x-text="auction.title"></h2>

                <div class="flex items-center space-x-8">
                    <div>
                        <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest">Current Bid</p>
                        <p class="text-3xl font-black text-white"
                            x-text="'¥' + auction.current_bid_yen.toLocaleString()"></p>
                    </div>
                    <a :href="'/auctions/' + auction.yahoo_auction_id"
                        class="px-8 py-3 bg-white text-zinc-950 font-black text-xs uppercase tracking-widest rounded-full transition-all hover:bg-blue-500 hover:text-white shadow-lg shadow-blue-600/30">
                        Place Bid
                    </a>
                </div>
            </div>
        </div>
    </template>

    <!-- Navigation -->
    <button @click="prev()"
        class="absolute left-6 top-1/2 -translate-y-1/2 p-3 bg-white/5 backdrop-blur-md rounded-full text-white/50 hover:bg-white/20 hover:text-white transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next()"
        class="absolute right-6 top-1/2 -translate-y-1/2 p-3 bg-white/5 backdrop-blur-md rounded-full text-white/50 hover:bg-white/20 hover:text-white transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</div>
