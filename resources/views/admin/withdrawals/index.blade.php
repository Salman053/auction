<x-admin-layout :title="'Withdrawals'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Withdrawals</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Approve or reject withdrawal requests.</p>
            </div>

            <div class="flex flex-wrap gap-2 text-sm">
                @foreach (['pending', 'approved', 'rejected', 'all'] as $tab)
                    <a
                        href="{{ route('admin.withdrawals.index', ['status' => $tab]) }}"
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
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Created</th>
                    <th class="px-5 py-3 text-right">Decision</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($withdrawals as $w)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $w->user?->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $w->user?->email }}</div>
                        </td>
                        <td class="px-5 py-4 font-semibold">¥{{ number_format((int) $w->amount_yen) }}</td>
                        <td class="px-5 py-4 font-semibold">{{ strtoupper($w->status) }}</td>
                        <td class="px-5 py-4">
                            <div>{{ $w->created_at?->diffForHumans() }}</div>
                            @if($w->transaction_id)
                                <div class="mt-1 text-[10px] font-bold text-zinc-500 uppercase tracking-tighter">ID: {{ $w->transaction_id }}</div>
                            @endif
                            @if($w->receipt_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($w->receipt_path) }}" target="_blank" class="mt-1 flex items-center gap-1 text-[10px] font-bold text-[#1877f2] hover:underline">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    View Receipt
                                </a>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($w->status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.withdrawals.decide', $w) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve" />
                                        <button class="rounded-full bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-500" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.decide', $w) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject" />
                                        <button class="rounded-full bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-500" type="submit">Reject</button>
                                    </form>
                                </div>
                            @else
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $w->approved_at?->diffForHumans() }}
                                    @if ($w->approvedBy)
                                        · by {{ $w->approvedBy->email }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="5">No withdrawals found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $withdrawals->links() }}
        </div>
    </div>
</x-admin-layout>

