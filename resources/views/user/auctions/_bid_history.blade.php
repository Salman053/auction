@forelse ($bids as $bid)
    <div class="flex items-center justify-between gap-4 px-8 py-5 transition hover:bg-zinc-50 dark:hover:bg-white/5">
        <div class="flex items-center gap-4">
            <div
                class="h-2 w-2 rounded-full @if ($bid->status === 'active') bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] @elseif($bid->status === 'outbid') bg-zinc-300 @elseif($bid->status === 'superseded') bg-brand-gold/40 @else bg-red-500 @endif">
            </div>
            <div>
                <p class="text-sm font-bold text-zinc-900 dark:text-white">
                    @if ($bid->user_id == auth('user')->id())
                        <span class="text-brand-gold">You</span>
                    @else
                        @if ($bid->user)
                            {{ $bid->user->name ?? 'Collector #' . substr($bid->user->id, 0, 4) }}
                        @else
                            System
                        @endif
                    @endif
                </p>
                <p class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">
                    {{ $bid->status === 'superseded' ? 'Proxy Increase' : $bid->status }} ·
                    {{ $bid->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-lg font-black text-zinc-900 dark:text-white">
                ¥{{ number_format($bid->amount_yen) }}
            </p>
            @if ($bid->user_id == auth('user')->id() && $bid->max_amount_yen > $bid->amount_yen)
                <p class="text-[9px] font-bold uppercase tracking-widest text-brand-gold">
                    Max: ¥{{ number_format($bid->max_amount_yen) }}
                </p>
            @endif
        </div>
    </div>
@empty
    <div class="p-12 text-center text-sm text-zinc-500 italic">
        No bids have been placed yet. Be the first to bid!
    </div>
@endforelse
