{{-- Toast Container --}}
<ol id="sonner-toast-container" 
    position="bottom-right" 
    max-toasts="3" 
    rich-colors="true">
</ol>

{{-- Flash Messages --}}
@if(session('success') || session('error') || session('warning') || session('info') || session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Wait for Vite to load app.js and expose window.toast
            setTimeout(() => {
                if (typeof window.toast === 'undefined') return;
                
                @if(session('success'))
                toast.success({!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                toast.error({!! json_encode(session('error')) !!});
            @endif

            @if(session('warning'))
                toast.warning({!! json_encode(session('warning')) !!});
            @endif

            @if(session('info'))
                toast.info({!! json_encode(session('info')) !!});
            @endif

            @if(session('status'))
                toast({!! json_encode(session('status')) !!});
            @endif
            }, 100);
        });
    </script>
@endif
