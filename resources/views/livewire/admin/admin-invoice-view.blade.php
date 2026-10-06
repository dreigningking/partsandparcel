<div class="space-y-6">

  <!-- ========================================================================= -->
  <!-- BREADCRUMBS & TOP HEADER -->
  <!-- ========================================================================= -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-soft">
    <div>
      <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
        <span>/</span>
        <a href="{{ route('admin.invoices') }}" class="hover:text-pp-600 transition">Invoices</a>
        <span>/</span>
        <span class="text-pp-600 dark:text-pp-400 font-mono font-bold">{{ $invoice->invoice_number }}</span>
      </div>

      <div class="flex items-center gap-3 flex-wrap mt-1">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-950 dark:text-white tracking-tight flex items-center gap-2.5">
          <span>Invoice {{ $invoice->invoice_number }}</span>
        </h1>

        <!-- INVOICE STATUS BADGE -->
        @php
          $statusBadge = match($invoice->status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            'accepted', 'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            'refunded' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
            default => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
          };
        @endphp
        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusBadge }}">
          {{ $invoice->status }}
        </span>

        <!-- PAYMENT METHOD BADGE -->
        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $invoice->payment_method === 'platform' ? 'bg-pp-100 text-pp-800 border border-pp-200 dark:bg-pp-950/60 dark:text-pp-300 dark:border-pp-800' : 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
          {{ $invoice->payment_method === 'platform' ? 'Platform Escrow' : 'Direct Transfer' }}
        </span>
      </div>

      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-2 flex-wrap">
        <span>Issued {{ $invoice->created_at->format('M d, Y · h:i A') }}</span>
        <span>·</span>
        <span>Currency: <strong class="text-slate-700 dark:text-slate-300">{{ $invoice->currency }} ({{ $invoice->currency_symbol }})</strong></span>
        @if ($invoice->due_at)
          <span>·</span>
          <span>Due: {{ $invoice->due_at->format('M d, Y') }}</span>
        @endif
      </p>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
      <a
        href="{{ route('admin.invoices') }}"
        class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
      >
        <i class="fas fa-arrow-left text-xs"></i>
        <span>All Invoices</span>
      </a>

      @if ($invoice->isPlatformEscrow() && $invoice->status === 'paid' && (! $settlement || $settlement->status !== 'settled'))
        <button
          type="button"
          wire:click="$set('showEscrowReleaseModal', true)"
          class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
        >
          <i class="fas fa-unlock"></i>
          <span>Release Escrow</span>
        </button>
      @endif

      @if (! in_array($invoice->status, ['cancelled', 'refunded']))
        <button
          type="button"
          wire:click="$set('showCancelModal', true)"
          class="px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/60 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-700 dark:text-rose-400 font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer"
        >
          <i class="fas fa-ban text-xs"></i>
          <span>Cancel</span>
        </button>
      @endif
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session('status'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-check-circle text-emerald-500 text-base"></i>
        <span>{{ session('status') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  @if (session('error'))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 dark:border-rose-800/60 dark:bg-rose-950/40 p-4 text-xs font-bold text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- ========================================================================= -->
  <!-- BUYER & SELLER COMMERCIAL ENTITY CARDS -->
  <!-- ========================================================================= -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- BUYER SUMMARY -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-soft space-y-3">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <i class="fas fa-user text-pp-600"></i> Buyer Information
        </span>
        @if ($invoice->buyer)
          <a href="{{ route('admin.users.show', $invoice->buyer->id) }}" class="text-[11px] font-bold text-pp-600 hover:underline">
            View Profile &rarr;
          </a>
        @endif
      </div>
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 flex items-center justify-center text-base font-black shrink-0 border border-pp-200/60 dark:border-pp-800/60">
          {{ strtoupper(substr($invoice->buyer?->name ?? 'B', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="text-sm font-black text-slate-900 dark:text-white truncate">
            {{ $invoice->buyer?->name ?? 'Unregistered Buyer' }}
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $invoice->buyer?->email }}</p>
          @if ($invoice->buyer?->phone)
            <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $invoice->buyer->phone }}</p>
          @endif
        </div>
      </div>
    </div>

    <!-- SELLER SUMMARY -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-soft space-y-3">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <i class="fas fa-store text-emerald-600"></i> Seller / Merchant Information
        </span>
        @if ($invoice->seller)
          <a href="{{ route('admin.users.show', $invoice->seller->id) }}" class="text-[11px] font-bold text-emerald-600 hover:underline">
            View Profile &rarr;
          </a>
        @endif
      </div>
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-base font-black shrink-0 border border-emerald-200/60 dark:border-emerald-800/60">
          {{ strtoupper(substr($invoice->seller?->business_name ?: ($invoice->seller?->name ?? 'S'), 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="text-sm font-black text-slate-900 dark:text-white truncate">
            {{ $invoice->seller?->business_name ?: ($invoice->seller?->name ?? 'Merchant') }}
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $invoice->seller?->email }}</p>
          @if ($invoice->seller?->phone)
            <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $invoice->seller->phone }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB NAVIGATION BAR -->
  <!-- ========================================================================= -->
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-2 shadow-soft">
    <div class="flex items-center gap-1 overflow-x-auto custom-scrollbar pb-1 sm:pb-0">

      <!-- 1. DETAILS TAB -->
      <button
        type="button"
        wire:click="switchTab('details')"
        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'details' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
      >
        <i class="fas fa-file-invoice {{ $activeTab === 'details' ? 'text-pp-400 dark:text-pp-600' : 'text-slate-400' }}"></i>
        <span>Invoice Details</span>
      </button>

      <!-- 2. SHIPMENTS TAB -->
      <button
        type="button"
        wire:click="switchTab('shipment')"
        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'shipment' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
      >
        <i class="fas fa-truck-fast {{ $activeTab === 'shipment' ? 'text-white' : 'text-pp-600 dark:text-pp-400' }}"></i>
        <span>Shipments &amp; Tracking</span>
        @if ($allShipments->isNotEmpty())
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'shipment' ? 'bg-white/20 text-white' : 'bg-pp-100 text-pp-800 dark:bg-pp-900/60 dark:text-pp-300' }}">
            {{ $allShipments->count() }}
          </span>
        @endif
      </button>

      <!-- 3. SERVICES TAB (IF EXISTS) -->
      @if ($hasServices)
        <button
          type="button"
          wire:click="switchTab('service')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'service' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
        >
          <i class="fas fa-screwdriver-wrench {{ $activeTab === 'service' ? 'text-white' : 'text-sky-600' }}"></i>
          <span>Service Jobs</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'service' ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300' }}">
            {{ $serviceJobs->count() }}
          </span>
        </button>
      @endif

      <!-- 4. ISSUES TAB -->
      <button
        type="button"
        wire:click="switchTab('issue')"
        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'issue' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
      >
        <i class="fas fa-triangle-exclamation {{ $activeTab === 'issue' ? 'text-white' : 'text-amber-600' }}"></i>
        <span>Issues</span>
        @if ($issues->isNotEmpty())
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'issue' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' }}">
            {{ $issues->count() }}
          </span>
        @endif
      </button>

      <!-- 5. DISPUTES TAB -->
      @if ($hasDisputes)
        <button
          type="button"
          wire:click="switchTab('dispute')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'dispute' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
        >
          <i class="fas fa-scale-balanced {{ $activeTab === 'dispute' ? 'text-white' : 'text-purple-600' }}"></i>
          <span>Disputes</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'dispute' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300' }}">
            {{ $disputes->count() }}
          </span>
        </button>
      @endif

      <!-- 6. REPLACEMENTS TAB -->
      @if ($hasReplacements)
        <button
          type="button"
          wire:click="switchTab('replacement')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'replacement' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
        >
          <i class="fas fa-boxes-packing {{ $activeTab === 'replacement' ? 'text-white' : 'text-teal-600' }}"></i>
          <span>Replacements</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'replacement' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-800 dark:bg-teal-900/60 dark:text-teal-300' }}">
            {{ $replacements->count() }}
          </span>
        </button>
      @endif

      <!-- 7. REFUNDS TAB -->
      @if ($hasRefunds)
        <button
          type="button"
          wire:click="switchTab('refund')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'refund' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
        >
          <i class="fas fa-hand-holding-dollar {{ $activeTab === 'refund' ? 'text-white' : 'text-emerald-600' }}"></i>
          <span>Refunds</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $activeTab === 'refund' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' }}">
            {{ $refunds->count() }}
          </span>
        </button>
      @endif

      <!-- 8. WARRANTY TAB -->
      @if ($hasWarranty)
        <button
          type="button"
          wire:click="switchTab('warranty')"
          class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeTab === 'warranty' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}"
        >
          <i class="fas fa-shield-halved {{ $activeTab === 'warranty' ? 'text-white' : 'text-blue-600' }}"></i>
          <span>Warranty Claims</span>
          @if ($invoice->isWithinWarranty())
            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase {{ $activeTab === 'warranty' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300' }}">
              Active
            </span>
          @endif
        </button>
      @endif

    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB CONTENT -->
  <!-- ========================================================================= -->
  <div>
    @if ($activeTab === 'details')
      @include('livewire.admin.tabs.tab-details')
    @elseif ($activeTab === 'shipment')
      @include('livewire.admin.tabs.tab-shipment')
    @elseif ($activeTab === 'service')
      @include('livewire.admin.tabs.tab-service')
    @elseif ($activeTab === 'issue')
      @include('livewire.admin.tabs.tab-issue')
    @elseif ($activeTab === 'dispute')
      @include('livewire.admin.tabs.tab-dispute')
    @elseif ($activeTab === 'replacement')
      @include('livewire.admin.tabs.tab-replacement')
    @elseif ($activeTab === 'refund')
      @include('livewire.admin.tabs.tab-refund')
    @elseif ($activeTab === 'warranty')
      @include('livewire.admin.tabs.tab-warranty')
    @endif
  </div>

  <!-- ========================================================================= -->
  <!-- ADMIN MODALS -->
  <!-- ========================================================================= -->

  <!-- ESCROW RELEASE MODAL -->
  @if ($showEscrowReleaseModal)
    <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center gap-3 text-emerald-600">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-lg">
            <i class="fas fa-unlock"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Confirm Escrow Release</h3>
            <p class="text-[11px] text-slate-500">Invoice {{ $invoice->invoice_number }}</p>
          </div>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
          Are you sure you want to manually release escrow funds to the seller? This will mark the seller's settlement as settled and complete the commercial transaction.
        </p>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            wire:click="$set('showEscrowReleaseModal', false)"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="confirmEscrowRelease"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
          >
            Yes, Release Escrow
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- CANCEL INVOICE MODAL -->
  @if ($showCancelModal)
    <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center gap-3 text-rose-600">
          <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/60 flex items-center justify-center text-lg">
            <i class="fas fa-ban"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Cancel Invoice</h3>
            <p class="text-[11px] text-slate-500">Invoice {{ $invoice->invoice_number }}</p>
          </div>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
          Are you sure you want to cancel this invoice? Any pending buyer payment links will be invalidated.
        </p>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            wire:click="$set('showCancelModal', false)"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
          >
            Close
          </button>
          <button
            type="button"
            wire:click="confirmCancelInvoice"
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
          >
            Confirm Cancellation
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- UPDATE SHIPMENT STATUS MODAL -->
  @if ($showShipmentStatusModal)
    <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-truck-fast text-pp-600"></i>
            <span>Override Shipment Status</span>
          </h3>
          <button wire:click="$set('showShipmentStatusModal', false)" class="text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">New Shipment Status</label>
            <select
              wire:model="newShipmentStatus"
              class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
              <option value="pending">Pending Dispatch</option>
              <option value="dispatched">Dispatched</option>
              <option value="in_transit">In Transit</option>
              <option value="delivered">Delivered</option>
              <option value="returned">Returned</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            wire:click="$set('showShipmentStatusModal', false)"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="updateShipmentStatus"
            class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
          >
            Save Status
          </button>
        </div>
      </div>
    </div>
  @endif

</div>
