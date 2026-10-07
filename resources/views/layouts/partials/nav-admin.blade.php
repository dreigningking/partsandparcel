<nav class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar">
    <!-- CONSOLE SWITCH BUTTON -->
    <div class="mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
        <a href="{{ route('dashboard') }}" class="nav flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 text-xs font-extrabold text-slate-700 dark:text-slate-300 transition border border-slate-200/80 dark:border-slate-700/80 shadow-2xs" title="Switch to User Dashboard">
            <span class="flex items-center gap-2">
                <span class="text-base shrink-0">👤</span>
                <span class="label">User Dashboard</span>
            </span>
            <span class="label text-[10px] text-pp-600 dark:text-pp-400 font-bold">Switch ›</span>
        </a>
    </div>

    <!-- 1. DASHBOARD (AdminDashboard) -->
    <a
        class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
        href="{{ route('admin.dashboard') }}"
        title="Dashboard"
    >
        <span class="shrink-0 text-base">⌂</span>
        <span class="label">Dashboard</span>
    </a>

    <!-- 2. MODERATION (AdminModerations) -->
    @if(auth()->user()?->hasPermission('manage_moderation'))
        <a
            class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.moderations*') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
            href="{{ route('admin.moderations') }}"
            title="Moderation"
        >
            <span class="shrink-0 text-base">⚑</span>
            <span class="label">Moderation</span>
            @php $pendingModerationsCount = \App\Models\Moderation::where('status', 'pending')->count(); @endphp
            @if($pendingModerationsCount > 0)
                <span class="label ml-auto text-[10px] bg-amber-100 text-amber-700 rounded-full px-2 font-bold">{{ $pendingModerationsCount }}</span>
            @endif
        </a>
    @endif

    <!-- 3. ANALYTICS & REPORTS (AdminAnalytics) -->
    @if(auth()->user()?->hasPermission('view_analytics'))
        <a
            class="nav flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin.analytics*') ? 'bg-pp-50 text-pp-700 font-semibold' : 'hover:bg-slate-50 text-slate-700 font-medium' }} text-sm transition"
            href="{{ route('admin.analytics') }}"
            title="Analytics &amp; Reports"
        >
            <span class="shrink-0 text-base">▣</span>
            <span class="label">Analytics &amp; Reports</span>
        </a>
    @endif

    <!-- 4. MARKETPLACE -->
    @if(auth()->user()?->hasAnyPermission(['manage_users', 'manage_subscriptions', 'manage_listings', 'moderate_discussions', 'manage_promotions', 'manage_coupons', 'view_invoices']))
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
        <div class="nav-segment pt-1">
            <button
                type="button"
                id="btn-market"
                class="section group w-full text-left px-3 py-2 text-[10px] uppercase tracking-wider font-extrabold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors cursor-pointer flex items-center justify-between rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 select-none"
                onclick="openGroup('market')"
                aria-expanded="{{ $marketActive ? 'true' : 'false' }}"
            >
                <span class="section-label">MARKETPLACE</span>
                <span class="chevron-wrapper inline-flex items-center justify-center w-4 h-4">
                    <svg class="chevron-icon w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200 {{ $marketActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div id="market" class="sub {{ $marketActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
                <!-- Users (AdminUsers) -->
                @if(auth()->user()?->hasPermission('manage_users'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.users') }}"
                        title="Users"
                    >
                        <span class="shrink-0 text-base">👥</span>
                        <span class="label">Users</span>
                        <span class="label ml-auto text-[10px] bg-slate-100 text-slate-700 rounded-full px-2 font-bold">{{ \App\Models\User::count() }}</span>
                    </a>
                @endif

                <!-- Subscription (AdminSubscriptions) -->
                @if(auth()->user()?->hasPermission('manage_subscriptions'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.subscriptions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.subscriptions') }}"
                        title="Subscriptions"
                    >
                        <span class="shrink-0 text-base">◈</span>
                        <span class="label">Subscription</span>
                    </a>
                @endif

                <!-- Listings (AdminListings) -->
                @if(auth()->user()?->hasPermission('manage_listings'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.properties*') || request()->routeIs('admin.listings*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.properties') }}"
                        title="Listings"
                    >
                        <span class="shrink-0 text-base">▤</span>
                        <span class="label">Listings</span>
                        <span class="label ml-auto text-[10px] bg-pp-100 text-pp-700 rounded-full px-2 font-bold">
                            {{ \App\Models\Listing::count() }}
                        </span>
                    </a>
                @endif

                <!-- Discussions (AdminDiscussions) -->
                @if(auth()->user()?->hasPermission('moderate_discussions'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.discussions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.discussions') }}"
                        title="Discussions"
                    >
                        <span class="shrink-0 text-base">💬</span>
                        <span class="label">Discussions</span>
                        @php $discussionsCount = class_exists(\App\Models\Discussion::class) ? \App\Models\Discussion::count() : 0; @endphp
                        @if($discussionsCount > 0)
                            <span class="label ml-auto text-[10px] bg-slate-100 text-slate-700 rounded-full px-2 font-bold">{{ $discussionsCount }}</span>
                        @endif
                    </a>
                @endif

                <!-- Promotion (AdminPromotions) -->
                @if(auth()->user()?->hasPermission('manage_promotions'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.promotions*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.promotions') }}"
                        title="Promotions"
                    >
                        <span class="shrink-0 text-base">🚀</span>
                        <span class="label">Promotion</span>
                        @php $activePromos = \App\Models\Promotion::where('status', 'active')->count(); @endphp
                        @if($activePromos > 0)
                            <span class="label ml-auto text-[10px] bg-indigo-100 text-indigo-700 rounded-full px-2 font-bold">{{ $activePromos }}</span>
                        @endif
                    </a>
                @endif

                <!-- Coupons & Discounts (AdminCoupons) -->
                @if(auth()->user()?->hasPermission('manage_coupons'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.coupons*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.coupons') }}"
                        title="Coupons &amp; Discounts"
                    >
                        <span class="shrink-0 text-base">🎟</span>
                        <span class="label">Coupons &amp; Discounts</span>
                        @php $activeCouponsCount = \App\Models\Coupon::where('is_active', true)->count(); @endphp
                        @if($activeCouponsCount > 0)
                            <span class="label ml-auto text-[10px] bg-emerald-100 text-emerald-700 rounded-full px-2 font-bold">{{ $activeCouponsCount }}</span>
                        @endif
                    </a>
                @endif

                <!-- Invoices (AdminInvoices) -->
                @if(auth()->user()?->hasPermission('view_invoices'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.invoices*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.invoices') }}"
                        title="Invoices"
                    >
                        <span class="shrink-0 text-base">🧾</span>
                        <span class="label">Invoices</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- 5. TRUST & SUPPORT -->
    @if(auth()->user()?->hasAnyPermission(['resolve_disputes', 'manage_support']))
        @php
            $trustActive = request()->routeIs('admin.disputes*') || request()->routeIs('admin.support*');
        @endphp
        <div class="nav-segment pt-1">
            <button
                type="button"
                id="btn-trust"
                class="section group w-full text-left px-3 py-2 text-[10px] uppercase tracking-wider font-extrabold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors cursor-pointer flex items-center justify-between rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 select-none"
                onclick="openGroup('trust')"
                aria-expanded="{{ $trustActive ? 'true' : 'false' }}"
            >
                <span class="section-label">TRUST &amp; RESOLUTION</span>
                <span class="chevron-wrapper inline-flex items-center justify-center w-4 h-4">
                    <svg class="chevron-icon w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200 {{ $trustActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div id="trust" class="sub {{ $trustActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
                <!-- Customer Support Desk -->
                @if(auth()->user()?->hasPermission('manage_support'))
                    @php
                        $supportUser = \App\Models\User::getSupportUser();
                        $supportUnread = \App\Models\ConversationMessage::whereHas('conversation', function ($q) {
                            $q->where('contextable_type', \App\Models\User::class);
                        })->whereNull('read_at')->where('sender_id', '!=', $supportUser->id)->count();
                    @endphp
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.support*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.support') }}"
                        title="Customer Support Desk"
                    >
                        <span class="shrink-0 text-base">🎧</span>
                        <span class="label">Support Desk</span>
                        @if($supportUnread > 0)
                            <span class="label ml-auto text-[10px] bg-rose-100 text-rose-600 rounded-full px-2 font-bold">{{ $supportUnread }}</span>
                        @endif
                    </a>
                @endif

                <!-- Disputes (AdminDisputes) -->
                @if(auth()->user()?->hasPermission('resolve_disputes'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.disputes*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.disputes') }}"
                        title="Disputes"
                    >
                        <span class="shrink-0 text-base">⚖</span>
                        <span class="label">Disputes</span>
                        @php $openDisputes = \App\Models\Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->count(); @endphp
                        @if($openDisputes > 0)
                            <span class="label ml-auto text-[10px] bg-rose-100 text-rose-600 rounded-full px-2 font-bold">{{ $openDisputes }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- 6. CONTENT & BLOG -->
    @if(auth()->user()?->hasAnyPermission(['manage_blog', 'manage_blog_comments']))
        @php
            $contentActive = request()->routeIs('admin.blog*');
        @endphp
        <div class="nav-segment pt-1">
            <button
                type="button"
                id="btn-content_blog"
                class="section group w-full text-left px-3 py-2 text-[10px] uppercase tracking-wider font-extrabold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors cursor-pointer flex items-center justify-between rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 select-none"
                onclick="openGroup('content_blog')"
                aria-expanded="{{ $contentActive ? 'true' : 'false' }}"
            >
                <span class="section-label">CONTENT &amp; BLOG</span>
                <span class="chevron-wrapper inline-flex items-center justify-center w-4 h-4">
                    <svg class="chevron-icon w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200 {{ $contentActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div id="content_blog" class="sub {{ $contentActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
                <!-- Posts (AdminBlog) -->
                @if(auth()->user()?->hasPermission('manage_blog'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog') || request()->routeIs('admin.blog.create') || request()->routeIs('admin.blog.show') || request()->routeIs('admin.blog.edit') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.blog') }}"
                        title="Posts"
                    >
                        <span class="shrink-0 text-base">📰</span>
                        <span class="label">Posts</span>
                    </a>
                @endif

                <!-- Post Comments (AdminBlogComments) -->
                @if(auth()->user()?->hasPermission('manage_blog_comments'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.blog.comments*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.blog.comments') }}"
                        title="Post Comments"
                    >
                        <span class="shrink-0 text-base">💬</span>
                        <span class="label">Post Comments</span>
                        @php $pendingCommCount = \App\Models\PostComment::pending()->count(); @endphp
                        @if($pendingCommCount > 0)
                            <span class="label ml-auto text-[10px] bg-amber-100 text-amber-800 rounded-full px-2 font-bold">{{ $pendingCommCount }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- 7. FINANCE & REVENUE -->
    @if(auth()->user()?->hasAnyPermission(['manage_payments', 'view_revenue', 'manage_payouts']))
        @php
            $financeActive = request()->routeIs('admin.payments*') ||
                             request()->routeIs('admin.revenue*') ||
                             request()->routeIs('admin.payouts*');
        @endphp
        <div class="nav-segment pt-1">
            <button
                type="button"
                id="btn-finance"
                class="section group w-full text-left px-3 py-2 text-[10px] uppercase tracking-wider font-extrabold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors cursor-pointer flex items-center justify-between rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 select-none"
                onclick="openGroup('finance')"
                aria-expanded="{{ $financeActive ? 'true' : 'false' }}"
            >
                <span class="section-label">FINANCE &amp; REVENUE</span>
                <span class="chevron-wrapper inline-flex items-center justify-center w-4 h-4">
                    <svg class="chevron-icon w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200 {{ $financeActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div id="finance" class="sub {{ $financeActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
                <!-- Payments (AdminPayments) -->
                @if(auth()->user()?->hasPermission('manage_payments'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.payments*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.payments') }}"
                        title="Payments"
                    >
                        <span class="shrink-0 text-base">₦</span>
                        <span class="label">Payments</span>
                    </a>
                @endif

                <!-- Revenue (AdminRevenue) -->
                @if(auth()->user()?->hasPermission('view_revenue'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.revenue*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.revenue') }}"
                        title="Revenue"
                    >
                        <span class="shrink-0 text-base">📈</span>
                        <span class="label">Revenue</span>
                    </a>
                @endif

                <!-- Payouts (AdminPayouts) -->
                @if(auth()->user()?->hasPermission('manage_payouts'))
                    <a
                        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.payouts*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                        href="{{ route('admin.payouts') }}"
                        title="Payouts"
                    >
                        <span class="shrink-0 text-base">◷</span>
                        <span class="label">Payouts</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if(auth()->user()?->isSuperAdmin())
        {{-- 8. SYSTEM (SUPER ADMIN ONLY) --}}
        @php
            $systemActive = request()->routeIs('admin.settings*');
        @endphp
        <div class="nav-segment pt-1">
            <button
                type="button"
                id="btn-system"
                class="section group w-full text-left px-3 py-2 text-[10px] uppercase tracking-wider font-extrabold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors cursor-pointer flex items-center justify-between rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 select-none"
                onclick="openGroup('system')"
                aria-expanded="{{ $systemActive ? 'true' : 'false' }}"
            >
                <span class="section-label">SYSTEM</span>
                <span class="chevron-wrapper inline-flex items-center justify-center w-4 h-4">
                    <svg class="chevron-icon w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200 {{ $systemActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div id="system" class="sub {{ $systemActive ? 'open' : '' }} pl-1 space-y-1 mt-1">
                <!-- General Settings (AdminGeneral) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.general*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.general') }}"
                    title="General Settings"
                >
                    <span class="shrink-0 text-base">⚙</span>
                    <span class="label">General Settings</span>
                </a>

                <!-- Geography Settings (AdminCountries) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.countries*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.countries') }}"
                    title="Geography Settings"
                >
                    <span class="shrink-0 text-base">🌍</span>
                    <span class="label">Geography Settings</span>
                </a>

                <!-- Roles & Permissions (AdminRolesPermissions) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.roles*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.roles') }}"
                    title="Roles &amp; Permissions"
                >
                    <span class="shrink-0 text-base">🛡</span>
                    <span class="label">Roles &amp; Permissions</span>
                </a>

                <!-- Categorization Settings (AdminCategories) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.categories*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.categories') }}"
                    title="Categorization Settings"
                >
                    <span class="shrink-0 text-base">◈</span>
                    <span class="label">Categorization Settings</span>
                </a>

                <!-- Subscription Plans (AdminSubscriptionPlans) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.subscription-plans*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.subscription-plans') }}"
                    title="Subscription Plans"
                >
                    <span class="shrink-0 text-base">💳</span>
                    <span class="label">Subscription Plans</span>
                </a>

                <!-- Staff Accounts (AdminStaff) -->
                <a
                    class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.settings.staff*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
                    href="{{ route('admin.settings.staff') }}"
                    title="Staff Accounts"
                >
                    <span class="shrink-0 text-base">♙</span>
                    <span class="label">Staff Accounts</span>
                </a>
            </div>
        </div>
    @endif

    <!-- 9. NOTIFICATIONS (AdminNotifications) -->
    <a
        class="nav flex items-center gap-3 p-2.5 rounded-xl {{ request()->routeIs('admin.notifications*') ? 'bg-pp-50 text-pp-700 font-bold' : 'hover:bg-slate-50 text-slate-600' }} text-sm transition"
        href="{{ route('admin.notifications') }}"
        title="Notifications"
    >
        <span class="shrink-0 text-base">🔔</span>
        <span class="label">Notifications</span>
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
            title="Logout"
        >
            <span class="shrink-0 text-base">↪</span>
            <span class="label">Logout</span>
        </button>
    </form>
</nav>