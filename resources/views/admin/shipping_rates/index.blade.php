<x-admin-layout :title="'Shipping Rates'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Shipping Rates</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Manage ports, countries, and shipping fees.</p>
            </div>

            <a href="{{ route('admin.shipping_rates.create') }}" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-rose-700">
                Add Shipping Rate
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-bold text-emerald-600 dark:text-emerald-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">Location Name</th>
                    <th class="px-5 py-3">Country</th>
                    <th class="px-5 py-3">Port</th>
                    <th class="px-5 py-3">Fee (JPY)</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($rates as $rate)
                    <tr>
                        <td class="px-5 py-4 font-semibold">{{ $rate->name }}</td>
                        <td class="px-5 py-4">{{ $rate->country ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $rate->port ?? '-' }}</td>
                        <td class="px-5 py-4 font-mono font-bold text-rose-600 dark:text-rose-400">¥{{ number_format($rate->fee_yen) }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.shipping_rates.edit', $rate) }}" class="rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-xs font-semibold hover:bg-zinc-100 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                                    Edit
                                </a>
                                <form id="delete-form-{{ $rate->id }}" method="POST" action="{{ route('admin.shipping_rates.destroy', $rate) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm
                                        data-confirm-title="Confirm Deletion"
                                        data-confirm-message="Are you sure you want to delete this shipping rate for {{ $rate->name }}?"
                                        data-confirm-text="Delete"
                                        data-confirm-type="danger"
                                        data-confirm-on-confirm="#delete-form-{{ $rate->id }}"
                                        class="rounded-full border border-zinc-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-100 dark:border-white/10 dark:bg-rose-500/10 dark:hover:bg-rose-500/20"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $rates->links() }}
        </div>
    </div>
</x-admin-layout>
