<x-user-layout :title="'Financial Hub'">
    <div class="mx-auto px-4 py-8 space-y-12">

        {{-- Header & Core Stats --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black tracking-tighter text-zinc-900 dark:text-white uppercase">Financial Hub
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage your bidding liquidity and track chronological
                    activity.</p>
                <div class="pt-4">
                    <a href="{{ route('user.withdrawals.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-zinc-100 dark:bg-white/5 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-400 transition hover:bg-zinc-200 dark:hover:bg-white/10 shadow-sm border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                        Request Liquidation
                    </a>s
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full lg:w-auto">
                {{-- Stat Cards --}}
                <div class="rounded-lg bg-blue-600 p-6 shadow-xl shadow-blue-600/20 text-white">
                    <div class="text-[10px] font-black uppercase tracking-widest text-blue-100/60 mb-2">Available</div>
                    <div class="text-2xl font-black">¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</div>
                </div>

                <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-2">
                        Committed</div>
                    <div class="text-2xl font-black text-zinc-900 dark:text-white">
                        ¥{{ number_format((int) ($wallet?->locked_balance_yen ?? 0)) }}</div>
                </div>

                <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-2">
                        Total Power</div>
                    <div class="text-2xl font-black text-zinc-900 dark:text-white">¥{{ number_format($capacityYen) }}
                    </div>
                    <div class="mt-1 text-[9px] font-black uppercase tracking-widest text-emerald-500">
                        {{ $multiplierPercent }}% Factor</div>
                </div>

                <div class="rounded-lg bg-zinc-950 p-6 shadow-2xl">
                    <div class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-2">Limit</div>
                    <div class="text-2xl font-black text-white">¥{{ number_format($availableCapacityYen) }}</div>
                </div>
            </div>
        </div>

        @if (session('status') === 'deposit-requested')
            <div
                class="rounded-lg border border-blue-600/20 bg-blue-600/5 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-blue-600 animate-pulse text-center">
                Transmission Received: Awaiting Administrative Verification
            </div>
        @endif

        {{-- Main Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

            {{-- Deposit Form --}}
            <div class="lg:col-span-5 xl:col-span-4">
                <div
                    class="rounded-[2.5rem] bg-slate-100 dark:bg-gray-800 p-8 dark:text-white text-black shadow-2xl relative overflow-hidden">
                    <div class="relative z-10 space-y-8">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight">Fund Account</h2>
                            <p
                                class="mt-1 text-[10px] font-black uppercase tracking-widest dark:text-white/50 text-black/50">
                                Secure
                                liquidity injection</p>
                        </div>

                        <form id="deposit-form" method="POST" action="{{ route('user.wallet.deposits.store') }}"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <label for="amount_yen"
                                    class="block text-[9px] font-black uppercase tracking-widest dark:text-white/50 text-black/50 mb-3 ml-1">Target
                                    Amount (JPY)</label>
                                <div
                                    class="relative flex items-center dark:bg-gray-800 bg-white rounded-lg border border-white/10 p-2">
                                    <span class="pl-4 text-2xl font-black text-blue-600">¥</span>
                                    <input id="amount_yen" name="amount_yen" type="number" min="1000"
                                        value="{{ old('amount_yen', 10000) }}"
                                        class="w-full bg-transparent border-none text-center text-3xl font-black focus:ring-0" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="provider"
                                        class="block text-[9px] font-black uppercase tracking-widest dark:text-white/50 text-black/50 mb-3 ml-1">Transmission
                                        Method</label>
                                    <select id="provider" name="provider"
                                        class="w-full rounded-lg border-none bg-white dark:bg-gray-800 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-black dark:text-white">
                                        <option value="bank">Wire / Bank Transfer</option>
                                        <option value="card">Credit Card</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="stripe" {{ old('provider') === 'stripe' ? 'selected' : '' }}>
                                            Stripe (Online Card)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="transaction_id"
                                        class="block text-[9px] font-black uppercase tracking-widest dark:text-white/50 text-black/50 mb-3 ml-1 ">Reference
                                        ID (Optional)</label>
                                    <input id="transaction_id" required name="transaction_id"
                                        value="{{ old('transaction_id') }}" placeholder="TXN-ID-XXXX"
                                        class="w-full rounded-lg border-none  bg-white dark:bg-gray-800 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-black dark:text-white " />
                                </div>
                            </div>

                            {{-- Receipt Upload --}}
                            <div>
                                <label
                                    class="block text-[9px] font-black uppercase tracking-widest dark:text-white/50 text-black/50 mb-3 ml-1">Verification
                                    Receipt</label>
                                <div class="relative group/upload">
                                    <input id="receipt" required name="receipt" type="file"
                                        accept="image/*,application/pdf" class="hidden"
                                        onchange="document.getElementById('receipt-name').textContent = this.files[0].name" />
                                    <label for="receipt"
                                        class="flex flex-col items-center justify-center gap-3 w-full py-8 rounded-lg border-2 border-dashed dark:border-white/20 border-black/20 bg-black/5 cursor-pointer hover:border-blue-600 transition-all">
                                        <svg class="h-6 w-6 text-black/40 dark:text-white/40 group-hover/upload:text-blue-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span id="receipt-name"
                                            class="text-[10px] font-black uppercase tracking-widest text-black/50 dark:text-white/50">Upload
                                            Documents</span>
                                    </label>
                                </div>
                            </div>

                            <button type="button" data-confirm data-confirm-title="Authorize Deposit"
                                data-confirm-message="Initialize transmission of ¥{amount} to your bidding wallet?"
                                data-confirm-amount-selector="#amount_yen" data-confirm-text="Authorize"
                                data-confirm-type="info" data-confirm-on-confirm="#deposit-form"
                                class="w-full rounded-lg bg-blue-600 px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-blue-600/30 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                                Process Bidding Power
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Ledger --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <div
                    class="rounded-[2.5rem] bg-white dark:bg-zinc-800  shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between">
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-900 dark:text-white">
                            Ledger History</h2>
                        <span
                            class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Chronological</span>
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="p-20 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">No Records Found
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-zinc-100 dark:text-white dark:divide-white/5">
                            @foreach ($transactions as $tx)
                                @php $isCredit = $tx->amount_yen > 0; @endphp
                                <div
                                    class="flex items-center justify-between gap-4 p-8 transition hover:bg-zinc-50 dark:hover:bg-white/[0.02]">
                                    <div class="flex items-center gap-6">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-lg {{ $isCredit ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10' : 'bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-400' }}">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5"
                                                    d="{{ $isCredit ? 'M19 14l-7 7m0 0l-7-7m7 7V3' : 'M5 10l7-7m0 0l7 7m-7-7v18' }}" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div
                                                class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ ucfirst($tx->type) }}</div>

                                            <div
                                                class="mt-1 text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                                {{ $tx->created_at?->format('M j, Y') }}</div>

                                            <div
                                                class="text-sm font-black text-zinc-500 dark:text-white uppercase tracking-tight">
                                                Status :&nbsp; {{ ucfirst($tx->status) }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-xl font-black {{ $isCredit ? 'text-emerald-500' : 'text-zinc-900 dark:text-white' }} tracking-tighter">
                                            {{ $isCredit ? '+' : '' }}¥{{ number_format((int) $tx->amount_yen) }}
                                        </div>
                                        @if ($tx->provider)
                                            <div
                                                class="mt-1 text-[8px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                                {{ $tx->provider }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t border-zinc-100 p-8 dark:border-white/5 bg-zinc-50/30">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
