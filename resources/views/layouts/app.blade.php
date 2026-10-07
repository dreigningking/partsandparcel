<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <title>{{ $title ?? config('app.name') }}</title>

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
                const mode = localStorage.getItem('pp_theme_mode') || 'system';
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
                    screens: {
                        'lg': '1025px',
                        'xl': '1225px',
                    },
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
                        soft: '0 10px 35px -5px rgba(42, 31, 120, .08), 0 4px 12px -2px rgba(0,0,0,0.03)',
                        card: '0 4px 20px rgba(31, 25, 79, .06)',
                        hover: '0 12px 30px rgba(81, 64, 200, .12)'
                    }
                }
            }
          }
          </script>
      @endif

      <!-- ICONS & SHARED CUSTOM CSS -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}?v={{ file_exists(public_path('css/app-custom.css')) ? filemtime(public_path('css/app-custom.css')) : time() }}">

      @livewireStyles
      @stack('styles')
  </head>
  <body class="bg-slate-50/50 text-slate-900 pb-16 lg:pb-0">
      <div id="overlay" class="fixed inset-0 bg-slate-900/40 z-[70] hidden lg:hidden" onclick="toggleMobileSidebar()"></div>
      
      @include('layouts.partials.header')
      @include('layouts.partials.sidebar', ['sidebarClass' => 'lg:hidden'])
      
      <main>
          {{ $slot }}
          @include('layouts.partials.footer')
      </main> 
      
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
      <script src="{{ asset('js/app-custom.js') }}?v={{ file_exists(public_path('js/app-custom.js')) ? filemtime(public_path('js/app-custom.js')) : time() }}"></script>
      @stack('scripts')
  </body>
</html>
