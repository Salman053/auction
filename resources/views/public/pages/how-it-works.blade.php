<x-guest-layout :title="'Market Protocol'">
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold">Operational Flow</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white sm:text-6xl">Bidding Protocol.</h1>
                <p class="mt-6 text-lg font-medium leading-relaxed text-slate-500 dark:text-zinc-400">Master the streamlined process of international luxury procurement.</p>
            </div>

            <div class="mt-24 space-y-32">
                {{-- Step 1 --}}
                <div class="flex flex-col gap-16 lg:flex-row lg:items-center">
                    <div class="lg:w-1/2">
                        <div class="mb-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-navy text-xl font-black text-brand-gold">01</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Initialize Capital</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Deposit Yen into your secure WatchHub wallet. This capital isn't just for purchase; it's the foundation of your bidding power. Our system instantly grants you a 500% bidding multiplier based on your net balance.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">Instant Yen Conversion</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">5x Bidding Multiplier</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="overflow-hidden rounded-[3rem] bg-slate-100 p-12 dark:bg-zinc-900">
                             {{-- Placeholder for nice visual or icon grid --}}
                             <div class="h-64 flex items-center justify-center text-slate-300 dark:text-zinc-700 italic">Financial Module Interface Graphic</div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col gap-16 lg:flex-row-reverse lg:items-center">
                    <div class="lg:w-1/2">
                        <div class="mb-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-navy text-xl font-black text-brand-gold">02</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Real-Time Execution</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Monitor thousands of live Yahoo Japan listings through our professional console. Execute manual bids or set advanced "Sniper" protocols to secure your position in the final seconds of high-value auctions.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">High-Frequency Market Sync</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">One-Click Bidding Engine</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="overflow-hidden rounded-[3rem] bg-brand-navy p-12 text-white">
                             <div class="h-64 flex items-center justify-center text-white/10 italic">Biding Console visualization</div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col gap-16 lg:flex-row lg:items-center">
                    <div class="lg:w-1/2">
                        <div class="mb-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-navy text-xl font-black text-brand-gold">03</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Secure Fulfillment</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Upon victory, our Tokyo hub manages the entire fulfillment cycle. From seller settlement and in-person authentication to international logistics and customs clearance — your piece arrives insured and ready for the vault.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">White-Glove Logistics</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">Global Insured Shipping</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="overflow-hidden rounded-[3rem] bg-slate-900 p-12 text-white">
                             <div class="h-64 flex items-center justify-center text-white/20 italic">Global Logistics Mapping Graphic</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-32 text-center">
                <a href="{{ route('register') }}" class="group relative inline-flex items-center gap-4 overflow-hidden rounded-2xl bg-brand-gold px-12 py-6 text-sm font-black text-brand-navy shadow-2xl transition hover:scale-105 active:scale-95">
                    Open Your Bidding Console
                    <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
