<x-user-layout :title="'Create Support Inquiry'">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('user.support.index') }}"
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 transition hover:bg-zinc-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
            <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white uppercase tracking-tighter">
            New Support Inquiry</h1>
    </div>

    <div class="mx-auto max-w-3xl">
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10 lg:p-10">
            <form method="POST" action="{{ route('user.support.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="subject"
                        class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">
                        Inquiry Subject
                    </label>
                    <input id="subject" name="subject" value="{{ old('subject') }}" type="text" required
                        placeholder="Brief summary of your issue"
                        class="w-full rounded-lg border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-white/5 dark:ring-white/10 dark:text-white">
                    @error('subject')
                        <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body"
                        class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">
                        Inquiry Details
                    </label>
                    <textarea id="body" name="body" rows="8" required
                        placeholder="Please provide as much detail as possible..."
                        class="w-full rounded-lg border-none bg-zinc-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-zinc-200 focus:ring-2 focus:ring-blue-600 dark:bg-white/5 dark:ring-white/10 dark:text-white">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('user.support.index') }}"
                        class="text-xs font-black uppercase tracking-widest text-zinc-500 hover:text-zinc-700 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-8 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:scale-[1.02] active:scale-95">
                        Transmit Inquiry
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 rounded-xl bg-blue-50/50 p-6 ring-1 ring-blue-100 dark:bg-blue-900/10 dark:ring-blue-900/20">
            <div class="flex gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-blue-900 dark:text-blue-100 uppercase tracking-widest">Priority Protocol</h3>
                    <p class="mt-1 text-xs font-bold leading-relaxed text-blue-700/80 dark:text-blue-300/60">
                        Our administrative team processes dashboard inquiries with higher priority. Typical response window is within 60 minutes during peak hours.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
