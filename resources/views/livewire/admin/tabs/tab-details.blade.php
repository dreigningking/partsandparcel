<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- ADMIN ESCROW & TRANSACTION STATUS OVERVIEW BANNER -->
  <!-- ========================================================================= -->
  @php
    $adminEscrowStatus = match(true) {
      $invoice->status === 'cancelled' => [
        'label' => 'Invoice Cancelled',
        'badge' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
        'desc' => 'This commercial invoice has been cancelled. No funds or settlement active.',
        'icon' => 'fa-ban text-rose-600',
      ],
      $invoice->status === 'refunded' => [
        'label' => 'Refund Processed to Buyer',
        'badge' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300',
        'desc' => 'Escrow funds or direct settlement reversed and returned to the buyer.',
        'icon' => 'fa-arrow-turn-down-left text-purple-600',
      ],
      $invoice->payment_method === 'direct' && $invoice->status === 'paid' => [
        'label' => 'Paid Directly to Seller (No Escrow Protection)',
        'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
        'desc' => 'Buyer paid seller outside platform escrow via direct bank transfer.',
        'icon' => 'fa-building-columns text-amber-600',
      ],
      ! in_array($invoice->status, ['paid', 'accepted']) => [
        'label' => 'Waiting for Buyer Payment',
        'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
        'desc' => 'Awaiting payment transfer or escrow deposit from the buyer.',
        'icon' => 'fa-clock text-slate-500',
      ],
      $settlement && $settlement->status === 'settled' => [
        'label' => 'Settlement Settled to Seller',
        'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
        'desc' => 'Commercial transaction completed. Net funds disbursed to seller account.',
        'icon' => 'fa-circle-check text-emerald-600',
      ],
      $settlement && $settlement->status === 'eligible' => [
        'label' => 'Waiting for Admin / Finance Payout Approval',
        'badge' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300',
        'desc' => 'Buyer inspection completed. Seller settlement is eligible for final disbursement.',
        'icon' => 'fa-hourglass-half text-blue-600',
      ],
      default => [
        'label' => 'Escrow Held - Waiting for Delivery & Inspection',
        'badge' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
        'desc' => 'Funds are locked securely in platform escrow. Waiting for shipment delivery and buyer inspection.',
        'icon' => 'fa-shield-halved text-amber-600',
      ],
    };
  @endphp

  <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
      <div class="w-11 h-11 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-lg shrink-0">
        <i class="fas {{ $adminEscrowStatus['icon'] }}"></i>
      </div>
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-xs font-black uppercase text-slate-400">Admin Oversight Status:</span>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $adminEscrowStatus['badge'] }}">
            {{ $adminEscrowStatus['label'] }}
          </span>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $adminEscrowStatus['desc'] }}</p>
      </div>
    </div>

    <div class="text-right self-start sm:self-auto shrink-0">
      <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Commercial Volume</span>
      <strong class="text-xl font-black text-slate-950 dark:text-white block font-mono">
        {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}
      </strong>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- LINE ITEMS TABLE -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
      <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list text-pp-600"></i> Invoice Items &amp; Services
      </h3>
      <span class="text-xs text-slate-400 font-bold">{{ $invoice->items->count() }} items listed</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
          <tr>
            <th class="p-3">Description</th>
            <th class="p-3">Type</th>
            <th class="p-3 text-center">Qty</th>
            <th class="p-3 text-right">Unit Price</th>
            <th class="p-3">Warranty</th>
            <th class="p-3 text-right">Total Price</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
          @forelse ($invoice->items as $item)
            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
              <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $item->description }}</td>
              <td class="p-3">
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 text-[9px] font-black uppercase">
                  {{ $item->type ?: 'ITEM' }}
                </span>
              </td>
              <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
              <td class="p-3 text-right font-extrabold">{{ $invoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
              <td class="p-3">
                @if ($item->warranty_period_days)
                  <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 text-[10px] font-extrabold">
                    {{ $item->warranty_period_days }} Days
                  </span>
                @else
                  <span class="text-slate-400">N/A</span>
                @endif
              </td>
              <td class="p-3 text-right font-black text-slate-950 dark:text-white">{{ $invoice->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-6 text-center text-slate-400">No line items recorded on this commercial invoice.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- TOTALS BREAKDOWN -->
    <div class="grid sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
      <div class="p-4 rounded-2xl {{ $invoice->payment_method === 'platform' ? 'bg-pp-50/60 border border-pp-200/80 dark:bg-pp-950/30 dark:border-pp-900/60' : 'bg-amber-50/60 border border-amber-200/80 dark:bg-amber-950/30 dark:border-amber-900/60' }} space-y-2">
        <span class="font-bold {{ $invoice->payment_method === 'platform' ? 'text-pp-900 dark:text-pp-300' : 'text-amber-900 dark:text-amber-300' }} flex items-center gap-1.5">
          <i class="fas {{ $invoice->payment_method === 'platform' ? 'fa-shield-halved text-pp-600' : 'fa-building-columns text-amber-600' }}"></i>
          <span>{{ $invoice->payment_method === 'platform' ? 'Platform Escrow Protection Terms' : 'Direct Seller Bank Transfer Terms' }}</span>
        </span>
        <p class="text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
          {{ $invoice->payment_method === 'platform' ? 'Buyer payment is locked in escrow. Payout to seller will be triggered once buyer inspection period elapses or package is accepted.' : 'Payment is transacted directly between buyer and seller outside platform escrow protection.' }}
        </p>
      </div>

      <div class="space-y-1.5 text-right">
        <div class="flex justify-between text-slate-600 dark:text-slate-400">
          <span>Subtotal:</span>
          <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if ($invoice->discount > 0)
          <div class="flex justify-between text-pp-600 font-bold">
            <span>Agreed Discount:</span>
            <span>-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount, 2) }}</span>
          </div>
        @endif
        @if ($invoice->commission > 0)
          <div class="flex justify-between text-emerald-600 font-bold">
            <span>Platform Commission:</span>
            <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->commission, 2) }}</span>
          </div>
        @endif
        <div class="border-t border-slate-200 dark:border-slate-800 pt-2 flex justify-between items-baseline text-base">
          <span class="font-bold text-slate-900 dark:text-white">Total Commercial Price:</span>
          <span class="text-2xl font-black text-slate-950 dark:text-white">{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- FINANCIAL AUDIT & SETTLEMENT LEDGER -->
  <!-- ========================================================================= -->
  <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 text-white shadow-soft space-y-4">
    <div class="flex items-center justify-between border-b border-white/10 pb-3">
      <h3 class="text-sm font-black text-white flex items-center gap-2">
        <i class="fas fa-arrows-split-up-and-left text-pp-400"></i>
        <span>Financial Settlement &amp; Audit Trail</span>
      </h3>
      <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-pp-500/20 text-pp-300 border border-pp-400/30">
        Escrow State: {{ $settlement ? ucfirst($settlement->status) : ($invoice->status === 'paid' ? 'Held' : 'Unpaid') }}
      </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
      <!-- Pillar 1: Commercial Invoice -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">1. Commercial Invoice</span>
        <strong class="text-white block font-mono">{{ $invoice->invoice_number }}</strong>
        <span class="text-[11px] text-slate-300 block">Gross Total: {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</span>
        <span class="text-[10px] text-slate-400 block">Created: {{ $invoice->created_at->format('M d, Y') }}</span>
      </div>

      <!-- Pillar 2: Payment Record -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">2. Inflow Payment Transaction</span>
        @if ($payment)
          <strong class="text-emerald-400 block font-mono text-[11px] truncate">Ref: {{ $payment->reference }}</strong>
          <span class="text-[11px] text-slate-300 block">Gateway: {{ ucfirst($payment->provider) }} · Status: {{ $payment->status }}</span>
          <span class="text-[10px] text-slate-400 block">Paid at: {{ $payment->paid_at ? $payment->paid_at->format('M d, Y · h:i A') : 'Recorded' }}</span>
        @else
          <span class="text-slate-400 italic block">No payment gateway transaction record</span>
          <span class="text-[10px] text-slate-500 block">Payment method: {{ ucfirst($invoice->payment_method) }}</span>
        @endif
      </div>

      <!-- Pillar 3: Settlement Record -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">3. Seller Settlement Ledger</span>
        @if ($settlement)
          <strong class="text-white block font-mono">#SET-{{ str_pad($settlement->id, 5, '0', STR_PAD_LEFT) }}</strong>
          <span class="text-[11px] text-emerald-400 block font-bold">Net Payout: {{ $settlement->currency_symbol ?? $invoice->currency_symbol }}{{ number_format($settlement->amount ?? $settlement->net_amount ?? 0, 2) }}</span>
          <span class="text-[10px] text-slate-400 block">Status: {{ ucfirst($settlement->status) }}</span>
        @else
          <span class="text-slate-400 italic block">Pending settlement creation</span>
        @endif
      </div>
    </div>
  </div>

  <!-- SELLER BANK DETAILS (FOR AUDIT) -->
  @php
    $sellerAcc = $invoice->seller?->bankAccounts?->first();
  @endphp
  @if ($sellerAcc)
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-soft space-y-2 text-xs">
      <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
        <i class="fas fa-building-columns text-amber-600"></i> Seller Bank Account on File (For Settlement Disbursement)
      </span>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
          <span class="text-[10px] text-slate-400 block uppercase">Bank Name</span>
          <strong class="text-slate-900 dark:text-white">{{ $sellerAcc->bank_name }}</strong>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
          <span class="text-[10px] text-slate-400 block uppercase">Account Name</span>
          <strong class="text-slate-900 dark:text-white">{{ $sellerAcc->account_name }}</strong>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
          <span class="text-[10px] text-slate-400 block uppercase">Account Number</span>
          <strong class="text-pp-600 font-mono text-sm tracking-wider">{{ $sellerAcc->account_number }}</strong>
        </div>
      </div>
    </div>
  @endif

</div>
