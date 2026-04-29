<x-guest-layout :title="'Inquiry Terminal'">
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-8">
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-2">
                {{-- Info Side --}}
                <div class="lg:max-w-md">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-gold">Direct Inquiry</p>
                    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 dark:text-white sm:text-6xl">Connect with the Hub.</h1>
                    <p class="mt-8 text-lg font-medium leading-relaxed text-slate-500 dark:text-zinc-400">Our administrative team is available 24/7 for high-value procurement inquiries and technical support.</p>

                    <div class="mt-12 space-y-10">
                        <div class="flex items-start gap-6">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-navy text-brand-gold">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Administrative Email</h3>
                                <p class="mt-2 text-slate-500 dark:text-zinc-400">concierge@watchhub.jp</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-navy text-brand-gold">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Tokyo Hub</h3>
                                <p class="mt-2 text-slate-500 dark:text-zinc-400 font-medium">Shiodome City Center 15F,<br>Minato-ku, Tokyo, 105-7115 Japan</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 rounded-3xl bg-slate-50 p-8 dark:bg-zinc-900/50">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Response Protocol</p>
                        <p class="mt-4 text-xs font-bold leading-relaxed text-slate-900 dark:text-white">Active session members receive priority resolution within 60 minutes. Guest inquiries are processed within 24 hours.</p>
                    </div>
                </div>

                {{-- Form Side --}}
                <div class="rounded-[3rem] bg-white p-10 shadow-2xl ring-1 ring-slate-100 dark:bg-zinc-900 dark:ring-white/5 lg:p-12">
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Full Identity</label>
                                <input id="name" name="name" type="text" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            </div>
                            <div>
                                <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Connect Email</label>
                                <input id="email" name="email" type="email" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Inquiry Type</label>
                            <select id="subject" name="subject" class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white">
                                <option>Market Access Support</option>
                                <option>Logistics & Shipping</option>
                                <option>Custodial Wallet Verification</option>
                                <option>Strategic Partnership</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Inquiry Details</label>
                            <textarea id="message" name="message" rows="5" required class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-bold shadow-inner ring-1 ring-slate-200 focus:ring-2 focus:ring-brand-gold dark:bg-black/20 dark:ring-white/10 dark:text-white"></textarea>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-brand-navy py-5 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] active:scale-95 dark:bg-brand-gold dark:text-brand-navy">
                            Transmit Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
