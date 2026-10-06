<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- WARRANTY OVERVIEW HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-blue-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blue-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 border border-blue-200">
          <i class="fas fa-shield-halved"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
            <span>Active Inspection &amp; Item Warranty</span>
            @if ($invoice->isWithinWarranty())
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                Active Coverage
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                Period Elapsed
              </span>
            @endif
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Warranty backed by <strong>{{ $invoice->seller->business_name ?: $invoice->seller->name }}</strong>. Protects against defective hardware after escrow acceptance.
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto">
        @if ($invoice->isWithinWarranty())
          <span class="px-3.5 py-1.5 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-black flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-clock text-emerald-600"></i>
            <span>{{ $invoice->activeWarrantyEndsAt()?->diffForHumans() }} left</span>
          </span>
        @else
          <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">
            Warranty Period Ended
          </span>
        @endif
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- COVERED LINE ITEMS -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
      <i class="fas fa-certificate text-emerald-600"></i> Covered Hardware &amp; Warranty Terms
    </h3>

    <div class="space-y-3 text-xs">
      @forelse ($invoice->items->where('warranty_period_days', '>', 0) as $wItem)
        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="space-y-1">
            <strong class="font-extrabold text-slate-900 block text-sm">{{ $wItem->description }}</strong>
            <div class="flex items-center gap-2 text-[11px] text-slate-500 flex-wrap">
              <span>Coverage Duration: <strong class="text-emerald-700 font-bold">{{ $wItem->warranty_period_days }} Days</strong></span>
              @if ($wItem->warranty_terms)
                <span>· Terms: {{ $wItem->warranty_terms }}</span>
              @endif
            </div>
          </div>

          @if ($isBuyer && $invoice->isWithinWarranty())
            <button wire:click="openWarrantyModal({{ $wItem->id }})" type="button" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 self-start sm:self-auto shrink-0 shadow-2xs cursor-pointer">
              <i class="fas fa-screwdriver-wrench"></i>
              <span>Claim Warranty</span>
            </button>
          @endif
        </div>
      @empty
        <div class="p-4 rounded-xl bg-slate-50 text-slate-500 text-center">
          No items on this commercial invoice have a warranty period specified.
        </div>
      @endforelse
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- SUBMITTED WARRANTY CLAIMS -->
  <!-- ========================================================================= -->
  @php
    $warrantyClaims = $issues->where('type', 'warranty_claim');
  @endphp
  <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-clipboard-list text-blue-600"></i> Submitted Warranty Claims
      </h3>
      <span class="text-xs text-slate-400 font-bold">{{ $warrantyClaims->count() }} claims recorded</span>
    </div>

    <div class="space-y-4">
      @forelse ($warrantyClaims as $wClaim)
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 pb-2">
            <div>
              <strong class="font-extrabold text-slate-900 block">Claim #CLM-{{ str_pad($wClaim->id, 4, '0', STR_PAD_LEFT) }}</strong>
              <span class="text-[11px] text-slate-500">Submitted on {{ $wClaim->created_at->format('M d, Y · h:i A') }}</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase self-start sm:self-auto {{ $wClaim->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($wClaim->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
              {{ Str::headline($wClaim->status) }}
            </span>
          </div>

          <div>
            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px] block">Reported Fault Explanation:</span>
            <p class="text-slate-800 leading-relaxed mt-0.5">{{ $wClaim->description }}</p>
          </div>

          <!-- SELLER WARRANTY RESPONSE ACTIONS -->
          @if ($wClaim->status === 'open' && $isSeller)
            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between gap-3">
              <span class="text-slate-500 text-[11px]">Respond to this warranty claim with replacement or repair decision.</span>
              <button wire:click="openSellerWarrantyModal({{ $wClaim->id }})" type="button" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                Respond to Claim
              </button>
            </div>
          @endif
        </div>
      @empty
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
          No warranty claims have been filed for this invoice.
        </div>
      @endforelse
    </div>
  </div>

</div>
