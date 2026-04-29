<x-user-layout :title="'Withdrawals'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Withdrawals</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Request withdrawals and track status.</p>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-brand-navy p-5 shadow-lg ring-1 ring-white/10 sm:w-64">
                <div class="absolute -right-4 -top-4 opacity-10">
                    <svg class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                </div>
                <div class="relative">
                    <div class="text-xs font-black uppercase tracking-widest text-brand-gold">Withdrawable Cash</div>
                    <div class="mt-2 text-2xl font-black text-white">¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</div>
                </div>
            </div>
        </div>

        @if (session('status') === 'withdrawal-requested')
            <div class="mt-4 rounded-2xl border border-[#1877f2]/20 bg-[#1877f2]/10 px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                Withdrawal requested. Admin approval pending.
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10 lg:p-8">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Pull Funds to Bank</h2>
            <p class="mt-1 text-xs font-bold text-slate-500">Initiate an instant simulated payout routing to your bank.</p>

            <form method="POST" action="{{ route('user.withdrawals.store') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf
                <div>
                    <label class="text-xs font-black uppercase tracking-widest text-slate-500" for="amount_yen">Payout Amount (JPY)</label>
                    <div class="relative mt-2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-lg font-black text-slate-400">¥</span>
                        </div>
                        <input id="amount_yen" name="amount_yen" type="number" min="1000" value="{{ old('amount_yen', 10000) }}" class="block w-full rounded-2xl border-0 bg-slate-50 py-4 pl-10 pr-4 text-lg font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                    </div>
                    @error('amount_yen') <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="transaction_id" class="text-xs font-black uppercase tracking-widest text-slate-500">Transaction/Reference ID</label>
                    <input id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}"
                        placeholder="REF-12345678"
                        class="mt-2 block w-full rounded-2xl border-0 bg-slate-50 py-4 px-4 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                    @error('transaction_id')
                        <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="receipt" class="text-xs font-black uppercase tracking-widest text-slate-500">Upload Receipt / Screenshot (Optional)</label>
                    <div class="mt-2 flex items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 dark:border-white/10 dark:bg-black/20">
                        <input id="receipt" name="receipt" type="file" accept="image/*,application/pdf" class="hidden" onchange="document.getElementById('receipt-name').textContent = this.files[0].name" />
                        <label for="receipt" class="cursor-pointer text-center">
                            <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span id="receipt-name" class="mt-2 block text-xs font-bold text-slate-500">Select file for upload</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-black uppercase tracking-widest text-slate-500" for="memo">Note (Optional)</label>
                    <input id="memo" name="memo" value="{{ old('memo') }}" placeholder="e.g. Withdrawal for purchase" class="mt-2 block w-full rounded-2xl border-0 bg-slate-50 py-4 px-4 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                </div>
                <button type="submit" class="w-full rounded-2xl bg-brand-navy px-8 py-4 text-sm font-black text-white shadow-lg transition hover:bg-slate-800 dark:bg-brand-gold dark:text-brand-navy dark:hover:bg-amber-400">Request Withdrawal</button>
            </form>
        </div>

        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-slate-100 p-6 dark:border-white/5 lg:px-8">
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Payout History</h2>
            </div>

            @if ($withdrawals->isEmpty())
                <div class="p-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 dark:bg-white/5">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </div>
                    <p class="mt-4 text-xs font-bold text-slate-500">No payouts logged yet.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($withdrawals as $w)
                        <div class="flex items-center justify-between gap-4 p-6 transition hover:bg-slate-50 dark:hover:bg-white/[0.02] lg:px-8">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">Payout</div>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-500">{{ $w->created_at?->format('M j, Y') }}</span>
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-white/10 dark:text-white">
                                            {{ strtoupper($w->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right text-base font-black text-slate-900 dark:text-white">
                                -¥{{ number_format((int) $w->amount_yen) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 p-6 dark:border-white/5">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-user-layout>

