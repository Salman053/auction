<div id="confirm-dialog" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="confirm-dialog-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-dialog-title"
            aria-describedby="confirm-dialog-message"
            class="relative w-full max-w-lg overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all dark:bg-zinc-900"
        >
            <div class="p-8 sm:p-10">
                <div class="flex items-start gap-5">
                    <div id="confirm-dialog-icon" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-brand-gold/10 text-brand-navy"></div>

                    <div class="flex-1">
                        <h3 id="confirm-dialog-title" class="text-xl font-black tracking-tight text-slate-900 dark:text-white"></h3>
                        <p id="confirm-dialog-message" class="mt-3 text-sm font-medium leading-relaxed text-slate-500 dark:text-zinc-400"></p>
                    </div>
                </div>

                <div class="mt-10 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        id="confirm-dialog-cancel"
                        type="button"
                        class="rounded-2xl bg-slate-50 px-6 py-3.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10"
                    >
                        Cancel
                    </button>
                    <button
                        id="confirm-dialog-confirm"
                        type="button"
                        class="rounded-2xl bg-brand-navy px-8 py-3.5 text-sm font-black uppercase tracking-widest text-white transition hover:scale-105 active:scale-95 shadow-xl"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

