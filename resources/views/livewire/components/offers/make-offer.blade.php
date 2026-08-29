<div>
    @if ($isOpen)
        <!-- BACKDROP OVERLAY -->
        <div wire:click="closeDrawer" class="fixed inset-0 z-[98] bg-slate-950/50 backdrop-blur-xs transition"></div>

        <!-- MAKE PACKAGE OFFER SIDE-DRAWER -->
        <div class="fixed top-0 right-0 bottom-0 z-[99] w-full sm:w-[480px] bg-white shadow-2xl flex flex-col transition">
            
            <!-- DRAWER HEADER -->
            <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-pp-100 text-pp-700 grid place-items-center shrink-0 font-bold">
                        <i class="fas fa-handshake text-pp-600"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Request Seller Delivery &amp; Offer</h3>
                        <p class="text-[11px] text-slate-500">Submit address &amp; proposal to <strong class="text-slate-900">{{ $sellerName }}</strong></p>
                    </div>
                </div>
                <button wire:click="closeDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- DRAWER FORM BODY (SCROLLABLE) -->
            <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">
                
                <!-- STEP 1: DELIVERY OPTION SELECTION -->
                <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-3">
                    <span class="text-xs font-extrabold text-slate-900 block border-b border-pp-200/60 pb-2">
                        <i class="fas fa-truck text-pp-600"></i> Delivery
                    </span>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900">
                            <input type="radio" wire:model.live="deliveryMode" value="pickup" class="accent-pp-600" />
                            <span>○ I'll pick it up</span>
                        </label>
                        <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                            "I'll collect this from the seller." Collect directly from {{ $sellerName }}. No shipment is necessary.
                        </p>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-extrabold text-slate-900 pt-1">
                            <input type="radio" wire:model.live="deliveryMode" value="seller_delivery" class="accent-pp-600" />
                            <span>○ Seller will deliver</span>
                        </label>
                        <p class="text-[11px] text-slate-600 pl-5 leading-relaxed">
                            "The seller will deliver this to me." {{ $sellerName }} dispatches shipment to your location.
                        </p>
                    </div>

                    @if ($deliveryMode === 'seller_delivery')
                        <div class="space-y-3 pt-3 border-t border-pp-200/60">
                            <div>
                                <label class="text-[11px] font-bold text-slate-700 block mb-1">Select Delivery Destination Address <span class="text-rose-500">*</span></label>
                                <select wire:model.live="deliveryAddressId" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                                    @foreach ($savedAddresses as $addr)
                                        <option value="{{ $addr['id'] }}">{{ $addr['label'] }}: {{ $addr['address'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 font-medium flex items-start gap-2">
                                <i class="fas fa-info-circle text-amber-600 text-xs shrink-0 mt-0.5"></i>
                                <span><strong>{{ $sellerName }}</strong> will review your address and include their delivery quote in the offer.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- STEP 2: PROPOSED PRICE INPUT -->
                <div class="space-y-1">
                    <label class="text-xs font-extrabold text-slate-900 block mb-1">Proposed Package Price for Items (₦) <span class="text-rose-500">*</span></label>
                    <input type="number" wire:model="proposedPrice" placeholder="e.g. 270000" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-sm font-black text-pp-700 outline-none focus:border-pp-600" />
                    <p class="text-[10px] text-slate-500 mt-1">Enter your proposed price for the items (seller will add their delivery quote).</p>
                </div>

                <!-- STEP 3: CUSTOM NOTES -->
                <div class="space-y-1">
                    <label class="text-xs font-extrabold text-slate-900 block mb-1">Custom Notes / Instructions</label>
                    <textarea wire:model="offerNote" placeholder="Add custom instructions or delivery notes for {{ $sellerName }}..." rows="3" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600"></textarea>
                </div>

            </div>

            <!-- DRAWER FOOTER -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3 shrink-0">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-bold">Proposed Items Price:</span>
                    <span class="text-lg font-black text-slate-950">₦{{ number_format((float) ($proposedPrice ?: 0)) }}</span>
                </div>

                <button wire:click="submitPackageOffer" class="w-full py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Offer &amp; Delivery Request to {{ strtok($sellerName, ' ') }}</span>
                </button>
            </div>

        </div>
    @endif
</div>