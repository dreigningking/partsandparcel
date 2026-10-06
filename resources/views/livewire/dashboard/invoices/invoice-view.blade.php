<div class="flex flex-col gap-6">

  <!-- ========================================================================= -->
  <!-- TOP BREADCRUMB & HEADER -->
  <!-- ========================================================================= -->
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
        <span>Invoice #{{ $invoice->invoice_number }}</span>
        
        @if ($invoice->status === 'paid')
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-check-circle"></i> PAID {{ $invoice->payment_method === 'direct' ? '(DIRECT TRANSFER)' : '(ESCROW PROTECTED)' }}
          </span>
        @elseif ($invoice->status === 'accepted')
          <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-handshake"></i> DEAL COMPLETED &amp; ACCEPTED
          </span>
        @elseif ($invoice->status === 'cancelled')
          <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-ban"></i> CANCELLED
          </span>
        @else
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-clock"></i> PAYMENT PENDING
          </span>
        @endif
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Issued on {{ ($invoice->issued_at ?: $invoice->created_at)->format('M d, Y') }}
        · Payment Method: <strong class="text-slate-800 uppercase">{{ $invoice->payment_method === 'direct' ? 'Direct Bank Transfer' : 'Parts & Parcel Platform Escrow' }}</strong>
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

  <!-- ========================================================================= -->
  <!-- FLASH MESSAGES -->
  <!-- ========================================================================= -->
  @if (session()->has('seller_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-check-circle text-emerald-600 text-base"></i>
      <span>{{ session('seller_success') }}</span>
    </div>
  @endif

  @if (session()->has('buyer_notice'))
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-info-circle text-blue-600 text-base"></i>
      <span>{{ session('buyer_notice') }}</span>
    </div>
  @endif

  @if (session()->has('buyer_payment_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-shield-alt text-emerald-600 text-base"></i>
      <span>{{ session('buyer_payment_success') }}</span>
    </div>
  @endif

  @if (session()->has('review_success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-xs">
      <i class="fas fa-star text-amber-500 text-base"></i>
      <span>{{ session('review_success') }}</span>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- DYNAMIC TAB NAVIGATION BAR -->
  <!-- ========================================================================= -->
  <div class="bg-white rounded-3xl border border-slate-200 p-2 shadow-soft">
    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1 px-1">

      <!-- TAB 1: DETAILS (ALWAYS VISIBLE) -->
      <button
        type="button"
        wire:click="switchTab('details')"
        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'details' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
      >
        <i class="fas fa-file-invoice {{ $activeTab === 'details' ? 'text-white' : 'text-pp-600' }}"></i>
        <span>Invoice Details</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'details' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
          {{ $invoice->items->count() }}
        </span>
      </button>

      <!-- TAB 2: SERVICE JOB (WHEN INVOICE CONTAINS SERVICES) -->
      @if ($hasServices)
        <button
          type="button"
          wire:click="switchTab('service')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'service' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-screwdriver-wrench {{ $activeTab === 'service' ? 'text-white' : 'text-sky-600' }}"></i>
          <span>Service Job</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'service' ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-800' }}">
            {{ $serviceJobs->count() ?: '1' }}
          </span>
        </button>
      @endif

      <!-- TAB 3: SHIPMENTS (WHEN SHIPMENT OR LOGISTICS EXIST) -->
      @if ($hasShipments)
        <button
          type="button"
          wire:click="switchTab('shipment')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'shipment' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-truck-fast {{ $activeTab === 'shipment' ? 'text-white' : 'text-pp-600' }}"></i>
          <span>Shipment &amp; Tracking</span>
          @if ($outboundShipment)
            <span class="px-1.5 py-0.2 rounded-full text-[9px] uppercase font-black {{ $activeTab === 'shipment' ? 'bg-white/20 text-white' : ($outboundShipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : 'bg-pp-100 text-pp-800') }}">
              {{ $outboundShipment->status }}
            </span>
          @endif
        </button>
      @endif

      <!-- TAB 4: ISSUES (WHEN ISSUES EXIST OR CAN BE VIEWED) -->
      @if ($hasIssues || in_array($invoice->status, ['paid', 'accepted']))
        <button
          type="button"
          wire:click="switchTab('issue')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'issue' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-triangle-exclamation {{ $activeTab === 'issue' ? 'text-white' : 'text-rose-600' }}"></i>
          <span>Issues</span>
          @if ($issues->isNotEmpty())
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'issue' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">
              {{ $issues->count() }}
            </span>
          @endif
        </button>
      @endif

      <!-- TAB 5: REPLACEMENTS (WHEN REPLACEMENT RECORD EXISTS) -->
      @if ($hasReplacements)
        <button
          type="button"
          wire:click="switchTab('replacement')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'replacement' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-repeat {{ $activeTab === 'replacement' ? 'text-white' : 'text-amber-600' }}"></i>
          <span>Replacements</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'replacement' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
            {{ $replacements->count() }}
          </span>
        </button>
      @endif

      <!-- TAB 6: REFUNDS (WHEN REFUNDS EXIST) -->
      @if ($hasRefunds)
        <button
          type="button"
          wire:click="switchTab('refund')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'refund' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-hand-holding-dollar {{ $activeTab === 'refund' ? 'text-white' : 'text-emerald-600' }}"></i>
          <span>Refunds</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'refund' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
            {{ $refunds->count() }}
          </span>
        </button>
      @endif

      <!-- TAB 7: DISPUTES (WHEN DISPUTE RECORD EXISTS) -->
      @if ($hasDisputes)
        <button
          type="button"
          wire:click="switchTab('dispute')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'dispute' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-scale-balanced {{ $activeTab === 'dispute' ? 'text-white' : 'text-purple-600' }}"></i>
          <span>Disputes</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'dispute' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800' }}">
            {{ $disputes->count() }}
          </span>
        </button>
      @endif

      <!-- TAB 8: WARRANTY CLAIMS (WHEN WARRANTY APPLIES) -->
      @if ($hasWarranty)
        <button
          type="button"
          wire:click="switchTab('warranty')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'warranty' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}"
        >
          <i class="fas fa-shield-halved {{ $activeTab === 'warranty' ? 'text-white' : 'text-blue-600' }}"></i>
          <span>Warranty Claims</span>
          @if ($invoice->isWithinWarranty())
            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase {{ $activeTab === 'warranty' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800' }}">
              Active
            </span>
          @endif
        </button>
      @endif

    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- ACTIVE TAB CONTENT -->
  <!-- ========================================================================= -->
  <div>
    @if ($activeTab === 'details')
      @include('livewire.dashboard.invoices.tabs.tab-details')
    @elseif ($activeTab === 'service')
      @include('livewire.dashboard.invoices.tabs.tab-service')
    @elseif ($activeTab === 'shipment')
      @include('livewire.dashboard.invoices.tabs.tab-shipment')
    @elseif ($activeTab === 'issue')
      @include('livewire.dashboard.invoices.tabs.tab-issue')
    @elseif ($activeTab === 'dispute')
      @include('livewire.dashboard.invoices.tabs.tab-dispute')
    @elseif ($activeTab === 'replacement')
      @include('livewire.dashboard.invoices.tabs.tab-replacement')
    @elseif ($activeTab === 'refund')
      @include('livewire.dashboard.invoices.tabs.tab-refund')
    @elseif ($activeTab === 'warranty')
      @include('livewire.dashboard.invoices.tabs.tab-warranty')
    @endif
  </div>

  <!-- ========================================================================= -->
  <!-- MODALS -->
  <!-- ========================================================================= -->
  @include('livewire.dashboard.invoices.tabs.tab-modals')

</div>