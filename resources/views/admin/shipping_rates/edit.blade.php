<x-admin-layout :title="'Edit Shipping Rate'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.shipping_rates.index') }}" class="rounded-full bg-zinc-100 p-2 text-zinc-600 transition hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Edit Shipping Rate</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Update port, country, or fee for {{ $shippingRate->name }}.</p>
            </div>
        </div>
    </div>

    <div class="mt-6 max-w-2xl rounded-2xl bg-white p-8 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <form method="POST" action="{{ route('admin.shipping_rates.update', $shippingRate) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">Display Name (e.g. Dubai Main Port)</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $shippingRate->name) }}"
                        required
                        class="mt-2 block w-full rounded-xl border-zinc-200 bg-zinc-50 px-4 py-3 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/15 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white/25"
                    />
                    @error('name')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">Country</label>
                    <input
                        type="text"
                        name="country"
                        id="country"
                        value="{{ old('country', $shippingRate->country) }}"
                        class="mt-2 block w-full rounded-xl border-zinc-200 bg-zinc-50 px-4 py-3 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/15 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white/25"
                    />
                    @error('country')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="port" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">Port Name (Optional)</label>
                    <input
                        type="text"
                        name="port"
                        id="port"
                        value="{{ old('port', $shippingRate->port) }}"
                        class="mt-2 block w-full rounded-xl border-zinc-200 bg-zinc-50 px-4 py-3 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/15 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white/25"
                    />
                    @error('port')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fee_yen" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300">Shipping Fee (JPY)</label>
                    <div class="relative mt-2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-zinc-500">¥</span>
                        </div>
                        <input
                            type="number"
                            name="fee_yen"
                            id="fee_yen"
                            value="{{ old('fee_yen', $shippingRate->fee_yen) }}"
                            required
                            min="0"
                            class="block w-full rounded-xl border-zinc-200 bg-zinc-50 py-3 pl-8 pr-4 text-sm font-bold outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/15 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white/25"
                        />
                    </div>
                    @error('fee_yen')
                        <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="rounded-full bg-rose-600 px-10 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-rose-700">
                    Update Shipping Rate
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
