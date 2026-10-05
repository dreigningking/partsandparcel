<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Transactions</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Subscriptions</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Seller Subscriptions &amp; Memberships
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Track seller tier memberships, active entitlement quotas, validity lifecycles, and subscription invoices.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.settings.subscription-plans') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
            >
                <i class="fas fa-sliders-h text-slate-400"></i>
                <span>Configure Plans &amp; Rates</span>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Subscriptions</span>
                <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-id-card"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalSubscriptions) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">All time enrolled accounts</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Memberships</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-user-check"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                <span>{{ number_format($activeSubscriptions) }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">Live</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Currently valid memberships</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Expired / Inactive</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                    <i class="fas fa-user-clock"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($expiredSubscriptions) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Elapsed or cancelled tiers</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subscription Revenue</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-coins"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">₦{{ number_format($totalRevenue, 2) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Completed payment receipts</p>
        </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- SEARCH -->
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by member name, email, or tier..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                />
            </div>

            <!-- PLAN FILTER -->
            <div>
                <select
                    wire:model.live="planId"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="">All Tiers / Plans</option>
                    @foreach ($plans as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- STATUS FILTER -->
            <div>
                <select
                    wire:model.live="status"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <!-- SORT BY -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="starts_at">Purchase Date</option>
                    <option value="ends_at">Expiry Date</option>
                    <option value="id">Subscription ID</option>
                </select>
            </div>

            <!-- SORT DIRECTION -->
            <div>
                <select
                    wire:model.live="sortDir"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="desc">Newest First</option>
                    <option value="asc">Oldest First</option>
                </select>
            </div>
        </div>

        @if ($search !== '' || $status !== '' || $planId !== '' || $dateFrom !== '' || $dateTo !== '')
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 font-medium">Filtered results active</span>
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="text-pp-600 hover:text-pp-700 font-bold flex items-center gap-1.5 transition cursor-pointer"
                >
                    <i class="fas fa-undo-alt text-[10px]"></i>
                    <span>Reset All Filters</span>
                </button>
            </div>
        @endif
    </div>

    <!-- SUBSCRIPTIONS TABLE -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3.5">Subscription</th>
                        <th class="px-5 py-3.5">Subscriber</th>
                        <th class="px-5 py-3.5">Plan / Tier</th>
                        <th class="px-5 py-3.5">Entitlements (Req / Resp / List)</th>
                        <th class="px-5 py-3.5">Validity Lifecycle</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($subscriptions as $sub)
                        @php
                            $isLive = $sub->isActive();
                            $daysLeft = $sub->ends_at ? now()->diffInDays($sub->ends_at, false) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- SUBSCRIPTION ID & DATE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900 dark:text-white font-mono">
                                    #SUB-{{ str_pad($sub->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ optional($sub->starts_at)->format('M d, Y') }}
                                </div>
                            </td>

                            <!-- SUBSCRIBER -->
                            <td class="px-5 py-4">
                                @if ($sub->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-pp-100 dark:bg-pp-900/60 text-pp-700 dark:text-pp-300 font-black flex items-center justify-center text-xs uppercase shrink-0">
                                            {{ substr($sub->user->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a
                                                href="{{ route('admin.users.show', ['user' => $sub->user->id]) }}"
                                                class="font-extrabold text-slate-900 dark:text-white hover:text-pp-600 transition truncate block"
                                            >
                                                {{ $sub->user->name }}
                                            </a>
                                            <div class="text-[11px] text-slate-400 truncate">
                                                {{ $sub->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- PLAN / TIER -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($sub->plan)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 font-extrabold text-xs">
                                        <i class="fas fa-id-badge text-[10px]"></i>
                                        <span>{{ $sub->plan->name }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ rtrim(rtrim((string)$sub->plan->escrow_percentage, '0'), '.') }}% Escrow Fee
                                        @if ($sub->plan->escrow_cap)
                                            · Cap: ₦{{ number_format($sub->plan->escrow_cap) }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Custom / Deleted Plan</span>
                                @endif
                            </td>

                            <!-- ENTITLEMENTS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="space-y-0.5 text-xs">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $sub->request_limit }} <span class="font-normal text-slate-400 text-[11px]">req/day</span>
                                        · {{ $sub->response_limit }} <span class="font-normal text-slate-400 text-[11px]">resp/day</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-pp-600 dark:text-pp-400">
                                        {{ number_format($sub->listing_limit) }} max listings
                                    </div>
                                </div>
                            </td>

                            <!-- VALIDITY -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                    {{ optional($sub->ends_at)->format('M d, Y') }}
                                </div>
                                <div class="mt-0.5">
                                    @if ($sub->ends_at && $sub->ends_at->isFuture())
                                        <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400">
                                            {{ $daysLeft }} days remaining
                                        </span>
                                    @elseif ($sub->ends_at)
                                        <span class="text-[10px] font-bold text-rose-500">
                                            Expired {{ $sub->ends_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">Lifetime / No Expiry</span>
                                    @endif
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($sub->status === 'active' && $isLive)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Active</span>
                                    </span>
                                @elseif ($sub->status === 'expired' || ! $isLive)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-extrabold text-[11px]">
                                        <span>Expired</span>
                                    </span>
                                @elseif ($sub->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-extrabold text-[11px]">
                                        <span>Cancelled</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-[11px]">
                                        <span>{{ ucfirst($sub->status) }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="openDetails({{ $sub->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="View Subscription Details & Invoices"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $sub->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-pp-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Edit Quotas & Validity Period"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteSubscription({{ $sub->id }})"
                                        wire:confirm="Are you sure you want to delete this subscription record?"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Delete Subscription"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">No subscriptions found</div>
                                <p class="text-xs text-slate-400 mt-1">Adjust search parameters or filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subscriptions->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: SUBSCRIPTION DETAILS & INVOICE -->
    @if ($showDetailsModal && $selectedSubscription)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeDetails">
            <div class="w-full max-w-xl rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                Subscription #SUB-{{ str_pad($selectedSubscription->id, 5, '0', STR_PAD_LEFT) }}
                            </h3>
                            <p class="text-xs text-slate-400">Enrolled on {{ optional($selectedSubscription->starts_at)->format('M d, Y · H:i') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetails" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- SUBSCRIBER -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Member Information</span>
                        @if ($selectedSubscription->user)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $selectedSubscription->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $selectedSubscription->user->email }}</div>
                                    @if ($selectedSubscription->user->phone)
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $selectedSubscription->user->phone }}</div>
                                    @endif
                                </div>
                                <a
                                    href="{{ route('admin.users.show', ['user' => $selectedSubscription->user->id]) }}"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                >
                                    User Profile
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-slate-400">Account no longer exists.</p>
                        @endif
                    </div>

                    <!-- TIER & LIMITS GRID -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Plan Tier</span>
                            <span class="font-black text-xs text-slate-900 dark:text-white mt-1 block">
                                {{ $selectedSubscription->plan?->name ?? 'Custom' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Requests Cap</span>
                            <span class="font-black text-xs text-slate-900 dark:text-white mt-1 block">
                                {{ $selectedSubscription->request_limit }} / day
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Quotes Cap</span>
                            <span class="font-black text-xs text-slate-900 dark:text-white mt-1 block">
                                {{ $selectedSubscription->response_limit }} / day
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Listings Cap</span>
                            <span class="font-black text-xs text-slate-900 dark:text-white mt-1 block">
                                {{ number_format($selectedSubscription->listing_limit) }} items
                            </span>
                        </div>
                    </div>

                    <!-- LIFECYCLE PERIOD -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 space-y-2">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Validity Schedule</span>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block">Starts On</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ optional($selectedSubscription->starts_at)->format('F d, Y · H:i') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Expires On</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ optional($selectedSubscription->ends_at)->format('F d, Y · H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT / INVOICE RECORDS -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 space-y-2">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Payment &amp; Invoice History</span>
                        @if ($selectedSubscription->payments->isNotEmpty())
                            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($selectedSubscription->payments as $payment)
                                    <div class="py-2 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $payment->reference }}</div>
                                            <div class="text-[10px] text-slate-400">{{ optional($payment->paid_at ?? $payment->created_at)->format('M d, Y · H:i') }} · via {{ $payment->provider ?? 'Gateway' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-black text-slate-900 dark:text-white">₦{{ number_format($payment->amount, 2) }}</div>
                                            <span class="text-[10px] font-bold uppercase {{ $payment->status === 'completed' || $payment->status === 'success' ? 'text-emerald-600' : 'text-amber-500' }}">
                                                {{ $payment->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400">No payment records found (e.g. Free Tier or manual enrollment).</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        @if ($selectedSubscription->status !== 'active')
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedSubscription->id }}, 'active')"
                                class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition"
                            >
                                Activate Membership
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedSubscription->id }}, 'cancelled')"
                                class="px-3 py-1.5 rounded-lg bg-slate-700 text-white font-bold text-xs hover:bg-slate-800 transition"
                            >
                                Cancel Membership
                            </button>
                        @endif
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetails"
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: EDIT SUBSCRIPTION -->
    @if ($showEditModal && $selectedSubscription)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeEdit">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">
                        Edit Subscription #SUB-{{ str_pad($selectedSubscription->id, 5, '0', STR_PAD_LEFT) }}
                    </h3>
                    <button type="button" wire:click="closeEdit" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="saveEdit" class="space-y-4">
                    <!-- PLAN SELECTOR -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Membership Plan</label>
                        <select
                            wire:model="editPlanId"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        >
                            @foreach ($plans as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('editPlanId') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                        <select
                            wire:model="editStatus"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        >
                            <option value="active">Active (Entitlements unlocked)</option>
                            <option value="pending">Pending</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('editStatus') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- DATES -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Starts At</label>
                            <input
                                type="date"
                                wire:model="editStartsAt"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden"
                            />
                            @error('editStartsAt') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ends At</label>
                            <input
                                type="date"
                                wire:model="editEndsAt"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden"
                            />
                            @error('editEndsAt') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- CUSTOM LIMITS -->
                    <div class="grid grid-cols-3 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Daily Requests</label>
                            <input
                                type="number"
                                min="0"
                                wire:model="editRequestLimit"
                                class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Daily Quotes</label>
                            <input
                                type="number"
                                min="0"
                                wire:model="editResponseLimit"
                                class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Max Listings</label>
                            <input
                                type="number"
                                min="0"
                                wire:model="editListingLimit"
                                class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            wire:click="closeEdit"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-save"></i>
                            <span wire:loading.remove wire:target="saveEdit">Save Changes</span>
                            <span wire:loading wire:target="saveEdit">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
