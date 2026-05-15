<x-user-layout :title="'Support'">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Support Inquiries</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">View and respond to your active support tickets.
                </p>
            </div>
            <div>
                <a href="{{ route('user.support.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02]">
                    New Inquiry
                </a>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <div
            class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
            @if ($tickets->isEmpty())
                <div class="p-12 text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-50 dark:bg-white/5">
                        <svg class="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-bold text-zinc-900 dark:text-white">No support tickets found.</p>
                    <p class="mt-1 text-xs text-zinc-500">If you need assistance, please submit a new inquiry.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($tickets as $ticket)
                        <a href="{{ route('user.support.show', $ticket) }}"
                            class="block p-6 transition hover:bg-slate-50 dark:hover:bg-white/[0.02] lg:px-8">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $ticket->subject }}
                                    </h3>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                        <span>#{{ $ticket->id }}</span>
                                        <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                        <span>{{ $ticket->created_at->format('M j, Y H:i') }}</span>
                                    </p>
                                </div>
                                <div>
                                    @if ($ticket->status === 'open')
                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-600 ring-1 ring-inset ring-amber-500/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">Open</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-zinc-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-zinc-600 ring-1 ring-inset ring-zinc-500/10 dark:bg-white/5 dark:text-zinc-400 dark:ring-white/10">Closed</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 p-6 dark:border-white/5">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-user-layout>
