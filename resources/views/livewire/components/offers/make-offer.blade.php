<div>
    @if ($isOpen)
        <!-- BACKDROP OVERLAY -->
        <div wire:click="closeDrawer" class="fixed inset-0 z-[98] bg-slate-950/60 backdrop-blur-xs transition"></div>

        <!-- MAKE PACKAGE OFFER SIDE-DRAWER -->
        <div class="fixed top-0 right-0 bottom-0 z-[99] w-full sm:w-[500px] bg-white shadow-2xl flex flex-col transition">
            
            <!-- DRAWER HEADER -->
            <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-pp-100 text-pp-700 grid place-items-center shrink-0 font-bold">
                        <i class="fas fa-handshake text-pp-600"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Make Custom Offer &amp; Proposal</h3>
                        <p class="text-[11px] text-slate-500">Submitting to <strong class="text-slate-900">{{ $sellerName }}</strong></p>
                    </div>
                </div>
                <button wire:click="closeDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- DRAWER FORM BODY (SCROLLABLE) -->
            <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-bold text-xs">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- STEP 1: ITEMS INCLUDED IN OFFER -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-boxes text-pp-600"></i> Items to Include ({{ count($selectedItemIds) }}/{{ count($cartItems) }})
                        </span>
                        <span class="text-[11px] text-slate-500">Uncheck to exclude items</span>
                    </div>

                    <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-slate-50/50 p-2 space-y-1">
                        @foreach ($cartItems as $item)
                            @php
                                $isSelected = in_array($item['id'], $selectedItemIds);
                            @endphp
                            <div wire:click="toggleItem({{ $item['id'] }})" class="p-2.5 rounded-xl cursor-pointer flex items-center justify-between gap-3 transition {{ $isSelected ? 'bg-white border border-pp-200 shadow-2xs' : 'opacity-60 hover:opacity-100' }}">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" @checked($isSelected) class="accent-pp-600 rounded cursor-pointer pointer-events-none" />
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $item['title'] }}</div>
                                        <div class="text-[10px] text-slate-500">Qty: {{ $item['quantity'] }} · {{ $item['specs'] ?? 'Standard' }}</div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-900 text-xs">₦{{ number_format($item['price'] * $item['quantity']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- STEP 2: DELIVERY OPTION SELECTION -->
                <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-3">
                    <span class="text-xs font-extrabold text-slate-900 block border-b border-pp-200/60 pb-2">
                        <i class="fas fa-truck text-pp-600"></i> Delivery &amp; Fulfillment
                    </span>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900">
                            <input type="radio" wire:model.live="deliveryMode" value="pickup" class="accent-pp-600" />
                            <span>Buyer Pickup</span>
                        </label>
                        <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                            "I'll collect this from the seller." Collect directly from {{ $sellerName }}. No shipping charge.
                        </p>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900 pt-1">
                            <input type="radio" wire:model.live="deliveryMode" value="seller_delivery" class="accent-pp-600" />
                            <span>Seller Delivery</span>
                        </label>
                        <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                            "The seller will deliver this to me." {{ $sellerName }} dispatches shipment to your destination.
                        </p>
                    </div>

                    @if ($deliveryMode === 'seller_delivery')
                        <div class="space-y-3 pt-3 border-t border-pp-200/60">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[11px] font-bold text-slate-700">Delivery Destination Address <span class="text-rose-500">*</span></label>
                                    <button type="button" wire:click="$toggle('showNewAddressForm')" class="text-[10px] font-bold text-pp-600 hover:underline">
                                        {{ $showNewAddressForm ? 'Cancel' : '+ Add Address' }}
                                    </button>
                                </div>

                                @if ($showNewAddressForm)
                                    <div class="p-3 bg-white rounded-xl border border-pp-200 space-y-2 mb-2">
                                        <input type="text" wire:model="newAddressLine" placeholder="Street Address line..." class="w-full p-2 border border-slate-200 rounded-lg text-xs" />
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" wire:model="newCity" placeholder="City (e.g. Ikeja)" class="p-2 border border-slate-200 rounded-lg text-xs" />
                                            <input type="text" wire:model="newState" placeholder="State (e.g. Lagos)" class="p-2 border border-slate-200 rounded-lg text-xs" />
                                        </div>
                                        <button type="button" wire:click="saveNewAddress" class="w-full py-1.5 bg-pp-600 text-white rounded-lg font-bold text-[11px]">Save &amp; Select Address</button>
                                    </div>
                                @else
                                    <select wire:model.live="deliveryAddressId" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                                        @foreach ($savedAddresses as $addr)
                                            <option value="{{ $addr['id'] }}">{{ $addr['label'] ?? 'Address' }}: {{ $addr['address'] }} ({{ $addr['city'] }}, {{ $addr['state'] }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 font-medium flex items-start gap-2">
                                <i class="fas fa-info-circle text-amber-600 text-xs shrink-0 mt-0.5"></i>
                                <span><strong>{{ $sellerName }}</strong> will review your location and verify delivery dispatch.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- STEP 3: PROPOSED PRICE & SAVINGS PREVIEW -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-slate-900">Your Proposed Price (₦) <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-slate-500">Original Subtotal: <strong>₦{{ number_format($selectedSubtotal) }}</strong></span>
                    </div>
                    
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-sm font-bold text-slate-400">₦</span>
                        <input type="number" wire:model.live="proposedPrice" placeholder="e.g. 260000" class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white text-base font-black text-pp-700 outline-none focus:border-pp-600" />
                    </div>

                    @if ($savings > 0)
                        <div class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 flex items-center justify-between">
                            <span>Requested Discount:</span>
                            <span>-₦{{ number_format($savings) }} ({{ round(($savings / max(1, $selectedSubtotal)) * 100) }}% off)</span>
                        </div>
                    @endif
                </div>

                <!-- STEP 4: WARRANTY TERM SELECTION -->
                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-slate-900 block">Requested Warranty Term</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="setWarrantyDays(7)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyDays === 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">7 Days</button>
                        <button type="button" wire:click="setWarrantyDays(14)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyDays === 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">14 Days</button>
                        <button type="button" wire:click="setWarrantyDays(30)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyDays === 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">30 Days</button>
                    </div>
                </div>

                <!-- STEP 5: OPTIONAL REPAIR / WORKMANSHIP SERVICE -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900">
                        <input type="checkbox" wire:model.live="requestRepair" class="accent-pp-600 rounded" />
                        <span>Request Installation or Testing Service</span>
                    </label>

                    @if ($requestRepair)
                        <div class="space-y-2 pt-2 border-t border-slate-200">
                            <input type="text" wire:model="repairServiceType" placeholder="e.g. Motherboard installation &amp; testing" class="w-full p-2 border border-slate-200 rounded-lg text-xs" />
                            <textarea wire:model="repairDetails" rows="2" placeholder="Specific technician requests or testing requirements..." class="w-full p-2 border border-slate-200 rounded-lg text-xs"></textarea>
                        </div>
                    @endif
                </div>

                <!-- STEP 6: CUSTOM NOTES -->
                <div class="space-y-1">
                    <label class="text-xs font-extrabold text-slate-900 block mb-1">Proposal Notes / Instructions for Seller</label>
                    <textarea wire:model="offerNote" placeholder="Add custom terms, questions, or timing preferences for {{ $sellerName }}..." rows="2" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600"></textarea>
                </div>

            </div>

            <!-- DRAWER FOOTER -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3 shrink-0">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-bold">Proposed Items Net Price:</span>
                    <span class="text-xl font-black text-slate-950">₦{{ number_format((float) str_replace(',', '', $proposedPrice ?: '0')) }}</span>
                </div>

                <button wire:click="submitPackageOffer" class="w-full py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Offer Proposal to {{ strtok($sellerName, ' ') }}</span>
                </button>
            </div>

        </div>
    @endif
</div>