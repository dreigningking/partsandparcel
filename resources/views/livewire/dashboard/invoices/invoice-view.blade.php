<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('invoices') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Invoices
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Commercial Invoice</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Invoice #{{ $invoice->invoice_number ?? 'INV-9082' }}</span>
        
        @if (($invoice->status ?? '') === 'paid')
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-check-circle"></i> PAID {{ ($invoice->payment_method ?? '') === 'direct' ? '(DIRECT TRANSFER)' : '(ESCROW PROTECTED)' }}
          </span>
        @elseif (($invoice->status ?? '') === 'cancelled')
          <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-ban"></i> CANCELLED (NOT PURCHASED)
          </span>
        @else
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-clock"></i> PAYMENT PENDING
          </span>
        @endif
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Issued on {{ optional($invoice->issued_at ?? $invoice->created_at)->format('M d, Y') ?? 'Recent' }}
        · Payment Method: <strong class="text-slate-800 uppercase">{{ ($invoice->payment_method ?? 'platform') === 'direct' ? 'Direct Bank Transfer' : 'Parts & Parcel Platform Escrow' }}</strong>
      </p>
    </div>

    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-print"></i> Print Invoice
      </button>
      @if($invoice && $invoice->id)
        <a href="{{ route('invoices.export.pdf', $invoice->id) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
          <i class="fas fa-download"></i> Download PDF
        </a>
      @endif
    </div>
  </div>

  @if (session()->has('seller_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-check-circle text-emerald-600 text-base"></i>
      {{ session('seller_success') }}
    </div>
  @endif

  @if (session()->has('buyer_notice'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-ban text-rose-600 text-base"></i>
      {{ session('buyer_notice') }}
    </div>
  @endif

  @if (session()->has('review_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
      <i class="fas fa-star text-amber-500 text-base"></i>
      {{ session('review_success') }}
    </div>
  @endif

  <!-- SELLER DIRECT PAYMENT VERIFICATION PROMPT (POST-WARRANTY) -->
  @if (($invoice->payment_method ?? '') === 'direct' && ($invoice->status ?? '') !== 'paid' && ($invoice->status ?? '') !== 'cancelled')
    <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-50 via-white to-amber-50/50 border-2 border-amber-300 shadow-soft space-y-3">
      <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
            <i class="fas fa-question-circle text-lg"></i>
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
              Did the buyer pay?
              <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold uppercase">Post-Warranty Check</span>
            </h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
              The inspection warranty period for this order has elapsed. Please verify if the buyer transferred the agreed 
              <strong class="text-slate-950 font-black">₦{{ number_format($invoice->total ?? 0) }}</strong> directly to your bank account.
            </p>
            <div class="mt-2 text-[11px] text-pp-800 bg-pp-50/80 p-2.5 rounded-xl border border-pp-200/60 inline-flex items-center gap-2">
              <i class="fas fa-chart-line text-pp-600"></i>
              <span><strong>Why confirm?</strong> Marking this invoice as paid records the completed sale, increasing the public count of sales on your listings and jobs done on your services to build trust with future buyers.</span>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button wire:click="confirmDirectPaymentReceived" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
          <i class="fas fa-check-circle"></i> Yes, Buyer Paid (Mark as Paid &amp; Increase Sales Count)
        </button>
      </div>
    </div>
  @endif

  <!-- BUYER INTERACTIVE REVIEW PROMPT & 'I DIDN'T BUY IT' BUTTON -->
  @if (($invoice->status ?? '') === 'paid')
    <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-soft space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i> Review Your Experience
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Share feedback on the item or service received from <strong>{{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'the seller') }}</strong>.</p>
        </div>

        <!-- Small button: I didn't buy it -->
        <button wire:click="reportDidNotBuy" class="text-xs text-rose-600 hover:text-rose-800 hover:underline font-bold self-start sm:self-auto flex items-center gap-1 cursor-pointer">
          <i class="fas fa-times-circle"></i> I didn't buy it
        </button>
      </div>

      @if (! $reviewSubmitted)
        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 block mb-1">Your Rating:</label>
            <div class="flex items-center gap-1">
              @for ($i = 1; $i <= 5; $i++)
                <button type="button" wire:click="$set('rating', {{ $i }})" class="text-lg transition cursor-pointer {{ $rating >= $i ? 'text-amber-400 hover:text-amber-500' : 'text-slate-200 hover:text-slate-300' }}">
                  <i class="fas fa-star"></i>
                </button>
              @endfor
              <span class="ml-2 text-xs font-bold text-slate-700">{{ $rating }} / 5 Stars</span>
            </div>
          </div>

          <div>
            <label class="font-bold text-slate-700 block mb-1">Feedback Comment (Optional):</label>
            <textarea wire:model="reviewComment" rows="2" placeholder="Tell other buyers about item quality, condition, or technician speed..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"></textarea>
          </div>

          <div class="flex items-center justify-between pt-1">
            <button wire:click="submitReview" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-paper-plane"></i> Submit Review
            </button>
            <span class="text-[11px] text-slate-400">Reviews are verified and displayed on seller listings</span>
          </div>
        </div>
      @else
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
          <i class="fas fa-check-circle text-emerald-600"></i> Your review has been recorded. Thank you for building a safer marketplace!
        </div>
      @endif
    </div>
  @endif

  <!-- INVOICE CARD -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    
    <!-- INVOICE META -->
    <div class="flex flex-col sm:flex-row justify-between gap-6 border-b border-slate-100 pb-6 text-xs">
      <div class="space-y-1">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Seller / Provider Details</span>
        <h3 class="text-base font-extrabold text-slate-900">{{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'Adam Computers Ltd') }}</h3>
        <p class="text-slate-600">{{ $invoice->seller->primaryLocation?->address_line_1 ?? 'Computer Village, Ikeja, Lagos' }}</p>
        
        @php
          $sellerAcc = $invoice->seller?->bankAccounts?->first();
        @endphp
        @if ($sellerAcc)
          <p class="text-pp-700 font-bold">
            <i class="fas fa-university text-slate-400"></i> Bank: {{ $sellerAcc->bank_name }} · Acc #: {{ $sellerAcc->account_number }} ({{ $sellerAcc->account_name }})
          </p>
        @else
          <p class="text-slate-500">Bank: GTBank · Acc: 0123456789</p>
        @endif
      </div>

      <div class="space-y-1 text-left sm:text-right">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Customer / Buyer Details</span>
        <h3 class="text-base font-extrabold text-slate-900">{{ $invoice->buyer->name ?? 'TechSam (Samuel Ike)' }}</h3>
        <p class="text-slate-600">{{ $invoice->buyer->primaryLocation?->address_line_1 ?? 'Lekki Phase 1, Lagos' }}</p>
        <p class="text-slate-500">
          Payment: <strong class="text-slate-900 uppercase">{{ ($invoice->payment_method ?? '') === 'direct' ? 'Direct Seller Bank Transfer' : 'Parts & Parcel Platform Escrow' }}</strong>
        </p>
      </div>
    </div>

    <!-- LINE ITEMS TABLE -->
    <div class="space-y-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list-ul text-pp-600"></i> Agreed Commercial Line Items
      </h3>

      <div class="overflow-x-auto rounded-2xl border border-slate-200">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
            <tr>
              <th class="p-3">Item / Service Description</th>
              <th class="p-3">Type</th>
              <th class="p-3 text-center">Qty</th>
              <th class="p-3 text-right">Unit Price</th>
              <th class="p-3">Agreed Warranty</th>
              <th class="p-3 text-right">Total Price</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
            @if ($invoice->items && $invoice->items->isNotEmpty())
              @foreach ($invoice->items as $item)
                <tr>
                  <td class="p-3 font-bold text-slate-900">{{ $item->description }}</td>
                  <td class="p-3">
                    <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold uppercase">
                      {{ $item->type ?? 'ITEM' }}
                    </span>
                  </td>
                  <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                  <td class="p-3 text-right font-extrabold">₦{{ number_format($item->unit_price) }}</td>
                  <td class="p-3">
                    @if ($item->warranty_period_days)
                      <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
                        {{ $item->warranty_period_days }} DAYS
                      </span>
                    @else
                      <span class="text-slate-400">N/A</span>
                    @endif
                  </td>
                  <td class="p-3 text-right font-black text-slate-950">₦{{ number_format($item->amount) }}</td>
                </tr>
              @endforeach
            @else
              <tr>
                <td class="p-3 font-bold text-slate-900">HP EliteBook 840 G5 Laptop</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold">ITEM</span></td>
                <td class="p-3 text-center font-bold">1</td>
                <td class="p-3 text-right font-extrabold">₦250,000</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">14 DAYS</span></td>
                <td class="p-3 text-right font-black text-slate-950">₦250,000</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <!-- TOTALS & PAYMENT TERMS -->
    <div class="grid sm:grid-cols-2 gap-6 pt-3 border-t border-slate-100 text-xs">
      <div class="p-4 rounded-2xl {{ ($invoice->payment_method ?? '') === 'direct' ? 'bg-amber-50 border border-amber-200' : 'bg-pp-50 border border-pp-200' }} space-y-2">
        @if (($invoice->payment_method ?? '') === 'direct')
          <b class="text-amber-900 font-bold flex items-center gap-1.5"><i class="fas fa-university text-amber-600"></i> Direct Seller Transfer Terms:</b>
          <p class="text-slate-700 leading-relaxed text-[11px]">
            Payment was made directly between buyer and seller outside platform escrow. After the warranty inspection period, the seller confirms receipt to increment their verified sales count.
          </p>
        @else
          <b class="text-pp-900 font-bold flex items-center gap-1.5"><i class="fas fa-shield-alt text-pp-600"></i> Platform Escrow Protection Terms:</b>
          <p class="text-slate-700 leading-relaxed text-[11px]">
            Funds are held securely by Parts &amp; Parcel until the buyer receives and verifies the package within the agreed warranty period.
          </p>
        @endif
      </div>

      <div class="space-y-1.5 text-right">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal:</span>
          <span>₦{{ number_format($invoice->subtotal ?? 250000) }}</span>
        </div>
        @if (($invoice->discount ?? 0) > 0)
          <div class="flex justify-between text-pp-700 font-bold">
            <span>Agreed Discount:</span>
            <span>-₦{{ number_format($invoice->discount) }}</span>
          </div>
        @endif
        @if (($invoice->payment_method ?? '') === 'platform')
          <div class="flex justify-between text-slate-600">
            <span>Escrow Protection Fee (+):</span>
            <span>₦1,500</span>
          </div>
        @endif
        <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
          <span class="font-bold text-slate-900">Total Amount:</span>
          <span class="text-2xl font-black text-slate-950">₦{{ number_format($invoice->total ?? 250000) }}</span>
        </div>
      </div>
    </div>

  </div>

</div>