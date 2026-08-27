<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">My Community Requests</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track your open product &amp; service requests posted to the community hub.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">Open (2)</button>
      <button class="px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50">Fulfilled / Purchased (4)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Closed (1)</button>
    </div>
  </div>

  <!-- REQUEST CARDS STREAM -->
  <div class="space-y-4">
    
    <!-- REQUEST CARD 1 -->
    <div class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-100">OPEN FOR PROPOSALS</span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Posted 2 hours ago</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-500">Ref: #REQ-8402</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[11px]">3 PRIVATE OFFERS RECEIVED</span>
      </div>

      <div class="grid sm:grid-cols-12 gap-4 items-center">
        <div class="sm:col-span-6 space-y-1">
          <h3 class="text-base font-extrabold text-slate-900">Looking for tested HP EliteBook 840 G5 Motherboard (Core i5 8th Gen)</h3>
          <p class="text-xs text-slate-500">Target Budget: <strong class="text-pp-700">₦70,000 - ₦90,000</strong> · Fulfillment: Self-Pickup in Ikeja</p>
          <p class="text-xs text-slate-600 italic">"Must be clean pull with no prior repairs. Located at Computer Village Ikeja."</p>
        </div>

        <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
          <span class="text-xs text-slate-400 font-bold block uppercase">Activity Stats</span>
          <span class="text-lg font-black text-slate-950">5 Responses</span>
          <span class="text-[10px] text-emerald-600 font-bold block">3 Vendor Offers Submitted</span>
        </div>

        <div class="sm:col-span-3 flex flex-col gap-2">
          <a href="{{ route('myrequest.view', ['id' => 'REQ-8402']) }}" class="w-full py-2.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition block">
            Manage Request &amp; View Offers →
          </a>
        </div>
      </div>
    </div>

    <!-- REQUEST CARD 2 -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-100">OPEN FOR PROPOSALS</span>
          <span class="text-slate-300">·</span>
          <span class="font-bold text-slate-900">Posted Yesterday</span>
          <span class="text-slate-300">·</span>
          <span class="text-slate-500">Ref: #REQ-8390</span>
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">1 OFFER RECEIVED</span>
      </div>

      <div class="grid sm:grid-cols-12 gap-4 items-center">
        <div class="sm:col-span-6 space-y-1">
          <h3 class="text-base font-extrabold text-slate-900">Need HP 840 G5 Battery (Original 50Wh Tested)</h3>
          <p class="text-xs text-slate-500">Target Budget: <strong class="text-pp-700">₦20,000 - ₦28,000</strong> · Fulfillment: Community Delivery</p>
        </div>

        <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
          <span class="text-xs text-slate-400 font-bold block uppercase">Activity Stats</span>
          <span class="text-lg font-black text-slate-950">2 Responses</span>
        </div>

        <div class="sm:col-span-3 flex flex-col gap-2">
          <a href="{{ route('myrequest.view', ['id' => 'REQ-8390']) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs text-center shadow-xs transition block">
            Manage Request &amp; View Offers →
          </a>
        </div>
      </div>
    </div>

  </div>

</div>