<x-admin-layout :title="'Proxies'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">Proxy Pool</h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Health and rotation metrics (CSV import + checks next).</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">Host</th>
                    <th class="px-5 py-3">Country</th>
                    <th class="px-5 py-3">Active</th>
                    <th class="px-5 py-3">Success</th>
                    <th class="px-5 py-3">Failure</th>
                    <th class="px-5 py-3">Last used</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($proxies as $proxy)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $proxy->scheme }}://{{ $proxy->host }}:{{ $proxy->port }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $proxy->username ? 'auth: '.$proxy->username : '' }}</div>
                        </td>
                        <td class="px-5 py-4">{{ $proxy->country ?? '—' }}</td>
                        <td class="px-5 py-4">{{ $proxy->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-5 py-4">{{ number_format($proxy->success_count) }}</td>
                        <td class="px-5 py-4">{{ number_format($proxy->failure_count) }}</td>
                        <td class="px-5 py-4">{{ $proxy->last_used_at?->diffForHumans() ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="6">No proxies added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $proxies->links() }}
        </div>
    </div>
</x-admin-layout>

