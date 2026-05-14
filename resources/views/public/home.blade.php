<x-guest-layout :title="'Japanese Proxy Auctions & Marketplace'">
    <div class="bg-zinc-50 dark:bg-zinc-950 pb-20">
        {{-- Hero Slider Section --}}
        <section class="mx-auto max-w-7xl px-4 py-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 h-[300px] lg:h-[450px]">
                {{-- Main Large Slider --}}
                <div class="relative overflow-hidden rounded-lg lg:col-span-8 bg-zinc-900 group">
                    <div x-data="{
                        active: 0,
                        slides: [
                            { title: 'Exclusive Japanese Antiques', subtitle: 'Direct Access to Yahoo Japan Auctions', cta: 'Browse Antiques', img: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=2070&auto=format&fit=crop' },
                            { title: 'Luxury Timepieces', subtitle: 'Rare Watches from Tokyo Collectors', cta: 'View Watches', img: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=2080&auto=format&fit=crop' },
                            { title: 'Classic JDM Parts', subtitle: 'Genuine Automotive Components', cta: 'Search Parts', img: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=2070&auto=format&fit=crop' }
                        ],
                        next() { this.active = (this.active + 1) % this.slides.length },
                        prev() { this.active = (this.active - 1 + this.slides.length) % this.slides.length }
                    }" x-init="setInterval(() => next(), 10000)" class="h-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div x-show="active === index" x-transition:enter="transition ease-out duration-700"
                                x-transition:enter-start="opacity-0 scale-105"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute inset-0 flex items-center px-12">
                                <img :src="slide.img"
                                    class="absolute inset-0 h-full w-full object-cover opacity-60" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent">
                                </div>
                                <div class="relative z-10 max-w-lg text-white">
                                    <span
                                        class="inline-block rounded-full bg-blue-600/20 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 ring-1 ring-blue-400/30 mb-6">Live
                                        Marketplace</span>
                                    <h2 class="text-4xl font-black lg:text-6xl tracking-tighter" x-text="slide.title">
                                    </h2>
                                    <p class="mt-4 text-xl font-medium text-zinc-300" x-text="slide.subtitle"></p>
                                    <a :href="'{{ route('auctions.index') }}'"
                                        class="mt-8 inline-block rounded-full bg-blue-600 px-10 py-4 text-[11px] font-black uppercase tracking-widest text-white transition-all hover:bg-blue-700 hover:scale-105 active:scale-95 shadow-2xl shadow-blue-600/40"
                                        x-text="slide.cta"></a>
                                </div>
                            </div>
                        </template>

                        {{-- Controls --}}
                        <div class="absolute bottom-6 right-12 flex gap-2">
                            <template x-for="(slide, index) in slides" :key="index">
                                <button @click="active = index" class="h-1.5 rounded-full transition-all"
                                    :class="active === index ? 'w-8 bg-blue-600' : 'w-2 bg-white/30'"></button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Side Banner: Current Stats --}}
                <div
                    class="relative overflow-hidden rounded-lg min-h-fit lg:col-span-4 bg-white p-8 border border-zinc-200 dark:bg-zinc-900 dark:border-white/5">
                    <div class="relative z-10">
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Market
                            Dynamics</span>
                        <h2 class="mt-4 text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Proxy Bidding
                            Made Easy</h2>
                        <p class="mt-4 text-sm text-zinc-500 leading-relaxed dark:text-zinc-400">Secure rare items from
                            Japan with our automated bidding engine. We handle the logistics, you secure the win.</p>

                        <div class="mt-8 space-y-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-widest">
                                        Real-time Sync</p>
                                    <p class="text-[10px] text-zinc-500">Instant updates from Yahoo Japan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-widest">
                                        Global Shipping</p>
                                    <p class="text-[10px] text-zinc-500">Fully insured Tokyo-to-Door</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register') }}"
                            class="mt-8 flex w-full items-center justify-center rounded-lg bg-zinc-900 py-4 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:bg-black hover:scale-[1.02] dark:bg-white dark:text-zinc-900">Start
                            Bidding Now</a>
                    </div>
                </div>
            </div>
        </section>



        {{-- Active Auctions Grid --}}
        <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">Hot Live Auctions</h2>
                    <p class="text-sm text-zinc-500 mt-1">Trending items with active bidding activity</p>
                </div>
                <a href="{{ route('auctions.index') }}"
                    class="group flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest">
                    Market Catalog
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($featured->take(10) as $auction)
                    <x-auction-card :auction="$auction" />
                @endforeach
            </div>
        </section>

        {{-- Shipping Locations Section --}}
        @if ($shippingLocations->isNotEmpty())
            <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2
                            class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">
                            Import Destinations</h2>
                        <p class="text-sm text-zinc-500 mt-1">Available shipping locations we deliver to from Japan</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($shippingLocations as $rate)
                        <div
                            class="group relative overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-lg dark:border-white/5 dark:bg-zinc-900">
                            <div class="flex items-start justify-between">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="inline-block rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">
                                    ¥{{ number_format($rate->fee_yen) }}
                                </span>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm font-black text-zinc-900 dark:text-white">{{ $rate->name }}</p>
                                <p class="mt-1 text-[11px] font-medium text-zinc-500 uppercase tracking-widest">
                                    {{ $rate->port }} · {{ $rate->country }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        {{-- Promotion Banners: Auction Context --}}
        <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="relative min-h-fit h-72 overflow-hidden rounded-[2.5rem] bg-blue-600 p-12 text-white">
                    <div class="relative z-10 max-w-md">
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">New Feature</span>
                        <h3 class="mt-4 text-3xl font-black leading-tight">Instant Bidding Power with 5x Leverage</h3>
                        <p class="mt-4 text-sm text-blue-100 leading-relaxed">Deposit funds and immediately bid up to 5
                            times your balance on high-value Japanese auctions.</p>
                        <a href="{{ route('user.wallet.index') }}"
                            class="mt-8 inline-block rounded-full bg-white px-8 py-3 text-xs font-black uppercase tracking-widest text-blue-600 shadow-xl transition hover:scale-105">Top
                            up Wallet</a>
                    </div>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 opacity-20">
                        <svg class="h-64 w-64" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.21 1.87 1.15 0 1.81-.58 1.81-1.39 0-2.26-4.91-1.32-4.91-4.48 0-1.26.76-2.45 2.2-2.85V7h2.67v1.91c1.38.31 2.39 1.19 2.56 2.55h-1.96c-.11-.78-.59-1.32-1.63-1.32-1.07 0-1.67.46-1.67 1.18 0 1.84 4.91.97 4.91 4.38 0 1.28-.73 2.42-2.19 2.89z" />
                        </svg>
                    </div>
                </div>
                <div
                    class="relative h-72 min-h-fit overflow-hidden rounded-[2.5rem] bg-zinc-900 p-12 text-white dark:bg-blue-900/20">
                    <div class="relative z-10 max-w-md">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Logistics</span>
                        <h3 class="mt-4 text-3xl font-black leading-tight">Professional Inspection in Tokyo</h3>
                        <p class="mt-4 text-sm text-zinc-400 leading-relaxed">Our experts verify every winning item at
                            our Tokyo hub. We ensure authenticity before global dispatch.</p>
                        <a href="{{ route('how-it-works') }}"
                            class="mt-8 inline-block rounded-full bg-blue-600 px-8 py-3 text-xs font-black uppercase tracking-widest text-white shadow-xl transition hover:scale-105">Our
                            Process</a>
                    </div>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 opacity-10">
                        <svg class="h-64 w-64" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 8l-8 5-8-5V6l8 5 8-5v2zm0-4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mx-auto max-w-7xl px-4 py-20 lg:px-8 text-center">
            <div class="relative overflow-hidden rounded-[4rem] bg-zinc-100 p-12 dark:bg-zinc-900 sm:p-24">
                <div class="relative z-10">
                    <h2 class="text-4xl font-black tracking-tighter text-zinc-900 dark:text-white sm:text-6xl">Your
                        Gateway to Japan.</h2>
                    <p class="mx-auto mt-8 max-w-xl text-lg text-zinc-500">Stop missing out on rare Japanese items.
                        Join thousands of international collectors today.</p>
                    <div class="mt-12 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="rounded-lg bg-blue-600 px-10 py-5 text-[11px] font-black uppercase tracking-widest text-white shadow-2xl shadow-blue-600/40 transition hover:scale-105 active:scale-95">Create
                            Free Account</a>
                        <a href="{{ route('auctions.index') }}"
                            class="rounded-lg bg-white px-10 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-900 shadow-sm ring-1 ring-zinc-200 transition hover:bg-zinc-50 hover:scale-105">Explore
                            Market</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
