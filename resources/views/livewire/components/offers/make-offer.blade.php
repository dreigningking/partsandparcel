<div>
    @if ($isOpen)
        <!-- BACKDROP OVERLAY -->
        <div wire:click="closeDrawer" class="fixed inset-0 z-[98] bg-slate-950/60 backdrop-blur-xs transition"></div>

        <!-- MAKE PACKAGE OFFER SIDE-DRAWER -->
        <div class="fixed top-0 right-0 bottom-0 z-[99] w-full sm:w-[540px] bg-white shadow-2xl flex flex-col transition">
            
            <!-- DRAWER HEADER -->
            <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-pp-100 text-pp-700 grid place-items-center shrink-0 font-bold">
                        <i class="fas fa-handshake text-pp-600"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Make Custom Offer</h3>
                        <p class="text-[11px] text-slate-500">Submitting to <strong class="text-slate-900">{{ $sellerName }}</strong></p>
                    </div>
                </div>
                <button wire:click="closeDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- DYNAMIC STEP INDICATOR WIZARD BAR -->
            <div class="px-6 py-2.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-[11px] font-bold overflow-x-auto gap-2">
                @for ($stepIndex = 1; $stepIndex <= $totalSteps; $stepIndex++)
                    @php
                        $isCurrent = ($currentStep === $stepIndex);
                        $isCompleted = ($currentStep > $stepIndex);
                        
                        if ($stepIndex <= $itemCount) {
                            $stepLabel = $itemCount === 1 ? 'Item Details' : "Item {$stepIndex}";
                        } elseif ($stepIndex === $itemCount + 1) {
                            $stepLabel = 'Shipment';
                        } else {
                            $stepLabel = 'Review';
                        }
                    @endphp

                    <button type="button" 
                            wire:click="goToStep({{ $stepIndex }})" 
                            class="flex items-center gap-1.5 shrink-0 cursor-pointer {{ $isCurrent ? 'text-pp-700 font-extrabold' : ($isCompleted ? 'text-emerald-700 font-bold' : 'text-slate-400') }}">
                        <span class="w-5 h-5 rounded-full text-[10px] grid place-items-center shrink-0 {{ $isCurrent ? 'bg-pp-600 text-white' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600') }}">
                            {{ $isCompleted ? '✓' : $stepIndex }}
                        </span>
                        <span class="whitespace-nowrap">{{ $stepLabel }}</span>
                    </button>

                    @if ($stepIndex < $totalSteps)
                        <i class="fas fa-chevron-right text-[8px] text-slate-300 shrink-0"></i>
                    @endif
                @endfor
            </div>

            <!-- DRAWER FORM BODY (SCROLLABLE) -->
            <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-bold text-xs flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- STEPS 1 TO N: ITEM SPECIFIC PRICE & WARRANTY NEGOTIATION -->
                @if ($currentStep <= $itemCount)
                    @php
                        $currIdx = $currentStep - 1;
                        $currItem = $offerItems[$currIdx] ?? null;
                    @endphp

                    @if ($currItem)
                        <div class="space-y-4">
                            
                            <!-- ITEM DETAILS CARD -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold text-pp-700 uppercase tracking-wider bg-pp-100 px-2 py-0.5 rounded-full">
                                        Item {{ $currentStep }} of {{ $itemCount }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-600">Qty: {{ $currItem['quantity'] }}</span>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 grid place-items-center text-xl shrink-0 shadow-2xs">
                                        {{ $currItem['icon'] ?? '📦' }}
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900 leading-snug">{{ $currItem['title'] }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Condition: {{ $currItem['specs'] ?? 'Standard' }}</p>
                                        <p class="text-xs font-bold text-slate-700 mt-1">Listing Price: <strong class="text-slate-950">₦{{ number_format($currItem['price']) }}</strong> each</p>
                                    </div>
                                </div>
                            </div>

                            <!-- PROPOSED PRICE INPUT FOR THIS ITEM -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-slate-900">
                                        {{ $currItem['is_negotiable'] ? 'Your Proposed Unit Price (₦)' : 'Listing Price (₦)' }}
                                        <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-slate-500">Original: <strong>₦{{ number_format($currItem['price']) }}</strong></span>
                                </div>

                                @if ($currItem['is_negotiable'])
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-3 text-sm font-bold text-slate-400">₦</span>
                                        <input type="number" 
                                               wire:model.live="offerItems.{{ $currIdx }}.proposed_price" 
                                               placeholder="e.g. {{ $currItem['price'] }}" 
                                               class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white text-base font-black text-pp-700 outline-none focus:border-pp-600" />
                                    </div>

                                    @php
                                        $numProposed = (float) str_replace(',', '', (string) ($currItem['proposed_price'] ?? 0));
                                        $itemSavings = max(0, (float) $currItem['price'] - $numProposed);
                                    @endphp

                                    @if ($itemSavings > 0)
                                        <div class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 flex items-center justify-between">
                                            <span>Requested Discount:</span>
                                            <span>-₦{{ number_format($itemSavings) }} ({{ round(($itemSavings / max(1, $currItem['price'])) * 100) }}% off)</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                        <div>
                                            <div class="text-base font-black text-slate-900">₦{{ number_format($currItem['price']) }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium">Fixed Price Listing · Price is non-negotiable</div>
                                        </div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-1 bg-slate-200 text-slate-700 rounded-md">Fixed Price</span>
                                    </div>
                                @endif
                            </div>

                            <!-- WARRANTY TERMS SELECTION FOR THIS ITEM -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-slate-900">Warranty Term for this Item</label>
                                    @if ($currItem['is_warranty_negotiable'])
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Negotiable</span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Fixed</span>
                                    @endif
                                </div>

                                @if ($currItem['is_warranty_negotiable'])
                                    <p class="text-[11px] text-slate-500 leading-snug">The seller allows negotiating warranty duration. Select or enter requested days:</p>

                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button" 
                                                wire:click="setItemWarrantyDays({{ $currIdx }}, 7)" 
                                                class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ (int)($currItem['proposed_warranty_days'] ?? 0) === 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                                            7 Days
                                        </button>
                                        <button type="button" 
                                                wire:click="setItemWarrantyDays({{ $currIdx }}, 14)" 
                                                class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ (int)($currItem['proposed_warranty_days'] ?? 0) === 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                                            14 Days
                                        </button>
                                        <button type="button" 
                                                wire:click="setItemWarrantyDays({{ $currIdx }}, 30)" 
                                                class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ (int)($currItem['proposed_warranty_days'] ?? 0) === 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                                            30 Days
                                        </button>
                                    </div>

                                    <div class="space-y-1 pt-1">
                                        <label class="text-[11px] font-bold text-slate-700">Custom Days &amp; Specifications</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="number" 
                                                   wire:model.live="offerItems.{{ $currIdx }}.proposed_warranty_days" 
                                                   placeholder="Days" 
                                                   class="p-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-800" />
                                            <input type="text" 
                                                   wire:model="offerItems.{{ $currIdx }}.proposed_warranty_terms" 
                                                   placeholder="e.g. Testing &amp; replacement terms" 
                                                   class="col-span-2 p-2 border border-slate-200 rounded-xl text-xs text-slate-800" />
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                            <i class="fas fa-shield-alt text-pp-600"></i>
                                            @if (($currItem['listing_warranty_days'] ?? 0) > 0)
                                                <span>{{ $currItem['listing_warranty_days'] }} Days Warranty</span>
                                            @else
                                                <span>No Warranty (Sold As-Is)</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1">
                                            {{ $currItem['listing_warranty_terms'] ?: 'Warranty terms are set by the seller and cannot be negotiated for this listing.' }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endif
                @endif

                <!-- STEP N + 1: SHIPMENT REQUEST & DESTINATION ADDRESS -->
                @if ($currentStep === $itemCount + 1)
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-3">
                            <span class="text-xs font-extrabold text-slate-900 block border-b border-pp-200/60 pb-2">
                                <i class="fas fa-truck text-pp-600"></i> Delivery &amp; Fulfillment Method
                            </span>

                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900">
                                    <input type="radio" wire:model.live="deliveryMode" value="pickup" class="accent-pp-600" />
                                    <span>Buyer Pickup</span>
                                </label>
                                <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                                    "I'll collect this from the seller." Collect directly from {{ $sellerName }}. No shipping fee.
                                </p>

                                @if ($allowShipping)
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900 pt-2">
                                        <input type="radio" wire:model.live="deliveryMode" value="seller_delivery" class="accent-pp-600" />
                                        <span>Seller Delivery</span>
                                    </label>
                                    <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                                        "The seller will deliver this to me." {{ $sellerName }} dispatches shipment to your destination address.
                                    </p>
                                @else
                                    <div class="mt-2 p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2">
                                        <i class="fas fa-info-circle text-amber-600 mt-0.5 shrink-0"></i>
                                        <span><strong>Local Pickup Only:</strong> Shipping is not offered for item(s) in this offer; buyer pickup is required.</span>
                                    </div>
                                @endif
                            </div>

                            @if ($deliveryMode === 'seller_delivery' && $allowShipping)
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
                                        <span><strong>{{ $sellerName }}</strong> will review your location and quote any delivery dispatch fee upon acceptance or counter.</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- STEP N + 2: SPECIAL REQUESTS, NOTES & FINAL PACKAGE REVIEW -->
                @if ($currentStep === $itemCount + 2)
                    <div class="space-y-4">
                        
                        <!-- OPTIONAL REPAIR / WORKMANSHIP SERVICE -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900">
                                <input type="checkbox" wire:model.live="requestRepair" class="accent-pp-600 rounded" />
                                <span>Request Installation or Testing Service (Special Request)</span>
                            </label>

                            @if ($requestRepair)
                                <div class="space-y-2 pt-2 border-t border-slate-200">
                                    <div>
                                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Service Type</label>
                                        <input type="text" wire:model="repairServiceType" placeholder="e.g. Component Installation &amp; Testing" class="w-full p-2 border border-slate-200 rounded-lg text-xs" />
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Service Specifications</label>
                                        <textarea wire:model="repairDetails" rows="2" placeholder="Specific technician requests or testing requirements..." class="w-full p-2 border border-slate-200 rounded-lg text-xs"></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- CUSTOM NOTES / TERMS -->
                        <div class="space-y-1">
                            <label class="text-xs font-extrabold text-slate-900 block mb-1">Proposal Notes / Instructions for Seller</label>
                            <textarea wire:model="offerNote" placeholder="Add custom terms, questions, or timing preferences for {{ $sellerName }}..." rows="3" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600"></textarea>
                        </div>

                        <!-- COMPREHENSIVE PACKAGE REVIEW CARD -->
                        <div class="p-4 rounded-2xl bg-pp-50 border border-pp-200 space-y-3 text-xs">
                            <span class="font-extrabold text-slate-900 uppercase tracking-wider block border-b border-pp-200/60 pb-2">
                                Offer Package Summary
                            </span>

                            <div class="divide-y divide-pp-100 space-y-2">
                                @foreach ($offerItems as $idx => $it)
                                    <div class="pt-2 first:pt-0 flex items-center justify-between">
                                        <div>
                                            <div class="font-extrabold text-slate-900">{{ $it['title'] }} (x{{ $it['quantity'] }})</div>
                                            <div class="text-[10px] text-slate-500">
                                                Warranty: <strong>{{ $it['proposed_warranty_days'] ?? 0 }} Days</strong>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-black text-slate-900">₦{{ number_format((float) str_replace(',', '', (string) $it['proposed_price']) * (int) $it['quantity']) }}</div>
                                            @if ((float) $it['price'] > (float) str_replace(',', '', (string) $it['proposed_price']))
                                                <div class="text-[10px] text-emerald-700 font-bold">Orig: ₦{{ number_format($it['price'] * $it['quantity']) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-2 border-t border-pp-200 flex justify-between items-baseline font-extrabold">
                                <span class="text-slate-700">Total Proposed Offer:</span>
                                <span class="text-lg text-pp-700 font-black">₦{{ number_format($proposedSubtotal) }}</span>
                            </div>

                            @if ($savings > 0)
                                <div class="text-[11px] font-bold text-emerald-800 bg-emerald-100/70 p-2 rounded-xl flex justify-between">
                                    <span>Total Requested Savings:</span>
                                    <span>-₦{{ number_format($savings) }}</span>
                                </div>
                            @endif

                            <div class="text-[11px] text-slate-600 pt-1 border-t border-pp-100 flex items-center justify-between">
                                <span>Fulfillment Method:</span>
                                <span class="font-bold text-slate-900">{{ $deliveryMode === 'seller_delivery' ? 'Seller Delivery' : 'Buyer Pickup' }}</span>
                            </div>
                        </div>

                    </div>
                @endif

            </div>

            <!-- DRAWER FOOTER NAVIGATION -->
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-3 shrink-0">
                @if ($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                        ← Back
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Next Step</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </button>
                @else
                    <button type="button" wire:click="submitPackageOffer" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-xs transition cursor-pointer flex items-center gap-2">
                        <i class="fas fa-paper-plane text-pp-400"></i>
                        <span>Submit Custom Offer</span>
                    </button>
                @endif
            </div>

        </div>
    @endif
</div>