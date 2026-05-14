<x-admin-layout :title="'Scraping Logs'">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">Scraping Logs</h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Runs, outcomes, and proxy used.</p>
    </div>

    <div
        class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead
                class="bg-zinc-50 border-b border-black/5 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                <tr>
                    <th class="px-6 py-4">Session UUID</th>
                    <th class="px-6 py-4">Runtime Status</th>
                    <th class="px-6 py-4">Initialization</th>
                    <th class="px-6 py-4">Completion</th>
                    <th class="px-6 py-4">Routing Node</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-5 py-4 font-mono text-xs">{{ $log->run_uuid }}</td>
                        <td class="px-5 py-4 font-semibold">{{ strtoupper($log->status) }}</td>
                        <td class="px-5 py-4">{{ $log->started_at?->diffForHumans() ?? '—' }}</td>
                        <td class="px-5 py-4">{{ $log->ended_at?->diffForHumans() ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $log->proxy ? $log->proxy->host . ':' . $log->proxy->port : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="5">No logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $logs->links() }}
        </div>
    </div>
</x-admin-layout>
