<nav class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar">
    <!-- CONSOLE SWITCH BUTTON -->
    <div class="mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
        <a href="{{ route('dashboard') }}" class="nav flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 text-xs font-extrabold text-slate-700 dark:text-slate-300 transition border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
            <span class="flex items-center gap-2">
                <span class="text-base">👤</span>
                <span class="label">User Dashboard</span>
            </span>
            <span class="label text-[10px] text-pp-600 dark:text-pp-400 font-bold">Switch ›</span>
        </a>
    </div>

    <a
        class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.*') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
        href="{{ route('admin.dashboard') }}"
    >
        ⌂ <span class="label">Dashboard</span>
    </a>
    
    <a class="nav flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 text-slate-700 text-sm transition" href="{{ route('earnings') }}">
        ▣ <span class="label">Analytics &amp; Reports</span>
    </a>

    <!-- MARKETPLACE -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('market')"
        >
            <span>MARKETPLACE</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="market" class="sub open pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('mylistings') }}"
            >
                ▤ <span class="label">Listings</span>
                <span class="label ml-auto text-[10px] bg-pp-100 text-pp-700 rounded-full px-2 font-bold">
                    {{ \App\Models\Listing::count() }}
                </span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.categories*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.categories') }}"
            >
                ◈ <span class="label">Brands &amp; Categories</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.categories*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.categories') }}"
            >
                ◆ <span class="label">Device Models</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('myitems') }}"
            >
                ▥ <span class="label">Items &amp; Components</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.moderations*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.moderations') }}"
            >
                ⚑ <span class="label">Content Moderation</span>
                @php $pendingModerationsCount = \App\Models\Moderation::where('status', 'pending')->count(); @endphp
                @if($pendingModerationsCount > 0)
                    <span class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2 font-bold">{{ $pendingModerationsCount }}</span>
                @endif
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.promotions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.promotions') }}"
            >
                🚀 <span class="label">Promotions</span>
                @php $activePromos = \App\Models\Promotion::where('status', 'active')->count(); @endphp
                @if($activePromos > 0)
                    <span class="label ml-auto text-[10px] bg-indigo-100 text-indigo-700 rounded-full px-2 font-bold">{{ $activePromos }}</span>
                @endif
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.coupons*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.coupons') }}"
            >
                🎟 <span class="label">Coupons &amp; Discounts</span>
                @php $activeCouponsCount = \App\Models\Coupon::where('is_active', true)->count(); @endphp
                @if($activeCouponsCount > 0)
                    <span class="label ml-auto text-[10px] bg-emerald-100 text-emerald-700 rounded-full px-2 font-bold">{{ $activeCouponsCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- USERS & ACCOUNTS -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('users')"
        >
            <span>USERS &amp; ACCOUNTS</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="users" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('profile') }}"
            >
                👥 <span class="label">All Users</span>
                <span class="label ml-auto text-[10px] bg-slate-100 text-slate-700 rounded-full px-2 font-bold">{{ \App\Models\User::count() }}</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('profile') }}"
            >
                ✓ <span class="label">Verification / KYC</span>
                @php $unverifiedCount = \App\Models\User::where('is_verified', false)->count(); @endphp
                @if($unverifiedCount > 0)
                    <span class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2 font-bold">{{ $unverifiedCount }}</span>
                @endif
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.staff*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.staff') }}"
            >
                ♙ <span class="label">Staff Accounts</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.roles*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.roles') }}"
            >
                🛡 <span class="label">Roles &amp; Permissions</span>
            </a>
        </div>
    </div>

    <!-- TRANSACTIONS -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('tx')"
        >
            <span>TRANSACTIONS</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="tx" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('invoices') }}"
            >
                🧾 <span class="label">Invoices</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('offers') }}"
            >
                ◇ <span class="label">Offers</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('earnings') }}"
            >
                ₦ <span class="label">Payments &amp; Escrow</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('earnings') }}"
            >
                ◷ <span class="label">Settlements &amp; Payouts</span>
            </a>
        </div>
    </div>

    <!-- TRUST & RESOLUTION -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('trust')"
        >
            <span>TRUST &amp; RESOLUTION</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="trust" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('disputes') }}"
            >
                ⚖ <span class="label">Disputes</span>
                @php $openDisputes = \App\Models\Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->count(); @endphp
                @if($openDisputes > 0)
                    <span class="label ml-auto text-[10px] bg-rose-100 text-rose-600 rounded-full px-2 font-bold">{{ $openDisputes }}</span>
                @endif
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('disputes') }}"
            >
                ↩ <span class="label">Returns &amp; Replacements</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('disputes') }}"
            >
                🛡 <span class="label">Fraud &amp; Risk Protection</span>
            </a>
        </div>
    </div>

    <!-- COMMUNITY -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('community')"
        >
            <span>COMMUNITY</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="community" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('community') }}"
            >
                💬 <span class="label">Discussions</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('myrequests') }}"
            >
                ✉ <span class="label">Community RFQs</span>
            </a>
        </div>
    </div>

    <!-- CONTENT & BLOG -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('blog_content')"
        >
            <span>CONTENT &amp; BLOG</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="blog_content" class="sub {{ request()->routeIs('admin.blog*') || request()->routeIs('admin.moderations*') ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog') || request()->routeIs('admin.blog.create') || request()->routeIs('admin.blog.show') || request()->routeIs('admin.blog.edit') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.blog') }}"
            >
                📰 <span class="label">Blog Articles</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog.comments') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.blog.comments') }}"
            >
                💬 <span class="label">Post Comments</span>
                @php $pendingCommCount = \App\Models\PostComment::pending()->count(); @endphp
                @if($pendingCommCount > 0)
                    <span class="label ml-auto text-[10px] bg-amber-100 text-amber-800 rounded-full px-2 font-bold">{{ $pendingCommCount }}</span>
                @endif
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.moderations*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.moderations') }}"
            >
                🛡 <span class="label">Content Moderation</span>
            </a>
        </div>
    </div>

    <!-- LOGISTICS -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('logistics')"
        >
            <span>LOGISTICS</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="logistics" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('shipments') }}"
            >
                ⇢ <span class="label">Shipments &amp; Tracking</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('locations') }}"
            >
                📍 <span class="label">Drop-off Locations</span>
            </a>
        </div>
    </div>

    <!-- FINANCE & REVENUE -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('finance')"
        >
            <span>FINANCE &amp; REVENUE</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="finance" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.subscriptions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.subscriptions') }}"
            >
                ◈ <span class="label">Subscriptions</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.subscription-plans*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.subscription-plans') }}"
            >
                💳 <span class="label">Subscription Plans</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('earnings') }}"
            >
                ₦ <span class="label">Platform Revenue</span>
            </a>
        </div>
    </div>

    <!-- SYSTEM -->
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('system')"
        >
            <span>SYSTEM</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="system" class="sub pl-1 space-y-1 mt-1">
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.general*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.general') }}"
            >
                ⚙ <span class="label">Platform Settings</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.promotion-plans*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.promotion-plans') }}"
            >
                ⚡ <span class="label">Promotion Plans</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.countries*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.countries') }}"
            >
                🌍 <span class="label">Countries &amp; States</span>
            </a>
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 text-sm text-slate-600 transition"
                href="{{ route('notifications') }}"
            >
                🔔 <span class="label">Notifications</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button
                    type="submit"
                    class="nav w-full text-left flex items-center gap-3 p-2.5 rounded-xl hover:bg-rose-50 text-rose-600 font-bold text-xs transition cursor-pointer"
                >
                    ↪ <span class="label">Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>