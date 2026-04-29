@props(['filters' => [], 'route'])

<form method="GET" action="{{ $route }}" class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-xl ring-1 ring-slate-100 dark:ring-white/5 mb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
        {{-- Search --}}
        <div class="lg:col-span-2">
            <label for="q" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Search timepieces</label>
            <div class="relative">
                <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}" placeholder="Omega, Rolex, Grand Seiko..." 
                    class="w-full rounded-2xl border-none bg-slate-50 dark:bg-black/20 px-5 py-3 text-sm font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-brand-gold dark:text-white" />
            </div>
        </div>

        {{-- Price Range --}}
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Price (¥)</label>
            <div class="flex items-center gap-2">
                <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" 
                    class="w-full rounded-xl border-none bg-slate-50 dark:bg-black/20 px-3 py-3 text-xs font-bold focus:ring-2 focus:ring-brand-gold dark:text-white" />
                <span class="text-slate-300">-</span>
                <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max" 
                    class="w-full rounded-xl border-none bg-slate-50 dark:bg-black/20 px-3 py-3 text-xs font-bold focus:ring-2 focus:ring-brand-gold dark:text-white" />
            </div>
        </div>

        {{-- Sort --}}
        <div>
            <label for="sort" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Sort by</label>
            <select name="sort" id="sort" class="w-full rounded-xl border-none bg-slate-50 dark:bg-black/20 px-5 py-3 text-xs font-bold focus:ring-2 focus:ring-brand-gold dark:text-white appearance-none">
                <option value="ends_soon" {{ ($filters['sort'] ?? '') === 'ends_soon' ? 'selected' : '' }}>Ending Soon</option>
                <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="newest" {{ ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' }}>Recently Added</option>
                <option value="bid_count" {{ ($filters['sort'] ?? '') === 'bid_count' ? 'selected' : '' }}>Most Bids</option>
            </select>
        </div>

        {{-- Unique Sellers --}}
        <div class="flex items-center gap-3 h-full pb-3">
            <div class="relative flex items-center">
                <input type="checkbox" name="unique_sellers" id="unique_sellers" value="1" {{ ($filters['unique_sellers'] ?? '') ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-none bg-slate-50 dark:bg-black/20 text-brand-gold focus:ring-brand-gold" />
            </div>
            <label for="unique_sellers" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Unique Sellers Only</label>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-brand-navy text-brand-gold rounded-xl py-3 text-xs font-black uppercase tracking-widest transition hover:scale-[1.02] active:scale-95 shadow-lg">
                Filter
            </button>
            <a href="{{ $route }}" class="bg-slate-100 dark:bg-white/5 text-slate-400 rounded-xl px-4 py-3 text-xs font-black uppercase tracking-widest transition hover:bg-slate-200 dark:hover:bg-white/10 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    </div>
</form>
