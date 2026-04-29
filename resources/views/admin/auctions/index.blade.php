<x-admin-layout :title="'Market Monitoring'">
    <div class="mb-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase italic">Market Monitoring</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Advanced oversight of synced Yahoo Japan auctions and bidding activity.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2 p-1 bg-zinc-100 dark:bg-white/5 rounded-xl w-fit">
                <a href="{{ route('admin.auctions.index', ['tab' => 'all']) }}" 
                   class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition {{ $currentTab === 'all' ? 'bg-white text-rose-600 shadow-sm dark:bg-zinc-800 dark:text-rose-400' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    All
                </a>
                <a href="{{ route('admin.auctions.index', ['tab' => 'active']) }}" 
                   class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition {{ $currentTab === 'active' ? 'bg-white text-rose-600 shadow-sm dark:bg-zinc-800 dark:text-rose-400' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Active
                </a>
                <a href="{{ route('admin.auctions.index', ['tab' => 'won']) }}" 
                   class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition {{ $currentTab === 'won' ? 'bg-white text-rose-600 shadow-sm dark:bg-zinc-800 dark:text-rose-400' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Winning Bids
                </a>
                <a href="{{ route('admin.auctions.index', ['tab' => 'shipment_pending']) }}" 
                   class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition {{ $currentTab === 'shipment_pending' ? 'bg-white text-rose-600 shadow-sm dark:bg-zinc-800 dark:text-rose-400' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Shipment Pending
                </a>
            </div>
        </div>
    </div>

    {{-- Top 3 Rankings --}}
    @if($topBids->isNotEmpty() && $currentTab === 'all')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($topBids as $index => $topAuction)
                <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 group transition hover:shadow-xl">
                    <div class="absolute -right-4 -top-4 flex h-16 w-16 items-center justify-center rounded-full bg-rose-600/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 font-black italic text-2xl group-hover:scale-110 transition">
                        #{{ $index + 1 }}
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 shrink-0 rounded-xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 bg-zinc-50 dark:bg-black/20">
                            <img src="{{ $topAuction->thumbnail_url }}" alt="" class="h-full w-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">Most Bid Piece</h3>
                            <p class="text-sm font-bold text-zinc-900 dark:text-white truncate leading-tight group-hover:text-rose-600 dark:group-hover:text-rose-400 transition">
                                {{ $topAuction->title }}
                            </p>
                            <div class="mt-1 text-xs font-black text-rose-600 dark:text-rose-400">
                                {{ $topAuction->bid_count }} BIDS REGISTERED
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mb-8">
        <x-auction-filters :filters="$filters" :route="route('admin.auctions.index')" />
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-black/5 bg-zinc-50/50 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/2">
                        <th class="px-6 py-4">Auction Information</th>
                        <th class="px-6 py-4 text-center">Engagement</th>
                        <th class="px-6 py-4">Status & Logistics</th>
                        <th class="px-6 py-4 text-right">Valuation</th>
                        <th class="px-6 py-4">Timeline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/10">
                    @forelse ($auctions as $auction)
                        <tr class="group cursor-pointer transition hover:bg-zinc-50 dark:hover:bg-white/5" 
                            onclick="window.location='{{ route('admin.auctions.show', $auction) }}'">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="relative h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-zinc-100 ring-1 ring-black/5 dark:bg-black/20 dark:ring-white/10">
                                        <img src="{{ $auction->thumbnail_url }}" alt="" class="h-full w-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end justify-center pb-1">
                                            <span class="text-[8px] font-black text-white uppercase tracking-tighter">View</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-zinc-900 group-hover:text-rose-600 dark:text-white dark:group-hover:text-rose-400 transition truncate max-w-[300px]">
                                            {{ $auction->title }}
                                        </div>
                                        <div class="mt-0.5 flex items-center gap-2">
                                            <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ $auction->yahoo_auction_id }}</span>
                                            <span class="h-1 w-1 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                                            <span class="text-[10px] font-medium text-rose-500/70">{{ $auction->seller_name ?? 'Anonymous Seller' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-zinc-900 dark:text-white">{{ $auction->bids_count }}</span>
                                            <span class="text-[8px] font-black uppercase tracking-tighter text-zinc-400">Bids</span>
                                        </div>
                                        <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-zinc-900 dark:text-white">{{ $auction->watchlist_items_count }}</span>
                                            <span class="text-[8px] font-black uppercase tracking-tighter text-zinc-400">Watch</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'ended' => 'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400',
                                                'sold' => 'bg-brand-gold/10 text-brand-gold',
                                            ];
                                            $color = $statusColors[$auction->status] ?? $statusColors['ended'];
                                        @endphp
                                        <span class="inline-flex rounded-lg px-2 py-0.5 text-[10px] font-black uppercase tracking-widest {{ $color }}">
                                            {{ $auction->status }}
                                        </span>
                                        
                                        @if($auction->shipment_status === 'bidder_confirmed')
                                            <span class="inline-flex rounded-lg bg-amber-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 animate-pulse">
                                                Ready to Ship
                                            </span>
                                        @elseif($auction->shipment_status === 'admin_approved')
                                            <span class="inline-flex rounded-lg bg-emerald-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                Approved
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="text-xs font-black text-zinc-900 dark:text-white leading-tight">¥{{ number_format($auction->current_bid_yen) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Current Valuation</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-xs font-bold {{ $auction->ends_at?->isPast() ? 'text-zinc-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $auction->ends_at?->format('M d, H:i') ?? '—' }}
                                </div>
                                <div class="text-[9px] font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-tighter">
                                    {{ $auction->ends_at?->diffForHumans() ?? 'Scheduled sync' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center dark:bg-white/5">
                                        <svg class="h-8 w-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div class="max-w-xs">
                                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">No auctions found</h3>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">There are no auctions matching your criteria in the {{ $currentTab }} tab.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-black/5 bg-zinc-50/50 p-6 dark:border-white/10 dark:bg-white/2">
            {{ $auctions->links() }}
        </div>
    </div>
</x-admin-layout>
