<x-user-layout :title="'My Bids'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">My Bids</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track and manage your active and historical timepiece bids.</p>
            </div>
            
            <div class="flex items-center gap-2 p-1 bg-zinc-100 dark:bg-white/5 rounded-xl w-fit">
                <a href="{{ route('user.bids.index', ['status' => 'all']) }}" 
                   class="px-4 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition {{ $currentStatus === 'all' ? 'bg-white text-brand-navy shadow-sm dark:bg-zinc-800 dark:text-brand-gold' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('user.bids.index', ['status' => 'won']) }}" 
                   class="px-4 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition {{ $currentStatus === 'won' ? 'bg-white text-brand-navy shadow-sm dark:bg-zinc-800 dark:text-brand-gold' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Won ({{ $counts['won'] }})
                </a>
                <a href="{{ route('user.bids.index', ['status' => 'lost']) }}" 
                   class="px-4 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition {{ $currentStatus === 'lost' ? 'bg-white text-brand-navy shadow-sm dark:bg-zinc-800 dark:text-brand-gold' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Lost ({{ $counts['lost'] }})
                </a>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
        @if ($bids->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-zinc-50 dark:bg-white/5">
                    <svg class="h-8 w-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-bold text-zinc-900 dark:text-white">No bids found</h3>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">You don't have any bids in this category yet.</p>
                <a href="{{ route('auctions.index') }}" class="mt-6 text-xs font-black uppercase tracking-widest text-brand-gold hover:underline">Browse Auctions</a>
            </div>
        @else
            <div class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($bids as $bid)
                    @php
                        $canCancel = $bid->status === 'active' && $bid->created_at && $bid->created_at->greaterThanOrEqualTo(now()->subHours(1));
                        $isWon = $bid->status === 'won';
                        $isOutbid = $bid->status === 'outbid';
                        $isActive = $bid->status === 'active';
                    @endphp

                    <div class="group px-6 py-5 transition hover:bg-zinc-50 dark:hover:bg-white/5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                @if($bid->auction?->image_url)
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-zinc-100 dark:bg-black/20">
                                        <img src="{{ $bid->auction->image_url }}" alt="" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-black/20 dark:text-zinc-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="min-w-0">
                                    <a href="{{ route('user.auctions.show', $bid->auction) }}" class="block text-sm font-bold text-zinc-900 dark:text-white hover:text-brand-gold transition truncate leading-tight">
                                        {{ $bid->auction?->title }}
                                    </a>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-black uppercase tracking-widest',
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' => $isWon,
                                            'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' => $isOutbid,
                                            'bg-brand-gold/10 text-brand-gold' => $isActive,
                                            'bg-zinc-100 text-zinc-600 dark:bg-white/5 dark:text-zinc-400' => !$isWon && !$isOutbid && !$isActive,
                                        ])>
                                            {{ $bid->status }}
                                        </span>
                                        <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500">
                                            {{ $bid->created_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-6">
                                <div class="text-right">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-0.5">Bid Amount</div>
                                    <div class="text-sm font-black text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</div>
                                </div>

                                @if ($canCancel)
                                    <form id="cancel-bid-{{ $bid->id }}" method="POST" action="{{ route('user.bids.cancel', $bid) }}">
                                        @csrf
                                        <input id="bid-amount-{{ $bid->id }}" type="hidden" value="{{ $bid->amount_yen }}">
                                        <button
                                            type="button"
                                            data-confirm
                                            data-confirm-title="Cancel Bid"
                                            data-confirm-message="Cancel your bid of ¥{amount} for {{ $bid->auction?->title }}? This will immediately release your locked funds."
                                            data-confirm-amount-selector="#bid-amount-{{ $bid->id }}"
                                            data-confirm-text="Cancel Bid"
                                            data-confirm-cancel-text="Keep Bid"
                                            data-confirm-type="danger"
                                            data-confirm-on-confirm="#cancel-bid-{{ $bid->id }}"
                                            class="rounded-xl bg-rose-50 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20"
                                        >
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('user.auctions.show', $bid->auction) }}" class="p-2 text-zinc-400 hover:text-brand-gold transition dark:text-zinc-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($bids->hasPages())
                <div class="border-t border-black/5 bg-zinc-50 p-6 dark:border-white/10 dark:bg-white/2">
                    {{ $bids->links() }}
                </div>
            @endif
        @endif
    </div>
</x-user-layout>
