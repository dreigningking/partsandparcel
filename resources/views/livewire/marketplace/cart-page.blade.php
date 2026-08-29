<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

  <!-- PAGE HEADER & BREADCRUMB -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
    <div>
      <div class="flex items-center gap-2 mb-1 text-xs text-slate-500 font-medium">
        <a href="/" class="hover:text-pp-600 transition">Marketplace</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Shopping Cart</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3">
        <span>Cart Items Grouped by Store</span>
        <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-xs font-extrabold">{{ count($cartGrouped) }} Seller Stores</span>
      </h1>
    </div>

    <a href="/" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
      <i class="fas fa-arrow-left text-[10px]"></i> Continue Shopping
    </a>
  </div>

  <!-- FULFILLMENT PATHS GUIDE BANNER -->
  <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-3">
    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-route text-pp-600"></i> Delivery &amp; Fulfillment Options Overview
      </h3>
      <span class="text-[10px] font-bold text-pp-700 bg-pp-50 px-2 py-0.5 rounded-full border border-pp-100">
        Choose your path per seller
      </span>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
      <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
        <div class="w-8 h-8 rounded-xl bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-xs shrink-0">1</div>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Buyer Pickup</h4>
          <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">"I'll collect this from the seller." Pick up directly at seller location. No shipment necessary.</p>
        </div>
      </div>

      <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-pp-50/70 border border-pp-200/80">
        <div class="w-8 h-8 rounded-xl bg-pp-600 text-white font-extrabold grid place-items-center text-xs shrink-0">
          <i class="fas fa-truck text-xs"></i>
        </div>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Seller Delivery</h4>
          <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">"The seller will deliver this to me." Seller dispatches shipment directly to your address.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-12 gap-8">
    
    <!-- LEFT: SELLER GROUPED CARTS -->
    <div class="lg:col-span-8 space-y-6">
      
      @foreach ($cartGrouped as $sellerCart)
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-soft">
          
          <!-- SELLER STORE HEADER -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-pp-100 text-pp-700 grid place-items-center font-extrabold text-xs">
                {{ strtoupper(substr($sellerCart['name'], 0, 2)) }}
              </div>
              <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                  <span>{{ $sellerCart['name'] }}</span>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100">✓ Verified Store</span>
                </h3>
                <p class="text-xs text-slate-500 flex items-center gap-1">
                  <i class="fas fa-map-marker-alt text-slate-400 text-[10px]"></i> {{ $sellerCart['location'] }}
                </p>
              </div>
            </div>

            <span class="text-xs font-extrabold text-pp-700 bg-pp-50 px-3 py-1 rounded-full border border-pp-100 self-start sm:self-auto">
              {{ count($sellerCart['items']) }} Item(s) in Cart
            </span>
          </div>

          <!-- ITEMS IN THIS SELLER'S CART -->
          <div class="divide-y divide-slate-100">
            @foreach ($sellerCart['items'] as $item)
              <div class="py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-xl bg-slate-100 grid place-items-center text-xl shrink-0">
                    💻
                  </div>
                  <div>
                    <h4 class="text-xs font-extrabold text-slate-900">{{ $item['title'] }}</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Unit Price: ₦{{ number_format($item['price']) }}</p>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <!-- QUANTITY CONTROLS -->
                  <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl border border-slate-200">
                    <button wire:click="updateQuantity('{{ $sellerCart['id'] }}', '{{ $item['id'] }}', {{ $item['quantity'] - 1 }})" class="w-6 h-6 rounded-lg bg-white shadow-2xs font-bold text-xs text-slate-700 hover:bg-slate-100 grid place-items-center cursor-pointer">-</button>
                    <span class="w-6 text-center font-extrabold text-xs text-slate-900">{{ $item['quantity'] }}</span>
                    <button wire:click="updateQuantity('{{ $sellerCart['id'] }}', '{{ $item['id'] }}', {{ $item['quantity'] + 1 }})" class="w-6 h-6 rounded-lg bg-white shadow-2xs font-bold text-xs text-slate-700 hover:bg-slate-100 grid place-items-center cursor-pointer">+</button>
                  </div>

                  <span class="text-sm font-extrabold text-slate-950 w-24 text-right">₦{{ number_format($item['price'] * $item['quantity']) }}</span>

                  <button wire:click="removeItem('{{ $sellerCart['id'] }}', '{{ $item['id'] }}')" class="text-slate-400 hover:text-rose-600 transition p-1 cursor-pointer" title="Remove item">
                    <i class="fas fa-trash-alt text-xs"></i>
                  </button>
                </div>
              </div>
            @endforeach
          </div>

          <!-- SELLER CART SUBTOTAL & DUAL ACTION BUTTONS -->
          @php
            $sellerSubtotal = collect($sellerCart['items'])->sum(fn($i) => $i['price'] * $i['quantity']);
          @endphp

          <div class="pt-3 border-t border-slate-100 space-y-4">
            <div class="flex justify-between items-baseline">
              <span class="text-xs text-slate-500 font-medium">Subtotal for {{ $sellerCart['name'] }}:</span>
              <span class="text-xl font-extrabold text-slate-950">₦{{ number_format($sellerSubtotal) }}</span>
            </div>

            <!-- TWO EXPLICIT ACTION BUTTONS -->
            <div class="grid sm:grid-cols-2 gap-3">
              <!-- BUTTON 1: MAKE CUSTOM OFFER -->
              <button wire:click="$dispatch('open-make-offer', { seller_id: '{{ $sellerCart['id'] }}', seller_name: '{{ $sellerCart['name'] }}' })" class="p-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition flex items-center gap-3 cursor-pointer text-left">
                <div class="w-9 h-9 rounded-xl bg-slate-800 text-white grid place-items-center shrink-0">
                  <i class="fas fa-handshake text-pp-400 text-sm"></i>
                </div>
                <div>
                  <div class="font-extrabold text-xs">Make Custom Offer</div>
                  <div class="text-[10px] text-slate-400 font-normal mt-0.5">Submit custom price &amp; delivery proposal</div>
                </div>
              </button>

              <!-- BUTTON 2: PROCEED TO CHECKOUT -->
              <a href="{{ route('checkout') }}?seller={{ $sellerCart['id'] }}" class="p-3.5 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-between gap-2 text-left">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-pp-500 text-white grid place-items-center shrink-0">
                    <i class="fas fa-shopping-bag text-sm"></i>
                  </div>
                  <div>
                    <div class="font-extrabold text-xs">Proceed to Checkout</div>
                    <div class="text-[10px] text-pp-200 font-normal mt-0.5">Select pickup or seller delivery &amp; pay</div>
                  </div>
                </div>
                <i class="fas fa-arrow-right text-xs shrink-0"></i>
              </a>
            </div>
          </div>

        </div>
      @endforeach

    </div>

    <!-- RIGHT: CART SUMMARY SIDEBAR -->
    <div class="lg:col-span-4 space-y-6">
      
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft sticky top-24">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-receipt text-pp-600"></i> Overall Cart Summary
        </h3>

        @php
          $grandTotal = collect($cartGrouped)->sum(function($group) {
            return collect($group['items'])->sum(fn($i) => $i['price'] * $i['quantity']);
          });
        @endphp

        <div class="space-y-2 text-xs">
          <div class="flex justify-between text-slate-600">
            <span>Total Items across stores</span>
            <span class="font-bold text-slate-900">4 Items</span>
          </div>
          <div class="flex justify-between text-slate-600">
            <span>Seller Stores</span>
            <span class="font-bold text-slate-900">{{ count($cartGrouped) }} Stores</span>
          </div>
          <div class="border-t border-slate-100 pt-2 flex justify-between items-baseline">
            <span class="font-bold text-slate-900">Grand Total</span>
            <span class="text-2xl font-black text-slate-950">₦{{ number_format($grandTotal) }}</span>
          </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-pp-50/70 border border-pp-100 text-[11px] text-slate-600 leading-snug space-y-1">
          <b class="text-pp-900 font-bold flex items-center gap-1"><i class="fas fa-shield-alt text-pp-600"></i> Parts &amp; Parcel Escrow Guarantee</b>
          <p>Payments are held securely until items are received and inspected.</p>
        </div>
      </div>

    </div>

  </div>

</main>