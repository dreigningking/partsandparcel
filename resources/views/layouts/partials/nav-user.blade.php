<nav class="flex-1 overflow-y-auto custom-scrollbar p-3.5 space-y-3">
    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="mb-1 pb-2 border-b border-slate-100">
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center justify-between p-2.5 rounded-xl bg-pp-50 hover:bg-pp-100 border border-pp-200/80 text-xs font-extrabold text-pp-700 transition shadow-2xs">
                <span class="flex items-center gap-2">
                    <span class="text-base">🛡</span>
                    <span class="label">Admin Console</span>
                </span>
                <span class="label text-[10px] bg-pp-600 text-white rounded-md px-1.5 py-0.5 font-bold">Switch ›</span>
            </a>
        </div>
    @endif
    
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
            href="{{ route('subscriptions') }}">
            <span class="text-base shrink-0">⚡</span>
            <span class="label">Subscription</span>
            <span class="label ml-auto text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">Free</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('messages') }}">
            <span class="text-base shrink-0">💬</span>
            <span class="label">Messages</span>
            <span class="label ml-auto w-2 h-2 rounded-full bg-pp-600"></span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('offers') }}">
            <span class="text-base shrink-0">🏷️</span>
            <span class="label">Offers</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('invoices') }}">
            <span class="text-base shrink-0">📄</span>
            <span class="label">Invoices</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('shipments') }}">
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
                href="{{ route('wishlists') }}">
                <span class="text-base shrink-0">❤️</span>
                <span class="label">Favorites</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-pp-50 hover:text-pp-700 text-xs font-semibold text-slate-600 transition"
                href="{{ route('myrequests') }}">
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
                href="{{ route('myitems') }}">
                <span class="text-base shrink-0">📦</span>
                <span class="label">My Items</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 hover:text-amber-900 text-xs font-semibold text-slate-600 transition"
                href="{{ route('mylistings') }}">
                <span class="text-base shrink-0">📋</span>
                <span class="label">My Listings</span>
                <span class="label ml-auto text-[10px] font-bold bg-slate-100 text-slate-700 rounded-full px-2 py-0.5">8</span>
            </a>
            <a class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 hover:text-amber-900 text-xs font-semibold text-slate-600 transition"
                href="{{ route('myresponses') }}">
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
            href="{{ route('locations') }}">
            <span class="text-base shrink-0">📍</span>
            <span class="label">Locations</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('notifications') }}">
            <span class="text-base shrink-0">🔔</span>
            <span class="label">Notifications</span>
            <span class="label ml-auto text-[10px] font-extrabold bg-rose-100 text-rose-700 rounded-full px-2 py-0.5">5</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('profile') }}">
            <span class="text-base shrink-0">👤</span>
            <span class="label">Profile</span>
        </a>
        <a class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
            href="{{ route('help') }}">
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