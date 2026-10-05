<div class="flex flex-col gap-6">
  
  <!-- HEADER & TOP SCOPE TABS -->
  <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Commercial Invoices</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track commercial invoice records, Escrow payment states, and billing history.</p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <!-- SCOPE TABS: All, Buying (I'm Paying), Selling (I'm Receiving) -->
      <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-200 text-xs font-bold shadow-2xs">
        <button 
          wire:click="setScope('all')" 
          type="button"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer {{ $scope === 'all' ? 'bg-pp-600 text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-100 font-bold' }}"
        >
          All ({{ $allCount }})
        </button>

        <button 
          wire:click="setScope('buying')" 
          type="button"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $scope === 'buying' ? 'bg-blue-600 text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700 font-bold' }}"
        >
          <span>Buying <span class="hidden sm:inline font-normal text-[11px] opacity-90">(I'm Paying)</span></span>
          <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $scope === 'buying' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $buyingCount }}</span>
        </button>

        <button 
          wire:click="setScope('selling')" 
          type="button"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $scope === 'selling' ? 'bg-emerald-600 text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 font-bold' }}"
        >
          <span>Selling <span class="hidden sm:inline font-normal text-[11px] opacity-90">(I'm Receiving)</span></span>
          <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $scope === 'selling' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $sellingCount }}</span>
        </button>
      </div>
      
    </div>
  </div>

  <!-- METRIC VOLUME CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Total Invoice Volume</span>
      <span class="text-2xl font-black text-slate-950">
        ₦{{ number_format($totalVolume, 2) }}
      </span>
      <span class="text-[11px] text-slate-500 font-medium block">
        {{ $totalInvoicesCount }} {{ Str::plural('Commercial Invoice', $totalInvoicesCount) }}
      </span>
    </div>

    <div class="bg-white rounded-3xl border-2 border-pp-500 p-5 shadow-soft space-y-1">
      <span class="text-xs text-pp-700 font-bold uppercase tracking-wider block flex items-center gap-1.5">
        <i class="fas fa-shield-alt text-pp-600"></i> Escrow Protected Volume
      </span>
      <span class="text-2xl font-black text-pp-700">
        ₦{{ number_format($escrowVolume, 2) }}
      </span>
      <span class="text-[11px] text-emerald-600 font-bold block flex items-center gap-1">
        <i class="fas fa-check-circle"></i> 100% Escrow dispute protection
      </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-amber-800 font-bold uppercase tracking-wider block flex items-center gap-1.5">
        <i class="fas fa-university text-amber-600"></i> Direct Seller Invoices
      </span>
      <span class="text-2xl font-black text-slate-900">
        ₦{{ number_format($directVolume, 2) }}
      </span>
      <span class="text-[11px] text-amber-700 font-bold block">Direct bank transfers</span>
    </div>
  </div>

  <!-- FILTERS BAR -->
  <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-4">
    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2 text-xs font-extrabold text-slate-900 uppercase tracking-wider">
        <i class="fas fa-filter text-pp-600"></i>
        <span>Filter Invoices</span>
      </div>
      @if($search || $currency || $paymentMethod || $source || $status || $dateFrom || $dateTo)
        <button 
          wire:click="resetFilters" 
          type="button"
          class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 transition cursor-pointer"
        >
          <i class="fas fa-times-circle"></i> Clear All Filters
        </button>
      @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      <!-- Search Input -->
      <div class="lg:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Search Ref / Party</label>
        <div class="relative">
          <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="INV ref or other party name..."
            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 pl-8 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium placeholder-slate-400 transition"
          >
          <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
        </div>
      </div>

      <!-- Currency Filter -->
      <div>
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Currency</label>
        <select 
          wire:model.live="currency" 
          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition cursor-pointer"
        >
          <option value="">All Currencies</option>
          @foreach($availableCurrencies as $curr)
            <option value="{{ $curr }}">{{ $curr }}</option>
          @endforeach
        </select>
      </div>

      <!-- Payment Method Filter -->
      <div>
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Method</label>
        <select 
          wire:model.live="paymentMethod" 
          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition cursor-pointer"
        >
          <option value="">All Methods</option>
          <option value="platform">Platform (Escrow)</option>
          <option value="direct">Direct Transfer</option>
        </select>
      </div>

      <!-- Source Filter: Marketplace vs Community -->
      <div>
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Source</label>
        <select 
          wire:model.live="source" 
          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition cursor-pointer"
        >
          <option value="">All Sources</option>
          <option value="marketplace">🛒 Marketplace (Cart)</option>
          <option value="community">💬 Community (Discussions)</option>
        </select>
      </div>

      <!-- Status Filter -->
      <div>
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
        <select 
          wire:model.live="status" 
          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition cursor-pointer"
        >
          <option value="">All Statuses</option>
          <option value="paid">Paid</option>
          <option value="issued">Issued (Unpaid)</option>
          <option value="accepted">Accepted</option>
          <option value="partially_paid">Partially Paid</option>
          <option value="draft">Draft</option>
          <option value="cancelled">Cancelled</option>
          <option value="expired">Expired</option>
        </select>
      </div>

      <!-- Date Range (From / To) -->
      <div class="sm:col-span-2 md:col-span-3 lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 border-t border-slate-100">
        <div>
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
          <input 
            type="date" 
            wire:model.live="dateFrom" 
            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition"
          >
        </div>
        <div>
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date To</label>
          <input 
            type="date" 
            wire:model.live="dateTo" 
            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:border-pp-500 text-slate-800 font-medium transition"
          >
        </div>
      </div>
    </div>
  </div>

  <!-- INVOICES TABLE CONTAINER -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-file-invoice-dollar text-pp-600"></i>
        <span>Invoice Records</span>
        <span class="text-slate-400 font-medium">({{ $invoices->total() }})</span>
      </h3>
      <a href="{{ route('invoices.export.excel') }}" class="px-3.5 py-2 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs" title="Export Invoices as Excel Spreadsheet">
        <i class="fas fa-file-excel text-emerald-600"></i>
        <span>Export Excel</span>
      </a>
      <span class="text-xs text-slate-500 font-medium">Sorted by latest issued</span>
    </div>

    <!-- RESPONSIVE TABLE WITH NO-WRAP HEADERS -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-extrabold border-b border-slate-200 text-[11px] tracking-wider">
          <tr>
            <th class="p-3.5 whitespace-nowrap">Invoice Ref</th>
            <th class="p-3.5 whitespace-nowrap">Other Party</th>
            <th class="p-3.5 whitespace-nowrap">Source</th>
            <th class="p-3.5 whitespace-nowrap text-right">Amount</th>
            <th class="p-3.5 whitespace-nowrap">Fulfilment Path</th>
            <th class="p-3.5 whitespace-nowrap">Payment Method</th>
            <th class="p-3.5 whitespace-nowrap">Status</th>
            <th class="p-3.5 whitespace-nowrap text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          @forelse($invoices as $inv)
            @php
              $isCurrentUserBuyer = ($currentUserId === $inv->buyer_id);
              $isCurrentUserSeller = ($currentUserId === $inv->seller_id);
              $isCommunitySource = ($inv->offer && $inv->offer->discussion_id);
            @endphp
            <tr class="hover:bg-slate-50/80 transition">
              <!-- 1. INVOICE REF -->
              <td class="p-3.5 whitespace-nowrap">
                <a href="{{ route('invoices.view', $inv->invoice_number) }}" class="font-extrabold text-pp-600 hover:text-pp-700 hover:underline block text-xs">
                  #{{ $inv->invoice_number }}
                </a>
                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">
                  {{ ($inv->issued_at ?: $inv->created_at)?->format('M d, Y') }}
                </span>
              </td>

              <!-- 2. OTHER PARTY -->
              <td class="p-3.5 whitespace-nowrap">
                @if($isCurrentUserBuyer)
                  <div class="flex items-center gap-2">
                    <div>
                      <span class="font-bold text-slate-900 block">
                        {{ $inv->seller?->business_name ?: ($inv->seller?->name ?: 'Seller') }}
                      </span>
                      <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.2 rounded-full border border-amber-200/60">
                        Seller
                      </span>
                    </div>
                  </div>
                @elseif($isCurrentUserSeller)
                  <div class="flex items-center gap-2">
                    <div>
                      <span class="font-bold text-slate-900 block">
                        {{ $inv->buyer?->business_name ?: ($inv->buyer?->name ?: 'Buyer') }}
                      </span>
                      <span class="inline-flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.2 rounded-full border border-blue-200/60">
                        Buyer
                      </span>
                    </div>
                  </div>
                @else
                  <div class="text-xs">
                    <span class="text-slate-900 font-bold">{{ $inv->buyer?->name ?: 'Buyer' }}</span>
                    <span class="text-slate-400 mx-1">→</span>
                    <span class="text-slate-700 font-semibold">{{ $inv->seller?->name ?: 'Seller' }}</span>
                  </div>
                @endif
              </td>

              <!-- 3. SOURCE -->
              <td class="p-3.5 whitespace-nowrap">
                @if($isCommunitySource)
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-comments text-indigo-500"></i> Community
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-shopping-cart text-emerald-600"></i> Marketplace
                  </span>
                @endif
              </td>

              <!-- 4. AMOUNT -->
              <td class="p-3.5 whitespace-nowrap text-right">
                <span class="font-extrabold text-slate-950 text-sm block">
                  {{ $inv->currency_symbol }}{{ number_format($inv->total, 2) }}
                </span>
                <span class="text-[10px] text-slate-400 font-medium block">
                  {{ $inv->currency }} &bull; {{ $inv->items->count() }} {{ Str::plural('item', $inv->items->count()) }}
                </span>
              </td>

              <!-- 5. FULFILMENT PATH -->
              <td class="p-3.5 whitespace-nowrap">
                @if($inv->delivery_method === 'seller_responsible')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-pp-50 border border-pp-200/80 text-pp-800 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-truck text-pp-600"></i> Seller Delivery
                  </span>
                @elseif($inv->delivery_method === 'buyer_responsible')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-walking text-slate-500"></i> Self-Pickup
                  </span>
                @elseif($inv->delivery_method === 'platform_responsible')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-motorcycle text-purple-500"></i> Community Rider
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold">
                    Standard Fulfilment
                  </span>
                @endif
              </td>

              <!-- 6. PAYMENT METHOD -->
              <td class="p-3.5 whitespace-nowrap">
                @if($inv->payment_method === 'platform')
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-shield-alt text-emerald-600"></i> Escrow Protected
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-[11px] font-bold shadow-2xs">
                    <i class="fas fa-university text-amber-600"></i> Direct Seller Transfer
                  </span>
                @endif
              </td>

              <!-- 7. STATUS -->
              <td class="p-3.5 whitespace-nowrap">
                @if($inv->status === 'paid')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wide">
                    PAID
                  </span>
                @elseif($inv->status === 'issued')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold uppercase tracking-wide">
                    ISSUED
                  </span>
                @elseif($inv->status === 'accepted')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wide">
                    ACCEPTED
                  </span>
                @elseif($inv->status === 'partially_paid')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-900 text-[10px] font-extrabold uppercase tracking-wide">
                    PARTIAL
                  </span>
                @elseif($inv->status === 'cancelled')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold uppercase tracking-wide">
                    CANCELLED
                  </span>
                @elseif($inv->status === 'expired')
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[10px] font-extrabold uppercase tracking-wide">
                    EXPIRED
                  </span>
                @else
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase tracking-wide">
                    {{ strtoupper($inv->status) }}
                  </span>
                @endif
              </td>

              <!-- 8. ACTION -->
              <td class="p-3.5 whitespace-nowrap text-right">
                <a 
                  href="{{ route('invoices.view', $inv->invoice_number) }}" 
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition shadow-2xs"
                >
                  <span>View Invoice</span>
                  <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-10 text-center">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                    <i class="fas fa-file-invoice"></i>
                  </div>
                  <div>
                    <h4 class="text-sm font-bold text-slate-900">No Invoices Found</h4>
                    <p class="text-xs text-slate-500 max-w-sm mt-0.5">
                      @if($search || $currency || $paymentMethod || $source || $status || $dateFrom || $dateTo || $scope !== 'all')
                        No commercial invoices match your current filter parameters. Try clearing or relaxing the filters.
                      @else
                        You do not have any commercial invoices in this category yet.
                      @endif
                    </p>
                  </div>
                  @if($search || $currency || $paymentMethod || $source || $status || $dateFrom || $dateTo)
                    <button 
                      wire:click="resetFilters" 
                      type="button" 
                      class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer"
                    >
                      Clear Filters
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="pt-2 border-t border-slate-100">
      {{ $invoices->links() }}
    </div>
  </div>

</div>