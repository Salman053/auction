@props(['winningBids', 'lostBids', 'resultBids'])

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
