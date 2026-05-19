<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="h-full bg-zinc-50 antialiased dark:bg-zinc-950">
    <x-page-loader />




    <div class="flex h-full overflow-hidden">
        {{-- ============================================= --}}
        {{-- DESKTOP SIDEBAR (hidden on mobile) --}}
        {{-- ============================================= --}}
        <aside
            class="hidden w-[var(--sidebar-width)] shrink-0 flex-col border-r border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900 lg:flex">
            <div class="flex h-[var(--topbar-height)] items-center px-8">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                        <span class="text-xl font-black text-white">A</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-blue-600">AuctionHub</span>
                </a>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto px-6 py-8">
                <p class="mb-4 px-3 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Navigation</p>

                <x-dashboard.sidebar-item href="{{ route('user.dashboard') }}" :active="request()->routeIs('user.dashboard')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </x-slot>
                    Overview
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.auctions.index') }}" :active="request()->routeIs('user.auctions.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </x-slot>
                    Live Market
                </x-dashboard.sidebar-item>

                {{-- <x-dashboard.sidebar-item href="{{ route('user.categories.index') }}" :active="request()->routeIs('user.categories.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </x-slot>
                    Market Explorer
                </x-dashboard.sidebar-item> --}}

                <x-dashboard.sidebar-item href="{{ route('user.bids.index') }}" :active="request()->routeIs('user.bids.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </x-slot>
                    My Bids
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.watchlist.index') }}" :active="request()->routeIs('user.watchlist.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </x-slot>
                    Watchlist
                </x-dashboard.sidebar-item>

                <p class="mb-4 mt-10 px-3 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Account</p>

                <x-dashboard.sidebar-item href="{{ route('user.wallet.index') }}" :active="request()->routeIs('user.wallet.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </x-slot>
                    Wallet
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.withdrawals.index') }}" :active="request()->routeIs('user.withdrawals.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </x-slot>
                    Withdrawals
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.notifications.index') }}" :active="request()->routeIs('user.notifications.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </x-slot>
                    Alerts
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.support.index') }}" :active="request()->routeIs('user.support.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </x-slot>
                    Support
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('user.profile.edit') }}" :active="request()->routeIs('user.profile.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </x-slot>
                    Profile
                </x-dashboard.sidebar-item>
            </nav>

            <div class="border-t border-zinc-200 p-8 dark:border-white/10">
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
                <button type="button" data-confirm data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?" data-confirm-text="Logout"
                    data-confirm-type="danger" data-confirm-on-confirm="#logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        {{-- ============================================= --}}
        {{-- MAIN CONTENT AREA --}}
        {{-- ============================================= --}}
        <div class="flex flex-1 flex-col overflow-hidden">
            {{-- Top Bar (mobile hamburger + desktop layout) --}}
            <header
                class="flex h-[var(--topbar-height)] shrink-0 items-center justify-between border-b border-zinc-200 bg-white px-4 dark:border-white/10 dark:bg-zinc-900 sm:px-6 lg:px-8">
                <div class="flex flex-1 items-center gap-3 lg:gap-8">
                    {{-- Mobile menu button (visible only on < lg) --}}
                    <button id="mobileMenuToggle"
                        class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10 lg:hidden"
                        aria-label="Open menu">
                        <svg class=" h-4 w-4 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @if ($backUrl ?? false)
                        <button onclick="window.history.back()"
                            class=" h-9 items-center justify-center inline-flex rounded-lg bg-slate-100 px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </button>
                    @endif

                    <h2 class="hidden text-sm md:text-lg font-bold text-slate-900 dark:text-white lg:block">
                        {{ Str::limit($title ?? 'Collector Console', 30, '...') }}
                    </h2>

                    {{-- Global Search Bar --}}
                    <div class="max-w-md flex-1">
                        <form action="{{ route('user.auctions.index') }}" method="GET" class="relative group">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="h-4 w-4 text-zinc-400 transition-colors group-focus-within:text-blue-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Search Japan Auctions..."
                                class="w-full rounded-lg border-none bg-zinc-50 py-2.5 pl-11 pr-4 text-xs font-bold shadow-inner ring-1 ring-zinc-200 transition-all focus:bg-white focus:ring-2 focus:ring-blue-600 dark:bg-black/20 dark:ring-white/10 dark:text-white dark:placeholder:text-zinc-600" />
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-4 sm:gap-6 ml-4">
                    <div class="hidden items-center gap-2 text-sm font-bold text-slate-500 sm:flex">
                        <span
                            class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        System Online
                    </div>

                    <div class="hidden h-6 w-px bg-zinc-200 dark:bg-white/10 sm:block"></div>

                    <x-notification-dropdown />

                    <div class=" rounded-full">
                        <x-theme-toggle />
                    </div>

                    <a href="{{ route('user.profile.edit') }}"
                        class="flex h-9 w-9 overflow-hidden rounded-full ring-2 ring-blue-600/20 transition hover:ring-blue-600 sm:h-10 sm:w-10">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth('user')->user()?->name ?? 'User') }}&background=2563EB&color=FFFFFF"
                            alt="Profile">
                    </a>
                </div>
            </header>

            {{-- Scrollable Content --}}
            <main class="flex-1 overflow-y-auto p-4 pb-24 sm:p-6 lg:p-8 lg:pb-8">
                <div class="">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- MOBILE BOTTOM NAVIGATION BAR (visible only on mobile/tablet) --}}
    {{-- ============================================= --}}
    <div id="mobileBottomNav"
        class="fixed bottom-0 left-0 z-40 block w-full border-t border-zinc-200 bg-white/95 backdrop-blur-lg dark:border-white/10 dark:bg-zinc-900/95 lg:hidden">
        <div class="flex items-center justify-around px-2 py-2">
            {{-- Dashboard --}}
            <a href="{{ route('user.dashboard') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-lg px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.dashboard') ? 'text-blue-600 dark:text-blue-500' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>

            {{-- Live Market --}}
            <a href="{{ route('user.auctions.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-lg px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.auctions.*') ? 'text-blue-600 dark:text-blue-500' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-[10px] font-medium">Market</span>
            </a>

            {{-- My Bids --}}
            <a href="{{ route('user.bids.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-lg px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.bids.*') ? 'text-blue-600 dark:text-blue-500' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-medium">Bids</span>
            </a>

            {{-- Watchlist --}}
            <a href="{{ route('user.watchlist.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-lg px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.watchlist.*') ? 'text-blue-600 dark:text-blue-500' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-[10px] font-medium">Watchlist</span>
            </a>

            {{-- More menu (opens slide-out drawer) --}}
            <button id="mobileMoreButton"
                class="flex flex-col items-center justify-center gap-1 rounded-lg px-3 py-2 text-zinc-500 transition-all duration-200 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-[10px] font-medium">More</span>
            </button>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- MOBILE SLIDE-OUT MENU (drawer from left) --}}
    {{-- ============================================= --}}
    <div id="mobileDrawerOverlay"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm transition-all duration-300 dark:bg-black/70">
    </div>

    <div id="mobileDrawer"
        class="fixed inset-y-0 left-0 z-50 w-[280px] -translate-x-full transform bg-white shadow-2xl transition-transform duration-300 ease-out dark:bg-zinc-900 lg:hidden">
        <div class="flex h-full flex-col">
            {{-- Drawer header with brand and close button --}}
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-5 dark:border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600">
                        <span class="text-lg font-black text-white">A</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-blue-600">AuctionHub</span>
                </div>
                <button id="closeDrawerBtn"
                    class="rounded-full p-1.5 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Drawer navigation links (full menu) --}}
            <nav class="flex-1 overflow-y-auto px-4 py-6">
                <div class="space-y-1">
                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Navigation</p>

                    <a href="{{ route('user.dashboard') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Overview
                    </a>

                    <a href="{{ route('user.auctions.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.auctions.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Live Market
                    </a>

                    <a href="{{ route('user.bids.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.bids.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        My Bids
                    </a>

                    <a href="{{ route('user.watchlist.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.watchlist.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Watchlist
                    </a>

                    <div class="my-5 h-px bg-slate-200 dark:bg-white/10"></div>

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Account</p>

                    <a href="{{ route('user.wallet.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.wallet.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Wallet
                    </a>

                    <a href="{{ route('user.withdrawals.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.withdrawals.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                        Withdrawals
                    </a>

                    <a href="{{ route('user.notifications.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.notifications.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Alerts
                    </a>

                    <a href="{{ route('user.support.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.support.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Support
                    </a>

                    <a href="{{ route('user.profile.edit') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.profile.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile
                    </a>
                </div>
            </nav>

            {{-- Drawer logout button --}}
            <div class="border-t border-slate-200 p-6 dark:border-white/10">
                <button type="button" data-confirm data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?"
                    data-confirm-text="Logout" data-confirm-type="danger"
                    data-confirm-on-confirm="#mobile-logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Logout
                </button>
                <form id="mobile-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- Mobile Navigation JavaScript --}}
    {{-- ============================================= --}}
    <script>
        (function() {
            // Elements
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMoreButton = document.getElementById('mobileMoreButton');
            const drawerOverlay = document.getElementById('mobileDrawerOverlay');
            const drawer = document.getElementById('mobileDrawer');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const body = document.body;

            function openDrawer() {
                if (!drawer || !drawerOverlay) return;
                drawer.classList.remove('-translate-x-full');
                drawerOverlay.classList.remove('hidden');
                body.style.overflow = 'hidden';
                // Prevent body scroll
                document.addEventListener('touchmove', preventScroll, {
                    passive: false
                });
            }

            function closeDrawer() {
                if (!drawer || !drawerOverlay) return;
                drawer.classList.add('-translate-x-full');
                drawerOverlay.classList.add('hidden');
                body.style.overflow = '';
                document.removeEventListener('touchmove', preventScroll);
            }

            function preventScroll(e) {
                e.preventDefault();
            }

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', openDrawer);
            }

            if (mobileMoreButton) {
                mobileMoreButton.addEventListener('click', openDrawer);
            }

            if (closeDrawerBtn) {
                closeDrawerBtn.addEventListener('click', closeDrawer);
            }

            if (drawerOverlay) {
                drawerOverlay.addEventListener('click', closeDrawer);
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && drawer && !drawer.classList.contains('-translate-x-full')) {
                    closeDrawer();
                }
            });

            const drawerLinks = document.querySelectorAll('.drawer-nav-link');
            drawerLinks.forEach(link => {
                link.addEventListener('click', function() {
                    closeDrawer();
                });
            });


            function adjustMainPadding() {
                const mainElement = document.querySelector('main');
                const bottomNav = document.getElementById('mobileBottomNav');
                if (mainElement && bottomNav && window.innerWidth < 1024) {
                    mainElement.style.paddingBottom = '80px';
                } else if (mainElement) {
                    mainElement.style.paddingBottom = '';
                }
            }

            adjustMainPadding();
            window.addEventListener('resize', adjustMainPadding);

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeDrawer();
                    body.style.overflow = '';
                } else {
                    adjustMainPadding();
                }
            });
        })();
    </script>
    <x-confirm-dialog />
    <x-toast />
    <x-cookie-consent />

    <script>
        (function() {
            const initGlobalScrollTracking = () => {
                const scrollKey = 'globalScroll_' + window.location.pathname + window.location.search;
                const scrollElement = document.querySelector('main') || window;

                // 1. Instantly restore if we have a value
                const savedScroll = sessionStorage.getItem(scrollKey);
                if (savedScroll) {
                    setTimeout(() => {
                        if (scrollElement === window) {
                            window.scrollTo({
                                top: parseInt(savedScroll),
                                behavior: 'instant'
                            });
                        } else {
                            scrollElement.scrollTop = parseInt(savedScroll);
                        }
                    }, 50);
                }

                // 2. Track scroll events continuously
                scrollElement.addEventListener('scroll', () => {
                    const scrollPos = scrollElement === window ? window.scrollY : scrollElement.scrollTop;
                    sessionStorage.setItem(scrollKey, scrollPos);
                }, {
                    passive: true
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initGlobalScrollTracking);
            } else {
                initGlobalScrollTracking();
            }

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) initGlobalScrollTracking();
            });

            document.addEventListener('livewire:navigated', initGlobalScrollTracking);
        })();
    </script>
</body>

</html>
