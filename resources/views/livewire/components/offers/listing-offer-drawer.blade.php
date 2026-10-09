<div x-data="{
    open: @entangle('isOpen'),
    init() {
        window.addEventListener('open-listing-offer', () => { this.open = true; });
        window.addEventListener('open-make-offer', () => { this.open = true; });
        window.addEventListener('close-listing-offer', () => { this.open = false; });
    }
}">
    <!-- BACKDROP OVERLAY -->
    <div class="overlay fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs transition"
         :class="{ 'open': open }"
         @click="open = false; $wire.closeDrawer()"></div>

    <!-- 3-STEP LISTING OFFER DRAWER -->
    <div class="drawer fixed top-0 right-0 bottom-0 z-50 w-full sm:w-[500px] bg-white shadow-2xl flex flex-col"
         :class="{ 'open': open }">

      <!-- DRAWER HEADER -->
      <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
        <div>
          <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
            <i class="fas fa-handshake text-pp-600"></i>
            <span>Make an Offer</span>
          </h3>
          <p class="text-[11px] text-slate-400">
            Step {{ $currentStep }} of 3 ·
            <strong class="text-slate-700">{{ $currentStep === 1 ? 'Configure Item' : ($currentStep === 2 ? 'Fulfillment & Address' : 'Review & Submit') }}</strong>
          </p>
        </div>
        <button type="button" @click="open = false; $wire.closeDrawer()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 font-bold text-lg grid place-items-center transition cursor-pointer">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- STEP PROGRESS BAR -->
      <div class="w-full bg-slate-100 h-1">
        <div class="bg-pp-600 h-1 transition-all duration-300" style="width: {{ ($currentStep / 3) * 100 }}%"></div>
      </div>

      <!-- NON-BINDING & AVAILABILITY DISCLAIMER BANNER -->
      <div class="px-6 py-2.5 bg-amber-50 border-b border-amber-200 text-amber-900 text-[11px] flex items-center gap-2 shrink-0">
        <i class="fas fa-info-circle text-amber-600 shrink-0"></i>
        <span>Offers are subject to item availability and non-binding until payment is completed.</span>
      </div>

      <!-- DRAWER BODY (SCROLLABLE) -->
      <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">
        @if (! $listingId)
          <!-- SKELETON PLACEHOLDER WHILE LOADING -->
          <div class="space-y-4 animate-pulse">
            <div class="h-20 bg-slate-100 rounded-2xl"></div>
            <div class="h-36 bg-slate-100 rounded-2xl"></div>
            <div class="h-28 bg-slate-100 rounded-2xl"></div>
          </div>
        @else
          @if ($currentStep === 1)
          <div class="space-y-4">
            <!-- LISTING HEADER CARD -->
            <div class="p-4 rounded-2xl bg-pp-50/80 border border-pp-100 flex items-center gap-3">
              @if ($listingImage)
                <img src="{{ $listingImage }}" alt="{{ $listingTitle }}" class="w-14 h-14 rounded-xl object-cover shrink-0 border border-pp-200">
              @else
                <div class="w-14 h-14 rounded-xl bg-white border border-pp-200 grid place-items-center text-2xl shrink-0">
                  {{ $listingIcon }}
                </div>
              @endif
              <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase font-extrabold text-pp-700 tracking-wider block">Seller: {{ $sellerName }}</span>
                <h4 class="text-sm font-extrabold text-slate-900 truncate leading-snug">{{ $listingTitle }}</h4>
                <div class="text-[11px] text-slate-500 font-medium flex items-center gap-2 mt-0.5">
                  <span>{{ $listingSpecs }}</span>
                  <span>·</span>
                  <span class="text-slate-700 font-bold">Asking: ₦{{ number_format($askingPrice) }}</span>
                </div>
              </div>
            </div>

            <!-- PROPOSED UNIT PRICE BLOCK -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-extrabold text-slate-800">Unit Price Proposal</span>
                <span class="text-slate-400 text-[11px]">List Price: ₦{{ number_format($askingPrice) }}</span>
              </div>

              @if ($isNegotiable)
                <div>
                  <label class="block font-bold text-slate-700 mb-1">Your Proposed Price per Unit (₦) <span class="text-rose-500">*</span></label>
                  <input type="number" step="0.01" wire:model.live.debounce.300ms="proposedPrice" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-black text-sm text-slate-900 outline-none focus:border-pp-500 transition" />
                  @error('proposedPrice') <span class="text-rose-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                </div>
              @else
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-[11px]">
                  <i class="fas fa-lock text-slate-400 mr-1"></i> Price for this listing is fixed at <strong>₦{{ number_format($askingPrice) }}</strong>.
                </div>
              @endif

              <!-- QUANTITY SELECTOR -->
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <div>
                  <span class="font-bold text-slate-800 block">Offer Quantity</span>
                  <span class="text-[10px] text-slate-400">Available: {{ $maxQuantity }} units</span>
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" wire:click="$set('quantity', {{ max(1, $quantity - 1) }})" class="w-8 h-8 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 font-black text-slate-700 grid place-items-center cursor-pointer">-</button>
                  <span class="font-black text-sm text-slate-900 w-8 text-center">{{ $quantity }}</span>
                  <button type="button" wire:click="$set('quantity', {{ min($maxQuantity, $quantity + 1) }})" class="w-8 h-8 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 font-black text-slate-700 grid place-items-center cursor-pointer">+</button>
                </div>
              </div>
            </div>

            <!-- WARRANTY NEGOTIATION BLOCK -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-extrabold text-slate-800">Warranty Coverage</span>
                <span class="text-slate-400 text-[11px]">Seller Offers: {{ $listingWarrantyDays ?? 14 }} Days</span>
              </div>

              @if ($isWarrantyNegotiable)
                <div class="space-y-3">
                  <div>
                    <label class="block font-bold text-slate-700 mb-1">Proposed Warranty Period</label>
                    <div class="grid grid-cols-3 gap-2">
                      <button type="button" wire:click="setWarrantyDays(7)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ $proposedWarrantyDays == 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">7 Days</button>
                      <button type="button" wire:click="setWarrantyDays(14)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ $proposedWarrantyDays == 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">14 Days</button>
                      <button type="button" wire:click="setWarrantyDays(30)" class="py-2 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ $proposedWarrantyDays == 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700 font-black' : 'border-slate-200 text-slate-700' }}">30 Days</button>
                    </div>
                  </div>
                  <div>
                    <label class="block font-bold text-slate-700 mb-1">Warranty Scope &amp; Terms</label>
                    <textarea wire:model="proposedWarrantyTerms" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 text-xs outline-none focus:border-pp-500" placeholder="e.g. Standard inspection &amp; defect replacement warranty..."></textarea>
                  </div>
                </div>
              @else
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-[11px]">
                  <i class="fas fa-shield-alt text-slate-400 mr-1"></i> Warranty terms are fixed by seller ({{ $listingWarrantyDays ?? 14 }} days).
                </div>
              @endif
            </div>
          </div>

        <!-- STEP 2: SHIPMENT & FULFILLMENT -->
        @elseif ($currentStep === 2)
          <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <span class="text-[10px] uppercase font-extrabold text-slate-600 tracking-wider flex items-center gap-1.5">
                <i class="fas fa-truck text-pp-600"></i> Fulfillment Method
              </span>
              <p class="text-[11px] text-slate-600 leading-relaxed">
                Choose whether you will pick up the item from the seller or request seller dispatch delivery.
              </p>
            </div>

            <!-- DELIVERY MODE RADIO SELECTION -->
            <div class="grid grid-cols-2 gap-3">
              <button type="button" wire:click="$set('deliveryMode', 'pickup')" class="p-4 rounded-2xl border text-left transition cursor-pointer {{ $deliveryMode === 'pickup' ? 'border-2 border-pp-600 bg-pp-50/50 shadow-2xs' : 'border-slate-200 hover:border-slate-300' }}">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xl">🏪</span>
                  <span class="text-[10px] font-black uppercase {{ $deliveryMode === 'pickup' ? 'text-pp-700' : 'text-slate-400' }}">Free</span>
                </div>
                <div class="font-black text-slate-900 text-xs">Buyer Pickup</div>
                <div class="text-[10px] text-slate-500 mt-0.5">Collect at seller location ({{ $sellerLocation }})</div>
              </button>

              @if ($allowShipping)
                <button type="button" wire:click="$set('deliveryMode', 'seller_delivery')" class="p-4 rounded-2xl border text-left transition cursor-pointer {{ $deliveryMode === 'seller_delivery' ? 'border-2 border-pp-600 bg-pp-50/50 shadow-2xs' : 'border-slate-200 hover:border-slate-300' }}">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xl">🚚</span>
                    <span class="text-[10px] font-black uppercase {{ $deliveryMode === 'seller_delivery' ? 'text-pp-700' : 'text-slate-400' }}">Dispatched</span>
                  </div>
                  <div class="font-black text-slate-900 text-xs">Seller Delivery</div>
                  <div class="text-[10px] text-slate-500 mt-0.5">Dispatched to your delivery address</div>
                </button>
              @else
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50 text-left opacity-60">
                  <div class="text-xl mb-2">🚚</div>
                  <div class="font-bold text-slate-500 text-xs">Delivery Unavailable</div>
                  <div class="text-[10px] text-slate-400 mt-0.5">Pickup only for this listing</div>
                </div>
              @endif
            </div>

            <!-- DELIVERY ADDRESS SELECTOR (IF SELLER DELIVERY) -->
            @if ($deliveryMode === 'seller_delivery')
              <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                  <span class="font-extrabold text-slate-800">Your Delivery Address</span>
                  @if (!empty($savedAddresses) && !$showNewAddressForm)
                    <button type="button" wire:click="$set('showNewAddressForm', true)" class="text-pp-600 hover:underline font-bold text-[11px] cursor-pointer">+ New Address</button>
                  @endif
                </div>

                @if (!empty($savedAddresses) && !$showNewAddressForm)
                  <div class="space-y-2">
                    @foreach ($savedAddresses as $addr)
                      <label class="p-3 rounded-xl border flex items-start gap-2.5 cursor-pointer transition {{ $deliveryAddressId == $addr['id'] ? 'border-pp-500 bg-pp-50/30' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" wire:model.live="deliveryAddressId" value="{{ $addr['id'] }}" class="mt-0.5 text-pp-600 focus:ring-pp-500" />
                        <div class="min-w-0 flex-1">
                          <span class="font-bold text-slate-900 block text-xs">{{ $addr['label'] }}</span>
                          <span class="text-[11px] text-slate-500 block truncate">{{ $addr['address'] }}, {{ $addr['city'] }}, {{ $addr['state'] }}</span>
                        </div>
                      </label>
                    @endforeach
                  </div>
                @else
                  <!-- NEW ADDRESS FORM -->
                  <div class="space-y-2.5 pt-1">
                    <div>
                      <label class="block font-bold text-slate-700 text-[11px] mb-1">Address Label</label>
                      <input type="text" wire:model="newAddressLabel" placeholder="e.g. Workshop, Home, Office" class="w-full p-2 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                    </div>
                    <div>
                      <label class="block font-bold text-slate-700 text-[11px] mb-1">Street Address <span class="text-rose-500">*</span></label>
                      <input type="text" wire:model="newAddressLine" placeholder="e.g. 15 Otigba Street" class="w-full p-2 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                      @error('newAddressLine') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                      <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">City</label>
                        <input type="text" wire:model="newCity" placeholder="e.g. Ikeja" class="w-full p-2 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                      </div>
                      <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">State</label>
                        <input type="text" wire:model="newState" placeholder="e.g. Lagos" class="w-full p-2 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500" />
                      </div>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                      @if (!empty($savedAddresses))
                        <button type="button" wire:click="$set('showNewAddressForm', false)" class="text-slate-500 hover:text-slate-700 text-[11px] font-bold">Cancel</button>
                      @else
                        <span></span>
                      @endif
                      <button type="button" wire:click="saveNewAddress" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition cursor-pointer">Save Address</button>
                    </div>
                  </div>
                @endif
              </div>
            @endif
          </div>

        <!-- STEP 3: REVIEW & SUBMIT -->
        @else
          <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-2xs">
              <span class="text-[10px] uppercase font-extrabold text-pp-700 tracking-wider block">Proposal Summary</span>

              <div class="space-y-2 border-b border-slate-100 pb-3">
                <div class="flex items-center justify-between text-xs">
                  <span class="text-slate-600 font-medium">{{ $listingTitle }} (x{{ $quantity }})</span>
                  <span class="font-extrabold text-slate-900">₦{{ number_format($proposedTotal) }}</span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                  <span>List Price Total</span>
                  <span class="line-through">₦{{ number_format($originalTotal) }}</span>
                </div>
                @if ($savings > 0)
                  <div class="flex items-center justify-between text-[11px] text-emerald-600 font-extrabold">
                    <span>Discount Requested</span>
                    <span>-₦{{ number_format($savings) }}</span>
                  </div>
                @endif
              </div>

              <!-- KEY TERMS CHIPS -->
              <div class="grid grid-cols-2 gap-2 text-slate-600 text-xs pt-1">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                  <span class="text-[10px] text-slate-400 font-bold uppercase block">Warranty</span>
                  <span class="font-bold text-slate-800">{{ $proposedWarrantyDays }} Days</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                  <span class="text-[10px] text-slate-400 font-bold uppercase block">Fulfillment</span>
                  <span class="font-bold text-slate-800">{{ $deliveryMode === 'seller_delivery' ? 'Seller Delivery' : 'Buyer Pickup' }}</span>
                </div>
              </div>

              <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="font-extrabold text-slate-900 text-sm">Total Offer Amount</span>
                <span class="text-xl font-black text-pp-700">₦{{ number_format($proposedTotal) }}</span>
              </div>
            </div>

            <!-- OPTIONAL NOTE TO SELLER -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
              <label class="block font-bold text-slate-800 text-xs">Personal Note / Message to Seller (Optional)</label>
              <textarea wire:model="offerNote" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 text-xs outline-none focus:border-pp-500" placeholder="e.g. Can pick up today with instant payment."></textarea>
            </div>
          </div>
        @endif
        @endif
      </div>

      <!-- DRAWER FOOTER ACTIONS -->
      @if ($listingId)
        <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between gap-3 shrink-0">
          @if ($currentStep > 1)
            <button type="button" wire:click="prevStep" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
              <i class="fas fa-arrow-left mr-1"></i> Back
            </button>
          @else
            <div></div>
          @endif

          @if ($currentStep < 3)
            <button type="button" wire:click="nextStep" class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
              <span>Continue</span>
              <i class="fas fa-arrow-right"></i>
            </button>
          @else
            <button type="button" wire:click="submitOffer" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5">
              <i class="fas fa-paper-plane"></i>
              <span>Send Offer Proposal</span>
            </button>
          @endif
        </div>
      @endif

    </div>
</div>
