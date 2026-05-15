<x-admin-layout :title="'Ticket #' . $ticket->id">
    {{-- Hero Section with Ticket Header --}}
    <div class="mb-6">
        <div
            class="rounded-lg bg-gradient-to-br from-white to-zinc-50/50 p-6 shadow-sm ring-1 ring-black/5 dark:from-zinc-900 dark:to-zinc-900/50 dark:ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 text-white shadow-md">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                </path>
                            </svg>
                        </div>
                        <h1 class="truncate text-xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-2xl">
                            {{ $ticket->subject }}
                        </h1>
                        @if ($ticket->status === 'open')
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                </span>
                                Open
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Closed
                            </span>
                        @endif
                    </div>
                    <div
                        class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Requester:</span>
                            <span
                                class="font-semibold text-zinc-900 dark:text-white">{{ $ticket->requester_email ?? ($ticket->user?->email ?? '—') }}</span>
                        </div>
                        @if ($ticket->user)
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 9h6m-6 3h6m-6 3h6M3 9h6m-6 3h6m-6 3h6M9 3v18"></path>
                                </svg>
                                <span>User ID:</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">#{{ $ticket->user_id }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Created:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-white">{{ $ticket->created_at->format('M j, Y H:i') }}</span>
                        </div>
                        @if ($ticket->closed_at)
                            <div class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Closed:</span>
                                <span class="font-medium">{{ $ticket->closed_at->format('M j, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form id="delete-ticket-form" action="{{ route('admin.support-tickets.destroy', $ticket) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            data-confirm
                            data-confirm-title="Delete Ticket"
                            data-confirm-message="Are you sure you want to permanently delete this ticket and all its messages? This action cannot be undone."
                            data-confirm-text="Delete Ticket"
                            data-confirm-type="danger"
                            data-confirm-on-confirm="#delete-ticket-form"
                            class="rounded-lg bg-white px-4 py-3 text-xs font-black uppercase tracking-widest text-rose-600 ring-1 ring-rose-200 transition hover:bg-rose-50 dark:bg-zinc-900 dark:ring-rose-500/30">Delete
                            Ticket</button>
                    </form>

                    @if ($ticket->status === 'open')
                        <form id="close-ticket-form" action="{{ route('admin.support-tickets.close', $ticket) }}"
                            method="POST" class="inline">
                            @csrf
                            <button type="button" data-confirm data-confirm-title="Close Ticket"
                                data-confirm-message="Are you sure you want to close this ticket? The user will be notified."
                                data-confirm-text="Close Ticket" data-confirm-type="danger"
                                data-confirm-on-confirm="#close-ticket-form"
                                class="inline-flex items-center gap-2 rounded-lg bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-600 shadow-sm transition-all hover:bg-rose-100 hover:shadow-md active:scale-[0.98] dark:bg-rose-900/20 dark:text-rose-400 dark:hover:bg-rose-900/30">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Close Ticket
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.support-tickets.reopen', $ticket) }}" method="POST"
                            class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-600 shadow-sm transition-all hover:bg-emerald-100 hover:shadow-md active:scale-[0.98] dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Reopen Ticket
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.support-tickets.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition-all hover:bg-zinc-50 hover:shadow-md active:scale-[0.98] dark:border-white/10 dark:bg-white/5 dark:text-zinc-300 dark:hover:bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content: Conversation - WhatsApp Style -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div
                class="flex h-[calc(100vh-320px)] flex-col rounded-lg bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <!-- Chat Header -->
                <div class="flex items-center justify-between border-b border-black/5 px-5 py-4 dark:border-white/10">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <h2 class="text-lg font-semibold">Conversation</h2>
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $ticket->messages->count() }}
                        {{ Str::plural('message', $ticket->messages->count()) }}</span>
                </div>

                <!-- Messages Container - Scrollable, newest at bottom -->
                <div id="message-container" class="flex-1 space-y-4 overflow-y-auto px-4 py-4 scroll-smooth">
                    @forelse ($ticket->messages as $message)
                        @php
                            $isAdmin = $message->author?->role === \App\Enums\UserRole::Admin;
                            $isInternal = $message->is_internal;
                            $isOwnMessage = $isAdmin; // Admin messages appear on right (like WhatsApp sent)
                        @endphp

                        <!-- Internal Note Banner -->
                        @if ($isInternal)
                            <div class="flex justify-center">
                                <div
                                    class="rounded-full bg-amber-100 px-3 py-1 text-center text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                    <svg class="mr-1 inline h-3 w-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    Internal Note
                                </div>
                            </div>
                        @endif

                        <!-- Message Bubble -->
                        <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }} group">
                            <div
                                class="flex max-w-[85%] items-end gap-2 {{ $isOwnMessage ? 'flex-row-reverse' : '' }}">
                                <!-- Avatar -->
                                <div
                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $isOwnMessage ? 'bg-gradient-to-br from-blue-600 to-blue-700' : 'bg-gradient-to-br from-zinc-400 to-zinc-500 dark:from-zinc-600 dark:to-zinc-700' }} text-white shadow-sm">
                                    <span class="text-xs font-bold">
                                        {{ substr($message->author?->name ?? ($ticket->requester_name ?? 'R'), 0, 1) }}
                                    </span>
                                </div>

                                <!-- Message Content -->
                                <div class="relative">
                                    <div
                                        class="rounded-lg px-4 py-2 shadow-sm {{ $isOwnMessage ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' }} {{ $isInternal ? 'border-l-4 border-l-amber-400' : '' }}">
                                        <p class=" break-words text-sm leading-relaxed">
                                            {{ $message->body }}
                                        </p>
                                    </div>
                                    <!-- Message Metadata -->
                                    <div
                                        class="mt-1 flex items-center gap-1.5 text-[10px] font-medium {{ $isOwnMessage ? 'justify-end' : 'justify-start' }} {{ $isOwnMessage ? 'text-zinc-500' : 'text-zinc-400 dark:text-zinc-500' }}">
                                        <span>{{ $message->created_at?->format('H:i') }}</span>
                                        <span>•</span>
                                        <span>{{ $message->created_at?->diffForHumans() }}</span>
                                        @if ($isOwnMessage)
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                        <form id="delete-message-{{ $message->id }}"
                                            action="{{ route('admin.support-tickets.messages.destroy', [$ticket, $message]) }}"
                                            method="POST" class="ml-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                data-confirm
                                                data-confirm-title="Remove Message"
                                                data-confirm-message="Are you sure you want to remove this message from the ticket history?"
                                                data-confirm-text="Remove"
                                                data-confirm-type="danger"
                                                data-confirm-on-confirm="#delete-message-{{ $message->id }}"
                                                class="text-zinc-400 hover:text-rose-500 transition">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full flex-col items-center justify-center">
                            <div class="rounded-full bg-zinc-100 p-4 dark:bg-zinc-800">
                                <svg class="h-10 w-10 text-zinc-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                No messages yet
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                Start the conversation!
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Reply Form - Attachment Style -->
            @if ($ticket->status === 'open')
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                    <div class="mb-4 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <h2 class="text-lg font-black tracking-tight">Quick Reply</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}"
                        class="space-y-4">
                        @csrf
                        <div>
                            <textarea id="body" name="body" rows="6"
                                class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-5 py-4 text-sm outline-none transition-all focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-white/10 dark:bg-white/5 dark:text-white"
                                placeholder="Type your response here...">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="mt-2 flex items-center gap-1 text-sm text-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <label class="flex cursor-pointer items-center gap-2.5 group">
                            <input type="checkbox" name="is_internal" value="1"
                                class="h-4 w-4 rounded border-zinc-300 text-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/20 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span
                                class="text-sm font-medium text-zinc-700 transition group-hover:text-zinc-900 dark:text-zinc-300 dark:group-hover:text-white">
                                Internal note <span class="text-zinc-400">(hidden from user)</span>
                            </span>
                        </label>
                        <button type="submit"
                            class="mt-2 w-full transform rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition-all hover:shadow-xl hover:shadow-blue-600/30 active:scale-[0.98]">
                            Send Response
                        </button>
                    </form>
                </div>
            @else
                <div
                    class="rounded-lg bg-gradient-to-br from-zinc-50 to-white p-7 text-center ring-1 ring-black/5 dark:from-white/5 dark:to-zinc-900 dark:ring-white/10">
                    <div class="mx-auto w-fit rounded-full bg-zinc-100 p-3 dark:bg-zinc-800">
                        <svg class="h-8 w-8 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-zinc-600 dark:text-zinc-400">This ticket is currently
                        closed.</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">Reopen it to continue the conversation.
                    </p>
                    <form action="{{ route('admin.support-tickets.reopen', $ticket) }}" method="POST"
                        class="mt-6">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-zinc-900 shadow-sm ring-1 ring-zinc-200 transition-all hover:bg-zinc-50 hover:shadow-md active:scale-[0.98] dark:bg-zinc-900 dark:text-white dark:ring-white/10 dark:hover:bg-zinc-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Reopen Ticket
                        </button>
                    </form>
                </div>
            @endif

            <!-- User Info Card -->
            @if ($ticket->user)
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                    <div class="mb-5 flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md text-xl font-bold">
                            {{ substr($ticket->user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">
                                {{ $ticket->user->name }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ticket->user->email }}
                            </p>
                        </div>
                    </div>
                    <div class="space-y-3 border-t border-zinc-100 pt-4 dark:border-white/5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Role</span>
                            <span
                                class="{{ $ticket->user->role->value === 'admin' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} rounded-lg px-2 py-0.5 text-xs font-bold">
                                {{ strtoupper($ticket->user->role->value) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Balance</span>
                            <span
                                class="text-sm font-bold text-emerald-600 dark:text-emerald-400">¥{{ number_format($ticket->user->wallet?->balance_yen ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Member
                                Since</span>
                            <span
                                class="text-sm font-medium text-zinc-900 dark:text-white">{{ $ticket->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('admin.users.index', ['search' => $ticket->user->email]) }}"
                            class="flex items-center justify-center gap-2 rounded-lg bg-slate-50 py-2.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-100 hover:shadow-sm dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            View Full Profile
                        </a>
                    </div>
                </div>
            @endif

            <!-- Metadata Card -->
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
                <div class="mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400">Ticket Metadata</h3>
                </div>
                <dl class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">Ticket ID</dt>
                        <dd class="font-mono font-bold text-zinc-900 dark:text-white">#{{ $ticket->id }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd class="font-medium">
                            @if ($ticket->status === 'open')
                                <span class="text-emerald-600 dark:text-emerald-400">Active</span>
                            @else
                                <span class="text-rose-600 dark:text-rose-400">Closed</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">Created</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">
                            {{ $ticket->created_at->format('M j, Y H:i') }}</dd>
                    </div>
                    @if ($ticket->closed_at)
                        <div class="flex items-center justify-between text-sm">
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">Closed</dt>
                            <dd class="font-medium text-rose-600 dark:text-rose-400">
                                {{ $ticket->closed_at->format('M j, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Auto-scroll to bottom script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('message-container');
            if (container) {
                // Scroll to bottom on load
                container.scrollTop = container.scrollHeight;

                // Optional: Create a MutationObserver to auto-scroll when new messages are added
                const observer = new MutationObserver(function() {
                    container.scrollTop = container.scrollHeight;
                });

                observer.observe(container, {
                    childList: true,
                    subtree: true
                });
            }
        });
    </script>
</x-admin-layout>
