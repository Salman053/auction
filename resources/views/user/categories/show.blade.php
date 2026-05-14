<x-user-layout :title="$category->name">
    <div class="mb-12">
        <div class="mb-10 flex items-center justify-between gap-4">
            <div>
                <nav class="mb-4 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-400">
                    <a href="{{ route('user.categories.index') }}" class="hover:text-blue-600">Categories</a>
                    @if ($category->parent)
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                        <a href="{{ route('user.categories.show', $category->parent) }}"
                            class="hover:text-blue-600">{{ $category->parent->name }}</a>
                    @endif
                </nav>
                <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">
                    {{ $category->name }}
                </h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Explore specific niches within the
                    {{ $category->name }} sector.</p>
            </div>

            <a href="{{ route('user.auctions.index', ['category' => $category->yahoo_category_id]) }}"
                class="rounded-full bg-blue-600 px-8 py-4 text-[11px] font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                View All Auctions
            </a>
        </div>

        @if ($category->children->count() > 0)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($category->children as $child)
                    <a href="{{ route('user.categories.show', $child) }}"
                        class="group flex items-center justify-between rounded-lg bg-white p-6 shadow-sm ring-1 ring-zinc-200 transition hover:shadow-lg dark:bg-zinc-900 dark:ring-white/10">
                        <span
                            class="text-sm font-black text-zinc-900 dark:text-white group-hover:text-blue-600 transition-colors">{{ $child->name }}</span>
                        <svg class="h-4 w-4 text-zinc-300 transition-transform group-hover:translate-x-1 dark:text-zinc-700"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        @else
            <div
                class="rounded-[2.5rem] bg-zinc-100 p-12 text-center dark:bg-white/5 border border-dashed border-zinc-300 dark:border-white/10">
                <div class="mb-6 flex justify-center">
                    <div class="rounded-full bg-white p-6 shadow-xl dark:bg-zinc-800">
                        <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white">This is a specialized niche.</h3>
                <p class="mt-2 text-zinc-500">No further sub-categories available. You can view all active listings in
                    this category below.</p>
                <a href="{{ route('user.auctions.index', ['category' => $category->yahoo_category_id]) }}"
                    class="mt-8 inline-flex rounded-full bg-zinc-900 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white transition hover:scale-105 dark:bg-white dark:text-zinc-900">
                    Search Live Listings
                </a>
            </div>
        @endif
    </div>
</x-user-layout>
