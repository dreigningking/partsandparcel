<div class="flex flex-col gap-6">

  <!-- TOP HEADER & BREADCRUMB -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-extrabold text-pp-600 uppercase tracking-wider">Admin Control Center</span>
        <span class="text-slate-300">/</span>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Marketplace</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-2.5">
        <span>Listings Management</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold">
          {{ number_format($metrics['total']) }} Total
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Audit, moderate, and monitor all public parts, disassembled components, and whole unit listings across the marketplace.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.moderations', ['type' => 'listing']) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fas fa-gavel text-amber-400"></i>
        <span>Moderation Queue</span>
        @if($metrics['pending'] > 0)
          <span class="px-1.5 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black">
            {{ $metrics['pending'] }}
          </span>
        @endif
      </a>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-exclamation-triangle text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- METRIC KPI CARDS -->
  <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
    
    <!-- 1. TOTAL LISTINGS -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-pp-50 text-pp-600 border border-pp-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-boxes-stacked"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Catalog</span>
        <span class="text-lg font-black text-slate-950">{{ number_format($metrics['total']) }}</span>
      </div>
    </div>

    <!-- 2. LIVE & ACTIVE -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-circle-check"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Live &amp; Public</span>
        <span class="text-lg font-black text-emerald-600">{{ number_format($metrics['live']) }}</span>
      </div>
    </div>

    <!-- 3. PENDING REVIEW -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3 {{ $metrics['pending'] > 0 ? 'ring-2 ring-amber-400/50 bg-amber-50/20' : '' }}">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pending Review</span>
        <span class="text-lg font-black text-amber-600">{{ number_format($metrics['pending']) }}</span>
      </div>
    </div>

    <!-- 4. DEPLETED / SOLD OUT -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-dolly"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sold Out</span>
        <span class="text-lg font-black text-purple-700">{{ number_format($metrics['sold_out']) }}</span>
      </div>
    </div>

    <!-- 5. TOTAL INVENTORY VALUE -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3 col-span-2 sm:col-span-1">
      <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-coins"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Listed Value</span>
        <span class="text-base font-black text-slate-950">₦{{ number_format($metrics['inventory_value'], 0) }}</span>
      </div>
    </div>

  </div>

  <!-- FILTER CONTROLS BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
    <div class="flex items-center justify-between flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-filter text-pp-600"></i> Search &amp; Filter Catalog
      </h3>

      @if($search || $selectedCategory || $selectedBrand || $selectedType || $selectedCondition || $selectedStatus || $shippingOnly || $priceSort)
        <button type="button" wire:click="resetFilters" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1 cursor-pointer">
          <i class="fas fa-undo text-[10px]"></i> Reset All Filters
        </button>
      @endif
    </div>

    <!-- FILTER GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
      
      <!-- 1. SEARCH INPUT -->
      <div class="lg:col-span-2">
        <label class="block font-bold text-slate-700 mb-1">Search Keywords</label>
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Title, model, brand, seller name, #ID..."
            class="w-full pl-8 pr-3 p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 bg-slate-50/50 transition"
          />
        </div>
      </div>

      <!-- 2. CATEGORY DROPDOWN -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Category</label>
        <select wire:model.live="selectedCategory" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 3. BRAND DROPDOWN -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Brand</label>
        <select wire:model.live="selectedBrand" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Brands</option>
          @foreach($brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 4. ITEM TYPE (Whole, Part, Scrap) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Item Type</label>
        <select wire:model.live="selectedType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Types</option>
          <option value="whole">Whole Unit</option>
          <option value="part">Harvested Part</option>
          <option value="scrap">Scrap Material</option>
        </select>
      </div>

      <!-- 5. STATUS -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Status</label>
        <select wire:model.live="selectedStatus" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Statuses</option>
          <option value="live">Live (Active)</option>
          <option value="pending">Pending Review</option>
          <option value="rejected">Rejected</option>
          <option value="draft">Draft (Unpublished)</option>
          <option value="sold_out">Sold Out (0 Qty)</option>
          <option value="inactive">Inactive / Unsubscribed</option>
        </select>
      </div>

    </div>

    <!-- FILTER ROW 2: CONDITION, SORTING, SHIPPING -->
    <div class="flex items-center justify-between flex-wrap gap-3 pt-2 border-t border-slate-100 text-xs">
      <div class="flex items-center gap-3 flex-wrap">
        
        <!-- CONDITION -->
        <div class="flex items-center gap-1.5">
          <span class="text-slate-500 font-bold">Condition:</span>
          <select wire:model.live="selectedCondition" class="p-1.5 px-2.5 rounded-lg border border-slate-200 text-xs text-slate-800 bg-slate-50">
            <option value="">All Conditions</option>
            <option value="new">Brand New</option>
            <option value="used">Used / Tested</option>
            <option value="refurbished">Refurbished</option>
            <option value="for_parts">For Parts / Repair</option>
          </select>
        </div>

        <!-- SORT -->
        <div class="flex items-center gap-1.5">
          <span class="text-slate-500 font-bold">Sort By:</span>
          <select wire:model.live="priceSort" class="p-1.5 px-2.5 rounded-lg border border-slate-200 text-xs text-slate-800 bg-slate-50">
            <option value="">Latest Created</option>
            <option value="high_low">Price: High to Low</option>
            <option value="low_high">Price: Low to High</option>
            <option value="qty_desc">Highest Stock</option>
            <option value="sold_desc">Most Sold</option>
            <option value="oldest">Oldest First</option>
          </select>
        </div>

        <!-- SHIPPING TOGGLE -->
        <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 ml-2 select-none">
          <input type="checkbox" wire:model.live="shippingOnly" class="rounded accent-pp-600 w-3.5 h-3.5" />
          <span>Offers Nationwide Shipping Only</span>
        </label>
      </div>

      <div class="text-[11px] text-slate-400 font-semibold">
        Showing {{ $listings->firstItem() ?? 0 }} - {{ $listings->lastItem() ?? 0 }} of {{ $listings->total() }} records
      </div>
    </div>

  </div>

  <!-- LISTINGS DATA TABLE CONTAINER -->
  <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
    
    <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list-check text-pp-600"></i> Listings Catalog ({{ $listings->total() }})
      </h3>
      <span class="text-xs text-slate-400 font-semibold">Page {{ $listings->currentPage() }} of {{ $listings->lastPage() ?: 1 }}</span>
    </div>

    <!-- RESPONSIVE TABLE WRAPPER -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
        <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
          <tr>
            <th class="px-4 py-3.5 min-w-[300px]">Item &amp; Asset Specification</th>
            <th class="px-4 py-3.5 min-w-[180px]">Merchant / Seller</th>
            <th class="px-4 py-3.5 w-32">Stock Levels</th>
            <th class="px-4 py-3.5 text-right w-36">Pricing &amp; Terms</th>
            <th class="px-4 py-3.5 w-32 whitespace-nowrap">Status</th>
            <th class="px-4 py-3.5 text-center w-36">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 font-medium text-slate-700 dark:text-slate-300">
          @forelse($listings as $lst)
            @php
              $asset = $lst->item;
              $isComponent = ($asset && $asset->parent_id !== null);
              $itemType = $asset?->item_type ?? 'part';
              $itemTitle = $asset?->name ?: ($asset?->deviceModel?->name ?? 'Marketplace Asset #' . $lst->id);
              $brandName = $asset?->deviceModel?->brand?->name ?? $asset?->parent?->deviceModel?->brand?->name ?? '—';
              $categoryName = $asset?->deviceModel?->category?->name ?? $asset?->parent?->deviceModel?->category?->name ?? 'General';
              $modelName = $asset?->deviceModel?->name ?? $asset?->parent?->deviceModel?->name ?? 'Unspecified Model';
              $condition = $asset?->condition_status ?? 'used';
              
              $availableQty = $lst->availableQuantity();
              $primaryImg = $lst->primary_image_url;

              // Moderate status badge
              $latestMod = $lst->latestModeration;
              $status = $lst->status;
              if ($availableQty <= 0 && $status === 'live') {
                  $status = 'sold_out';
              }
            @endphp

            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
              
              <!-- 1. ITEM & ASSET SPECIFICATION (THUMBNAIL + 3-LINE META) -->
              <td class="px-4 py-3.5 min-w-[300px]">
                <div class="flex items-start gap-3">
                  
                  <!-- Thumbnail -->
                  <div class="w-12 h-12 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 flex items-center justify-center text-slate-400">
                    @if($primaryImg)
                      <img src="{{ $primaryImg }}" alt="{{ $itemTitle }}" class="w-full h-full object-cover" />
                    @else
                      <i class="fas fa-cube text-base"></i>
                    @endif
                  </div>

                  <!-- Details -->
                  <div class="flex flex-col gap-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                      <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase {{ $itemType === 'whole' ? 'bg-blue-100 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300' : ($itemType === 'scrap' ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200' : 'bg-pp-100 dark:bg-pp-950/60 text-pp-900 dark:text-pp-300') }}">
                        {{ $itemType === 'whole' ? 'Whole Unit' : ($itemType === 'scrap' ? 'Scrap' : 'Component') }}
                      </span>
                      <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ str_replace('_', ' ', $condition) }}
                      </span>
                      @if($asset?->year)
                        <span class="text-[10px] text-slate-400 font-bold">({{ $asset->year }})</span>
                      @endif
                    </div>

                    <a href="{{ route('admin.properties.show', $lst->id) }}" class="font-extrabold text-slate-900 dark:text-white hover:text-pp-600 transition text-xs line-clamp-1">
                      {{ $itemTitle }}
                    </a>

                    <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-2 flex-wrap">
                      <span>Brand: <strong class="text-slate-800 dark:text-slate-200">{{ $brandName }}</strong></span>
                      <span>·</span>
                      <span>Model: <strong class="text-slate-800 dark:text-slate-200">{{ $modelName }}</strong></span>
                      <span>·</span>
                      <span>Category: <strong class="text-slate-800 dark:text-slate-200">{{ $categoryName }}</strong></span>
                    </div>
                  </div>

                </div>
              </td>

              <!-- 2. MERCHANT / SELLER -->
              <td class="px-4 py-3.5 min-w-[180px]">
                @if($lst->user)
                  <div class="flex flex-col gap-0.5">
                    <a href="{{ route('admin.users.show', $lst->user->id) }}" class="font-extrabold text-slate-900 dark:text-white hover:text-pp-600 transition text-xs flex items-center gap-1.5">
                      <span>{{ $lst->user->business_name ?: $lst->user->name }}</span>
                      @if($lst->user->is_verified)
                        <i class="fas fa-check-circle text-emerald-500 text-[10px]" title="Verified Merchant"></i>
                      @endif
                    </a>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $lst->user->email }}</span>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                      <span>{{ $lst->user->country?->name ?? 'Nigeria' }}</span>
                      @if($asset?->location)
                        <span>· {{ $asset->location->city }}</span>
                      @endif
                    </div>
                  </div>
                @else
                  <span class="text-slate-400 text-xs italic">Unknown Merchant</span>
                @endif
              </td>

              <!-- 3. STOCK LEVELS -->
              <td class="px-4 py-3.5 w-32">
                <div class="space-y-0.5">
                  <div class="flex items-baseline gap-1.5">
                    <span class="text-xs font-black {{ $availableQty > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                      {{ $availableQty }} Available
                    </span>
                  </div>
                  <span class="text-[10px] text-slate-400 block font-medium">
                    Total: {{ $lst->quantity }} units
                  </span>
                  @if($lst->reserved_quantity > 0)
                    <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold block">
                      {{ $lst->reserved_quantity }} in active carts
                    </span>
                  @endif
                  @if($lst->sold_quantity > 0)
                    <span class="text-[10px] text-blue-700 dark:text-blue-400 font-bold block">
                      {{ $lst->sold_quantity }} completed sales
                    </span>
                  @endif
                </div>
              </td>

              <!-- 4. PRICING & TERMS -->
              <td class="px-4 py-3.5 text-right w-36">
                <div class="flex flex-col items-end gap-1">
                  <span class="text-xs font-black text-slate-900 dark:text-white">
                    ₦{{ number_format($lst->price, 2) }}
                  </span>
                  
                  <div class="flex items-center gap-1">
                    @if($lst->is_negotiable)
                      <span class="px-1.5 py-0.2 rounded bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-[9px] font-bold">
                        Negotiable
                      </span>
                    @else
                      <span class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[9px] font-bold">
                        Fixed
                      </span>
                    @endif

                    @if($lst->allow_shipping)
                      <span class="px-1.5 py-0.2 rounded bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 text-[9px] font-bold" title="Nationwide Delivery Available">
                        <i class="fas fa-truck text-[8px]"></i> Shipping
                      </span>
                    @else
                      <span class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[9px] font-bold" title="Local Pickup Only">
                        Pickup
                      </span>
                    @endif
                  </div>

                  @if($lst->warranty_period_days > 0)
                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                      <i class="fas fa-shield-alt text-[9px]"></i> {{ $lst->warranty_period_days }}d Warranty
                    </span>
                  @endif
                </div>
              </td>

              <!-- 5. STATUS & MODERATION -->
              <td class="px-4 py-3.5 w-32">
                <div class="flex flex-col gap-1">
                  @if($status === 'live' || $status === 'active')
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-400 text-[10px] font-black uppercase inline-flex items-center gap-1 w-fit">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Live
                    </span>
                  @elseif($status === 'pending' || ($latestMod && $latestMod->status === 'pending'))
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 text-[10px] font-black uppercase inline-flex items-center gap-1 w-fit animate-pulse">
                      <i class="fas fa-clock text-[9px]"></i> Pending Review
                    </span>
                  @elseif($status === 'rejected' || ($latestMod && $latestMod->status === 'rejected'))
                    <span class="px-2.5 py-1 rounded-full bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-rose-700 dark:text-rose-400 text-[10px] font-black uppercase inline-flex items-center gap-1 w-fit" title="{{ $latestMod?->reason }}">
                      <i class="fas fa-ban text-[9px]"></i> Rejected
                    </span>
                  @elseif($status === 'sold_out')
                    <span class="px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800/60 text-purple-700 dark:text-purple-400 text-[10px] font-black uppercase inline-flex items-center gap-1 w-fit">
                      Sold Out
                    </span>
                  @elseif($status === 'draft')
                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold uppercase inline-flex items-center gap-1 w-fit">
                      Draft
                    </span>
                  @else
                    <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-bold uppercase inline-flex items-center gap-1 w-fit">
                      {{ ucfirst($status) }}
                    </span>
                  @endif

                  <span class="text-[10px] text-slate-400 font-medium">
                    {{ $lst->created_at->format('M d, Y') }}
                  </span>
                </div>
              </td>

              <!-- 6. ACTIONS -->
              <td class="px-4 py-3.5 text-center w-36">
                <div class="flex items-center justify-center gap-1.5">
                  
                  <!-- View Details -->
                  <a
                    href="{{ route('admin.properties.show', $lst->id) }}"
                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center transition"
                    title="View Full Listing Details"
                  >
                    <i class="fas fa-eye text-xs"></i>
                  </a>

                  <!-- Quick Approve (if pending or rejected) -->
                  @if(! $lst->is_published || ($latestMod && $latestMod->status === 'pending'))
                    <button
                      type="button"
                      wire:click="approve({{ $lst->id }})"
                      wire:confirm="Approve this listing and publish it to the live marketplace?"
                      class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center transition cursor-pointer"
                      title="Quick Approve"
                    >
                      <i class="fas fa-check text-xs"></i>
                    </button>
                  @endif

                  <!-- Quick Reject (if not rejected) -->
                  @if($latestMod?->status !== 'rejected')
                    <button
                      type="button"
                      wire:click="openRejectModal({{ $lst->id }})"
                      class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-400 flex items-center justify-center transition cursor-pointer"
                      title="Reject with Reason"
                    >
                      <i class="fas fa-times text-xs"></i>
                    </button>
                  @endif

                  <!-- Delete -->
                  <button
                    type="button"
                    wire:click="delete({{ $lst->id }})"
                    wire:confirm="Are you sure you want to delete this listing? This action cannot be undone."
                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-400 flex items-center justify-center transition cursor-pointer"
                    title="Delete Listing"
                  >
                    <i class="fas fa-trash text-xs"></i>
                  </button>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl">
                    <i class="fas fa-boxes-packing"></i>
                  </div>
                  <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">No Listings Found</h4>
                  <p class="text-xs text-slate-500 dark:text-slate-400 text-center">No listings match your active keyword, category, status, or condition filters.</p>
                  <button
                    type="button"
                    wire:click="resetFilters"
                    class="mt-2 px-4 py-2 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-extrabold text-xs transition cursor-pointer"
                  >
                    Clear Filter Criteria
                  </button>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    @if ($listings->hasPages())
      <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $listings->links() }}
      </div>
    @endif

  </div>

  <!-- QUICK REJECT MODAL -->
  @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-xs">
      <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-black">
              <i class="fas fa-ban"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Disapprove Listing #{{ $selectedListingId }}</h3>
              <p class="text-[11px] text-slate-500">The seller will receive this explanation in their notification center.</p>
            </div>
          </div>
          <button type="button" wire:click="$set('showRejectModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Standard Preset Reason</label>
            <select wire:model.live="presetReason" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50">
              <option value="">Select a standard reason...</option>
              <option value="Incorrect category or mismatched vehicle/device model specification.">Incorrect Category or Model Mismatch</option>
              <option value="Misleading price or unrealistic shipping fee specification.">Misleading Pricing</option>
              <option value="Low quality, watermarked, or stolen stock images. Please upload genuine photos of the part.">Image / Photo Quality Policy</option>
              <option value="Prohibited item or counterfeit component not allowed on platform.">Prohibited or Counterfeit Component</option>
              <option value="Incomplete condition notes or missing technical specification details.">Incomplete Description</option>
            </select>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Detailed Rejection Explanation *</label>
            <textarea
              wire:model="rejectionReason"
              rows="4"
              placeholder="State clear, actionable reasons why this listing cannot be published..."
              class="w-full p-3 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 transition"
            ></textarea>
            @error('rejectionReason')
              <span class="text-rose-600 text-[11px] font-bold mt-1 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
          <button
            type="button"
            wire:click="$set('showRejectModal', false)"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="submitReject"
            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
          >
            <i class="fas fa-ban"></i>
            <span>Confirm Rejection</span>
          </button>
        </div>

      </div>
    </div>
  @endif

</div>
