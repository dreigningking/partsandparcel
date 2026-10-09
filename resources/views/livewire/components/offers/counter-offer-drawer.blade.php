<div x-data="{
    open: @entangle('isOpen'),
    init() {
        window.addEventListener('open-counter-offer', () => { this.open = true; });
        window.addEventListener('close-counter-offer', () => { this.open = false; });
    }
}">
    <!-- BACKDROP OVERLAY -->
    <div class="overlay fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs transition"
         :class="{ 'open': open }"
         @click="open = false; $wire.closeDrawer()"></div>

    <!-- COUNTER OFFER STEP WIZARD DRAWER -->
    <div class="drawer fixed top-0 right-0 bottom-0 z-50 w-full sm:w-[520px] bg-white shadow-2xl flex flex-col"
         :class="{ 'open': open }">

        <!-- LOADING STATE -->
        <div wire:loading.flex wire:target="loadOffer" class="absolute inset-0 bg-white/90 z-20 flex-col items-center justify-center p-8 text-center gap-3">
            <div class="w-8 h-8 rounded-full border-2 border-pp-600 border-t-transparent animate-spin"></div>
            <span class="text-xs font-semibold text-slate-500">Loading counter-offer details...</span>
        </div>

        <!-- DRAWER HEADER -->
        <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-pp-600"></i>
                    <span>{{ $isEditMode ? 'Edit Offer Proposal' : 'Construct Counter-Offer' }}</span>
                </h3>
                <p class="text-[11px] text-slate-400">
                    Step {{ $currentStep }} of {{ $totalSteps }} · 
                    @if ($offer)
                        Ref #OFF-{{ $offer->id }}
                    @endif
                </p>
            </div>
            <button type="button" @click="open = false; $wire.closeDrawer()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 font-bold text-lg grid place-items-center transition cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- STEP PROGRESS BAR -->
        <div class="w-full bg-slate-100 h-1">
            <div class="bg-pp-600 h-1 transition-all duration-300" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
        </div>

        <!-- NON-BINDING & AVAILABILITY DISCLAIMER BANNER -->
        <div class="px-6 py-2.5 bg-amber-50 border-b border-amber-200 text-amber-900 text-[11px] flex items-center gap-2 shrink-0">
            <i class="fas fa-info-circle text-amber-600 shrink-0"></i>
            <span>Offers are subject to item availability and non-binding until payment is completed.</span>
        </div>

        @if (! $offer)
            <div class="p-6 space-y-4 animate-pulse flex-1">
                <div class="h-20 bg-slate-100 rounded-2xl"></div>
                <div class="h-36 bg-slate-100 rounded-2xl"></div>
                <div class="h-28 bg-slate-100 rounded-2xl"></div>
            </div>
        @else
            <!-- DRAWER BODY (SCROLLABLE) -->
            <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">

                <!-- STEP A: CATALOG / SERVICE ITEM STEPS (1 .. itemsCount) -->
                @if ($currentStep <= $itemsCount && isset($items[$currentStep - 1]))
                    @php $item = $items[$currentStep - 1]; @endphp
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-pp-50/80 border border-pp-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase font-extrabold text-pp-700 tracking-wider">Item {{ $currentStep }} of {{ $itemsCount }}</span>
                                @if (!empty($item['is_new']))
                                    <button type="button" wire:click="removeItemFromCounter({{ $currentStep - 1 }})" class="text-rose-600 hover:text-rose-800 font-bold text-[11px] cursor-pointer">
                                        <i class="fas fa-trash-alt mr-1"></i> Remove Added Item
                                    </button>
                                @endif
                            </div>

                            @if (!empty($item['is_new']))
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Item Title / Description</label>
                                    <input type="text" wire:model="items.{{ $currentStep - 1 }}.title" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-bold text-xs text-slate-900 outline-none focus:border-pp-500" placeholder="e.g. 16GB RAM, Charging Adapter" />
                                </div>
                            @else
                                <h4 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $item['title'] }}</h4>
                            @endif

                            <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                <span>Quantity: <strong class="text-slate-800">{{ $item['quantity'] }}</strong></span>
                                <span>·</span>
                                <span>Type: <strong class="text-slate-800 uppercase">{{ $item['type'] }}</strong></span>
                            </div>
                        </div>

                        <!-- PRICE NEGOTIATION BLOCK: LAST OFFER VS YOUR OFFER -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="font-extrabold text-slate-800">Unit Price Negotiation</span>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Offer (Other Party)</span>
                                    <span class="font-extrabold text-slate-900 text-xs">₦{{ number_format($item['original_price']) }}</span>
                                </div>
                            </div>

                            @if ($item['is_negotiable'])
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Your Counter Unit Price (₦) <span class="text-rose-500">*</span></label>
                                    <input type="number" step="0.01" wire:model.live.debounce.300ms="items.{{ $currentStep - 1 }}.counter_price" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-black text-sm text-slate-900 outline-none focus:border-pp-500 transition" />
                                </div>
                            @else
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-[11px]">
                                    <i class="fas fa-lock text-slate-400 mr-1"></i> Price for this listing is fixed at <strong>₦{{ number_format($item['original_price']) }}</strong>.
                                </div>
                            @endif
                        </div>

                        <!-- WARRANTY NEGOTIATION BLOCK: LAST OFFER VS YOUR OFFER -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="font-extrabold text-slate-800">Warranty Coverage</span>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Offer (Other Party)</span>
                                    <span class="font-extrabold text-slate-900 text-xs">{{ $item['original_warranty_days'] ?? 14 }} Days</span>
                                </div>
                            </div>

                            @if ($item['is_warranty_negotiable'])
                                <div class="space-y-3">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Your Counter Warranty Period (Days)</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <button type="button" wire:click="$set('items.{{ $currentStep - 1 }}.counter_warranty_days', 7)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ ($item['counter_warranty_days'] ?? 14) == 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">7 Days</button>
                                            <button type="button" wire:click="$set('items.{{ $currentStep - 1 }}.counter_warranty_days', 14)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ ($item['counter_warranty_days'] ?? 14) == 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">14 Days</button>
                                            <button type="button" wire:click="$set('items.{{ $currentStep - 1 }}.counter_warranty_days', 30)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ ($item['counter_warranty_days'] ?? 14) == 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">30 Days</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Your Counter Warranty Scope &amp; Terms</label>
                                        <textarea wire:model="items.{{ $currentStep - 1 }}.counter_warranty_terms" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 text-xs outline-none focus:border-pp-500" placeholder="e.g. Standard replacement warranty covering factory defects..."></textarea>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-[11px]">
                                    <i class="fas fa-shield-alt text-slate-400 mr-1"></i> Warranty terms are non-negotiable for this item ({{ $item['original_warranty_days'] ?? 14 }} days).
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- STEP B: PICKUP SHIPMENT (BUYER TO SELLER) -->
                @elseif ($pickupStepNum && $currentStep === $pickupStepNum)
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-2">
                            <span class="text-[10px] uppercase font-extrabold text-amber-800 tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-truck-loading text-amber-600"></i> Pickup Shipment (Buyer to Seller)
                            </span>
                            <h4 class="text-sm font-extrabold text-slate-900 leading-snug">Device Collection from Customer</h4>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                This shipment handles picking up the faulty item or equipment directly from the buyer and dispatching it to the technician's workshop.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="font-extrabold text-slate-800">Pickup Fee Negotiation</span>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Offer (Other Party)</span>
                                    <span class="font-extrabold text-slate-900 text-xs">₦{{ number_format($originalPickupFee) }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Your Counter Pickup Fee (₦)</label>
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="counterPickupFee" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-black text-sm text-slate-900 outline-none focus:border-pp-500 transition" />
                                <span class="text-[10px] text-slate-400 mt-1 block">Set to 0 if pickup is complimentary or waived.</span>
                            </div>
                        </div>
                    </div>

                <!-- STEP C: DELIVERY SHIPMENT (SELLER TO BUYER) -->
                @elseif ($deliveryStepNum && $currentStep === $deliveryStepNum)
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-indigo-50/80 border border-indigo-200 space-y-2">
                            <span class="text-[10px] uppercase font-extrabold text-indigo-800 tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-truck text-indigo-600"></i> Delivery Shipment (Seller to Buyer)
                            </span>
                            <h4 class="text-sm font-extrabold text-slate-900 leading-snug">Return / Order Dispatch Delivery</h4>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                This shipment covers transporting the repaired equipment or purchased parts back to the customer's specified address.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="font-extrabold text-slate-800">Delivery Fee Negotiation</span>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Offer (Other Party)</span>
                                    <span class="font-extrabold text-slate-900 text-xs">₦{{ number_format($originalDeliveryFee) }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Your Counter Delivery Fee (₦)</label>
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="counterDeliveryFee" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-black text-sm text-slate-900 outline-none focus:border-pp-500 transition" />
                                <span class="text-[10px] text-slate-400 mt-1 block">Set to 0 if shipping is free or included.</span>
                            </div>
                        </div>
                    </div>

                <!-- STEP D: SPECIAL REQUESTS & FINAL REVIEW (reviewStepNum) -->
                @else
                    <div class="space-y-4">
                        <!-- ADD ANOTHER ITEM TO COUNTER OFFER (ADDS STEP) -->
                        <div class="p-4 rounded-2xl border-2 border-dashed border-pp-300 bg-pp-50/40 flex items-center justify-between">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-xs">Bundle More Items into Offer</span>
                                <span class="text-[11px] text-slate-500">Add an extra part, component, or item step to this proposal.</span>
                            </div>
                            <button type="button" wire:click="addNewItemToCounter" class="px-3 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5 shrink-0">
                                <i class="fas fa-plus"></i> Add Item Step
                            </button>
                        </div>

                        <!-- SPECIAL SERVICE REQUESTS BUILDER -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <span class="font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-tools text-pp-600"></i> Add Special Service Requests (Optional)
                            </span>
                            <p class="text-[11px] text-slate-500">
                                Include any additional installation, diagnostic, or repair labor required for this negotiation.
                            </p>

                            <div class="space-y-2 pt-1">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="text" wire:model="newServiceTitle" placeholder="Service title (e.g. BIOS Flashing)" class="p-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                                    <input type="number" wire:model="newServicePrice" placeholder="Fee (₦) e.g. 5000" class="p-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-600">
                                        <span>Warranty:</span>
                                        <select wire:model="newServiceWarrantyDays" class="p-1 rounded-lg border border-slate-200 text-xs">
                                            <option value="7">7 Days</option>
                                            <option value="14">14 Days</option>
                                            <option value="30">30 Days</option>
                                        </select>
                                    </div>
                                    <button type="button" wire:click="addSpecialRequest" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition cursor-pointer">
                                        + Add Service
                                    </button>
                                </div>
                            </div>

                            @if (!empty($specialRequests))
                                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                    @foreach($specialRequests as $idx => $sr)
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-900">{{ $sr['description'] }}</span>
                                                <span class="text-[10px] text-slate-400 block">{{ $sr['warranty_period_days'] }}-day warranty</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-slate-800">₦{{ number_format($sr['unit_price']) }}</span>
                                                <button type="button" wire:click="removeSpecialRequest({{ $idx }})" class="text-rose-500 hover:text-rose-700 text-xs cursor-pointer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- DISCOUNT & NOTES -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                            <span class="font-extrabold text-slate-800 block">Offer Discount &amp; Terms</span>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Bundle / Special Discount (₦)</label>
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="counterDiscount" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 outline-none focus:border-pp-500" placeholder="e.g. 2000" />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Terms &amp; Message for Recipient</label>
                                <textarea wire:model="counterNotes" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" placeholder="Explain your revision details, testing timeline, or conditions..."></textarea>
                            </div>
                        </div>

                        <!-- NET TOTAL SUMMARY CARD -->
                        <div class="p-4 rounded-2xl bg-pp-900 text-white space-y-2 shadow-sm">
                            <div class="flex items-center justify-between text-xs text-pp-200">
                                <span>Proposed Counter Net Total:</span>
                                <span>{{ count($items) + ($hasPickup ? 1 : 0) + ($hasDelivery ? 1 : 0) + count($specialRequests) }} line items</span>
                            </div>
                            <div class="text-2xl font-black">₦{{ number_format($counterTotal) }}</div>
                            @if ($counterDiscount > 0)
                                <div class="text-[11px] text-pp-300 font-semibold">Includes ₦{{ number_format($counterDiscount) }} discount</div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            <!-- DRAWER FOOTER NAVIGATION -->
            <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between gap-3 shrink-0">
                @if ($currentStep > 1)
                    <button type="button" wire:click="prevStep" class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-chevron-left text-[10px]"></i> Previous
                    </button>
                @else
                    <button type="button" @click="open = false; $wire.closeDrawer()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Cancel
                    </button>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep" class="py-2.5 px-5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Next Step</span> <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                @else
                    <button type="button" wire:click="submit" class="py-2.5 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-paper-plane"></i>
                        <span>{{ $isEditMode ? 'Save & Update Offer' : 'Submit Counter Offer' }}</span>
                    </button>
                @endif
            </div>
        @endif

    </div>
</div>
