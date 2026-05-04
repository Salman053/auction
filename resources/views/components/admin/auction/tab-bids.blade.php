@props(['auction', 'stats'])

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
