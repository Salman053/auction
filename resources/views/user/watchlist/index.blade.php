<x-user-layout :title="'Watchlist'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">Watchlist</h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Auctions you are watching.</p>
    </div>

    <div class="mt-6 rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        @if ($items->isEmpty())
            <div class="p-6 text-sm text-zinc-600 dark:text-zinc-400">No items yet. Add auctions from the Auctions page.</div>
        @else
            <div class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($items as $item)
                    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold">{{ $item->auction?->title }}</div>
                            <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                Current: ¥{{ number_format((int) ($item->auction?->current_bid_yen ?? 0)) }} · Ends {{ $item->auction?->ends_at?->diffForHumans() ?? '—' }}
                            </div>
                        </div>

                        @if ($item->auction)
                            <form method="POST" action="{{ route('user.watchlist.destroy', $item->auction) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="border-t border-black/5 p-4 dark:border-white/10">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-user-layout>

