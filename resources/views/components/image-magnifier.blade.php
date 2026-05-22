@props([
    'images' => [],
    'title' => 'Product Image',
    'aspectRatio' => 'aspect-square',
])

@php
    // Normalize images list
    $imagesArray = is_array($images) ? $images : (is_string($images) ? [$images] : []);
    if (empty($imagesArray)) {
        $imagesArray = ['https://placehold.co/600x600/1e293b/d4af37?text=No+Image'];
    }
@endphp

<div x-data="imageMagnifier({
    images: {{ json_encode($imagesArray, JSON_UNESCAPED_SLASHES) }},
    title: '{{ e($title) }}'
})" class="flex flex-col gap-5 w-full">
    
    <!-- Main Display & Magnifying Container -->
    <div x-ref="container" 
         @mousemove="handleMouseMove($event)" 
         @mouseleave="handleMouseLeave()"
         @click="openZoomPreview()"
         class="zoom-container group relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 rounded-[2rem] shadow-sm cursor-zoom-in flex items-center justify-center p-8 transition-all duration-300 {{ $aspectRatio }}">
        
        <!-- Main Image -->
        <img x-ref="image" 
             :src="images[activeImage]" 
             :alt="title"
             class="zoom-image max-h-full max-w-full object-contain drop-shadow-2xl transition-transform duration-200 ease-out select-none pointer-events-none">

        <!-- Circular Lens (Hidden by default, shown on hover) -->
        <div x-ref="magnifier"
             class="advanced-magnifier absolute rounded-full pointer-events-none border-2 border-[#0078d4] bg-white bg-no-repeat shadow-[0_0_0_3px_rgba(255,255,255,0.3),_0_0_0_6px_rgba(0,120,212,0.2)] dark:bg-zinc-900 dark:border-[#479ef5]"
             style="width: 180px; height: 180px; display: none; background-size: auto; z-index: 20;">
        </div>

        <!-- Navigation Arrows (Overlay on Hover) -->
        <template x-if="images.length > 1">
            <div>
                <button @click.stop="prevImage()"
                    class="absolute left-4 p-3 bg-white/80 dark:bg-zinc-900/80 rounded-full shadow-lg backdrop-blur-md hover:scale-110 opacity-0 group-hover:opacity-100 transition-all duration-200 z-30"
                    type="button">
                    <svg class="h-5 w-5 text-zinc-800 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button @click.stop="nextImage()"
                    class="absolute right-4 p-3 bg-white/80 dark:bg-zinc-900/80 rounded-full shadow-lg backdrop-blur-md hover:scale-110 opacity-0 group-hover:opacity-100 transition-all duration-200 z-30"
                    type="button">
                    <svg class="h-5 w-5 text-zinc-800 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Gallery Thumbnails Strip -->
    <template x-if="images.length > 1">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-white/5 rounded-[1.5rem] p-5 shadow-xs transition-all">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-3">Media Gallery</h3>
            <div class="grid grid-cols-4 gap-4">
                <template x-for="(url, index) in images" :key="index">
                    <button @click="activeImage = index; resetZoomPreviewImage();"
                        :class="activeImage === index ? 'ring-2 ring-blue-600 dark:ring-blue-400 border-transparent shadow-md scale-[1.02]' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                        class="relative flex h-20 cursor-pointer items-center justify-center rounded-xl bg-zinc-50 dark:bg-zinc-800 overflow-hidden border border-zinc-200/80 dark:border-white/5 shadow-3xs transition-all duration-200 p-1"
                        type="button">
                        <img :src="url" alt="" class="h-full w-full object-contain rounded-lg">
                    </button>
                </template>
            </div>
        </div>
    </template>

    <!-- Detailed Zoomed Preview Overlay -->
    <div x-show="previewActive" 
         x-ref="zoomPreview"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="zoomed-preview fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[min(90vw,850px)] h-[min(85vh,850px)] bg-white dark:bg-zinc-900 border-2 border-[#0078d4] dark:border-blue-400 rounded-[2rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] overflow-hidden pointer-events-none"
         style="z-index: 1000; display: none;">
         <div class="absolute top-5 right-5 bg-black/60 backdrop-blur-md text-white px-3 py-1.5 rounded-full text-[10px] font-bold font-mono tracking-widest z-10 select-none">🔍 PAN VIEW</div>
         <div x-ref="zoomedImage"
              class="zoomed-image w-full h-full bg-no-repeat transition-[background-position] duration-75 ease-out"
              style="background-size: auto; background-position: 50% 50%;">
         </div>
    </div>

    <!-- Darkened Overlay Panel for Closing Zoomed Preview -->
    <div x-show="previewActive"
         @click="closeZoomPreview()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="zoom-overlay fixed inset-0 bg-black/60 backdrop-blur-xs cursor-zoom-out"
         style="z-index: 999; display: none;">
    </div>
</div>

<script>
    (function() {
        const initComponent = () => {
            if (window.Alpine && !window.Alpine.components?.imageMagnifier) {
                window.Alpine.data('imageMagnifier', (config) => ({
                    images: config.images || [],
                    title: config.title || 'Image',
                    activeImage: 0,
                    previewActive: false,
                    isMouseOver: false,
                    panHandler: null,

                    init() {
                        this.resetMagnifierBackground();
                    },

                    prevImage() {
                        this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length;
                        this.resetMagnifierBackground();
                    },

                    nextImage() {
                        this.activeImage = (this.activeImage + 1) % this.images.length;
                        this.resetMagnifierBackground();
                    },

                    resetMagnifierBackground() {
                        const image = this.$refs.image;
                        const magnifier = this.$refs.magnifier;
                        if (image && magnifier) {
                            magnifier.style.backgroundImage = `url('${this.images[this.activeImage]}')`;
                        }
                    },

                    handleMouseMove(e) {
                        this.isMouseOver = true;
                        const container = this.$refs.container;
                        const image = this.$refs.image;
                        const magnifier = this.$refs.magnifier;
                        if (!container || !image || !magnifier) return;

                        const rect = container.getBoundingClientRect();
                        const mouseX = e.clientX - rect.left;
                        const mouseY = e.clientY - rect.top;

                        if (mouseX < 0 || mouseY < 0 || mouseX > rect.width || mouseY > rect.height) {
                            magnifier.style.display = 'none';
                            return;
                        }

                        magnifier.style.display = 'block';

                        const magnifierWidth = 180;
                        const magnifierHeight = 180;
                        let left = mouseX - magnifierWidth / 2;
                        let top = mouseY - magnifierHeight / 2;

                        left = Math.max(0, Math.min(left, rect.width - magnifierWidth));
                        top = Math.max(0, Math.min(top, rect.height - magnifierHeight));

                        magnifier.style.left = left + 'px';
                        magnifier.style.top = top + 'px';

                        const zoom = 1.5;

                        const imgNaturalWidth = image.naturalWidth || 600;
                        const imgNaturalHeight = image.naturalHeight || 600;
                        const imgDisplayWidth = image.clientWidth || rect.width;
                        const imgDisplayHeight = image.clientHeight || rect.height;

                        if (imgDisplayWidth === 0) return;

                        const magnifierCenterX = left + magnifierWidth / 2;
                        const magnifierCenterY = top + magnifierHeight / 2;

                        const ratioX = magnifierCenterX / rect.width;
                        const ratioY = magnifierCenterY / rect.height;

                        const bgPosX = (ratioX * imgNaturalWidth) * zoom - (magnifierWidth / 2);
                        const bgPosY = (ratioY * imgNaturalHeight) * zoom - (magnifierHeight / 2);

                        magnifier.style.backgroundImage = `url('${image.src}')`;
                        magnifier.style.backgroundSize = `${imgNaturalWidth * zoom}px ${imgNaturalHeight * zoom}px`;
                        magnifier.style.backgroundPosition = `-${bgPosX}px -${bgPosY}px`;
                    },

                    handleMouseLeave() {
                        this.isMouseOver = false;
                        const magnifier = this.$refs.magnifier;
                        if (magnifier) {
                            magnifier.style.display = 'none';
                        }
                    },

                    openZoomPreview() {
                        this.previewActive = true;
                        const image = this.$refs.image;
                        const zoomPreview = this.$refs.zoomPreview;
                        const zoomedImage = this.$refs.zoomedImage;
                        if (!image || !zoomPreview || !zoomedImage) return;

                        zoomPreview.style.display = 'block';

                        const imgNaturalWidth = image.naturalWidth || 800;
                        const imgNaturalHeight = image.naturalHeight || 800;
                        const previewWidth = zoomPreview.clientWidth || 800;
                        const previewHeight = zoomPreview.clientHeight || 800;

                        const zoomFactor = Math.max(2, Math.min(4, previewWidth / 120));

                        zoomedImage.style.backgroundImage = `url('${image.src}')`;
                        zoomedImage.style.backgroundSize = `${imgNaturalWidth * zoomFactor}px ${imgNaturalHeight * zoomFactor}px`;
                        zoomedImage.style.backgroundPosition = '50% 50%';

                        this.panHandler = (e) => {
                            const rect = zoomPreview.getBoundingClientRect();
                            let x = e.clientX - rect.left;
                            let y = e.clientY - rect.top;
                            x = Math.max(0, Math.min(x, rect.width));
                            y = Math.max(0, Math.min(y, rect.height));

                            const percentX = x / rect.width;
                            const percentY = y / rect.height;

                            const bgMaxX = (imgNaturalWidth * zoomFactor) - previewWidth;
                            const bgMaxY = (imgNaturalHeight * zoomFactor) - previewHeight;

                            const bgPosX = percentX * bgMaxX;
                            const bgPosY = percentY * bgMaxY;

                            zoomedImage.style.backgroundPosition = `-${bgPosX}px -${bgPosY}px`;
                        };

                        window.addEventListener('mousemove', this.panHandler);
                    },

                    closeZoomPreview() {
                        this.previewActive = false;
                        const zoomPreview = this.$refs.zoomPreview;
                        if (zoomPreview) {
                            zoomPreview.style.display = 'none';
                        }
                        if (this.panHandler) {
                            window.removeEventListener('mousemove', this.panHandler);
                            this.panHandler = null;
                        }
                    },

                    resetZoomPreviewImage() {
                        this.closeZoomPreview();
                        this.resetMagnifierBackground();
                    }
                }));
            }
        };

        if (window.Alpine) {
            initComponent();
        } else {
            document.addEventListener('alpine:init', initComponent);
        }
    })();
</script>
