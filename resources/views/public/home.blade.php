<x-guest-layout :title="'Japanese Proxy Auctions'">
    {{-- Hero Section with Carousel --}}
    <section class="relative overflow-hidden bg-brand-navy pb-32 pt-20 text-white lg:pt-32" x-data="{
        active: 0,
        items: [
            { title: 'Rare Japanese Antiques', desc: 'Direct access to exclusive historical collections.', img: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=2070&auto=format&fit=crop' },
            { title: 'Luxury Timepieces', desc: 'High-end horology from Tokyo\'s premier collectors.', img: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=2080&auto=format&fit=crop' },
            { title: 'Automotive Excellence', desc: 'Japanese performance cars and rare parts.', img: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=2070&auto=format&fit=crop' }
        ],
        next() { this.active = (this.active + 1) % this.items.length },
        prev() { this.active = (this.active - 1 + this.items.length) % this.items.length }
    }"
        x-init="setInterval(() => next(), 8000)">

        {{-- Background Images --}}
        <template x-for="(item, index) in items" :key="index">
            <div class="absolute inset-0 z-0 transition-opacity duration-1000"
                :class="active === index ? 'opacity-30 blur-[2px]' : 'opacity-0'" x-show="active === index">
                <img :src="item.img" :alt="item.title" class="h-full w-full object-cover" />
            </div>
        </template>
        <div class="absolute inset-0 bg-gradient-to-b from-brand-navy/10 via-brand-navy/10 to-brand-navy/10"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-8">
            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                <div
                    class="mb-8 inline-flex items-center gap-3 rounded-full bg-brand-gold/20 px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-brand-gold ring-1 ring-brand-gold/30">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-brand-gold animate-pulse"></span>
                    Global Access · Local Expertise
                </div>

                <div class="min-h-[250px] md:min-h-[350px]">
                    <h1 class="text-5xl font-black leading-tight tracking-tight sm:text-7xl lg:text-8xl"
                        x-text="items[active].title">
                    </h1>
                    <p class="mt-8 max-w-2xl text-lg font-medium leading-relaxed text-zinc-400 lg:text-xl"
                        x-text="items[active].desc">
                    </p>
                </div>

                <div class="mt-12 flex flex-wrap items-center gap-6">
                    <a href="{{ route('auctions.index') }}"
                        class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl bg-brand-gold px-10 py-5 text-sm font-black text-brand-navy shadow-[0_20px_40px_rgba(212,175,55,0.2)] transition hover:scale-[1.03] active:scale-95">
                        Browse All Categories
                        <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('register') }}"
                        class="flex items-center gap-3 rounded-2xl px-6 py-5 text-sm font-black text-white transition hover:bg-white/5">
                        Join the Community
                    </a>
                </div>

                {{-- Carousel Controls --}}
                <div class="mt-12 flex items-center gap-4">
                    <button @click="prev()"
                        class="rounded-full border border-white/20 p-3 hover:bg-white/10 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex gap-2">
                        <template x-for="(item, index) in items" :key="index">
                            <button @click="active = index" class="h-1.5 rounded-full transition-all duration-300"
                                :class="active === index ? 'w-8 bg-brand-gold' : 'w-1.5 bg-white/30'"></button>
                        </template>
                    </div>
                    <button @click="next()"
                        class="rounded-full border border-white/20 p-3 hover:bg-white/10 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Market Statistics --}}
    <section class="relative z-20 -mt-16 mx-auto max-w-7xl px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Listings</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">1.8M+</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Buying Power</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">5x Deposit</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Categories</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">25 Major</p>
            </div>
            <div class="rounded-3xl bg-brand-gold p-8 shadow-2xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-navy/60">Verified Sellers</p>
                <p class="mt-2 text-3xl font-black text-brand-navy">Premium Only</p>
            </div>
        </div>
    </section>

    {{-- Category Exploration --}}
    <section class="py-32 bg-slate-50 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mb-16 text-center lg:text-left">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Marketplace</h2>
                <h3 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white lg:text-5xl">Explore
                    Categories</h3>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @php
                    $topCategories = [
                        ['id' => '26318', 'name' => 'Automotive', 'icon' => '🚗'],
                        ['id' => '23000', 'name' => 'Fashion', 'icon' => '👗'],
                        ['id' => '23632', 'name' => 'Electronics', 'icon' => '📷'],
                        ['id' => '25464', 'name' => 'Toys & Games', 'icon' => '🎮'],
                        ['id' => '23140', 'name' => 'Watches', 'icon' => '⌚'],
                        ['id' => '20000', 'name' => 'Antiques', 'icon' => '🏺'],
                        ['id' => '23336', 'name' => 'Computers', 'icon' => '💻'],
                        ['id' => '24198', 'name' => 'Home & DIY', 'icon' => '🏠'],
                        ['id' => '42177', 'name' => 'Beauty', 'icon' => '💄'],
                        ['id' => '23976', 'name' => 'Food', 'icon' => '🍱'],
                        ['id' => '2084055844', 'name' => 'Pets', 'icon' => '🐾'],
                        ['id' => '2084060731', 'name' => 'Real Estate', 'icon' => '🏙️'],
                    ];
                @endphp
                @foreach ($topCategories as $cat)
                    <a href="{{ route('auctions.index', ['category' => $cat['id']]) }}"
                        class="group flex flex-col items-center rounded-3xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-100 transition hover:shadow-xl hover:ring-brand-gold dark:bg-zinc-900 dark:ring-white/5">
                        <span
                            class="text-4xl mb-4 transition-transform group-hover:scale-110">{{ $cat['icon'] }}</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $cat['name'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('auctions.index') }}"
                    class="text-sm font-black text-brand-gold hover:underline underline-offset-8 uppercase tracking-widest">View
                    All 1,000+ Categories</a>
            </div>
        </div>
    </section>

    {{-- Featured Selections --}}
    <section class="bg-slate-900 py-32 text-white dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mb-16 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Curated Picks</h2>
                    <h3 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Live Hot Auctions</h3>
                </div>
                <a href="{{ route('auctions.index') }}"
                    class="group flex items-center gap-3 text-sm font-black uppercase tracking-widest text-brand-gold transition hover:text-white">
                    View Market Catalog
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            @if ($featured->isEmpty())
                <div
                    class="flex h-80 items-center justify-center rounded-[2.5rem] border-2 border-dashed border-white/5 bg-white/5 italic text-white/30">
                    Synchronizing live market data from Japan...
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featured as $auction)
                        <div
                            class="group relative flex flex-col overflow-hidden rounded-[2.5rem] bg-white text-slate-900 shadow-2xl transition-transform duration-500 hover:-translate-y-2 dark:bg-zinc-800 dark:text-white">
                            <a href="{{ route('auctions.show', $auction) }}" class="flex-1">
                                <div class="relative aspect-[4/5] overflow-hidden">
                                    <img src="{{ $auction->thumbnail_url }}" alt=""
                                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    </div>
                                    <div class="absolute bottom-6 left-6 right-6">
                                        <span
                                            class="inline-flex w-full justify-center rounded-2xl bg-brand-gold py-3 text-xs font-black uppercase tracking-widest text-brand-navy opacity-0 transition-all duration-300 group-hover:opacity-100">Place
                                            Bid</span>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Closing
                                        {{ $auction->ends_at?->diffForHumans() }}</p>
                                    <h4
                                        class="mt-2 truncate text-sm font-black transition-colors group-hover:text-brand-gold">
                                        {{ $auction->title }}</h4>
                                    <div
                                        class="mt-6 flex items-center justify-between border-t border-slate-50 pt-4 dark:border-white/5">
                                        <span class="text-xs font-bold text-slate-400">Current Price</span>
                                        <span
                                            class="text-xl font-black text-brand-navy dark:text-brand-gold">¥{{ number_format($auction->current_bid_yen) }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Value Proposition --}}
    <section id="market-dynamics" class="py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mb-20 text-center lg:text-left">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Our Process</h2>
                <h3 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white lg:text-5xl">
                    Professional Proxy Flow</h3>
            </div>

            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <div class="group relative">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        01</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">Instant Capital</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Deposit Yen via Stripe or
                        Bank Transfer. Your balance immediately gives you 5x leverage to bid on any auction on Yahoo
                        Japan.</p>
                </div>
                <div
                    class="group relative border-t border-slate-100 pt-12 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 dark:border-white/5">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        02</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">Real-Time Sync</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Our scraper monitors
                        listings 24/7. When you bid, our automated engine communicates directly with Yahoo Japan to
                        secure your spot.</p>
                </div>
                <div
                    class="group relative border-t border-slate-100 pt-12 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 dark:border-white/5">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        03</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">Global Shipping</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Winning items are shipped
                        to our Japan warehouse, inspected, and then forwarded to your global address with full insurance
                        and tracking.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="py-32">
        <div class="mx-auto max-w-7xl px-4 md:px-8 ">
            <div class="relative overflow-hidden rounded-[4rem] bg-brand-navy p-12 text-center text-white sm:p-24">
                <div class="absolute right-0 top-0 -mr-24 -mt-24 h-96 w-96 rounded-full bg-brand-gold/10 blur-3xl">
                </div>
                <div class="relative z-10">
                    <h2 class="text-4xl font-black tracking-tight sm:text-6xl">Your Japan Market Entry.</h2>
                    <p class="mx-auto mt-8 max-w-xl text-lg text-white/50">Stop missing out on rare Japanese items.
                        Join thousands of international buyers using AuctionHub today.</p>
                    <div class="mt-12">
                        <a href="{{ route('register') }}"
                            class="inline-flex rounded-2xl bg-white px-10 py-5 text-sm font-black text-brand-navy shadow-xl transition hover:scale-105 active:scale-95">Create
                            Free Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
