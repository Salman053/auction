@props(['title', 'id', 'height' => 300])

<div {{ $attributes->merge(['class' => 'dashboard-card overflow-hidden']) }}>
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-900 dark:text-white">{{ $title }}</h3>
        <div class="flex items-center gap-2">
            <span class="flex h-1.5 w-1.5 rounded-full bg-brand-gold"></span>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Live Trend</span>
        </div>
    </div>
    
    <div id="{{ $id }}" style="min-height: {{ $height }}px;"></div>
</div>
