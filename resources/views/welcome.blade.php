<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AuctionHub Japan · Luxury Gateway</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-brand-navy flex items-center justify-center min-h-screen text-white overflow-hidden relative"
    x-data="{ 
        active: 0, 
        items: @js($featured->map(fn($a) => [
            'id' => $a->id,
            'title' => Str::limit($a->title, 50),
            'price' => '¥' . number_format($a->current_bid_yen),
            'img' => $a->thumbnail_url,
            'url' => route('auctions.show', $a)
        ])),
        next() { this.active = (this.active + 1) % this.items.length },
        init() { setInterval(() => this.next(), 8000) }
    }">

    {{-- Interactive Auction Background Carousel --}}
    <div class="absolute inset-0 z-0">
        <template x-for="(item, index) in items" :key="index">
            <div x-show="active === index" 
                 x-transition:enter="transition opacity duration-1000"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition opacity duration-1000"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                <img :src="item.img" class="h-full w-full object-cover opacity-30 blur-[2px] scale-110 transition-transform duration-[8000ms]" :class="active === index ? 'scale-100' : 'scale-110'" />
                
                {{-- Floating Auction Card --}}
                <div class="absolute bottom-20 right-10 md:right-20 text-right z-30 max-w-md pointer-events-none">
                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-[2.5rem] border border-white/10 pointer-events-auto shadow-2xl transition-all hover:scale-105">
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold mb-3">Now Trending</p>
                        <a :href="item.url" class="block">
                            <h2 class="text-xl font-black text-white mb-4 leading-tight hover:text-brand-gold transition" x-text="item.title"></h2>
                        </a>
                        <div class="flex items-center justify-between gap-6">
                             <div>
                                <p class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Current Price</p>
                                <p class="text-2xl font-black text-brand-gold" x-text="item.price"></p>
                             </div>
                             <a :href="item.url" class="bg-white text-brand-navy px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-brand-gold transition">
                                Bid Now
                             </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="absolute inset-0 bg-gradient-to-tr from-brand-navy via-brand-navy/60 to-transparent z-10"></div>

    {{-- Main Brand Overlay --}}
    <div class="relative z-20 text-left w-full max-w-7xl px-8 md:px-20">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-brand-gold mb-10 shadow-[0_20px_50px_rgba(212,175,55,0.3)]">
            <span class="text-4xl font-black text-brand-navy">A</span>
        </div>
        
        <h1 class="text-6xl md:text-9xl font-black mb-8 tracking-tighter leading-[0.85]">
            AUCTION<br>HUB <span class="text-brand-gold">JAPAN</span>
        </h1>
        
        <p class="text-zinc-300 text-lg md:text-2xl mb-12 max-w-xl font-medium leading-relaxed">
            The professional digital gateway to the Japanese market. Direct proxy access to Yahoo Japan Auctions with technical logistics support.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-start gap-8">
            <a href="{{ route('auctions.index') }}" class="group relative px-12 py-6 bg-brand-gold text-brand-navy font-black rounded-2xl hover:scale-105 transition active:scale-95 shadow-2xl flex items-center gap-4">
                Explore Marketplace
                <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </a>
            
            <nav class="flex items-center gap-8 text-sm font-black uppercase tracking-widest text-zinc-400">
                <a href="{{ route('login') }}" class="hover:text-white transition">Login</a>
                <span class="text-zinc-800">/</span>
                <a href="{{ route('register') }}" class="hover:text-white transition">Register</a>
            </nav>
        </div>

        {{-- Market Pulse --}}
        <div class="mt-24 grid grid-cols-2 md:grid-cols-4 gap-12 border-t border-white/5 pt-12 max-w-3xl">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-gold/60">Live Listings</p>
                <p class="text-2xl font-black mt-2 tracking-tight">1.8M+</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-gold/60">Proxy Speed</p>
                <p class="text-2xl font-black mt-2 tracking-tight">Real-Time</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-gold/60">Leverage</p>
                <p class="text-2xl font-black mt-2 tracking-tight">5x Capacity</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-gold/60">Shipping</p>
                <p class="text-2xl font-black mt-2 tracking-tight">Global Hub</p>
            </div>
        </div>
    </div>

    {{-- Category Strip --}}
    <div class="absolute bottom-10 left-10 md:left-20 z-20 hidden md:flex items-center gap-10 opacity-40 hover:opacity-100 transition-opacity duration-500">
        <a href="{{ route('auctions.index', ['category' => '23140']) }}" class="text-[9px] font-black uppercase tracking-[0.4em] hover:text-brand-gold transition">Timepieces</a>
        <a href="{{ route('auctions.index', ['category' => '26318']) }}" class="text-[9px] font-black uppercase tracking-[0.4em] hover:text-brand-gold transition">Automotive</a>
        <a href="{{ route('auctions.index', ['category' => '23000']) }}" class="text-[9px] font-black uppercase tracking-[0.4em] hover:text-brand-gold transition">Fashion</a>
        <a href="{{ route('auctions.index', ['category' => '2084060731']) }}" class="text-[9px] font-black uppercase tracking-[0.4em] hover:text-brand-gold transition">Real Estate</a>
        <a href="{{ route('auctions.index') }}" class="text-[9px] font-black uppercase tracking-[0.4em] text-brand-gold underline underline-offset-8">All Categories</a>
    </div>

</body>

</html>
