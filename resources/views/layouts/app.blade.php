<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <title>{{ $title ?? config('app.name') }}</title>

      @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
          @vite(['resources/css/app.css', 'resources/js/app.js'])
      @else
          <script  src="https://cdn.tailwindcss.com"></script>
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
          <style>
              @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
              html { scroll-behavior: smooth; }
              body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif; }
              .mega-menu { display:none; }
              .mega-menu.open { display:block; }
              .nav-chevron { transition: transform .2s ease; }
              .nav-trigger.open .nav-chevron { transform: rotate(180deg); }
              .hero-grid {
              background-image:
                  linear-gradient(rgba(81,64,200,.03) 1px, transparent 1px),
                  linear-gradient(90deg, rgba(81,64,200,.03) 1px, transparent 1px);
              background-size: 32px 32px;
              }
              .product-img {
              background: radial-gradient(circle at 50% 40%, #ffffff 0%, #f8f7ff 60%, #eeeefc 100%);
              }
          </style>    
      @endif

      @livewireStyles
  </head>
  <body class="bg-slate-50/50 text-slate-900 pb-16 lg:pb-0">
      @include('layouts.partials.header')
      <main>
          {{ $slot }}
          @include('layouts.partials.footer')
      </main> 
      @include('layouts.partials.mobile-footer')

      @livewireScripts
      <!-- SCRIPT FOR MEGA MENU INTERACTIVITY & ACCOUNT DROPDOWN -->
      <script>
        const triggers = document.querySelectorAll('.nav-trigger');
      const menus = document.querySelectorAll('.mega-menu');

      triggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          const key = trigger.dataset.menu;
          const menu = document.getElementById('mega-' + key);
          const isOpen = menu ? menu.classList.contains('open') : false;

          menus.forEach(m => m.classList.remove('open'));
          triggers.forEach(t => t.classList.remove('open'));

          if (!isOpen && menu) {
            menu.classList.add('open');
            trigger.classList.add('open');
          }
        });
      });

      menus.forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
      });

      document.addEventListener('click', () => {
        menus.forEach(m => m.classList.remove('open'));
        triggers.forEach(t => t.classList.remove('open'));
        document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
      });

      const accountTriggers = document.querySelectorAll('.account-menu-trigger');
      accountTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          const wrapper = trigger.closest('.account-menu-wrapper');
          const panel = wrapper ? wrapper.querySelector('.account-dropdown-panel') : null;
          const chevron = trigger.querySelector('.chevron-icon');
          const isOpen = panel ? !panel.classList.contains('hidden') : false;

          menus.forEach(m => m.classList.remove('open'));
          triggers.forEach(t => t.classList.remove('open'));
          document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
          document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));

          if (!isOpen && panel) {
            panel.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
          }
        });
      });

      function setThemeMode(mode, event) {
        if (event) event.stopPropagation();
        const btns = document.querySelectorAll('.theme-btn');
        btns.forEach(b => {
          b.classList.remove('bg-white', 'shadow-xs', 'font-bold', 'text-slate-900');
          b.classList.add('font-semibold', 'text-slate-600');
        });

        if (event && event.target) {
          event.target.classList.add('bg-white', 'shadow-xs', 'font-bold', 'text-slate-900');
          event.target.classList.remove('text-slate-600');
        }

        const label = document.getElementById('current-mode-label');
        if (label) label.textContent = mode.charAt(0).toUpperCase() + mode.slice(1);

        if (mode === 'dark') {
          document.documentElement.classList.add('dark');
        } else if (mode === 'light') {
          document.documentElement.classList.remove('dark');
        } else {
          if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        }
      }
    </script>

  </body>
</html>
