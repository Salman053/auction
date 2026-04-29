@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}" @class([
    'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition duration-200',
    'bg-brand-navy text-brand-gold shadow-lg shadow-brand-navy/20 dark:bg-brand-gold dark:text-brand-navy' => $active,
    'text-slate-500 hover:bg-slate-100 dark:text-zinc-400 dark:hover:bg-white/5 hover:text-brand-navy dark:hover:text-white' => !$active,
])>
    @if($icon)
    <div @class([
        'h-5 w-5 transition duration-200',
        'text-brand-gold dark:text-brand-navy' => $active,
        'text-slate-400 group-hover:text-brand-navy dark:text-zinc-500 dark:group-hover:text-white' => !$active,
    ])>
        {{ $icon }}
    </div>
    @endif
    
    <span class="tracking-wide">{{ $slot }}</span>
</a>
