<x-guest-layout :title="'Market Protocol'">
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold">Operational Flow</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white sm:text-6xl">Bidding
                    Protocol.</h1>
                <p class="mt-6 text-lg font-medium leading-relaxed text-slate-500 dark:text-zinc-400">Master the
                    streamlined process of international luxury procurement.</p>
            </div>

            <div class="mt-24 space-y-32">
                {{-- Step 1 --}}
                <div class="flex flex-col gap-16 lg:flex-row lg:items-center">
                    <div class="lg:w-1/2">
                        <div
                            class="mb-8 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-navy text-xl font-black text-brand-gold">
                            01</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Initialize Capital</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Deposit Yen into your
                            secure WatchHub wallet. This capital isn't just for purchase; it's the foundation of your
                            bidding power. Our system instantly grants you a 500% bidding multiplier based on your net
                            balance.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">Instant Yen
                                    Conversion</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">5x Bidding
                                    Multiplier</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="overflow-hidden rounded-[3rem] bg-slate-100 p-8 dark:bg-zinc-900 shadow-xl">
                            <img src="{{ asset('images/deposit.png') }}" alt="Fund Account Interface"
                                class="w-full h-auto rounded-lg">
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col gap-16 lg:flex-row-reverse lg:items-center">
                    <div class="lg:w-1/2">
                        <div
                            class="mb-8 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-navy text-xl font-black text-brand-gold">
                            02</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Real-Time Execution</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Monitor thousands of
                            live Yahoo Japan listings through our professional console. Execute manual bids or set
                            advanced "Sniper" protocols to secure your position in the final seconds of high-value
                            auctions.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">High-Frequency Market
                                    Sync</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">One-Click Bidding
                                    Engine</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-8 shadow-2xl">
                            {{-- Live Console Simulation --}}
                            <div class="font-mono text-[10px] space-y-2">
                                <div class="flex justify-between text-brand-gold font-bold mb-4">
                                    <span>CONSOLE_ACTIVE</span>
                                    <span>SYNC: OK</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach (['#A9922 - BID: ¥450,000', '#B2819 - BID: ¥120,500', '#C1102 - BID: ¥3,200,000', '#D0091 - BID: ¥95,000'] as $item)
                                        <div class="flex justify-between text-white/60 animate-pulse">
                                            <span>{{ $item }}</span>
                                            <span class="text-emerald-400">STATUS_LIVE</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-8 border-t border-white/10 pt-4 text-center">
                                    <span class="text-brand-gold tracking-widest font-bold">EXECUTION_READY</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col gap-16 lg:flex-row lg:items-center">
                    <div class="lg:w-1/2">
                        <div
                            class="mb-8 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-navy text-xl font-black text-brand-gold">
                            03</div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Secure Fulfillment</h2>
                        <p class="mt-6 text-sm leading-relaxed text-slate-500 dark:text-zinc-400">Upon victory, our
                            Tokyo hub manages the entire fulfillment cycle. From seller settlement and in-person
                            authentication to international logistics and customs clearance — your piece arrives insured
                            and ready for the vault.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">White-Glove
                                    Logistics</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">Global Insured
                                    Shipping</span>
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div
                            class="overflow-hidden rounded-[3rem] bg-slate-900 p-12 text-white shadow-2xl flex flex-col items-center justify-center">
                            <div class="flex space-x-4 mb-4">
                                <div
                                    class="w-16 h-16 rounded-full bg-brand-gold flex items-center justify-center text-brand-navy font-black">
                                    JP</div>
                                <div class="w-8 h-16 flex items-center justify-center text-white/50">&rarr;</div>
                                <div
                                    class="w-16 h-16 rounded-full bg-brand-navy border-2 border-brand-gold flex items-center justify-center text-brand-gold font-black">
                                    INTL</div>
                            </div>
                            <span class="text-xs font-bold text-white/50 tracking-widest uppercase">Global Fulfillment
                                Bridge</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video Tutorial Section --}}
            <div class="mt-32">
                <div class="mx-auto max-w-2xl text-center mb-12">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">Platform Walkthrough</h2>
                    <p class="mt-4 text-slate-500 dark:text-zinc-400">See the protocol in action with a quick guided
                        overview.</p>
                </div>
                <div class="mx-auto max-w-5xl">
                    <div class="aspect-video w-full rounded-3xl overflow-hidden shadow-2xl bg-slate-900">
                        <video controls class="h-full w-full object-cover">
                            <source src="{{ asset('media/tutorial.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>

            <div class="mt-32 text-center">
                <a href="{{ route('register') }}"
                    class="group relative inline-flex items-center gap-4 overflow-hidden rounded-lg bg-brand-gold px-12 py-6 text-sm font-black text-brand-navy shadow-2xl transition hover:scale-105 active:scale-95">
                    Open Your Bidding Console
                    <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
