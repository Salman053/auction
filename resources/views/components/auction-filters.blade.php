@props(['filters' => [], 'route'])

<form method="GET" action="{{ $route }}"
    class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-xl ring-1 ring-slate-100 dark:ring-white/5 mb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 items-end">
        {{-- Search --}}
        <div class="lg:col-span-2">
            <label for="q"
                class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Search
                auctions</label>
            <div class="relative">
                <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search for anything..."
                    class="w-full rounded-2xl border-none bg-slate-50 dark:bg-black/20 px-5 py-3 text-sm font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-brand-gold dark:text-white" />
            </div>
        </div>

        {{-- Category --}}
        @php
            $topLevelCategories = [
                '' => 'All Categories',
                '26318' => 'Automotive',
                '23000' => 'Fashion',
                '23140' => 'Watches',
                '24698' => 'Sports',
                '23632' => 'Electronics',
                '23336' => 'Computers',
                '25464' => 'Toys & Games',
                '24242' => 'Hobby',
                '20000' => 'Antiques',
                '21600' => 'Books',
                '22152' => 'Music',
                '21964' => 'Movies',
                '24198' => 'Home & DIY',
                '2084060731' => 'Real Estate',
                '26084' => 'Other',
            ];
            $currentCategory = $filters['category'] ?? '';
        @endphp
        <div>
            <label for="category"
                class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Category</label>
            <select name="category" id="category" onchange="this.form.submit()"
                class="w-full rounded-xl border-none bg-slate-50 dark:bg-black/20 px-5 py-3 text-xs font-bold focus:ring-2 focus:ring-brand-gold dark:text-white appearance-none">
                @foreach($topLevelCategories as $id => $name)
                    <option value="{{ $id }}" {{ $currentCategory == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
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

        {{-- Status --}}
        @php
            $currentStatus = $filters['status'] ?? 'active';
        @endphp
        <div>
            <label for="status"
                class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Status</label>
            <select name="status" id="status" onchange="this.form.submit()"
                class="w-full rounded-xl border-none bg-slate-50 dark:bg-black/20 px-5 py-3 text-xs font-bold focus:ring-2 focus:ring-brand-gold dark:text-white appearance-none">
                <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active</option>
                <option value="finished" {{ $currentStatus === 'finished' ? 'selected' : '' }}>Finished</option>
                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Auctions</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 bg-brand-navy text-brand-gold rounded-xl py-3 text-xs font-black uppercase tracking-widest transition hover:scale-[1.02] active:scale-95 shadow-lg">
                Filter
            </button>
            <a href="{{ $route }}"
                class="bg-slate-100 dark:bg-white/5 text-slate-400 rounded-xl px-4 py-3 text-xs font-black uppercase tracking-widest transition hover:bg-slate-200 dark:hover:bg-white/10 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    </div>
</form>
