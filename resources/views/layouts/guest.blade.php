<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Japanese Proxy Auctions' }} · {{ config('app.name') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-zinc-50 antialiased dark:bg-zinc-950">
    <x-page-loader />
    <header
        class="sticky top-0 z-50 border-b border-zinc-200 bg-white/95 backdrop-blur-md dark:border-white/5 dark:bg-zinc-900/95">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            {{-- Top Header Row --}}
            <div class="flex items-center justify-between py-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tighter text-blue-600">{{ config('app.name') }}</span>
                </a>

                {{-- Search Bar --}}
                <div class="mx-8 hidden flex-1 max-w-2xl lg:block">
                    <form action="{{ route('auctions.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search for any product or brand"
                            class="w-full rounded-full border-zinc-200 bg-zinc-50 py-2.5 pl-5 pr-12 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                        <button type="submit"
                            class="absolute right-1 top-1 flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-white transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 lg:gap-6">
                    <div class="hidden items-center gap-2 text-xs font-medium text-zinc-500 lg:flex">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="text-[10px] leading-tight text-zinc-400">Deliver to</p>
                            @php
                                $shippingLocations = $shippingLocations ?? \App\Models\ShippingRate::orderBy('country')->get();
                                $locationLabels = collect($shippingLocations)->map(fn($l) => is_string($l) ? $l : $l->country)->unique()->values();
                            @endphp
                            @if ($locationLabels->isNotEmpty())
                                <p class="font-bold text-zinc-900 dark:text-white">{{ $locationLabels->join(', ') }}</p>
                            @else
                                <p class="font-bold text-zinc-900 dark:text-white">Worldwide</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-theme-toggle />

                        @auth('user')
                            <a href="{{ route('user.dashboard') }}"
                                class="flex flex-col items-center gap-0.5 text-zinc-600 transition hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Account</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex flex-col items-center gap-0.5 text-zinc-600 transition hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Sign In</span>
                            </a>
                        @endauth

                        <a href="{{ route('user.watchlist.index') }}"
                            class="relative flex flex-col items-center gap-0.5 text-zinc-600 transition hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">Watchlist</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bottom Header Row / Nav --}}
            <nav
                class="flex items-center gap-6 overflow-x-auto border-t border-zinc-100 py-3 text-sm font-medium scrollbar-hide dark:border-white/5">
                <a href="{{ route('auctions.index') }}"
                    class="flex items-center gap-1.5 whitespace-nowrap text-[11px] font-black uppercase tracking-widest text-zinc-900 dark:text-white hover:text-blue-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    All Categories
                </a>
                @php
                    $navCategories = $navCategories ?? \App\Models\Category::where('depth', 0)->orderBy('priority', 'desc')->orderBy('name')->limit(8)->get();
                @endphp
                @foreach ($navCategories as $navCat)
                    <a href="{{ route('auctions.index', ['category' => $navCat->yahoo_category_id]) }}"
                        class="whitespace-nowrap text-[11px] font-black uppercase tracking-widest text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400">{{ $navCat->name }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="">
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-zinc-200 bg-white py-20 dark:border-white/5 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                            <span class="text-lg font-black text-white">A</span>
                        </div>
                        <span
                            class="text-lg font-black tracking-tighter text-zinc-900 dark:text-white">AuctionHub</span>
                    </a>
                    <p class="mt-6 max-w-sm text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                        The premier digital gateway to Japanese auctions. Direct access to Yahoo Japan Auctions with
                        professional logistics and transparent bidding across all categories.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Market</h4>
                    <ul class="mt-6 space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <li><a href="{{ route('auctions.index') }}" class="hover:text-blue-600 transition">Live
                                Catalog</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Winning Bids</a>
                        </li>
                        <li><a href="#" class="hover:text-blue-600 transition">Featured
                                Items</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Account
                    </h4>
                    <ul class="mt-6 space-y-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition">Account Login</a>
                        </li>
                        <li><a href="{{ route('faq') }}" class="hover:text-blue-600 transition">Support Center</a>
                        </li>
                        <li><a href="{{ route('contact') }}" class="hover:text-blue-600 transition">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-20 flex flex-col justify-between gap-6 border-t border-zinc-100 pt-10 dark:border-white/5 sm:flex-row sm:items-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">© {{ date('Y') }}
                    AuctionHub Japan. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#"
                        class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-blue-600 transition">Privacy
                        Policy</a>
                    <a href="#"
                        class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-blue-600 transition">Terms
                        of Service</a>
                </div>
            </div>
        </div>
    </footer>
    <x-confirm-dialog />
    <x-toast />
    <x-cookie-consent />
</body>

</html>
