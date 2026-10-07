<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700">Settings</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Countries &amp; Regions
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage supported operating countries, local currencies, dialing codes, timezones, and payment gateway bindings.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreate"
            class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer self-start sm:self-auto"
        >
            <i class="fas fa-plus"></i> Add New Country
        </button>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search countries by name, code, phone code, or currency..."
                class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
            />
            @if ($search)
                <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <div class="text-xs text-slate-500 font-bold">
            Total Countries: <strong class="text-slate-900 dark:text-white">{{ $countries->count() }}</strong>
        </div>
    </div>

    <!-- COUNTRIES TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 min-w-[300px]">Country</th>
                        <th class="px-4 py-3.5">Currency</th>
                        <th class="px-4 py-3.5 text-nowrap">Cost / View</th>
                        <th class="px-4 py-3.5 text-nowrap">Cost / Click</th>
                        <th class="px-4 py-3.5">Timezone</th>
                        <th class="px-4 py-3.5">Gateways</th>
                        <th class="px-4 py-3.5 text-center">States</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Default</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($countries as $country)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- NAME -->
                            <td class="px-4 py-3.5 min-w-[300px]">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 border border-pp-200 dark:border-pp-800/60 font-black text-xs grid place-items-center uppercase shadow-2xs shrink-0">
                                        {{ $country->code }}
                                    </div>
                                    <div>
                                        <b class="font-extrabold text-slate-900 dark:text-white block">{{ $country->name }} {{ $country->phone_code ? '  '.$country->phone_code: '' }}</b>
                                        @if ($country->is_default)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded">
                                                <i class="fas fa-check-circle text-[8px]"></i> Primary Default
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- CURRENCY -->
                            <td class="px-4 py-3.5">
                                <span class="font-extrabold text-slate-900 dark:text-white">{{ $country->currency }}</span>
                                <span class="text-slate-400 dark:text-slate-500 ml-1">({{ $country->currency_symbol }})</span>
                            </td>

                            <!-- COST / VIEW -->
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-800 dark:text-slate-200">
                                <span class="text-slate-400 text-[10px]">{{ $country->currency_symbol }}</span>{{ number_format((float) ($country->views ?? 0), 4) }}
                            </td>

                            <!-- COST / CLICK -->
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-800 dark:text-slate-200">
                                <span class="text-slate-400 text-[10px]">{{ $country->currency_symbol }}</span>{{ number_format((float) ($country->clicks ?? 0), 2) }}
                            </td>

                            <!-- TIMEZONE -->
                            <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                                {{ $country->timezone ?: '—' }}
                            </td>

                            <!-- GATEWAYS -->
                            <td class="px-4 py-3.5">
                                @if (!empty($country->payment_gateway))
                                    @php
                                        $gateways = is_string($country->payment_gateway) ? json_decode($country->payment_gateway, true) : $country->payment_gateway;
                                    @endphp
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($gateways as $gw)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 border border-pp-200 dark:border-pp-800/60">
                                                {{ $gw }}
                                            </span>
                                        @endforeach 
                                    </div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">None</span>
                                @endif
                            </td>

                            <!-- STATES -->
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-extrabold text-[11px] text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                        {{ $country->states_count }} states
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="syncStates({{ $country->id }})"
                                        class="w-6 h-6 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-pp-600 transition grid place-items-center"
                                        title="Re-sync states from GeographyService"
                                    >
                                        <i class="fas fa-rotate text-[10px]"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="px-4 py-3.5 text-center">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $country->id }})"
                                    class="px-2.5 py-1 rounded-full text-nowrap text-[10px] font-black uppercase transition cursor-pointer {{ $country->is_active ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-100' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-200' }}"
                                    title="Click to toggle status"
                                >
                                    <i class="fas fa-circle text-[8px] mr-1"></i>
                                    {{ $country->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            <!-- DEFAULT -->
                            <td class="px-4 py-3.5 text-center">
                                @if ($country->is_default)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 font-black text-[10px] uppercase">
                                        YES
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        wire:click="setDefault({{ $country->id }})"
                                        class="text-xs text-slate-400 hover:text-pp-600 font-bold hover:underline cursor-pointer"
                                    >
                                        Set Default
                                    </button>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $country->id }})"
                                        class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer flex items-center gap-1"
                                    >
                                        <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                                    </button>
                                    @if (! $country->is_default)
                                        <button
                                            type="button"
                                            wire:click="deleteCountry({{ $country->id }})"
                                            wire:confirm="Are you sure you want to delete country '{{ $country->name }}' and its states?"
                                            class="px-2.5 py-1.5 rounded-lg border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 transition cursor-pointer"
                                            title="Delete country"
                                        >
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-8 text-center text-slate-400 dark:text-slate-500 font-medium">
                                No countries match your search query. Click "+ Add New Country" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- MODAL: ADD / EDIT COUNTRY -->
    <!-- ========================================================================================= -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6 space-y-5 shadow-soft max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-globe text-pp-600"></i>
                        {{ $editingId ? 'Edit Country: ' . $name : 'Add New Country' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 text-sm cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveCountry" class="space-y-4 text-xs">
                    <!-- NAME -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Country Name</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="e.g. Nigeria, United States, Ghana, United Kingdom"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('name') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- CODE & PHONE CODE -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300 block">ISO-2 Code</label>
                            <input
                                type="text"
                                wire:model="code"
                                maxlength="2"
                                placeholder="NG"
                                class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white uppercase outline-none focus:border-pp-500 transition"
                            />
                            <p class="text-[10px] text-slate-400">2-letter ISO code (e.g. NG, US)</p>
                            @error('code') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300 block">Phone Dialing Code</label>
                            <input
                                type="text"
                                wire:model="phone_code"
                                placeholder="+234"
                                class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                            />
                            <p class="text-[10px] text-slate-400">Dialing prefix (e.g. +234, +1)</p>
                            @error('phone_code') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- CURRENCY & SYMBOL -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300 block">Currency Code</label>
                            <input
                                type="text"
                                wire:model="currency"
                                maxlength="3"
                                placeholder="NGN"
                                class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white uppercase outline-none focus:border-pp-500 transition"
                            />
                            <p class="text-[10px] text-slate-400">3-letter currency (e.g. NGN, USD)</p>
                            @error('currency') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300 block">Currency Symbol</label>
                            <input
                                type="text"
                                wire:model="currency_symbol"
                                placeholder="₦"
                                class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                            />
                            <p class="text-[10px] text-slate-400">Symbol (e.g. ₦, $, £, €)</p>
                            @error('currency_symbol') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- TIMEZONE -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Default Timezone</label>
                        <input
                            type="text"
                            wire:model="timezone"
                            placeholder="Africa/Lagos"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        <p class="text-[10px] text-slate-400">Standard PHP timezone identifier (e.g. Africa/Lagos, America/New_York)</p>
                        @error('timezone') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- AD PROMOTION RATES (VIEWS & CLICKS) -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 space-y-3">
                        <div>
                            <h4 class="font-black text-slate-900 dark:text-white text-xs flex items-center gap-1.5">
                                <i class="fas fa-bullhorn text-pp-600"></i>
                                <span>Promotion &amp; Advertising Rates</span>
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Define unit pricing charged to sellers in this country when purchasing catalog views or CPC clicks.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 dark:text-slate-300 block text-xs">Cost per View / Impression</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">{{ $currency_symbol ?: '₦' }}</span>
                                    <input
                                        type="number"
                                        step="0.0001"
                                        min="0"
                                        wire:model="views"
                                        placeholder="0.0050"
                                        class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-xs text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                                    />
                                </div>
                                <p class="text-[10px] text-slate-400">4 decimal precision (e.g. 0.0050, 2.5000)</p>
                                @error('views') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 dark:text-slate-300 block text-xs">Cost per Click (CPC)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">{{ $currency_symbol ?: '₦' }}</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        wire:model="clicks"
                                        placeholder="20.00"
                                        class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 font-mono font-bold text-xs text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                                    />
                                </div>
                                <p class="text-[10px] text-slate-400">2 decimal precision (e.g. 20.00, 50.00)</p>
                                @error('clicks') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT GATEWAYS -->
                    <div class="space-y-1.5 pt-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Supported Payment Gateways</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($availableGateways as $gw)
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 cursor-pointer hover:border-pp-300 transition">
                                    <input
                                        type="checkbox"
                                        value="{{ $gw }}"
                                        wire:model="payment_gateway"
                                        class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                                    />
                                    <div>
                                        <strong class="font-bold text-slate-900 dark:text-white block text-xs capitalize">{{ $gw }}</strong>
                                        <span class="text-[10px] text-slate-400">Escrow & Payout</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_gateway') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- TOGGLES -->
                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center gap-4">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                            />
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">Active Country</span>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="is_default"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                            />
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">Set as System Default</span>
                        </label>
                    </div>

                    @if (! $editingId)
                        <div class="p-3 rounded-xl bg-pp-50 border border-pp-200 text-pp-900 text-[11px] flex items-center gap-2">
                            <i class="fas fa-sync text-pp-600"></i>
                            <span>Upon saving, GeographyService will automatically run to fetch and persist all states for this country.</span>
                        </div>
                    @endif

                    <!-- ACTIONS -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        >
                            <i class="fas fa-check"></i> {{ $editingId ? 'Update Country' : 'Save Country & Sync States' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
