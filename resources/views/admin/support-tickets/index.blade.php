<x-admin-layout :title="'Support Tickets'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Support Tickets</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Inbox for user and guest inquiries.</p>
            </div>

            <div class="flex flex-wrap gap-2 text-sm">
                @foreach (['open', 'closed', 'all'] as $tab)
                    <a
                        href="{{ route('admin.support-tickets.index', ['status' => $tab]) }}"
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
                    <th class="px-5 py-3">Subject</th>
                    <th class="px-5 py-3">Requester</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-5 py-4">
                            <a class="font-semibold text-[#1877f2] hover:underline" href="{{ route('admin.support-tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                        </td>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $ticket->requester_email ?? $ticket->user?->email ?? '—' }}
                        </td>
                        <td class="px-5 py-4 font-semibold">{{ strtoupper($ticket->status) }}</td>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">{{ $ticket->created_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400" colspan="4">No tickets.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $tickets->links() }}
        </div>
    </div>
</x-admin-layout>

