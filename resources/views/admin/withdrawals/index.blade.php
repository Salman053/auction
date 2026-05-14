<x-admin-layout :title="'Withdrawals'">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Withdrawals</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Approve or reject withdrawal requests.</p>
            </div>

            <div class="flex flex-wrap gap-2 text-sm">
                @foreach (['pending', 'approved', 'rejected', 'all'] as $tab)
                    <a href="{{ route('admin.withdrawals.index', ['status' => $tab]) }}"
                        class="rounded-full px-5 py-2 text-[10px] font-black uppercase tracking-widest transition-all {{ $status === $tab ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10' }}">
                        {{ ucfirst($tab) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div
        class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead
                class="bg-zinc-50 border-b border-black/5 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                <tr>
                    <th class="px-6 py-4">Beneficiary</th>
                    <th class="px-6 py-4">Payout Value</th>
                    <th class="px-6 py-4">Transfer Details</th>
                    <th class="px-6 py-4">Approval State</th>
                    <th class="px-6 py-4">Submission Date</th>
                    <th class="px-6 py-4 text-right">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($withdrawals as $w)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $w->user?->name }}</span>
                                <span class="text-xs font-medium text-slate-500">{{ $w->user?->email }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-black text-slate-900 dark:text-brand-gold">
                            ¥{{ number_format((int) $w->amount_yen) }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div>
                                    <span
                                        class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:bg-white/5 dark:text-zinc-400">
                                        {{ str_replace('_', ' ', $w->destination_type ?? 'N/A') }}
                                    </span>
                                </div>

                                @if ($w->memo)
                                    <div x-data="{
                                        copied: false,
                                        copyMemo() {
                                            // This grabs the text directly from the div below to avoid JS syntax errors
                                            const text = $refs.memoText.innerText.trim();
                                            navigator.clipboard.writeText(text);
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 2000);
                                        }
                                    }" @click="copyMemo"
                                        class="group relative max-w-[260px] cursor-pointer rounded-lg border border-zinc-200 bg-zinc-50/50 p-4 transition-all hover:border-blue-600 hover:bg-white dark:border-white/10 dark:bg-black/20 dark:hover:bg-black/30">

                                        <!-- Added x-ref here to safely target the text -->
                                        <div x-ref="memoText"
                                            class="text-[11px] font-bold leading-relaxed text-slate-900 dark:text-zinc-100">
                                            {{ $w->memo }}
                                        </div>

                                        <div
                                            class="mt-2.5 flex items-center gap-2 border-t border-slate-100 pt-2 dark:border-white/5">
                                            <div
                                                class="flex h-5 w-5 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-slate-200 group-hover:ring-brand-gold dark:bg-zinc-900 dark:ring-white/10">
                                                <!-- Icon Toggle -->
                                                <svg x-show="!copied"
                                                    class="h-3 w-3 text-slate-400 group-hover:text-brand-gold"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V21a2 2 0 01-2 2h-6a2 2 0 01-2-2v-5m-5-5l5-5" />
                                                </svg>
                                                <svg x-show="copied" x-cloak class="h-3 w-3 text-emerald-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>

                                            <!-- Text Feedback -->
                                            <span class="text-[10px] font-black uppercase tracking-widest transition"
                                                :class="copied ? 'text-emerald-500' :
                                                    'text-zinc-500 group-hover:text-blue-600'">
                                                <span x-text="copied ? 'Copied to Clipboard!' : 'Copy Details'"></span>
                                            </span>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-black uppercase tracking-widest {{ $w->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($w->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ $w->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs font-bold text-slate-500">
                            <div>{{ $w->created_at?->diffForHumans() }}</div>
                            @if ($w->transaction_id)
                                <div class="mt-1 font-black uppercase tracking-tighter text-slate-400">Ref:
                                    {{ $w->transaction_id }}</div>
                            @endif
                            @if ($w->receipt_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($w->receipt_path) }}"
                                    target="_blank" class="mt-1 flex items-center gap-1 text-[#1877f2] hover:underline">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Receipt
                                </a>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($w->status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.withdrawals.decide', $w) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve" />
                                        <button
                                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                                            type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.decide', $w) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject" />
                                        <button
                                            class="rounded-lg bg-rose-50 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-rose-600 hover:bg-rose-100 transition"
                                            type="submit">Reject</button>
                                    </form>
                                </div>
                            @else
                                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    {{ $w->approved_at?->diffForHumans() }}
                                    @if ($w->approvedBy)
                                        · {{ $w->approvedBy->name }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-12 text-center text-sm font-bold text-slate-400" colspan="6">No withdrawal
                            requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $withdrawals->links() }}
        </div>
    </div>
</x-admin-layout>
