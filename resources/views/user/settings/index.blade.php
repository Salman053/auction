<x-user-layout :title="'Account Settings'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">Account Settings</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Profile, security, and notification preferences (wireframe).</p>

        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl bg-[#f0f2f5] p-5 text-sm dark:bg-white/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Profile</div>
                <div class="mt-2 font-semibold">{{ $user?->name }}</div>
                <div class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $user?->email }}</div>
                <div class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">Editable profile + KYC + 2FA screens next.</div>
            </div>

            <div class="rounded-2xl bg-[#f0f2f5] p-5 text-sm dark:bg-white/5">
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Security</div>
                <ul class="mt-3 space-y-2 text-zinc-700 dark:text-zinc-300">
                    <li>Change password</li>
                    <li>Two-factor authentication</li>
                    <li>Active sessions</li>
                </ul>
            </div>
        </div>
    </div>
</x-user-layout>

