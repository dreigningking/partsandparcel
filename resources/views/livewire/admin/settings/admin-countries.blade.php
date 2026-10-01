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
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-soft">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 uppercase font-black text-[10px] tracking-wider">
                    <tr>
                        <th class="p-4">Country</th>
                        <th class="p-4 text-center">Code</th>
                        <th class="p-4">Phone Code</th>
                        <th class="p-4">Currency</th>
                        <th class="p-4">Timezone</th>
                        <th class="p-4">Gateways</th>
                        <th class="p-4 text-center">States</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Default</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                    @forelse ($countries as $country)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                            <!-- NAME -->
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-pp-50 text-pp-700 border border-pp-200 font-black text-xs grid place-items-center uppercase shadow-2xs">
                                        {{ $country->code }}
                                    </div>
                                    <div>
                                        <b class="font-extrabold text-slate-900 dark:text-white block">{{ $country->name }}</b>
                                        @if ($country->is_default)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded">
                                                <i class="fas fa-check-circle text-[8px]"></i> Primary Default
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- CODE -->
                            <td class="p-4 text-center font-mono font-bold text-slate-900 dark:text-white">
                                {{ $country->code }}
                            </td>

                            <!-- PHONE CODE -->
                            <td class="p-4 font-mono font-bold text-slate-600 dark:text-slate-400">
                                {{ $country->phone_code ?: '—' }}
                            </td>

                            <!-- CURRENCY -->
                            <td class="p-4">
                                <span class="font-extrabold text-slate-900 dark:text-white">{{ $country->currency }}</span>
                                <span class="text-slate-400 ml-1">({{ $country->currency_symbol }})</span>
                            </td>

                            <!-- TIMEZONE -->
                            <td class="p-4 text-slate-600 dark:text-slate-400 font-medium">
                                {{ $country->timezone ?: '—' }}
                            </td>

                            <!-- GATEWAYS -->
                            <td class="p-4">
                                @if (!empty($country->payment_gateway))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ((array) $country->payment_gateway as $gw)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-pp-50 text-pp-700 border border-pp-200">
                                                {{ $gw }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">None</span>
                                @endif
                            </td>

                            <!-- STATES -->
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 font-extrabold text-[11px] text-slate-800 dark:text-slate-200">
                                        {{ $country->states_count }} states
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="syncStates({{ $country->id }})"
                                        class="w-6 h-6 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-pp-600 transition grid place-items-center"
                                        title="Re-sync states from GeographyService"
                                    >
                                        <i class="fas fa-rotate text-[10px]"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="p-4 text-center">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $country->id }})"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer {{ $country->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                    title="Click to toggle status"
                                >
                                    <i class="fas fa-circle text-[8px] mr-1"></i>
                                    {{ $country->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            <!-- DEFAULT -->
                            <td class="p-4 text-center">
                                @if ($country->is_default)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] uppercase">
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
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $country->id }})"
                                        class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center gap-1"
                                    >
                                        <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                                    </button>
                                    @if (! $country->is_default)
                                        <button
                                            type="button"
                                            wire:click="deleteCountry({{ $country->id }})"
                                            wire:confirm="Are you sure you want to delete country '{{ $country->name }}' and its states?"
                                            class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition cursor-pointer"
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
                            <td colspan="10" class="p-8 text-center text-slate-400 font-medium">
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

                    <!-- PAYMENT GATEWAYS -->
                    <div class="space-y-1.5 pt-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Supported Payment Gateways</label>
                        <p class="text-[10px] text-slate-400 mb-1.5">Select the gateways configured for escrow and payouts in this country:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="paystack"
                                    wire:model="payment_gateway"
                                    class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                                />
                                <div>
                                    <strong class="font-bold text-slate-900 dark:text-white block text-xs">Paystack</strong>
                                    <span class="text-[10px] text-slate-400">Card, Transfer, NUBAN</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="flutterwave"
                                    wire:model="payment_gateway"
                                    class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                                />
                                <div>
                                    <strong class="font-bold text-slate-900 dark:text-white block text-xs">Flutterwave</strong>
                                    <span class="text-[10px] text-slate-400">Multi-currency, Payouts</span>
                                </div>
                            </label>
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
