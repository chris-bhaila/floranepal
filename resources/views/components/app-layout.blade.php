<!-- resources/views/components/app-layout.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FloraNepal' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/FloraNepalLogoOnly.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.55s ease forwards;
        }

        .fade-up>* {
            animation: fadeUp 0.55s ease forwards;
            opacity: 0;
        }

        .fade-up>*:nth-child(1) {
            animation-delay: 0.05s;
        }

        .fade-up>*:nth-child(2) {
            animation-delay: 0.15s;
        }

        .fade-up>*:nth-child(3) {
            animation-delay: 0.25s;
        }

        .fade-up>*:nth-child(4) {
            animation-delay: 0.35s;
        }

        .fade-up>*:nth-child(5) {
            animation-delay: 0.45s;
        }

        .fade-up>*:nth-child(6) {
            animation-delay: 0.55s;
        }

        .fade-up>*:nth-child(7) {
            animation-delay: 0.65s;
        }

        .fade-up>*:nth-child(8) {
            animation-delay: 0.75s;
        }

        .fade-up>*:nth-child(9) {
            animation-delay: 0.85s;
        }

        .fade-up>*:nth-child(10) {
            animation-delay: 0.95s;
        }

        .fade-up>*:nth-child(11) {
            animation-delay: 1.05s;
        }
    </style>
</head>
<!-- <script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const token = localStorage.getItem('auth_token');
    //     if (token) {
    //         fetch('/auth/mobile/login', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
    //                 },
    //                 body: JSON.stringify({
    //                     token: token
    //                 })
    //             }).then(response => response.json())
    //             .then(data => {
    //                 if (data.success) {
    //                     localStorage.removeItem('auth_token');
    //                     window.location.reload();
    //                 }
    //             });
    //     }
    // });

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('auth_token');
        console.log('auth_token found:', token ? 'yes' : 'no');
        if (token) {
            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            console.log('csrf meta found:', csrfMeta ? 'yes' : 'no');

            fetch('/auth/mobile/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : ''
                    },
                    body: JSON.stringify({
                        token: token
                    })
                })
                .then(r => {
                    console.log('Response status:', r.status);
                    return r.json();
                })
                .then(data => {
                    console.log('Response data:', JSON.stringify(data));
                    if (data.success) {
                        localStorage.removeItem('auth_token');
                        window.location.reload();
                    }
                })
                .catch(err => console.log('Fetch error:', err.message));
        }
    });
</script> -->

<body class="font-sans h-screen">

    {{ $slot }}

    <footer class="w-full py-4 text-center" style="background: transparent;">
        <a href="{{ route('privacy-policy') }}"
           class="text-xs"
           style="color: rgba(134,239,172,0.35);">
            Privacy Policy
        </a>
    </footer>

    {{-- Toast container --}}
    <div x-data="fnToast()" @toast.window="add($event.detail)"
         class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-80 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div x-show="t.visible"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-6"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-6"
                 class="pointer-events-auto relative overflow-hidden rounded-xl shadow-2xl border"
                 :class="{
                     'bg-gray-900 border-green-500/30': t.type === 'success',
                     'bg-gray-900 border-red-500/30':   t.type === 'error',
                     'bg-gray-900 border-amber-500/30': t.type === 'warning',
                     'bg-gray-900 border-blue-500/30':  t.type === 'info',
                 }">
                <div class="flex items-start gap-3 px-4 pt-4 pb-3">
                    {{-- Icon --}}
                    <span class="shrink-0 mt-0.5 w-5 h-5"
                          :class="{
                              'text-green-400': t.type === 'success',
                              'text-red-400':   t.type === 'error',
                              'text-amber-400': t.type === 'warning',
                              'text-blue-400':  t.type === 'info',
                          }"
                          x-html="icons[t.type]"></span>
                    {{-- Text --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold leading-snug"
                           :class="{
                               'text-green-300': t.type === 'success',
                               'text-red-300':   t.type === 'error',
                               'text-amber-300': t.type === 'warning',
                               'text-blue-300':  t.type === 'info',
                           }"
                           x-text="t.title"></p>
                        <p class="text-xs text-gray-400 mt-0.5 leading-relaxed" x-text="t.message"></p>
                    </div>
                    {{-- Close --}}
                    <button @click="dismiss(t.id)"
                            class="shrink-0 text-gray-500 hover:text-gray-300 transition-colors -mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Progress bar --}}
                <div class="h-0.5 w-full"
                     :class="{
                         'bg-green-900/40': t.type === 'success',
                         'bg-red-900/40':   t.type === 'error',
                         'bg-amber-900/40': t.type === 'warning',
                         'bg-blue-900/40':  t.type === 'info',
                     }">
                    <div class="h-full origin-left"
                         :class="{
                             'bg-green-500': t.type === 'success',
                             'bg-red-500':   t.type === 'error',
                             'bg-amber-500': t.type === 'warning',
                             'bg-blue-500':  t.type === 'info',
                         }"
                         x-init="$el.animate([{transform:'scaleX(1)'},{transform:'scaleX(0)'}],
                                 {duration: 3500, fill: 'forwards', easing: 'linear'})">
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function fnToast() {
            return {
                toasts: [],
                icons: {
                    success: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
                    error:   `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
                    warning: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
                    info:    `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                },
                defaultTitle: { success: 'Success', error: 'Error', warning: 'Warning', info: 'Info' },
                add({ type = 'info', message = '', title = '' }) {
                    const id = Date.now();
                    this.toasts.push({ id, type, message, title: title || this.defaultTitle[type], visible: true });
                    setTimeout(() => this.dismiss(id), 3700);
                },
                dismiss(id) {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) {
                        t.visible = false;
                        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 250);
                    }
                },
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            const token = localStorage.getItem('auth_token');
            if (!token) return;
            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            fetch('/auth/mobile/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '' },
                body: JSON.stringify({ token }),
            })
            .then(r => r.json())
            .then(data => { if (data.success) { localStorage.removeItem('auth_token'); window.location.href = '/dashboard'; } })
            .catch(() => {});

            @if(session('success'))
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: @json(session('success')) } }));
            @endif
            @if(session('error'))
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: @json(session('error')) } }));
            @endif
            @if(session('warning'))
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', message: @json(session('warning')) } }));
            @endif
            @if(session('info'))
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'info', message: @json(session('info')) } }));
            @endif
        });
    </script>
</body>

</html>