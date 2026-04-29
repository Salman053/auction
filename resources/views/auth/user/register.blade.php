<x-guest-layout :title="'Initialize Portfolio'">
    <div class="mx-auto flex min-h-[70vh] flex-col justify-center py-20">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-navy dark:bg-brand-gold">
                    <span class="text-3xl font-black text-brand-gold dark:text-brand-navy">W</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Collector Registration</h1>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-zinc-400">Initialize your account to access the Japanese watch market.</p>
            </div>

            <div class="mt-12 rounded-[2.5rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Legal Identity / Display Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('name') <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Primary Access Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('email') <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Private Pass-Key</label>
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            @error('password') <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Confirm Key</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        </div>
                    </div>

                    <p class="text-[10px] leading-relaxed text-slate-400">
                        By initializing your account, you agree to our <a href="#" class="text-brand-gold underline underline-offset-2">Collector Terms</a> and <a href="#" class="text-brand-gold underline underline-offset-2">Market Logistics Protocols</a>.
                    </p>

                    <button type="submit" class="w-full rounded-2xl bg-brand-navy py-4 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] active:scale-95 dark:bg-brand-gold dark:text-brand-navy">
                        Initialize Permanent Account
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-50 pt-8 text-center text-xs font-bold dark:border-white/5">
                    <span class="text-slate-400">Existing Member?</span>
                    <a href="{{ route('login') }}" class="ml-1 text-slate-900 underline decoration-brand-gold decoration-2 underline-offset-4 hover:text-brand-gold transition dark:text-white">Access Dashboard</a>
                </div>
            </div>
            
            <p class="mt-10 text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Vault-Grade Data Protection Enabled</p>
        </div>
    </div>
</x-guest-layout>
