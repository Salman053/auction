<x-admin-layout :title="'Deposits'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Deposits</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Approve or reject pending deposit requests.</p>
            </div>

            <div class="flex flex-wrap gap-2 text-sm">
                @foreach (['pending', 'approved', 'rejected', 'all'] as $tab)
                    <a
                        href="{{ route('admin.deposits.index', ['status' => $tab]) }}"
                        class="rounded-full px-4 py-2 font-semibold {{ $status === $tab ? 'bg-[#1877f2] text-white' : 'bg-[#f0f2f5] text-zinc-900 hover:bg-zinc-200/60 dark:bg-white/5 dark:text-zinc-100 dark:hover:bg-white/10' }}"
                    >
                        {{ ucfirst($tab) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Provider</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Decision</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($transactions as $tx)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $tx->wallet?->user?->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $tx->wallet?->user?->email }}</div>
                        </td>
                        <td class="px-5 py-4 font-semibold">¥{{ number_format((int) $tx->amount_yen) }}</td>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ strtoupper((string) $tx->provider) }}</div>
                            @if($tx->provider_reference)
                                <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-tighter">ID: {{ $tx->provider_reference }}</div>
                            @endif
                            @if($tx->receipt_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($tx->receipt_path) }}" target="_blank" class="mt-1 flex items-center gap-1 text-[10px] font-bold text-[#1877f2] hover:underline">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    View Receipt
                                </a>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                @if ($tx->status === 'pending') bg-amber-600/10 text-amber-700 dark:text-amber-300
                                @elseif ($tx->status === 'approved') bg-green-600/10 text-green-700 dark:text-green-300
                                @else bg-red-600/10 text-red-700 dark:text-red-300 @endif
                            ">
                                {{ strtoupper($tx->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($tx->status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <form id="approve-form-{{ $tx->id }}" method="POST" action="{{ route('admin.deposits.decide', $tx) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve" />
                                        <button type="button"
                                            data-confirm
                                            data-confirm-title="Approve Deposit"
                                            data-confirm-message="Are you sure you want to approve this deposit request?"
                                            data-confirm-text="Approve"
                                            data-confirm-type="success"
                                            data-confirm-on-confirm="#approve-form-{{ $tx->id }}"
                                            class="rounded-full bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-500">Approve</button>
                                    </form>
                                    <form id="reject-form-{{ $tx->id }}" method="POST" action="{{ route('admin.deposits.decide', $tx) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject" />
                                        <button type="button"
                                            data-confirm
                                            data-confirm-title="Reject Deposit"
                                            data-confirm-message="Are you sure you want to reject this deposit request?"
                                            data-confirm-text="Reject"
                                            data-confirm-type="danger"
                                            data-confirm-on-confirm="#reject-form-{{ $tx->id }}"
                                            class="rounded-full bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-500">Reject</button>
                                    </form>
                                </div>
                            @else
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $tx->approved_at?->diffForHumans() }}
                                    @if ($tx->approvedBy)
                                        · by {{ $tx->approvedBy->email }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="5">No deposit transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $transactions->links() }}
        </div>
    </div>
</x-admin-layout>
