<div>
    <!-- WELCOME CONTROL BANNER -->
    <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-pp-900 text-white p-7 shadow-soft">
        <div class="text-xs uppercase tracking-widest text-pp-200 font-bold">Platform control center</div>
        <h1 class="text-3xl font-extrabold mt-2">Good morning, {{ auth()->user()->first_name ?? auth()->user()->name ?? 'Administrator' }} 👋</h1>
        <p class="text-sm text-slate-300 mt-2 max-w-2xl">
            Monitor marketplace activity, transactions, community health, logistics and issues requiring administrative attention.
        </p>
    </div>

    <!-- 4 PRIMARY KPI METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">GMV this month</div>
            <div class="text-2xl font-black mt-2 text-slate-900 dark:text-white">@money($gmvThisMonth)</div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">↑ Active volume</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Platform revenue</div>
            <div class="text-2xl font-black mt-2 text-pp-600 dark:text-pp-300">@money($platformRevenue)</div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">↑ 5% escrow commission</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active users</div>
            <div class="text-2xl font-black mt-2 text-slate-900 dark:text-white">{{ number_format($activeUsersCount) }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-1">+{{ $newUsersThisWeek }} this week</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Needs attention</div>
            <div class="text-2xl font-black mt-2 {{ $needsAttentionCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600' }}">{{ $needsAttentionCount }}</div>
            <div class="text-xs text-rose-500 dark:text-rose-400 font-semibold mt-1">Disputes, moderation &amp; KYC</div>
        </div>
    </div>

    <!-- 7-DAY ACTIVITY CHART & ACTION QUEUE -->
    <div class="grid xl:grid-cols-[1.35fr_1fr] gap-6 mt-6">
        <!-- 7-DAY ACTIVITY CHART -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="font-extrabold text-slate-900 dark:text-white">Platform activity</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Transaction volume — last 7 days</p>
            </div>
            
            <div class="h-52 flex items-end gap-3 sm:gap-4 mt-6 pt-4 border-b border-slate-100 dark:border-slate-700/60 pb-2">
                @foreach($chartDays as $cDay)
                    <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                        <div class="w-full relative flex items-end justify-center h-full">
                            <!-- Tooltip -->
                            <div class="absolute -top-8 opacity-0 group-hover:opacity-100 transition duration-150 text-[10px] font-bold bg-slate-900 text-white rounded-lg px-2 py-0.5 pointer-events-none whitespace-nowrap shadow-md z-10">
                                @money($cDay['volume'])
                            </div>
                            <!-- Bar -->
                            <div class="w-full rounded-t-lg transition-all duration-300 {{ $cDay['isToday'] ? 'bg-pp-600 shadow-sm' : 'bg-pp-100 hover:bg-pp-200 dark:bg-pp-900/60 dark:hover:bg-pp-800/80' }}"
                                 style="height: {{ $cDay['heightPercent'] }}%"></div>
                        </div>
                        <span class="text-[11px] font-bold {{ $cDay['isToday'] ? 'text-pp-600 dark:text-pp-400 font-extrabold' : 'text-slate-400 dark:text-slate-500' }}">{{ $cDay['day'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 pt-3">
                <span>7-day rolling window</span>
                <span class="font-bold text-pp-600 dark:text-pp-400">Total volume trend</span>
            </div>
        </div>

        <!-- ACTION QUEUE -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 shadow-xs">
            <h2 class="font-extrabold text-slate-900 dark:text-white">Action queue</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Items requiring urgent attention</p>
            
            <div class="space-y-2.5 mt-5">
                <a class="flex items-center gap-3 p-3.5 rounded-xl bg-rose-50/80 dark:bg-rose-950/30 border border-rose-100/80 dark:border-rose-900/40 hover:bg-rose-100/70 transition" href="{{ route('disputes') }}">
                    <span class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 grid place-items-center font-bold text-base shrink-0">⚖</span>
                    <div class="flex-1 min-w-0">
                        <b class="text-xs text-slate-900 dark:text-slate-100">{{ $activeDisputesCount }} active {{ \Illuminate\Support\Str::plural('dispute', $activeDisputesCount) }}</b>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $oldestDisputeDays > 0 ? "Oldest opened {$oldestDisputeDays} days ago" : 'No delayed disputes' }}</div>
                    </div>
                    <span class="text-slate-400 text-sm font-bold">›</span>
                </a>

                <a class="flex items-center gap-3 p-3.5 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-100/80 dark:border-amber-900/40 hover:bg-amber-100/70 transition" href="{{ route('mylistings') }}">
                    <span class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-400 grid place-items-center font-bold text-base shrink-0">⚑</span>
                    <div class="flex-1 min-w-0">
                        <b class="text-xs text-slate-900 dark:text-slate-100">{{ $pendingListingsCount }} listings awaiting moderation</b>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Catalog verification required</div>
                    </div>
                    <span class="text-slate-400 text-sm font-bold">›</span>
                </a>

                <a class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-50/80 dark:bg-blue-950/30 border border-blue-100/80 dark:border-blue-900/40 hover:bg-blue-100/70 transition" href="#">
                    <span class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 grid place-items-center font-bold text-base shrink-0">✓</span>
                    <div class="flex-1 min-w-0">
                        <b class="text-xs text-slate-900 dark:text-slate-100">{{ $unverifiedUsersCount }} users awaiting verification</b>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">KYC / vendor compliance review</div>
                    </div>
                    <span class="text-slate-400 text-sm font-bold">›</span>
                </a>

                <a class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-100/80 dark:border-emerald-900/40 hover:bg-emerald-100/70 transition" href="{{ route('earnings') }}">
                    <span class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 grid place-items-center font-bold text-base shrink-0">↗</span>
                    <div class="flex-1 min-w-0">
                        <b class="text-xs text-slate-900 dark:text-slate-100">{{ $pendingSettlementsCount }} payout batches pending</b>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">@money($pendingSettlementsAmount) awaiting release</div>
                    </div>
                    <span class="text-slate-400 text-sm font-bold">›</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3 SUB-METRIC PANELS: MARKETPLACE, FINANCIAL, COMMUNITY -->
    <div class="grid xl:grid-cols-3 gap-6 mt-6">
        <!-- MARKETPLACE -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="font-extrabold text-slate-900 dark:text-white">Marketplace</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Current ecosystem activity</p>
                <div class="space-y-3.5 mt-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Active listings</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($activeListings) }}</b>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">New today</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($newListingsToday) }}</b>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Offers today</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($offersToday) }}</b>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Completed sales</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($completedSales) }}</b>
                    </div>
                </div>
            </div>
            <a href="{{ route('welcome') }}" class="w-full mt-6 rounded-xl bg-pp-600 hover:bg-pp-700 text-white py-3 text-xs font-bold text-center block transition shadow-xs">
                Manage Marketplace
            </a>
        </div>

        <!-- FINANCIAL SNAPSHOT -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="font-extrabold text-slate-900 dark:text-white">Financial snapshot</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Money flowing through the platform</p>
                <div class="space-y-4 mt-5">
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Payments collected</div>
                        <div class="text-xl font-black mt-1 text-slate-900 dark:text-white">@money($paymentsCollected)</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Seller settlements pending</div>
                        <div class="text-xl font-black mt-1 text-slate-900 dark:text-white">@money($pendingSettlementsAmount)</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Refunds this month</div>
                        <div class="text-xl font-black mt-1 text-rose-600 dark:text-rose-400">@money($refundsThisMonth)</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('invoices') }}" class="w-full mt-6 rounded-xl border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/50 py-3 text-xs font-bold text-slate-800 dark:text-slate-200 text-center block transition">
                Open Invoices &amp; Finance
            </a>
        </div>

        <!-- COMMUNITY HEALTH -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="font-extrabold text-slate-900 dark:text-white">Community health</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Forum and service activity</p>
                <div class="space-y-3.5 mt-5">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Discussions today</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($discussionsToday) }}</b>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Responses today</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($responsesToday) }}</b>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Reports awaiting review</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($reportsAwaitingReview) }}</b>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Pending moderation</span>
                        <b class="text-slate-900 dark:text-white">{{ number_format($pendingListingsCount) }}</b>
                    </div>
                </div>
            </div>
            <a href="{{ route('community') }}" class="w-full mt-6 rounded-xl border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/50 py-3 text-xs font-bold text-slate-800 dark:text-slate-200 text-center block transition">
                Moderate Community
            </a>
        </div>
    </div>

    <!-- RECENT PLATFORM EVENTS FEED -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl mt-6 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between">
            <div>
                <b class="text-slate-900 dark:text-white">Recent platform events</b>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Latest high-priority activity across the system</p>
            </div>
            <span class="text-xs font-bold text-pp-600 dark:text-pp-400">Live stream</span>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
            @forelse($recentEvents as $event)
                <div class="p-4 flex gap-3.5 items-center hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                    <span class="w-9 h-9 rounded-xl {{ $event['bg'] }} grid place-items-center shrink-0 font-bold text-sm shadow-2xs">
                        {{ $event['icon'] }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <b class="text-sm text-slate-900 dark:text-slate-100 truncate block">{{ $event['title'] }}</b>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $event['subtitle'] }}</div>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 shrink-0 font-medium">{{ $event['time'] }}</span>
                </div>
            @empty
                <div class="p-8 text-center text-xs font-semibold text-slate-400 dark:text-slate-500">
                    No recent events recorded in the activity feed.
                </div>
            @endforelse
        </div>
    </div>
</div>
