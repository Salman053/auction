<x-admin-layout :title="'Explore User: ' . $user->name" :back-url="route('admin.users.index')">
    <div class="mb-6">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-navy text-brand-gold">
                        <span class="text-xl font-black uppercase">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $user->name }}
                        </h1>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $user->email }} · Member
                            since {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-zinc-200 bg-white px-5 py-2.5 text-xs font-black uppercase tracking-widest transition hover:bg-zinc-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10 {{ $user->suspended_at ? 'text-green-600' : 'text-rose-600' }}">
                            {{ $user->suspended_at ? 'Unsuspend' : 'Suspend' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Stats Cards --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Wallet Summary --}}
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Wallet Summary</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500">Total Balance</span>
                        <span
                            class="text-lg font-black text-zinc-900 dark:text-white">¥{{ number_format($user->wallet?->balance_yen ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500">Locked for Bids</span>
                        <span
                            class="text-sm font-bold text-brand-gold">¥{{ number_format($user->wallet?->locked_balance_yen ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500">Available</span>
                        <span
                            class="text-sm font-bold text-green-500">¥{{ number_format(($user->wallet?->balance_yen ?? 0) - ($user->wallet?->locked_balance_yen ?? 0)) }}</span>
                    </div>
                </div>
            </div>

            {{-- Bidding Config --}}
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Bidding Configuration</h3>
                <form method="POST" action="{{ route('admin.users.multiplier', $user) }}" class="mt-4">
                    @csrf
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Multiplier (%)</label>
                    <div class="mt-1 flex gap-2">
                        <input type="number" name="bidding_multiplier_percent"
                            value="{{ $user->bidding_multiplier_percent ?? 500 }}"
                            class="flex-1 rounded-lg border-zinc-200 bg-zinc-50 px-4 py-2 text-sm focus:border-brand-navy focus:ring-brand-navy dark:border-white/10 dark:bg-white/5">
                        <button type="submit"
                            class="rounded-lg bg-brand-navy px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition hover:scale-105 active:scale-95">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            {{-- Platform Activity --}}
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Platform Activity</h3>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="rounded-lg bg-zinc-50 p-4 dark:bg-white/5">
                        <span
                            class="block text-xl font-black text-zinc-900 dark:text-white">{{ $user->bids->count() }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Bids</span>
                    </div>
                    <div class="rounded-lg bg-zinc-50 p-4 dark:bg-white/5">
                        <span
                            class="block text-xl font-black text-zinc-900 dark:text-white">{{ $user->watchlistItems->count() }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Watched</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tables Section --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Recent Bids --}}
            <div
                class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <div class="border-b border-black/5 bg-zinc-50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Recent
                        Bidding History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-zinc-50/50 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:bg-white/2">
                            <tr>
                                <th class="px-6 py-3">Auction</th>
                                <th class="px-6 py-3">Bid Amount</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/10">
                            @forelse($bids as $bid)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.auctions.show', $bid->auction) }}"
                                            class="font-bold text-brand-navy hover:underline dark:text-brand-gold">
                                            {{ Str::limit($bid->auction->title, 40) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 font-black text-zinc-900 dark:text-white">
                                        ¥{{ number_format($bid->amount_yen) }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest 
                                            @if ($bid->status === 'active') bg-green-100 text-green-700 @elseif($bid->status === 'outbid') bg-zinc-100 text-zinc-500 @else bg-brand-gold/10 text-brand-gold @endif">
                                            {{ $bid->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-500">
                                        {{ $bid->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-zinc-500 italic">No bidding
                                        history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($bids->hasPages())
                    <div class="p-4 border-t border-black/5 dark:border-white/10">
                        {{ $bids->appends(['transactions_page' => $transactions->currentPage()])->links() }}
                    </div>
                @endif
            </div>

            {{-- Recent Transactions --}}
            <div
                class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <div class="border-b border-black/5 bg-zinc-50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Wallet
                        Transactions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-zinc-50/50 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:bg-white/2">
                            <tr>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/10">
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">
                                        {{ ucwords(str_replace('_', ' ', (string) $tx->type)) }}</td>
                                    <td
                                        class="px-6 py-4 font-black {{ $tx->amount_yen >= 0 ? 'text-green-600' : 'text-rose-600' }}">
                                        {{ $tx->amount_yen >= 0 ? '+' : '' }}¥{{ number_format($tx->amount_yen) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest 
                                            @if ($tx->status === 'completed') bg-green-100 text-green-700 @elseif($tx->status === 'pending') bg-brand-gold/10 text-brand-gold @else bg-rose-100 text-rose-700 @endif">
                                            {{ $tx->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-500">
                                        {{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-zinc-500 italic">No
                                        transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-black/5 dark:border-white/10">
                        {{ $transactions->appends(['bids_page' => $bids->currentPage()])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
