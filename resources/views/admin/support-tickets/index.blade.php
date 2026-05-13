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
                        class="rounded-full px-5 py-2 font-black uppercase tracking-widest text-[10px] transition-all {{ $status === $tab ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10' }}"
                    >
                        {{ ucfirst($tab) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 border-b border-black/5 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                <tr>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Requester</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-6 py-4">
                            <a class="font-bold text-blue-600 hover:text-blue-700 transition" href="{{ route('admin.support-tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                        </td>
                        <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $ticket->requester_email ?? $ticket->user?->email ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @if ($ticket->status === 'open')
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">OPEN</span>
                            @else
                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">CLOSED</span>
                            @endif
                        </td>
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

