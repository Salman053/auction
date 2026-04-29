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
                            'ended' => 'bg-zinc-100 text-zinc-600 ring-zinc-500/10 dark:bg-white/5 dark:text-zinc-400',
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

        {{-- Tab: Bid History --}}
        <div x-show="tab === 'bids'" x-cloak class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-black/5 bg-zinc-50/50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Bidder</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3 text-right">Max Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Via</th>
                            <th class="px-5 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/10">
                        @forelse ($auction->bids as $index => $bid)
                            <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-white/5 {{ $index === 0 ? 'bg-amber-50/30 dark:bg-amber-500/5' : '' }}">
                                <td class="px-5 py-3.5 text-zinc-400">
                                    @if ($index === 0)
                                        <span class="inline-flex items-center justify-center rounded-full bg-amber-100 p-1 dark:bg-amber-500/20">
                                            <svg class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </span>
                                    @else
                                        {{ $stats['total_bids'] - $index }}
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                            {{ strtoupper(substr($bid->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $bid->user->name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $bid->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</td>
                                <td class="px-5 py-3.5 text-right tabular-nums text-zinc-500 dark:text-zinc-400">
                                    {{ $bid->max_amount_yen ? '¥'.number_format($bid->max_amount_yen) : '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $bidStatusColors = [
                                            'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'won' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                            'outbid' => 'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400',
                                            'cancelled' => 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400',
                                        ];
                                        $bidColor = $bidStatusColors[$bid->status] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400';
                                    @endphp
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $bidColor }}">{{ ucfirst($bid->status) }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/5 dark:text-zinc-400">{{ $bid->placed_via }}</span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $bid->created_at->format('M d, H:i:s') }}
                                    <div class="text-zinc-400 dark:text-zinc-500">{{ $bid->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No bids have been placed on this auction.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Results --}}
        <div x-show="tab === 'results'" x-cloak class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-black/5 bg-zinc-50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Win / Loss Summary</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Separate view of winning and lost bids for this auction.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <strong class="font-semibold text-zinc-900 dark:text-white">Won</strong> {{ $winningBids->count() }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-zinc-100 px-3 py-1 text-zinc-700 dark:bg-white/5 dark:text-zinc-300">
                            <strong class="font-semibold text-zinc-900 dark:text-white">Lost</strong> {{ $lostBids->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-black/5 bg-zinc-50/50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Bidder</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/10">
                        @forelse ($resultBids as $index => $bid)
                            <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-white/5 {{ $bid->status === 'won' ? 'bg-emerald-50/20 dark:bg-emerald-500/10' : '' }}">
                                <td class="px-5 py-3.5 text-zinc-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                            {{ strtoupper(substr($bid->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $bid->user->name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $bid->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $resultStatusColors = [
                                            'won' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'outbid' => 'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400',
                                            'lost' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                            'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
                                        ];
                                        $resultColor = $resultStatusColors[$bid->status] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400';
                                    @endphp
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $resultColor }}">{{ ucfirst($bid->status) }}</span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $bid->created_at->format('M d, H:i:s') }}
                                    <div class="text-zinc-400 dark:text-zinc-500">{{ $bid->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No winning or lost bids yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Watchers --}}
        <div x-show="tab === 'watchers'" x-cloak class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-black/5 bg-zinc-50/50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3">Added</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/10">
                        @forelse ($auction->watchlistItems as $item)
                            <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-white/5">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                            {{ strtoupper(substr($item->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $item->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">{{ $item->user->email }}</td>
                                <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400">{{ $item->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No users are watching this auction.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Images --}}
        <div x-show="tab === 'images'" x-cloak class="mt-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            @if (!empty($auction->image_urls) && count($auction->image_urls) > 0)
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($auction->image_urls as $idx => $imageUrl)
                        <a href="{{ $imageUrl }}" target="_blank" rel="noopener"
                           class="group relative overflow-hidden rounded-xl ring-1 ring-black/10 transition hover:ring-rose-500 dark:ring-white/10 dark:hover:ring-rose-500">
                            <img src="{{ $imageUrl }}" alt="Image {{ $idx + 1 }}"
                                 class="aspect-square w-full object-cover transition group-hover:scale-105">
                            <div class="absolute inset-0 flex items-end justify-center bg-gradient-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100">
                                <span class="mb-3 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                                    Open full size ↗
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center gap-2 py-10">
                    <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No images available for this auction.</p>
                </div>
            @endif
        </div>

        {{-- Tab: Raw JSON Data --}}
        <div x-show="tab === 'raw'" x-cloak class="mt-4 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Raw Scraped Data (JSON)</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">The original data received from the Yahoo Japan scraper.</p>
            </div>
            <div class="p-4">
                @if ($auction->raw)
                    <pre class="max-h-[500px] overflow-auto rounded-xl bg-zinc-950 p-4 text-xs leading-relaxed text-emerald-400 font-mono dark:bg-black">{{ json_encode($auction->raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="flex flex-col items-center gap-2 py-10">
                        <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No raw data stored for this auction.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Alpine.js x-cloak style ===== --}}
    <style>[x-cloak] { display: none !important; }</style>
</x-admin-layout>
