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
        <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-xs font-extrabold">{{ count($cartGrouped) }} Seller {{ Str::plural('Store', count($cartGrouped)) }}</span>
      </h1>
    </div>

    <a href="/" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
      <i class="fas fa-arrow-left text-[10px]"></i> Continue Shopping
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

  @guest
    <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
      <div class="flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-pp-600 text-white grid place-items-center text-sm shrink-0">🛒</span>
        <div>
          <b class="text-slate-900 font-bold">Shopping as a Guest:</b>
          <span class="text-slate-600 ml-1">Your cart items are saved. Please log in when you are ready to checkout.</span>
        </div>
      </div>
      <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition shadow-2xs text-center shrink-0">
        Log In to Checkout
      </a>
    </div>
  @endguest

  @if (empty($cartGrouped))
    <!-- EMPTY CART STATE -->
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-soft space-y-4 max-w-lg mx-auto my-8">
      <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 text-2xl grid place-items-center mx-auto shadow-inner">
        <i class="fas fa-shopping-bag"></i>
      </div>
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">Your Cart is Empty</h2>
        <p class="text-xs text-slate-500 mt-1">Explore verified devices, authentic spare parts, and scrap items on Parts &amp; Parcel.</p>
      </div>
      <div class="pt-2">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition">
          <i class="fas fa-search text-xs"></i> Browse Marketplace
        </a>
      </div>
    </div>
  @else

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
            
            <!-- SELLER HEADER -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl {{ $sellerCart['avatar_bg'] ?? 'bg-pp-600' }} text-white font-black grid place-items-center text-base shrink-0 shadow-xs">
                  {{ $sellerCart['avatar'] ?? 'S' }}
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-sm text-slate-900">{{ $sellerCart['name'] }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $sellerCart['badge_bg'] ?? 'bg-emerald-50 text-emerald-700' }}">
                      {{ $sellerCart['badge'] ?? 'Verified Store' }}
                    </span>
                  </div>
                  <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fas fa-map-marker-alt text-[10px] text-slate-400"></i>
                    {{ $sellerCart['location'] }}
                  </p>
                </div>
              </div>

              <span class="text-xs font-bold text-slate-600 bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-100">
                {{ count($sellerCart['items']) }} {{ Str::plural('Item', count($sellerCart['items'])) }}
              </span>
            </div>

            <!-- CART ITEMS LIST FOR THIS SELLER -->
            <div class="divide-y divide-slate-100">
              @foreach ($sellerCart['items'] as $item)
                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4 flex-wrap sm:flex-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 grid place-items-center text-xl shrink-0">
                      {{ $item['icon'] ?? '📦' }}
                    </div>
                    <div>
                      <a href="{{ !empty($item['listing_id']) ? route('listing-details', $item['listing_id']) : '#' }}" class="font-extrabold text-xs text-slate-900 hover:text-pp-600 transition block">
                        {{ $item['title'] }}
                      </a>
                      <p class="text-[11px] text-slate-500 mt-0.5">{{ $item['specs'] ?? 'Standard' }}</p>
                      <p class="text-xs font-bold text-pp-700 mt-0.5">₦{{ number_format($item['price']) }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-4 ml-auto sm:ml-0">
                    <!-- QUANTITY CONTROL -->
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
                <button type="button" wire:click="proceedToCheckout('{{ $sellerCart['id'] }}')" class="p-3.5 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-between gap-2 text-left cursor-pointer">
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
                </button>
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
            $totalItemsCount = collect($cartGrouped)->sum(function($group) {
              return collect($group['items'])->sum('quantity');
            });
          @endphp

          <div class="space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Total Items across stores</span>
              <span class="font-bold text-slate-900">{{ $totalItemsCount }} {{ Str::plural('Item', $totalItemsCount) }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
              <span>Seller Stores</span>
              <span class="font-bold text-slate-900">{{ count($cartGrouped) }} {{ Str::plural('Store', count($cartGrouped)) }}</span>
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
  @endif

</main>