<div id="global-page-loader"
    class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 dark:bg-zinc-950 transition-opacity duration-700 ease-in-out">
    <div class="relative flex items-center justify-center">
        {{-- Outer spinning ring --}}
        <div
            class="absolute h-24 w-24 rounded-full border-t-2 border-brand-gold/80 border-r-2 border-r-transparent animate-spin">
        </div>
        {{-- Inner pulsing ring --}}
        <div class="absolute h-16 w-16 rounded-full border border-brand-gold/30 animate-ping"></div>
        {{-- Center Logo element --}}
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-navy shadow-lg dark:bg-brand-gold">
            <span class="text-xl font-black text-brand-gold dark:text-brand-navy">W</span>
        </div>
    </div>
    <div class="mt-8 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 animate-pulse">
        Initializing Secure Connection
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const maxWait = setTimeout(() => hideLoader(), 3000);

        window.addEventListener('load', () => {
            clearTimeout(maxWait);
            hideLoader();
        });

        function hideLoader() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                // Fade out
                loader.style.opacity = '0';
                // Remove from DOM after fade completes
                setTimeout(() => {
                    if (loader.parentNode) {
                        loader.parentNode.removeChild(loader);
                    }
                }, 700); // matches the duration-700 class
            }
        }
    });

    // Handle back/forward cache restoration so loader doesn't get stuck if user navigates back
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.display = 'none';
            }
        }
    });
</script>
