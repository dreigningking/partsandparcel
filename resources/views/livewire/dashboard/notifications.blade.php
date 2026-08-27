<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Notifications &amp; Activity Alerts</h1>
      <p class="text-xs text-slate-500 mt-0.5">Stay updated on offer counter-proposals, payment releases, and community discussions.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">All Alerts (8)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Transactions</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Offers &amp; Negotiations</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Community</button>
    </div>
  </div>

  <!-- NOTIFICATIONS STREAM -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-bell text-pp-600"></i> Recent Platform Activity
      </h3>
      <button class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">Mark All as Read</button>
    </div>

    <div class="divide-y divide-slate-100">
      
      <!-- NOTIF 1: COUNTER OFFER -->
      <div class="py-4 flex items-start gap-4 hover:bg-slate-50/80 p-3 rounded-2xl transition">
        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 grid place-items-center text-base shrink-0">
          <i class="fas fa-handshake"></i>
        </div>
        <div class="flex-1 space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-extrabold text-slate-900">New Counter-Offer Received</span>
            <span class="text-slate-400 text-[11px]">15 mins ago</span>
          </div>
          <p class="text-xs text-slate-600 leading-snug">
            <strong>Abel Electronics</strong> submitted a counter-offer of <strong>₦80,000</strong> for your request #REQ-8402 (HP EliteBook 840 G5 Motherboard).
          </p>
          <a href="{{ route('offers.view', ['id' => 'OFF-9021']) }}" class="inline-block text-xs font-bold text-pp-600 hover:underline mt-1">Review Counter-Offer →</a>
        </div>
      </div>

      <!-- NOTIF 2: INVOICE PAID -->
      <div class="py-4 flex items-start gap-4 hover:bg-slate-50/80 p-3 rounded-2xl transition">
        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 grid place-items-center text-base shrink-0">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="flex-1 space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-extrabold text-slate-900">Escrow Payment Confirmed</span>
            <span class="text-slate-400 text-[11px]">2 hours ago</span>
          </div>
          <p class="text-xs text-slate-600 leading-snug">
            Invoice <strong>#INV-9082</strong> (₦265,000) was paid by TechSam. Funds are held in Escrow pending parcel delivery.
          </p>
          <a href="{{ route('invoices.view', ['id' => 'INV-9082']) }}" class="inline-block text-xs font-bold text-pp-600 hover:underline mt-1">View Commercial Invoice →</a>
        </div>
      </div>

      <!-- NOTIF 3: COMMUNITY RESPONSE -->
      <div class="py-4 flex items-start gap-4 hover:bg-slate-50/80 p-3 rounded-2xl transition">
        <div class="w-10 h-10 rounded-2xl bg-pp-100 text-pp-700 grid place-items-center text-base shrink-0">
          <i class="fas fa-comment-dots"></i>
        </div>
        <div class="flex-1 space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-extrabold text-slate-900">New Response on Your Community Request</span>
            <span class="text-slate-400 text-[11px]">3 hours ago</span>
          </div>
          <p class="text-xs text-slate-600 leading-snug">
            <strong>Seth Tech Hub</strong> responded to your request: <em>"We have 2 units of HP 840 G5 motherboards available at our shop."</em>
          </p>
          <a href="{{ route('myrequest.view', ['id' => 'REQ-8402']) }}" class="inline-block text-xs font-bold text-pp-600 hover:underline mt-1">View Community Post →</a>
        </div>
      </div>

    </div>
  </div>

</div>