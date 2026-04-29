<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>{{ $title ?? 'Admin' }} · {{ config('app.name') }}</title>

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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-600">
                        <span class="text-xl font-black text-white">A</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-slate-900 dark:text-white">AdminHub</span>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-6 py-8">
                <p class="mb-4 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">System Control</p>

                <x-dashboard.sidebar-item href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </x-slot>
                    Overview
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </x-slot>
                    Collectors
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.auctions.index') }}" :active="request()->routeIs('admin.auctions.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </x-slot>
                    Market Monitoring
                </x-dashboard.sidebar-item>

                <p class="mb-4 mt-8 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Financials</p>

                <x-dashboard.sidebar-item href="{{ route('admin.deposits.index') }}" :active="request()->routeIs('admin.deposits.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </x-slot>
                    Deposits
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.withdrawals.index') }}" :active="request()->routeIs('admin.withdrawals.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </x-slot>
                    Withdrawals
                </x-dashboard.sidebar-item>

                <p class="mb-4 mt-8 px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Operations</p>

                <x-dashboard.sidebar-item href="{{ route('admin.proxies.index') }}" :active="request()->routeIs('admin.proxies.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </x-slot>
                    Proxies
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.scraping-logs.index') }}" :active="request()->routeIs('admin.scraping-logs.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot>
                    Scraping Health
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.shipping_rates.index') }}" :active="request()->routeIs('admin.shipping_rates.*')">
                    <x-slot name="icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                    Shipping Rates
                </x-dashboard.sidebar-item>

                <x-dashboard.sidebar-item href="{{ route('admin.profile.edit') }}" :active="request()->routeIs('admin.profile.*')">
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
                <form id="admin-logout-form" method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                <button type="button"
                    data-confirm
                    data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?"
                    data-confirm-text="Logout"
                    data-confirm-type="danger"
                    data-confirm-on-confirm="#admin-logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Admin Logout
                </button>
                </form>
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

                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title ?? 'Admin Control' }}</h2>
                </div>

                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="hidden items-center gap-2 text-sm font-bold text-slate-500 sm:flex">
                        <span
                            class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        System Secure
                    </div>

                    <div class="hidden h-6 w-px bg-slate-200 dark:bg-white/10 sm:block"></div>

                    <x-notification-dropdown />

                    <div class="bg-black rounded-full">
                        <x-theme-toggle />
                    </div>

                    <a href="{{ route('admin.profile.edit') }}"
                        class="flex h-9 w-9 overflow-hidden rounded-full ring-2 ring-rose-500/20 transition hover:ring-rose-500 sm:h-10 sm:w-10">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=E11D48&color=FFFFFF"
                            alt="Admin">
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
            {{-- Dashboard Overview --}}
            <a href="{{ route('admin.dashboard') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'text-rose-600 dark:text-rose-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>

            {{-- Collectors (Users) --}}
            <a href="{{ route('admin.users.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-rose-600 dark:text-rose-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-[10px] font-medium">Collectors</span>
            </a>

            {{-- Market Monitoring --}}
            <a href="{{ route('admin.auctions.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('admin.auctions.*') ? 'text-rose-600 dark:text-rose-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-[10px] font-medium">Market</span>
            </a>

            {{-- Financials (Deposits + Withdrawals combined shortcut or default to deposits) --}}
            <a href="{{ route('admin.deposits.index') }}"
                class="mobile-nav-item flex flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('admin.deposits.*') || request()->routeIs('admin.withdrawals.*') ? 'text-rose-600 dark:text-rose-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                </svg>
                <span class="text-[10px] font-medium">Finance</span>
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
    {{-- MOBILE SLIDE-OUT DRAWER MENU (left side) --}}
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
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600">
                        <span class="text-lg font-black text-white">A</span>
                    </div>
                    <span class="text-xl font-black tracking-tighter text-slate-900 dark:text-white">AdminHub</span>
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
                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">System Control
                    </p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Overview
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Collectors
                    </a>

                    <a href="{{ route('admin.auctions.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.auctions.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Market Monitoring
                    </a>

                    <div class="my-5 h-px bg-slate-200 dark:bg-white/10"></div>

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Financials</p>

                    <a href="{{ route('admin.deposits.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.deposits.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                        Deposits
                    </a>

                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.withdrawals.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                        Withdrawals
                    </a>

                    <div class="my-5 h-px bg-slate-200 dark:bg-white/10"></div>

                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Operations</p>

                    <a href="{{ route('admin.proxies.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.proxies.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Proxies
                    </a>

                    <a href="{{ route('admin.scraping-logs.index') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.scraping-logs.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Scraping Health
                    </a>

                    <a href="{{ route('admin.profile.edit') }}"
                        class="drawer-nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all {{ request()->routeIs('admin.profile.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10' }}">
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
                <form id="admin-mobile-logout-form" method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                <button type="button"
                    data-confirm
                    data-confirm-title="Confirm Logout"
                    data-confirm-message="Are you sure you want to end your current session?"
                    data-confirm-text="Logout"
                    data-confirm-type="danger"
                    data-confirm-on-confirm="#admin-mobile-logout-form"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Admin Logout
                </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- Mobile Navigation JavaScript (Drawer + Bottom Bar) --}}
    {{-- ============================================= --}}
    <script>
        (function() {
            // DOM Elements
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMoreButton = document.getElementById('mobileMoreButton');
            const drawerOverlay = document.getElementById('mobileDrawerOverlay');
            const drawer = document.getElementById('mobileDrawer');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const body = document.body;

            // Helper: prevent body scroll when drawer open
            function preventScroll(e) {
                e.preventDefault();
            }

            // Open drawer
            function openDrawer() {
                if (!drawer || !drawerOverlay) return;
                drawer.classList.remove('-translate-x-full');
                drawerOverlay.classList.remove('hidden');
                body.style.overflow = 'hidden';
                document.addEventListener('touchmove', preventScroll, {
                    passive: false
                });
            }

            // Close drawer
            function closeDrawer() {
                if (!drawer || !drawerOverlay) return;
                drawer.classList.add('-translate-x-full');
                drawerOverlay.classList.add('hidden');
                body.style.overflow = '';
                document.removeEventListener('touchmove', preventScroll);
            }

            // Event listeners for opening drawer
            if (mobileMenuToggle) mobileMenuToggle.addEventListener('click', openDrawer);
            if (mobileMoreButton) mobileMoreButton.addEventListener('click', openDrawer);

            // Close drawer events
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
            if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && drawer && !drawer.classList.contains('-translate-x-full')) {
                    closeDrawer();
                }
            });

            // Close drawer after clicking any navigation link inside drawer (smooth UX)
            const drawerLinks = document.querySelectorAll('.drawer-nav-link');
            drawerLinks.forEach(link => {
                link.addEventListener('click', function() {
                    closeDrawer();
                });
            });

            // Adjust main content bottom padding when bottom navigation is visible on mobile
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
            window.addEventListener('resize', function() {
                adjustMainPadding();
                // Auto-close drawer if screen becomes desktop size
                if (window.innerWidth >= 1024) {
                    closeDrawer();
                    body.style.overflow = '';
                }
            });

            // Optional: Add active state tracking for bottom nav items via route detection
            // (already handled by Blade @class conditions, but ensures dynamic updates)
            // Also handle finance bottom nav highlighting for deposits/withdrawals routes
            const financeNav = document.querySelector('a[href="{{ route('admin.deposits.index') }}"]');
            if (financeNav) {
                const isFinanceRoute =
                    {{ request()->routeIs('admin.deposits.*') || request()->routeIs('admin.withdrawals.*') ? 'true' : 'false' }};
                if (isFinanceRoute) {
                    financeNav.classList.add('text-rose-600', 'dark:text-rose-500');
                    financeNav.classList.remove('text-slate-500', 'dark:text-slate-400');
                }
            }
        })();
    </script>
    <x-confirm-dialog />
    <x-toast />
</body>

</html>
