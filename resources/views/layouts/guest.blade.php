<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Japanese Watch Auctions' }} · {{ config('app.name') }}</title>

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

<body class="min-h-full bg-slate-50 antialiased dark:bg-zinc-950">
    <header class="sticky top-0 z-50 border-b border-white/10 bg-brand-navy/95 backdrop-blur-md dark:bg-zinc-900/95">
        <div class="mx-auto flex max-w-7xl  flex-wrap gap-3 items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-gold">
                    <span class="text-xl font-black text-brand-navy">W</span>
                </div>
                <span class="text-xl font-black tracking-tighter text-white">WatchHub</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden items-center gap-8 lg:flex">
                <a href="{{ route('auctions.index') }}"
                    class="text-sm font-bold text-white/70 transition hover:text-white">Catalog</a>
                <a href="{{ route('how-it-works') }}"
                    class="text-sm font-bold text-white/70 transition hover:text-white">How it Works</a>
                <a href="{{ route('faq') }}"
                    class="text-sm font-bold text-white/70 transition hover:text-white">FAQ</a>
                <a href="{{ route('about') }}"
                    class="text-sm font-bold text-white/70 transition hover:text-white">About</a>
            </nav>

            <div class="flex items-center gap-6">
                <x-theme-toggle />

                @auth('user')
                    <a href="{{ route('user.dashboard') }}"
                        class="group flex items-center gap-3 rounded-2xl bg-brand-gold px-6 py-2.5 text-sm font-black text-brand-navy shadow-lg transition hover:scale-[1.02] active:scale-95">
                        Console
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </a>
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}"
                            class="text-sm font-black text-white/70 transition hover:text-white">Login</a>
                        <a href="{{ route('register') }}"
                            class="rounded-2xl bg-white px-6 py-2.5 text-sm font-black text-brand-navy shadow-lg transition hover:scale-[1.02] active:scale-95">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="">
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-slate-200 bg-white py-20 dark:border-white/5 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy dark:bg-brand-gold">
                            <span class="text-lg font-black text-brand-gold dark:text-brand-navy">W</span>
                        </div>
                        <span class="text-lg font-black tracking-tighter text-slate-900 dark:text-white">WatchHub</span>
                    </a>
                    <p class="mt-6 max-w-sm text-sm leading-relaxed text-slate-500 dark:text-zinc-400">
                        The premier digital gateway to Japanese horology. Direct access to Yahoo Japan Auctions with
                        professional logistics and transparent bidding.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Market</h4>
                    <ul class="mt-6 space-y-4 text-sm text-slate-500 dark:text-zinc-400">
                        <li><a href="{{ route('auctions.index') }}"
                                class="hover:text-brand-navy dark:hover:text-brand-gold">Live Catalog</a></li>
                        <li><a href="#" class="hover:text-brand-navy dark:hover:text-brand-gold">Winning Bids</a>
                        </li>
                        <li><a href="#" class="hover:text-brand-navy dark:hover:text-brand-gold">Featured
                                Pieces</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Collectors
                    </h4>
                    <ul class="mt-6 space-y-4 text-sm text-slate-500 dark:text-zinc-400">
                        <li><a href="{{ route('login') }}"
                                class="hover:text-brand-navy dark:hover:text-brand-gold">Account Login</a></li>
                        <li><a href="{{ route('faq') }}"
                                class="hover:text-brand-navy dark:hover:text-brand-gold">Support Center</a></li>
                        <li><a href="{{ route('contact') }}"
                                class="hover:text-brand-navy dark:hover:text-brand-gold">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-20 flex flex-col justify-between gap-6 border-t border-slate-100 pt-10 dark:border-white/5 sm:flex-row sm:items-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">© {{ date('Y') }}
                    WatchHub Horology. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#"
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-navy transition">Privacy
                        Policy</a>
                    <a href="#"
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-navy transition">Terms
                        of Service</a>
                </div>
            </div>
        </div>
    </footer>
    <x-confirm-dialog />
    <x-toast />
</body>

</html>
