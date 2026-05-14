<x-user-layout :title="'Market Overview'">
    <div class="mb-10 flex items-center flex-wrap gap-2.5 justify-between">
        <div>
            <h1 class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white">Collector Console</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Welcome back, {{ $user?->name ?? 'User' }}. Here is
                your market standing.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('user.auctions.index') }}"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                Explore Market
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-dashboard.stat-card label="Wallet Balance" value="¥{{ number_format($wallet?->balance_yen ?? 0) }}"
            trend="+12% vs last month" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Locked Funds" value="¥{{ number_format($wallet?->locked_balance_yen ?? 0) }}"
            trend="Funds reserved for active bids" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Bidding Capacity" value="¥{{ number_format($capacityYen) }}"
            trend="Multiplied @ {{ $multiplierPercent }}%" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Active Bids" value="{{ number_format($activeBidsCount) }}"
            trend="{{ number_format($wonAuctionsCount) }} total wins" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19V6l12-2v13M9 19a2 2 0 11-4 0 2 2 0 014 0zm12-2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>
    </div>


    {{-- Charts Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <x-dashboard.chart-card title="Bidding Intensity" id="bidIntensityChart" class="lg:col-span-2" />

        <x-dashboard.card title="Capacity Composition" class="flex flex-col h-full">
            <div id="capacityRadialChart" class="flex-1 min-h-[220px] w-full"></div>

            <div class="mt-6 p-4 rounded-lg bg-zinc-50/50 dark:bg-white/2 border border-zinc-200 dark:border-white/5">
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                            Liquid Capital
                        </p>
                        <p class="text-xl font-black text-zinc-900 dark:text-white leading-none mt-1">
                            ¥{{ number_format($availableCapacityYen) }}
                        </p>
                    </div>
                    <span class="text-[10px] font-black text-blue-600">
                        {{ $capacityYen > 0 ? round(($availableCapacityYen / $capacityYen) * 100) : 0 }}%
                    </span>
                </div>

                <div class="h-2.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                    <div class="h-full bg-blue-600 transition-all duration-500 ease-out shadow-[0_0_8px_rgba(37,99,235,0.4)]"
                        style="width: {{ $capacityYen > 0 ? ($availableCapacityYen / $capacityYen) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </x-dashboard.card>

    </div>

    {{-- Hot Auctions Section --}}
    <div class="mt-10">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">Hot & Trending</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Most active auctions on the platform right now.
                </p>
            </div>
            <a href="{{ route('user.auctions.index') }}"
                class="text-sm font-black text-blue-600 hover:text-blue-700 transition">View All &rarr;</a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2  lg:grid-cols-6">
            @foreach ($hotAuctions as $auction)
                @php($isWatchlisted = in_array($auction->id, $watchlistedAuctionIds ?? [], true))
                <div
                    class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:bg-zinc-900 dark:ring-white/10">
                    <div class="relative aspect-square overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img src="{{ $auction->thumbnail_url ?? 'https://placehold.co/400x300/1e293b/d4af37?text=AuctionHub' }}"
                            alt="{{ $auction->title }}"
                            class="h-full w-full object-contain transition duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        <div class="absolute right-4 top-4 z-30">
                            <form method="POST"
                                action="{{ $isWatchlisted ? route('user.watchlist.destroy', $auction) : route('user.watchlist.store', $auction) }}">
                                @csrf
                                @if ($isWatchlisted)
                                    @method('DELETE')
                                @endif
                                <button type="submit"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 shadow-lg backdrop-blur-md transition hover:scale-110 dark:bg-zinc-800/90">
                                    <svg class="h-5 w-5 {{ $isWatchlisted ? 'fill-blue-600 text-blue-600' : 'text-zinc-600 dark:text-zinc-300' }}"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="inline-flex h-2 w-2 rounded-full bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.4)]"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                                @if ($auction->ends_at)
                                    {{ $auction->ends_at->isPast() ? 'Ended' : 'Ends' }}
                                    {{ $auction->ends_at->diffForHumans() }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                        <h3
                            class="line-clamp-2 min-h-[40px] text-sm font-black text-zinc-900 group-hover:text-blue-600 transition dark:text-white leading-tight">
                            <a href="{{ route('user.auctions.show', $auction) }}">
                                <span class="absolute inset-0"></span>
                                {{ $auction->title }}
                            </a>
                        </h3>
                        <div
                            class="mt-4 flex items-end justify-between border-t border-zinc-100 pt-3 dark:border-white/5">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Current Bid
                                </p>
                                <p class="mt-1 text-xl font-black text-zinc-900 dark:text-white tracking-tighter">
                                    ¥{{ number_format($auction->current_bid_yen) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Activity</p>
                                <p class="mt-1 text-[11px] font-black uppercase tracking-widest text-blue-600">
                                    {{ number_format($auction->bid_count) }} bids</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($chartData);

            // Bid Intensity Chart
            new ApexCharts(document.querySelector("#bidIntensityChart"), {
                series: [{
                    name: 'Your Bids',
                    data: chartData.bids
                }, {
                    name: 'Wallet Net (¥)',
                    data: chartData.walletNet
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    fontFamily: 'inherit',
                    background: 'transparent'
                },
                colors: ['#2563EB', '#10B981'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    borderColor: 'rgba(0,0,0,0.05)'
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            }).render();

            // Capacity Radial Chart
            new ApexCharts(document.querySelector("#capacityRadialChart"), {
                series: [
                    {{ $capacityYen > 0 ? round((($wallet?->locked_balance_yen ?? 0) / $capacityYen) * 100) : 0 }}
                ],
                chart: {
                    type: 'radialBar',
                    height: 250,
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '65%'
                        },
                        track: {
                            background: 'rgba(0,0,0,0.05)'
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                color: '#2563EB',
                                fontSize: '24px',
                                fontWeight: '900',
                                formatter: (val) => val + '%'
                            }
                        }
                    }
                },
                colors: ['#2563EB'],
                labels: ['Committed Capacity'],
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            }).render();
        });
    </script>
</x-user-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scrollKey = 'dashboardScroll_' + window.location.search;
        const scrollElement = document.querySelector('main') || window;

        // Restore scroll position
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

        const saveScroll = () => {
            const scrollPos = scrollElement === window ? window.scrollY : scrollElement.scrollTop;
            sessionStorage.setItem(scrollKey, scrollPos);
        };

        window.addEventListener('beforeunload', saveScroll);
        window.addEventListener('pagehide', saveScroll);
    });
</script>
