@props(['filters' => [], 'route'])

<form method="GET" action="{{ $route }}"
    class="bg-white dark:bg-zinc-900 rounded-lg p-6 shadow-xl ring-1 ring-zinc-100 dark:ring-white/5 mb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-6 items-end">
        {{-- Search --}}
        <div class="lg:col-span-2">
            <label for="q" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Search
                auctions</label>
            <div class="relative">
                <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search for anything..."
                    class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-5 py-3 text-sm font-black placeholder:text-zinc-400 focus:ring-2 focus:ring-blue-600 dark:text-white" />
            </div>
        </div>

        {{-- Category --}}
        @php
            $topLevelCategories = [
                '' => 'All Categories',
                '26318' => 'Automotive',
                '23000' => 'Fashion',
                '23140' => 'Watches',
                '20060' => 'Art & Design',
                '20000' => 'Antiques',
                '24698' => 'Sports & Recreation',
                '23632' => 'Consumer Electronics',
                '23336' => 'Computers & IT',
                '25464' => 'Toys & Games',
                '24242' => 'Collectibles & Hobby',
                '21600' => 'Books, Magazines & Comics',
                '22152' => 'Music (CDs, Vinyl)',
                '21964' => 'Movies & Video',
                '24198' => 'Home, Furniture & DIY',
                '2084043920' => 'Health & Beauty',
                '2084008403' => 'Baby & Kids',
                '20412' => 'Food & Beverages',
                '2084023616' => 'Pets & Supplies',
                '2084005069' => 'Tickets & Travel',
                '2084060731' => 'Real Estate',
                '26084' => 'Other',
            ];
            $currentCategory = $filters['category'] ?? '';
        @endphp
        <div>
            <label for="category"
                class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Category</label>
            <select name="category" id="category" onchange="this.form.submit()"
                class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-5 py-3 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-blue-600 dark:text-white appearance-none">
                @foreach ($topLevelCategories as $id => $name)
                    <option value="{{ $id }}" {{ $currentCategory == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Price Range --}}
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Price (¥)</label>
            <div class="flex items-center gap-2">
                <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min"
                    class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-3 py-3 text-xs font-black focus:ring-2 focus:ring-blue-600 dark:text-white" />
                <span class="text-zinc-300">-</span>
                <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max"
                    class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-3 py-3 text-xs font-black focus:ring-2 focus:ring-blue-600 dark:text-white" />
            </div>
        </div>

        {{-- Status --}}
        @php
            $currentStatus = $filters['status'] ?? 'active';
            $currentSort = $filters['sort'] ?? 'random';
        @endphp
        <div>
            <label for="status"
                class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Status</label>
            <select name="status" id="status" onchange="this.form.submit()"
                class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-5 py-3 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-blue-600 dark:text-white appearance-none">
                <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active</option>
                <option value="finished" {{ $currentStatus === 'finished' ? 'selected' : '' }}>Finished</option>
                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Auctions</option>
            </select>
        </div>

        {{-- Sort --}}
        <div>
            <label for="sort" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Sort
                By</label>
            <select name="sort" id="sort" onchange="this.form.submit()"
                class="w-full rounded-lg border-none bg-zinc-50 dark:bg-black/20 px-5 py-3 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-blue-600 dark:text-white appearance-none">
                <option value="random" {{ $currentSort === 'random' ? 'selected' : '' }}>Random</option>
                <option value="ends_soon" {{ $currentSort === 'ends_soon' ? 'selected' : '' }}>Ends Soon</option>
                <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Price: Low to High
                </option>
                <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Price: High to Low
                </option>
                <option value="bid_count" {{ $currentSort === 'bid_count' ? 'selected' : '' }}>Most Bids</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 bg-blue-600 text-white rounded-lg py-3 text-xs font-black uppercase tracking-widest transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95 shadow-lg shadow-blue-600/20">
                Filter
            </button>
            <a href="{{ $route }}"
                class="bg-zinc-100 dark:bg-white/5 text-zinc-400 rounded-lg px-4 py-3 text-xs font-black uppercase tracking-widest transition hover:bg-zinc-200 dark:hover:bg-white/10 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    </div>
</form>
