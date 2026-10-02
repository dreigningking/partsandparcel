<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">System Settings</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Subscription Plans</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Subscription Plans & Seller Tiers
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure membership tiers, marketplace entitlements (daily RFQs, quote responses, listing limits), escrow discounts, and regional prices.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <button
                type="button"
                wire:click="openCreate"
                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-plus"></i>
                <span>Add Subscription Plan</span>
            </button>
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
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tiers</span>
                <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-layer-group"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $totalPlans }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Configured subscription tiers</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Subscribers</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-users"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-emerald-600 dark:text-emerald-400">
                {{ number_format($activeSubscribersCount) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Members on active subscriptions</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Default Tier</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fas fa-star"></i>
                </span>
            </div>
            <div class="mt-3 text-xl font-black text-slate-900 dark:text-white truncate">
                {{ $defaultPlan?->name ?? 'None' }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Auto-assigned to new accounts</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lowest Escrow Fee</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-percent"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                {{ $plans->min('escrow_percentage') ? rtrim(rtrim((string)$plans->min('escrow_percentage'), '0'), '.') . '%' : '5%' }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Premium vendor rate</p>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search plans by name, slug, or country..."
                class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500"
            />
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Showing <span class="font-bold text-slate-800 dark:text-white">{{ $plans->count() }}</span> subscription tiers
        </div>
    </div>

    <!-- PLANS TABLE -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3.5">Plan Details</th>
                        <th class="px-5 py-3.5">Marketplace Entitlements</th>
                        <th class="px-5 py-3.5">Escrow Fee</th>
                        <th class="px-5 py-3.5">Pricing (Monthly / Annual)</th>
                        <th class="px-5 py-3.5">Subscribers</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($plans as $plan)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- PLAN DETAILS -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $plan->is_default ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400' : 'bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="fas {{ $plan->is_default ? 'fa-star' : 'fa-id-badge' }}"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $plan->name }}</span>
                                            @if ($plan->is_default)
                                                <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-extrabold text-[10px] uppercase tracking-wider">
                                                    Default
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                                {{ $plan->slug }}
                                            </span>
                                            @if (!empty($plan->features['verified_badge']))
                                                <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-0.5">
                                                    <i class="fas fa-check-circle text-[9px]"></i> Verified Badge
                                                </span>
                                            @endif
                                            @if (!empty($plan->features['priority_placement']))
                                                <span class="text-[10px] text-pp-600 font-bold flex items-center gap-0.5">
                                                    <i class="fas fa-arrow-up text-[9px]"></i> Priority Boost
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- ENTITLEMENTS -->
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        <i class="fas fa-comments text-[10px] text-slate-400"></i>
                                        <span>{{ $plan->request_limit }} requests / day</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        <i class="fas fa-reply text-[10px] text-slate-400"></i>
                                        <span>{{ $plan->response_limit }} responses / day</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        <i class="fas fa-boxes text-[10px] text-slate-400"></i>
                                        <span>{{ number_format($plan->listing_limit) }} active listings</span>
                                    </div>
                                </div>
                            </td>

                            <!-- ESCROW FEE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 font-extrabold text-xs">
                                    <i class="fas fa-shield-alt text-[10px]"></i>
                                    <span>{{ rtrim(rtrim((string)$plan->escrow_percentage, '0'), '.') }}%</span>
                                </span>
                            </td>

                            <!-- PRICING -->
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    @forelse ($plan->prices as $price)
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 uppercase">
                                                {{ $price->country?->code ?? 'NG' }}
                                            </span>
                                            <span class="font-extrabold text-slate-900 dark:text-white">
                                                {{ $price->country?->currency_symbol ?? '₦' }}{{ number_format($price->price_monthly, 2) }}
                                                <span class="text-[10px] font-normal text-slate-400">/ mo</span>
                                            </span>
                                            <span class="text-slate-400 text-[10px]">·</span>
                                            <span class="font-bold text-slate-600 dark:text-slate-300">
                                                {{ $price->country?->currency_symbol ?? '₦' }}{{ number_format($price->price_annual, 2) }}
                                                <span class="text-[10px] font-normal text-slate-400">/ yr</span>
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">No prices configured</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- SUBSCRIBERS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-black text-slate-900 dark:text-white text-sm">
                                    {{ number_format($plan->subscriptions_count) }}
                                </span>
                                <span class="text-slate-400 text-xs">users</span>
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $plan->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold transition cursor-pointer {{ $plan->is_active ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}"
                                    title="Click to toggle status"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ $plan->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                    <span>{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $plan->id }})"
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="fas fa-edit text-slate-400"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="deletePlan({{ $plan->id }})"
                                        wire:confirm="Are you sure you want to delete this subscription plan?"
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
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">No subscription plans found</div>
                                <p class="text-xs text-slate-400 mt-1">Get started by creating a new subscription membership tier.</p>
                                <button
                                    type="button"
                                    wire:click="openCreate"
                                    class="mt-4 px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-soft transition"
                                >
                                    Create First Plan
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: CREATE / EDIT SUBSCRIPTION PLAN -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeModal">
            <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">
                            {{ $editingId ? 'Edit Subscription Plan' : 'Create Subscription Plan' }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Configure membership tier entitlements, limits, and multi-currency pricing.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="savePlan" class="space-y-4">
                    <!-- BASIC IDENTITY -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Plan Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Pro Technician & Vendor"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                            />
                            @error('name') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Slug <span class="text-slate-400 font-normal">(Optional, auto-generated)</span>
                            </label>
                            <input
                                type="text"
                                wire:model="slug"
                                placeholder="e.g. pro-technician-vendor"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                            />
                            @error('slug') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- TOGGLES -->
                    <div class="flex items-center gap-6 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Active Tier</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_default" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Default Free Plan for New Signups</span>
                        </label>
                    </div>

                    <!-- LIMITS & ENTITLEMENTS -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Marketplace Entitlements &amp; Caps</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Daily Requests</label>
                                <input
                                    type="number"
                                    min="0"
                                    wire:model="request_limit"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                                @error('request_limit') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Daily Responses</label>
                                <input
                                    type="number"
                                    min="0"
                                    wire:model="response_limit"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                                @error('response_limit') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Max Listings</label>
                                <input
                                    type="number"
                                    min="0"
                                    wire:model="listing_limit"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                                @error('listing_limit') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Escrow Fee %</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    wire:model="escrow_percentage"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                                @error('escrow_percentage') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SPECIAL PERKS CHECKLIST -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Special Vendor Perks</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="priority_placement" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                                <span class="font-bold text-slate-700 dark:text-slate-300">Priority Placement in Search</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="verified_badge" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                                <span class="font-bold text-slate-700 dark:text-slate-300">Verified Seller Badge</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="dedicated_arbitration" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                                <span class="font-bold text-slate-700 dark:text-slate-300">Dedicated Arbitration & Dispute Handling</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="dedicated_support" class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-700" />
                                <span class="font-bold text-slate-700 dark:text-slate-300">24/7 Dedicated Support Agent</span>
                            </label>
                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Public Summary Description
                        </label>
                        <input
                            type="text"
                            wire:model="description"
                            placeholder="e.g. 10 community requests/day, 10 quote responses/day, up to 50 active listings, 7% escrow fee"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                    </div>

                    <!-- REGIONAL COUNTRY PRICING -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Country Pricing Rates</h4>
                            <button
                                type="button"
                                wire:click="addPriceRow"
                                class="px-2.5 py-1 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 font-extrabold text-[11px] hover:bg-pp-100 transition flex items-center gap-1 cursor-pointer"
                            >
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Add Country</span>
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach ($priceRows as $index => $row)
                                <div class="grid grid-cols-1 sm:grid-cols-7 gap-2 items-center p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700" wire:key="price-row-{{ $index }}">
                                    <!-- COUNTRY SELECTOR -->
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-slate-400 mb-0.5">Country</label>
                                        <select
                                            wire:model="priceRows.{{ $index }}.country_id"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden"
                                        >
                                            <option value="">Select country...</option>
                                            @foreach ($countries as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->currency }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- MONTHLY PRICE -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-400 mb-0.5">Monthly Price</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model="priceRows.{{ $index }}.price_monthly"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden"
                                        />
                                    </div>

                                    <!-- ANNUAL PRICE -->
                                    <div class="sm:col-span-2 flex items-end gap-1.5">
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-slate-400 mb-0.5">Annual Price</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                wire:model="priceRows.{{ $index }}.price_annual"
                                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden"
                                            />
                                        </div>
                                        @if (count($priceRows) > 1)
                                            <button
                                                type="button"
                                                wire:click="removePriceRow({{ $index }})"
                                                class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                                title="Remove country rate"
                                            >
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @error('priceRows') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- FOOTER BUTTONS -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-check"></i>
                            <span wire:loading.remove wire:target="savePlan">Save Subscription Plan</span>
                            <span wire:loading wire:target="savePlan">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
