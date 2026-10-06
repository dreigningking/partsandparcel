<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Marketplace</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Coupons</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Coupons &amp; Promotional Discounts
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure promotional discount codes, percentage and fixed deductions, order eligibility thresholds, and usage caps.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.coupons.create') }}"
                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-plus"></i>
                <span>Create Coupon</span>
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
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Coupons</span>
                <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-ticket-alt"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalCoupons) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Configured discount codes</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Vouchers</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-check"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                <span>{{ number_format($activeCoupons) }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">Live</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Available for checkout redemption</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Redemptions</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fas fa-receipt"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalRedemptions) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Total orders discounted</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Expired Codes</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                    <i class="fas fa-calendar-times"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($expiredCoupons) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Past expiration date</p>
        </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- SEARCH -->
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by coupon code..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500 uppercase font-mono"
                />
            </div>

            <!-- TYPE FILTER -->
            <div>
                <select
                    wire:model.live="type"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="">All Discount Types</option>
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (₦)</option>
                </select>
            </div>

            <!-- STATUS FILTER -->
            <div>
                <select
                    wire:model.live="status"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active &amp; Valid</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            <!-- SORT -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="created_at">Date Created</option>
                    <option value="code">Coupon Code</option>
                    <option value="value">Discount Value</option>
                    <option value="used_count">Most Used</option>
                    <option value="expires_at">Expiry Date</option>
                </select>
            </div>
        </div>

        @if ($search !== '' || $status !== '' || $type !== '' || $sortBy !== 'created_at')
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

    <!-- COUPONS TABLE -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3.5">Coupon Code</th>
                        <th class="px-5 py-3.5">Discount Rate</th>
                        <th class="px-5 py-3.5">Min Order / Cap</th>
                        <th class="px-5 py-3.5">Redemption Usage</th>
                        <th class="px-5 py-3.5">Validity Period</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($coupons as $coupon)
                        @php
                            $isExpired = $coupon->isExpired();
                            $daysLeft = $coupon->expires_at ? now()->diffInDays($coupon->expires_at, false) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- CODE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 font-mono font-black text-xs uppercase tracking-wider border border-pp-200/60 dark:border-pp-800/60">
                                        {{ $coupon->code }}
                                    </span>
                                </div>
                            </td>

                            <!-- DISCOUNT RATE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($coupon->type === 'percentage')
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm text-pp-600 dark:text-pp-400">
                                        {{ rtrim(rtrim((string)$coupon->value, '0'), '.') }}% OFF
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">Percentage Discount</span>
                                @else
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm text-emerald-600 dark:text-emerald-400">
                                        ₦{{ number_format($coupon->value, 2) }} OFF
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">Fixed Amount Deduction</span>
                                @endif
                            </td>

                            <!-- MIN ORDER / CAP -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-xs">
                                    @if ($coupon->min_order_amount > 0)
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                                            Min: ₦{{ number_format($coupon->min_order_amount, 2) }}
                                        </div>
                                    @else
                                        <div class="text-slate-400">No minimum</div>
                                    @endif

                                    @if ($coupon->max_discount !== null && $coupon->max_discount > 0)
                                        <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                            Max discount: ₦{{ number_format($coupon->max_discount, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- REDEMPTION USAGE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-black text-slate-900 dark:text-white text-xs">
                                    {{ number_format($coupon->used_count) }}
                                    <span class="font-normal text-slate-400">/ {{ $coupon->usage_limit !== null ? number_format($coupon->usage_limit) : 'Unlimited' }}</span>
                                </div>
                                @if ($coupon->usage_limit !== null && $coupon->usage_limit > 0)
                                    @php $percent = min(100, round(($coupon->used_count / $coupon->usage_limit) * 100)); @endphp
                                    <div class="w-24 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="bg-pp-600 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                @endif
                            </td>

                            <!-- VALIDITY -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($coupon->expires_at)
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $coupon->expires_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-[10px] mt-0.5">
                                        @if ($isExpired)
                                            <span class="text-rose-500 font-bold">Expired</span>
                                        @else
                                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $daysLeft }} days left</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">No Expiration Date</span>
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($coupon->is_active && ! $isExpired)
                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $coupon->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-[11px] transition cursor-pointer"
                                        title="Click to deactivate"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Active</span>
                                    </button>
                                @elseif ($isExpired)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-extrabold text-[11px]">
                                        <span>Expired</span>
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $coupon->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-extrabold text-[11px] transition cursor-pointer"
                                        title="Click to activate"
                                    >
                                        <span>Inactive</span>
                                    </button>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('admin.coupons.edit', $coupon) }}"
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="fas fa-edit text-slate-400"></i>
                                        <span>Edit</span>
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="deleteCoupon({{ $coupon->id }})"
                                        wire:confirm="Are you sure you want to permanently delete coupon {{ $coupon->code }}?"
                                        class="px-2.5 py-1.5 rounded-lg border border-rose-200 dark:border-rose-900/60 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">No coupons found</div>
                                <p class="text-xs text-slate-400 mt-1">Get started by creating your first promotional coupon.</p>
                                <a
                                    href="{{ route('admin.coupons.create') }}"
                                    class="inline-block mt-4 px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-soft transition"
                                >
                                    Create First Coupon
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($coupons->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

</div>
