<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('disputes') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Resolution Center
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Case Ref: #DSP-7012</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Dispute Case #DSP-7012</span>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
          FUNDS HELD IN ESCROW (₦85,000)
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Issue: Defective Board on Arrival · Invoice #INV-9079</p>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
        Approve Full Refund to Buyer
      </button>
    </div>
  </div>

  <!-- DISPUTE CASE DETAILS -->
  <div class="grid md:grid-cols-12 gap-6">
    
    <div class="md:col-span-8 space-y-6">
      
      <!-- BUYER CLAIM CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fas fa-exclamation-circle text-rose-600"></i> Buyer Claim Statement (TechSam)
        </h3>

        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
          "The motherboard arrived today via GIG Logistics. Upon connecting the power supply, the board draws no power and gets extremely hot around the power IC. The listing guaranteed a tested working board."
        </p>

        <!-- UPLOADED EVIDENCE GALLERY -->
        <div class="space-y-2">
          <span class="text-xs font-bold text-slate-800">Uploaded Evidence Files (2 Photos / Video):</span>
          <div class="flex items-center gap-3">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200 grid place-items-center text-2xl font-bold">📷</div>
            <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200 grid place-items-center text-2xl font-bold">📹</div>
          </div>
        </div>
      </div>

    </div>

    <div class="md:col-span-4 space-y-6">
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
          Escrow Protection Action
        </h3>
        <p class="text-xs text-slate-600 leading-relaxed">
          Parts &amp; Parcel Escrow protection holds the ₦85,000 payment until both parties agree or mediation is finalized.
        </p>
        <button class="w-full py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
          Propose Partial Settlement
        </button>
      </div>
    </div>

  </div>

</div>