<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Parts & Parcel — Dashboard' }}</title>
    
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
                            soft: '0 10px 35px -5px rgba(42,31,120,.08),0 4px 12px -2px rgba(0,0,0,.03)',
                            card: '0 4px 20px rgba(31,25,79,.06)'
                        }
                    }
                }
            }
        </script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
            [x-cloak] { display: none !important; }
            * {
                font-family: Inter, ui-sans-serif, system-ui, sans-serif
            }

            .sidebar {
                transition: width .25s, transform .25s
            }

            .sidebar.collapsed {
                width: 76px
            }

            .sidebar.collapsed .label,
            .sidebar.collapsed .section-label,
            .sidebar.collapsed .user-meta,
            .sidebar.collapsed .chevron,
            .sidebar.collapsed .section-title,
            .sidebar.collapsed .brand-text,
            .sidebar.collapsed .logo-toggle {
                display: none
            }

            .sidebar.collapsed .nav-item {
                justify-content: center
            }

            .submenu {
                max-height: 0;
                overflow: hidden;
                transition: max-height .25s
            }

            .submenu.open {
                max-height: 500px
            }

            .main-area {
                transition: margin-left .25s
            }

            @media(max-width:1023px) {
                .sidebar {
                    width: 280px !important;
                    transform: translateX(-100%);
                    position: fixed !important
                }

                .sidebar.mobile-open {
                    transform: translateX(0)
                }

                .main-area {
                    margin-left: 0 !important
                }
            }
        </style>
    @endif

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
</head>

<body class="bg-slate-50 text-slate-900">
    <div id="overlay" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden" onclick="toggleMobileSidebar()"></div>
    
    <aside id="sidebar"
        class="sidebar fixed left-0 top-0 bottom-0 z-50 w-[280px] bg-white border-r border-slate-200 flex flex-col">
        <div class="h-[72px] px-5 flex items-center justify-between border-b border-slate-100"><a
                class="flex items-center gap-2.5" href="/">
                <div class="w-10 h-10 rounded-xl bg-pp-600 text-white grid place-items-center font-black">P</div><span
                    class="brand-text text-lg font-extrabold">Parts &amp; Parcel</span>
            </a><button onclick="toggleSidebar()"
                class="logo-toggle hidden lg:grid w-8 h-8 place-items-center rounded-lg hover:bg-slate-100">‹</button></div>
        <div class="p-4 border-b border-slate-100">
            <div class="user-card flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-bold grid place-items-center">OS</div>
                <div class="user-meta min-w-0"><b class="text-sm">Olu Samuel</b>
                    <div class="text-[11px] text-slate-500">Free Member · Buyer &amp; Seller</div>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-2">
            <div>
                <div
                    class="section-label px-3 pt-2 pb-2 text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">
                    General</div><a
                    class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl bg-pp-50 text-pp-700 font-semibold text-sm"
                    href="{{ route('dashboard') }}">⌂<span class="label">Overview</span></a><a
                    class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 text-sm"
                    href="#">◎<span class="label">Profile</span></a><a
                    class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 text-sm"
                    href="#">◈<span class="label">Subscriptions</span><span
                        class="label ml-auto text-[10px] px-2 py-0.5 rounded-full bg-slate-200">Free</span></a>
            </div>
            <div><button onclick="openSection('buyer')"
                    class="section-title w-full flex justify-between px-3 py-2 text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-500"><span
                        class="section-label">● &nbsp; Buying</span><span id="bc"
                        class="chevron">⌄</span></button>
                <div id="buyer" class="submenu open pl-1"><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">▦<span class="label">My Purchases</span><span
                            class="label ml-auto text-[10px] bg-pp-100 text-pp-700 rounded-full px-2">2</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">🛒<span class="label">Cart</span><span
                            class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2">3</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">♡<span class="label">Saved Listings</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">◇<span class="label">My Offers</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">💬<span class="label">My Discussions</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">📍<span class="label">My Locations</span></a></div>
            </div>
            <div><button onclick="openSection('seller')"
                    class="section-title w-full flex justify-between px-3 py-2 text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-500"><span
                        class="section-label">● &nbsp; Selling</span><span id="sc"
                        class="chevron">⌄</span></button>
                <div id="seller" class="submenu pl-1"><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">▣<span class="label">Seller Overview</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">💻<span class="label">My Devices &amp; Parts</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">⌁<span class="label">My Listings</span><span
                            class="label ml-auto text-[10px] bg-slate-100 rounded-full px-2">8</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">◇<span class="label">Offers Received</span><span
                            class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2">4</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">₦<span class="label">Sales &amp; Invoices</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">⇢<span class="label">Shipments</span></a><a
                        class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                        href="#">₦<span class="label">Payouts</span></a></div>
            </div>
            <div>
                <div
                    class="section-label px-3 pt-2 pb-2 text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">
                    Community</div><a
                    class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                    href="#">💬<span class="label">Messages</span><span
                        class="label ml-auto w-2 h-2 rounded-full bg-pp-600"></span></a><a
                    class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600"
                    href="#">◎<span class="label">Forum</span></a>
            </div>
        </nav>
        <div class="p-3 border-t border-slate-100"><a
                class="nav-item flex gap-3 px-3 py-2.5 rounded-xl hover:bg-red-50 text-sm text-slate-500"
                href="/">↪<span class="label">Logout</span></a></div>
    </aside>

    <div id="main" class="main-area ml-[280px] min-h-screen">
        <header class="sticky top-0 z-30 h-[72px] bg-white/95 backdrop-blur border-b border-slate-200">
            <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <button onclick="toggleMobileSidebar()" class="lg:hidden w-10 h-10 rounded-xl hover:bg-slate-100">☰</button>
                  <button onclick="toggleSidebar()" class="hidden lg:grid w-10 h-10 place-items-center rounded-xl hover:bg-slate-100">›</button>
                  <div>
                      <div class="text-xs text-slate-400">Dashboard</div><b class="text-sm">Good morning, Olu 👋</b>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <a href="{{ route('welcome') }}" class="hidden md:block px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">← Back to Marketplace</a>
                  
                  @include('layouts.partials.header-user-actions')
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 max-w-[1500px] mx-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

    <script>
        let collapsed = false;

        function toggleSidebar() {
            collapsed = !collapsed;
            document.getElementById('sidebar').classList.toggle('collapsed', collapsed);
            document.getElementById('main').style.marginLeft = collapsed ? '76px' : '280px'
        }

        function openSection(s) {
            let b = document.getElementById('buyer'),
                v = document.getElementById('seller');
            if (s === 'buyer') {
                b.classList.toggle('open');
                v.classList.remove('open')
            } else {
                v.classList.toggle('open');
                b.classList.remove('open')
            }
        }

        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('overlay').classList.toggle('hidden')
        }

        // ACCOUNT DROPDOWN TOGGLE LOGIC FOR VANILLA JS
        document.addEventListener('DOMContentLoaded', () => {
            const accountTriggers = document.querySelectorAll('.account-menu-trigger');
            accountTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const wrapper = trigger.closest('.account-menu-wrapper');
                    const panel = wrapper ? wrapper.querySelector('.account-dropdown-panel') : null;
                    const chevron = trigger.querySelector('.chevron-icon');
                    const isOpen = panel ? !panel.classList.contains('hidden') : false;

                    document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
                    document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));

                    if (!isOpen && panel) {
                        panel.classList.remove('hidden');
                        if (chevron) chevron.classList.add('rotate-180');
                    }
                });
            });

            document.addEventListener('click', () => {
                document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
                document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
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
