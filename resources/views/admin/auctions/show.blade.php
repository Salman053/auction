<x-admin-layout :title="$auction->title">
    {{-- ===== BREADCRUMB + BACK ===== --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.auctions.index') }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-3.5 py-1.5 text-xs font-semibold text-zinc-600 transition hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Auctions
        </a>
    </div>

    {{-- ===== HERO HEADER ===== --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-start">
            {{-- Thumbnail --}}
            <div class="shrink-0">
                @if ($auction->thumbnail_url)
                    <img src="{{ $auction->thumbnail_url }}" alt="{{ $auction->title }}"
                         class="h-40 w-40 rounded-xl object-cover ring-1 ring-black/10 dark:ring-white/10 lg:h-48 lg:w-48">
                @else
                    <div class="flex h-40 w-40 items-center justify-center rounded-xl bg-zinc-100 dark:bg-white/5 lg:h-48 lg:w-48">
                        <svg class="h-16 w-16 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-start gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white lg:text-3xl">{{ $auction->title }}</h1>
                    @php
                        $statusColors = [
                            'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                            'finished' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20 dark:bg-emerald-500/20 dark:text-emerald-300',
                            'closed' => 'bg-zinc-100 text-zinc-600 ring-zinc-500/10 dark:bg-white/5 dark:text-zinc-400',
                            'ended_no_bids' => 'bg-zinc-50 text-zinc-500 ring-zinc-500/10 dark:bg-white/5 dark:text-zinc-500',
                            'sold' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400',
                            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-500/10 dark:text-red-400',
                        ];
                        $color = $statusColors[$auction->status] ?? $statusColors['ended'];
                    @endphp
                    <span class="mt-1 inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $color }}">
                        {{ strtoupper($auction->status) }}
                    </span>

                    @if ($auction->shipment_status !== 'pending')
                        @php
                            $shipmentColors = [
                                'bidder_confirmed' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                'admin_approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                            ];
                            $sColor = $shipmentColors[$auction->shipment_status] ?? 'bg-zinc-100 text-zinc-600 ring-zinc-500/10';
                        @endphp
                        <span class="mt-1 inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $sColor }}">
                            SHIPMENT: {{ strtoupper(str_replace('_', ' ', $auction->shipment_status)) }}
                        </span>
                    @endif

                    @if ($auction->shipment_status === 'bidder_confirmed')
                        <div class="flex items-center gap-2 mt-1">
                            <form method="POST" action="{{ route('admin.auctions.approve-shipment', $auction) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-700">
                                    Approve Shipment
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.auctions.reject-shipment', $auction) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-rose-700">
                                    Reject
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-3 lg:grid-cols-4">
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Yahoo ID</span>
                        <div class="mt-0.5 font-mono font-semibold text-zinc-900 dark:text-white">{{ $auction->yahoo_auction_id }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Views</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ number_format($auction->view_count ?? 0) }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Seller</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ $auction->seller_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Seller Rating</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">
                            @if ($auction->seller_rating)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ number_format($auction->seller_rating, 1) }}
                                </span>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Condition</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ $auction->condition ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Starting Bid</span>
                        <div class="mt-0.5 font-semibold tabular-nums text-zinc-900 dark:text-white">¥{{ number_format($auction->starting_bid_yen) }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Current Bid</span>
                        <div class="mt-0.5 text-lg font-bold tabular-nums text-rose-600 dark:text-rose-400">¥{{ number_format($auction->current_bid_yen) }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Starts At</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ $auction->starts_at?->format('M d, Y H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400">Ends At</span>
                        <div class="mt-0.5 font-semibold text-zinc-900 dark:text-white">
                            @if ($auction->ends_at)
                                {{ $auction->ends_at->format('M d, Y H:i') }}
                                <div class="text-xs font-normal text-zinc-500 dark:text-zinc-400">
                                    {{ $auction->ends_at->isPast() ? 'Ended' : 'Ends' }} {{ $auction->ends_at->diffForHumans() }}
                                </div>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-xs text-zinc-400 dark:text-zinc-500">
                    Last synced {{ $auction->last_synced_at?->diffForHumans() ?? 'never' }}
                    · Created {{ $auction->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total Bids</div>
            <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-zinc-900 dark:text-white">{{ $stats['total_bids'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Unique Bidders</div>
            <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-zinc-900 dark:text-white">{{ $stats['unique_bidders'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Highest Bid</div>
            <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-rose-600 dark:text-rose-400">¥{{ number_format($stats['highest_bid']) }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Watchers</div>
            <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-zinc-900 dark:text-white">{{ $stats['watchers'] }}</div>
        </div>
    </div>

    {{-- ===== TABS SECTION ===== --}}
    <div class="mt-6" x-data="{ tab: 'bids' }">
        @php
            $resultBids = $auction->bids->filter(fn ($bid) => in_array($bid->status, ['won', 'outbid', 'lost', 'cancelled'], true));
            $winningBids = $resultBids->where('status', 'won');
            $lostBids = $resultBids->whereNotIn('status', ['won', 'active']);
        @endphp

        {{-- Tab Headers --}}
        <div class="flex gap-1 rounded-xl bg-zinc-100 p-1 dark:bg-white/5">
            <button @click="tab = 'bids'"
                    :class="tab === 'bids' ? 'bg-white shadow-sm text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all">
                Bid History
                <span class="ml-1.5 rounded-full bg-zinc-200/80 px-2 py-0.5 text-xs dark:bg-white/10">{{ $stats['total_bids'] }}</span>
            </button>
            <button @click="tab = 'results'"
                    :class="tab === 'results' ? 'bg-white shadow-sm text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all">
                Results
                <span class="ml-1.5 rounded-full bg-zinc-200/80 px-2 py-0.5 text-xs dark:bg-white/10">{{ $resultBids->count() }}</span>
            </button>
            <button @click="tab = 'watchers'"
                    :class="tab === 'watchers' ? 'bg-white shadow-sm text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all">
                Watchers
                <span class="ml-1.5 rounded-full bg-zinc-200/80 px-2 py-0.5 text-xs dark:bg-white/10">{{ $stats['watchers'] }}</span>
            </button>
            <button @click="tab = 'images'"
                    :class="tab === 'images' ? 'bg-white shadow-sm text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all">
                Images
                <span class="ml-1.5 rounded-full bg-zinc-200/80 px-2 py-0.5 text-xs dark:bg-white/10">{{ count($auction->image_urls ?? []) }}</span>
            </button>
            <button @click="tab = 'raw'"
                    :class="tab === 'raw' ? 'bg-white shadow-sm text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all">
                Raw Data
            </button>
        </div>

        {{-- Tab Components --}}
        <x-admin.auction.tab-bids :auction="$auction" :stats="$stats" />
        <x-admin.auction.tab-results :winning-bids="$winningBids" :lost-bids="$lostBids" :result-bids="$resultBids" />
        <x-admin.auction.tab-watchers :auction="$auction" />
        <x-admin.auction.tab-images :auction="$auction" />
        <x-admin.auction.tab-raw :auction="$auction" />
    </div>

    {{-- ===== Alpine.js x-cloak style ===== --}}
    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
