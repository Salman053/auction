<x-guest-layout :title="'Support Center'">
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold">Administrative Support</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white sm:text-6xl">Intelligence Base.</h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg font-medium leading-relaxed text-slate-500 dark:text-zinc-400">Everything you need to know about the WatchHub bidding ecosystem and logistics protocols.</p>
            </div>

            <div class="mx-auto mt-20 max-w-3xl space-y-8">
                {{-- Bidding --}}
                <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                    <h3 class="flex items-center gap-4 text-lg font-black text-slate-900 dark:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy text-[10px] text-brand-gold">Q</span>
                        How does the 500% Bidding Limit work?
                    </h3>
                    <p class="mt-4 pl-12 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">To maintain market stability, we allow you to bid up to 5 times the amount held in your wallet. For example, a ¥100,000 deposit grants you a ¥500,000 total bidding capacity across multiple auctions.</p>
                </div>

                {{-- Deposits --}}
                <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                    <h3 class="flex items-center gap-4 text-lg font-black text-slate-900 dark:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy text-[10px] text-brand-gold">Q</span>
                        What currencies do you accept for deposits?
                    </h3>
                    <p class="mt-4 pl-12 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">All internal transactions are settled in Japanese Yen (JPY). You can deposit funds via bank transfer or credit card, which will be automatically converted at our competitive mid-market rates.</p>
                </div>

                {{-- Shipping --}}
                <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                    <h3 class="flex items-center gap-4 text-lg font-black text-slate-900 dark:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy text-[10px] text-brand-gold">Q</span>
                        How long does international delivery take?
                    </h3>
                    <p class="mt-4 pl-12 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Once an auction is won, we typically secure the timepiece from the seller within 3-5 business days. After authentication, international shipping via FedEx or DHL with full insurance takes an additional 5-7 days.</p>
                </div>

                {{-- Authentication --}}
                <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5">
                    <h3 class="flex items-center gap-4 text-lg font-black text-slate-900 dark:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy text-[10px] text-brand-gold">Q</span>
                        Do you guarantee watch authenticity?
                    </h3>
                    <p class="mt-4 pl-12 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">WatchHub is a direct gateway to primary Japanese auction houses which have their own rigorous internal authentication. Additionally, our Tokyo-based logistics team performs a secondary verification before any international transit.</p>
                </div>
            </div>

            <div class="mt-20 text-center">
                <p class="text-sm font-bold text-slate-400">Still have unanswered queries?</p>
                <div class="mt-6">
                    <a href="{{ route('contact') }}" class="inline-flex rounded-2xl bg-brand-navy px-10 py-4 text-sm font-black text-brand-gold shadow-lg transition hover:scale-105 active:scale-95 dark:bg-brand-gold dark:text-brand-navy">Open Professional Inquiry</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
