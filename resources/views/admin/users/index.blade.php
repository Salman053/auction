<x-admin-layout :title="'Users'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Users</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Search, suspend, and adjust bidding multiplier.</p>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-80">
                <label class="sr-only" for="q">Search</label>
                <input
                    id="q"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search email or name…"
                    class="w-full rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5 dark:focus:border-white/25"
                />
            </form>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Multiplier</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-5 py-4 font-semibold">{{ strtoupper((string) $user->role?->value) }}</td>
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
                                    class="w-28 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5 dark:focus:border-white/25"
                                />
                                <button class="rounded-full bg-[#1877f2] px-4 py-2 text-xs font-semibold text-white hover:bg-[#166fe5]" type="submit">Save</button>
                            </form>
                        </td>
                        <td class="px-5 py-4">
                            @if ($user->suspended_at)
                                <span class="rounded-full bg-red-600/10 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-300">Suspended</span>
                            @else
                                <span class="rounded-full bg-green-600/10 px-3 py-1 text-xs font-semibold text-green-700 dark:text-green-300">Active</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <form id="suspend-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                @csrf
                                <button
                                    type="button"
                                    data-confirm
                                    data-confirm-title="{{ $user->suspended_at ? 'Confirm Unsuspend' : 'Confirm Suspension' }}"
                                    data-confirm-message="Are you sure you want to {{ $user->suspended_at ? 'unsuspend' : 'suspend' }} the collector {{ $user->name }} ({{ $user->email }})?"
                                    data-confirm-text="{{ $user->suspended_at ? 'Unsuspend' : 'Suspend' }}"
                                    data-confirm-type="{{ $user->suspended_at ? 'info' : 'danger' }}"
                                    data-confirm-on-confirm="#suspend-form-{{ $user->id }}"
                                    class="rounded-full border border-zinc-200 bg-zinc-50 px-4 py-2 text-xs font-semibold hover:bg-zinc-100 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10"
                                >
                                    {{ $user->suspended_at ? 'Unsuspend' : 'Suspend' }}
                                </button>
                            </form>
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
