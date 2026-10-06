<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- WARRANTY AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-blue-200 dark:border-blue-900/60 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blue-100 dark:border-blue-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 flex items-center justify-center text-lg shrink-0 border border-blue-200 dark:border-blue-800">
          <i class="fas fa-shield-halved"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-slate-950 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <span>Commercial Warranty Inspection &amp; Claims</span>
            <span class="px-2.5 py-0.5 rounded-full {{ $invoice->isWithinWarranty() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} text-[10px] font-black uppercase">
              {{ $invoice->isWithinWarranty() ? 'Active Coverage' : 'Period Elapsed' }}
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit hardware warranty periods, post-escrow defect claims, and merchant resolution responses.
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto">
        @if ($invoice->isWithinWarranty())
          <span class="px-3 py-1.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-black flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-clock text-emerald-600"></i>
            <span>{{ $invoice->activeWarrantyEndsAt()?->diffForHumans() }} left</span>
          </span>
        @else
          <span class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold">
            Warranty Period Ended
          </span>
        @endif
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- COVERED HARDWARE ITEMS -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
      <i class="fas fa-certificate text-emerald-600"></i> Covered Hardware Line Items
    </h3>

    <div class="space-y-3 text-xs">
      @forelse ($invoice->items->where('warranty_period_days', '>', 0) as $wItem)
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="space-y-1">
            <strong class="font-extrabold text-slate-900 dark:text-white block text-sm">{{ $wItem->description }}</strong>
            <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap">
              <span>Duration: <strong class="text-emerald-700 dark:text-emerald-400 font-bold">{{ $wItem->warranty_period_days }} Days</strong></span>
              @if ($wItem->warranty_terms)
                <span>· Terms: {{ $wItem->warranty_terms }}</span>
              @endif
            </div>
          </div>

          <span class="px-3 py-1 rounded-full text-xs font-black uppercase self-start sm:self-auto {{ $invoice->isWithinWarranty() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
            {{ $invoice->isWithinWarranty() ? 'Active Warranty Coverage' : 'Warranty Elapsed' }}
          </span>
        </div>
      @empty
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-slate-500 text-center">
          No hardware items on this invoice have a warranty period specified.
        </div>
      @endforelse
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- SUBMITTED WARRANTY CLAIMS -->
  <!-- ========================================================================= -->
  @php
    $claimsList = $warrantyClaims ?? collect();
  @endphp
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
      <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-clipboard-list text-blue-600"></i> Submitted Warranty Claims
      </h3>
      <span class="text-xs text-slate-400 font-bold">{{ $claimsList->count() }} claims recorded</span>
    </div>

    <div class="space-y-4">
      @forelse ($claimsList as $wClaim)
        @php
          $adminWarrantyClaimStatus = match($wClaim->status) {
            'accepted', 'resolved' => [
              'label' => 'Warranty Claim Accepted by Seller',
              'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
              'icon' => 'fa-circle-check text-emerald-600',
            ],
            'rejected', 'disputed' => [
              'label' => 'Warranty Claim Denied / In Dispute',
              'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
              'icon' => 'fa-scale-balanced text-rose-600',
            ],
            default => [
              'label' => 'Waiting for Seller Warranty Response',
              'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
              'icon' => 'fa-clock text-amber-600',
            ],
          };
        @endphp

        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3 text-xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-2">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-mono font-bold text-slate-900 dark:text-white">Claim #CLM-{{ str_pad($wClaim->id, 4, '0', STR_PAD_LEFT) }}</span>
              <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold uppercase">{{ Str::headline($wClaim->claim_type ?: 'Defect') }}</span>
              <span class="text-slate-400">·</span>
              <span class="text-[11px] text-slate-500">Submitted {{ $wClaim->created_at->format('M d, Y · h:i A') }}</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $adminWarrantyClaimStatus['badge'] }}">
              {{ $adminWarrantyClaimStatus['label'] }}
            </span>
          </div>

          <div>
            <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Reported Hardware Fault:</span>
            <p class="text-slate-800 dark:text-slate-200 leading-relaxed mt-0.5 font-medium">{{ $wClaim->description }}</p>
          </div>

          @if ($wClaim->seller_notes)
            <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/60 space-y-1">
              <span class="font-black uppercase text-blue-900 dark:text-blue-300 text-[10px] block">Seller Decision &amp; Notes</span>
              <p class="text-blue-950 dark:text-blue-200 font-medium">{{ $wClaim->seller_notes }}</p>
            </div>
          @endif
        </div>
      @empty
        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500">
          No warranty claims have been filed for this invoice.
        </div>
      @endforelse
    </div>
  </div>

</div>
