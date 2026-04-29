@props(['label', 'value', 'trend' => null, 'trendUp' => true, 'icon' => null])

<div {{ $attributes->merge(['class' => 'dashboard-card']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="dashboard-stat-label">{{ $label }}</p>
            <h4 class="dashboard-stat-value mt-2">{{ $value }}</h4>
            
            @if($trend)
            <div class="mt-3 flex items-center gap-1.5 text-xs font-bold {{ $trendUp ? 'text-emerald-600' : 'text-rose-600' }}">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    @if($trendUp)
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    @endif
                </svg>
                <span>{{ $trend }}</span>
            </div>
            @endif
        </div>
        
        @if($icon)
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-navy/5 text-brand-navy dark:bg-white/5 dark:text-brand-gold">
            {{ $icon }}
        </div>
        @endif
    </div>
</div>
