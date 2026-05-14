<div id="confirm-dialog" class="fixed inset-0 z-[999] hidden" aria-hidden="true">
    <div id="confirm-dialog-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div role="dialog" aria-modal="true" aria-labelledby="confirm-dialog-title"
            aria-describedby="confirm-dialog-message"
            class="relative w-full max-w-lg overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-white/10">
            <div class="p-8 sm:p-10">
                <div class="flex items-start gap-5">
                    <div id="confirm-dialog-icon"
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-white/5 text-zinc-900 dark:text-white">
                    </div>

                    <div class="flex-1">
                        <h3 id="confirm-dialog-title"
                            class="text-xl font-black tracking-tight text-zinc-900 dark:text-white"></h3>
                        <p id="confirm-dialog-message"
                            class="mt-3 text-sm font-medium leading-relaxed text-zinc-500 dark:text-zinc-400"></p>
                    </div>
                </div>

                <div class="mt-10 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button id="confirm-dialog-cancel" type="button"
                        class="rounded-lg bg-zinc-100 px-6 py-3.5 text-sm font-bold text-zinc-600 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        Cancel
                    </button>
                    <button id="confirm-dialog-confirm" type="button"
                        class="rounded-lg bg-blue-600 px-8 py-3.5 text-sm font-black uppercase tracking-widest text-white transition hover:bg-blue-500 hover:scale-[1.02] active:scale-95 shadow-xl shadow-blue-600/20">
                        Confirm Action
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
