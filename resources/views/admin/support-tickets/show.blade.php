<x-admin-layout :title="'Ticket'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{{ $ticket->subject }}</h1>
                <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Requester: {{ $ticket->requester_email ?? $ticket->user?->email ?? '—' }} · Status: {{ strtoupper($ticket->status) }}
                </div>
            </div>
            <a href="{{ route('admin.support-tickets.index') }}" class="rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">Back</a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="border-b border-black/5 px-5 py-4 dark:border-white/10">
                <h2 class="text-lg font-semibold">Messages</h2>
            </div>
            <div class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($ticket->messages as $message)
                    <div class="px-5 py-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div class="font-semibold">{{ $message->author?->email ?? 'Guest/User' }} @if($message->is_internal) <span class="ml-2 rounded-full bg-amber-600/10 px-2 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">Internal</span> @endif</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $message->created_at?->diffForHumans() }}</div>
                        </div>
                        <div class="mt-2 whitespace-pre-wrap text-zinc-700 dark:text-zinc-300">{{ $message->body }}</div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-sm text-zinc-600 dark:text-zinc-400">No messages.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
            <h2 class="text-lg font-semibold">Reply</h2>
            <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="text-sm font-semibold" for="body">Message</label>
                    <textarea id="body" name="body" rows="6" class="mt-1 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5">{{ old('body') }}</textarea>
                    @error('body') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" name="is_internal" value="1" class="rounded border-zinc-300 dark:border-zinc-700" />
                    Internal note
                </label>
                <button type="submit" class="rounded-full bg-[#1877f2] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#166fe5]">Send</button>
            </form>
        </div>
    </div>
</x-admin-layout>

