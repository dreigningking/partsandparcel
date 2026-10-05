<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Parts & Parcel — Dashboard' }}</title>
    
    <script>
        function applyAppTheme(theme) {
            try {
                if (theme) {
                    localStorage.setItem('pp_theme_mode', theme);
                }
                const currentMode = theme || localStorage.getItem('pp_theme_mode') || 'system';
                const isDark = currentMode === 'dark' || (currentMode === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        }

        (function() {
            try {
                const userPref = @json(auth()->check() ? auth()->user()->theme_preference : null);
                const mode = userPref || localStorage.getItem('pp_theme_mode') || 'system';
                applyAppTheme(mode);
            } catch (e) {}
        })();

        window.addEventListener('pp-theme-changed', function(event) {
            const theme = (event.detail && typeof event.detail === 'object') ? (event.detail.theme || 'system') : (event.detail || 'system');
            applyAppTheme(theme);
        });

        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                const current = localStorage.getItem('pp_theme_mode') || 'system';
                if (current === 'system') {
                    applyAppTheme('system');
                }
            });
        }
    </script>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            pp: {
                                50: '#f5f3ff',
                                100: '#ebe7ff',
                                200: '#d9d2ff',
                                500: '#5140c8',
                                600: '#4634b7',
                                700: '#38299b',
                                800: '#2c1e7a',
                                900: '#20165f'
                            }
                        },
                        boxShadow: {
                            soft: '0 10px 35px -5px rgba(42,31,120,.08),0 4px 12px -2px rgba(0,0,0,.03)',
                            card: '0 4px 20px rgba(31,25,79,.06)'
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- ICONS & SHARED CUSTOM CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    @livewireStyles
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900">
    <div id="overlay" class="fixed inset-0 bg-slate-900/40 z-[70] hidden lg:hidden" onclick="toggleMobileSidebar()"></div>
    
    <!-- ELEGANT DASHBOARD SIDEBAR -->
    @include('layouts.partials.sidebar')

    <!-- MAIN AREA -->
    <div id="main" class="main-area ml-[280px] min-h-screen">
        <header class="sticky top-0 z-30 h-[72px] bg-white/95 backdrop-blur border-b border-slate-200">
            <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <!-- MOBILE BRAND LOGO (REPLACES TOGGLE SIDEBAR ON MOBILE) -->
                  <a class="flex items-center gap-2.5 lg:hidden" href="/" title="Parts & Parcel Home">
                      <div class="w-9 h-9 rounded-xl bg-pp-600 text-white grid place-items-center shadow-sm">
                          <svg viewBox="0 0 32 32" class="w-5 h-5" fill="none">
                              <path d="M16 3 27 9.2v13.6L16 29 5 22.8V9.2L16 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                              <path d="M16 3v13m11-6.8-11 6.8L5 9.2M16 16v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          </svg>
                      </div>
                      <span class="text-lg font-extrabold text-slate-900 tracking-tight">Parts &amp; Parcel</span>
                  </a>

                  <!-- DESKTOP RETRACT SIDEBAR TOGGLE -->
                  <button onclick="toggleSidebar()" class="hidden lg:grid w-10 h-10 place-items-center rounded-xl hover:bg-slate-100 text-slate-700 font-extrabold" title="Toggle Retract Sidebar">›</button>

                  <div class="hidden sm:flex flex-col">
                      <div class="text-xs text-slate-400">{{ Route::is('admin.*') ? 'Admin Console' : 'Dashboard' }}</div>
                      {{-- <b class="text-sm font-extrabold text-slate-900">Good morning, {{ auth()->user()->name ?? 'User' }}</b> --}}
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <a href="{{ route('welcome') }}" class="hidden md:flex items-center gap-1 px-3.5 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition border border-slate-200/80">Marketplace ↗</a>
                  
                  @include('layouts.partials.header-user-actions')
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 max-w-[1500px] mx-auto">
            {{$slot}}
        </main>
    </div>
    @include('layouts.partials.mobile-footer')
    <!-- GLOBAL DRAWERS & OVERLAYS -->
    @livewire('components.messaging.message-drawer')
    @livewire('components.messaging.conversation-drawer')
    @livewire('components.offers.quick-view-offers')
    @livewire('components.offers.make-offer')
    @livewire('components.offers.counter-offer-drawer')
    @livewire('components.report-modal')
    @livewire('components.add-location-modal')

    <!-- REVERB & LARAVEL ECHO CLIENT -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        if (typeof Pusher !== 'undefined' && typeof Echo !== 'undefined') {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ config('broadcasting.connections.reverb.key') ?? env('REVERB_APP_KEY', 'partsandparcelkey') }}',
                wsHost: '{{ config('broadcasting.connections.reverb.options.host') ?? env('REVERB_HOST', '127.0.0.1') }}',
                wsPort: {{ config('broadcasting.connections.reverb.options.port') ?? env('REVERB_PORT', 8080) }},
                wssPort: {{ config('broadcasting.connections.reverb.options.port') ?? env('REVERB_PORT', 8080) }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            });
        }
    </script>

    @livewireScripts

    <!-- SHARED CUSTOM JS -->
    <script src="{{ asset('js/app-custom.js') }}"></script>
    <script src="{{ asset('js/webcam-capture.js') }}"></script>
    @stack('scripts')
</body>

</html>
