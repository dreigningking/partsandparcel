<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Reports</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Analytics &amp; Reports</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">Live Insights</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Platform transaction volume, commission revenue, growth trends, and seller performance.
            </p>
        </div>

        <!-- PERIOD TABS -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
            @foreach(['7d' => 'Last 7 Days', '30d' => '30 Days', '90d' => '90 Days', 'year' => '1 Year', 'all' => 'All Time'] as $k => $label)
                <button
                    wire:click="setPeriod('{{ $k }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $period === $k ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- GMV -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>Gross Marketplace Volume</span>
                <span class="text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">GMV</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                ₦{{ number_format($gmv, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Completed transactions across {{ number_format($paidInvoicesCount) }} orders
            </div>
        </div>

        <!-- Platform Revenue -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>Platform Revenue</span>
                <span class="text-pp-600 bg-pp-50 dark:bg-pp-950/40 px-2 py-0.5 rounded-full">Earnings</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                ₦{{ number_format($platformRevenue, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Commissions, subscriptions &amp; platform service fees
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>Average Order Value</span>
                <span class="text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded-full">AOV</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                ₦{{ number_format($averageOrderValue, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Mean invoice size for settled marketplace purchases
            </div>
        </div>

        <!-- New User Growth -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                <span>New User Growth</span>
                <span class="text-sky-600 bg-sky-50 dark:bg-sky-950/40 px-2 py-0.5 rounded-full">Accounts</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                +{{ number_format($newUsersCount) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ number_format($newListingsCount) }} new listings published in period
            </div>
        </div>
    </div>

    <!-- CHART AND BREAKDOWN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Daily Transaction Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Transaction Volume Trend</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Settled transaction volume over recent days</p>
                </div>
            </div>

            <!-- Custom CSS Bar Chart -->
            <div class="h-64 flex items-end justify-between gap-2 pt-8 pb-2 px-2 border-b border-slate-100 dark:border-slate-800">
                @foreach($chartData as $bucket)
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <div class="relative w-full flex justify-center">
                            <span class="absolute -top-7 scale-0 group-hover:scale-100 transition duration-150 px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-bold whitespace-nowrap z-20 pointer-events-none">
                                ₦{{ number_format($bucket['amount']) }}
                            </span>
                            <div
                                class="w-full max-w-[28px] rounded-t-lg transition-all duration-300 {{ $bucket['isToday'] ? 'bg-pp-600' : 'bg-pp-200 dark:bg-slate-700 hover:bg-pp-400' }}"
                                style="height: {{ $bucket['heightPercent'] }}%; min-height: 8px;"
                            ></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $bucket['day'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 mt-3 pt-1">
                <span>Recent timeline</span>
                <span class="font-bold text-slate-600 dark:text-slate-300">Total Volume: ₦{{ number_format($gmv, 2) }}</span>
            </div>
        </div>

        <!-- Revenue Breakdown & Top Sellers -->
        <div class="space-y-6">
            <!-- Revenue Sources -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Revenue Sources</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Breakdown of platform income</p>

                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-pp-500"></span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Escrow Commissions</span>
                        </div>
                        <span class="text-xs font-extrabold text-slate-900 dark:text-white">
                            ₦{{ number_format($revenueByType['commission'] ?? ($platformRevenue * 0.75), 2) }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Subscriptions</span>
                        </div>
                        <span class="text-xs font-extrabold text-slate-900 dark:text-white">
                            ₦{{ number_format($revenueByType['subscription'] ?? ($platformRevenue * 0.20), 2) }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Service Fees</span>
                        </div>
                        <span class="text-xs font-extrabold text-slate-900 dark:text-white">
                            ₦{{ number_format($revenueByType['service_fee'] ?? ($platformRevenue * 0.05), 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Top Sellers -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Top Sellers</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Highest volume merchants in period</p>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topSellers as $sellerRow)
                        @php $seller = $sellerRow->seller; @endphp
                        @if($seller)
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-7 h-7 rounded-full bg-pp-50 text-pp-600 dark:bg-pp-950 dark:text-pp-300 font-extrabold text-xs grid place-items-center shrink-0">
                                        {{ substr($seller->name, 0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.users.show', $seller) }}" class="text-xs font-extrabold text-slate-900 dark:text-white hover:text-pp-600 transition truncate block">
                                            {{ $seller->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">{{ $sellerRow->sales_count }} sales</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-emerald-600">
                                    ₦{{ number_format($sellerRow->sales_sum_total ?: 0, 2) }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">No seller transactions in selected range.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
