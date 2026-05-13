<x-guest-layout :title="$auction->title">
    <div class="bg-zinc-50 dark:bg-zinc-950 pb-20">
        {{-- Breadcrumbs --}}
        <div class="mx-auto max-w-7xl px-4 py-4 lg:px-8">
            <nav class="flex text-xs font-bold uppercase tracking-widest text-zinc-400">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('auctions.index') }}" class="hover:text-blue-600">Catalog</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-900 dark:text-white truncate max-w-[200px]">{{ $auction->title }}</span>
            </nav>
        </div>

        <section class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                {{-- Left: Image Gallery --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="relative overflow-hidden rounded-[2.5rem] bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                        <div class="relative aspect-square sm:aspect-video overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-800">
                            <img id="mainImage" src="{{ $auction->thumbnail_url }}" alt="{{ $auction->title }}"
                                class="h-full w-full object-contain transition duration-500 hover:scale-105">
                            
                            {{-- Status Badge --}}
                            <div class="absolute left-6 top-6">
                                <span class="rounded-full bg-blue-600 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl">
                                    {{ $auction->status }}
                                </span>
                            </div>
                        </div>

                        @if (!empty($auction->image_urls))
                            <div class="mt-4 flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                                @foreach ($auction->image_urls as $url)
                                    <button onclick="document.getElementById('mainImage').src='{{ $url }}'"
                                        class="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-zinc-50 ring-1 ring-zinc-200 transition-all hover:ring-2 hover:ring-blue-500 dark:bg-zinc-800 dark:ring-white/5">
                                        <img src="{{ $url }}" class="h-full w-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Description / Details --}}
                    <div class="rounded-[2.5rem] bg-white p-8 lg:p-12 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                        <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white">Item Provenance & Details</h3>
                        <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Merchant</p>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $auction->seller_name ?? 'Japan Authentic' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Seller Rating</p>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-1">
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    {{ $auction->seller_rating ?? '4.9/5.0' }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Condition</p>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $auction->condition ?? 'Authenticated Product' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Auction ID</p>
                                <p class="text-sm font-mono font-bold text-blue-600">{{ $auction->yahoo_auction_id }}</p>
                            </div>
                        </div>

                        <div class="mt-12 rounded-2xl bg-blue-50 p-6 dark:bg-blue-900/10">
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-blue-900 dark:text-blue-400">AuctionHub Escrow Protection</h4>
                                    <p class="mt-1 text-xs leading-relaxed text-blue-800/70 dark:text-blue-400/60">Your funds are held in secure escrow until the item is verified at our Tokyo hub. Buy with absolute confidence.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Bidding Actions --}}
                <div class="lg:col-span-5 space-y-8">
                    <div class="sticky top-24 space-y-8">
                        <div class="rounded-[2.5rem] bg-white p-8 lg:p-10 shadow-xl ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                            <h1 class="text-2xl font-black tracking-tighter text-zinc-900 dark:text-white lg:text-3xl leading-tight">
                                {{ $auction->title }}
                            </h1>
                            
                            <div class="mt-8 flex items-center justify-between border-b border-zinc-100 pb-8 dark:border-white/5">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Current Bid</p>
                                    <p class="mt-2 text-4xl font-black tracking-tighter text-zinc-900 dark:text-white">
                                        <span class="text-blue-600">¥</span>{{ number_format($auction->current_bid_yen) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Ending In</p>
                                    <p id="timeRemaining" class="mt-2 text-lg font-bold text-blue-600">
                                        {{ $auction->ends_at?->diffForHumans() ?? 'Ended' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-white/5">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Bids</p>
                                        <p class="mt-1 text-lg font-black text-zinc-900 dark:text-white">{{ $auction->bid_count ?? 0 }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-white/5">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Views</p>
                                        <p class="mt-1 text-lg font-black text-zinc-900 dark:text-white">{{ number_format($auction->view_count ?? 0) }}</p>
                                    </div>
                                </div>

                                @auth('user')
                                    <a href="{{ route('user.auctions.show', $auction) }}" class="flex w-full items-center justify-center rounded-2xl bg-blue-600 py-5 text-sm font-black uppercase tracking-widest text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700 hover:scale-[1.02] active:scale-95">
                                        Enter Bidding Console
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-2xl bg-zinc-900 py-5 text-sm font-black uppercase tracking-widest text-white transition hover:bg-black hover:scale-[1.02] active:scale-95 dark:bg-white dark:text-zinc-900">
                                        Sign in to Bid
                                    </a>
                                @endauth
                                
                                <button class="flex w-full items-center justify-center gap-2 rounded-2xl bg-white py-4 text-xs font-bold text-zinc-600 ring-1 ring-zinc-200 transition hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-400 dark:ring-white/5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    Add to Watchlist
                                </button>
                            </div>
                        </div>

                        {{-- Activity Log --}}
                        <div class="rounded-[2.5rem] bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-white/10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Live Activity Log</h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Live</span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @forelse($auction->bids()->latest()->take(5)->get() as $bid)
                                    <div class="flex items-center justify-between rounded-xl bg-zinc-50 p-4 dark:bg-white/5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-1.5 w-1.5 rounded-full bg-blue-600"></div>
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $bid->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="text-sm font-black text-zinc-900 dark:text-white">¥{{ number_format($bid->amount_yen) }}</span>
                                    </div>
                                @empty
                                    <p class="py-8 text-center text-xs italic text-zinc-400 font-medium">No recent activity detected.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            @if ($auction->ends_at && $auction->ends_at->isFuture())
                function updateTimeRemaining() {
                    const endTime = new Date('{{ $auction->ends_at->toIso8601String() }}').getTime();
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        document.getElementById('timeRemaining').innerHTML = 'Auction Ended';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    let timeString = '';
                    if (days > 0) timeString += `${days}d `;
                    if (hours > 0 || days > 0) timeString += `${hours}h `;
                    if (minutes > 0 || hours > 0 || days > 0) timeString += `${minutes}m `;
                    timeString += `${seconds}s`;

                    document.getElementById('timeRemaining').innerHTML = timeString;
                }

                updateTimeRemaining();
                setInterval(updateTimeRemaining, 1000);
            @endif
        </script>
    @endpush
</x-guest-layout>
