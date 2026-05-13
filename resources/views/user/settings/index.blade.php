<x-user-layout :title="'Account Settings'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">Account Settings</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Profile, security, and notification preferences (wireframe).</p>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-[2rem] bg-zinc-50 p-8 text-sm dark:bg-white/5">
                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Identity</div>
                <div class="mt-4 text-xl font-black text-zinc-900 dark:text-white">{{ $user?->name }}</div>
                <div class="mt-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $user?->email }}</div>
                <div class="mt-8">
                    <button class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-700 transition">Modify Profile</button>
                </div>
            </div>

            <div class="rounded-[2rem] bg-zinc-50 p-8 text-sm dark:bg-white/5">
                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Protection</div>
                <ul class="mt-6 space-y-4">
                    <li class="flex items-center gap-3 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        Change Security Credentials
                    </li>
                    <li class="flex items-center gap-3 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-zinc-300"></span>
                        Multi-Factor Authentication
                    </li>
                    <li class="flex items-center gap-3 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-zinc-300"></span>
                        Device Management
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-user-layout>

