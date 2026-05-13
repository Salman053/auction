<x-user-layout :title="'Account Security'">
    <div class="mb-10">
        <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white">Profile Management</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Manage your collector identity and security settings.</p>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
        {{-- Profile Information --}}
        <div class="lg:col-span-2">
            <x-dashboard.card title="Collector Identity">
                <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Full Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Email Address</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-blue-600 px-10 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:scale-[1.02] active:scale-95">Save Changes</button>
                        
                        @if (session('status') === 'profile-updated')
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 animate-pulse">Identity updated successfully.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>

            <x-dashboard.card title="Payouts" class="mt-10">
                <div class="rounded-3xl border border-zinc-200 bg-zinc-50 p-6 dark:border-white/10 dark:bg-white/5">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Manual Withdrawal Processing</h3>
                    <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                        Withdrawals are reviewed and approved by admins for compliance. Submit a request from the Withdrawals page and track it from your wallet ledger.
                    </p>
                </div>
            </x-dashboard.card>

            {{-- Update Password --}}
            <x-dashboard.card title="Security Credentials" class="mt-10">
                <form method="POST" action="{{ route('user.profile.password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label for="current_password" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Current Password</label>
                            <input id="current_password" name="current_password" type="password" required class="w-full rounded-2xl border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">New Password</label>
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Confirm New</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-zinc-900 px-10 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-zinc-900/20 transition hover:scale-[1.02] active:scale-95 dark:bg-white dark:text-zinc-900">Update Security</button>
                        
                        @if (session('status') === 'password-updated')
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 animate-pulse">Credentials successfully rotated.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>
        </div>

        {{-- Side Info --}}
        <div>
            <div class="rounded-[2.5rem] bg-blue-600 p-10 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 h-32 w-32 rounded-full bg-white/10 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-8 flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 backdrop-blur-xl border border-white/20">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-200">Security Shield</h3>
                    <p class="mt-6 text-[13px] font-medium leading-relaxed text-blue-50/80">Your account is secured with enterprise-grade encryption. Ensure your password is unique and contains a mix of symbols and characters.</p>
                    <div class="mt-10 space-y-4 border-t border-white/10 pt-8">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-white/50">Status</span>
                            <span class="inline-flex items-center gap-2 text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
