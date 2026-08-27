<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">My Community Hub Responses</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your submitted proposals and negotiated offers for buyer community requests.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">Active Responses (3)</button>
      <button class="px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50">Accepted (8)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Declined</button>
    </div>
  </div>

  <!-- RESPONSES STREAM -->
  <div class="space-y-4">
    
    <!-- RESPONSE CARD 1 -->
    <div class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">RESPONSE #RESP-1092</span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Buyer: TechSam (Ikeja)</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-400">Responded 2 hours ago</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[11px]">COUNTER OFFER RECEIVED</span>
      </div>

      <div class="grid sm:grid-cols-12 gap-4 items-center">
        <div class="sm:col-span-6 space-y-1">
          <h3 class="text-base font-extrabold text-slate-900">HP EliteBook 840 G5 Motherboard Request</h3>
          <p class="text-xs text-slate-500">Your Quote: <strong class="text-slate-900">₦80,000</strong> · 14-Day Warranty · Self-Pickup</p>
          <p class="text-xs text-slate-600 italic">"Original motherboard, clean condition. Tested working."</p>
        </div>

        <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
          <span class="text-xs text-slate-400 font-bold block uppercase">Buyer Counter</span>
          <span class="text-2xl font-black text-slate-950">₦78,000</span>
          <span class="text-[10px] text-amber-700 font-bold block">Awaiting your response</span>
        </div>

        <div class="sm:col-span-3 flex flex-col gap-2">
          <a href="{{ route('myresponse.view', ['id' => 'RESP-1092']) }}" class="w-full py-2.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition block">
            View Negotiation &amp; Respond →
          </a>
        </div>
      </div>
    </div>

  </div>

</div>