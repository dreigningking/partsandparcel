<div class="flex items-center gap-1.5 sm:gap-2.5">
    
    <!-- WISHLIST -->
    <a href="buyer-dashboard.html?tab=saved" aria-label="Wishlist" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Saved Wishlist">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold grid place-items-center">5</span>
    </a>

    <!-- NOTIFICATIONS -->
    <button aria-label="Notifications" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Notifications">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-amber-500 text-white text-[10px] font-bold grid place-items-center">2</span>
    </button>

    <button aria-label="Message" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Message">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-amber-500 text-white text-[10px] font-bold grid place-items-center">2</span>
    </button>

    <!-- CART -->
    <a href="cart.html" aria-label="Cart" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition" title="Shopping Cart">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-pp-600 text-white text-[10px] font-bold grid place-items-center">3</span>
    </a>

    <!-- ACCOUNT DROPDOWN MENU -->
    <div class="relative inline-block text-left account-menu-wrapper" x-data="{ open: false }">
        <button type="button" 
                @click="open = !open"
                class="account-menu-trigger hidden sm:flex items-center gap-2 p-1.5 pl-2.5 pr-3 rounded-xl border border-slate-200 hover:border-pp-300 text-xs font-semibold text-slate-800 transition bg-white shadow-xs cursor-pointer">
            <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold text-xs grid place-items-center">A</div>
            <span>Account</span>
            <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5 transition-transform duration-200 chevron-icon" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>

        <!-- DROPDOWN PANEL -->
        <div class="account-dropdown-panel absolute right-0 mt-2 w-60 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs animate-in fade-in slide-in-from-top-2 duration-200"
             :class="{ 'hidden': !open }"
             @click.outside="open = false">
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
                <a href="index.html" class="flex items-center gap-3 px-4 py-2 text-rose-600 hover:bg-rose-50 font-extrabold transition">
                    <span class="text-base">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>