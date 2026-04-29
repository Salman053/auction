<x-user-layout :title="'Account Security'">
    <div class="mb-10">
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Profile Management</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Manage your collector identity and security settings.</p>
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
                            <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Full Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-brand-navy px-8 py-3 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] dark:bg-brand-gold dark:text-brand-navy">Save Changes</button>
                        
                        @if (session('status') === 'profile-updated')
                            <p class="text-xs font-bold text-emerald-600 animate-pulse">Identity updated successfully.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>

            <x-dashboard.card title="Payouts" class="mt-10">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 dark:border-white/10 dark:bg-white/5">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Manual Withdrawal Processing</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-zinc-400">
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
                            <label for="current_password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Current Password</label>
                            <input id="current_password" name="current_password" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">New Password</label>
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Confirm New</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-slate-900 px-8 py-3 text-sm font-black text-white shadow-lg transition hover:scale-[1.02] dark:bg-white dark:text-slate-900">Update Security</button>
                        
                        @if (session('status') === 'password-updated')
                            <p class="text-xs font-bold text-emerald-600 animate-pulse">Credentials successfully rotated.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>
        </div>

        {{-- Side Info --}}
        <div>
            <div class="rounded-3xl bg-brand-navy p-8 text-white">
                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 border border-white/20">
                    <svg class="h-8 w-8 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <h3 class="text-sm font-bold uppercase tracking-widest text-brand-gold">Security Shield</h3>
                <p class="mt-4 text-xs leading-relaxed text-white/60">Your account is secured with enterprise-grade encryption. Ensure your password is unique and contains a mix of symbols and characters.</p>
                <div class="mt-8 space-y-4 border-t border-white/10 pt-6">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-white/40">Status</span>
                        <span class="text-emerald-400">Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
