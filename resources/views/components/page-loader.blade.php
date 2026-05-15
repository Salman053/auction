<div id="global-page-loader"
    class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50/10 backdrop-blur-sm  dark:bg-zinc-950 transition-opacity duration-700 ease-in-out">
    <style>
        .loader {
            width: 60px;
            aspect-ratio: 1;
            padding: 10px;
            box-sizing: border-box;
            display: grid;
        }

        .loader,
        .loader:before,
        .loader:after {
            --c: no-repeat linear-gradient(#046D8B 0 0);
            background: var(--c), var(--c), var(--c), var(--c);
            animation: l18-1 1.5s infinite cubic-bezier(0, 0, 1, 1), l18-2 1.5s infinite;
        }

        .loader:before,
        .loader:after {
            content: "";
            grid-area: 1/1;
            animation-timing-function: cubic-bezier(0, 0.2, 1, 1), linear;
        }

        .loader:after {
            margin: 10px;
            animation-timing-function: cubic-bezier(0, 0.4, 1, 1), linear;
        }

        @keyframes l18-1 {

            0%,
            10% {
                background-size: 0 4px, 4px 0
            }

            40%,
            60% {
                background-size: 100% 4px, 4px 100%
            }

            90%,
            100% {
                background-size: 0 4px, 4px 0
            }
        }

        @keyframes l18-2 {

            0%,
            49.9% {
                background-position: 0 0, 0 0, 100% 100%, 100% 100%
            }

            50%,
            100% {
                background-position: 100% 0, 0 100%, 0 100%, 100% 0
            }
        }
    </style>
    <div class="loader"></div>
    <div class="mt-8 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 animate-pulse">
        Initializing Secure Connection
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const maxWait = setTimeout(() => hideLoader(), 9000);

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
