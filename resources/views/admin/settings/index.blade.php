<x-admin-layout :title="'System Settings'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <h1 class="text-2xl font-semibold tracking-tight">System Settings</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Global defaults for bidding and scraping.</p>

        @if (session('status') === 'saved')
            <div class="mt-4 rounded-2xl border border-green-600/20 bg-green-600/10 px-4 py-3 text-sm text-green-800 dark:text-green-200">Saved.</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
            @csrf
            <div>
                <label class="text-sm font-semibold" for="default_bidding_multiplier_percent">Default bidding multiplier (%)</label>
                <input id="default_bidding_multiplier_percent" name="default_bidding_multiplier_percent" type="number" min="100" max="2000" value="{{ old('default_bidding_multiplier_percent', $defaultMultiplier) }}" class="mt-1 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5" />
                @error('default_bidding_multiplier_percent') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm font-semibold" for="scrape_interval_minutes">Scrape interval (minutes)</label>
                <input id="scrape_interval_minutes" name="scrape_interval_minutes" type="number" min="1" max="60" value="{{ old('scrape_interval_minutes', $scrapeIntervalMinutes) }}" class="mt-1 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none focus:border-[#1877f2] focus:ring-2 focus:ring-[#1877f2]/15 dark:border-white/10 dark:bg-white/5" />
                @error('scrape_interval_minutes') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 rounded-2xl border border-zinc-200 p-4 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#635BFF] text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="flex-grow">
                        <label class="text-sm font-bold text-slate-900 dark:text-white" for="stripe_payment_enabled">Enable Stripe Live Payments</label>
                        <p class="text-xs text-slate-500">If toggled OFF, the system uses instant auto-approval simulations.</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" id="stripe_payment_enabled" name="stripe_payment_enabled" value="1" class="peer sr-only" {{ old('stripe_payment_enabled', $stripePaymentEnabled) ? 'checked' : '' }}>
                        <div class="peer flex h-6 w-11 items-center rounded-full bg-slate-200 after:absolute after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#1877f2] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none focus:ring-2 focus:ring-[#1877f2] dark:bg-white/10"></div>
                    </label>
                </div>
                @error('stripe_payment_enabled') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="lg:col-span-2">
                <button type="submit" class="rounded-full bg-[#1877f2] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#166fe5]">Save settings</button>
            </div>
        </form>
    </div>
</x-admin-layout>

