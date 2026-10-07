<div class="flex items-center gap-1.5 sm:gap-2.5">
    
    <!-- COMMUNITY MENU LINK (WEB ONLY — FIRST ITEM) -->
    <a href="{{ route('community') }}" class="hidden lg:flex items-center gap-2 px-3.5 py-3 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 border border-pp-200/80 text-xs font-extrabold transition shadow-2xs mr-1" title="Parts & Parcel Community">
        <svg class="w-4 h-4 text-pp-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <span>Community</span>
    </a>

    <!-- BLOG & KNOWLEDGE BASE MENU LINK -->
    <a href="{{ route('blog.index') }}" class="hidden lg:flex items-center gap-2 px-3.5 py-3 rounded-xl hover:bg-slate-100 text-slate-700 hover:text-pp-700 border border-slate-200/80 text-xs font-extrabold transition shadow-2xs mr-1" title="Parts & Parcel Technical Guides & Blog">
        <svg class="w-4 h-4 text-pp-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
            <path d="M6 6h10"></path>
            <path d="M6 10h10"></path>
        </svg>
        <span>Blog</span>
    </a>

    <!-- CART (LIVEWIRE REACTIVE COUNTER) -->
    @livewire('components.header.cart-counter', ['variant' => 'desktop'], key('header-cart-counter'))

    @auth
        <!-- MESSAGES BUTTON & REAL-TIME BADGE -->
        @livewire('components.messaging.message-counter-badge')

        <!-- NOTIFICATIONS BUTTON & DROPDOWN (LIVEWIRE COMPONENT) -->
        @livewire('components.header.notification-dropdown')

        <!-- ACCOUNT DROPDOWN MENU -->
        <div class="relative inline-block text-left account-menu-wrapper">
            @php
                $currentUser = auth()->user();
                $userInitials = $currentUser ? collect(explode(' ', $currentUser->name))->map(fn($seg) => strtoupper(substr($seg, 0, 1)))->take(2)->join('') : 'U';
                $roleName = $currentUser?->isAdmin() ? ($currentUser->role?->name ?? 'Administrator') : 'Member';
            @endphp
            <button type="button" class="account-menu-trigger flex items-center gap-2 p-1.5 pl-2.5 pr-3 rounded-xl border border-slate-200 hover:border-pp-300 text-xs font-semibold text-slate-800 transition bg-white shadow-xs cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-pp-600 text-white font-bold text-xs grid place-items-center shadow-xs">{{ $userInitials }}</div>
                <span class="hidden sm:inline-block max-w-[100px] truncate">{{ Str::limit($currentUser?->name ?? 'Account', 12) }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5 transition-transform duration-200 chevron-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </button>

            <!-- DROPDOWN PANEL -->
            <div class="account-dropdown-panel hidden absolute right-0 mt-2 w-60 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs animate-in fade-in slide-in-from-top-2 duration-200">
                <!-- USER PROFILE HEADER -->
                <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-pp-600 text-white font-bold text-xs grid place-items-center shadow-xs">{{ $userInitials }}</div>
                        <div class="overflow-hidden">
                            <p class="font-extrabold text-slate-900 truncate">{{ $currentUser?->name ?? 'User' }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $currentUser?->email ?? '' }}</p>
                            <div class="text-[11px] {{ $currentUser?->isAdmin() ? 'text-emerald-600 font-bold' : 'text-slate-500 font-semibold' }}">{{ $roleName }}</div>
                        </div>
                    </div>
                </div>

                <!-- DASHBOARD & NAVIGATION LINKS -->
                <div class="py-1.5 border-b border-slate-100">
                    @if($currentUser?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-pp-700 bg-pp-50/70 hover:bg-pp-100 font-bold transition">
                            <span class="text-base">🛡</span>
                            <span>Admin Console</span>
                        </a>
                    @endif

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
                        <span class="text-base">📊</span>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('subscriptions') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
                        <span class="text-base">💳</span>
                        <span>Subscription</span>
                        <span class="ml-auto text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">Free</span>
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
                        <span class="text-base">👤</span>
                        <span>Profile</span>
                    </a>
                </div>

                <!-- COLOR MODE SELECTOR -->
                <div class="px-4 py-3 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Color Mode</span>
                        <span id="current-mode-label" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Light</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1 rounded-xl text-center">
                        <button type="button" onclick="setThemeMode('light', event)" class="theme-btn active py-1 rounded-lg text-[10px] font-bold text-slate-900 bg-white shadow-xs transition">Light</button>
                        <button type="button" onclick="setThemeMode('dark', event)" class="theme-btn py-1 rounded-lg text-[10px] font-semibold text-slate-600 hover:text-slate-900 transition">Dark</button>
                        <button type="button" onclick="setThemeMode('system', event)" class="theme-btn py-1 rounded-lg text-[10px] font-semibold text-slate-600 hover:text-slate-900 transition">System</button>
                    </div>
                </div>

                <!-- LOGOUT -->
                <div class="pt-1">
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-rose-600 hover:bg-rose-50 font-extrabold transition cursor-pointer text-left">
                            <span class="text-base">🚪</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- GUEST THEME TOGGLE BUTTON -->
        <button type="button" onclick="toggleThemeMode(event)" aria-label="Toggle Theme" class="p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer" title="Toggle color theme">
            <!-- SUN ICON (visible when dark) -->
            <svg class="w-5 h-5 hidden dark:block text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
            </svg>
            <!-- MOON ICON (visible when light) -->
            <svg class="w-5 h-5 block dark:hidden text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>
        </button>

        <!-- GUEST COMPACT LOGIN ICON (VISIBLE FROM 1025px TO 1224px) -->
        <a href="{{ route('login') }}" class="hidden lg:flex xl:hidden p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 border border-slate-200/80 transition ml-1" title="Login to your account" aria-label="Login">
            <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </a>

        <!-- GUEST BUTTONS: LOGIN & REGISTER (VISIBLE ON 1225px AND ABOVE) -->
        <div class="hidden xl:flex items-center gap-2 ml-1">
            <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:border-pp-300 text-xs font-bold text-slate-700 hover:text-pp-700 hover:bg-slate-50 transition shadow-2xs">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold transition shadow-xs hover:shadow flex items-center gap-1.5">
                <span>Register</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    @endauth
</div>