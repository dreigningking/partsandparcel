<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myresponses') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to My Responses
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Proposal #RESP-1092</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Proposal for Request #REQ-8402</span>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
          COUNTER OFFER RECEIVED
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Community Request by TechSam: "Looking for HP EliteBook 840 G5 Motherboard"</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('offers.view', ['id' => 'OFF-9021']) }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
        Open Full Negotiation Thread →
      </a>
    </div>
  </div>

  <!-- PROPOSAL & COUNTER SUMMARY -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    <div class="grid sm:grid-cols-2 gap-6 border-b border-slate-100 pb-6 text-xs">
      <div class="space-y-1">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Your Original Proposal</span>
        <h3 class="text-xl font-black text-slate-950">₦80,000</h3>
        <p class="text-slate-600">Warranty: 14 Days · Fulfillment: Self-Pickup</p>
      </div>

      <div class="space-y-1 text-left sm:text-right">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Buyer Counter Offer</span>
        <h3 class="text-xl font-black text-amber-700">₦78,000</h3>
        <p class="text-slate-600">Note: "Let us meet in the middle at ₦78,000."</p>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3">
      <a href="{{ route('offers.view', ['id' => 'OFF-9021']) }}" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
        Accept Buyer Counter (₦78,000)
      </a>
    </div>
  </div>

</div>