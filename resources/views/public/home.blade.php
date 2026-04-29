<x-guest-layout :title="'Japanese Watch Auctions'">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-brand-navy pb-32 pt-20  text-white lg:pt-32">
        <div class="absolute inset-0 z-0">
            <img src="/images/hero_banner.png" alt="Luxury Watches"
                class="h-full w-full object-cover opacity-30 blur-[2px]" />
            <div class="absolute inset-0 bg-gradient-to-b from-brand-navy/60 via-brand-navy/80 to-brand-navy"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-8">
            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                <div
                    class="mb-8 inline-flex items-center gap-3 rounded-full bg-brand-gold/20 px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-brand-gold ring-1 ring-brand-gold/30">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-brand-gold animate-pulse"></span>
                    Live from Yahoo Japan
                </div>

                <h1 class="text-5xl font-black leading-tight tracking-[calc(-0.02em)] sm:text-7xl lg:text-8xl">
                    Master the art of <br>
                    <span class="text-brand-gold">Precision Bidding.</span>
                </h1>

                <p class="mt-8 max-w-2xl text-lg font-medium leading-relaxed text-zinc-400 lg:text-xl">
                    Experience direct access to Japan's most exclusive watch auctions. Win rare timepieces with a
                    professional bidding engine designed for serious collectors.
                </p>

                <div class="mt-12 flex flex-wrap items-center gap-6">
                    <a href="{{ route('auctions.index') }}"
                        class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl bg-brand-gold px-10 py-5 text-sm font-black text-brand-navy shadow-[0_20px_40px_rgba(212,175,55,0.2)] transition hover:scale-[1.03] active:scale-95">
                        Start Exploration
                        <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="#market-dynamics"
                        class="flex items-center gap-3 rounded-2xl px-6 py-5 text-sm font-black text-white/70 transition hover:text-white hover:bg-white/5">
                        Platform Logistics
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust & Scale --}}
    <section class="relative z-20 -mt-16 mx-auto max-w-7xl px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Live Inventory</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">120,402+</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Winning Edge</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">500% Limit</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Daily Volume</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">¥1.2B+</p>
            </div>
            <div class="rounded-3xl bg-brand-gold p-8 shadow-2xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-navy/60">Success Rate</p>
                <p class="mt-2 text-3xl font-black text-brand-navy">99.8%</p>
            </div>
        </div>
    </section>

    {{-- Value Proposition --}}
    <section id="market-dynamics" class="py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mb-20 text-center lg:text-left">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Market Dynamics</h2>
                <h3 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white lg:text-5xl">
                    Professional Collectors' Flow</h3>
            </div>

            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <div class="group relative">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        01</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">Secure Capitalization</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Deposit Yen into our
                        protected custodial wallet. Your capital status immediately unlocks 5x leverage for aggressive
                        market positioning.</p>
                </div>
                <div
                    class="group relative border-t border-slate-100 pt-12 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 dark:border-white/5">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        02</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">Algorithmic Bidding</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Execute bids in real-time.
                        Our high-frequency engine syncs directly with Japanese auction houses ensuring your position is
                        always maintained.</p>
                </div>
                <div
                    class="group relative border-t border-slate-100 pt-12 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 dark:border-white/5">
                    <div
                        class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 font-black text-slate-900 transition-colors group-hover:bg-brand-navy group-hover:text-brand-gold dark:bg-white/5 dark:text-white">
                        03</div>
                    <h4 class="text-xl font-black text-slate-900 dark:text-white">White-Glove Logistics</h4>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Once victory is secured,
                        settlements are instant. We handle the export legalities and insured shipping to your private
                        vault.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Selections --}}
    <section class="bg-slate-900 py-32 text-white dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mb-16 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Executive Selection</h2>
                    <h3 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Live Horology Auctions</h3>
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
                                            class="inline-flex w-full justify-center rounded-2xl bg-brand-gold py-3 text-xs font-black uppercase tracking-widest text-brand-navy opacity-0 transition-all duration-300 group-hover:opacity-100">Executive
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
                                        <span class="text-xs font-bold text-slate-400">Current Position</span>
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

    {{-- Final CTA --}}
    <section class="py-32">
        <div class="mx-auto max-w-7xl px-4 md:px-8 ">
            <div class="relative overflow-hidden rounded-[4rem] bg-brand-navy p-12 text-center text-white sm:p-24">
                <div class="absolute right-0 top-0 -mr-24 -mt-24 h-96 w-96 rounded-full bg-brand-gold/10 blur-3xl">
                </div>
                <div class="relative z-10">
                    <h2 class="text-4xl font-black tracking-tight sm:text-6xl">Secure your first piece today.</h2>
                    <p class="mx-auto mt-8 max-w-xl text-lg text-white/50">Join an exclusive network of international
                        luxury collectors accessing Japan's market directly.</p>
                    <div class="mt-12">
                        <a href="{{ route('register') }}"
                            class="inline-flex rounded-2xl bg-white px-10 py-5 text-sm font-black text-brand-navy shadow-xl transition hover:scale-105 active:scale-95">Open
                            Permanent Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
