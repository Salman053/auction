wwww<x-user-layout :title="'Support Ticket #' . $ticket->id">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.support.index') }}"
                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold tracking-tight">Support Ticket #{{ $ticket->id }}</h1>
            </div>
        </div>
        <div>
            @if ($ticket->status === 'open')
                <span
                    class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600 ring-1 ring-inset ring-amber-500/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">Open</span>
            @else
                <span
                    class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-500/10 dark:bg-white/5 dark:text-slate-400 dark:ring-white/10">Closed</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
                <div class="border-b border-slate-100 p-6 dark:border-white/5 lg:px-8">
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ $ticket->subject }}</h2>
                </div>

                <div class="p-6 lg:p-8 space-y-8">
                    @foreach ($ticket->messages as $message)
                        <div
                            class="flex gap-4 {{ $message->author_user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                            <div class="shrink-0">
                                @if ($message->author_user_id === auth()->id())
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-navy text-brand-gold">
                                        <span class="font-bold">You</span>
                                    </div>
                                @else
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-600 text-white">
                                        <span class="font-bold">W</span>
                                    </div>
                                @endif
                            </div>
                            <div
                                class="flex flex-col {{ $message->author_user_id === auth()->id() ? 'items-end' : 'items-start' }} max-w-[80%]">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-bold text-slate-500">
                                        {{ $message->author_user_id === auth()->id() ? 'You' : 'WatchHub Support' }}
                                    </span>
                                    <span
                                        class="text-[10px] text-slate-400">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <div
                                    class="rounded-2xl p-4 {{ $message->author_user_id === auth()->id() ? 'bg-brand-navy text-white rounded-tr-sm' : 'bg-slate-100 dark:bg-white/5 text-slate-900 dark:text-white rounded-tl-sm' }}">
                                    <p class="whitespace-pre-wrap text-sm leading-relaxed">{{ $message->body }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($ticket->status === 'open')
                <div
                    class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10 lg:p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white mb-4">Post a
                        Reply</h3>
                    <form method="POST" action="{{ route('user.support.reply', $ticket) }}">
                        @csrf
                        <div>
                            <textarea name="body" rows="4"
                                class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white"
                                placeholder="Type your message here..." required></textarea>
                            @error('body')
                                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit"
                                class="rounded-2xl bg-brand-navy px-8 py-3 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] active:scale-95 dark:bg-brand-gold dark:text-brand-navy">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-zinc-900 dark:ring-white/10">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Inquiry Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</p>
                        <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ ucfirst($ticket->status) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Created</p>
                        <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">
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
        </div>
    </div>
</x-user-layout>
