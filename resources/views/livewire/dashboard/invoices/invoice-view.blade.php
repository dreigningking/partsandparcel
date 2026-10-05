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

  @if (session()->has('buyer_payment_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-shield-alt text-emerald-600 text-base"></i>
      {{ session('buyer_payment_success') }}
    </div>
  @endif

  @if (session()->has('buyer_direct_notice'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-university text-amber-600 text-base"></i>
      {{ session('buyer_direct_notice') }}
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

    <!-- PAYMENT SELECTION SECTION (FOR UNPAID / ISSUED INVOICES) -->
    @if (($invoice->status ?? '') !== 'paid' && ($invoice->status ?? '') !== 'cancelled')
      <div class="p-6 rounded-3xl bg-slate-50/70 border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-200/80 pb-3">
          <div>
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-credit-card text-pp-600"></i> Select Payment Option
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Select how you wish to complete payment for this invoice</p>
          </div>
          <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-slate-200 text-slate-700 self-start sm:self-auto">
            Payment Choice
          </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <!-- OPTION A: PAY VIA PARTS & PARCEL (ESCROW PROTECTED) -->
          <div wire:click="setPaymentOption('platform')" class="p-5 rounded-2xl border transition cursor-pointer flex flex-col justify-between {{ $paymentOption === 'platform' ? 'border-2 border-pp-600 bg-white shadow-soft ring-2 ring-pp-100' : 'border-slate-200 hover:border-pp-300 bg-white' }}">
            <div class="space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                  <input type="radio" name="payment_option_radio" value="platform" @checked($paymentOption === 'platform') class="accent-pp-600 w-4 h-4 cursor-pointer" />
                  <span class="text-xs font-extrabold text-slate-900 uppercase flex items-center gap-1.5">
                    <i class="fas fa-shield-alt text-pp-600"></i> Parts &amp; Parcel Escrow
                  </span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[10px] font-extrabold shrink-0">
                  + ₦{{ number_format($escrowFee) }} ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap) - Capped @endif) ESCROW FEE
                </span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Pay securely via platform escrow. Your funds remain locked until you receive, inspect, and verify the agreed items within the warranty window.
              </p>

              <div class="space-y-1.5 text-[11px] text-slate-600 pt-1">
                <div class="flex items-center gap-2 text-pp-800 font-bold">
                  <i class="fas fa-check-circle text-pp-600 text-xs"></i> 100% Money-Back Escrow Guarantee
                </div>
                <div class="flex items-center gap-2 text-slate-600">
                  <i class="fas fa-check-circle text-slate-400 text-xs"></i> Full dispute protection &amp; platform mediation
                </div>
                <div class="flex items-center gap-2 text-slate-600">
                  <i class="fas fa-check-circle text-slate-400 text-xs"></i> Instant automated receipt &amp; verified record
                </div>
              </div>

              <!-- COUPON / PROMO VOUCHER (ONLY ON PLATFORM / ESCROW PAYMENTS) -->
              @if ($paymentOption === 'platform')
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2" wire:click.stop>
                  <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                    Have a Promo Voucher / Coupon?
                  </label>

                  @if ($couponValid && $appliedCouponId)
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                      <div class="flex items-center gap-1.5 text-emerald-800 font-extrabold truncate">
                        <i class="fas fa-ticket text-emerald-600"></i>
                        <span class="truncate">{{ $couponMessage }}</span>
                      </div>
                      <button
                        type="button"
                        wire:click="removeCoupon"
                        class="text-xs font-bold text-rose-600 hover:underline shrink-0 ml-2 cursor-pointer"
                      >
                        Remove
                      </button>
                    </div>
                  @else
                    <div class="flex items-center gap-2">
                      <div class="relative w-full">
                        <i class="fas fa-ticket absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input
                          type="text"
                          wire:model="couponCode"
                          wire:keydown.enter.prevent="applyCoupon"
                          placeholder="Coupon code"
                          class="w-full text-xs font-bold uppercase rounded-xl border border-slate-200 pl-8 pr-2.5 py-2 focus:border-pp-600 focus:outline-none"
                        />
                      </div>
                      <button
                        type="button"
                        wire:click="applyCoupon"
                        class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition shrink-0 cursor-pointer"
                      >
                        Apply
                      </button>
                    </div>
                    @error('couponCode')
                      <span class="text-[11px] text-rose-500 font-bold block">{{ $message }}</span>
                    @enderror
                  @endif
                </div>
              @endif
            </div>

            @if ($paymentOption === 'platform')
              <div class="pt-5 mt-4 border-t border-slate-100">
                <button
                  type="button"
                  wire:click="payWithPlatformEscrow"
                  class="w-full py-3 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                >
                  <i class="fas fa-shield-alt text-white"></i>
                  <span>Pay ₦{{ number_format($totalPayable) }} with Escrow Protection →</span>
                </button>
              </div>
            @endif
          </div>

          <!-- OPTION B: PAY DIRECTLY TO SELLER (OFF-PLATFORM DIRECT TRANSFER) -->
          <div wire:click="setPaymentOption('direct')" class="p-5 rounded-2xl border transition cursor-pointer flex flex-col justify-between {{ $paymentOption === 'direct' ? 'border-2 border-amber-600 bg-white shadow-soft ring-2 ring-amber-100' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
            <div class="space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                  <input type="radio" name="payment_option_radio" value="direct" @checked($paymentOption === 'direct') class="accent-amber-600 w-4 h-4 cursor-pointer" />
                  <span class="text-xs font-extrabold text-slate-900 uppercase flex items-center gap-1.5">
                    <i class="fas fa-university text-slate-600"></i> Direct Bank Transfer
                  </span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold shrink-0">
                  DIRECT SELLER TRANSFER
                </span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Transfer directly to the seller's bank account. No platform escrow fee is charged. Note: Coupons cannot be used on direct payments.
              </p>

              @if ($paymentOption === 'direct')
                <!-- SELLER BANK ACCOUNT DETAILS -->
                <div class="mt-3 p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs space-y-2">
                  <span class="text-[10px] font-extrabold uppercase text-amber-900 tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-building-columns text-amber-700"></i> Seller Bank Details ({{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'Seller') }})
                  </span>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-slate-900">
                    <div class="bg-white p-2 rounded-lg border border-amber-100 shadow-2xs">
                      <span class="text-[9px] text-slate-400 font-bold block uppercase">Bank Name</span>
                      <strong class="text-xs text-slate-950 font-black truncate block">{{ $sellerBank['bank_name'] }}</strong>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-amber-100 shadow-2xs">
                      <span class="text-[9px] text-slate-400 font-bold block uppercase">Account Name</span>
                      <strong class="text-xs text-slate-950 font-black truncate block">{{ $sellerBank['account_name'] }}</strong>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-amber-100 shadow-2xs">
                      <span class="text-[9px] text-slate-400 font-bold block uppercase">Account Number</span>
                      <strong class="text-xs text-pp-700 font-black tracking-wider block">{{ $sellerBank['account_number'] }}</strong>
                    </div>
                  </div>
                </div>

                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-800 space-y-0.5">
                  <b class="font-bold text-rose-700 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-triangle text-rose-600"></i> Direct Payment Notice
                  </b>
                  <p class="text-rose-900 leading-relaxed text-[11px]">
                    You are paying {{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'the seller') }} directly outside Parts &amp; Parcel Escrow. Ensure you keep your payment transfer receipt.
                  </p>
                </div>
              @endif
            </div>

            @if ($paymentOption === 'direct')
              <div class="pt-5 mt-4 border-t border-slate-100">
                <button
                  type="button"
                  wire:click="confirmDirectTransferSent"
                  class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                >
                  <i class="fas fa-check-circle text-emerald-400"></i>
                  <span>I Have Transferred ₦{{ number_format($totalPayable) }} to Seller</span>
                </button>
              </div>
            @endif
          </div>
        </div>
      </div>
    @endif

    <!-- TOTALS & PAYMENT TERMS -->
    <div class="grid sm:grid-cols-2 gap-6 pt-3 border-t border-slate-100 text-xs">
      <div class="p-4 rounded-2xl {{ ($invoice->status === 'paid' ? (($invoice->payment_method ?? '') === 'direct' ? 'bg-amber-50 border border-amber-200' : 'bg-pp-50 border border-pp-200') : ($paymentOption === 'direct' ? 'bg-amber-50 border border-amber-200' : 'bg-pp-50 border border-pp-200')) }} space-y-2">
        @if (($invoice->status === 'paid' ? ($invoice->payment_method ?? '') : $paymentOption) === 'direct')
          <b class="text-amber-900 font-bold flex items-center gap-1.5"><i class="fas fa-university text-amber-600"></i> Direct Seller Transfer Terms:</b>
          <p class="text-slate-700 leading-relaxed text-[11px]">
            Payment is made directly between buyer and seller outside platform escrow. After the warranty inspection period, the seller confirms receipt to increment their verified sales count.
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
          <span>₦{{ number_format($invoice->subtotal ?? 0) }}</span>
        </div>
        @if ($offerDiscount > 0)
          <div class="flex justify-between text-pp-700 font-bold">
            <span>Agreed Offer Discount:</span>
            <span>-₦{{ number_format($offerDiscount) }}</span>
          </div>
        @endif
        @if (($invoice->status ?? '') !== 'paid' && $paymentOption === 'platform' && $couponDiscount > 0)
          <div class="flex justify-between text-emerald-600 font-bold">
            <span>Promo Voucher Discount:</span>
            <span>-₦{{ number_format($couponDiscount) }}</span>
          </div>
        @endif

        @if (($invoice->status ?? '') === 'paid')
          @if (($invoice->payment_method ?? '') === 'platform')
            <div class="flex justify-between text-slate-600">
              <span>Escrow Protection Fee ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap), capped at ₦{{ number_format($escrowCap) }}@endif):</span>
              <span>+₦{{ number_format($escrowFee) }}</span>
            </div>
          @else
            <div class="flex justify-between text-slate-500 text-[11px]">
              <span>Escrow Protection:</span>
              <span class="font-bold text-amber-700">NO ESCROW (Direct Seller Payment)</span>
            </div>
          @endif
          <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
            <span class="font-bold text-slate-900">Total Paid:</span>
            <span class="text-2xl font-black text-slate-950">₦{{ number_format($invoice->total ?? 0) }}</span>
          </div>
        @else
          @if ($paymentOption === 'platform')
            <div class="flex justify-between text-slate-600">
              <span>Escrow Protection Fee ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap), capped at ₦{{ number_format($escrowCap) }}@endif):</span>
              <span class="font-bold text-pp-700">+₦{{ number_format($escrowFee) }}</span>
            </div>
          @else
            <div class="flex justify-between text-slate-500 text-[11px]">
              <span>Escrow Protection Fee:</span>
              <span class="font-bold text-amber-700">NO ESCROW (Direct Transfer)</span>
            </div>
          @endif
          <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
            <span class="font-bold text-slate-900">Total Amount Payable:</span>
            <span class="text-2xl font-black text-slate-950">₦{{ number_format($totalPayable) }}</span>
          </div>
        @endif
      </div>
    </div>

  </div>

</div>