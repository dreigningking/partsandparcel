<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- REFUNDS HEADER & SUMMARY -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-emerald-200 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
          <i class="fas fa-hand-holding-dollar"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-emerald-950 uppercase tracking-wider flex items-center gap-2">
            <span>Escrow Refunds &amp; Capital Reversal Ledger</span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
              Financial Audit
            </span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Transparent breakdown of funds returned to the buyer via escrow reversal or direct bank credit.
          </p>
        </div>
      </div>

      @if ($refunds->isNotEmpty())
        <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-right self-start sm:self-auto">
          <span class="text-[10px] font-bold text-emerald-700 uppercase block">Total Refunded to Buyer</span>
          <strong class="text-lg font-black text-emerald-950 block">
            {{ $invoice->currency_symbol }}{{ number_format($refunds->sum('amount'), 2) }}
          </strong>
        </div>
      @endif
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- REFUNDS LIST -->
  <!-- ========================================================================= -->
  <div class="space-y-4">
    @forelse ($refunds as $refund)
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-900 font-black text-xs uppercase flex items-center gap-1.5">
              <i class="fas fa-arrow-turn-down-left text-emerald-600"></i>
              <span>Refund #REF-{{ str_pad($refund->id, 4, '0', STR_PAD_LEFT) }}</span>
            </span>
            <span class="text-xs text-slate-400">·</span>
            <span class="text-xs text-slate-500">{{ $refund->created_at->format('M d, Y · h:i A') }}</span>
          </div>

          <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $refund->status === 'completed' || $refund->status === 'processed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
              {{ Str::headline($refund->status) }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-1">
            <span class="text-[10px] font-bold text-emerald-700 uppercase block">Refund Amount</span>
            <strong class="text-base font-black text-emerald-950 block">
              {{ $invoice->currency_symbol }}{{ number_format($refund->amount, 2) }}
            </strong>
            <span class="text-[11px] text-slate-500 block">Currency: {{ $invoice->currency }}</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Beneficiary (Recipient)</span>
            <strong class="text-slate-900 font-extrabold block truncate">
              {{ $refund->buyer?->name ?: $invoice->buyer->name }} (Buyer)
            </strong>
            <span class="text-[11px] text-slate-500 block">Escrow wallet / original payment source</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Payment Gateway Reference</span>
            <strong class="text-slate-900 font-mono font-bold block truncate">
              {{ $refund->payment?->reference ?: 'Platform Escrow Reversal' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              Processed: {{ $refund->processed_at ? $refund->processed_at->format('M d, Y') : 'Automated escrow credit' }}
            </span>
          </div>
        </div>

        @if ($refund->reason)
          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px] block">Recorded Reason:</span>
            <p class="text-slate-800 font-medium mt-0.5 leading-relaxed">{{ $refund->reason }}</p>
          </div>
        @endif

        @if ($refund->items && $refund->items->isNotEmpty())
          <div class="space-y-2 text-xs pt-1">
            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px] block">Refunded Line Items:</span>
            <div class="space-y-1">
              @foreach ($refund->items as $rItem)
                <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center justify-between text-xs">
                  <span class="font-bold text-slate-900">{{ $rItem->description ?: 'Item Refund' }}</span>
                  <strong class="text-slate-950 font-black">{{ $invoice->currency_symbol }}{{ number_format($rItem->amount, 2) }}</strong>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>
    @empty
      <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center text-xs space-y-2 shadow-soft">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 grid place-items-center mx-auto text-lg mb-2">
          <i class="fas fa-hand-holding-dollar"></i>
        </div>
        <h4 class="text-sm font-extrabold text-slate-900">No refunds recorded for this invoice</h4>
        <p class="text-slate-500 max-w-md mx-auto">
          Funds are protected in platform escrow. If an order cannot be fulfilled or an agreed return is accepted, refunds will be logged here.
        </p>
      </div>
    @endforelse
  </div>

</div>
