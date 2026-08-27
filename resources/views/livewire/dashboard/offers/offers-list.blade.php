<div class="space-y-6">
  
  <!-- PAGE HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Offers &amp; Negotiations Inbox</h1>
      <p class="text-xs text-slate-500 mt-0.5">Review, negotiate, and respond to buyer package offers &amp; community hub proposals.</p>
    </div>

    <!-- FILTER TABS -->
    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button wire:click="$set('activeTab', 'received')" class="px-3 py-1.5 rounded-lg transition {{ $activeTab === 'received' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Received (4)</button>
      <button wire:click="$set('activeTab', 'sent')" class="px-3 py-1.5 rounded-lg transition {{ $activeTab === 'sent' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Sent (2)</button>
      <button wire:click="$set('activeTab', 'accepted')" class="px-3 py-1.5 rounded-lg transition {{ $activeTab === 'accepted' ? 'bg-emerald-600 text-white' : 'text-emerald-700 hover:bg-emerald-50' }}">Accepted (12)</button>
      <button wire:click="$set('activeTab', 'declined')" class="px-3 py-1.5 rounded-lg transition {{ $activeTab === 'declined' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Declined</button>
      <button wire:click="$set('activeTab', 'all')" class="px-3 py-1.5 rounded-lg transition {{ $activeTab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">All (18)</button>
    </div>
  </div>

  <!-- OFFERS LIST STREAM -->
  <div class="space-y-4">
    
    <!-- OFFER CARD 1: CART PACKAGE SOURCE -->
    <div class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
          <!-- SOURCE BADGE -->
          <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] flex items-center gap-1">
            <i class="fas fa-shopping-cart text-pp-600"></i> SOURCE: BUYER CART PACKAGE
          </span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Buyer: TechSam (Ikeja, Lagos)</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-400">45 mins ago</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[11px]">EXPIRES IN 23 HOURS</span>
      </div>

      <div class="grid sm:grid-cols-12 gap-4 items-center">
        <div class="sm:col-span-6 flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-xl shrink-0">💻</div>
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <h4 class="text-sm font-extrabold text-slate-900">HP EliteBook 840 G5 Deal</h4>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">3 Items Included</span>
            </div>
            <p class="text-xs text-slate-500">Includes: Laptop + 16GB RAM Upgrade + Community Delivery Request</p>
            <p class="text-xs text-slate-600 italic">Terms: "Can pick up today if RAM upgrade is installed."</p>
          </div>
        </div>

        <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
          <span class="text-xs text-slate-400 block font-medium">Proposed Offer:</span>
          <span class="text-2xl font-extrabold text-slate-950">₦265,000</span>
          <span class="text-[10px] text-pp-700 font-bold block">Discounted (-₦5,000)</span>
        </div>

        <div class="sm:col-span-3 flex flex-col gap-2">
          <a href="{{ route('offers.view', ['id' => 'OFF-9021']) }}" class="w-full py-2.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition block">
            View Negotiation &amp; Details →
          </a>
          <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 font-medium">
            <span>Latest: Round 3 Counter</span>
            <span class="text-amber-800 font-bold">Action Needed</span>
          </div>
        </div>
      </div>
    </div>

    <!-- OFFER CARD 2: COMMUNITY HUB REQUEST SOURCE -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
          <!-- SOURCE BADGE -->
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] flex items-center gap-1">
            <i class="fas fa-users text-emerald-600"></i> SOURCE: COMMUNITY HUB REQUEST
          </span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Seller: Abel Electronics Hub</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-400">2 hours ago</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">ROUND 1</span>
      </div>

      <div class="grid sm:grid-cols-12 gap-4 items-center">
        <div class="sm:col-span-6 flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 grid place-items-center text-xl shrink-0">🛠️</div>
          <div class="space-y-1">
            <h4 class="text-sm font-extrabold text-slate-900">Dell Latitude 5420 Motherboard (Tested)</h4>
            <p class="text-xs text-slate-500">Includes 14-Day Warranty + Self-Pickup in Computer Village</p>
            <p class="text-xs text-slate-600 italic">"Clean board pulled from working unit. Guaranteed no prior repair."</p>
          </div>
        </div>

        <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
          <span class="text-xs text-slate-400 block font-medium">Offered Price:</span>
          <span class="text-2xl font-extrabold text-slate-950">₦85,000</span>
          <span class="text-[10px] text-emerald-600 font-bold block">14-Day Warranty Included</span>
        </div>

        <div class="sm:col-span-3 flex flex-col gap-2">
          <a href="{{ route('offers.view', ['id' => 'OFF-9022']) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs text-center shadow-xs transition block">
            View Negotiation &amp; Details →
          </a>
          <span class="text-[11px] text-slate-400 text-center">Awaiting your response</span>
        </div>
      </div>
    </div>

    <!-- OFFER CARD 3: ACCEPTED OFFER -->
    <div class="bg-emerald-50/40 rounded-3xl border border-emerald-300 p-6 space-y-4">
      <div class="flex items-center justify-between text-xs border-b border-emerald-200 pb-3 flex-wrap gap-2">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px]">
            <i class="fas fa-check-circle"></i> ACCEPTED &amp; RESERVED
          </span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Buyer: Abel Tech Parts</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-500">Accepted Yesterday</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">PAYMENT PENDING</span>
      </div>

      <div class="flex items-center justify-between text-xs flex-wrap gap-4">
        <div>
          <h4 class="font-bold text-slate-900 text-sm">Dell Latitude 5420 Scrap Unit</h4>
          <p class="text-slate-600 mt-0.5">Accepted Final Price: <span class="font-extrabold text-slate-950">₦150,000</span></p>
        </div>
        <a href="{{ route('offers.view', ['id' => 'OFF-9019']) }}" class="px-4 py-2 rounded-xl bg-white border border-emerald-300 hover:bg-emerald-100/50 font-bold text-emerald-800 text-xs transition">
          View Agreed Details →
        </a>
      </div>
    </div>

  </div>

</div>