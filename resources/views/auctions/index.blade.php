<x-guest-layout :search="$filters['q'] ?? ''" :title="'Global Market'">
    <div class="py-12 sm:py-20">
        <div class="mx-auto max-w-7xl min-w-[80%] px-8">
            <div class="flex flex-row flex-wrap items-center justify-between gap-6">
                <div>
                    <h1 class="text-xs font-black uppercase tracking-[0.3em] text-brand-gold">Real-Time Market</h1>
                    <h2 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white sm:text-5xl">Live
                        Watch Catalog.</h2>
                    <p class="mt-4 text-sm font-medium text-slate-500 dark:text-zinc-400">Directly synchronized with
                        Yahoo Japan Auctions.</p>
                </div>

                <div class="mt-12">
                    <x-auction-filters :filters="$filters" :route="route('auctions.index')" />
                </div>
            </div>

            @if ($auctions->isEmpty())
                <div
                    class="mt-16 flex h-80 flex-col items-center justify-center rounded-[3rem] border-2 border-dashed border-slate-200 bg-white/50 p-20 text-center dark:border-white/5 dark:bg-black/20">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="mt-4 text-sm font-bold text-slate-400 italic">No timepieces matching those parameters
                        found in the current market pool.</p>
                </div>
            @else
                <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($auctions as $auction)
                        <div
                            class="group relative flex flex-col overflow-hidden rounded-[2.5rem] bg-white text-slate-900 shadow-2xl transition-transform duration-500 hover:-translate-y-2 dark:bg-zinc-800 dark:text-white">
                            <a href="{{ route('auctions.show', $auction) }}" class="flex-1">
                                <div class="relative aspect-[4/5] overflow-hidden">
                                    <img src="{{ $auction->thumbnail_url }}" alt=""
                                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        loading="lazy" />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    </div>
                                    <div class="absolute bottom-6 left-6 right-6">
                                        <span
                                            class="inline-flex w-full justify-center rounded-2xl bg-brand-gold py-3 text-xs font-black uppercase tracking-widest text-brand-navy opacity-0 transition-all duration-300 group-hover:opacity-100">View
                                            Logistics</span>
                                    </div>

                                    @if ($auction->status !== 'active')
                                        <div
                                            class="absolute top-4 right-4 rounded-xl bg-rose-500 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg">
                                            {{ $auction->status }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-8">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ends
                                        {{ $auction->ends_at?->diffForHumans() ?? '—' }}</p>
                                    <h4
                                        class="mt-2 truncate text-sm font-black transition-colors group-hover:text-brand-gold">
                                        {{ $auction->title }}</h4>

                                    <div
                                        class="mt-6 flex items-center justify-between border-t border-slate-50 pt-4 dark:border-white/5">
                                        <div>
                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">
                                                Position</p>
                                            <p class="text-xl font-black text-brand-navy dark:text-brand-gold">
                                                ¥{{ number_format($auction->current_bid_yen) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">
                                                Seller Hub</p>
                                            <p
                                                class="text-[10px] font-bold text-slate-900 dark:text-white truncate max-w-[80px]">
                                                {{ $auction->seller_name ?? 'Trusted' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-20">
                    {{ $auctions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
