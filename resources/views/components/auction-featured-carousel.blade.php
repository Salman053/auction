@props([
    'auctions',
    'title' => 'Trending Marketplace',
    'viewAllUrl' => null,
    'autoScroll' => true,
    'autoScrollDelay' => 4000,
])

<div x-data="auctionCarousel({ autoScroll: @js($autoScroll), delay: @js($autoScrollDelay) })" x-init="init()" @mouseenter="pauseAutoScroll()" @mouseleave="resumeAutoScroll()"
    class="relative w-full group/carousel">

    {{-- Header Section --}}
    <div class="flex items-end justify-between mb-8 gap-4">
        <div>
            <h3 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                {{ $title }}
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Discover exclusive items ending soon
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if ($viewAllUrl)
                <a href="{{ $viewAllUrl }}"
                    class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    View All
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            @endif

        </div>
    </div>

    {{-- Carousel Track --}}
    <div x-ref="scrollContainer" @scroll="updateButtons()" tabindex="0"
        class="flex gap-5 overflow-x-auto pb-6 pt-2 px-1 snap-x snap-mandatory scrollbar-hide scroll-smooth outline-none rounded-2xl">

        @forelse($auctions as $auction)
            <a href="{{ route('auctions.show', $auction->id) }}"
                class="snap-start flex-none w-60 group/card focus:outline-none rounded-3xl" @click="pauseAutoScroll()">

                <article
                    class="relative overflow-hidden rounded-3xl aspect-[4/5] bg-zinc-100 dark:bg-zinc-900 shadow-lg ring-1 ring-zinc-200/60 dark:ring-zinc-800 transition-all duration-300 group-hover/card:shadow-2xl group-hover/card:ring-blue-500/30">
                    <div class="relative h-full overflow-hidden">
                        <img src="{{ $auction->thumbnail_url }}" alt="{{ $auction->title }}" loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover/card:scale-105"
                            onerror="this.src='https://via.placeholder.com/400x500/1f2937/9ca3af?text=No+Image'; this.classList.add('opacity-60')">
                    </div>

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-60 group-hover/card:opacity-80 transition-opacity duration-500">
                    </div>

                    <div
                        class="absolute bottom-0 left-0 right-0 p-5 transform translate-y-2 group-hover/card:translate-y-0 transition-transform duration-500 ease-out">
                        <div class="flex items-baseline justify-between mb-3">
                            <span
                                class="text-blue-400 font-black text-lg tracking-tight">¥{{ number_format($auction->current_bid_yen) }}</span>
                            <span
                                class="text-xs font-medium text-zinc-300 bg-zinc-800/60 px-2.5 py-1 rounded-full">{{ $auction->bid_count ?? 0 }}
                                bids</span>
                        </div>
                        <h4
                            class="font-bold text-white text-sm leading-snug line-clamp-2 mb-3 group-hover/card:text-blue-100 transition-colors">
                            {{ $auction->title }}</h4>
                    </div>
                </article>
            </a>
        @empty
            <div class="flex-none w-full py-16 text-center text-zinc-500">No auctions found.</div>
        @endforelse
    </div>

    {{-- Progress Indicator --}}
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
        <div class="h-full bg-blue-500 transition-all duration-200 ease-linear" :style="`width: ${progress}%`"></div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('auctionCarousel', ({
            autoScroll,
            delay
        }) => ({
            scrollContainer: null,
            scrollAmount: 270,
            autoScrollEnabled: autoScroll,
            autoScrollInterval: null,
            progressInterval: null,
            progress: 0,

            init() {
                this.scrollContainer = this.$refs.scrollContainer;
                if (this.autoScrollEnabled) this.startAutoScroll();
            },
            startAutoScroll() {
                this.progressInterval = setInterval(() => {
                    this.progress = Math.min(100, this.progress + (100 / (delay / 50)));
                }, 50);
                this.autoScrollInterval = setInterval(() => {
                    this.scrollToNext();
                    this.progress = 0;
                }, delay);
            },
            stopAutoScroll() {
                clearInterval(this.autoScrollInterval);
                clearInterval(this.progressInterval);
                this.progress = 0;
            },
            pauseAutoScroll() {
                if (this.autoScrollEnabled) this.stopAutoScroll();
            },
            resumeAutoScroll() {
                if (this.autoScrollEnabled) this.startAutoScroll();
            },
            resetAutoScroll() {
                this.stopAutoScroll();
                this.startAutoScroll();
            },
            scrollToNext() {
                const {
                    scrollLeft,
                    scrollWidth,
                    clientWidth
                } = this.scrollContainer;
                if (scrollLeft + clientWidth >= scrollWidth - 10) this.scrollContainer.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                });
                else this.scrollContainer.scrollBy({
                    left: this.scrollAmount,
                    behavior: 'smooth'
                });
            },
            scroll(dir) {
                this.scrollContainer.scrollBy({
                    left: dir * this.scrollAmount,
                    behavior: 'smooth'
                });
            },
            updateButtons() {
                /* Logic for button state disabled could be added here */
            }
        }));
    });
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
