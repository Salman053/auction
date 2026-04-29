<x-user-layout :title="'Market Overview'">
    <div class="mb-10 flex items-center flex-wrap gap-2.5 justify-between">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Collector Console</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Welcome back, {{ $user?->name ?? 'Collector' }}.
                Here is
                your market standing.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- <button
                class="flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-bold shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Report
            </button> --}}
            <a href="{{ route('user.auctions.index') }}"
                class="flex items-center gap-2 rounded-2xl bg-brand-navy px-6 py-3 text-sm font-black text-brand-gold shadow-lg transition hover:scale-[1.02] dark:bg-brand-gold dark:text-brand-navy">
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

        <x-dashboard.card title="Capacity Composition" class="flex flex-col">
            <div id="capacityRadialChart" class="flex-1"></div>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-widest">
                    <span class="text-slate-400">Available</span>
                    <span class="text-slate-900 dark:text-white">¥{{ number_format($availableCapacityYen) }}</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/5">
                    <div class="h-full bg-brand-gold"
                        style="width: {{ $capacityYen > 0 ? ($availableCapacityYen / $capacityYen) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </x-dashboard.card>
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
                colors: ['#1877F2', '#10B981'],
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
                                color: '#1877F2',
                                fontSize: '22px',
                                fontWeight: 'bold',
                                formatter: (val) => val + '%'
                            }
                        }
                    }
                },
                colors: ['#1877F2'],
                labels: ['Committed Capacity'],
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            }).render();
        });
    </script>
</x-user-layout>
