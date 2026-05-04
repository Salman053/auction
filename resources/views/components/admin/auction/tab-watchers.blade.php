@props(['auction'])

<div x-show="tab === 'watchers'" x-cloak class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 bg-zinc-50/50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Added</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @forelse ($auction->watchlistItems as $item)
                    <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-white/5">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                    {{ strtoupper(substr($item->user->name ?? '?', 0, 2)) }}
                                </div>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $item->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">{{ $item->user->email }}</td>
                        <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400">{{ $item->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No users are watching this auction.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
