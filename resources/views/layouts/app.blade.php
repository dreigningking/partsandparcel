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
          }
          </script>
      @endif

      <!-- SHARED CUSTOM CSS -->
      <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

      @livewireStyles
  </head>
  <body class="bg-slate-50/50 text-slate-900 pb-16 lg:pb-0">
      @include('layouts.partials.header')
      
      <main>
          {{ $slot }}
          @include('layouts.partials.footer')
      </main> 
      
      @include('layouts.partials.mobile-footer')

      <!-- GLOBAL DRAWERS & OVERLAYS -->
      @include('layouts.partials.drawers')

      @livewireScripts

      <!-- SHARED CUSTOM JS -->
      <script src="{{ asset('js/app-custom.js') }}"></script>
  </body>
</html>
