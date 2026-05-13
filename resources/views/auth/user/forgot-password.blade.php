<x-guest-layout :title="'Lost Access'">
    <div class="mx-auto flex min-h-[60vh] flex-col justify-center py-20">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase tracking-tighter">Recover Access</h1>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-zinc-400">Enter your email to receive a secure recovery link.</p>
            </div>

            <div class="mt-12 rounded-[2.5rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                @if (session('status'))
                    <div class="mb-8 rounded-2xl bg-emerald-50 p-4 text-xs font-bold text-emerald-600 dark:bg-emerald-500/10">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Account Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('email')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-2xl bg-blue-600 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                        Email Recovery Link
                    </button>

                    <div class="pt-4 text-center">
                        <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-blue-600 transition">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
