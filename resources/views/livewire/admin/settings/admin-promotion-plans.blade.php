<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">System Settings</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Promotion Plans</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Promotion Plans & Pricing Rates
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure country-specific advertising rates for Pay-Per-Click (CPC) and Impression (Views) campaigns.
            </p>
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <button
                type="button"
                wire:click="openCreate"
                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-plus"></i>
                <span>Add Promotion Plan</span>
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
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Plans</span>
                <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-layer-group"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $totalPlans }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Configured rate cards</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Covered Countries</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-globe-africa"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $coveredCountriesCount }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Countries with active plans</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Default PPC Rate</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fas fa-mouse-pointer"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                ₦20.00
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Standard cost per verified click</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Default CPM Rate</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                ₦5.00
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Per 1,000 views (₦0.0050/view)</p>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search plans by name or country..."
                class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500"
            />
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Showing <span class="font-bold text-slate-800 dark:text-white">{{ $plans->count() }}</span> rate cards
        </div>
    </div>

    <!-- PLANS TABLE -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3.5">Plan Name</th>
                        <th class="px-5 py-3.5">Target Country</th>
                        <th class="px-5 py-3.5">Cost Per Click (CPC)</th>
                        <th class="px-5 py-3.5">Cost Per View (Impression)</th>
                        <th class="px-5 py-3.5">CPM (1,000 Views)</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($plans as $plan)
                        @php
                            $symbol = $plan->country?->currency_symbol ?? '₦';
                            $currencyCode = $plan->country?->currency ?? 'NGN';
                            $cpm = (float) $plan->views * 1000;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-pp-50 dark:bg-pp-950/50 text-pp-600 dark:text-pp-400 flex items-center justify-center font-bold text-xs">
                                        <i class="fas fa-bullhorn"></i>
                                    </span>
                                    <div>
                                        <div class="font-extrabold text-slate-900 dark:text-white">{{ $plan->name }}</div>
                                        @if ($plan->slug)
                                            <span class="inline-block mt-0.5 text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                                {{ $plan->slug }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($plan->country)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase">
                                            {{ $plan->country->code }}
                                        </span>
                                        <span class="font-medium text-slate-800 dark:text-slate-200">
                                            {{ $plan->country->name }}
                                        </span>
                                        <span class="text-[11px] text-slate-400">({{ $currencyCode }})</span>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[11px] font-semibold">
                                        Global / Default
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-xs">
                                    <i class="fas fa-mouse-pointer text-[10px]"></i>
                                    <span>{{ $symbol }}{{ number_format($plan->clicks, 2) }}</span>
                                    <span class="text-[10px] font-normal text-indigo-500">/ click</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs">
                                    <i class="fas fa-eye text-[10px]"></i>
                                    <span>{{ $symbol }}{{ number_format($plan->views, 4) }}</span>
                                    <span class="text-[10px] font-normal text-emerald-500">/ view</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $symbol }}{{ number_format($cpm, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right">
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
                                        wire:confirm="Are you sure you want to delete this promotion plan?"
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
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">No promotion plans found</div>
                                <p class="text-xs text-slate-400 mt-1">Get started by creating a new country promotion rate card.</p>
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

    <!-- MODAL: CREATE / EDIT PLAN -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">
                            {{ $editingId ? 'Edit Promotion Plan' : 'Create Promotion Plan' }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Specify PPC and Impression pricing for this geographical market.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="savePlan" class="space-y-4">
                    <!-- PLAN NAME -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Plan Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="e.g. Nigeria Standard PPC & Impression Plan"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                        @error('name') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- TARGET COUNTRY -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Target Country
                        </label>
                        <select
                            wire:model="country_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        >
                            <option value="">Global / No Specific Country</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }}) — {{ $c->currency }} ({{ $c->currency_symbol }})</option>
                            @endforeach
                        </select>
                        @error('country_id') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- SLUG (OPTIONAL) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Slug <span class="text-slate-400 font-normal">(Optional, auto-generated from name)</span>
                        </label>
                        <input
                            type="text"
                            wire:model="slug"
                            placeholder="e.g. nigeria-standard-promotion-plan"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                        />
                        @error('slug') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- PRICING RATES GRID -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                        <!-- COST PER CLICK -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Cost Per Click (CPC) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₦</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="clicks"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Cost per verified ad click</span>
                            @error('clicks') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- COST PER VIEW (IMPRESSION) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Cost Per View (Impression) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₦</span>
                                <input
                                    type="number"
                                    step="0.0001"
                                    min="0"
                                    wire:model="views"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-500"
                                />
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">0.0050 = ₦5.00 per 1k views</span>
                            @error('views') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- BUTTONS -->
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
                            <span wire:loading.remove wire:target="savePlan">Save Promotion Plan</span>
                            <span wire:loading wire:target="savePlan">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
