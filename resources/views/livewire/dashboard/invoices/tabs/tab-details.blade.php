<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- CONTEXTUAL NOTICE PILLS & DEEP-LINK ALERT BANNERS -->
  <!-- ========================================================================= -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
    
    <!-- 1. SHIPMENT NOTICE -->
    @if ($outboundShipment)
      <div wire:click="switchTab('shipment')" class="p-3.5 rounded-2xl bg-gradient-to-r from-pp-50 to-white border border-pp-200/80 shadow-2xs hover:shadow-soft hover:border-pp-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-pp-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-truck-fast"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-slate-900 truncate">Shipment In Transit</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-extrabold uppercase {{ $outboundShipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : 'bg-pp-100 text-pp-800' }}">
                {{ $outboundShipment->status }}
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">Waybill: #{{ $outboundShipment->tracking_number }}</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-pp-600 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Track <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 2. ACTIVE ISSUE ALERT -->
    @if ($hasIssues)
      @php $activeIssue = $issues->first(); @endphp
      <div wire:click="switchTab('issue')" class="p-3.5 rounded-2xl bg-gradient-to-r from-rose-50 to-white border border-rose-200 shadow-2xs hover:shadow-soft hover:border-rose-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-triangle-exclamation"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-rose-950 truncate">Issue Reported</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-rose-200 text-rose-900 uppercase">
                Escrow Held
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ Str::headline($activeIssue->type ?? 'Order Issue') }}</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-rose-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          View <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 3. REPLACEMENT NOTICE -->
    @if ($hasReplacements)
      <div wire:click="switchTab('replacement')" class="p-3.5 rounded-2xl bg-gradient-to-r from-amber-50 to-white border border-amber-200 shadow-2xs hover:shadow-soft hover:border-amber-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-repeat"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-amber-950 truncate">Replacement Active</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-amber-100 text-amber-800 uppercase">
                {{ $replacements->count() }} unit
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">Status: {{ Str::headline($replacements->first()?->status ?? 'In progress') }}</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-amber-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Details <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 4. REFUND NOTICE -->
    @if ($hasRefunds)
      @php $totalRefunded = $refunds->sum('amount'); @endphp
      <div wire:click="switchTab('refund')" class="p-3.5 rounded-2xl bg-gradient-to-r from-emerald-50 to-white border border-emerald-200 shadow-2xs hover:shadow-soft hover:border-emerald-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-hand-holding-dollar"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-emerald-950 truncate">Refund Recorded</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 uppercase">
                {{ $invoice->currency_symbol }}{{ number_format($totalRefunded) }}
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $refunds->count() }} refund transaction</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-emerald-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Review <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 5. DISPUTE ALERT -->
    @if ($hasDisputes)
      <div wire:click="switchTab('dispute')" class="p-3.5 rounded-2xl bg-gradient-to-r from-purple-50 to-white border border-purple-200 shadow-2xs hover:shadow-soft hover:border-purple-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-scale-balanced"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-purple-950 truncate">Active Dispute</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-purple-100 text-purple-800 uppercase">
                In Mediation
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">Platform mediation team assigned</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-purple-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Resolve <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 6. WARRANTY NOTICE -->
    @if ($hasWarranty && in_array($invoice->status, ['paid', 'accepted']))
      <div wire:click="switchTab('warranty')" class="p-3.5 rounded-2xl bg-gradient-to-r from-blue-50 to-white border border-blue-200 shadow-2xs hover:shadow-soft hover:border-blue-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-shield-halved"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-blue-950 truncate">Active Warranty</span>
              @if ($invoice->isWithinWarranty())
                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-blue-100 text-blue-800 uppercase">
                  {{ $invoice->activeWarrantyEndsAt()?->diffForHumans(['parts' => 1]) }} left
                </span>
              @endif
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">Coverage by {{ $invoice->seller->business_name ?: $invoice->seller->name }}</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-blue-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Claim <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

    <!-- 7. SERVICE JOB NOTICE -->
    @if ($hasServices)
      <div wire:click="switchTab('service')" class="p-3.5 rounded-2xl bg-gradient-to-r from-sky-50 to-white border border-sky-200 shadow-2xs hover:shadow-soft hover:border-sky-400 transition cursor-pointer flex items-center justify-between gap-3 group">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center text-sm shrink-0 shadow-2xs group-hover:scale-105 transition">
            <i class="fas fa-screwdriver-wrench"></i>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
              <span class="text-xs font-black text-sky-950 truncate">Service Order</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-sky-100 text-sky-800 uppercase">
                Technician
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">Job specs &amp; review</p>
          </div>
        </div>
        <span class="text-[11px] font-black text-sky-700 group-hover:translate-x-0.5 transition shrink-0 flex items-center gap-1">
          Open <i class="fas fa-chevron-right text-[9px]"></i>
        </span>
      </div>
    @endif

  </div>

  <!-- ========================================================================= -->
  <!-- BUYER / SELLER PENDING PAYMENT BANNERS -->
  <!-- ========================================================================= -->
  @if ($invoice->status !== 'paid' && $invoice->status !== 'accepted' && $invoice->status !== 'cancelled')
    @if ($isBuyer)
      <div class="p-5 rounded-3xl bg-gradient-to-r from-amber-50 via-white to-amber-50/60 border-2 border-amber-300 shadow-soft flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs text-base">
            <i class="fas fa-credit-card"></i>
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
              Payment Required by You (Buyer)
              <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold uppercase">
                {{ $invoice->remainingTimeText() }}
              </span>
            </h3>
            <p class="text-xs text-slate-600 mt-1">
              Please complete payment for this invoice before it expires. You can pay securely with <strong>Parts &amp; Parcel Escrow</strong> or directly via bank transfer below.
            </p>
          </div>
        </div>
        <a href="#payment-section" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 shrink-0">
          <span>Make Payment Now</span>
          <i class="fas fa-arrow-down text-[10px]"></i>
        </a>
      </div>
    @elseif ($isSeller)
      <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 shadow-soft flex items-center justify-between gap-4 text-xs">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-2xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 shadow-xs text-base">
            <i class="fas fa-clock"></i>
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
              Payment Pending from Buyer
              <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold uppercase">
                {{ $invoice->remainingTimeText() }}
              </span>
            </h3>
            <p class="text-slate-600 mt-1">
              The buyer ({{ $invoice->buyer->name }}) has been issued this invoice. Once paid, funds will be locked securely in Escrow and you will be notified to dispatch the package.
            </p>
          </div>
        </div>
      </div>
    @endif
  @else
    <div class="flex items-center gap-2">
      @if ($isBuyer)
        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
          <i class="fas fa-user-check text-emerald-600"></i>
          <span>Paid by You (Buyer) · Total {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</span>
        </span>
      @elseif ($isSeller)
        <span class="px-3 py-1.5 rounded-xl bg-pp-50 border border-pp-200 text-pp-800 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
          <i class="fas fa-wallet text-pp-600"></i>
          <span>Beneficiary: You (Seller) · Total {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</span>
        </span>
      @endif
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- INTERACTIVE PLATFORM ESCROW PROCESSES & STEPPER (WHEN PAID VIA PLATFORM) -->
  <!-- ========================================================================= -->
  @if ($invoice->isPlatformEscrow() && in_array($invoice->status, ['paid', 'accepted']))
    <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200 shadow-soft space-y-6">
      
      <!-- ESCROW LIFECYCLE HEADER & STEPPER -->
      <div class="border-b border-slate-100 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-pp-100 text-pp-700 flex items-center justify-center text-xs font-black">
              <i class="fas fa-shield-halved"></i>
            </div>
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
              Escrow Fulfillment &amp; Inspection Processes
            </h3>
          </div>
          <span class="text-[11px] font-bold text-slate-400">Funds 100% Protected</span>
        </div>

        @php
          $isShipped = $outboundShipment && in_array($outboundShipment->status, ['dispatched', 'delivered']);
          $isDelivered = $outboundShipment && $outboundShipment->status === 'delivered';
          $isDealCompleted = in_array($invoice->status, ['accepted', 'completed']);
        @endphp

        <!-- STEPPER PROGRESS BAR -->
        <div class="grid grid-cols-4 gap-2 text-center text-xs">
          <!-- STEP 1: PAYMENT SECURED -->
          <div class="space-y-1">
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold grid place-items-center mx-auto shadow-2xs">✓</div>
            <span class="font-extrabold text-slate-900 block text-[11px]">Payment Secured</span>
            <span class="text-[10px] text-emerald-600 font-bold">In Escrow</span>
          </div>

          <!-- STEP 2: SHIPPED -->
          <div class="space-y-1 {{ ! $isShipped ? 'opacity-50' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $isShipped ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto shadow-2xs">
              {{ $isShipped ? '✓' : '2' }}
            </div>
            <span class="font-extrabold text-slate-900 block text-[11px]">Dispatched</span>
            <span class="text-[10px] text-slate-400 font-medium">{{ $isShipped ? 'On The Way' : 'Pending Seller' }}</span>
          </div>

          <!-- STEP 3: RECEIVED -->
          <div class="space-y-1 {{ ! $isDelivered ? 'opacity-50' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $isDelivered ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto shadow-2xs">
              {{ $isDelivered ? '✓' : '3' }}
            </div>
            <span class="font-extrabold text-slate-900 block text-[11px]">Received</span>
            <span class="text-[10px] text-slate-400 font-medium">{{ $isDelivered ? 'Delivered' : 'Pending Arrival' }}</span>
          </div>

          <!-- STEP 4: DEAL COMPLETED -->
          <div class="space-y-1 {{ ! $isDealCompleted ? 'opacity-50' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $isDealCompleted ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-extrabold grid place-items-center mx-auto shadow-2xs">
              {{ $isDealCompleted ? '✓' : '4' }}
            </div>
            <span class="font-extrabold text-slate-900 block text-[11px]">Completed</span>
            <span class="text-[10px] text-slate-400 font-medium">{{ $isDealCompleted ? 'Settlement Released' : 'Pending Acceptance' }}</span>
          </div>
        </div>
      </div>

      <!-- ACTIVE ACTIONS BASED ON ROLE AND STAGE -->
      <!-- ACTIVE ACTIONS BASED ON ROLE AND STAGE -->
      @if ($isSeller)
        @if ($this->isFulfillmentWarningActive())
          <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 flex items-start gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-1 text-xs">
              <h4 class="font-extrabold uppercase text-rose-950">Fulfillment Deadline Warning!</h4>
              <p class="mt-0.5 text-rose-900 leading-relaxed">
                You have not yet dispatched or prepared this package. This order will be <strong>automatically cancelled and refunded</strong> in approximately <strong>{{ $this->autoCancelHoursRemaining }} hours</strong> if not fulfilled.
              </p>
            </div>
          </div>
        @endif

        @if (! $isShipped && ! $invoice->ready_for_pickup_at)
          <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h4 class="text-xs font-black text-amber-950 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-box text-amber-600"></i> Action Required: Order Awaiting Fulfillment
              </h4>
              <p class="text-xs text-amber-900 mt-1">
                The buyer's funds are securely locked in platform escrow. Please dispatch the package or mark it ready for self-pickup.
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
              <button wire:click="markReadyForPickup" type="button" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-store"></i>
                <span>Mark Ready for Pickup</span>
              </button>
              <button wire:click="openShipmentModal" type="button" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-truck"></i>
                <span>Mark Shipped &amp; Add Tracking</span>
              </button>
              @if ($this->canCancelOrder())
                <button wire:click="openCancelModal" type="button" class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                  <i class="fas fa-ban"></i>
                  <span>Cancel Order</span>
                </button>
              @endif
            </div>
          </div>
        @elseif ($invoice->ready_for_pickup_at && ! $isDelivered)
          <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                <i class="fas fa-store"></i>
              </div>
              <div>
                <p class="font-bold">Ready for Customer Pickup</p>
                <p class="text-blue-700 text-[11px] mt-0.5">Package was marked ready on {{ $invoice->ready_for_pickup_at->format('M d, Y H:i') }}. Awaiting customer collection.</p>
              </div>
            </div>
            @if ($this->canCancelOrder())
              <button wire:click="openCancelModal" type="button" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-ban"></i>
                <span>Cancel</span>
              </button>
            @endif
          </div>
        @elseif ($isShipped && ! $isDelivered)
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 flex items-center justify-between gap-4">
            <div>
              <p class="font-bold text-slate-900">Package In Transit to Buyer</p>
              <p class="text-slate-500 text-[11px] mt-0.5">Dispatched via {{ $outboundShipment?->provider_name ?? 'Courier' }} ({{ $outboundShipment?->tracking_number ?? 'Waybill' }}). Awaiting delivery confirmation by the buyer.</p>
            </div>
            <button wire:click="switchTab('shipment')" type="button" class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">
              View Tracking Tab →
            </button>
          </div>
        @elseif ($isDelivered && ! $isDealCompleted)
          <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-center justify-between gap-4">
            <div>
              <p class="font-bold">Delivered to Buyer</p>
              <p class="text-blue-800 text-[11px] mt-0.5">The buyer has received the package and is currently inspecting items. Escrow settlement will be released upon acceptance.</p>
            </div>
          </div>
        @elseif ($isDealCompleted)
          <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center justify-between gap-4">
            <div>
              <p class="font-bold">Escrow Released &amp; Deal Completed</p>
              <p class="text-emerald-800 text-[11px] mt-0.5">The buyer accepted the package. Your settlement is queued and eligible for payout.</p>
            </div>
          </div>
        @endif
      @endif

      @if ($isBuyer)
        @if (! $isShipped && ! $invoice->ready_for_pickup_at)
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h4 class="font-bold text-slate-900">Seller is Preparing Your Order</h4>
              <p class="text-slate-500 text-[11px] mt-0.5">Your payment is securely held in Parts &amp; Parcel Escrow. The seller has been instructed to fulfill the package.</p>
            </div>
            @if ($this->canCancelOrder())
              <button wire:click="openCancelModal" type="button" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                <i class="fas fa-ban"></i>
                <span>Cancel Order</span>
              </button>
            @endif
          </div>
        @elseif ($invoice->ready_for_pickup_at && ! $isDelivered)
          <div class="p-5 rounded-2xl bg-pp-50/70 border border-pp-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h4 class="text-xs font-black text-pp-950 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-store text-pp-600"></i> Your Package is Ready for Pickup!
              </h4>
              <p class="text-xs text-pp-900 mt-1">
                The seller has prepared your package for collection at their location. Once collected, please confirm below.
              </p>
            </div>
            <div class="flex items-center gap-2">
              <button wire:click="openReceiveConfirmModal" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 shrink-0 cursor-pointer">
                <i class="fas fa-box-open"></i>
                <span>I've Picked Up Package</span>
              </button>
              @if ($this->canCancelOrder())
                <button wire:click="openCancelModal" type="button" class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                  <i class="fas fa-ban"></i>
                  <span>Cancel</span>
                </button>
              @endif
            </div>
          </div>
        @elseif ($isShipped && ! $isDelivered)
          <div class="p-5 rounded-2xl bg-pp-50/70 border border-pp-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h4 class="text-xs font-black text-pp-950 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-truck-moving text-pp-600"></i> Your Package is in Transit!
              </h4>
              <p class="text-xs text-pp-900 mt-1">
                Dispatched via <strong>{{ $outboundShipment?->provider_name ?? 'Courier' }}</strong> (Tracking #{{ $outboundShipment?->tracking_number ?? 'Waybill' }}).
                When the package arrives, please confirm reception below.
              </p>
            </div>
            <button wire:click="openReceiveConfirmModal" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 shrink-0 cursor-pointer">
              <i class="fas fa-box-open"></i>
              <span>I've Received Package</span>
            </button>
          </div>
        @elseif ($isDelivered && ! $isDealCompleted && (! $latestIssue || $latestIssue->status === 'resolved'))
          <div class="p-5 rounded-2xl bg-white border-2 border-slate-900 shadow-soft space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
              <div>
                <h4 class="text-xs font-black text-slate-950 uppercase tracking-wider flex items-center gap-2">
                  <i class="fas fa-clipboard-check text-emerald-600"></i> Package Reception Confirmed
                </h4>
                <p class="text-xs text-slate-600 mt-0.5">Please test and verify the received items now. Choose an action below:</p>
              </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
              <button wire:click="completeDeal" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-check-circle"></i>
                <span>Complete Deal (Accept Package &amp; Release Escrow)</span>
              </button>

              <button wire:click="openIssueModal" type="button" class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 font-extrabold text-xs transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-exclamation-triangle text-rose-600"></i>
                <span>Report Something (Defect / Damaged / Return)</span>
              </button>
            </div>
          </div>
        @endif
      @endif

    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- SELLER DIRECT PAYMENT VERIFICATION PROMPT -->
  <!-- ========================================================================= -->
  @if ($invoice->payment_method === 'direct' && $invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-50 via-white to-amber-50/50 border-2 border-amber-300 shadow-soft space-y-3">
      <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
            <i class="fas fa-question-circle text-lg"></i>
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
              Did the buyer pay?
              <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold uppercase">Direct Transfer Check</span>
            </h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
              Please verify if the buyer transferred the agreed 
              <strong class="text-slate-950 font-black">{{ $invoice->currency_symbol }}{{ number_format($invoice->total) }}</strong> directly to your bank account.
            </p>
          </div>
        </div>
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button wire:click="confirmDirectPaymentReceived" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
          <i class="fas fa-check-circle"></i> Yes, Buyer Paid (Mark as Paid &amp; Increase Sales Count)
        </button>
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- MAIN INVOICE LINE ITEMS CARD -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    
    <!-- INVOICE META: SELLER & BUYER DETAILS -->
    <div class="flex flex-col sm:flex-row justify-between gap-6 border-b border-slate-100 pb-6 text-xs">
      <div class="space-y-1">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Seller / Provider Details</span>
        <h3 class="text-base font-extrabold text-slate-900">{{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'Verified Seller') }}</h3>
        <p class="text-slate-600">{{ $invoice->seller->primaryLocation?->address_line_1 ?? 'Registered Business Location' }}</p>
        
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
        <h3 class="text-base font-extrabold text-slate-900">{{ $invoice->buyer->name ?? 'Customer' }}</h3>
        <p class="text-slate-600">{{ $invoice->buyer->primaryLocation?->address_line_1 ?? 'Delivery Location' }}</p>
        <p class="text-slate-500">
          Payment: <strong class="text-slate-900 uppercase">{{ $invoice->payment_method === 'direct' ? 'Direct Seller Bank Transfer' : 'Parts & Parcel Platform Escrow' }}</strong>
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
            @forelse ($invoice->items as $item)
              <tr class="hover:bg-slate-50/60 transition">
                <td class="p-3 font-bold text-slate-900">{{ $item->description }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold uppercase">
                    {{ $item->type ?? 'ITEM' }}
                  </span>
                </td>
                <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                <td class="p-3 text-right font-extrabold">{{ $invoice->currency_symbol }}{{ number_format($item->unit_price) }}</td>
                <td class="p-3">
                  @if ($item->warranty_period_days)
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
                      {{ $item->warranty_period_days }} DAYS
                    </span>
                  @else
                    <span class="text-slate-400">N/A</span>
                  @endif
                </td>
                <td class="p-3 text-right font-black text-slate-950">{{ $invoice->currency_symbol }}{{ number_format($item->amount) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="p-4 text-center text-slate-500">No line items recorded on this commercial invoice.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAYMENT SELECTION SECTION (FOR UNPAID / ISSUED INVOICES) -->
    @if ($invoice->status !== 'paid' && $invoice->status !== 'accepted' && $invoice->status !== 'cancelled')
      <div id="payment-section" class="p-6 rounded-3xl bg-slate-50/70 border border-slate-200 space-y-4">
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
                  + {{ $invoice->currency_symbol }}{{ number_format($escrowFee) }} ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap) - Capped @endif) ESCROW FEE
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
              </div>

              <!-- COUPON / PROMO VOUCHER (PLATFORM PAYMENTS) -->
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

            @if ($paymentOption === 'platform' && $isBuyer)
              <div class="pt-5 mt-4 border-t border-slate-100">
                <button
                  type="button"
                  wire:click="payWithPlatformEscrow"
                  wire:loading.attr="disabled"
                  class="w-full py-3 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                  <span wire:loading.remove wire:target="payWithPlatformEscrow" class="flex items-center gap-2">
                    <i class="fas fa-shield-alt text-white"></i>
                    <span>Pay {{ $invoice->currency_symbol }}{{ number_format($totalPayable) }} with Escrow Protection →</span>
                  </span>
                  <span wire:loading wire:target="payWithPlatformEscrow" class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin text-white"></i>
                    <span>Redirecting to Payment Gateway...</span>
                  </span>
                </button>
              </div>
            @endif
          </div>

          <!-- OPTION B: PAY DIRECTLY TO SELLER -->
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
                    <i class="fas fa-building-columns text-amber-700"></i> Seller Bank Details
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
              @endif
            </div>

            @if ($paymentOption === 'direct' && $isBuyer)
              <div class="pt-5 mt-4 border-t border-slate-100">
                <button
                  type="button"
                  wire:click="confirmDirectTransferSent"
                  class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                >
                  <i class="fas fa-check-circle text-emerald-400"></i>
                  <span>I Have Transferred {{ $invoice->currency_symbol }}{{ number_format($totalPayable) }} to Seller</span>
                </button>
              </div>
            @endif
          </div>
        </div>
      </div>
    @endif

    <!-- TOTALS & PAYMENT TERMS -->
    <div class="grid sm:grid-cols-2 gap-6 pt-3 border-t border-slate-100 text-xs">
      <div class="p-4 rounded-2xl {{ (in_array($invoice->status, ['paid', 'accepted']) ? ($invoice->payment_method === 'direct' ? 'bg-amber-50 border border-amber-200' : 'bg-pp-50 border border-pp-200') : ($paymentOption === 'direct' ? 'bg-amber-50 border border-amber-200' : 'bg-pp-50 border border-pp-200')) }} space-y-2">
        @if ((in_array($invoice->status, ['paid', 'accepted']) ? $invoice->payment_method : $paymentOption) === 'direct')
          <b class="text-amber-900 font-bold flex items-center gap-1.5"><i class="fas fa-university text-amber-600"></i> Direct Seller Transfer Terms:</b>
          <p class="text-slate-700 leading-relaxed text-[11px]">
            Payment is made directly between buyer and seller outside platform escrow.
          </p>
        @else
          <b class="text-pp-900 font-bold flex items-center gap-1.5"><i class="fas fa-shield-alt text-pp-600"></i> Platform Escrow Protection Terms:</b>
          <p class="text-slate-700 leading-relaxed text-[11px]">
            Funds are securely held in escrow until delivery is verified and the deal is completed.
          </p>
        @endif
      </div>

      <div class="space-y-1.5 text-right">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal:</span>
          <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal ?? 0) }}</span>
        </div>
        @if ($offerDiscount > 0)
          <div class="flex justify-between text-pp-700 font-bold">
            <span>Agreed Offer Discount:</span>
            <span>-{{ $invoice->currency_symbol }}{{ number_format($offerDiscount) }}</span>
          </div>
        @endif
        @if (! in_array($invoice->status, ['paid', 'accepted']) && $paymentOption === 'platform' && $couponDiscount > 0)
          <div class="flex justify-between text-emerald-600 font-bold">
            <span>Promo Voucher Discount:</span>
            <span>-{{ $invoice->currency_symbol }}{{ number_format($couponDiscount) }}</span>
          </div>
        @endif

        @if (in_array($invoice->status, ['paid', 'accepted']))
          @if ($invoice->payment_method === 'platform')
            <div class="flex justify-between text-slate-600">
              <span>Escrow Protection Fee ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap), capped at {{ $invoice->currency_symbol }}{{ number_format($escrowCap) }}@endif):</span>
              <span>+{{ $invoice->currency_symbol }}{{ number_format($escrowFee) }}</span>
            </div>
          @else
            <div class="flex justify-between text-slate-500 text-[11px]">
              <span>Escrow Protection:</span>
              <span class="font-bold text-amber-700">NO ESCROW (Direct Transfer)</span>
            </div>
          @endif
          <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
            <span class="font-bold text-slate-900">Total Paid:</span>
            <span class="text-2xl font-black text-slate-950">{{ $invoice->currency_symbol }}{{ number_format($invoice->total ?? 0) }}</span>
          </div>

          @if ($invoice->isPlatformEscrow())
            @php
              $payoutGross = $invoicePayment ? (float) $invoicePayment->amount : (float) ($invoice->total ?? 0);
              $payoutFee = $invoicePayment ? (float) $invoicePayment->escrow_fee : (float) ($escrowFee ?? 0);
              $netSellerProceeds = max(0.00, round($payoutGross - $payoutFee, 2));
            @endphp
            <div class="pt-2 border-t border-dashed border-slate-200 flex justify-between items-center text-xs">
              <span class="text-slate-500 font-bold flex items-center gap-1.5">
                <i class="fas fa-hand-holding-dollar text-emerald-600"></i> Net Seller Proceeds (Amount - Escrow Fee):
              </span>
              <div class="flex items-center gap-1.5">
                @if ($invoiceSettlement)
                  <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $invoiceSettlement->status === 'settled' ? 'bg-emerald-100 text-emerald-800' : ($invoiceSettlement->status === 'eligible' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $invoiceSettlement->status }}
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-100 text-slate-700">
                    Pending Acceptance
                  </span>
                @endif
                <span class="text-sm font-black text-emerald-700">
                  {{ $invoice->currency_symbol }}{{ number_format($invoiceSettlement ? $invoiceSettlement->amount : $netSellerProceeds, 2) }}
                </span>
              </div>
            </div>
          @endif

          @if ($isBuyer && $invoicePayment)
            <div class="text-[11px] text-slate-400">
              Paid via {{ ucfirst($invoicePayment->provider) }} · Ref: {{ $invoicePayment->reference }}
            </div>
          @endif
        @else
          @if ($paymentOption === 'platform')
            <div class="flex justify-between text-slate-600">
              <span>Escrow Protection Fee ({{ $escrowPercentage }}%@if($escrowCap && $escrowFee >= $escrowCap), capped at {{ $invoice->currency_symbol }}{{ number_format($escrowCap) }}@endif):</span>
              <span class="font-bold text-pp-700">+{{ $invoice->currency_symbol }}{{ number_format($escrowFee) }}</span>
            </div>
          @else
            <div class="flex justify-between text-slate-500 text-[11px]">
              <span>Escrow Protection Fee:</span>
              <span class="font-bold text-amber-700">NO ESCROW (Direct Transfer)</span>
            </div>
          @endif
          <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
            <span class="font-bold text-slate-900">Total Amount Due:</span>
            <span class="text-2xl font-black text-slate-950">{{ $invoice->currency_symbol }}{{ number_format($totalPayable) }}</span>
          </div>
        @endif
      </div>
    </div>

  </div>

  <!-- ========================================================================= -->
  <!-- ITEM REVIEW SECTION (UNLOCKED AFTER DEAL COMPLETION) -->
  <!-- ========================================================================= -->
  @if (in_array($invoice->status, ['accepted']) || ($invoice->payment_method === 'direct' && $invoice->status === 'paid'))
    <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-soft space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i> Review Your Experience
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Share feedback on the item received from <strong>{{ $invoice->seller->business_name ?: ($invoice->seller->name ?? 'the seller') }}</strong>.</p>
        </div>

        @if ($isBuyer)
          <button wire:click="reportDidNotBuy" type="button" class="text-xs text-rose-600 hover:text-rose-800 hover:underline font-bold self-start sm:self-auto flex items-center gap-1 cursor-pointer">
            <i class="fas fa-times-circle"></i> I didn't buy it
          </button>
        @endif
      </div>

      @if (! $reviewSubmitted && $isBuyer)
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
            <textarea wire:model="reviewComment" rows="2" placeholder="Tell other buyers about item condition, accuracy, packaging..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"></textarea>
          </div>

          <div class="flex items-center justify-between pt-1">
            <button wire:click="submitReview" type="button" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-paper-plane"></i> Submit Item Review
            </button>
            <span class="text-[11px] text-slate-400">Reviews are verified and displayed on seller listings</span>
          </div>
        </div>
      @elseif ($reviewSubmitted)
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
          <i class="fas fa-check-circle text-emerald-600"></i> Your verified review has been recorded. Thank you!
        </div>
      @endif
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- ACTIVE WARRANTY & INSPECTION SUMMARY (WHEN APPLICABLE) -->
  <!-- ========================================================================= -->
  @if ($invoice->hasWarranty() && in_array($invoice->status, ['paid', 'accepted']))
    <div class="bg-white rounded-3xl border border-blue-200 p-6 shadow-soft space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blue-100 pb-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0 border border-blue-200">
            <i class="fas fa-shield-halved"></i>
          </div>
          <div>
            <h3 class="text-xs font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
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
            </h3>
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
          @endif
          <button wire:click="switchTab('warranty')" type="button" class="text-xs font-black text-blue-700 hover:text-blue-900 flex items-center gap-1 cursor-pointer">
            <span>View All Claims</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
          </button>
        </div>
      </div>

      <div class="space-y-3 text-xs">
        @foreach ($invoice->items->where('warranty_period_days', '>', 0) as $wItem)
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
        @endforeach
      </div>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- FINANCIAL SETTLEMENT & AUDIT TRAIL LINKAGE -->
  <!-- ========================================================================= -->
  <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-pp-950 text-white shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-pp-500/20 border border-pp-400/30 text-pp-300 flex items-center justify-center text-lg font-black shadow-inner">
          <i class="fas fa-arrows-split-up-and-left"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-white flex items-center gap-2">
            Financial Settlement Linkage
            <span class="px-2.5 py-0.5 rounded-full bg-pp-500/20 text-pp-300 text-[10px] font-black uppercase tracking-wider border border-pp-400/30">
              Audit Trail
            </span>
          </h3>
          <p class="text-xs text-slate-300 mt-0.5">
            Real-time linkage connecting the commercial Invoice, Buyer Payment transaction, and Seller Settlement ledger.
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto">
        @if ($invoiceSettlement)
          <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase flex items-center gap-1.5 border {{ $invoiceSettlement->status === 'settled' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : ($invoiceSettlement->status === 'eligible' ? 'bg-blue-500/20 text-blue-300 border-blue-400/30' : 'bg-amber-500/20 text-amber-300 border-amber-400/30') }}">
            <i class="fas {{ $invoiceSettlement->status === 'settled' ? 'fa-check-circle' : ($invoiceSettlement->status === 'eligible' ? 'fa-hourglass-half' : 'fa-lock') }}"></i>
            <span>Settlement: {{ ucfirst($invoiceSettlement->status) }}</span>
          </span>
        @else
          <span class="px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-slate-300 text-xs font-bold flex items-center gap-1.5">
            <i class="fas fa-clock"></i>
            <span>Settlement: Pending Payment</span>
          </span>
        @endif
      </div>
    </div>

    <!-- 3-PILLAR TRANSACTION CHAIN -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs pt-1">
      
      <!-- PILLAR 1: INVOICE -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5 hover:bg-white/10 transition">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-pp-300 flex items-center gap-1.5">
            <i class="fas fa-file-invoice"></i> 1. Commercial Invoice
          </span>
          <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $invoice->status === 'paid' || $invoice->status === 'accepted' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
            {{ $invoice->status }}
          </span>
        </div>

        <div class="space-y-0.5">
          <strong class="text-sm font-black text-white block">#{{ $invoice->invoice_number }}</strong>
          <span class="text-[11px] text-slate-300 block">
            Method: {{ $invoice->payment_method === 'direct' ? 'Direct Bank Transfer' : 'Platform Escrow' }}
          </span>
        </div>

        <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px]">
          <span class="text-slate-400">Invoice Total:</span>
          <strong class="text-white font-extrabold text-xs">{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</strong>
        </div>
      </div>

      <!-- PILLAR 2: PAYMENT RECORD -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5 hover:bg-white/10 transition">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-pp-300 flex items-center gap-1.5">
            <i class="fas fa-credit-card"></i> 2. Buyer Payment
          </span>
          @if ($invoicePayment)
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ in_array($invoicePayment->status, ['successful', 'paid', 'held_in_escrow']) ? 'bg-emerald-500/20 text-emerald-300' : ($invoicePayment->status === 'failed' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300') }}">
              {{ $invoicePayment->status }}
            </span>
          @elseif ($invoice->status === 'paid' && $invoice->payment_method === 'direct')
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300">
              Direct Paid
            </span>
          @else
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-slate-500/20 text-slate-400">
              Unpaid
            </span>
          @endif
        </div>

        <div class="space-y-0.5">
          @if ($invoicePayment)
            <strong class="text-xs font-black text-white block truncate">Ref: {{ $invoicePayment->reference }}</strong>
            <span class="text-[11px] text-slate-300 block">
              Gateway: {{ ucfirst($invoicePayment->provider) }} · {{ $invoicePayment->currency }}
            </span>
          @elseif ($invoice->payment_method === 'direct')
            <strong class="text-xs font-black text-white block">Direct Bank Transfer</strong>
            <span class="text-[11px] text-slate-300 block">Confirmed by seller</span>
          @else
            <strong class="text-xs font-black text-slate-400 block">Awaiting Buyer Payment</strong>
            <span class="text-[11px] text-slate-400 block">No transaction record yet</span>
          @endif
        </div>

        <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px]">
          <span class="text-slate-400">Amount Charged:</span>
          @if ($invoicePayment)
            <strong class="text-white font-extrabold text-xs">{{ $invoicePayment->currency_symbol }}{{ number_format($invoicePayment->amount, 2) }}</strong>
          @else
            <strong class="text-slate-300 font-extrabold text-xs">{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}</strong>
          @endif
        </div>
      </div>

      <!-- PILLAR 3: SETTLEMENT RECORD -->
      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5 hover:bg-white/10 transition">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-pp-300 flex items-center gap-1.5">
            <i class="fas fa-vault"></i> 3. Seller Settlement
          </span>
          @if ($invoiceSettlement)
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $invoiceSettlement->status === 'settled' ? 'bg-emerald-500/20 text-emerald-300' : ($invoiceSettlement->status === 'eligible' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300') }}">
              {{ $invoiceSettlement->status }}
            </span>
          @else
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-slate-500/20 text-slate-400">
              Not Created
            </span>
          @endif
        </div>

        <div class="space-y-0.5">
          @if ($invoiceSettlement)
            <strong class="text-xs font-black text-white block">
              Settlement #SET-{{ str_pad($invoiceSettlement->id, 5, '0', STR_PAD_LEFT) }}
            </strong>
            <span class="text-[11px] text-slate-300 block">
              @if ($invoiceSettlement->status === 'settled')
                Settled on {{ $invoiceSettlement->settled_at?->format('M d, Y') ?? 'Confirmed' }}
              @elseif ($invoiceSettlement->status === 'eligible')
                Eligible since {{ $invoiceSettlement->eligible_at?->format('M d, Y') ?? 'Now' }}
              @else
                Locked in Escrow until deal completion
              @endif
            </span>
          @else
            <strong class="text-xs font-black text-slate-400 block">Pending Escrow Inflow</strong>
            <span class="text-[11px] text-slate-400 block">Creates upon verified payment</span>
          @endif
        </div>

        <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px]">
          <span class="text-slate-400">Net Seller Earnings:</span>
          @if ($invoiceSettlement)
            <strong class="text-emerald-400 font-black text-xs">{{ $invoiceSettlement->currency_symbol }}{{ number_format($invoiceSettlement->amount, 2) }}</strong>
          @else
            <strong class="text-slate-400 font-extrabold text-xs">{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</strong>
          @endif
        </div>
      </div>

    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- FINANCIAL PORTFOLIO & MULTI-CURRENCY LEDGER -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-soft space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-pp-50 text-pp-600 flex items-center justify-center text-base shrink-0 border border-pp-200/60 shadow-2xs">
          <i class="fas fa-wallet"></i>
        </div>
        <div>
          <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <span>Financial Portfolio &amp; Multi-Currency Ledger</span>
          </h2>
          <p class="text-[11px] text-slate-500 mt-0.5">
            Real-time breakdown of your seller earnings from settlements and buyer spendings across all currencies.
          </p>
        </div>
      </div>

      <!-- TABS & COLLAPSE TOGGLE -->
      <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
        <div class="flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-extrabold">
          <button
            type="button"
            wire:click="setFinancialTab('earnings')"
            class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer {{ $financialTab === 'earnings' ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
          >
            <i class="fas fa-hand-holding-dollar text-emerald-600"></i>
            <span>My Earnings</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $financialTab === 'earnings' ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-200 text-slate-700' }}">
              {{ count($userEarnings) }}
            </span>
          </button>

          <button
            type="button"
            wire:click="setFinancialTab('spendings')"
            class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer {{ $financialTab === 'spendings' ? 'bg-white text-pp-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
          >
            <i class="fas fa-cart-shopping text-pp-600"></i>
            <span>My Spendings</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $financialTab === 'spendings' ? 'bg-pp-100 text-pp-900' : 'bg-slate-200 text-slate-700' }}">
              {{ count($userSpendings) }}
            </span>
          </button>
        </div>

        <button
          type="button"
          wire:click="toggleFinancialSummary"
          class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-800 transition text-xs"
          title="{{ $showFinancialSummary ? 'Collapse Portfolio' : 'Expand Portfolio' }}"
        >
          <i class="fas fa-chevron-{{ $showFinancialSummary ? 'up' : 'down' }}"></i>
        </button>
      </div>
    </div>

    @if ($showFinancialSummary)
      @if ($financialTab === 'earnings')
        <div>
          <div class="flex items-center justify-between mb-3 text-[11px]">
            <span class="font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
              <i class="fas fa-coins text-emerald-600"></i> Seller Earnings From Invoices &amp; Settlements
            </span>
            <span class="text-slate-400">Funds released after buyer delivery acceptance &amp; warranty inspection</span>
          </div>

          @if (empty($userEarnings))
            <div class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200 text-center text-xs space-y-1">
              <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-400 grid place-items-center mx-auto text-base mb-2">
                <i class="fas fa-wallet"></i>
              </div>
              <p class="font-extrabold text-slate-800">No seller earnings recorded yet</p>
              <p class="text-slate-500 text-[11px]">When buyers pay for your listings or commercial offers, net settlement earnings will appear here grouped by currency.</p>
            </div>
          @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
              @foreach ($userEarnings as $curr => $data)
                <div class="p-4 rounded-2xl bg-gradient-to-br from-white to-slate-50/60 border border-slate-200/90 shadow-2xs hover:shadow-soft transition space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-black text-xs uppercase flex items-center gap-1">
                      <span>{{ $data['currency'] }}</span>
                      <span class="text-emerald-600 font-normal">({{ $data['symbol'] }})</span>
                    </span>
                    <span class="text-[10px] font-extrabold text-slate-400">
                      {{ $data['count'] }} {{ Str::plural('settlement', $data['count']) }}
                    </span>
                  </div>

                  <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Earnings</span>
                    <span class="text-xl font-black text-slate-950 block tracking-tight">
                      {{ $data['symbol'] }}{{ number_format($data['total'], 2) }}
                    </span>
                  </div>

                  <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-[10px]">
                    <div class="bg-emerald-50/60 p-2 rounded-xl border border-emerald-200/50">
                      <span class="font-bold text-emerald-800 block text-[9px] uppercase">Settled</span>
                      <strong class="font-extrabold text-emerald-950 block text-[11px] truncate">{{ $data['symbol'] }}{{ number_format($data['settled'], 2) }}</strong>
                    </div>
                    <div class="bg-blue-50/60 p-2 rounded-xl border border-blue-200/50">
                      <span class="font-bold text-blue-800 block text-[9px] uppercase">Eligible</span>
                      <strong class="font-extrabold text-blue-950 block text-[11px] truncate">{{ $data['symbol'] }}{{ number_format($data['eligible'], 2) }}</strong>
                    </div>
                    <div class="bg-amber-50/60 p-2 rounded-xl border border-amber-200/50">
                      <span class="font-bold text-amber-800 block text-[9px] uppercase">Escrow Held</span>
                      <strong class="font-extrabold text-amber-950 block text-[11px] truncate">{{ $data['symbol'] }}{{ number_format($data['pending'], 2) }}</strong>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @else
        <div>
          <div class="flex items-center justify-between mb-3 text-[11px]">
            <span class="font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
              <i class="fas fa-receipt text-pp-600"></i> Buyer Outflows Across Verified Orders
            </span>
            <span class="text-slate-400">Total amount paid including escrow fees and listing charges</span>
          </div>

          @if (empty($userSpendings))
            <div class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200 text-center text-xs space-y-1">
              <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-400 grid place-items-center mx-auto text-base mb-2">
                <i class="fas fa-cart-shopping"></i>
              </div>
              <p class="font-extrabold text-slate-800">No buyer purchases recorded yet</p>
              <p class="text-slate-500 text-[11px]">Completed buyer payments and escrow orders will appear here grouped by currency.</p>
            </div>
          @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
              @foreach ($userSpendings as $curr => $data)
                <div class="p-4 rounded-2xl bg-gradient-to-br from-white to-slate-50/60 border border-slate-200/90 shadow-2xs hover:shadow-soft transition space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-xl bg-pp-50 text-pp-800 border border-pp-200/80 font-black text-xs uppercase flex items-center gap-1">
                      <span>{{ $data['currency'] }}</span>
                      <span class="text-pp-600 font-normal">({{ $data['symbol'] }})</span>
                    </span>
                    <span class="text-[10px] font-extrabold text-slate-400">
                      {{ $data['count'] }} {{ Str::plural('order', $data['count']) }}
                    </span>
                  </div>

                  <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Outflow (Spent)</span>
                    <span class="text-xl font-black text-slate-950 block tracking-tight">
                      {{ $data['symbol'] }}{{ number_format($data['total'], 2) }}
                    </span>
                  </div>

                  <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[10px]">
                    <div class="bg-pp-50/60 p-2 rounded-xl border border-pp-200/50">
                      <span class="font-bold text-pp-700 block text-[9px] uppercase">Escrow Fees Paid</span>
                      <strong class="font-extrabold text-pp-950 block text-[11px] truncate">{{ $data['symbol'] }}{{ number_format($data['escrow_fee'], 2) }}</strong>
                    </div>
                    <div class="bg-slate-100/70 p-2 rounded-xl border border-slate-200/60">
                      <span class="font-bold text-slate-600 block text-[9px] uppercase">Completed Orders</span>
                      <strong class="font-extrabold text-slate-900 block text-[11px] truncate">{{ $data['count'] }} orders</strong>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @endif
    @endif
  </div>

</div>
