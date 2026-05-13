<x-admin-layout :title="'Users'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Users</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Search, suspend, and adjust bidding multiplier.</p>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-80">
                <label class="sr-only" for="q">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search collectors…"
                        class="w-full rounded-2xl border-0 bg-zinc-50 py-3 pl-11 pr-4 text-sm font-bold text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-200 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 dark:bg-black/20 dark:text-white dark:ring-white/10 dark:focus:ring-blue-600"
                    />
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 border-b border-black/5 text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                <tr>
                    <th class="px-6 py-4">Collector Information</th>
                    <th class="px-6 py-4">Security Role</th>
                    <th class="px-6 py-4">Leverage Multiplier</th>
                    <th class="px-6 py-4">Market Status</th>
                    <th class="px-6 py-4 text-right">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-lg bg-zinc-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-zinc-600 dark:bg-white/5 dark:text-zinc-400">
                                {{ strtoupper((string) $user->role?->value) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.users.multiplier', $user) }}" class="flex items-center gap-2">
                                @csrf
                                <input
                                    name="bidding_multiplier_percent"
                                    type="number"
                                    min="100"
                                    max="2000"
                                    value="{{ $user->bidding_multiplier_percent }}"
                                    placeholder="(default)"
                                    class="w-28 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 dark:border-white/10 dark:bg-white/5 dark:focus:border-white/20"
                                />
                                <button class="rounded-full bg-blue-600 px-4 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-700 shadow-lg shadow-blue-600/20" type="submit">Save</button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->suspended_at)
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">Suspended</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-xs font-bold transition hover:bg-zinc-50 hover:text-blue-600 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                                    Manage
                                </a>
                                <form id="suspend-form-{{ $user->id }}" method="POST"
                                    action="{{ route('admin.users.suspend', $user) }}">
                                    @csrf
                                    <button type="button" data-confirm
                                        data-confirm-title="{{ $user->suspended_at ? 'Confirm Unsuspend' : 'Confirm Suspension' }}"
                                        data-confirm-message="Are you sure you want to {{ $user->suspended_at ? 'unsuspend' : 'suspend' }} the collector {{ $user->name }} ({{ $user->email }})?"
                                        data-confirm-text="{{ $user->suspended_at ? 'Unsuspend' : 'Suspend' }}"
                                        data-confirm-type="{{ $user->suspended_at ? 'info' : 'danger' }}"
                                        data-confirm-on-confirm="#suspend-form-{{ $user->id }}"
                                        class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-xs font-bold transition hover:bg-zinc-100 hover:text-rose-600 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                                        {{ $user->suspended_at ? 'Activate' : 'Suspend' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-black/5 p-4 dark:border-white/10">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>
