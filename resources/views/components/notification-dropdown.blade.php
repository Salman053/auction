@php
    $user = auth('admin')->user() ?? auth('user')->user() ?? auth()->user();
    $unreadCount = $user ? $user->unreadNotifications->count() : 0;
    $isAdmin = $user && (isset($user->role) && $user->role === \App\Enums\UserRole::Admin->value || request()->is('admin*'));
    $prefix = $isAdmin ? 'admin' : 'user';
@endphp

<div class="relative">
    @if($user)
        <button id="notification-trigger" type="button"
                class="relative flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 text-zinc-600 transition hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            @if($unreadCount > 0)
                <span class="absolute right-2 top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-zinc-950">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

        <div id="notification-dropdown-menu" 
             class="absolute right-0 mt-3 w-80 origin-top-right overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 z-50 hidden transition-all duration-200">
            
            <div class="border-b border-black/5 bg-zinc-50 px-5 py-4 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Notifications</h3>
                    @if($unreadCount > 0)
                        <form action="{{ route($prefix . '.notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold text-brand-gold hover:underline">Mark all read</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="max-h-[400px] overflow-y-auto">
                @forelse($user->notifications->take(10) as $notification)
                    <div class="group relative border-b border-black/5 px-5 py-4 transition hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5 {{ $notification->unread() ? 'bg-zinc-50/50 dark:bg-white/2' : '' }}">
                        <div class="flex gap-3">
                            <div class="mt-1 flex h-2 w-2 shrink-0 rounded-full {{ $notification->unread() ? 'bg-brand-gold' : 'bg-transparent' }}"></div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-zinc-900 dark:text-white leading-relaxed">
                                    {{ $notification->data['message'] ?? 'No message content' }}
                                </p>
                                <span class="mt-1.5 block text-[10px] text-zinc-400 dark:text-zinc-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                        <svg class="h-10 w-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-2 text-xs font-medium text-zinc-400 dark:text-zinc-500">No notifications yet.</p>
                    </div>
                @endforelse
            </div>

            <a href="{{ route($prefix . '.notifications.index') }}" 
               class="block bg-zinc-50 py-3 text-center text-[10px] font-black uppercase tracking-widest text-zinc-500 transition hover:bg-zinc-100 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
                View all notifications
            </a>
        </div>
    @endif
</div>
