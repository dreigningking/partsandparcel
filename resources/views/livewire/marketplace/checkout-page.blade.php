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
            <i class="fas fa-truck text-pp-600"></i> 1. Select Fulfillment &amp; Delivery Method
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Choose how you want to receive your items from {{ $sellerName }}.</p>
        </div>

        <div class="space-y-3">
          
          <!-- OPTION 1: SELF-PICKUP / BUYER ARRANGES (DEFAULT) -->
          <div wire:click="selectDeliveryMethod('pickup')" class="flex items-start gap-4 p-4 rounded-2xl border transition cursor-pointer {{ $deliveryMethod === 'pickup' ? 'border-2 border-pp-600 bg-pp-50/40 shadow-2xs' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <input type="radio" name="delivery_method_radio" value="pickup" {{ $deliveryMethod === 'pickup' ? 'checked' : '' }} class="mt-1 accent-pp-600" />
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-extrabold text-slate-900 uppercase">OPTION 1: Self-Pickup or Buyer-Arranged Courier</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">NO DELIVERY FEE (₦0)</span>
              </div>
              <p class="text-xs text-slate-600 leading-relaxed">
                Pay for items now. Pick up in person at {{ $sellerName }}'s shop in {{ $sellerLocation }}, or send your own errand rider (Gokada, Uber, Max.ng). No address required from seller.
              </p>
            </div>
          </div>

          <!-- OPTION 2: COMMUNITY DELIVERY REQUEST (POST-CHECKOUT JOB) -->
          <div wire:click="selectDeliveryMethod('community')" class="flex items-start gap-4 p-4 rounded-2xl border transition cursor-pointer {{ $deliveryMethod === 'community' ? 'border-2 border-pp-600 bg-pp-50/40 shadow-2xs' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <input type="radio" name="delivery_method_radio" value="community" {{ $deliveryMethod === 'community' ? 'checked' : '' }} class="mt-1 accent-pp-600" />
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-extrabold text-slate-900 uppercase">OPTION 2: Post Delivery Request to Community Hub</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold">COMMUNITY QUOTE POST-CHECKOUT</span>
              </div>
              <p class="text-xs text-slate-600 leading-relaxed">
                Pay for items now. Once checkout is complete, your order will be automatically listed as a delivery job in the Community Hub where local verified dispatchers can offer delivery rates to your address.
              </p>
            </div>
          </div>

          <!-- OPTION 3: SELLER DIRECT DELIVERY (REQUIRES PRE-CHECKOUT OFFER) -->
          <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-2 opacity-90">
            <div class="flex items-center justify-between gap-2 flex-wrap">
              <span class="text-xs font-extrabold text-slate-800 uppercase">OPTION 3: Seller Direct Delivery</span>
              <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold">REQUIRES PRE-CHECKOUT OFFER</span>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
              Want {{ $sellerName }} to deliver directly to your address? You must request a seller delivery offer before paying. Return to cart and click "Request Seller Delivery &amp; Offer" to submit your address.
            </p>
            <div class="pt-1">
              <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-[11px] hover:bg-slate-800 transition">
                <i class="fas fa-handshake text-pp-400"></i> Return to Cart &amp; Request Seller Delivery
              </a>
            </div>
          </div>

          <!-- OPTION 4: SYSTEM INTEGRATED COURIER API (DISABLED / MVP) -->
          <div wire:click="selectDeliveryMethod('integrated')" class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-1.5 opacity-60 cursor-not-allowed">
            <div class="flex items-center justify-between gap-2 flex-wrap">
              <span class="text-xs font-extrabold text-slate-700 uppercase">OPTION 4: Platform Integrated Express Courier API</span>
              <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold">COMING SOON (MVP)</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
              Automated courier dispatch API integration is currently under development. Please select Self-Pickup or Community Delivery for now.
            </p>
          </div>

        </div>
      </div>

      <!-- STEP 2: SHIPPING ADDRESS SELECTOR (DISPLAYED WHEN DELIVERY METHOD IS NOT SELF-PICKUP) -->
      @if ($deliveryMethod !== 'pickup')
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-map-marker-alt text-pp-600"></i> 2. Select Destination Address for Delivery
            </h3>
            <a href="#" class="text-xs font-bold text-pp-600 hover:underline">+ Add New Address</a>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <!-- SAVED ADDRESS 1 -->
            <label class="p-4 rounded-2xl border-2 border-pp-600 bg-pp-50/50 cursor-pointer block">
              <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-pp-700 uppercase">Home Address (Default)</span>
                <input type="radio" name="address" checked class="accent-pp-600" />
              </div>
              <p class="text-xs font-bold text-slate-900 mt-2">Emmanuel Reign</p>
              <p class="text-xs text-slate-600 mt-1">Block 4B, Lekki Phase 1, Victoria Island, Lagos</p>
              <p class="text-[11px] text-slate-500 mt-1"><i class="fas fa-phone text-slate-400"></i> +234 803 123 4567</p>
            </label>

            <!-- SAVED ADDRESS 2 -->
            <label class="p-4 rounded-2xl border border-slate-200 bg-white cursor-pointer block hover:border-slate-300">
              <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-500 uppercase">Workshop Address</span>
                <input type="radio" name="address" class="accent-pp-600" />
              </div>
              <p class="text-xs font-bold text-slate-900 mt-2">Reign Tech Workshop</p>
              <p class="text-xs text-slate-600 mt-1">No 14 Otigba Street, Computer Village, Ikeja, Lagos</p>
              <p class="text-[11px] text-slate-500 mt-1"><i class="fas fa-phone text-slate-400"></i> +234 812 987 6543</p>
            </label>
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
              @if ($deliveryMethod === 'pickup') Self-Pickup / Errand @else Community Delivery Job @endif
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