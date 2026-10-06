<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- REFUNDS AUDIT HEADER -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-emerald-200 dark:border-emerald-900/60 p-6 sm:p-7 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-100 dark:border-emerald-900/40 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0 border border-emerald-200 dark:border-emerald-800">
          <i class="fas fa-hand-holding-dollar"></i>
        </div>
        <div>
          <h2 class="text-sm font-black text-emerald-950 dark:text-emerald-300 uppercase tracking-wider flex items-center gap-2">
            <span>Escrow Refunds &amp; Capital Reversal Ledger</span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-black uppercase">
              Admin Financial Audit
            </span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Audit capital returned to the buyer via escrow cancellation, mediation awards, or direct bank refunds.
          </p>
        </div>
      </div>

      @if ($refunds->isNotEmpty())
        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-right self-start sm:self-auto">
          <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase block">Total Refund Capital Reversed</span>
          <strong class="text-lg font-black text-emerald-950 dark:text-white block font-mono">
            {{ $invoice->currency_symbol }}{{ number_format($refunds->sum('amount'), 2) }}
          </strong>
        </div>
      @endif
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- REFUNDS LIST -->
  <!-- ========================================================================= -->
  @if ($refunds->isEmpty())
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center space-y-2 shadow-soft">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
        <i class="fas fa-money-bill-transfer"></i>
      </div>
      <h3 class="text-sm font-black text-slate-900 dark:text-white">No Refunds Recorded</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto">There are no escrow reversals or buyer refund payments on record for this invoice.</p>
    </div>
  @else
    @foreach ($refunds as $refund)
      @php
        $adminRefundStatus = match($refund->status) {
          'completed', 'processed' => [
            'label' => 'Refund Processed to Buyer',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'icon' => 'fa-circle-check text-emerald-600',
          ],
          'failed', 'rejected' => [
            'label' => 'Refund Failed / Rejected',
            'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
            'icon' => 'fa-times-circle text-rose-600',
          ],
          default => [
            'label' => 'Pending Escrow Reversal',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'icon' => 'fa-clock text-amber-600',
          ],
        };
      @endphp

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
        
        <!-- HEADER WITH ADMIN STATUS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-base shrink-0">
              <i class="fas {{ $adminRefundStatus['icon'] }}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 font-black text-xs uppercase flex items-center gap-1.5">
                  <i class="fas fa-arrow-turn-down-left text-emerald-600"></i>
                  <span>#REF-{{ str_pad($refund->id, 4, '0', STR_PAD_LEFT) }}</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminRefundStatus['badge'] }}">
                  {{ $adminRefundStatus['label'] }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Initiated {{ $refund->created_at->format('M d, Y · h:i A') }}
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800 space-y-1">
            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase block">Reversal Amount</span>
            <strong class="text-base font-black text-emerald-950 dark:text-white block font-mono">
              {{ $invoice->currency_symbol }}{{ number_format($refund->amount, 2) }}
            </strong>
            <span class="text-[11px] text-slate-500 block">Currency: {{ $invoice->currency }}</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Beneficiary (Buyer)</span>
            <strong class="text-slate-900 dark:text-white font-extrabold block truncate">
              {{ $refund->buyer?->name ?: ($invoice->buyer?->name ?? 'Buyer') }}
            </strong>
            <span class="text-[11px] text-slate-500 block truncate">Original escrow payment wallet / bank</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">Transaction Reference</span>
            <strong class="text-slate-900 dark:text-white font-mono font-bold block truncate">
              {{ $refund->payment?->reference ?: 'Platform Escrow Reversal' }}
            </strong>
            <span class="text-[11px] text-slate-500 block">
              Processed: {{ $refund->processed_at ? $refund->processed_at->format('M d, Y') : 'Automated escrow ledger' }}
            </span>
          </div>
        </div>

        @if ($refund->reason)
          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-xs">
            <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Recorded Reason / Resolution:</span>
            <p class="text-slate-800 dark:text-slate-200 font-medium mt-0.5 leading-relaxed">{{ $refund->reason }}</p>
          </div>
        @endif

        @if ($refund->items && $refund->items->isNotEmpty())
          <div class="space-y-1.5 pt-1 text-xs">
            <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Refunded Line Items:</span>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden">
              @foreach ($refund->items as $rItem)
                <div class="p-3 bg-white dark:bg-slate-900 flex items-center justify-between">
                  <div>
                    <strong class="text-slate-900 dark:text-white block font-bold">{{ $rItem->invoiceItem?->description ?: 'Refund Item' }}</strong>
                    <span class="text-[11px] text-slate-500">Qty: {{ $rItem->quantity }}</span>
                  </div>
                  <strong class="text-emerald-600 dark:text-emerald-400 font-black">
                    {{ $invoice->currency_symbol }}{{ number_format($rItem->amount, 2) }}
                  </strong>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>
    @endforeach
  @endif

</div>
