<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Marketplace</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Promotions</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Promotions & Sponsored Campaigns
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Monitor campaign delivery, manage sponsored listings, and inspect performance across PPC and impression promotions.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.settings.countries') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
            >
                <i class="fas fa-sliders-h text-slate-400"></i>
                <span>Configure Rate Cards</span>
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
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Campaigns</span>
                <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-rocket"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalPromotions) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">All time promotions placed</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Campaigns</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-bolt"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                <span>{{ number_format($activePromotions) }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">Live</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Currently receiving boost</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Clicks Delivered</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fas fa-mouse-pointer"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalClicksAchieved) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Verified PPC engagements</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Impressions Delivered</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalViewsAchieved) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Total sponsored listing views</p>
        </div>
    </div>

    <!-- FILTER & SEARCH TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- SEARCH -->
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by seller, email, or item..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                />
            </div>

            <!-- TYPE FILTER -->
            <div>
                <select
                    wire:model.live="type"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                >
                    <option value="">All Campaign Types</option>
                    <option value="clicks">Clicks (Pay-Per-Click)</option>
                    <option value="views">Views (Impression CPM)</option>
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
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- DATE FROM -->
            <div>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                />
            </div>

            <!-- DATE TO -->
            <div>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                />
            </div>
        </div>

        @if ($search !== '' || $status !== '' || $type !== '' || $dateFrom !== '' || $dateTo !== '')
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

    <!-- PROMOTIONS TABLE -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3.5">Started Date</th>
                        <th class="px-5 py-3.5">Seller</th>
                        <th class="px-5 py-3.5">Promoted Listing</th>
                        <th class="px-5 py-3.5">Campaign Type</th>
                        <th class="px-5 py-3.5">Delivered</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($promotions as $promotion)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- DATE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $promotion->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    {{ $promotion->created_at->format('H:i') }}
                                </div>
                            </td>

                            <!-- SELLER -->
                            <td class="px-5 py-4">
                                @if ($promotion->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-pp-100 dark:bg-pp-900/60 text-pp-700 dark:text-pp-300 font-black flex items-center justify-center text-xs uppercase shrink-0">
                                            {{ substr($promotion->user->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 dark:text-white truncate">
                                                {{ $promotion->user->name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 truncate">
                                                {{ $promotion->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- LISTING -->
                            <td class="px-5 py-4">
                                @if ($promotion->listing)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0 text-slate-400 overflow-hidden">
                                            @if ($promotion->listing->primary_image_url)
                                                <img src="{{ $promotion->listing->primary_image_url }}" alt="Listing image" class="w-full h-full object-cover rounded-xl" />
                                            @else
                                                <i class="fas fa-box text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <a
                                                href="{{ route('admin.listings.show', ['listing' => $promotion->listing]) }}"
                                                class="font-extrabold text-slate-900 dark:text-white hover:text-pp-600 transition truncate block"
                                                title="{{ $promotion->listing->item?->name ?? $promotion->listing->title }}"
                                            >
                                                {{ $promotion->listing->item?->name ?? $promotion->listing->title }}
                                            </a>
                                            <div class="text-[11px] text-pp-600 dark:text-pp-400 font-bold">
                                                ₦{{ number_format($promotion->listing->price, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Listing deleted or unassigned</span>
                                @endif
                            </td>

                            <!-- CAMPAIGN TYPE -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($promotion->type === 'clicks')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-xs">
                                        <i class="fas fa-mouse-pointer text-[10px]"></i>
                                        <span>Clicks (PPC)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-xs">
                                        <i class="fas fa-eye text-[10px]"></i>
                                        <span>Views (CPM)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- DELIVERED / ACHIEVED COUNT -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $targetQty = (int) ($promotion->payments?->metadata['quantity'] ?? 0);
                                @endphp
                                <div class="font-black text-slate-900 dark:text-white text-sm">
                                    {{ number_format($promotion->achieved_count) }}
                                    @if ($targetQty > 0)
                                        <span class="text-xs font-normal text-slate-400">/ {{ number_format($targetQty) }}</span>
                                    @endif
                                    <span class="text-xs font-semibold text-slate-400">{{ $promotion->type }}</span>
                                </div>
                                @if ($targetQty > 0)
                                    @php $progress = min(100, round(($promotion->achieved_count / $targetQty) * 100)); @endphp
                                    <div class="w-24 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-1 overflow-hidden" title="{{ $progress }}% delivered">
                                        <div class="bg-pp-600 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($promotion->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Active</span>
                                    </span>
                                @elseif ($promotion->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Pending</span>
                                    </span>
                                @elseif ($promotion->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-extrabold text-[11px]">
                                        <i class="fas fa-check text-[9px]"></i>
                                        <span>Completed</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-extrabold text-[11px]">
                                        <span>Inactive</span>
                                    </span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- QUICK TOGGLE STATUS BUTTON -->
                                    @if ($promotion->status === 'active')
                                        <button
                                            type="button"
                                            wire:click="updateStatus({{ $promotion->id }}, 'inactive')"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                            title="Pause / Deactivate"
                                        >
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @elseif ($promotion->status === 'inactive' || $promotion->status === 'pending')
                                        <button
                                            type="button"
                                            wire:click="updateStatus({{ $promotion->id }}, 'active')"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                            title="Activate Campaign"
                                        >
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif

                                    <!-- VIEW DETAILS -->
                                    <button
                                        type="button"
                                        wire:click="openDetails({{ $promotion->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="View Campaign Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $promotion->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-pp-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Edit Campaign"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- DELETE -->
                                    <button
                                        type="button"
                                        wire:click="deletePromotion({{ $promotion->id }})"
                                        wire:confirm="Are you sure you want to permanently delete this promotion?"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Delete Promotion"
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
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">No promotions found</div>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria or filter options.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($promotions->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: CAMPAIGN DETAILS -->
    @if ($showDetailsModal && $selectedPromotion)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeDetails">
            <div class="w-full max-w-xl rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-bullhorn"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                Promotion Campaign #{{ $selectedPromotion->id }}
                            </h3>
                            <p class="text-xs text-slate-400">Created on {{ $selectedPromotion->created_at->format('M d, Y · H:i') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetails" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- STATUS & TYPE BADGES -->
                    <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                            <span class="mt-1 font-extrabold text-sm capitalize text-slate-900 dark:text-white">
                                {{ $selectedPromotion->status }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Campaign Deliverable</span>
                            <span class="mt-1 font-extrabold text-sm text-slate-900 dark:text-white">
                                {{ number_format($selectedPromotion->achieved_count) }} {{ $selectedPromotion->type }}
                                @if ($target = ($selectedPromotion->payments?->metadata['quantity'] ?? null))
                                    <span class="text-xs font-normal text-slate-400 block font-sans">Target: {{ number_format((int)$target) }} {{ $selectedPromotion->type }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- PROMOTED LISTING INFO -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 space-y-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Promoted Listing</span>
                        @if ($selectedPromotion->listing)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 overflow-hidden shrink-0">
                                    @if ($selectedPromotion->listing->primary_image_url)
                                        <img src="{{ $selectedPromotion->listing->primary_image_url }}" alt="Listing" class="w-full h-full object-cover rounded-xl" />
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                                        {{ $selectedPromotion->listing->item?->name ?? $selectedPromotion->listing->title }}
                                    </div>
                                    <div class="text-xs text-pp-600 font-bold">
                                        ₦{{ number_format($selectedPromotion->listing->price, 2) }}
                                    </div>
                                </div>
                                <a
                                    href="{{ route('admin.listings.show', ['listing' => $selectedPromotion->listing]) }}"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                >
                                    Open Listing
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-slate-400">Listing record is no longer available.</p>
                        @endif
                    </div>

                    <!-- SELLER DETAILS -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 space-y-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Seller Information</span>
                        @if ($selectedPromotion->user)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $selectedPromotion->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $selectedPromotion->user->email }}</div>
                                </div>
                                <a
                                    href="{{ route('admin.users.show', ['user' => $selectedPromotion->user->id]) }}"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                >
                                    View Profile
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-slate-400">No seller information.</p>
                        @endif
                    </div>

                    <!-- PAYMENT & VOUCHER DETAILS -->
                    @if ($selectedPromotion->payments)
                        @php
                            $payment = $selectedPromotion->payments;
                            $meta = $payment->metadata ?? [];
                        @endphp
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Payment &amp; Billing</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ in_array($payment->status, ['completed', 'successful', 'success']) ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Amount Paid</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white">
                                        {{ $payment->currency ?? 'NGN' }} {{ number_format($payment->amount, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Provider / Channel</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ ucfirst($payment->provider ?? 'Gateway') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Reference</span>
                                    <span class="font-mono text-[11px] text-slate-700 dark:text-slate-300 break-all">
                                        {{ $payment->reference }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Paid Date</span>
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y · H:i') : 'Pending verification' }}
                                    </span>
                                </div>
                                @if (! empty($meta['coupon_code']))
                                    <div class="col-span-2 flex items-center justify-between p-2.5 rounded-lg bg-pp-50 dark:bg-pp-950/40 border border-pp-200/60 dark:border-pp-800/60">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-ticket-alt text-pp-600 text-xs"></i>
                                            <span class="text-xs text-slate-700 dark:text-slate-300">
                                                Coupon Applied: <strong class="font-mono font-black text-pp-700 dark:text-pp-300">{{ $meta['coupon_code'] }}</strong>
                                            </span>
                                        </div>
                                        @if (! empty($meta['discount']))
                                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                                -₦{{ number_format((float) $meta['discount'], 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        @if ($selectedPromotion->status !== 'active')
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedPromotion->id }}, 'active')"
                                class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition"
                            >
                                Mark Active
                            </button>
                        @endif
                        @if ($selectedPromotion->status !== 'completed')
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedPromotion->id }}, 'completed')"
                                class="px-3 py-1.5 rounded-lg bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 transition"
                            >
                                Mark Completed
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

    <!-- MODAL: EDIT CAMPAIGN -->
    @if ($showEditModal && $selectedPromotion)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeEdit">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">
                        Edit Campaign #{{ $selectedPromotion->id }}
                    </h3>
                    <button type="button" wire:click="closeEdit" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="saveEdit" class="space-y-4">
                    <!-- STATUS -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                        <select
                            wire:model="editStatus"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        >
                            <option value="active">Active (Boost live)</option>
                            <option value="pending">Pending (Awaiting boost)</option>
                            <option value="inactive">Inactive (Paused)</option>
                            <option value="completed">Completed (Goal finished)</option>
                        </select>
                        @error('editStatus') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- CAMPAIGN TYPE -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Campaign Type</label>
                        <select
                            wire:model="editType"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        >
                            <option value="clicks">Clicks (Pay-Per-Click)</option>
                            <option value="views">Views (Impression CPM)</option>
                        </select>
                        @error('editType') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- ACHIEVED COUNT -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Delivered / Achieved Deliverable Count
                        </label>
                        <input
                            type="number"
                            min="0"
                            wire:model="editAchievedCount"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                        <span class="text-[10px] text-slate-400 mt-1 block">Number of clicks or views delivered to this listing.</span>
                        @error('editAchievedCount') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
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
