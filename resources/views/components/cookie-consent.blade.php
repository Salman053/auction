@php
    $hasConsent = request()->cookie('cookie_consent') || (auth('user')->check() && auth('user')->user()->cookies_accepted);
@endphp

@if(!$hasConsent)
<div x-data="cookieConsent()" x-show="show" x-cloak
    class="fixed inset-x-0 bottom-0 z-[100] p-4 sm:p-6 lg:p-8"
    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-20"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-20">
    <div class="mx-auto max-w-4xl">
        <div
            class="relative overflow-hidden rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-slate-900/5 dark:bg-zinc-900 dark:ring-white/10 sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-gold/10 text-brand-gold">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-black tracking-tight text-slate-900 dark:text-white">Privacy &
                            Personalization</h2>
                    </div>
                    <p class="mt-3 text-sm font-medium leading-relaxed text-slate-500 dark:text-zinc-400">
                        We use cookies to enhance your horology portfolio experience. This includes essential site
                        functions, personalized analytics, and tailored notifications. By clicking "Accept All", you
                        consent to our use of these technologies.
                    </p>
                    <button @click="showInfo = !showInfo"
                        class="mt-2 text-xs font-bold text-brand-gold hover:underline">
                        Learn more about what we collect
                    </button>
                </div>
                <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                    <button @click="save(false)"
                        class="rounded-2xl bg-slate-50 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500 transition hover:bg-slate-100 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
                        Reject All
                    </button>
                    <button @click="save(true)"
                        class="rounded-2xl bg-brand-navy px-8 py-3 text-xs font-black uppercase tracking-widest text-brand-gold shadow-lg transition hover:scale-105 active:scale-95 dark:bg-brand-gold dark:text-brand-navy">
                        Accept All
                    </button>
                </div>
            </div>

            {{-- Expanded Info section --}}
            <div x-show="showInfo" x-collapse class="mt-6 border-t border-slate-50 pt-6 dark:border-white/5">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-brand-gold">What we collect
                        </h3>
                        <ul class="mt-3 space-y-2 text-xs font-medium text-slate-500 dark:text-zinc-400">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                Authenticated session tokens for secure access.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                Preferences such as language and bidding alerts.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                Interaction data to improve platform performance.
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-brand-gold">Why we collect it
                        </h3>
                        <ul class="mt-3 space-y-2 text-xs font-medium text-slate-500 dark:text-zinc-400">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                To provide real-time updates on Yahoo Auctions.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                To verify identity and prevent fraudulent bidding.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-gold"></span>
                                To personalize your horology dashboard.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cookieConsent', () => ({
            show: false,
            showInfo: false,
            init() {
                setTimeout(() => {
                    this.show = true;
                }, 1000);
            },
            async save(accepted) {
                try {
                    const response = await fetch("{{ route('cookie-consent.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            accepted: accepted,
                            settings: {
                                analytics: accepted,
                                marketing: accepted,
                                functional: true
                            }
                        })
                    });

                    if (response.ok) {
                        this.show = false;
                    }
                } catch (error) {
                    console.error('Failed to save cookie consent:', error);
                    // Fallback: just hide the popup
                    this.show = false;
                }
            }
        }));
    });
</script>
@endif
