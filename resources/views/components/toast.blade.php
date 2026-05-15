@props(['message' => null, 'type' => 'info'])

<script>
    (function() {
        const triggerToasts = () => {
            if (typeof window.toast === 'undefined') {
                // Poll for window.toast (provided by app.js module)
                setTimeout(triggerToasts, 100);
                return;
            }

            @if($message)
                window.toast.{{ $type }}({!! json_encode($message) !!});
            @endif

            {{-- Only handle session and global errors if this is the "default" call (no specific message) --}}
            @if(!$message)
                @if(session('success'))
                    window.toast.success({!! json_encode(session('success')) !!});
                @endif

                @if(session('error'))
                    window.toast.error({!! json_encode(session('error')) !!});
                @endif

                @if(session('warning'))
                    window.toast.warning({!! json_encode(session('warning')) !!});
                @endif

                @if(session('info'))
                    window.toast.info({!! json_encode(session('info')) !!});
                @endif

                @if(session('status'))
                    window.toast({!! json_encode(session('status')) !!});
                @endif

                @if($errors->any())
                    @foreach($errors->all() as $error)
                        window.toast.error({!! json_encode($error) !!});
                    @endforeach
                @endif
            @endif
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', triggerToasts);
        } else {
            triggerToasts();
        }
    })();
</script>
