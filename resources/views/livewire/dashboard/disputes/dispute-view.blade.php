<div class="flex flex-col gap-6">

  @if (! $dispute)
    <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center space-y-3 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 grid place-items-center mx-auto text-lg">
        <i class="fas fa-scale-balanced"></i>
      </div>
      <h3 class="text-base font-extrabold text-slate-900">Dispute Case Not Found</h3>
      <p class="text-xs text-slate-500">The requested dispute case does not exist or you do not have permission to view it.</p>
      <a href="{{ route('disputes') }}" class="inline-flex px-4 py-2 rounded-xl bg-pp-600 text-white font-extrabold text-xs">
        Return to Resolution Center
      </a>
    </div>
  @else
    <!-- TOP NAV & HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
          <a href="{{ route('disputes') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
            <i class="fas fa-arrow-left text-[10px]"></i> Back to Resolution Center
          </a>
          <span class="text-slate-300">·</span>
          <span class="text-xs font-extrabold text-slate-500 font-mono">Case Ref: #DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}</span>
          <span class="text-slate-300">·</span>
          <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-black uppercase">
            Origin: {{ $dispute->originCategory() }}
          </span>
          <span class="px-2 py-0.5 rounded-lg bg-purple-50 text-purple-800 text-[10px] font-extrabold">
            {{ $dispute->typeLabel() }}
          </span>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
          <span>Case #DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}</span>
          @if ($dispute->status === 'resolved')
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-extrabold text-[10px] uppercase">
              RESOLVED
            </span>
          @else
            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase flex items-center gap-1">
              <i class="fas fa-lock text-[9px]"></i> FUNDS HELD IN ESCROW ({{ $dispute->invoice?->currency_symbol }}{{ number_format($dispute->invoice?->total ?? 0) }})
            </span>
          @endif
        </h1>

        <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
          <span>Opened {{ $dispute->created_at->format('M d, Y · h:i A') }}</span>
          @if ($dispute->invoice)
            <span class="text-slate-300">·</span>
            <span>Related Invoice: <a href="{{ route('invoices.view', $dispute->invoice->id) }}?tab=dispute" class="text-pp-600 font-bold hover:underline">#INV-{{ $dispute->invoice->invoice_number }}</a></span>
          @endif
        </p>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
        @if ($dispute->invoice)
          <a href="{{ route('invoices.view', $dispute->invoice->id) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs shadow-2xs transition flex items-center gap-1.5">
            <i class="fas fa-file-invoice text-pp-600"></i>
            <span>View Full Invoice</span>
          </a>
        @endif
      </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if (session()->has('resolution_success'))
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ session('resolution_success') }}</span>
      </div>
    @endif

    <!-- DISPUTE CASE DETAILS -->
    <div class="grid md:grid-cols-12 gap-6">
      
      <div class="md:col-span-8 space-y-6">
        
        <!-- COMPLAINANT CLAIM CARD -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-exclamation-circle text-rose-600"></i> Complainant Claim ({{ $dispute->opener?->name ?: 'Complainant' }})
            </h3>
            <span class="text-[11px] text-slate-400 font-medium">Filed {{ $dispute->created_at->diffForHumans() }}</span>
          </div>

          <div class="space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Official Statement:</span>
            <p class="text-xs text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200/80 font-medium">
              "{{ $dispute->reason }}"
            </p>
          </div>

          <!-- UPLOADED EVIDENCE GALLERY -->
          @if ($dispute->evidence)
            <div class="space-y-2 pt-2 border-t border-slate-100">
              <span class="text-xs font-bold text-slate-800">Submitted Evidence:</span>
              <div class="p-3.5 rounded-xl bg-purple-50/60 border border-purple-200 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 truncate">
                  <i class="fas fa-paperclip text-purple-600"></i>
                  <span class="font-mono text-slate-700 truncate">{{ $dispute->evidence }}</span>
                </div>
                <a href="{{ $dispute->evidence }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-[11px] shrink-0 transition flex items-center gap-1">
                  <span>Open Evidence</span>
                  <i class="fas fa-external-link-alt text-[9px]"></i>
                </a>
              </div>
            </div>
          @endif
        </div>

        <!-- CONTEXT CARD BASED ON ORIGIN -->
        @if ($dispute->warrantyClaim)
          <div class="bg-white rounded-3xl border border-blue-200 p-6 space-y-3 shadow-soft">
            <h3 class="text-xs font-extrabold text-blue-950 uppercase tracking-wider border-b border-blue-100 pb-2.5 flex items-center gap-2">
              <i class="fas fa-shield-halved text-blue-600"></i> Associated Warranty Claim Context
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Claim Reference</span>
                <strong class="text-slate-900 block font-mono">#CLM-{{ str_pad($dispute->warrantyClaim->id, 4, '0', STR_PAD_LEFT) }}</strong>
                <span class="text-[11px] text-slate-500">Type: {{ Str::headline($dispute->warrantyClaim->claim_type) }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Target Item</span>
                <strong class="text-slate-900 block truncate">{{ $dispute->warrantyClaim->item?->description ?: ($dispute->warrantyClaim->invoiceItem?->description ?: 'Warranted Unit') }}</strong>
                <span class="text-[11px] text-slate-500">Status: {{ Str::headline($dispute->warrantyClaim->status) }}</span>
              </div>
            </div>
            @if ($dispute->warrantyClaim->seller_notes)
              <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                <span class="text-[10px] text-amber-900 font-extrabold uppercase block">Seller Denial Statement:</span>
                <p class="text-amber-950 mt-0.5 leading-relaxed">{{ $dispute->warrantyClaim->seller_notes }}</p>
              </div>
            @endif
          </div>
        @elseif ($dispute->replacement)
          <div class="bg-white rounded-3xl border border-amber-200 p-6 space-y-3 shadow-soft">
            <h3 class="text-xs font-extrabold text-amber-950 uppercase tracking-wider border-b border-amber-100 pb-2.5 flex items-center gap-2">
              <i class="fas fa-repeat text-amber-600"></i> Replacement Unit Context
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Replacement Ref</span>
                <strong class="text-slate-900 block font-mono">#REP-{{ str_pad($dispute->replacement->id, 4, '0', STR_PAD_LEFT) }}</strong>
                <span class="text-[11px] text-slate-500">Tracking: {{ $dispute->replacement->shipment?->tracking_number ?: 'Pending' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Delivery Method</span>
                <strong class="text-slate-900 block uppercase">{{ $dispute->replacement->delivery_method ?: 'Shipment' }}</strong>
                <span class="text-[11px] text-slate-500">Status: {{ Str::headline($dispute->replacement->status) }}</span>
              </div>
            </div>
            @if ($dispute->replacement->rejection_reason)
              <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs">
                <span class="text-[10px] text-rose-900 font-extrabold uppercase block">Buyer Rejection Reason:</span>
                <p class="text-rose-950 mt-0.5 leading-relaxed">{{ $dispute->replacement->rejection_reason }}</p>
              </div>
            @endif
          </div>
        @elseif ($dispute->returnRecord)
          <div class="bg-white rounded-3xl border border-indigo-200 p-6 space-y-3 shadow-soft">
            <h3 class="text-xs font-extrabold text-indigo-950 uppercase tracking-wider border-b border-indigo-100 pb-2.5 flex items-center gap-2">
              <i class="fas fa-box-tissue text-indigo-600"></i> Return Condition Context
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Return Record</span>
                <strong class="text-slate-900 block font-mono">#RET-{{ str_pad($dispute->returnRecord->id, 4, '0', STR_PAD_LEFT) }}</strong>
                <span class="text-[11px] text-slate-500">Waybill: {{ $dispute->returnRecord->shipment?->tracking_number ?: 'Pending' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Delivery Method</span>
                <strong class="text-slate-900 block uppercase">{{ $dispute->returnRecord->delivery_method ?: 'Courier' }}</strong>
                <span class="text-[11px] text-slate-500">Status: {{ Str::headline($dispute->returnRecord->status) }}</span>
              </div>
            </div>
            @if ($dispute->returnRecord->rejection_reason)
              <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs">
                <span class="text-[10px] text-rose-900 font-extrabold uppercase block">Seller Rejection Findings:</span>
                <p class="text-rose-950 mt-0.5 leading-relaxed">{{ $dispute->returnRecord->rejection_reason }}</p>
              </div>
            @endif
          </div>
        @elseif ($dispute->issue)
          <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-3 shadow-soft">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2.5 flex items-center gap-2">
              <i class="fas fa-circle-exclamation text-rose-600"></i> Initial Issue Statement
            </h3>
            <p class="text-xs text-slate-700 bg-slate-50 p-3.5 rounded-xl border border-slate-100 leading-relaxed">
              "{{ $dispute->issue->description }}"
            </p>
          </div>
        @endif

        <!-- MEDIATOR FINAL RESOLUTION (IF RESOLVED) -->
        @if ($dispute->resolution)
          <div class="bg-white rounded-3xl border border-emerald-200 p-6 space-y-3 shadow-soft">
            <h3 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wider border-b border-emerald-100 pb-2.5 flex items-center gap-2">
              <i class="fas fa-check-circle text-emerald-600"></i> Official Mediator Resolution
            </h3>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-2">
              <p class="text-emerald-950 leading-relaxed font-medium">
                {{ $dispute->resolution }}
              </p>
              <div class="flex items-center gap-3 text-[11px] text-emerald-800 pt-2 border-t border-emerald-200/60 flex-wrap">
                <span>Arbitrated by: <strong>{{ $dispute->resolver?->name ?: 'Platform Support' }}</strong></span>
                @if ($dispute->resolved_at)
                  <span>·</span>
                  <span>Resolved on {{ $dispute->resolved_at->format('M d, Y · h:i A') }}</span>
                @endif
              </div>
            </div>
          </div>
        @endif

        <!-- ADMIN ARBITRATION RESOLUTION PANEL -->
        @if ($isAdmin && $dispute->status !== 'resolved')
          <div class="bg-white rounded-3xl border border-purple-200 p-6 space-y-4 shadow-soft">
            <div class="flex items-center justify-between border-b border-purple-100 pb-3">
              <h3 class="text-xs font-extrabold text-purple-950 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-gavel text-purple-600"></i> Admin Arbitration Panel
              </h3>
              <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 text-[10px] font-black uppercase">
                Admin Power Active
              </span>
            </div>

            <div class="space-y-3 text-xs">
              <div>
                <label class="font-bold text-slate-700 block mb-1">Arbitration Decision *</label>
                <select wire:model="adminDecision" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-purple-500 outline-none">
                  <option value="buyer_favor">Rule in Buyer Favor (Authorize Full / Partial Refund)</option>
                  <option value="seller_favor">Rule in Seller Favor (Release Escrow Funds to Seller)</option>
                  <option value="split">Split Settlement (Custom Distribution)</option>
                </select>
              </div>

              @if ($adminDecision === 'buyer_favor' || $adminDecision === 'split')
                <div>
                  <label class="font-bold text-slate-700 block mb-1">Refund Amount (Leave empty for full order total)</label>
                  <input type="number" step="0.01" wire:model="adminRefundAmount" placeholder="e.g. {{ $dispute->invoice?->total }}" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-purple-500 outline-none" />
                </div>
              @endif

              <div>
                <label class="font-bold text-slate-700 block mb-1">Arbitration Rationale &amp; Notes *</label>
                <textarea wire:model="adminResolutionNotes" rows="3" placeholder="Explain the legal or evidence-based basis for this arbitration ruling..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:border-purple-500 outline-none"></textarea>
                @error('adminResolutionNotes') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
              </div>

              <div class="pt-2">
                <button wire:click="adminResolve" type="button" class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                  <i class="fas fa-gavel"></i>
                  <span>Execute Binding Arbitration Decision</span>
                </button>
              </div>
            </div>
          </div>
        @endif

      </div>

      <!-- SIDEBAR -->
      <div class="md:col-span-4 space-y-6">
        
        <!-- PARTIES INVOLVED CARD -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft text-xs">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fas fa-users text-slate-400"></i> Disputing Parties
          </h3>

          <div class="space-y-3">
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Complainant (Opened By)</span>
              <strong class="text-slate-900 block text-sm">{{ $dispute->opener?->name }}</strong>
              <span class="text-[11px] text-slate-500 block">{{ $dispute->opener?->email }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold text-slate-400 uppercase block">Respondent</span>
              <strong class="text-slate-900 block text-sm">{{ $dispute->respondent?->name ?: 'Counterparty' }}</strong>
              <span class="text-[11px] text-slate-500 block">{{ $dispute->respondent?->email }}</span>
            </div>
          </div>
        </div>

        <!-- ESCROW SECURITY CARD -->
        <div class="bg-white rounded-3xl border border-purple-200 p-6 space-y-4 shadow-soft text-xs">
          <h3 class="text-xs font-extrabold text-purple-950 uppercase tracking-wider border-b border-purple-100 pb-3 flex items-center gap-2">
            <i class="fas fa-shield-halved text-purple-600"></i> Escrow Protection Guard
          </h3>
          <p class="text-slate-600 leading-relaxed">
            Parts &amp; Parcel Escrow safely holds <strong>{{ $dispute->invoice?->currency_symbol }}{{ number_format($dispute->invoice?->total ?? 0) }}</strong> until mediation is finalized. Neither party can withdraw or siphon funds prematurely.
          </p>
          <div class="p-3 rounded-xl bg-purple-50 text-purple-900 text-[11px] font-medium">
            <i class="fas fa-info-circle mr-1"></i> Payout settlements will be unblocked and disbursed immediately upon arbitrator decision.
          </div>
        </div>

      </div>

    </div>
  @endif

</div>