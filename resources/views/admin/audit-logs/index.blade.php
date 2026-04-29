<x-admin-layout :title="'Audit Logs'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Audit Logs</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Search and review system activity.</p>
            </div>
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="w-full sm:w-80">
                <label class="sr-only" for="q">Search</label>
                <input id="q" name="q" value="{{ $search }}" placeholder="Search event…" class="w-full rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5" />
            </form>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">Time</th>
                    <th class="px-5 py-3">Event</th>
                    <th class="px-5 py-3">Actor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">{{ $log->created_at?->toDateTimeString() }}</td>
                        <td class="px-5 py-4 font-semibold">{{ $log->event }}</td>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">{{ $log->actor?->email ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="3">No logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $logs->links() }}
        </div>
    </div>
</x-admin-layout>

