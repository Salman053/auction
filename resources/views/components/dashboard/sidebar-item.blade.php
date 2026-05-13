@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}" @class([
    'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition duration-200',
    'bg-blue-600 text-white shadow-lg shadow-blue-600/20' => $active,
    'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/5 hover:text-blue-600 dark:hover:text-white' => !$active,
])>
    @if($icon)
    <div @class([
        'h-5 w-5 transition duration-200',
        'text-white' => $active,
        'text-zinc-400 group-hover:text-blue-600 dark:text-zinc-500 dark:group-hover:text-white' => !$active,
    ])>
        {{ $icon }}
    </div>
    @endif
    
    <span class="text-[11px] font-black uppercase tracking-widest">{{ $slot }}</span>
</a>
