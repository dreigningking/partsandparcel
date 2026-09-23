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
    @php
        $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
        $recentNotifications = auth()->user()?->notifications()->latest()->take(5)->get() ?? collect();
    @endphp
    <div class="relative">
        <button onclick="toggleNotificationsDropdown()" aria-label="Notifications" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer" title="Notifications">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            @if($unreadCount > 0)
                <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold grid place-items-center">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

        <!-- NOTIFICATIONS DROPDOWN PANEL -->
        <div id="notifications" class="dropdown absolute right-0 top-12 w-[350px] max-w-[calc(100vw-24px)] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[80]">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <div class="font-extrabold text-slate-900 text-sm">Notifications</div>
                    <div class="text-[11px] text-slate-500">{{ $unreadCount }} unread alerts</div>
                </div>
                <a href="{{ route('notifications') }}" class="text-xs font-bold text-pp-600 hover:underline">See all</a>
            </div>
            <div class="max-h-[360px] overflow-y-auto custom-scrollbar divide-y divide-slate-100 bg-white">
                @forelse($recentNotifications as $rNotif)
                    @php
                        $rData = $rNotif->data;
                        $rIcon = $rData['icon'] ?? 'fas fa-bell';
                        $rTitle = $rData['title'] ?? 'Notification';
                        $rAction = $rData['action_url'] ?? route('notifications');
                        $rUnread = $rNotif->unread();
                    @endphp
                    <a href="{{ $rAction }}" class="flex gap-3 p-4 {{ $rUnread ? 'bg-pp-50/60' : 'hover:bg-slate-50' }} transition">
                        <span class="w-9 h-9 rounded-lg bg-pp-100 text-pp-700 grid place-items-center shrink-0 font-bold text-xs">
                            <i class="{{ $rIcon }}"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">{{ $rTitle }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $rData['message'] ?? '' }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $rNotif->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                        No notifications yet.
                    </div>
                @endforelse
            </div>
            <a href="{{ route('notifications') }}" class="block text-center p-3 text-xs font-bold text-pp-600 hover:bg-slate-50 border-t border-slate-100 bg-white">View all notifications →</a>
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
        @php
            $currentUser = auth()->user();
            $userInitials = $currentUser ? collect(explode(' ', $currentUser->name))->map(fn($seg) => strtoupper(substr($seg, 0, 1)))->take(2)->join('') : 'U';
            $roleName = $currentUser?->isAdmin() ? ($currentUser->role?->name ?? 'Administrator') : 'Member';
        @endphp
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
</div>