<div class="flex flex-col gap-6">
  
  <!-- TOP NAV & HEADER + FLASH MESSAGE WRAPPER -->
  <div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <a href="{{ route('offers') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
            <i class="fas fa-arrow-left text-[10px]"></i> Back to Offers Inbox
          </a>
          <span class="text-slate-300">·</span>
          <span class="text-xs font-extrabold text-slate-500">Offer Ref: {{ $offerId }}</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
          <span>Package Negotiation &amp; Offer Details</span>
          @if ($offer?->cart_id)
            <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] uppercase">
              <i class="fas fa-shopping-cart text-pp-600"></i> SOURCE: BUYER CART
            </span>
          @elseif ($offer?->discussion_id)
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase">
              <i class="fas fa-users text-emerald-600"></i> SOURCE: COMMUNITY HUB
            </span>
          @else
            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 font-extrabold text-[10px] uppercase">
              DIRECT PROPOSAL
            </span>
          @endif
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
          Negotiation between <strong>{{ $offer?->sender?->business_name ?: $offer?->sender?->name ?: 'Party A' }}</strong> and <strong>{{ $offer?->recipient?->business_name ?: $offer?->recipient?->name ?: 'Party B' }}</strong>
        </p>
      </div>

      <div class="flex items-center gap-2">
        @if ($offer && $offer->status === 'accepted')
          <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 text-xs font-extrabold flex items-center gap-1.5">
            <i class="fas fa-check-circle text-emerald-600"></i> Offer Accepted
          </span>
        @elseif ($offer && $offer->status === 'countered')
          <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-extrabold flex items-center gap-1.5">
            <i class="fas fa-history"></i> Superceded by Counter-Offer
          </span>
        @else
          <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 text-xs font-extrabold flex items-center gap-1.5">
            <i class="fas fa-clock"></i> Active Proposal
          </span>
        @endif
      </div>
    </div>

    @if (session()->has('message'))
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2">
          <i class="fas fa-check-circle text-emerald-600 text-base"></i>
          <span>{{ session('message') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
      </div>
    @endif

    @if (session()->has('error'))
      <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2">
          <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
          <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer"><i class="fas fa-times"></i></button>
      </div>
    @endif
  </div>

  @php
    $user = auth()->user();
    $isSender = $user && $offer && ($user->id === $offer->sender_id);
    $isRecipient = $user && $offer && ($user->id === $offer->recipient_id);
    $canAct = $isRecipient && $offer && ($offer->status === 'pending');
    $currentTotal = $offer ? $offer->total() : 265000;
    $currentDiscount = $offer ? (float) $offer->discount : 5000;
    $itemsList = $offer ? $offer->items : collect([
      (object) ['description' => 'HP EliteBook 840 G5 Laptop', 'type' => 'item', 'quantity' => 1, 'unit_price' => 250000, 'warranty_period_days' => 14, 'warranty_terms' => 'Clean board testing warranty'],
      (object) ['description' => '16GB DDR4 RAM Upgrade Service', 'type' => 'service', 'quantity' => 1, 'unit_price' => 10000, 'warranty_period_days' => 14, 'warranty_terms' => 'Module testing warranty'],
      (object) ['description' => 'Express Seller Delivery to Destination', 'type' => 'delivery', 'quantity' => 1, 'unit_price' => 10000, 'warranty_period_days' => null, 'warranty_terms' => 'Verified rider dispatch'],
    ]);
  @endphp

  <!-- SECTION 1: LATEST ACTIVE OFFER (FULLY EXPANDED AT TOP) -->
  <div class="bg-white rounded-3xl border-2 {{ $canAct ? 'border-pp-600' : 'border-slate-200' }} p-6 sm:p-8 space-y-6 shadow-soft relative">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
      <div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-extrabold text-[10px] uppercase tracking-wider">
            LATEST ACTIVE OFFER PROPOSAL
          </span>
          @if ($canAct)
            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase animate-pulse">
              YOUR ACTION REQUIRED
            </span>
          @else
            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] uppercase">
              STATUS: {{ strtoupper($offer?->status ?? 'PENDING') }}
            </span>
          @endif
        </div>
        <h2 class="text-lg font-extrabold text-slate-900 mt-1">
          Proposed by {{ $offer?->sender?->business_name ?: $offer?->sender?->name ?: 'Vendor' }}
        </h2>
        <p class="text-xs text-slate-500">{{ $offer ? $offer->created_at->diffForHumans() : 'Recently' }}</p>
      </div>

      <div class="text-left sm:text-right">
        <span class="text-xs text-slate-400 font-bold uppercase block">Current Proposed Net Total</span>
        <span class="text-3xl font-extrabold text-slate-950">₦{{ number_format($currentTotal) }}</span>
        @if ($currentDiscount > 0)
          <span class="text-xs text-pp-700 font-bold block">Includes ₦{{ number_format($currentDiscount) }} Special Discount</span>
        @endif
      </div>
    </div>

    <!-- ITEMIZED OFFER ITEMS TABLE -->
    <div class="space-y-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list-ul text-pp-600"></i> Itemized Offer Breakdown ({{ count($itemsList) }} Items / Services)
      </h3>

      <div class="overflow-x-auto rounded-2xl border border-slate-200">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
            <tr>
              <th class="p-3">Item / Service Description</th>
              <th class="p-3">Type</th>
              <th class="p-3 text-center">Qty</th>
              <th class="p-3 text-right">Unit Price</th>
              <th class="p-3">Warranty Period</th>
              <th class="p-3">Warranty Terms</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
            @foreach ($itemsList as $item)
              <tr>
                <td class="p-3 font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <span class="text-base">
                      @if ($item->type === 'service') ⚡ @elseif ($item->type === 'delivery') 🚚 @else 💻 @endif
                    </span>
                    <span>{{ $item->description }}</span>
                  </div>
                </td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $item->type === 'service' ? 'bg-pp-100 text-pp-800' : ($item->type === 'delivery' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-900 text-white') }}">
                    {{ $item->type }}
                  </span>
                </td>
                <td class="p-3 text-center font-bold text-slate-900">{{ $item->quantity }}</td>
                <td class="p-3 text-right font-extrabold text-slate-900">₦{{ number_format($item->unit_price) }}</td>
                <td class="p-3">
                  @if ($item->warranty_period_days)
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">
                      {{ $item->warranty_period_days }} DAYS
                    </span>
                  @else
                    <span class="text-slate-400">N/A</span>
                  @endif
                </td>
                <td class="p-3 text-slate-500">{{ $item->warranty_terms ?: 'Standard terms' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- TERMS & DISCOUNT BREAKDOWN SUMMARY -->
    <div class="p-4 rounded-2xl bg-pp-50/50 border border-pp-200 space-y-2 text-xs">
      <div class="flex items-center justify-between font-bold text-slate-900 border-b border-pp-200/60 pb-2">
        <span>Delivery &amp; Proposal Note:</span>
        <span class="text-pp-700 font-extrabold">
          Fulfillment: {{ ($offer?->delivery_method ?? 'buyer_responsible') === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}
        </span>
      </div>
      <p class="text-slate-700 italic leading-relaxed">
        "{{ $offer?->terms ?: 'All items inspected and tested prior to dispatch or collection.' }}"
      </p>
    </div>

    <!-- RECIPIENT ACTION BUTTONS -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
      <div class="text-xs text-slate-500">
        <i class="fas fa-shield-alt text-pp-600 mr-1"></i> Funds held securely in Parts &amp; Parcel Escrow upon acceptance.
      </div>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        @if ($canAct)
          <button wire:click="openCounterDrawer" class="flex-1 sm:flex-none px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
            <i class="fas fa-pen-to-square"></i>
            <span>Make Counter Offer</span>
          </button>

          <button wire:click="acceptOffer" class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
            <i class="fas fa-check-circle"></i>
            <span>Accept Offer &amp; Reserve (₦{{ number_format($currentTotal) }})</span>
          </button>
        @elseif ($offer && $offer->status === 'accepted')
          <a href="{{ route('invoices') }}" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-2">
            <i class="fas fa-receipt"></i>
            <span>View Generated Invoice</span>
          </a>
        @elseif ($isSender)
          <span class="text-xs font-bold text-slate-500 bg-slate-100 px-4 py-2.5 rounded-xl">
            Awaiting response from {{ $offer?->recipient?->name }}
          </span>
        @endif
      </div>
    </div>
  </div>

  <!-- SECTION 2: TIMELINE OF PREVIOUS NEGOTIATION ROUNDS -->
  @php
    $previousRounds = collect($rounds)->filter(fn($r) => !($r['is_current'] ?? false))->values();
  @endphp

  @if ($previousRounds->isNotEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-history text-pp-600"></i> Negotiation History Timeline ({{ count($previousRounds) }} Previous Rounds)
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Click any round below to review previous proposals.</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">
          {{ count($previousRounds) }} Turns
        </span>
      </div>

      <div class="space-y-3">
        @foreach ($previousRounds as $pRound)
          <div class="rounded-2xl border border-slate-200 overflow-hidden transition">
            <button wire:click="toggleRound({{ $pRound['round_number'] }})" class="w-full text-left p-4 bg-slate-50 hover:bg-slate-100/70 flex items-center justify-between gap-4 transition cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-pp-100 text-pp-800 font-extrabold text-xs grid place-items-center">
                  R{{ $pRound['round_number'] }}
                </span>
                <div>
                  <span class="text-xs font-extrabold text-slate-900 block">Round {{ $pRound['round_number'] }}: Proposal by {{ $pRound['sender'] }}</span>
                  <span class="text-[11px] text-slate-500">{{ $pRound['time'] }} · Proposed: <strong>₦{{ number_format($pRound['total']) }}</strong></span>
                </div>
              </div>
              <div class="flex items-center gap-2 text-xs font-bold text-pp-600">
                <span>{{ $expandedRound === $pRound['round_number'] ? 'Hide Details' : 'Expand Details' }}</span>
                <i class="fas {{ $expandedRound === $pRound['round_number'] ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs"></i>
              </div>
            </button>

            @if ($expandedRound === $pRound['round_number'])
              <div class="p-4 bg-white border-t border-slate-200 space-y-3 text-xs">
                @if ($pRound['terms'])
                  <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700">
                    <strong>Note / Terms:</strong> "{{ $pRound['terms'] }}"
                  </div>
                @endif
                <div class="font-bold text-slate-800">Items in Round {{ $pRound['round_number'] }}:</div>
                <ul class="list-disc pl-5 space-y-1 text-slate-600">
                  @foreach ($pRound['items'] as $it)
                    <li>{{ $it->description }} — ₦{{ number_format($it->unit_price) }} ({{ $it->warranty_period_days ? "{$it->warranty_period_days} Days Warranty" : 'No Warranty' }})</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- COUNTER-OFFER BUILDER (DESKTOP SIDE-DRAWER & MOBILE OVERLAY) -->
  @if ($showCounterDrawer)
    <!-- BACKDROP OVERLAY -->
    <div wire:click="closeCounterDrawer" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-xs transition"></div>

    <!-- SIDE-DRAWER / MODAL CONTAINER -->
    <div class="fixed top-0 right-0 bottom-0 z-50 w-full sm:w-[480px] bg-white shadow-2xl flex flex-col transition">
      
      <!-- DRAWER HEADER -->
      <div class="h-16 px-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
        <div>
          <h3 class="font-extrabold text-slate-900 text-base">Construct Counter-Offer</h3>
          <p class="text-[11px] text-slate-400">Replying to {{ $offer?->sender?->name }} · {{ $offerId }}</p>
        </div>
        <button wire:click="closeCounterDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg grid place-items-center cursor-pointer">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- DRAWER FORM BODY (SCROLLABLE) -->
      <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 text-xs">
        
        <div class="p-3.5 rounded-2xl bg-pp-50 border border-pp-100 text-pp-900 space-y-1">
          <b class="font-bold flex items-center gap-1.5"><i class="fas fa-lightbulb text-pp-600"></i> Counter-Offer Guidance:</b>
          <p class="text-[11px] text-slate-600 leading-relaxed">
            Adjust the proposed price, set a discount, and customize the warranty terms. The other party will receive your revised breakdown immediately.
          </p>
        </div>

        <!-- PRICING & DISCOUNT INPUTS -->
        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Total Proposed Price (₦) <span class="text-rose-500">*</span></label>
            <input type="number" wire:model="counterPrice" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Offer Discount (₦)</label>
            <input type="number" wire:model="counterDiscount" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-slate-900 outline-none focus:border-pp-500" />
          </div>
        </div>

        <!-- WARRANTY PERIOD & TERMS -->
        <div class="space-y-3 border-t border-slate-100 pt-4">
          <label class="font-bold text-slate-800 block">Warranty Period</label>
          <div class="grid grid-cols-3 gap-2">
            <button type="button" wire:click="$set('warrantyPeriod', 7)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyPeriod === 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">7 Days</button>
            <button type="button" wire:click="$set('warrantyPeriod', 14)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyPeriod === 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">14 Days</button>
            <button type="button" wire:click="$set('warrantyPeriod', 30)" class="py-2 rounded-xl border text-center font-bold transition cursor-pointer {{ $warrantyPeriod === 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">30 Days</button>
          </div>

          <div class="space-y-1 pt-1">
            <label class="font-bold text-slate-700 block">Warranty Scope &amp; Details</label>
            <textarea wire:model="warrantyTerms" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 outline-none focus:border-pp-500" placeholder="Specify warranty terms..."></textarea>
          </div>
        </div>

        <!-- COUNTER OFFER NOTES -->
        <div class="space-y-1 border-t border-slate-100 pt-4">
          <label class="font-bold text-slate-800 block">Custom Message / Terms for Recipient</label>
          <textarea wire:model="counterNotes" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 outline-none focus:border-pp-500" placeholder="Explain your counter-offer details..."></textarea>
        </div>

      </div>

      <!-- DRAWER FOOTER -->
      <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between gap-3 shrink-0">
        <button wire:click="closeCounterDrawer" class="py-3 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
          Cancel
        </button>

        <button wire:click="submitCounterOffer" class="flex-1 py-3 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer text-center">
          Submit Counter Offer →
        </button>
      </div>

    </div>
  @endif

</div>