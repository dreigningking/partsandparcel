<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Directory</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">User Accounts</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                User Management & Accounts
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage registered users, business profiles, subscription plans, listing limits, and account status across Parts & Parcel.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            @if($search || $country !== 'all' || $subscriptionPlan !== 'all' || $filter !== 'all')
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-600 dark:text-rose-400 font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
                    title="Reset all active filters"
                >
                    <i class="fas fa-times-circle text-xs"></i>
                    <span>Reset Filters</span>
                </button>
            @endif

            <a
                href="{{ route('admin.subscriptions') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
                title="Manage subscription plans and billing"
            >
                <i class="fas fa-crown text-amber-500"></i>
                <span>Subscriptions</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:border-rose-800/60 dark:bg-rose-950/40 p-4 text-sm font-semibold text-rose-800 dark:text-rose-300 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Total Users -->
        <div 
            wire:click="setFilter('all')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'all' && $country === 'all' && $subscriptionPlan === 'all' ? 'border-pp-500 ring-2 ring-pp-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Users</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-users"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">All accounts</p>
        </div>

        <!-- Paid Subscribers -->
        <div 
            wire:click="setFilter('subscribers')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'subscribers' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subscribers</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-crown"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($activeSubscribersCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Active paid plans</p>
        </div>

        <!-- Verified Users -->
        <div 
            wire:click="setFilter('verified')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'verified' ? 'border-purple-500 ring-2 ring-purple-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Verified</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs">
                    <i class="fas fa-id-card"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-purple-600 dark:text-purple-400">{{ number_format($verifiedCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">KYC & Identity</p>
        </div>

        <!-- Businesses / Vendors -->
        <div 
            wire:click="setFilter('business')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'business' ? 'border-teal-500 ring-2 ring-teal-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Businesses</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs">
                    <i class="fas fa-store"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-teal-600 dark:text-teal-400">{{ number_format($businessCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Vendors & shops</p>
        </div>

        <!-- Sellers with Listings -->
        <div 
            wire:click="setFilter('sellers')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'sellers' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sellers</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-boxes-stacked"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($sellersCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">With listings</p>
        </div>

        <!-- Suspended -->
        <div 
            wire:click="setFilter('suspended')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'suspended' ? 'border-rose-500 ring-2 ring-rose-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Suspended</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                    <i class="fas fa-user-slash"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($suspendedCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Restricted</p>
        </div>
    </div>

    <!-- MAIN CONTROL PANEL & DATA TABLE -->
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">

        <!-- FILTERS & SEARCH TOOLBAR -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            
            <!-- STATUS TABS -->
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="setFilter('all')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'all' ? 'bg-pp-700 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>All Users</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $totalCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('subscribers')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'subscribers' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Paid Subscribers</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'subscribers' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $activeSubscribersCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('verified')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'verified' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Verified</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'verified' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $verifiedCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('business')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'business' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Businesses</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'business' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $businessCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('suspended')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'suspended' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Suspended</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'suspended' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $suspendedCount }}
                    </span>
                </button>
            </div>

            <!-- SEARCH, FILTERS & SORT CONTROLS -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Country Filter Dropdown -->
                <div class="relative">
                    <select
                        wire:model.live="country"
                        class="h-9 pl-3 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                        <option value="all">All Countries</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subscription Plan Filter Dropdown -->
                <div class="relative">
                    <select
                        wire:model.live="subscriptionPlan"
                        class="h-9 pl-3 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                        <option value="all">All Plans</option>
                        <option value="free">Free / No Plan</option>
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input: name, business, email, phone -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search name, business, email, phone..."
                        class="w-full h-9 pl-8 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                    @if($search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                        >
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Sort Field Dropdown -->
                <div class="relative">
                    <select
                        wire:model.live="sortBy"
                        class="h-9 pl-3 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                        <option value="created_at">Sort: Joined Date</option>
                        <option value="name">Sort: User Name</option>
                        <option value="business_name">Sort: Business Name</option>
                        <option value="listings_count">Sort: Total Listings</option>
                        <option value="live_listings_count">Sort: Live Listings</option>
                    </select>
                </div>

                <!-- Sort Order Toggle Button -->
                <button
                    type="button"
                    wire:click="$set('sortDir', '{{ $sortDir === 'desc' ? 'asc' : 'desc' }}')"
                    class="h-9 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
                    title="Toggle sort direction"
                >
                    <i class="fas fa-sort-amount-{{ $sortDir === 'desc' ? 'down' : 'up' }} text-slate-400"></i>
                    <span class="hidden sm:inline">{{ $sortDir === 'desc' ? 'Desc' : 'Asc' }}</span>
                </button>
            </div>
        </div>

        <!-- TABLE OF USERS -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sort('name')">
                            <div class="flex items-center gap-1.5">
                                <span>Details (name, business name, email, phone)</span>
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-pp-600"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3.5">Country</th>
                        <th class="px-4 py-3.5">Current Subscription</th>
                        <th class="px-4 py-3.5 cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sort('listings_count')">
                            <div class="flex items-center gap-1.5">
                                <span>Listings 1/5 (live/total)</span>
                                @if($sortBy === 'listings_count' || $sortBy === 'live_listings_count')
                                    <i class="fas fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-pp-600"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3.5 text-right">View More</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">

                            <!-- COLUMN 1: DETAILS (NAME, BUSINESS NAME, EMAIL, PHONE) -->
                            <td class="px-4 py-3.5 max-w-sm">
                                <div class="flex items-start gap-3">
                                    <!-- User Avatar / Initials Badge -->
                                    <div class="relative shrink-0 mt-0.5">
                                        @if($user->avatar_url)
                                            <img 
                                                src="{{ $user->avatar_url }}" 
                                                alt="{{ $user->name }}" 
                                                class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs"
                                            />
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pp-500 to-pp-700 text-white font-black text-xs flex items-center justify-center border border-pp-600 shadow-2xs">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif

                                        <!-- Verification or Suspension Badge Overlay -->
                                        @if($user->isSuspended())
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[9px] shadow-xs" title="Account Suspended">
                                                <i class="fas fa-ban text-[8px]"></i>
                                            </span>
                                        @elseif($user->is_verified || $user->is_fully_verified)
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-[9px] shadow-xs" title="Identity Verified">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                        @elseif($user->role)
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[9px] shadow-xs" title="Staff / Admin">
                                                <i class="fas fa-shield-alt text-[8px]"></i>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Details Block -->
                                    <div class="min-w-0 flex-1">
                                        <!-- Name & Badges -->
                                        <div class="flex items-center flex-wrap gap-1.5">
                                            <a 
                                                href="{{ route('admin.users.show', ['user' => $user->id]) }}" 
                                                class="font-bold text-slate-900 dark:text-white truncate hover:text-pp-600 dark:hover:text-pp-400 transition"
                                            >
                                                {{ $user->name }}
                                            </a>

                                            @if($user->is_verified || $user->is_fully_verified)
                                                <span class="text-purple-600 dark:text-purple-400" title="Verified Member">
                                                    <i class="fas fa-check-circle text-xs"></i>
                                                </span>
                                            @endif

                                            @if($user->role)
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/50">
                                                    {{ $user->role->name }}
                                                </span>
                                            @endif

                                            @if($user->isSuspended())
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/50">
                                                    Suspended
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Business Name -->
                                        @if($user->business_name)
                                            <div class="flex items-center gap-1.5 mt-0.5 text-xs font-semibold text-pp-700 dark:text-pp-400 truncate" title="{{ $user->business_name }}">
                                                <i class="fas fa-store text-[11px] shrink-0 opacity-80"></i>
                                                <span class="truncate">{{ $user->business_name }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-slate-400">
                                                <i class="fas fa-user text-[10px] shrink-0"></i>
                                                <span>Personal Account</span>
                                            </div>
                                        @endif

                                        <!-- Email & Phone Contact -->
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <a href="mailto:{{ $user->email }}" class="hover:text-pp-600 dark:hover:text-pp-400 transition flex items-center gap-1 truncate max-w-[200px]" title="{{ $user->email }}">
                                                <i class="fas fa-envelope text-[10px] text-slate-400 shrink-0"></i>
                                                <span class="truncate">{{ $user->email }}</span>
                                            </a>

                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}" class="hover:text-pp-600 dark:hover:text-pp-400 transition flex items-center gap-1" title="{{ $user->phone }}">
                                                    <i class="fas fa-phone text-[10px] text-slate-400 shrink-0"></i>
                                                    <span>{{ $user->phone }}</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 italic">No phone</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- COLUMN 2: COUNTRY -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black text-xs flex items-center justify-center border border-slate-200/80 dark:border-slate-700 shadow-2xs shrink-0">
                                        {{ $user->country?->code ?? '—' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 dark:text-white truncate">
                                            {{ $user->country?->name ?? 'Not Assigned' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $user->country?->currency_symbol ?? '' }} {{ $user->country?->currency ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- COLUMN 3: CURRENT SUBSCRIPTION -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($user->activeSubscription)
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-pp-50 text-pp-700 dark:bg-pp-950/60 dark:text-pp-300 border border-pp-200/60 dark:border-pp-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            {{ $user->activeSubscription->plan?->name ?? 'Active Plan' }}
                                        </span>

                                        <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                            <i class="fas fa-clock text-[9px]"></i>
                                            @if($user->activeSubscription->ends_at)
                                                <span>Renews/Expires {{ $user->activeSubscription->ends_at->format('M d, Y') }}</span>
                                            @else
                                                <span>Active continuous</span>
                                            @endif
                                        </div>

                                        @if($user->activeSubscription->plan?->escrow_percentage)
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                Escrow: {{ $user->activeSubscription->plan->escrow_percentage }}%
                                                @if($user->activeSubscription->plan->escrow_cap)
                                                    (Cap: ₦{{ number_format((float) $user->activeSubscription->plan->escrow_cap) }})
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            <span>Free / Starter Tier</span>
                                        </span>
                                        <div class="text-[10px] text-slate-400">
                                            Standard 10% escrow fee
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- COLUMN 4: LISTINGS 1/5 (LIVE/TOTAL) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $user->live_listings_count }}</span>
                                        <span class="text-xs font-bold text-slate-400">/</span>
                                        <span class="text-sm font-black text-slate-800 dark:text-slate-200">{{ $user->listings_count }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 tracking-tight uppercase">(live/total)</span>
                                    </div>

                                    @php
                                        $ratioPct = $user->listings_count > 0 ? min(100, round(($user->live_listings_count / $user->listings_count) * 100)) : 0;
                                        $listingCap = $user->activeSubscription?->listing_limit ?? $user->activeSubscription?->plan?->listing_limit ?? null;
                                    @endphp

                                    <!-- Mini visual ratio progress bar -->
                                    <div class="w-28 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $ratioPct }}%"></div>
                                    </div>

                                    @if($listingCap)
                                        <div class="text-[10px] text-slate-400">
                                            Cap: <span class="font-bold text-slate-600 dark:text-slate-300">{{ $listingCap }} max</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- COLUMN 5: VIEW MORE -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Quick Preview Modal Button -->
                                    <button
                                        type="button"
                                        wire:click="preview({{ $user->id }})"
                                        class="px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs"
                                        title="Quick View Details"
                                    >
                                        <i class="fas fa-eye text-xs text-slate-400"></i>
                                        <span>View more</span>
                                    </button>

                                    <!-- View Full Profile Route -->
                                    <a
                                        href="{{ route('admin.users.show', ['user' => $user->id]) }}"
                                        class="p-2 rounded-xl text-slate-400 hover:text-pp-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                        title="Open Full Profile Page"
                                    >
                                        <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                                    </a>

                                    <!-- Quick Suspend / Unsuspend Action -->
                                    @if(auth()->id() !== $user->id)
                                        <button
                                            type="button"
                                            wire:click="toggleSuspend({{ $user->id }})"
                                            wire:confirm="{{ $user->isSuspended() ? 'Are you sure you want to unsuspend this user?' : 'Are you sure you want to suspend this user? They will not be able to trade or publish listings.' }}"
                                            class="p-2 rounded-xl transition {{ $user->isSuspended() ? 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' : 'text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}"
                                            title="{{ $user->isSuspended() ? 'Unsuspend Account' : 'Suspend Account' }}"
                                        >
                                            <i class="fas fa-{{ $user->isSuspended() ? 'user-check' : 'user-slash' }} text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-lg mb-3">
                                    <i class="fas fa-users-slash text-slate-400"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No users match criteria</h3>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No user accounts found matching your selected search term, country, plan, or status filters.
                                </p>
                                @if($search || $country !== 'all' || $subscriptionPlan !== 'all' || $filter !== 'all')
                                    <button
                                        type="button"
                                        wire:click="resetFilters"
                                        class="mt-3 px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs transition"
                                    >
                                        Clear all filters
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR -->
        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Showing <span class="font-bold text-slate-700 dark:text-slate-200">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-700 dark:text-slate-200">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-700 dark:text-slate-200">{{ $users->total() }}</span> users
                </span>

                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-slate-400">Per page:</span>
                    <select
                        wire:model.live="perPage"
                        class="h-7 px-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-hidden"
                    >
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- QUICK USER PREVIEW MODAL -->
    @if ($showPreviewModal && $previewUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closePreview"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-800">
                    
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                @if($previewUser->avatar_url)
                                    <img src="{{ $previewUser->avatar_url }}" alt="{{ $previewUser->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-700" />
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pp-500 to-pp-700 text-white font-black text-sm flex items-center justify-center border border-pp-600">
                                        {{ strtoupper(substr($previewUser->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-extrabold text-slate-950 dark:text-white" id="modal-title">
                                        {{ $previewUser->name }}
                                    </h3>
                                    @if($previewUser->is_verified || $previewUser->is_fully_verified)
                                        <span class="text-purple-600 dark:text-purple-400" title="Verified Member">
                                            <i class="fas fa-check-circle text-sm"></i>
                                        </span>
                                    @endif
                                </div>
                                @if($previewUser->business_name)
                                    <p class="text-xs font-bold text-pp-600 dark:text-pp-400 flex items-center gap-1 mt-0.5">
                                        <i class="fas fa-store text-[11px]"></i>
                                        <span>{{ $previewUser->business_name }}</span>
                                    </p>
                                @endif
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Member since {{ $previewUser->created_at?->format('M d, Y') ?? 'N/A' }} ({{ $previewUser->created_at?->diffForHumans() }})
                                </p>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            wire:click="closePreview" 
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                        
                        <!-- Account Status & Badges Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                                <span class="mt-1 inline-flex items-center gap-1 text-xs font-bold {{ $previewUser->isSuspended() ? 'text-rose-600' : 'text-emerald-600' }}">
                                    <i class="fas fa-circle text-[8px]"></i>
                                    {{ $previewUser->isSuspended() ? 'Suspended' : 'Active Account' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Verification</span>
                                <span class="mt-1 inline-flex items-center gap-1 text-xs font-bold {{ $previewUser->is_verified ? 'text-purple-600' : 'text-slate-500' }}">
                                    <i class="fas fa-id-card text-[10px]"></i>
                                    {{ $previewUser->is_verified ? 'Verified' : 'Unverified' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Country</span>
                                <span class="mt-1 text-xs font-bold text-slate-800 dark:text-slate-200 block truncate">
                                    {{ $previewUser->country?->name ?? 'Not Set' }} ({{ $previewUser->country?->code ?? '—' }})
                                </span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catalog Quota</span>
                                <span class="mt-1 text-xs font-bold text-slate-800 dark:text-slate-200 block">
                                    <span class="text-emerald-600">{{ $previewUser->live_listings_count }} live</span> / {{ $previewUser->listings_count }} total
                                </span>
                            </div>
                        </div>

                        <!-- Contact & Identity Details -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Contact & Location</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Email Address:</span>
                                    <a href="mailto:{{ $previewUser->email }}" class="font-semibold text-pp-600 hover:underline">{{ $previewUser->email }}</a>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Phone Number:</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $previewUser->phone ?: 'Not provided' }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-slate-400 block text-[11px]">Primary Address:</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                                        @if($previewUser->primaryLocation)
                                            {{ $previewUser->primaryLocation->address_line_1 }}, {{ $previewUser->primaryLocation->city }}, {{ $previewUser->primaryLocation->state?->name }}
                                        @elseif($previewUser->locations->isNotEmpty())
                                            {{ $previewUser->locations->first()->address_line_1 }}, {{ $previewUser->locations->first()->city }}
                                        @else
                                            No verified address on record
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Subscription Details -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Subscription & Plan Standing</h4>
                            @if($previewUser->activeSubscription)
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-extrabold text-pp-700 dark:text-pp-300">
                                            {{ $previewUser->activeSubscription->plan?->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 space-y-0.5">
                                            <p>Escrow Fee: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $previewUser->activeSubscription->plan?->escrow_percentage }}%</span> @if($previewUser->activeSubscription->plan?->escrow_cap) (Cap: ₦{{ number_format((float) $previewUser->activeSubscription->plan->escrow_cap) }})@endif</p>
                                            <p>Listings Limit: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $previewUser->activeSubscription->listing_limit ?? $previewUser->activeSubscription->plan?->listing_limit ?? 'Unlimited' }}</span> items</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200">
                                            ACTIVE
                                        </span>
                                        <p class="text-[10px] text-slate-400 mt-1">
                                            @if($previewUser->activeSubscription->ends_at)
                                                Expires {{ $previewUser->activeSubscription->ends_at->format('M d, Y') }}
                                            @else
                                                Continuous
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    This user is on the <span class="font-bold text-slate-700 dark:text-slate-200">Free / Starter Tier</span> with default escrow fee rate of 10%.
                                </div>
                            @endif
                        </div>

                        <!-- Recent Listings (if any) -->
                        @if($previewUser->listings->isNotEmpty())
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        Recent Listings ({{ $previewUser->listings->count() }})
                                    </h4>
                                    <a href="{{ route('admin.listings', ['q' => $previewUser->name]) }}" class="text-[11px] font-bold text-pp-600 hover:underline">
                                        View All Listings →
                                    </a>
                                </div>
                                <div class="space-y-1.5">
                                    @foreach($previewUser->listings as $lst)
                                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate">
                                                    {{ $lst->title }}
                                                </div>
                                                <div class="text-[10px] text-slate-400">
                                                    ₦{{ number_format((float) ($lst->price ?? 0), 2) }} · Qty: {{ $lst->quantity }}
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $lst->is_published && $lst->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                                {{ $lst->is_published && $lst->is_active ? 'Live' : 'Draft / Paused' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            @if(auth()->id() !== $previewUser->id)
                                <button
                                    type="button"
                                    wire:click="toggleSuspend({{ $previewUser->id }})"
                                    wire:confirm="{{ $previewUser->isSuspended() ? 'Unsuspend this account?' : 'Suspend this account?' }}"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $previewUser->isSuspended() ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}"
                                >
                                    <i class="fas fa-{{ $previewUser->isSuspended() ? 'user-check' : 'user-slash' }} text-[11px]"></i>
                                    <span>{{ $previewUser->isSuspended() ? 'Unsuspend User' : 'Suspend User' }}</span>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="closePreview"
                                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition"
                            >
                                Close
                            </button>
                            <a
                                href="{{ route('admin.users.show', ['user' => $previewUser->id]) }}"
                                class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5"
                            >
                                <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                <span>Full Account Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
