<x-admin-layout :title="'Administrative Overview'">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Admin Command Center</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Total control over market logistics and scraper
                health.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.scraping-logs.index') }}"
                class="flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-bold shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Scraper Logs
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-black text-white shadow-lg transition hover:scale-[1.02] dark:bg-white dark:text-slate-900">
                System Settings
            </a>
        </div>
    </div>

    {{-- Admin Stats Grid --}}
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-dashboard.stat-card label="Total Collectors" value="{{ $userCount }}" trend="+5 this week"
            :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Market Items" value="{{ $auctionCount }}" trend="+20 new today" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Active Proxies" value="{{ $activeProxyCount }}" trend="{{ $failedProxyCount }} temporarily disabled"
            :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Scraper Status" value="{{ strtoupper($scraperStatus) }}" trend="{{ $pendingDepositCount }} pending deposits" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <x-dashboard.chart-card title="Scraping Throughput" id="scrapingTimelineChart" class="lg:col-span-2" />

        <x-dashboard.card title="Recent System Activity">
            <div class="space-y-6">
                @forelse ($recentActivity as $log)
                    <div class="flex items-start gap-4">
                        <span class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $log->event }}
                            </p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-0.5">
                                {{ $log->created_at?->diffForHumans() ?? '—' }}
                                @if ($log->actor)
                                    · {{ $log->actor->email }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500 dark:text-zinc-400">No activity yet.</div>
                @endforelse
            </div>

            <a href="{{ route('admin.audit-logs.index') }}"
                class="mt-8 block w-full rounded-2xl bg-slate-50 py-3 text-center text-xs font-bold uppercase tracking-widest text-slate-600 transition hover:bg-slate-100 dark:bg-white/5 dark:text-zinc-400">
                View All Activity
            </a>
        </x-dashboard.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const performanceData = @json($performanceData);

            new ApexCharts(document.querySelector("#scrapingTimelineChart"), {
                series: [{
                    name: 'Requests/Hr',
                    data: performanceData.scrapes
                }, {
                    name: 'Success Rate (%)',
                    data: performanceData.success_rate
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit',
                    background: 'transparent'
                },
                colors: ['#3b82f6', '#10b981'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: performanceData.labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                grid: {
                    borderColor: 'rgba(0,0,0,0.05)'
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            }).render();
        });
    </script>
</x-admin-layout>
