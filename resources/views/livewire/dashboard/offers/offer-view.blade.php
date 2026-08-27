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
          <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] uppercase">
            <i class="fas fa-shopping-cart text-pp-600"></i> SOURCE: BUYER CART
          </span>
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Negotiation between <strong>TechSam (Buyer)</strong> and <strong>Adam Computers (Seller)</strong></p>
      </div>

      <div class="flex items-center gap-2">
        <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 text-xs font-extrabold flex items-center gap-1.5">
          <i class="fas fa-clock"></i> Expires in 23 Hours
        </span>
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
  </div>

  <!-- SECTION 1: LATEST ACTIVE COUNTER-OFFER (ROUND 3 - FULLY EXPANDED AT TOP) -->
  <div class="bg-white rounded-3xl border-2 border-pp-600 p-6 sm:p-8 space-y-6 shadow-soft relative">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
      <div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-extrabold text-[10px] uppercase tracking-wider">
            ROUND 3 (CURRENT ACTIVE OFFER)
          </span>
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
            ACTION REQUIRED
          </span>
        </div>
        <h2 class="text-lg font-extrabold text-slate-900 mt-1">Proposed Counter-Offer by Adam Computers (Seller)</h2>
        <p class="text-xs text-slate-500">Submitted 45 minutes ago</p>
      </div>

      <div class="text-left sm:text-right">
        <span class="text-xs text-slate-400 font-bold uppercase block">Current Proposed Net Total</span>
        <span class="text-3xl font-extrabold text-slate-950">₦265,000</span>
        <span class="text-xs text-pp-700 font-bold block">Includes ₦5,000 Special Discount</span>
      </div>
    </div>

    <!-- ITEMIZED OFFER ITEMS TABLE -->
    <div class="space-y-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list-ul text-pp-600"></i> Itemized Offer Breakdown (3 Items / Services)
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
            <tr>
              <td class="p-3 font-bold text-slate-900">
                <div class="flex items-center gap-2">
                  <span class="text-base">💻</span>
                  <span>HP EliteBook 840 G5 Laptop</span>
                </div>
              </td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold">ITEM</span></td>
              <td class="p-3 text-center font-bold text-slate-900">1</td>
              <td class="p-3 text-right font-extrabold text-slate-900">₦250,000</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">14 DAYS</span></td>
              <td class="p-3 text-slate-500">Clean original board &amp; screen testing warranty</td>
            </tr>
            <tr>
              <td class="p-3 font-bold text-slate-900">
                <div class="flex items-center gap-2">
                  <span class="text-base">⚡</span>
                  <span>16GB DDR4 RAM Upgrade Service</span>
                </div>
              </td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-pp-100 text-pp-800 text-[9px] font-bold">SERVICE</span></td>
              <td class="p-3 text-center font-bold text-slate-900">1</td>
              <td class="p-3 text-right font-extrabold text-slate-900">₦10,000</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">14 DAYS</span></td>
              <td class="p-3 text-slate-500">Dual-channel module testing included</td>
            </tr>
            <tr>
              <td class="p-3 font-bold text-slate-900">
                <div class="flex items-center gap-2">
                  <span class="text-base">🚚</span>
                  <span>Express Seller Delivery to Lekki Phase 1</span>
                </div>
              </td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">DELIVERY</span></td>
              <td class="p-3 text-center font-bold text-slate-900">1</td>
              <td class="p-3 text-right font-extrabold text-slate-900">₦10,000</td>
              <td class="p-3 text-slate-400">N/A</td>
              <td class="p-3 text-slate-500">Same-day verified rider dispatch</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TERMS & DISCOUNT BREAKDOWN SUMMARY -->
    <div class="p-4 rounded-2xl bg-pp-50/50 border border-pp-200 space-y-2 text-xs">
      <div class="flex items-center justify-between font-bold text-slate-900 border-b border-pp-200/60 pb-2">
        <span>Seller Note / Terms:</span>
        <span class="text-pp-700 font-extrabold">Subtotal: ₦270,000 · Special Discount: -₦5,000</span>
      </div>
      <p class="text-slate-700 italic leading-relaxed">
        "I can install the 16GB RAM upgrade and arrange express delivery if we agree on ₦265,000. All items tested prior to dispatch."
      </p>
    </div>

    <!-- RECIPIENT ACTION BUTTONS -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
      <div class="text-xs text-slate-500">
        <i class="fas fa-shield-alt text-pp-600 mr-1"></i> Funds held securely in Parts &amp; Parcel Escrow upon acceptance.
      </div>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <button wire:click="openCounterDrawer" class="flex-1 sm:flex-none px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
          <i class="fas fa-pen-to-square"></i>
          <span>Make Counter Offer</span>
        </button>

        <button wire:click="acceptOffer" class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-1.5">
          <i class="fas fa-check-circle"></i>
          <span>Accept Offer &amp; Reserve (₦265,000)</span>
        </button>
      </div>
    </div>
  </div>

  <!-- SECTION 2: COMPACT COLLAPSIBLE ACCORDION TIMELINE (PREVIOUS NEGOTIATION ROUNDS) -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-history text-pp-600"></i> Negotiation History Timeline (2 Previous Rounds)
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">Click any round below to expand its itemized breakdown.</p>
      </div>
      <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">2 Previous Turns</span>
    </div>

    <div class="space-y-3">
      
      <!-- ROUND 2 ACCORDION ITEM -->
      <div class="rounded-2xl border border-slate-200 overflow-hidden transition">
        <button wire:click="toggleRound(2)" class="w-full text-left p-4 bg-slate-50 hover:bg-slate-100/70 flex items-center justify-between gap-4 transition cursor-pointer">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 font-extrabold text-xs grid place-items-center">R2</span>
            <div>
              <span class="text-xs font-extrabold text-slate-900 block">Round 2: Counter Offer by TechSam (Buyer)</span>
              <span class="text-[11px] text-slate-500">2 hours ago · Proposed Price: <strong>₦255,000</strong></span>
            </div>
          </div>
          <div class="flex items-center gap-2 text-xs font-bold text-pp-600">
            <span>{{ $expandedRound === 2 ? 'Hide Details' : 'Expand Details' }}</span>
            <i class="fas {{ $expandedRound === 2 ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs"></i>
          </div>
        </button>

        @if ($expandedRound === 2)
          <div class="p-4 bg-white border-t border-slate-200 space-y-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700">
              <strong>Buyer Note:</strong> "Can we do ₦255,000 for the laptop and RAM upgrade? I will handle pickup myself."
            </div>
            <div class="font-bold text-slate-800">Items Included in Round 2:</div>
            <ul class="list-disc pl-5 space-y-1 text-slate-600">
              <li>HP EliteBook 840 G5 Laptop — ₦245,000</li>
              <li>16GB DDR4 RAM Upgrade Service — ₦10,000</li>
            </ul>
          </div>
        @endif
      </div>

      <!-- ROUND 1 ACCORDION ITEM -->
      <div class="rounded-2xl border border-slate-200 overflow-hidden transition">
        <button wire:click="toggleRound(1)" class="w-full text-left p-4 bg-slate-50 hover:bg-slate-100/70 flex items-center justify-between gap-4 transition cursor-pointer">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-pp-100 text-pp-800 font-extrabold text-xs grid place-items-center">R1</span>
            <div>
              <span class="text-xs font-extrabold text-slate-900 block">Round 1: Initial Offer by TechSam (Buyer)</span>
              <span class="text-[11px] text-slate-500">3 hours ago · Proposed Price: <strong>₦250,000</strong></span>
            </div>
          </div>
          <div class="flex items-center gap-2 text-xs font-bold text-pp-600">
            <span>{{ $expandedRound === 1 ? 'Hide Details' : 'Expand Details' }}</span>
            <i class="fas {{ $expandedRound === 1 ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs"></i>
          </div>
        </button>

        @if ($expandedRound === 1)
          <div class="p-4 bg-white border-t border-slate-200 space-y-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700">
              <strong>Initial Buyer Offer Note:</strong> "Offering ₦250,000 for the bare HP EliteBook 840 G5 laptop."
            </div>
            <div class="font-bold text-slate-800">Items Included in Round 1:</div>
            <ul class="list-disc pl-5 space-y-1 text-slate-600">
              <li>HP EliteBook 840 G5 Laptop — ₦250,000</li>
            </ul>
          </div>
        @endif
      </div>

    </div>
  </div>

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
          <p class="text-[11px] text-slate-400">Offer Ref: {{ $offerId }}</p>
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
            Adjust item prices, add repair/upgrade services or delivery fees, and set warranty terms. The buyer will receive your revised breakdown.
          </p>
        </div>

        <!-- PRICING & DISCOUNT INPUTS -->
        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="font-bold text-slate-700 block">Total Proposed Price (₦)</label>
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
            <button type="button" wire:click="$set('warrantyPeriod', 7)" class="py-2 rounded-xl border text-center font-bold transition {{ $warrantyPeriod === 7 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">7 Days</button>
            <button type="button" wire:click="$set('warrantyPeriod', 14)" class="py-2 rounded-xl border text-center font-bold transition {{ $warrantyPeriod === 14 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">14 Days</button>
            <button type="button" wire:click="$set('warrantyPeriod', 30)" class="py-2 rounded-xl border text-center font-bold transition {{ $warrantyPeriod === 30 ? 'border-2 border-pp-600 bg-pp-50 text-pp-700' : 'border-slate-200 text-slate-700' }}">30 Days</button>
          </div>

          <div class="space-y-1 pt-1">
            <label class="font-bold text-slate-700 block">Warranty Terms &amp; Scope</label>
            <textarea wire:model="warrantyTerms" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 outline-none focus:border-pp-500" placeholder="Specify warranty terms..."></textarea>
          </div>
        </div>

        <!-- COUNTER OFFER NOTES -->
        <div class="space-y-1 border-t border-slate-100 pt-4">
          <label class="font-bold text-slate-800 block">Custom Message / Counter Terms</label>
          <textarea wire:model="counterNotes" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 text-slate-800 outline-none focus:border-pp-500" placeholder="Explain your counter offer details to buyer..."></textarea>
        </div>

      </div>

      <!-- DRAWER FOOTER -->
      <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between gap-3 shrink-0">
        <button wire:click="closeCounterDrawer" class="py-3 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
          Cancel
        </button>

        <button wire:click="submitCounterOffer" class="flex-1 py-3 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer text-center">
          Submit Counter Offer →
        </button>
      </div>

    </div>
  @endif

</div>