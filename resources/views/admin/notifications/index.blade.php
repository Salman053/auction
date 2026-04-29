<x-admin-layout :title="'System Notifications'">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">Admin Notifications</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">New bids, deposit requests, and system alerts.</p>
            </div>

            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-bold text-zinc-900 dark:text-white">All caught up</h3>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">There are no notifications to show right now.</p>
            </div>
        @else
            <div class="divide-y divide-black/5 dark:divide-white/10">
                @foreach ($notifications as $notification)
                    <div class="group relative px-6 py-5 transition hover:bg-zinc-50 dark:hover:bg-white/5 {{ !$notification->read_at ? 'bg-brand-gold/5 dark:bg-brand-gold/2' : '' }}">
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex gap-4">
                                <div class="mt-1 flex h-2 w-2 shrink-0 rounded-full {{ !$notification->read_at ? 'bg-brand-gold shadow-[0_0_8px_rgba(212,175,55,0.5)]' : 'bg-transparent' }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white leading-relaxed">
                                        {{ $notification->data['message'] ?? 'System alert' }}
                                    </p>
                                    
                                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                                            {{ $notification->created_at?->diffForHumans() }}
                                        </span>

                                        @if(isset($notification->data['transaction_id']))
                                            <a href="{{ route('admin.deposits.index', ['status' => 'pending']) }}" class="text-[10px] font-black uppercase tracking-widest text-brand-gold hover:underline">
                                                Review Transaction
                                            </a>
                                        @endif

                                        @if(isset($notification->data['auction_id']))
                                            <a href="{{ route('admin.auctions.show', $notification->data['auction_id']) }}" class="text-[10px] font-black uppercase tracking-widest text-brand-gold hover:underline">
                                                View Auction
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
</x-admin-layout>
