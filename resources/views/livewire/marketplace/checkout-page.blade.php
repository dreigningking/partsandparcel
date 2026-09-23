<main class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950">Checkout</h1>
      <p class="text-xs text-slate-500 mt-1">Purchasing from: <strong class="text-slate-900">{{ $sellerName }}</strong> ({{ $sellerLocation }})</p>
    </div>
    <a href="{{ route('cart') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
      <i class="fas fa-arrow-left text-[10px]"></i> Return to Cart
    </a>
  </div>

  @if (session()->has('warning'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-600 text-base"></i>
        <span>{{ session('warning') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <div class="grid lg:grid-cols-12 gap-8">
    
    <!-- LEFT CHECKOUT STEPS -->
    <div class="lg:col-span-8 space-y-6">
      
      <!-- STEP 1: FULFILLMENT & DELIVERY METHOD (FIRST AT TOP) -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <div class="border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-truck text-pp-600"></i> Delivery
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Select how you want to receive your item(s) from {{ $sellerName }}.</p>
        </div>

        <div class="space-y-3">
          
          <!-- OPTION 1: BUYER PICKUP -->
          <div wire:click="selectDeliveryMethod('pickup')" class="flex items-start gap-4 p-4 rounded-2xl border transition cursor-pointer {{ $deliveryMethod === 'pickup' ? 'border-2 border-pp-600 bg-pp-50/40 shadow-2xs' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <input type="radio" name="delivery_method_radio" value="pickup" {{ $deliveryMethod === 'pickup' ? 'checked' : '' }} class="mt-1 accent-pp-600" />
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-extrabold text-slate-900 uppercase">○ I'll pick it up</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">NO SHIPMENT NECESSARY</span>
              </div>
              <p class="text-xs text-slate-600 leading-relaxed">
                "I'll collect this from the seller." Collect directly from {{ $sellerName }} at {{ $sellerLocation }}. No shipment is created or necessary.
              </p>
            </div>
          </div>

          <!-- OPTION 2: SELLER DELIVERY -->
          <div wire:click="selectDeliveryMethod('seller_delivery')" class="flex items-start gap-4 p-4 rounded-2xl border transition cursor-pointer {{ $deliveryMethod === 'seller_delivery' ? 'border-2 border-pp-600 bg-pp-50/40 shadow-2xs' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <input type="radio" name="delivery_method_radio" value="seller_delivery" {{ $deliveryMethod === 'seller_delivery' ? 'checked' : '' }} class="mt-1 accent-pp-600" />
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-extrabold text-slate-900 uppercase">○ Seller will deliver</span>
                <span class="px-2 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[10px] font-extrabold">SELLER IS DELIVERY PARTY</span>
              </div>
              <p class="text-xs text-slate-600 leading-relaxed">
                "The seller will deliver this to me." {{ $sellerName }} delivers to your location. A shipment is created with the seller as the delivery party.
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- STEP 2: BUYER'S DELIVERY LOCATION (COLLECTED WHEN SELLER DELIVERY IS SELECTED) -->
      @if ($deliveryMethod === 'seller_delivery')
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-map-marker-alt text-pp-600"></i> Select Delivery Location
            </h3>
            <button type="button" wire:click="$toggle('showNewAddressModal')" class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">
              {{ $showNewAddressModal ? 'Cancel' : '+ Add New Address' }}
            </button>
          </div>

          @if ($showNewAddressModal)
            <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-3">
              <span class="font-extrabold text-xs text-slate-900 block">Add New Delivery Address</span>
              <div class="grid sm:grid-cols-2 gap-3 text-xs">
                <div>
                  <label class="font-bold text-slate-700 block mb-1">Label</label>
                  <input type="text" wire:model="newAddressLabel" placeholder="e.g. Home, Office, Workshop" class="w-full p-2 border border-slate-200 rounded-xl bg-white text-xs" />
                </div>
                <div>
                  <label class="font-bold text-slate-700 block mb-1">Contact Phone</label>
                  <input type="text" wire:model="newPhone" placeholder="+234 800 000 0000" class="w-full p-2 border border-slate-200 rounded-xl bg-white text-xs" />
                </div>
              </div>
              <div class="space-y-1 text-xs">
                <label class="font-bold text-slate-700 block mb-1">Address Line</label>
                <input type="text" wire:model="newAddressLine" placeholder="Street number, building, street name..." class="w-full p-2 border border-slate-200 rounded-xl bg-white text-xs" />
              </div>
              <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                  <label class="font-bold text-slate-700 block mb-1">City</label>
                  <input type="text" wire:model="newCity" class="w-full p-2 border border-slate-200 rounded-xl bg-white text-xs" />
                </div>
                <div>
                  <label class="font-bold text-slate-700 block mb-1">State</label>
                  <input type="text" wire:model="newState" class="w-full p-2 border border-slate-200 rounded-xl bg-white text-xs" />
                </div>
              </div>
              <button type="button" wire:click="saveNewAddress" class="py-2 px-4 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition cursor-pointer">
                Save &amp; Select Address
              </button>
            </div>
          @endif

          <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($savedAddresses as $addr)
              @php
                $isChosen = ($selectedAddressId == $addr['id']);
              @endphp
              <div wire:click="$set('selectedAddressId', {{ $addr['id'] }})" class="p-4 rounded-2xl cursor-pointer transition {{ $isChosen ? 'border-2 border-pp-600 bg-pp-50/50 shadow-2xs' : 'border border-slate-200 bg-white hover:border-slate-300' }}">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-extrabold {{ $isChosen ? 'text-pp-700' : 'text-slate-700' }} uppercase">{{ $addr['name'] ?? $addr['label'] ?? 'Address' }}</span>
                  <input type="radio" name="address_radio" @checked($isChosen) class="accent-pp-600" />
                </div>
                <p class="text-xs font-bold text-slate-900 mt-2">{{ $addr['address_line_1'] ?? $addr['address'] ?? '' }}</p>
                <p class="text-xs text-slate-600 mt-0.5">{{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}</p>
                @if (!empty($addr['phone']))
                  <p class="text-[11px] text-slate-500 mt-1"><i class="fas fa-phone text-slate-400"></i> {{ $addr['phone'] }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- STEP 3: PAYMENT METHOD (REDESIGNED: ESCROW VS DIRECT SELLER) -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-credit-card text-pp-600"></i> {{ $deliveryMethod === 'pickup' ? '2' : '3' }}. Select Payment Option
        </h3>

        <div class="space-y-4">
          
          <!-- OPTION A: PAY VIA PARTS & PARCEL (ESCROW PROTECTED) -->
          <div wire:click="setPaymentMethod('escrow')" class="block p-4.5 rounded-2xl border transition cursor-pointer {{ $paymentMethod === 'escrow' ? 'border-2 border-pp-600 bg-pp-50/40 shadow-2xs' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <input type="radio" name="payment_method_radio" value="escrow" {{ $paymentMethod === 'escrow' ? 'checked' : '' }} class="accent-pp-600" />
                <span class="text-xs font-extrabold text-slate-900 uppercase flex items-center gap-2">
                  <i class="fas fa-shield-alt text-pp-600"></i> Pay via Parts &amp; Parcel (Escrow Protected)
                </span>
              </div>
              <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[10px] font-extrabold">+ ₦{{ number_format($escrowFee) }} ESCROW FEE</span>
            </div>

            @if ($paymentMethod === 'escrow')
              <div class="mt-3 pt-3 border-t border-slate-200/60 text-xs text-slate-700 space-y-2">
                <p class="font-bold text-pp-800 flex items-center gap-1.5">
                  <i class="fas fa-check-circle text-pp-600"></i> Why Pay via Parts &amp; Parcel Escrow?
                </p>
                <ul class="list-disc pl-5 space-y-1 text-[11px] text-slate-600">
                  <li><strong>100% Money-Back Guarantee:</strong> Funds remain securely locked in escrow until you inspect and confirm item quality.</li>
                  <li><strong>Dispute Protection:</strong> Full platform mediation &amp; immediate refund if items are damaged or misrepresented.</li>
                  <li><strong>Instant Automated Receipt:</strong> Verified transaction record generated for your order history.</li>
                </ul>
              </div>
            @endif
          </div>

          <!-- OPTION B: PAY DIRECTLY TO SELLER (OFF-PLATFORM DIRECT TRANSFER) -->
          <div wire:click="setPaymentMethod('direct_seller')" class="block p-4.5 rounded-2xl border transition cursor-pointer {{ $paymentMethod === 'direct_seller' ? 'border-2 border-amber-600 bg-amber-50/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <input type="radio" wire:model.live="paymentMethod" value="direct_seller" class="accent-amber-600" />
                <span class="text-xs font-extrabold text-slate-900 uppercase flex items-center gap-2">
                  <i class="fas fa-university text-slate-600"></i> Pay Directly to Seller's Bank Account
                </span>
              </div>
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold">DIRECT TRANSFER</span>
            </div>

            @if ($paymentMethod === 'direct_seller')
              <div class="mt-3 pt-3 border-t border-amber-200/60 space-y-3">
                <!-- SELLER BANK ACCOUNT DETAILS -->
                <div class="p-4 rounded-xl bg-white border border-amber-300 text-xs space-y-1.5 shadow-2xs">
                  <span class="text-[10px] font-extrabold uppercase text-amber-800 tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-building-columns text-amber-600"></i> Seller Bank Account Details ({{ $sellerName }})
                  </span>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-slate-900">
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                      <span class="text-[10px] text-slate-500 font-bold block">BANK NAME</span>
                      <strong class="text-xs text-slate-950 font-black">{{ $sellerBank['bank_name'] }}</strong>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                      <span class="text-[10px] text-slate-500 font-bold block">ACCOUNT NAME</span>
                      <strong class="text-xs text-slate-950 font-black">{{ $sellerBank['account_name'] }}</strong>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                      <span class="text-[10px] text-slate-500 font-bold block">ACCOUNT NUMBER</span>
                      <strong class="text-sm text-pp-700 font-black tracking-wider">{{ $sellerBank['account_number'] }}</strong>
                    </div>
                  </div>
                </div>

                <!-- HIGH RISK WARNING NOTICE -->
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 space-y-1">
                  <b class="font-extrabold text-rose-700 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-sm"></i> HIGH RISK WARNING TO BUYER:
                  </b>
                  <p class="text-[11px] text-rose-800 leading-relaxed">
                    Direct bank transfers to the seller are <strong>NOT protected by Parts &amp; Parcel Escrow</strong>. Parts &amp; Parcel cannot issue refunds or resolve disputes for direct seller transfers if items are damaged, incorrect, or undelivered.
                  </p>
                </div>
              </div>
            @endif
          </div>

        </div>
      </div>

    </div>

    <!-- RIGHT ORDER SUMMARY -->
    <div class="lg:col-span-4">
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft sticky top-20">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fas fa-receipt text-pp-600"></i> Order Summary
        </h3>
        
        <div class="space-y-3 text-xs text-slate-600 border-b border-slate-100 pb-4">
          @foreach ($cartItems as $item)
            <div class="flex justify-between items-start">
              <span>{{ $item['quantity'] }}x {{ $item['title'] }}</span>
              <span class="font-bold text-slate-900">₦{{ number_format($item['price'] * $item['quantity']) }}</span>
            </div>
          @endforeach

          <div class="flex justify-between pt-2 border-t border-slate-100">
            <span>Fulfillment Method</span>
            <span class="font-bold text-slate-900">
              @if ($deliveryMethod === 'pickup') Buyer Pickup @else Seller Delivery @endif
            </span>
          </div>

          <div class="flex justify-between">
            <span>Delivery Fee</span>
            <span class="font-bold text-slate-900">₦{{ number_format($deliveryFee) }}</span>
          </div>

          <div class="flex justify-between">
            <span>Payment Mode</span>
            <span class="font-bold text-slate-900">
              @if ($paymentMethod === 'escrow') Escrow Protected @else Direct Transfer @endif
            </span>
          </div>

          <div class="flex justify-between">
            <span>Escrow Protection Fee</span>
            <span class="font-bold {{ $paymentMethod === 'escrow' ? 'text-pp-700' : 'text-slate-400' }}">
              @if ($paymentMethod === 'escrow') ₦{{ number_format($activeEscrowFee) }} @else NO ESCROW (Direct) @endif
            </span>
          </div>
        </div>

        <div class="flex items-baseline justify-between">
          <span class="text-xs font-bold text-slate-500">Total Payable:</span>
          <span class="text-2xl font-extrabold text-slate-950">₦{{ number_format($totalPayable) }}</span>
        </div>

        <!-- DYNAMIC ACTION BUTTON BASED ON PAYMENT METHOD -->
        @if ($paymentMethod === 'escrow')
          <button wire:click="placeOrder" class="w-full py-3.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center block shadow-xs transition cursor-pointer">
            Pay ₦{{ number_format($totalPayable) }} &amp; Place Order <i class="fas fa-arrow-right text-[10px] ml-1"></i>
          </button>
        @else
          <button wire:click="placeOrder" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs text-center block shadow-xs transition cursor-pointer flex items-center justify-center gap-2">
            <i class="fas fa-check-circle text-emerald-400"></i>
            <span>Mark Payment Completed</span>
          </button>
        @endif

        @if ($paymentMethod === 'escrow')
          <div class="p-3 rounded-xl bg-pp-50 border border-pp-100 text-[11px] text-pp-800 space-y-1">
            <b class="font-bold text-pp-700 flex items-center gap-1.5">
              <i class="fas fa-shield-alt text-pp-600"></i> Escrow Protection Guaranteed
            </b>
            <p class="text-slate-600 leading-relaxed">
              Your funds are held safely in escrow until you inspect and verify the delivered items from {{ $sellerName }}.
            </p>
          </div>
        @else
          <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-800 space-y-1">
            <b class="font-bold text-rose-700 flex items-center gap-1.5">
              <i class="fas fa-exclamation-triangle text-rose-600"></i> Direct Payment Notice
            </b>
            <p class="text-rose-900 leading-relaxed">
              You are paying {{ $sellerName }} directly outside Parts &amp; Parcel Escrow. Ensure you keep transfer receipts.
            </p>
          </div>
        @endif
      </div>
    </div>

  </div>

</main>