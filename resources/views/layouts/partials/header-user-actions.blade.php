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

    <!-- MESSAGES BUTTON -->
    <button onclick="openMessages()" aria-label="Message" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer" title="Messages">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-pp-600 text-white text-[10px] font-bold grid place-items-center">3</span>
    </button>

    <!-- NOTIFICATIONS BUTTON & DROPDOWN -->
    <div class="relative">
        <button onclick="toggleNotificationsDropdown()" aria-label="Notifications" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer" title="Notifications">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold grid place-items-center">5</span>
        </button>

        <!-- NOTIFICATIONS DROPDOWN PANEL -->
        <div id="notifications" class="dropdown absolute right-0 top-12 w-[350px] max-w-[calc(100vw-24px)] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[80]">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <div class="font-extrabold text-slate-900 text-sm">Notifications</div>
                    <div class="text-[11px] text-slate-500">5 unread messages &amp; alerts</div>
                </div>
                <a href="#" class="text-xs font-bold text-pp-600 hover:underline">See all</a>
            </div>
            <div class="max-h-[360px] overflow-y-auto custom-scrollbar divide-y divide-slate-100 bg-white">
                <a href="#" class="flex gap-3 p-4 bg-pp-50/60 hover:bg-slate-50 transition">
                    <span class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 grid place-items-center shrink-0 font-bold">✓</span>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Your offer was accepted</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">HP EliteBook 840 G5 · 5 min ago</div>
                    </div>
                </a>
                <a href="#" class="flex gap-3 p-4 hover:bg-slate-50 transition">
                    <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 grid place-items-center shrink-0 text-sm">💬</span>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Seller responded to your question</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Dell Latitude motherboard · 24 min ago</div>
                    </div>
                </a>
                <a href="#" class="flex gap-3 p-4 hover:bg-slate-50 transition">
                    <span class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 grid place-items-center shrink-0 text-sm">🛒</span>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Your cart is waiting</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">3 items across 2 sellers · 1 hour ago</div>
                    </div>
                </a>
                <a href="#" class="flex gap-3 p-4 hover:bg-slate-50 transition">
                    <span class="w-9 h-9 rounded-lg bg-violet-100 text-violet-700 grid place-items-center shrink-0 text-sm">♡</span>
                    <div>
                        <div class="text-xs font-bold text-slate-900">A saved listing changed price</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">HP EliteBook battery · 2 hours ago</div>
                    </div>
                </a>
            </div>
            <a href="#" class="block text-center p-3 text-xs font-bold text-pp-600 hover:bg-slate-50 border-t border-slate-100 bg-white">View all notifications →</a>
        </div>
    </div>

    <!-- CART -->
    <a href="{{ route('cart') }}" aria-label="Cart" class="hidden sm:flex relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Shopping Cart">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-pp-600 text-white text-[10px] font-bold grid place-items-center">3</span>
    </a>

    <!-- ACCOUNT DROPDOWN MENU -->
    <div class="relative inline-block text-left account-menu-wrapper">
        <button type="button" class="account-menu-trigger hidden sm:flex items-center gap-2 p-1.5 pl-2.5 pr-3 rounded-xl border border-slate-200 hover:border-pp-300 text-xs font-semibold text-slate-800 transition bg-white shadow-xs cursor-pointer">
            <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold text-xs grid place-items-center">A</div>
            <span>Account</span>
            <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5 transition-transform duration-200 chevron-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>

        <!-- DROPDOWN PANEL -->
        <div class="account-dropdown-panel hidden absolute right-0 mt-2 w-60 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs animate-in fade-in slide-in-from-top-2 duration-200">
            <!-- USER PROFILE HEADER -->
            <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-pp-600 text-white font-bold text-xs grid place-items-center shadow-xs">AO</div>
                    <div class="overflow-hidden">
                        <p class="font-extrabold text-slate-900 truncate">Abel Okon</p>
                        <p class="text-[11px] text-slate-500 truncate">abel.parts@example.ng</p>
                    </div>
                </div>
            </div>

            <!-- DASHBOARD & NAVIGATION LINKS -->
            <div class="py-1.5 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
                    <span class="text-base">📊</span>
                    <span>Dashboard</span>
                </a>
                
                <a href="subscriptions.html" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
                    <span class="text-base">💳</span>
                    <span>Subscription</span>
                    <span class="ml-auto text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">Free</span>
                </a>
                <a href="{{ route('dashboard', ['tab' => 'profile']) }}" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-pp-50 hover:text-pp-700 font-semibold transition">
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
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-rose-600 hover:bg-rose-50 font-extrabold transition">
                    <span class="text-base">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>