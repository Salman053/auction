<x-guest-layout :title="'Admin Login'">
    <div class="mx-auto flex min-h-[60vh] flex-col justify-center py-20">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center">
                <div
                    class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-navy dark:bg-brand-gold">
                    <span class="text-3xl font-black text-brand-gold dark:text-brand-navy">A</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Admin Control</h1>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-zinc-400">Manage users, deposits, scraping,
                    and platform settings.</p>
            </div>

            <div
                class="mt-12 rounded-[2.5rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email"
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Access
                            Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            autofocus
                            class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('email')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password"
                                class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Pass-Key</label>
                            <a href="#"
                                class="text-[10px] font-black uppercase tracking-widest text-brand-gold hover:text-brand-navy transition">Lost
                                Access?</a>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('password')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" value="1"
                            class="h-4 w-4 rounded border-slate-300 text-brand-navy focus:ring-brand-gold dark:border-white/10 dark:bg-black/20">
                        <label for="remember_me"
                            class="ml-3 text-xs font-bold text-slate-500 dark:text-zinc-500">Persistent Session</label>
                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-brand-navy py-4 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] active:scale-95 dark:bg-brand-gold dark:text-brand-navy">
                        Authorize Identity
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-50 pt-8 text-center text-xs font-bold dark:border-white/5">
                    <span class="text-slate-400">System Access Only</span>
                    <span
                        class="ml-1 text-slate-900 underline decoration-brand-gold decoration-2 underline-offset-4 dark:text-white">Restricted
                        Area</span>
                </div>
            </div>

            <p class="mt-10 text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Secured with
                Enterprise-Grade Precision</p>
        </div>
    </div>
</x-guest-layout>
