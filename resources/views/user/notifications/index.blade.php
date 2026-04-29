<x-user-layout :title="'My Alerts'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">Notifications</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Updates on your bids, wallet, and won items.</p>
            </div>

            <form method="POST" action="{{ route('user.notifications.mark-all-read') }}">
                @csrf
                <button type="submit"
                    class="rounded-xl border border-zinc-200 bg-zinc-50 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-600 transition hover:bg-zinc-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10">
                    Mark all read
                </button>
            </form>
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 overflow-hidden">
        @if ($notifications->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-zinc-50 dark:bg-white/5">
                    <svg class="h-8 w-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-bold text-zinc-900 dark:text-white">No new alerts</h3>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">We'll notify you here when there's activity on your account.</p>
            </div>
        @else
            <div class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($notifications as $notification)
                    <div class="group relative px-6 py-5 transition hover:bg-zinc-50 dark:hover:bg-white/5 {{ !$notification->read_at ? 'bg-brand-navy/5 dark:bg-brand-gold/2' : '' }}">
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex gap-4">
                                <div class="mt-1 flex h-2 w-2 shrink-0 rounded-full {{ !$notification->read_at ? 'bg-brand-gold shadow-[0_0_8px_rgba(212,175,55,0.5)]' : 'bg-transparent' }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white leading-relaxed">
                                        {{ $notification->data['message'] ?? 'Notification alert' }}
                                    </p>
                                    
                                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                                            {{ $notification->created_at?->diffForHumans() }}
                                        </span>

                                        @if(isset($notification->data['auction_id']))
                                            <a href="{{ route('user.auctions.show', $notification->data['auction_id']) }}" class="text-[10px] font-black uppercase tracking-widest text-brand-gold hover:underline">
                                                View Auction
                                            </a>
                                        @endif

                                        @if(isset($notification->data['balance']))
                                            <a href="{{ route('user.wallet.index') }}" class="text-[10px] font-black uppercase tracking-widest text-brand-gold hover:underline">
                                                Top Up Wallet
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$notification->read_at)
                                <div class="opacity-0 transition group-hover:opacity-100">
                                    <span class="inline-flex rounded-lg bg-brand-gold/10 px-2 py-1 text-[10px] font-bold text-brand-gold">NEW</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-black/5 bg-zinc-50 p-6 dark:border-white/10 dark:bg-white/2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-user-layout>
