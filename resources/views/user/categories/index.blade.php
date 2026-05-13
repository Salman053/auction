<x-user-layout :title="'Market Categories'">
    <div class="mb-12">
        <div class="mb-10">
            <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">Market Explorer</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Browse thousands of niches across the Japan Auction network.</p>
        </div>

        {{-- Top Level Category Grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('user.categories.show', $category) }}"
                    class="group relative flex flex-col overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-white/10">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-zinc-50 dark:bg-white/5 transition-transform group-hover:scale-150"></div>
                    
                    <div class="relative z-10">
                        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20 group-hover:scale-110 transition-transform">
                            <span class="text-xl font-black">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        
                        <h3 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white group-hover:text-blue-600 transition-colors">
                            {{ $category->name }}
                        </h3>
                        
                        <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-white/5">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">View Collection</span>
                            <svg class="h-4 w-4 text-zinc-300 transition-transform group-hover:translate-x-1 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-user-layout>
