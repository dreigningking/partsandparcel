<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Financials</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Payments &amp; Escrow</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Payments &amp; Escrow Ledger
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Monitor incoming platform payments, reconcile gateway receipts, track escrow fees collected, and audit transaction records.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto flex-wrap">
            @if($search || $status !== 'all' || $provider !== 'all' || $type !== 'all' || $dateFrom || $dateTo || $sortBy !== 'created_at')
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-600 dark:text-rose-400 font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer"
                    title="Reset all active filters"
                >
                    <i class="fas fa-times-circle text-xs"></i>
                    <span>Reset Filters</span>
                </button>
            @endif

            <a
                href="{{ route('admin.payouts') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
                title="View seller payouts and escrow settlements queue"
            >
                <i class="fas fa-hand-holding-usd text-teal-500"></i>
                <span>Payouts &amp; Settlements</span>
            </a>

            <a
                href="{{ route('admin.subscriptions') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
                title="Manage subscription plans and billing tiers"
            >
                <i class="fas fa-crown text-amber-500"></i>
                <span>Subscriptions</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:border-rose-800/60 dark:bg-rose-950/40 p-4 text-sm font-semibold text-rose-800 dark:text-rose-300 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Gross Transacted Volume -->
        <div 
            wire:click="setStatus('all')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $status === 'all' && $type === 'all' && $provider === 'all' ? 'border-pp-500 ring-2 ring-pp-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Volume</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-coins"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-lg sm:text-2xl font-black text-slate-900 dark:text-white truncate">
                ₦{{ number_format($kpi['totalVolume'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">{{ number_format($kpi['totalCount']) }} total transactions</p>
        </div>

        <!-- Escrow Fees Collected -->
        <div 
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-teal-200/60 dark:border-teal-900/40 shadow-2xs"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider">Escrow Fees</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs">
                    <i class="fas fa-shield-alt"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-lg sm:text-2xl font-black text-teal-700 dark:text-teal-400 truncate">
                ₦{{ number_format($kpi['totalEscrowFees'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Platform retention</p>
        </div>

        <!-- Successful Transactions -->
        <div 
            wire:click="setStatus('successful')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $status === 'successful' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Successful</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-check-circle"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">
                {{ number_format($kpi['successfulCount']) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Completed payments</p>
        </div>

        <!-- Held in Escrow -->
        <div 
            wire:click="setStatus('held_in_escrow')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $status === 'held_in_escrow' ? 'border-teal-500 ring-2 ring-teal-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">In Escrow</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs">
                    <i class="fas fa-lock"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                {{ number_format($kpi['heldEscrowCount']) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Safeguarded funds</p>
        </div>

        <!-- Pending Verification -->
        <div 
            wire:click="setStatus('pending')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $status === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pending</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-clock"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400">
                {{ number_format($kpi['pendingCount']) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Awaiting capture</p>
        </div>

        <!-- Failed or Cancelled -->
        <div 
            wire:click="setStatus('failed')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $status === 'failed' ? 'border-rose-500 ring-2 ring-rose-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Failed / Rev</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                    <i class="fas fa-times-circle"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400">
                {{ number_format($kpi['failedCount']) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Declined / broken</p>
        </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search reference, payer, business, email..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500"
                />
            </div>

            <!-- Payment Type Filter -->
            <div>
                <select
                    wire:model.live="type"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500"
                >
                    <option value="all">All Payment Types</option>
                    <option value="invoice">Marketplace Escrow (Invoices)</option>
                    <option value="subscription">Subscription Tier Upgrades</option>
                    <option value="promotion">Listing Promotions</option>
                </select>
            </div>

            <!-- Provider / Gateway Filter -->
            <div>
                <select
                    wire:model.live="provider"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500"
                >
                    <option value="all">All Gateways</option>
                    @foreach($availableProviders as $prov)
                        <option value="{{ $prov }}">{{ ucfirst($prov) }}</option>
                    @endforeach
                    <option value="paystack">Paystack</option>
                    <option value="flutterwave">Flutterwave</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select
                    wire:model.live="status"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500"
                >
                    <option value="all">All Statuses</option>
                    <option value="successful">Successful / Paid</option>
                    <option value="held_in_escrow">Held in Escrow</option>
                    <option value="pending">Pending Verification</option>
                    <option value="failed">Failed / Declined</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500"
                >
                    <option value="created_at">Sort: Date (Newest)</option>
                    <option value="amount">Sort: Amount (Gross)</option>
                    <option value="escrow_fee">Sort: Escrow Fee</option>
                    <option value="paid_at">Sort: Paid Date</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filter Sub-bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-slate-400 font-bold whitespace-nowrap">Date Range:</span>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300"
                    placeholder="From"
                />
                <span class="text-slate-400">to</span>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300"
                    placeholder="To"
                />
            </div>

            <div class="flex items-center gap-3 self-end sm:self-auto">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <span class="font-bold">Rows:</span>
                    <select
                        wire:model.live="perPage"
                        class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300"
                    >
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Reference &amp; Date</th>
                        <th class="px-5 py-3.5">Payer / Client</th>
                        <th class="px-5 py-3.5">Payment Type &amp; Channel</th>
                        <th class="px-5 py-3.5">Gross Amount &amp; Escrow Fee</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Settlement Info</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <!-- Reference & Date -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-extrabold text-slate-900 dark:text-white text-xs bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-700">
                                        {{ $payment->reference }}
                                    </span>
                                </div>
                                <div class="mt-1 text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <i class="far fa-clock text-[10px]"></i>
                                    <span>{{ $payment->created_at->format('M d, Y · h:i A') }}</span>
                                    <span>({{ $payment->created_at->diffForHumans() }})</span>
                                </div>
                            </td>

                            <!-- Payer / Client -->
                            <td class="px-5 py-4">
                                @if($payment->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pp-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.users.show', $payment->user->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-pp-600 transition truncate block">
                                                {{ $payment->user->name }}
                                            </a>
                                            @if($payment->user->business_name)
                                                <span class="text-[11px] text-slate-400 block truncate font-medium">
                                                    {{ $payment->user->business_name }}
                                                </span>
                                            @endif
                                            <span class="text-[11px] text-slate-400 block truncate">
                                                {{ $payment->user->email }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">User account deleted</span>
                                @endif
                            </td>

                            <!-- Payment Type & Channel -->
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <!-- Type Badge -->
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider
                                        {{ $payment->paymentable_type === \App\Models\Invoice::class ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' :
                                           ($payment->paymentable_type === \App\Models\Subscription::class ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800' :
                                           ($payment->paymentable_type === \App\Models\Promotion::class ? 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800' :
                                           'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700')) }}">
                                        @if($payment->paymentable_type === \App\Models\Invoice::class)
                                            <i class="fas fa-handshake text-[9px]"></i>
                                            <span>Marketplace Escrow</span>
                                        @elseif($payment->paymentable_type === \App\Models\Subscription::class)
                                            <i class="fas fa-crown text-[9px]"></i>
                                            <span>Subscription Upgrade</span>
                                        @elseif($payment->paymentable_type === \App\Models\Promotion::class)
                                            <i class="fas fa-bullhorn text-[9px]"></i>
                                            <span>Promotion</span>
                                        @else
                                            <i class="fas fa-credit-card text-[9px]"></i>
                                            <span>{{ $payment->type_label }}</span>
                                        @endif
                                    </span>

                                    <!-- Provider badge & info -->
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        <span class="font-bold uppercase tracking-wider text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $payment->provider ?: 'Direct' }}
                                        </span>
                                        @if($payment->paymentable instanceof \App\Models\Invoice)
                                            <span class="text-slate-400 font-mono">Inv #{{ $payment->paymentable->reference }}</span>
                                        @elseif($payment->paymentable instanceof \App\Models\Subscription)
                                            <span class="text-slate-400">{{ $payment->paymentable->plan?->name ?? 'Tier' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Gross Amount & Escrow Fee -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-sm text-slate-900 dark:text-white">
                                    {{ $payment->currency ?: 'NGN' }} {{ number_format($payment->amount, 2) }}
                                </div>
                                <div class="mt-0.5 text-[11px]">
                                    @if($payment->escrow_fee > 0)
                                        <span class="inline-flex items-center gap-1 text-teal-700 dark:text-teal-400 font-bold bg-teal-50 dark:bg-teal-950/40 px-1.5 py-0.5 rounded">
                                            <i class="fas fa-shield-alt text-[9px]"></i>
                                            Fee: {{ $payment->currency ?: 'NGN' }} {{ number_format($payment->escrow_fee, 2) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Escrow Fee: ₦0.00</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $st = strtolower($payment->status ?? 'pending');
                                @endphp
                                @if(in_array($st, ['successful', 'success', 'paid']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Successful</span>
                                    </span>
                                @elseif($st === 'held_in_escrow')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200 dark:bg-teal-950/40 dark:text-teal-400 dark:border-teal-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                        <span>In Escrow</span>
                                    </span>
                                @elseif($st === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Pending</span>
                                    </span>
                                @elseif($st === 'failed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Failed</span>
                                    </span>
                                @elseif($st === 'refunded')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        <span>Refunded</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                        <span>{{ ucfirst($st) }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Settlement Info -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($payment->settlement)
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-[11px] text-slate-800 dark:text-slate-200">
                                                {{ $payment->settlement->currency }} {{ number_format($payment->settlement->amount, 2) }}
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold uppercase {{ $payment->settlement->status === 'settled' ? 'bg-emerald-100 text-emerald-800' : ($payment->settlement->status === 'eligible' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ $payment->settlement->status }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 block font-mono">
                                            #SET-{{ $payment->settlement->id }}
                                        </span>
                                    </div>
                                @elseif($payment->paymentable_type === \App\Models\Invoice::class)
                                    <span class="text-[11px] text-slate-400 italic">Settlement pending init</span>
                                @else
                                    <span class="text-[11px] text-slate-400">Direct revenue (N/A)</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="showPayment({{ $payment->id }})"
                                        class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer"
                                        title="Inspect full transaction details"
                                    >
                                        <i class="fas fa-eye text-pp-600 text-[11px]"></i>
                                        <span>Inspect</span>
                                    </button>

                                    @if(! $payment->isSuccessful())
                                        <button
                                            type="button"
                                            wire:click="confirmPayment({{ $payment->id }})"
                                            wire:confirm="Confirm this payment? It will be marked as successful and activate the associated order, subscription, or promotion."
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1 cursor-pointer"
                                            title="Confirm bank transfer or captured payment"
                                        >
                                            <i class="fas fa-check text-[10px]"></i>
                                            <span>Confirm</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-lg">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <p class="font-bold text-slate-600 dark:text-slate-300 text-sm">No transactions found</p>
                                    <p class="text-xs text-slate-400">
                                        No payments match the active search filters. Try adjusting your query or resetting filters.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if ($payments->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- TRANSACTION DETAIL MODAL / DRAWER -->
    @if ($showDetailModal && $selectedPayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto">
            <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden my-auto max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="px-6 py-4.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/60 dark:bg-slate-800/60">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-pp-500/10 text-pp-600 flex items-center justify-center text-sm font-black">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Transaction Audit</span>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $selectedPayment->reference }}
                                </span>
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                Transacted on {{ $selectedPayment->created_at->format('M d, Y · h:i A') }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closePayment"
                        class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition cursor-pointer"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6 overflow-y-auto flex-1 text-xs text-slate-600 dark:text-slate-300">
                    
                    <!-- Top Summary Card -->
                    <div class="p-4.5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-soft grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Gross Amount</span>
                            <span class="text-xl font-black text-white mt-1 block">
                                {{ $selectedPayment->currency ?: 'NGN' }} {{ number_format($selectedPayment->amount, 2) }}
                            </span>
                            <span class="text-[10px] text-slate-400">Full charge amount</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider block">Escrow Platform Fee</span>
                            <span class="text-xl font-black text-teal-400 mt-1 block">
                                {{ $selectedPayment->currency ?: 'NGN' }} {{ number_format($selectedPayment->escrow_fee, 2) }}
                            </span>
                            <span class="text-[10px] text-slate-400">Retained by platform</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block">Net Seller Allocation</span>
                            <span class="text-xl font-black text-emerald-400 mt-1 block">
                                {{ $selectedPayment->currency ?: 'NGN' }} {{ number_format(max(0, $selectedPayment->amount - $selectedPayment->escrow_fee), 2) }}
                            </span>
                            <span class="text-[10px] text-slate-400">Payout pool amount</span>
                        </div>
                    </div>

                    <!-- Payer & Entity Information Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Payer Details -->
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-2">
                            <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <i class="fas fa-user-circle"></i>
                                <span>Payer Information</span>
                            </h4>
                            @if($selectedPayment->user)
                                <div class="space-y-1 pt-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400">Name:</span>
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $selectedPayment->user->name }}</span>
                                    </div>
                                    @if($selectedPayment->user->business_name)
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Business:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedPayment->user->business_name }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400">Email:</span>
                                        <span class="font-mono text-[11px]">{{ $selectedPayment->user->email }}</span>
                                    </div>
                                    @if($selectedPayment->user->phone)
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Phone:</span>
                                            <span class="font-mono text-[11px]">{{ $selectedPayment->user->phone }}</span>
                                        </div>
                                    @endif
                                    @if($selectedPayment->user->country)
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Country:</span>
                                            <span class="font-bold">{{ $selectedPayment->user->country->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-slate-400 italic">User no longer in database</p>
                            @endif
                        </div>

                        <!-- Transaction Logistics -->
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-2">
                            <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Channel &amp; Status</span>
                            </h4>
                            <div class="space-y-1 pt-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Status:</span>
                                    <span class="font-bold uppercase tracking-wider text-[10px] px-2 py-0.5 rounded {{ $selectedPayment->status === 'successful' ? 'bg-emerald-100 text-emerald-800' : ($selectedPayment->status === 'held_in_escrow' ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $selectedPayment->status }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Gateway Provider:</span>
                                    <span class="font-bold uppercase">{{ $selectedPayment->provider ?: 'Direct' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Currency:</span>
                                    <span class="font-bold">{{ $selectedPayment->currency ?: 'NGN' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Paid Timestamp:</span>
                                    <span>{{ $selectedPayment->paid_at ? $selectedPayment->paid_at->format('M d, Y · h:i A') : 'Pending / Not paid' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Item / Polymorphic Fulfillment Details -->
                    <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/40 space-y-2">
                        <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-box"></i>
                            <span>Fulfillment Target</span>
                        </h4>

                        @if($selectedPayment->paymentable instanceof \App\Models\Invoice)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Marketplace Invoice:</span>
                                    <a href="{{ route('invoices.view', $selectedPayment->paymentable->id) }}" target="_blank" class="font-mono font-bold text-pp-600 hover:underline">
                                        #INV-{{ $selectedPayment->paymentable->reference }} &rarr;
                                    </a>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Seller / Merchant:</span>
                                    <span class="font-bold">{{ $selectedPayment->paymentable->seller?->name ?? '—' }} ({{ $selectedPayment->paymentable->seller?->business_name ?? '—' }})</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Invoice Status:</span>
                                    <span class="font-bold uppercase text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                        {{ $selectedPayment->paymentable->status }}
                                    </span>
                                </div>
                            </div>
                        @elseif($selectedPayment->paymentable instanceof \App\Models\Subscription)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Subscription Tier:</span>
                                    <span class="font-bold text-amber-600">{{ $selectedPayment->paymentable->plan?->name ?? 'Pro Plan' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Coverage:</span>
                                    <span>{{ $selectedPayment->paymentable->starts_at?->format('M d, Y') }} to {{ $selectedPayment->paymentable->ends_at?->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @elseif($selectedPayment->paymentable instanceof \App\Models\Promotion)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Promotion Type:</span>
                                    <span class="font-bold capitalize">{{ $selectedPayment->paymentable->type }} Boost</span>
                                </div>
                                @if($selectedPayment->paymentable->listing)
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400">Target Listing:</span>
                                        <a href="{{ route('mylisting.view', $selectedPayment->paymentable->listing->id) }}" target="_blank" class="font-bold text-pp-600 hover:underline">
                                            {{ $selectedPayment->paymentable->listing->title }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-slate-400 italic">Standard direct transaction.</p>
                        @endif
                    </div>

                    <!-- Uploaded Proof of Payment / Receipt (if applicable) -->
                    @if ($selectedPayment->receipt_url)
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/40 space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Uploaded Bank Transfer Receipt</span>
                                </h4>
                                <a href="{{ $selectedPayment->receipt_url }}" target="_blank" class="text-pp-600 hover:underline font-bold text-[11px] flex items-center gap-1">
                                    <span>Open Full File</span>
                                    <i class="fas fa-external-link-alt text-[9px]"></i>
                                </a>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-900 max-h-64 flex items-center justify-center p-2">
                                <img src="{{ $selectedPayment->receipt_url }}" class="max-h-60 object-contain rounded-lg shadow-2xs" alt="Payment Proof" />
                            </div>
                        </div>
                    @endif

                    <!-- Raw Metadata Details (collapsible) -->
                    @if (!empty($selectedPayment->metadata))
                        <details class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/20 group">
                            <summary class="font-bold text-[11px] text-slate-500 hover:text-slate-800 dark:hover:text-white cursor-pointer flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-code"></i>
                                    <span>Gateway Raw Metadata Payload</span>
                                </span>
                                <i class="fas fa-chevron-down text-[10px] group-open:rotate-180 transition"></i>
                            </summary>
                            <pre class="mt-3 p-3 rounded-lg bg-slate-900 text-slate-200 font-mono text-[10px] overflow-x-auto">{{ json_encode($selectedPayment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </details>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button
                        type="button"
                        wire:click="closePayment"
                        class="w-full sm:w-auto px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer"
                    >
                        Close Inspector
                    </button>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        @if(! $selectedPayment->isSuccessful())
                            <button
                                type="button"
                                wire:click="markAsFailed({{ $selectedPayment->id }})"
                                wire:confirm="Mark this payment as failed/declined?"
                                class="w-full sm:w-auto px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 font-bold text-xs hover:bg-rose-100 transition cursor-pointer"
                            >
                                <i class="fas fa-ban mr-1"></i>
                                Mark as Failed
                            </button>

                            <button
                                type="button"
                                wire:click="confirmPayment({{ $selectedPayment->id }})"
                                wire:confirm="Confirm this payment? It will mark status as successful and disburse/activate the associated transaction."
                                class="w-full sm:w-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <i class="fas fa-check-circle"></i>
                                <span>Confirm Payment</span>
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <i class="fas fa-shield-check"></i>
                                <span>Verified &amp; Settled</span>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
