<aside id="sidebar" class="sidebar {{ $sidebarClass ?? '' }} fixed left-0 top-0 bottom-0 z-[80] w-[280px] bg-white border-r border-slate-200/80 flex flex-col shadow-soft">
        
    <!-- BRAND LOGO & RETRACT TOGGLE (HIDDEN ON MOBILE VIEW) -->
    <div class="h-[72px] px-5 hidden lg:flex items-center justify-between border-b border-slate-100 shrink-0">
        <a class="flex items-center gap-2.5" href="/">
            <div class="w-10 h-10 rounded-xl bg-pp-600 text-white grid place-items-center shadow-sm">
                <svg viewBox="0 0 32 32" class="w-6 h-6" fill="none">
                    <path d="M16 3 27 9.2v13.6L16 29 5 22.8V9.2L16 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M16 3v13m11-6.8-11 6.8L5 9.2M16 16v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="brand-text text-xl font-extrabold text-slate-900 tracking-tight">Parts &amp; Parcel</span>
        </a>
        <button onclick="toggleSidebar()"
            class="logo-toggle hidden lg:grid w-8 h-8 place-items-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition" title="Toggle Retract Sidebar">‹</button>
    </div>

    <!-- NAVIGATION ACCORDIONS -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar p-3.5 space-y-3">
        
        <!-- 1. GENERAL NAVIGATION SECTION -->
        <div>
            <div class="section-label px-3 pt-1 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">
                General Navigation
            </div>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl bg-pp-50 text-pp-700 font-extrabold text-xs transition shadow-2xs"
                href="{{ route('dashboard') }}">
                <span class="text-base shrink-0">📊</span>
                <span class="label">Overview</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="subscriptions.html">
                <span class="text-base shrink-0">⚡</span>
                <span class="label">Subscription</span>
                <span class="label ml-auto text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">Free</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="#">
                <span class="text-base shrink-0">💬</span>
                <span class="label">Messages</span>
                <span class="label ml-auto w-2 h-2 rounded-full bg-pp-600"></span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="{{ route('dashboard', ['tab' => 'offers']) }}">
                <span class="text-base shrink-0">🏷️</span>
                <span class="label">Offers</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="invoices.html">
                <span class="text-base shrink-0">📄</span>
                <span class="label">Invoices</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="shipments.html">
                <span class="text-base shrink-0">📦</span>
                <span class="label">Shipments</span>
            </a>
        </div>

        <!-- 2. BUYING ACCORDION (COLLAPSED BY DEFAULT) -->
        <div class="pt-1">
            <button onclick="openSection('buyer')"
                class="section-title w-full flex items-center justify-between p-2.5 rounded-2xl text-xs font-extrabold transition duration-200 bg-pp-50 text-pp-700 border border-pp-200">
                <div class="flex items-center gap-2.5">
                    <span class="text-base shrink-0">🛒</span>
                    <span class="label font-extrabold">Buying</span>
                </div>
                <div class="chevron-wrapper flex items-center gap-1.5">
                    <span class="label px-1.5 py-0.5 rounded-full bg-pp-200 text-pp-800 text-[10px] font-bold">2</span>
                    <svg id="bc" class="w-4 h-4 transition-transform duration-200 chevron" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>

            <div id="buyer" class="submenu pl-2 border-l-2 border-pp-200 ml-3 my-1 space-y-1">
                <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-pp-50 hover:text-pp-700 text-xs font-semibold text-slate-600 transition"
                    href="{{ route('dashboard', ['tab' => 'saved']) }}">
                    <span class="text-base shrink-0">❤️</span>
                    <span class="label">Favorites</span>
                </a>
                <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-pp-50 hover:text-pp-700 text-xs font-semibold text-slate-600 transition"
                    href="requests.html">
                    <span class="text-base shrink-0">📋</span>
                    <span class="label">My Requests</span>
                </a>
            </div>
        </div>

        <!-- 3. SELLING ACCORDION (COLLAPSED BY DEFAULT) -->
        <div class="pt-1">
            <button onclick="openSection('seller')"
                class="section-title w-full flex items-center justify-between p-2.5 rounded-2xl text-xs font-extrabold transition duration-200 bg-amber-50 text-amber-900 border border-amber-200">
                <div class="flex items-center gap-2.5">
                    <span class="text-base shrink-0">🏪</span>
                    <span class="label font-extrabold">Selling</span>
                </div>
                <div class="chevron-wrapper flex items-center gap-1.5">
                    <span class="label px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">PRO</span>
                    <svg id="sc" class="w-4 h-4 transition-transform duration-200 chevron" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>

            <div id="seller" class="submenu pl-2 border-l-2 border-amber-300 ml-3 my-1 space-y-1">
                <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 hover:text-amber-900 text-xs font-semibold text-slate-600 transition"
                    href="seller-items.html">
                    <span class="text-base shrink-0">📦</span>
                    <span class="label">My Items</span>
                </a>
                <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 hover:text-amber-900 text-xs font-semibold text-slate-600 transition"
                    href="seller-listings.html">
                    <span class="text-base shrink-0">📋</span>
                    <span class="label">My Listings</span>
                    <span class="label ml-auto text-[10px] font-bold bg-slate-100 text-slate-700 rounded-full px-2 py-0.5">8</span>
                </a>
                <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 hover:text-amber-900 text-xs font-semibold text-slate-600 transition"
                    href="seller-responses.html">
                    <span class="text-base shrink-0">💬</span>
                    <span class="label">My Responses</span>
                </a>
            </div>
        </div>

        <!-- 4. ACCOUNT SECTION -->
        <div class="pt-1 border-t border-slate-100">
            <div class="section-label px-3 pt-2 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">
                Account
            </div>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="locations.html">
                <span class="text-base shrink-0">📍</span>
                <span class="label">Locations</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="#">
                <span class="text-base shrink-0">🔔</span>
                <span class="label">Notifications</span>
                <span class="label ml-auto text-[10px] font-extrabold bg-rose-100 text-rose-700 rounded-full px-2 py-0.5">5</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="{{ route('dashboard', ['tab' => 'profile']) }}">
                <span class="text-base shrink-0">👤</span>
                <span class="label">Profile</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                href="help.html">
                <span class="text-base shrink-0">❓</span>
                <span class="label">Help</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-rose-50 text-rose-600 font-bold text-xs transition"
                href="/">
                <span class="text-base shrink-0">🚪</span>
                <span class="label">Logout</span>
            </a>
        </div>
    </nav>
</aside>