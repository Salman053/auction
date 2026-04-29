<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>

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

<body class="h-full bg-slate-50 antialiased dark:bg-zinc-950">
    <div class="flex h-full overflow-hidden">
        {{-- ============================================= --}}
        {{-- DESKTOP SIDEBAR (hidden on mobile) --}}
        {{-- ============================================= --}}
        <aside
            class="hidden w-[var(--sidebar-width)] shrink-0 flex-col border-r border-slate-200 bg-white dark:border-white/10 dark:bg-zinc-900 lg:flex">
            <div class="flex h-[var(--topbar-height)] items-center px-8">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-navy dark:bg-brand-gold">
                        <span class="text-xl font-black text-brand-gold dark:text-brand-navy">W</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-brand-navy dark:text-white">WatchHub</span>
                </a>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto px-6 py-8">
                <p class="mb-4 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Navigation</p>

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

                <x-dashboard.sidebar-item href="{{ route('user.bids.index') }}" :active="request()->routeIs('user.bids.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </x-slot>
                    My Bids
                </x-dashboard.sidebar-item>

                <p class="mb-4 mt-10 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Account</p>

                <x-dashboard.sidebar-item href="{{ route('user.wallet.index') }}" :active="request()->routeIs('user.wallet.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </x-slot>
                    Wallet
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

            <div class="border-t border-slate-200 p-8 dark:border-white/10">
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
                <button type="button"
                    data-confirm
                    data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?"
                    data-confirm-text="Logout"
                    data-confirm-type="danger"
                    data-confirm-on-confirm="#logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
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
                class="flex h-[var(--topbar-height)] shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 dark:border-white/10 dark:bg-zinc-900 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    {{-- Mobile menu button (visible only on < lg) --}}
                    <button id="mobileMenuToggle"
                        class="rounded-xl p-2 text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10 lg:hidden"
                        aria-label="Open menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title ?? 'Collector Console' }}
                    </h2>
                </div>

                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="hidden items-center gap-2 text-sm font-bold text-slate-500 sm:flex">
                        <span
                            class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        System Online
                    </div>

                    <div class="hidden h-6 w-px bg-slate-200 dark:bg-white/10 sm:block"></div>

                    <x-notification-dropdown />

                    <div class="bg-black rounded-full">
                        <x-theme-toggle />
                    </div>

                    <a href="{{ route('user.profile.edit') }}"
                        class="flex h-9 w-9 overflow-hidden rounded-full ring-2 ring-brand-gold/20 transition hover:ring-brand-gold sm:h-10 sm:w-10">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0F172A&color=D4AF37"
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
        class="fixed bottom-0 left-0 z-40 block w-full border-t border-slate-200 bg-white/95 backdrop-blur-lg dark:border-white/10 dark:bg-zinc-900/95 lg:hidden">
        <div class="flex items-center justify-around px-2 py-2">
            {{-- Dashboard --}}
            <a href="{{ route('user.dashboard') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.dashboard') ? 'text-brand-gold dark:text-brand-gold' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>

            {{-- Live Market --}}
            <a href="{{ route('user.auctions.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.auctions.*') ? 'text-brand-gold dark:text-brand-gold' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-[10px] font-medium">Market</span>
            </a>

            {{-- My Bids --}}
            <a href="{{ route('user.bids.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.bids.*') ? 'text-brand-gold dark:text-brand-gold' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-medium">Bids</span>
            </a>

            {{-- Wallet --}}
            <a href="{{ route('user.wallet.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('user.wallet.*') ? 'text-brand-gold dark:text-brand-gold' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="text-[10px] font-medium">Wallet</span>
            </a>

            {{-- More menu (opens slide-out drawer) --}}
            <button id="mobileMoreButton"
                class="flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-slate-500 transition-all duration-200 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10">
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
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-navy dark:bg-brand-gold">
                        <span class="text-lg font-black text-brand-gold dark:text-brand-navy">W</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-brand-navy dark:text-white">WatchHub</span>
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
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.dashboard') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Overview
                    </a>

                    <a href="{{ route('user.auctions.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.auctions.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Live Market
                    </a>

                    <a href="{{ route('user.bids.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.bids.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        My Bids
                    </a>

                    <div class="my-5 h-px bg-slate-200 dark:bg-white/10"></div>

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Account</p>

                    <a href="{{ route('user.wallet.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.wallet.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Wallet
                    </a>

                    <a href="{{ route('user.notifications.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.notifications.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Alerts
                    </a>

                    <a href="{{ route('user.profile.edit') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('user.profile.*') ? 'bg-brand-gold/10 text-brand-gold dark:bg-brand-gold/20' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
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
                <button type="button"
                    data-confirm
                    data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?"
                    data-confirm-text="Logout"
                    data-confirm-type="danger"
                    data-confirm-on-confirm="#mobile-logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
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

            // Helper: open drawer
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

            // Helper: close drawer
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

            // Open drawer from top bar hamburger
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', openDrawer);
            }

            // Open drawer from bottom nav "More" button
            if (mobileMoreButton) {
                mobileMoreButton.addEventListener('click', openDrawer);
            }

            // Close drawer via X button
            if (closeDrawerBtn) {
                closeDrawerBtn.addEventListener('click', closeDrawer);
            }

            // Close drawer when clicking overlay
            if (drawerOverlay) {
                drawerOverlay.addEventListener('click', closeDrawer);
            }

            // Close drawer on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && drawer && !drawer.classList.contains('-translate-x-full')) {
                    closeDrawer();
                }
            });

            // Optional: close drawer after clicking any nav link inside drawer (for better UX)
            const drawerLinks = document.querySelectorAll('.drawer-nav-link');
            drawerLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Small delay to allow navigation to start, but drawer closes instantly
                    closeDrawer();
                });
            });

            // Bottom nav active highlight is already handled by blade classes, 
            // but we add a small adjustment for dynamic route highlighting
            // (works out of the box because of Laravel route conditions)

            // Adjust bottom padding on main content when bottom nav is visible
            // (prevents content being hidden behind the fixed bottom bar)
            function adjustMainPadding() {
                const mainElement = document.querySelector('main');
                const bottomNav = document.getElementById('mobileBottomNav');
                if (mainElement && bottomNav && window.innerWidth < 1024) {
                    mainElement.style.paddingBottom = '80px';
                } else if (mainElement) {
                    mainElement.style.paddingBottom = '';
                }
            }

            // Run on load and resize
            adjustMainPadding();
            window.addEventListener('resize', adjustMainPadding);

            // Also close drawer on orientation change to avoid weird states
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
</body>

</html>
