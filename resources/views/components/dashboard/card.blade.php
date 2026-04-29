<div {{ $attributes->merge(['class' => 'dashboard-card']) }}>
    @if(isset($title) || isset($action))
    <div class="mb-6 flex items-center justify-between">
        @if(isset($title))
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-900 dark:text-white">{{ $title }}</h3>
        @endif
        
        @if(isset($action))
        <div>{{ $action }}</div>
        @endif
    </div>
    @endif
    
    {{ $slot }}
</div>
