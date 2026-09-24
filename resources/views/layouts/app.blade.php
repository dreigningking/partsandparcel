<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <title>{{ $title ?? config('app.name') }}</title>

      @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
          @vite(['resources/css/app.css', 'resources/js/app.js'])
      @else
          <script src="https://cdn.tailwindcss.com"></script>
          <script>
          tailwind.config = {
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
                    soft: '0 10px 35px -5px rgba(42, 31, 120, .08), 0 4px 12px -2px rgba(0,0,0,0.03)',
                    card: '0 4px 20px rgba(31, 25, 79, .06)',
                    hover: '0 12px 30px rgba(81, 64, 200, .12)'
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

      @livewireScripts
      
      <!-- SHARED CUSTOM JS -->
      <script src="{{ asset('js/app-custom.js') }}"></script>
      @stack('scripts')
  </body>
</html>
