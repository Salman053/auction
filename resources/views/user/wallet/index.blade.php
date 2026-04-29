<x-user-layout :title="'Wallet'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Wallet</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Deposit funds and track transactions.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="relative overflow-hidden rounded-3xl bg-brand-navy p-5 shadow-lg ring-1 ring-white/10">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <svg class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </div>
                    <div class="relative">
                        <div class="text-xs font-black uppercase tracking-widest text-brand-gold">Cash Balance</div>
                        <div class="mt-2 text-2xl font-black text-white">
                            ¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</div>
                    </div>
                </div>

                <div
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Locked Funds</div>
                    <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                        ¥{{ number_format((int) ($wallet?->locked_balance_yen ?? 0)) }}</div>
                </div>

                <div
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400">Bidding Capacity</div>
                    <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                        ¥{{ number_format($capacityYen) }}</div>
                    <div class="mt-1 text-[10px] font-bold tracking-widest text-emerald-500">{{ $multiplierPercent }}%
                        Multiplier Active</div>
                </div>

                <div class="rounded-3xl bg-brand-gold/10 p-5 shadow-sm ring-1 ring-brand-gold/20 dark:bg-brand-gold/5">
                    <div class="text-xs font-black uppercase tracking-widest text-brand-gold">Available Cap</div>
                    <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                        ¥{{ number_format($availableCapacityYen) }}</div>
                </div>
            </div>
        </div>

        @if (session('status') === 'deposit-requested')
            <div
                class="mt-4 rounded-2xl border border-[#1877f2]/20 bg-[#1877f2]/10 px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                Deposit request submitted. An admin must approve it.
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div
            class="rounded-3xl bg-white p-6 shadow-sm max-h-fit ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10 lg:p-8">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Fund Your Account</h2>
            <p class="mt-1 text-xs font-bold text-slate-500">Initiate an instant mock transfer to increase your bidding
                power.</p>

            <form id="deposit-form" method="POST" action="{{ route('user.wallet.deposits.store') }}"
                enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf

                <div>
                    <label for="amount_yen" class="text-xs font-black uppercase tracking-widest text-slate-500">Amount
                        (JPY)</label>
                    <div class="relative mt-2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-lg font-black text-slate-400">¥</span>
                        </div>
                        <input id="amount_yen" name="amount_yen" type="number" min="1000"
                            value="{{ old('amount_yen', 10000) }}"
                            class="block w-full rounded-2xl border-0 bg-slate-50 py-4 pl-10 pr-4 text-lg font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                    </div>
                    @error('amount_yen')
                        <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="provider"
                            class="text-xs font-black uppercase tracking-widest text-slate-500">Payment
                            Method</label>
                        <select id="provider" name="provider"
                            class="mt-2 block w-full rounded-2xl border-0 bg-slate-50 py-4 px-5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold">
                            <option value="bank" class="text-slate-900">Wire / Bank Transfer</option>
                            <option value="card" class="text-slate-900">Credit Card</option>
                            <option value="paypal" class="text-slate-900">PayPal</option>
                            <option value="stripe" class="text-slate-900"
                                {{ old('provider') === 'stripe' ? 'selected' : '' }}>Stripe (Online Card)</option>
                        </select>
                        @error('provider')
                            <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="transaction_id"
                            class="text-xs font-black uppercase tracking-widest text-slate-500">Transaction ID
                            (Optional)</label>
                        <input id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}"
                            placeholder="TXN-12345678"
                            class="mt-2 block w-full rounded-2xl border-0 bg-slate-50 py-4 px-4 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                        @error('transaction_id')
                            <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="receipt" class="text-xs font-black uppercase tracking-widest text-slate-500">Upload
                        Receipt (Optional)</label>
                    <div
                        class="mt-2 flex items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 dark:border-white/10 dark:bg-black/20">
                        <input id="receipt" name="receipt" type="file" accept="image/*,application/pdf"
                            class="hidden"
                            onchange="document.getElementById('receipt-name').textContent = this.files[0].name" />
                        <label for="receipt" class="cursor-pointer text-center">
                            <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span id="receipt-name" class="mt-2 block text-xs font-bold text-slate-500">Choose file or
                                drag and drop</span>
                            <span class="mt-1 block text-[10px] text-slate-400">JPG, PNG, PDF up to 5MB</span>
                        </label>
                    </div>
                    @error('receipt')
                        <div class="mt-2 text-xs font-bold text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="memo" class="text-xs font-black uppercase tracking-widest text-slate-500">Transfer
                        Note (Optional)</label>
                    <input id="memo" name="memo" value="{{ old('memo') }}"
                        placeholder="e.g. Deposit for bidding"
                        class="mt-2 block w-full rounded-2xl border-0 bg-slate-50 py-4 px-4 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-gold dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-brand-gold" />
                </div>

                <button type="button" data-confirm data-confirm-title="Confirm Deposit Request"
                    data-confirm-message="You are about to submit a deposit request for ¥{amount}. Please ensure you have transferred the funds if using manual methods."
                    data-confirm-amount-selector="#amount_yen" data-confirm-text="Submit Request"
                    data-confirm-type="info" data-confirm-on-confirm="#deposit-form"
                    class="w-full rounded-2xl bg-brand-navy px-8 py-4 text-sm font-black text-white shadow-lg transition hover:bg-slate-800 dark:bg-brand-gold dark:text-brand-navy dark:hover:bg-amber-400">
                    Process Auto-Deposit
                </button>
            </form>
        </div>

        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-slate-100 p-6 dark:border-white/5 lg:px-8">
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Ledger History</h2>
            </div>

            @if ($transactions->isEmpty())
                <div class="p-8 text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 dark:bg-white/5">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-xs font-bold text-slate-500">No transactions recorded yet.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($transactions as $tx)
                        @php
                            $isCredit = $tx->amount_yen > 0;
                            $amountColor = $isCredit ? 'text-emerald-500' : 'text-slate-900 dark:text-white';
                            $amountSign = $isCredit ? '+' : '';
                        @endphp
                        <div
                            class="flex items-center justify-between gap-4 p-6 transition hover:bg-slate-50 dark:hover:bg-white/[0.02] lg:px-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $isCredit ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400' }}">
                                    @if ($isCredit)
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">
                                        {{ ucfirst($tx->type) }}</div>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span
                                            class="text-xs font-bold text-slate-500">{{ $tx->created_at?->format('M j, Y') }}</span>
                                        <span
                                            class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-white/10 dark:text-white">
                                            {{ strtoupper($tx->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-black {{ $amountColor }}">
                                    {{ $amountSign }}¥{{ number_format((int) $tx->amount_yen) }}</div>
                                @if ($tx->provider)
                                    <div class="mt-1 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $tx->provider }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 p-6 dark:border-white/5">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-user-layout>
