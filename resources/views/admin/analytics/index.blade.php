<x-admin-layout :title="'Analytics'">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-600/5 group-hover:scale-110 transition-transform"></div>
            <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Market Users</div>
            <div class="mt-4 text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ number_format($total_users) }}</div>
        </div>
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-600/5 group-hover:scale-110 transition-transform"></div>
            <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Live Auctions</div>
            <div class="mt-4 text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ number_format($active_auctions) }}</div>
        </div>
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-amber-600/5 group-hover:scale-110 transition-transform"></div>
            <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Bidding Velocity</div>
            <div class="mt-4 text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ number_format($total_bids) }}</div>
        </div>
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-600/5 group-hover:scale-110 transition-transform"></div>
            <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Network Liquidity</div>
            <div class="mt-4 text-3xl font-black tracking-tight text-blue-600 dark:text-blue-400">¥{{ number_format($total_wallet_balance) }}</div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
            <h2 class="text-lg font-semibold">Recent Bidding Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-black/5 bg-zinc-50/50 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/2 dark:text-zinc-400">
                        <th class="px-6 py-4">Participant</th>
                        <th class="px-6 py-4">Auction Asset</th>
                        <th class="px-6 py-4 text-right">Commitment</th>
                        <th class="px-6 py-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/10">
                    @forelse ($recent_bids as $bid)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $bid->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate font-bold text-zinc-900 dark:text-white">{{ $bid->auction->title }}</div>
                                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ $bid->auction->yahoo_auction_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-zinc-500 dark:text-zinc-400">
                                {{ $bid->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">No recent bids found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>

