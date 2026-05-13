<x-user-layout :title="'Financial Hub'">
    <div class="max-w-[1400px] mx-auto px-4 py-8 space-y-12">
        
        {{-- Header & Core Stats --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">Financial Hub</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage your bidding liquidity and track chronological activity.</p>
                <div class="pt-4">
                     <a href="{{ route('user.withdrawals.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-zinc-100 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-zinc-600 transition hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10 shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                        Request Liquidation
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full lg:w-auto">
                <div class="relative overflow-hidden rounded-[2rem] bg-blue-600 p-6 shadow-2xl shadow-blue-600/20 min-w-[180px]">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <svg class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </div>
                    <div class="relative">
                        <div class="text-[10px] font-black uppercase tracking-widest text-blue-100/60 mb-2">Available Cash</div>
                        <div class="text-2xl font-black text-white">¥{{ number_format((int) ($wallet?->balance_yen ?? 0)) }}</div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white dark:bg-zinc-900 p-6 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 min-w-[180px]">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Committed</div>
                    <div class="text-2xl font-black text-zinc-900 dark:text-white">¥{{ number_format((int) ($wallet?->locked_balance_yen ?? 0)) }}</div>
                </div>

                <div class="rounded-[2rem] bg-white dark:bg-zinc-900 p-6 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 min-w-[180px]">
                    <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Total Power</div>
                    <div class="text-2xl font-black text-zinc-900 dark:text-white">¥{{ number_format($capacityYen) }}</div>
                    <div class="mt-1 text-[9px] font-black uppercase tracking-widest text-emerald-500">{{ $multiplierPercent }}% Factor</div>
                </div>

                <div class="rounded-[2rem] bg-zinc-950 p-6 shadow-2xl min-w-[180px]">
                    <div class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-2">Bidding Limit</div>
                    <div class="text-2xl font-black text-white">¥{{ number_format($availableCapacityYen) }}</div>
                </div>
            </div>
        </div>

        @if (session('status') === 'deposit-requested')
            <div class="rounded-2xl border border-blue-600/20 bg-blue-600/5 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-blue-600 animate-pulse text-center">
                Transmission Received: Awaiting Administrative Verification
            </div>
        @endif

        {{-- Main Hub Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: Deposit Form --}}
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="rounded-[2.5rem] bg-zinc-950 p-8 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-blue-600/10 blur-3xl transition-all group-hover:scale-150"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight">Fund Account</h2>
                            <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-white/40">Secure liquidity injection</p>
                        </div>

                        <form id="deposit-form" method="POST" action="{{ route('user.wallet.deposits.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div>
                                <label for="amount_yen" class="block text-[9px] font-black uppercase tracking-widest text-white/40 mb-3 ml-1">Target Amount (JPY)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                                        <span class="text-2xl font-black text-blue-600">¥</span>
                                    </div>
                                    <input id="amount_yen" name="amount_yen" type="number" min="1000"
                                        value="{{ old('amount_yen', 10000) }}"
                                        class="w-full rounded-2xl border-none bg-white/5 pl-12 pr-6 py-5 text-3xl font-black text-white focus:ring-2 focus:ring-blue-600 transition-all" />
                                </div>
                                @error('amount_yen')
                                    <p class="mt-2 text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="provider" class="block text-[9px] font-black uppercase tracking-widest text-white/40 mb-3 ml-1">Transmission Method</label>
                                    <select id="provider" name="provider"
                                        class="w-full rounded-2xl border-none bg-white/5 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-white focus:ring-2 focus:ring-blue-600 appearance-none">
                                        <option value="bank" class="bg-zinc-900">Wire / Bank Transfer</option>
                                        <option value="card" class="bg-zinc-900">Credit Card</option>
                                        <option value="paypal" class="bg-zinc-900">PayPal</option>
                                        <option value="stripe" class="bg-zinc-900" {{ old('provider') === 'stripe' ? 'selected' : '' }}>Stripe (Online Card)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="transaction_id" class="block text-[9px] font-black uppercase tracking-widest text-white/40 mb-3 ml-1">Reference ID (Optional)</label>
                                    <input id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}"
                                        placeholder="TXN-ID-XXXX"
                                        class="w-full rounded-2xl border-none bg-white/5 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-white focus:ring-2 focus:ring-blue-600 placeholder:text-white/10" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[9px] font-black uppercase tracking-widest text-white/40 mb-3 ml-1">Verification Receipt</label>
                                <div class="relative group/upload">
                                    <input id="receipt" name="receipt" type="file" accept="image/*,application/pdf" class="hidden"
                                        onchange="document.getElementById('receipt-name').textContent = this.files[0].name" />
                                    <label for="receipt" class="flex flex-col items-center justify-center gap-3 w-full py-8 rounded-2xl border-2 border-dashed border-white/10 bg-white/5 cursor-pointer group-hover/upload:border-blue-600 transition-all">
                                        <svg class="h-6 w-6 text-white/20 group-hover/upload:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span id="receipt-name" class="text-[10px] font-black uppercase tracking-widest text-white/40 group-hover/upload:text-white">Upload Documents</span>
                                    </label>
                                </div>
                            </div>

                            <button type="button" data-confirm data-confirm-title="Authorize Deposit"
                                data-confirm-message="Initialize transmission of ¥{amount} to your bidding wallet?"
                                data-confirm-amount-selector="#amount_yen" data-confirm-text="Authorize"
                                data-confirm-type="info" data-confirm-on-confirm="#deposit-form"
                                class="w-full rounded-2xl bg-blue-600 px-8 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-blue-600/30 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                                Process Bidding Power
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: History --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <div class="rounded-[2.5rem] bg-white dark:bg-zinc-900 shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 overflow-hidden">
                    <div class="px-8 py-6 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400">Ledger History</h2>
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Chronological</span>
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="p-20 text-center space-y-4">
                            <div class="h-16 w-16 bg-zinc-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto">
                                <svg class="h-8 w-8 text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">No Historical Records Found</p>
                        </div>
                    @else
                        <div class="divide-y divide-zinc-100 dark:divide-white/5">
                            @foreach ($transactions as $tx)
                                @php
                                    $isCredit = $tx->amount_yen > 0;
                                @endphp
                                <div class="flex items-center justify-between gap-4 p-8 transition hover:bg-zinc-50 dark:hover:bg-white/[0.02]">
                                    <div class="flex items-center gap-6">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $isCredit ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10' : 'bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-400' }}">
                                            @if ($isCredit)
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ ucfirst($tx->type) }}
                                            </div>
                                            <div class="mt-1 flex items-center gap-3">
                                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">{{ $tx->created_at?->format('M j, Y') }}</span>
                                                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-zinc-500 dark:bg-white/10 dark:text-zinc-400">
                                                    {{ strtoupper($tx->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-black {{ $isCredit ? 'text-emerald-500' : 'text-zinc-900 dark:text-white' }} tracking-tighter">
                                            {{ $isCredit ? '+' : '' }}¥{{ number_format((int) $tx->amount_yen) }}
                                        </div>
                                        @if ($tx->provider)
                                            <div class="mt-1 text-[8px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                                {{ $tx->provider }}
                                            </div>
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
