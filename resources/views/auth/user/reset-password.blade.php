<x-guest-layout :title="'Set New Pass-Key'">
    <div class="mx-auto flex min-h-[60vh] flex-col justify-center py-20">
        <div class="mx-auto w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase tracking-tighter">
                    New Pass-Key</h1>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-zinc-400">Establish a new secure pass-key for
                    your account.</p>
            </div>

            <div
                class="mt-12 rounded-[2.5rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email"
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Verified
                            Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}"
                            required readonly
                            class="w-full rounded-lg border-none bg-slate-100 px-5 py-4 text-sm font-bold text-zinc-500 shadow-inner ring-1 ring-slate-200 dark:bg-black/40 dark:ring-white/5 dark:text-zinc-500 cursor-not-allowed">
                        @error('email')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">New
                            Pass-Key</label>
                        <input id="password" name="password" type="password" required autofocus
                            class="w-full rounded-lg border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                        @error('password')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Confirm
                            Pass-Key</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full rounded-lg border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white">
                    </div>

                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-blue-600 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                        Reset Pass-Key
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
