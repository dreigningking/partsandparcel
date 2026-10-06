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

    <!-- 1. DASHBOARD (AdminDashboard) -->
    <a
        class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
        href="{{ route('admin.dashboard') }}"
    >
        ⌂ <span class="label">Dashboard</span>
    </a>

    <!-- 2. MODERATION (AdminModerations) -->
    <a
        class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.moderations*') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
        href="{{ route('admin.moderations') }}"
    >
        ⚑ <span class="label">Moderation</span>
        @php $pendingModerationsCount = \App\Models\Moderation::where('status', 'pending')->count(); @endphp
        @if($pendingModerationsCount > 0)
            <span class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2 font-bold">{{ $pendingModerationsCount }}</span>
        @endif
    </a>

    <!-- 3. ANALYTICS & REPORTS (AdminAnalytics) -->
    <a
        class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.analytics*') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
        href="{{ route('admin.analytics') }}"
    >
        ▣ <span class="label">Analytics &amp; Reports</span>
    </a>

    <!-- 4. MARKETPLACE -->
    @php
        $marketActive = request()->routeIs('admin.users*') ||
                        request()->routeIs('admin.subscriptions*') ||
                        request()->routeIs('admin.properties*') ||
                        request()->routeIs('admin.listings*') ||
                        request()->routeIs('admin.discussions*') ||
                        request()->routeIs('admin.promotions*') ||
                        request()->routeIs('admin.coupons*') ||
                        request()->routeIs('admin.invoices*');
    @endphp
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('market')"
        >
            <span>MARKETPLACE</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="market" class="sub {{ $marketActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <!-- Users (AdminUsers) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.users') }}"
            >
                👥 <span class="label">Users</span>
                <span class="label ml-auto text-[10px] bg-slate-100 text-slate-700 rounded-full px-2 font-bold">{{ \App\Models\User::count() }}</span>
            </a>

            <!-- Subscription (AdminSubscriptions) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.subscriptions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.subscriptions') }}"
            >
                ◈ <span class="label">Subscription</span>
            </a>

            <!-- Listings (AdminListings) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.properties*') || request()->routeIs('admin.listings*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.properties') }}"
            >
                ▤ <span class="label">Listings</span>
                <span class="label ml-auto text-[10px] bg-pp-100 text-pp-700 rounded-full px-2 font-bold">
                    {{ \App\Models\Listing::count() }}
                </span>
            </a>


            <!-- Discussions (AdminDiscussions) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.discussions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.discussions') }}"
            >
                💬 <span class="label">Discussions</span>
                @php $discussionsCount = class_exists(\App\Models\Discussion::class) ? \App\Models\Discussion::count() : 0; @endphp
                @if($discussionsCount > 0)
                    <span class="label ml-auto text-[10px] bg-slate-100 text-slate-700 rounded-full px-2 font-bold">{{ $discussionsCount }}</span>
                @endif
            </a>

            <!-- Promotion (AdminPromotions) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.promotions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.promotions') }}"
            >
                🚀 <span class="label">Promotion</span>
                @php $activePromos = \App\Models\Promotion::where('status', 'active')->count(); @endphp
                @if($activePromos > 0)
                    <span class="label ml-auto text-[10px] bg-indigo-100 text-indigo-700 rounded-full px-2 font-bold">{{ $activePromos }}</span>
                @endif
            </a>

            <!-- Coupons & Discounts (AdminCoupons) -->
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

            <!-- Invoices (AdminInvoices) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.invoices*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.invoices') }}"
            >
                🧾 <span class="label">Invoices</span>
            </a>
        </div>
    </div>

    <!-- 5. TRUST & RESOLUTION -->
    @php
        $trustActive = request()->routeIs('admin.disputes*')
                       
    @endphp
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('trust')"
        >
            <span>TRUST &amp; RESOLUTION</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="trust" class="sub {{ $trustActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <!-- Disputes (AdminDisputes) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.disputes*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.disputes') }}"
            >
                ⚖ <span class="label">Disputes</span>
                @php $openDisputes = \App\Models\Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->count(); @endphp
                @if($openDisputes > 0)
                    <span class="label ml-auto text-[10px] bg-rose-100 text-rose-600 rounded-full px-2 font-bold">{{ $openDisputes }}</span>
                @endif
            </a>

        </div>
    </div>

    <!-- 6. CONTENT & BLOG -->
    @php
        $contentActive = request()->routeIs('admin.blog*');
    @endphp
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('content_blog')"
        >
            <span>CONTENT &amp; BLOG</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="content_blog" class="sub {{ $contentActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <!-- Posts (AdminBlog) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog') || request()->routeIs('admin.blog.create') || request()->routeIs('admin.blog.show') || request()->routeIs('admin.blog.edit') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.blog') }}"
            >
                📰 <span class="label">Posts</span>
            </a>

            <!-- Post Comments (AdminBlogComments) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog.comments*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.blog.comments') }}"
            >
                💬 <span class="label">Post Comments</span>
                @php $pendingCommCount = \App\Models\PostComment::pending()->count(); @endphp
                @if($pendingCommCount > 0)
                    <span class="label ml-auto text-[10px] bg-amber-100 text-amber-800 rounded-full px-2 font-bold">{{ $pendingCommCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- 7. FINANCE & REVENUE -->
    @php
        $financeActive = request()->routeIs('admin.payments*') ||
                         request()->routeIs('admin.revenue*') ||
                         request()->routeIs('admin.payouts*');
    @endphp
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('finance')"
        >
            <span>FINANCE &amp; REVENUE</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="finance" class="sub {{ $financeActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <!-- Payments (AdminPayments) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.payments*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.payments') }}"
            >
                ₦ <span class="label">Payments</span>
            </a>

            <!-- Revenue (AdminRevenue) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.revenue*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.revenue') }}"
            >
                📈 <span class="label">Revenue</span>
            </a>

            <!-- Payouts (AdminPayouts) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.payouts*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.payouts') }}"
            >
                ◷ <span class="label">Payouts</span>
            </a>
        </div>
    </div>

    <!-- 8. SYSTEM -->
    @php
        $systemActive = request()->routeIs('admin.settings*');
    @endphp
    <div>
        <button
            class="section w-full text-left px-3 py-2 text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-700 transition cursor-pointer flex items-center justify-between"
            onclick="openGroup('system')"
        >
            <span>SYSTEM</span>
            <span class="text-xs">⌄</span>
        </button>
        <div id="system" class="sub {{ $systemActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
            <!-- General Settings (AdminGeneral) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.general*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.general') }}"
            >
                ⚙ <span class="label">General Settings</span>
            </a>

            <!-- Geography Settings (AdminCountries) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.countries*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.countries') }}"
            >
                🌍 <span class="label">Geography Settings</span>
            </a>

            <!-- Roles & Permissions (AdminRolesPermissions) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.roles*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.roles') }}"
            >
                🛡 <span class="label">Roles &amp; Permissions</span>
            </a>

            <!-- Categorization Settings (AdminCategories) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.categories*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.categories') }}"
            >
                ◈ <span class="label">Categorization Settings</span>
            </a>

            <!-- Subscription Plans (AdminSubscriptionPlans) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.subscription-plans*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.subscription-plans') }}"
            >
                💳 <span class="label">Subscription Plans</span>
            </a>

            <!-- Staff Accounts (AdminStaff) -->
            <a
                class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.staff*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                href="{{ route('admin.settings.staff') }}"
            >
                ♙ <span class="label">Staff Accounts</span>
            </a>
        </div>
    </div>

    <!-- 9. NOTIFICATIONS (AdminNotifications) -->
    <a
        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.notifications*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
        href="{{ route('admin.notifications') }}"
    >
        🔔 <span class="label">Notifications</span>
        @php $unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0; @endphp
        @if($unreadCount > 0)
            <span class="label ml-auto text-[10px] bg-rose-100 text-rose-600 rounded-full px-2 font-bold">{{ $unreadCount }}</span>
        @endif
    </a>

    <!-- 10. LOGOUT -->
    <form action="{{ route('logout') }}" method="POST" class="w-full">
        @csrf
        <button
            type="submit"
            class="nav w-full text-left flex items-center gap-3 p-2.5 rounded-xl hover:bg-rose-50 text-rose-600 font-bold text-xs transition cursor-pointer"
        >
            ↪ <span class="label">Logout</span>
        </button>
    </form>
</nav>