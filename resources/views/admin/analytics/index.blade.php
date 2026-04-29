<x-admin-layout :title="'Analytics'">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ number_format($total_users) }}</div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active Auctions</div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ number_format($active_auctions) }}</div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Bids Placed</div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ number_format($total_bids) }}</div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Wallet Assets</div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">¥{{ number_format($total_wallet_balance) }}</div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="border-b border-black/5 px-6 py-4 dark:border-white/10">
            <h2 class="text-lg font-semibold">Recent Bidding Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-black/5 bg-zinc-50/50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Auction</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/10">
                    @forelse ($recent_bids as $bid)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $bid->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate font-medium text-zinc-900 dark:text-white">{{ $bid->auction->title }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $bid->auction->yahoo_auction_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</span>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
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

