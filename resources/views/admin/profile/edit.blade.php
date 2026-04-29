<x-admin-layout :title="'Administrative Security'">
    <div class="mb-10">
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Admin Management</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Control your administrative access and security credentials.</p>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
        {{-- Profile Information --}}
        <div>
            <x-dashboard.card title="Administrative Identity">
                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Admin Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-rose-500 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Login Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-rose-500 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-slate-900 px-8 py-3 text-sm font-black text-white shadow-lg transition hover:scale-[1.02] dark:bg-white dark:text-slate-900">Save Identity</button>
                        
                        @if (session('status') === 'profile-updated')
                            <p class="text-xs font-bold text-emerald-600 animate-pulse">Admin profile updated.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>
        </div>

        {{-- Update Password --}}
        <div>
            <x-dashboard.card title="Access Credentials">
                <form method="POST" action="{{ route('admin.profile.password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Current System Password</label>
                        <input id="current_password" name="current_password" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-rose-500 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">New Admin Password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-rose-500 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-semibold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-rose-500 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="rounded-2xl bg-rose-600 px-8 py-3 text-sm font-black text-white shadow-lg transition hover:scale-[1.02]">Rotate Password</button>
                        
                        @if (session('status') === 'password-updated')
                            <p class="text-xs font-bold text-emerald-600 animate-pulse">Credentials successfully updated.</p>
                        @endif
                    </div>
                </form>
            </x-dashboard.card>
        </div>
    </div>
</x-admin-layout>
