<x-admin-layout :title="'Administrative Overview'">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Admin Command Center</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Total control over market logistics and scraper
                health.</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.scraping-logs.start') }}" method="POST"
                onsubmit="return !{{ $isScrapingRunning ? 'true' : 'false' }} && confirm('Start full scrape (13 pages, 1s delay)? This will run in the background.')">
                @csrf
                <button type="submit" @if ($isScrapingRunning) disabled @endif
                    class="flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-black text-white shadow-lg transition
                        {{ $isScrapingRunning
                            ? 'cursor-not-allowed bg-amber-500 shadow-amber-500/20 opacity-80'
                            : 'bg-emerald-600 shadow-emerald-600/20 hover:bg-emerald-700' }}">
                    @if ($isScrapingRunning)
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Scraper Running…
                    @else
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Run Manual Scrape
                    @endif
                </button>
            </form>

            @if($isScrapingRunning)
                <form action="{{ route('admin.scraping-logs.stop') }}" method="POST"
                    onsubmit="return confirm('Stop the running scraper? It will halt after the current page.')">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z" />
                        </svg>
                        Stop Scraper
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.scraping-logs.index') }}"
                class="flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-zinc-900 dark:ring-white/10 dark:hover:bg-white/5">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Scraper Logs
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                System Settings
            </a>
        </div>
    </div>

    {{-- Admin Stats Grid --}}
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-dashboard.stat-card label="Total Collectors" value="{{ $userCount }}"
            trend="+{{ $usersThisWeek }} this week" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Market Items" value="{{ $auctionCount }}" trend="+{{ $auctionsToday }} new today"
            :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Open Inquiries" value="{{ $openTicketCount }}"
            trend="{{ $pendingDepositCount }} pending deposits" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card label="Scraper Health" value="{{ strtoupper($scraperStatus) }}"
            trend="{{ $activeProxyCount }} active proxies" :trendUp="true">
            <x-slot name="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
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
                class="mt-8 block w-full rounded-lg bg-slate-50 py-3 text-center text-xs font-bold uppercase tracking-widest text-slate-600 transition hover:bg-slate-100 dark:bg-white/5 dark:text-zinc-400">
                View All Activity
            </a>
        </x-dashboard.card>
    </div>

    <script>
        // (function() {
        //     setTimeout(() => {
        //         window.location.reload()
        //     }, 1000);
        // })()
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector("#scrapingTimelineChart");
            if (!container) return;

            const performanceData = @json($performanceData);

            new ApexCharts(container, {
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
