<x-guest-layout :search="$filters['q'] ?? ''" :title="'Global Auction Market'">
    <div class="bg-zinc-50 dark:bg-zinc-950 pb-20">
        {{-- Header Section --}}
        <section class="border-b border-zinc-200 bg-white dark:border-white/5 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-600">Real-Time Market
                            Hub</span>
                        <h1
                            class="mt-4 text-4xl font-black tracking-tighter text-zinc-900 dark:text-white sm:text-5xl lg:text-6xl">
                            Live Auction Catalog.
                        </h1>
                        <p class="mt-6 text-base text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Directly synchronized with Yahoo Japan Auctions. Access exclusive Japanese inventory across
                            all categories with professional logistics support.
                        </p>
                    </div>

                    {{-- <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 rounded-2xl bg-zinc-100 px-4 py-2 dark:bg-white/5">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400">1.8M+ Active Listings</span>
                        </div>
                    </div> --}}
                </div>

                {{-- Filter Section --}}
                <div class="mt-12">
                    <x-auction-filters :filters="$filters" :route="route('auctions.index')" />
                </div>
            </div>
        </section>

        {{-- Main Content --}}
        <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
            @if ($auctions->isEmpty())
                <div
                    class="flex h-96 flex-col items-center justify-center rounded-[3rem] border-2 border-dashed border-zinc-200 bg-white/50 text-center dark:border-white/5 dark:bg-zinc-900/50">
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-3xl bg-zinc-100 text-zinc-400 dark:bg-white/5">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-black text-zinc-900 dark:text-white">No Listings Found</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Try adjusting your filters or search terms
                        to explore more of the market.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($auctions as $auction)
                        <x-auction-card :auction="$auction" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-20">
                    {{ $auctions->links() }}
                </div>
            @endif
        </section>
    </div>
</x-guest-layout>
