<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- ISSUES HEADER & ESCROW ALERT -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-rose-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rose-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0 border border-rose-200">
          <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-rose-950 uppercase tracking-wider flex items-center gap-2">
            <span>Reported Issues &amp; Inspection Claims</span>
            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 text-[10px] font-black uppercase">
              Escrow Protection Active
            </span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Friction resolution workspace. Escrow funds remain frozen until both buyer and seller agree on a remedy.
          </p>
        </div>
      </div>

      <!-- REPORT ISSUE BUTTON FOR BUYER -->
      @if ($isBuyer && in_array($invoice->status, ['paid', 'accepted']))
        <button wire:click="openIssueModal" type="button" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 self-start sm:self-auto cursor-pointer">
          <i class="fas fa-flag"></i>
          <span>Report New Issue</span>
        </button>
      @endif
    </div>

    <!-- ESCROW FROZEN NOTICE BANNER -->
    <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200 flex items-center gap-3 text-xs text-rose-950">
      <i class="fas fa-shield-halved text-rose-600 text-base shrink-0"></i>
      <div>
        <strong class="font-black block">Escrow Payout Protection</strong>
        <span class="text-rose-900 text-[11px]">
          Funds for this transaction are securely held on Parts &amp; Parcel until the reported issue is resolved amicably via replacement, return, or refund.
        </span>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- ISSUES LIST -->
  <!-- ========================================================================= -->
  <div class="space-y-4">
    @forelse ($issues as $issue)
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        
        <!-- ISSUE HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-xl bg-rose-100 text-rose-900 font-black text-xs uppercase flex items-center gap-1.5">
              <i class="fas fa-circle-exclamation text-rose-600"></i>
              <span>{{ Str::headline($issue->type) }}</span>
            </span>
            <span class="text-xs font-mono font-bold text-slate-400">#ISS-{{ str_pad($issue->id, 4, '0', STR_PAD_LEFT) }}</span>
            <span class="text-slate-300">·</span>
            <span class="text-xs text-slate-500 font-medium">Reported {{ $issue->created_at->format('M d, Y · h:i A') }}</span>
          </div>

          <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $issue->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($issue->status === 'open' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
              Status: {{ Str::headline($issue->status) }}
            </span>
          </div>
        </div>

        <!-- BUYER EXPLANATION -->
        <div class="space-y-1 text-xs">
          <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Buyer Statement:</span>
          <p class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-800 leading-relaxed">
            "{{ $issue->description }}"
          </p>
        </div>

        <!-- AFFECTED LINE ITEMS -->
        @if ($issue->items->isNotEmpty())
          <div class="space-y-2 text-xs">
            <span class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Affected Line Items:</span>
            <div class="space-y-1.5">
              @foreach ($issue->items as $issueItem)
                <div class="p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                  <div>
                    <strong class="text-slate-900 block font-bold">{{ $issueItem->invoiceItem?->description ?: 'Line item' }}</strong>
                    <span class="text-[11px] text-slate-500">Reason: {{ Str::headline($issueItem->reason) }}</span>
                  </div>
                  @if ($issueItem->evidence)
                    <a href="{{ $issueItem->evidence }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center gap-1 shrink-0">
                      <i class="fas fa-paperclip text-[10px]"></i> Evidence
                    </a>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- SELLER RESOLUTION PROPOSAL CARD -->
        @if ($issue->status === 'open' && $isSeller)
          <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
              <strong class="font-black text-amber-950 block">Action Required as Seller</strong>
              <span class="text-amber-900 text-[11px]">
                Review the buyer's issue report and propose an acceptable remedy (send replacement or issue refund).
              </span>
            </div>
            <button wire:click="openSellerIssueResponseModal({{ $issue->id }})" type="button" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer">
              <i class="fas fa-reply"></i>
              <span>Propose Resolution</span>
            </button>
          </div>
        @elseif ($issue->returnRecord || $issue->replacement)
          <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200 text-xs space-y-2">
            <div class="flex items-center justify-between">
              <strong class="font-black text-indigo-950 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                <i class="fas fa-handshake-angle text-indigo-600"></i> Agreed Remedy Proposal
              </strong>
              @if ($issue->replacement)
                <button wire:click="switchTab('replacement')" type="button" class="text-indigo-600 hover:underline font-extrabold text-[11px]">
                  View Replacements Tab →
                </button>
              @endif
            </div>
            <p class="text-indigo-900 leading-relaxed">
              @if ($issue->replacement)
                Seller agreed to dispatch a <strong>Replacement Unit</strong>.
              @else
                Seller agreed to an <strong>Escrow Refund</strong>.
              @endif
              @if ($issue->returnRecord)
                Item return required via {{ strtoupper($issue->returnRecord->delivery_method ?? 'shipment') }}.
              @endif
            </p>
          </div>
        @endif

      </div>
    @empty
      <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center text-xs space-y-2 shadow-soft">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 grid place-items-center mx-auto text-lg mb-2">
          <i class="fas fa-check-circle"></i>
        </div>
        <h4 class="text-sm font-extrabold text-slate-900">No issues reported for this invoice</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          Both parties are satisfied. If any defects, wrong items, or damages occur during shipping or inspection, issues can be recorded here.
        </p>
      </div>
    @endforelse
  </div>

</div>
