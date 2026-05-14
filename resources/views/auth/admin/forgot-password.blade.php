<x-guest-layout :title="'Admin Recovery'">
    <div class="mx-auto flex min-h-[60vh] flex-col justify-center py-20">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase tracking-tighter">
                    Admin Recovery</h1>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-zinc-400">Authorized personnel: Enter your
                    email to receive a recovery link.</p>
            </div>

            <div
                class="mt-12 rounded-[2.5rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                @if (session('status'))
                    <div
                        class="mb-8 rounded-lg bg-emerald-50 p-4 text-xs font-bold text-emerald-600 dark:bg-emerald-500/10">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email"
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Admin
                            Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            autofocus
                            class="w-full rounded-lg border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('email')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-brand-navy py-4 text-[10px] font-black uppercase tracking-widest text-brand-gold shadow-xl shadow-brand-gold/10 transition hover:bg-black/10 hover:scale-[1.02] active:scale-95">
                        Email Recovery Link
                    </button>

                    <div class="pt-4 text-center">
                        <a href="{{ route('admin.login') }}"
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-brand-gold transition">Back
                            to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
