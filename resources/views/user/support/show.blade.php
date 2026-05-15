<x-user-layout :title="'Support Ticket #' . $ticket->id">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.support.index') }}"
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 transition hover:bg-zinc-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">
                    Support Ticket #{{ $ticket->id }}</h1>
            </div>
        </div>
        <div>
            @if ($ticket->status === 'open')
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600 ring-1 ring-inset ring-amber-500/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">Open</span>
                    <form id="close-ticket-form" action="{{ route('user.support.close', $ticket) }}" method="POST">
                        @csrf
                        <button type="button" data-confirm data-confirm-title="Close Ticket"
                            data-confirm-message="Are you sure you want to close this ticket? You can reopen it later if needed."
                            data-confirm-text="Close Ticket" data-confirm-type="danger"
                            data-confirm-on-confirm="#close-ticket-form"
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-rose-600 transition">Close
                            Ticket</button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-500/10 \ dark:text-slate-400 dark:ring-white/10">Closed</span>
                    <form action="{{ route('user.support.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-blue-600 transition">Reopen
                            Ticket</button>
                    </form>
                </div>
            @endif
            <div class="mt-2 text-right">
                <form id="delete-ticket-form" action="{{ route('user.support.destroy', $ticket) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" data-confirm data-confirm-title="Delete Ticket History"
                        data-confirm-message="Are you sure you want to remove this ticket history? This action is irreversible."
                        data-confirm-text="Delete History" data-confirm-type="danger"
                        data-confirm-on-confirm="#delete-ticket-form"
                        class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-700 transition py-2">Delete
                        Ticket</button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div
                class="rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
                <div class="border-b border-zinc-100 p-6 dark:border-white/5 lg:px-8 bg-zinc-50/10">
                    <h2 class="text-xl font-black text-zinc-900 dark:text-white leading-tight">{{ $ticket->subject }}
                    </h2>
                </div>

                <div id="message-container" class="p-6 lg:p-8 space-y-8 max-h-[70vh] overflow-y-auto scroll-smooth">
                    @foreach ($ticket->messages as $message)
                        @php
                            $isUser = $message->author_user_id === auth('user')->id();
                        @endphp
                        <div class="flex gap-4 {{ $isUser ? 'flex-row-reverse' : '' }}">
                            <div class="shrink-0">
                                @if ($isUser)
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white shadow-md">
                                        <span
                                            class="text-xs font-bold">{{ substr(auth('user')->user()->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                @else
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-600 text-white shadow-sm">
                                        <span class="text-xs font-bold">S</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col {{ $isUser ? 'items-end' : 'items-start' }} max-w-[85%]">
                                <div class="flex items-center gap-2 mb-1 px-1">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        {{ $isUser ? 'Client Account' : 'Support Intelligence' }}
                                    </span>
                                    <span
                                        class="text-[10px] text-slate-400 font-medium">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <div
                                    class="rounded-lg p-4 transition-all duration-300 hover:shadow-md {{ $isUser ? 'bg-blue-600 text-white rounded-tr-sm' : 'bg-zinc-50 dark:bg-white/5 text-zinc-900 dark:text-white rounded-tl-sm ring-1 ring-zinc-100 dark:ring-white/5' }}">
                                    <p class="whitespace-pre-wrap text-sm leading-relaxed">{{ $message->body }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('message-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        </script>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-6">Concierge Log</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Status</p>
                        <p class="mt-1 text-sm font-black text-zinc-900 dark:text-white">
                            {{ strtoupper($ticket->status) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Established</p>
                        <p class="mt-1 text-sm font-black text-zinc-900 dark:text-white">
                            {{ $ticket->created_at->format('M j, Y H:i') }}</p>
                    </div>
                    @if ($ticket->closed_at)
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Closed</p>
                            <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">
                                {{ $ticket->closed_at->format('M j, Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @if ($ticket->status === 'open')
                <div
                    class="rounded-lg bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-600/5 blur-2xl"></div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400 mb-6">
                        Dispatch Reply</h3>
                    <form method="POST" action="{{ route('user.support.reply', $ticket) }}">
                        @csrf
                        <div>
                            <textarea name="body" rows="4"
                                class="w-full rounded-lg border-none bg-zinc-50 dark:bg-white/5 px-6 py-5 text-sm font-bold text-zinc-900 dark:text-white placeholder:text-zinc-400 dark:placeholder:text-white/20 focus:ring-2 focus:ring-blue-600 transition-all shadow-inner"
                                placeholder="Transmission details..." required></textarea>
                            @error('body')
                                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-8 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:scale-[1.02] active:scale-95">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div
                    class="rounded-lg bg-slate-50 p-8 text-center ring-1 ring-slate-200 dark:bg-white/5 dark:ring-white/10">
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400">This ticket is closed. If you still
                        need help, you can reopen it.</p>
                    <form action="{{ route('user.support.reopen', $ticket) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit"
                            class="rounded-lg bg-white px-6 py-2 text-xs font-bold text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-zinc-900 dark:text-white dark:ring-white/10">Reopen
                            Ticket</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-user-layout>
