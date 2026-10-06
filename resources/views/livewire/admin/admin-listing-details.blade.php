<div class="flex flex-col gap-6">

  @php
    $asset = $listing->item;
    $isComponent = ($asset && $asset->parent_id !== null);
    $itemType = $asset?->item_type ?? 'part';
    $itemTitle = $asset?->name ?: ($asset?->deviceModel?->name ?? 'Marketplace Asset #' . $listing->id);
    $brandName = $asset?->deviceModel?->brand?->name ?? $asset?->parent?->deviceModel?->brand?->name ?? '—';
    $categoryName = $asset?->deviceModel?->category?->name ?? $asset?->parent?->deviceModel?->category?->name ?? 'General';
    $modelName = $asset?->deviceModel?->name ?? $asset?->parent?->deviceModel?->name ?? 'Unspecified Model';
    $condition = $asset?->condition_status ?? 'used';
    
    $availableQty = $listing->availableQuantity();
    $latestMod = $listing->latestModeration;
    $status = $listing->status;
    if ($availableQty <= 0 && $status === 'live') {
        $status = 'sold_out';
    }

    $allMedia = collect();
    if ($listing->media) {
        $allMedia = $allMedia->merge($listing->media);
    }
    if ($asset && $asset->media) {
        $allMedia = $allMedia->merge($asset->media);
    }
  @endphp

  <!-- TOP BREADCRUMB & ACTION BAR -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1.5 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.properties') }}" class="text-pp-600 hover:underline flex items-center gap-1 font-extrabold">
          <i class="fas fa-arrow-left text-[10px]"></i> Listings
        </a>
        <span>/</span>
        <span class="text-slate-400">#{{ $listing->id }}</span>
        <span>/</span>
        <span class="text-slate-700 font-extrabold truncate max-w-[200px]">{{ $itemTitle }}</span>
      </div>

      <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-2xl font-black text-slate-950">
          {{ $itemTitle }}
        </h1>

        <!-- STATUS BADGE -->
        @if($status === 'live' || $status === 'active')
          <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Live on Marketplace
          </span>
        @elseif($status === 'pending' || ($latestMod && $latestMod->status === 'pending'))
          <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs animate-pulse">
            <i class="fas fa-clock text-[10px]"></i> Pending Review
          </span>
        @elseif($status === 'rejected' || ($latestMod && $latestMod->status === 'rejected'))
          <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-ban text-[10px]"></i> Rejected by Admin
          </span>
        @elseif($status === 'sold_out')
          <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            Sold Out (0 Qty)
          </span>
        @elseif($status === 'draft')
          <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase inline-flex items-center gap-1.5 shadow-2xs">
            Draft (Unpublished)
          </span>
        @else
          <span class="px-3 py-1 rounded-full bg-zinc-100 text-zinc-700 text-xs font-bold uppercase inline-flex items-center gap-1.5 shadow-2xs">
            {{ ucfirst($status) }}
          </span>
        @endif
      </div>

      <p class="text-xs text-slate-500 mt-1">
        Catalog Identifier: <code class="text-slate-800 font-bold bg-slate-100 px-1.5 py-0.5 rounded">ID: {{ $listing->id }}</code>
        · Slug: <code class="text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded">{{ $listing->slug }}</code>
        · Registered {{ $listing->created_at->format('M d, Y · H:i') }}
      </p>
    </div>

    <!-- ADMIN COMMAND BAR -->
    <div class="flex items-center gap-2 flex-wrap">
      
      <!-- View Marketplace Live Listing -->
      @if($listing->slug)
        <a
          href="{{ url('/marketplace/' . $listing->slug) }}"
          target="_blank"
          class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center gap-1.5"
          title="Open Public Marketplace Page"
        >
          <i class="fas fa-external-link-alt text-[10px]"></i>
          <span>Public View</span>
        </a>
      @endif

      <!-- Approve Button -->
      @if(! $listing->is_published || ($latestMod && $latestMod->status === 'pending'))
        <button
          type="button"
          wire:click="approve"
          wire:confirm="Approve this listing and publish it to the live marketplace?"
          class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
        >
          <i class="fas fa-check"></i>
          <span>Approve Listing</span>
        </button>
      @endif

      <!-- Reject Button -->
      @if($latestMod?->status !== 'rejected')
        <button
          type="button"
          wire:click="openRejectModal"
          class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
        >
          <i class="fas fa-ban"></i>
          <span>Reject / Flag</span>
        </button>
      @endif

      <!-- Toggle Publish -->
      <button
        type="button"
        wire:click="togglePublished"
        class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
        title="Toggle public visibility"
      >
        <i class="fas {{ $listing->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
        <span>{{ $listing->is_published ? 'Unpublish' : 'Publish' }}</span>
      </button>

      <!-- Delete Button -->
      <button
        type="button"
        wire:click="delete"
        wire:confirm="Are you sure you want to permanently delete this listing? All active cart items will be removed."
        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer"
      >
        <i class="fas fa-trash"></i>
        <span>Delete</span>
      </button>

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

  <!-- MAIN 2-COLUMN GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT COLUMN: PRODUCT SPECIFICATIONS & MEDIA (2 COLS) -->
    <div class="lg:col-span-2 space-y-6">

      <!-- 1. PHOTO GALLERY & MEDIA CAROUSEL CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-images text-pp-600"></i> Media Gallery &amp; Photos ({{ $allMedia->count() }})
          </h3>
          <span class="text-xs text-slate-400 font-semibold">Visual Inspection Photos</span>
        </div>

        @if($allMedia->isNotEmpty())
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($allMedia as $mediaItem)
              <a href="{{ $mediaItem->url }}" target="_blank" class="group relative rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 aspect-square block">
                <img src="{{ $mediaItem->url }}" alt="{{ $itemTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                  <i class="fas fa-search-plus"></i> View
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="p-8 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 space-y-2">
            <i class="fas fa-image text-3xl"></i>
            <p class="text-xs font-bold">No photos uploaded for this listing or physical asset.</p>
          </div>
        @endif
      </div>

      <!-- 2. TECHNICAL SPECIFICATIONS & ASSET MATRIX -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-microchip text-pp-600"></i> Technical Asset Specification
          </h3>
          <span class="text-xs font-bold text-slate-500">Asset #{{ $asset?->id ?? 'N/A' }}</span>
        </div>

        <!-- KEY DATA ATTRIBUTES GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
          
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Item Classification</span>
            <span class="font-black text-slate-900 text-sm capitalize flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full {{ $itemType === 'whole' ? 'bg-blue-600' : 'bg-pp-600' }}"></span>
              {{ $itemType === 'whole' ? 'Whole Device / Vehicle' : ($itemType === 'scrap' ? 'Scrap Metal' : 'Harvested Component') }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Condition Status</span>
            <span class="font-black text-slate-900 text-sm capitalize">
              {{ str_replace('_', ' ', $condition) }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Brand / Manufacturer</span>
            <span class="font-black text-slate-900 text-sm">
              {{ $brandName }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Device / Vehicle Model</span>
            <span class="font-black text-slate-900 text-sm">
              {{ $modelName }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Marketplace Category</span>
            <span class="font-black text-slate-900 text-sm">
              {{ $categoryName }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Production Year</span>
            <span class="font-black text-slate-900 text-sm">
              {{ $asset?->year ?: 'Not Specified' }}
            </span>
          </div>

        </div>

        <!-- CONDITION NOTES (IF PRESENT) -->
        @if($asset?->condition_notes)
          <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 text-xs space-y-1">
            <b class="text-amber-950 font-extrabold flex items-center gap-1.5">
              <i class="fas fa-clipboard-check text-amber-600"></i> Merchant Condition Assessment Notes:
            </b>
            <p class="text-amber-900 leading-relaxed">
              {{ $asset->condition_notes }}
            </p>
          </div>
        @endif

        <!-- DESCRIPTION -->
        <div class="space-y-1.5 text-xs">
          <label class="font-bold text-slate-700 block">Full Description &amp; Details:</label>
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed whitespace-pre-line">
            {{ $asset?->description ?: 'No detailed written description provided by merchant.' }}
          </div>
        </div>

      </div>

      <!-- 3. PARENT ASSET / HARVESTED CHILDREN TREE (DISASSEMBLY MATRIX) -->
      @if($isComponent && $asset->parent)
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-3 text-xs">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-sitemap text-pp-600"></i> Harvest Origin (Parent Device)
          </h3>
          <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center justify-between gap-3">
            <div>
              <span class="font-extrabold text-blue-950 block text-sm">{{ $asset->parent->name }}</span>
              <span class="text-[11px] text-blue-800">
                Model: {{ $asset->parent->deviceModel?->name ?? '—' }} · Harvested from Unit #{{ $asset->parent->id }}
              </span>
            </div>
            <a href="{{ route('item.view', $asset->parent->id) }}" class="px-3.5 py-1.5 rounded-xl bg-blue-900 text-white font-bold text-xs hover:bg-blue-800 transition">
              View Parent Asset
            </a>
          </div>
        </div>
      @endif

      @if($asset && $asset->children->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-3 text-xs">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-wrench text-pp-600"></i> Harvested Components Catalog ({{ $asset->children->count() }} Parts)
          </h3>
          <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 overflow-hidden">
            @foreach($asset->children as $child)
              <div class="p-3 bg-white hover:bg-slate-50 transition flex items-center justify-between text-xs">
                <div>
                  <span class="font-bold text-slate-900 block">{{ $child->name }}</span>
                  <span class="text-[10px] text-slate-500">Condition: {{ ucfirst($child->condition_status) }}</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-pp-50 text-pp-700">Part #{{ $child->id }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- 4. COMMERCIAL PRICING, WARRANTY & SHIPPING TERMS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-tag text-pp-600"></i> Commercial &amp; Transaction Terms
          </h3>
          <span class="text-xs font-bold text-slate-500">Commercial Terms</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
          
          <!-- UNIT PRICE -->
          <div class="p-4 rounded-2xl bg-pp-50/60 border border-pp-200 space-y-1">
            <span class="text-[10px] font-bold text-pp-800 uppercase tracking-wider block">Listing Unit Price</span>
            <span class="text-xl font-black text-slate-950">
              ₦{{ number_format($listing->price, 2) }}
            </span>
            <span class="text-[10px] text-slate-600 block">
              Negotiation: <strong class="text-slate-900">{{ $listing->is_negotiable ? 'Open to Buyer Offers' : 'Fixed Price' }}</strong>
            </span>
          </div>

          <!-- WARRANTY -->
          <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-1">
            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Warranty Protection</span>
            @if($listing->warranty_period_days > 0)
              <span class="text-lg font-black text-emerald-900 block">
                {{ $listing->warranty_period_days }} Days Coverage
              </span>
              <span class="text-[10px] text-emerald-700 font-semibold block">
                {{ $listing->is_warranty_negotiable ? 'Warranty negotiable' : 'Fixed warranty duration' }}
              </span>
            @else
              <span class="text-base font-black text-slate-600 block">No Warranty</span>
              <span class="text-[10px] text-slate-500 block">Sold as-is</span>
            @endif
          </div>

          <!-- FULFILLMENT & SHIPPING -->
          <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200 space-y-1">
            <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">Fulfillment Options</span>
            <span class="text-base font-black text-blue-950 block">
              {{ $listing->allow_shipping ? 'Delivery & Pickup' : 'Local Pickup Only' }}
            </span>
            <span class="text-[10px] text-blue-800 block">
              Origin: {{ $asset?->location?->city ?? 'Merchant Address' }}
            </span>
          </div>

        </div>

        @if($listing->warranty_terms)
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
            <b class="text-slate-900 font-bold block mb-1">Specific Warranty Terms:</b>
            <p>{{ $listing->warranty_terms }}</p>
          </div>
        @endif
      </div>

      <!-- 5. INVENTORY STOCK & CIRCULATION CONTROL -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-boxes-stacked text-pp-600"></i> Stock &amp; Circulation Inventory
          </h3>

          @if(! $isEditingStock)
            <button
              type="button"
              wire:click="startEditStock"
              class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1 cursor-pointer"
            >
              <i class="fas fa-pen-to-square text-[10px]"></i> Adjust Quantity
            </button>
          @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Catalog Units</span>
            <span class="text-base font-black text-slate-900">{{ $listing->quantity }} units</span>
          </div>

          <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Available to Buy</span>
            <span class="text-base font-black text-emerald-900">{{ $availableQty }} units</span>
          </div>

          <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200">
            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Held in Carts</span>
            <span class="text-base font-black text-amber-900">{{ $listing->reserved_quantity }} units</span>
          </div>

          <div class="p-3 rounded-2xl bg-blue-50 border border-blue-200">
            <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Completed Sales</span>
            <span class="text-base font-black text-blue-900">{{ $listing->sold_quantity }} units</span>
          </div>

        </div>

        <!-- ADMIN QUICK STOCK EDIT -->
        @if($isEditingStock)
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-end gap-3 text-xs">
            <div class="space-y-1 flex-1">
              <label class="font-bold text-slate-700">Adjust Total Stock Quantity:</label>
              <input
                type="number"
                min="0"
                wire:model="newQuantity"
                class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 bg-white font-extrabold"
              />
              @error('newQuantity')
                <span class="text-rose-600 text-[10px] font-bold">{{ $message }}</span>
              @enderror
            </div>
            <div class="flex items-center gap-2">
              <button
                type="button"
                wire:click="saveStock"
                class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition cursor-pointer"
              >
                Save Adjustment
              </button>
              <button
                type="button"
                wire:click="$set('isEditingStock', false)"
                class="px-3.5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer"
              >
                Cancel
              </button>
            </div>
          </div>
        @endif

      </div>

      <!-- 6. BUYER REVIEWS & RATINGS (IF ANY) -->
      @if($listing->reviews->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-star text-amber-500"></i> Buyer Reviews &amp; Feedback ({{ $listing->reviews->count() }})
            </h3>
            <span class="text-xs font-bold text-slate-700">Average: {{ $listing->averageRating() }} / 5.0</span>
          </div>

          <div class="space-y-3">
            @foreach($listing->reviews as $rev)
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                <div class="flex items-center justify-between">
                  <span class="font-extrabold text-slate-900">{{ $rev->user?->name ?? 'Verified Buyer' }}</span>
                  <div class="flex items-center text-amber-400 text-[10px]">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="fas fa-star {{ $i <= $rev->rating ? '' : 'text-slate-200' }}"></i>
                    @endfor
                  </div>
                </div>
                <p class="text-slate-600">{{ $rev->comment }}</p>
                <span class="text-[10px] text-slate-400 block">{{ $rev->created_at->format('M d, Y') }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif

    </div>

    <!-- RIGHT COLUMN: SELLER PROFILE, SUBSCRIPTION & MODERATION AUDIT TRAIL -->
    <div class="space-y-6">

      <!-- 1. SELLER & MERCHANT CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-store text-pp-600"></i> Merchant / Seller Profile
          </h3>
          <span class="text-xs font-bold text-slate-400">Owner</span>
        </div>

        @if($listing->user)
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-2xl bg-pp-100 text-pp-800 flex items-center justify-center font-black text-base shrink-0">
              {{ substr($listing->user->business_name ?: $listing->user->name, 0, 2) }}
            </div>
            <div>
              <h4 class="font-black text-slate-950 text-sm flex items-center gap-1.5">
                <span>{{ $listing->user->business_name ?: $listing->user->name }}</span>
                @if($listing->user->is_verified)
                  <i class="fas fa-check-circle text-emerald-500 text-xs" title="Verified Merchant"></i>
                @endif
              </h4>
              <p class="text-xs text-slate-500">{{ $listing->user->email }}</p>
              <p class="text-[11px] text-slate-400">{{ $listing->user->phone ?: 'No phone provided' }}</p>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
            <div class="flex items-center justify-between text-slate-600">
              <span>Country:</span>
              <strong class="text-slate-900">{{ $listing->user->country?->name ?? 'Nigeria' }}</strong>
            </div>
            <div class="flex items-center justify-between text-slate-600">
              <span>Registered Since:</span>
              <strong class="text-slate-900">{{ $listing->user->created_at->format('M Y') }}</strong>
            </div>
            <div class="flex items-center justify-between text-slate-600">
              <span>All Active Listings:</span>
              <strong class="text-pp-700 font-extrabold">{{ $listing->user->listings()->count() }} items</strong>
            </div>
          </div>

          <a
            href="{{ route('admin.users.show', $listing->user->id) }}"
            class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center justify-center gap-1.5"
          >
            <span>Inspect Merchant Account</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
          </a>
        @else
          <p class="text-xs text-slate-400 italic">No owner linked.</p>
        @endif
      </div>

      <!-- 2. SUBSCRIPTION & PROMOTION ENTITLEMENTS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-gem text-pp-600"></i> Plan &amp; Promotion Status
          </h3>
        </div>

        <!-- ACTIVE SUBSCRIPTION OF SELLER -->
        @php
          $activeSub = $listing->user?->subscriptions()->where('status', 'active')->latest()->first();
        @endphp

        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Seller Subscription</span>
          @if($activeSub)
            <span class="font-extrabold text-slate-900 text-sm block">
              {{ $activeSub->plan?->name ?? 'Active Plan' }}
            </span>
            <span class="text-[11px] text-emerald-700 font-bold block">
              Status: Active (Limit: {{ $activeSub->listing_limit }} items)
            </span>
            @if($activeSub->ends_at)
              <span class="text-[10px] text-slate-400 block">Renews / Ends: {{ $activeSub->ends_at->format('M d, Y') }}</span>
            @endif
          @else
            <span class="text-xs text-slate-500 font-medium block">No active subscription plan found.</span>
          @endif
        </div>

        <!-- PROMOTIONS -->
        <div class="pt-2 text-xs">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Campaign Boosts</span>
          @if($listing->promotions->isNotEmpty())
            <div class="space-y-2">
              @foreach($listing->promotions as $promo)
                <div class="p-2.5 rounded-xl bg-pp-50 border border-pp-200 flex items-center justify-between">
                  <span class="font-bold text-pp-900 uppercase text-[11px]">{{ $promo->type }}</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $promo->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ ucfirst($promo->status) }}
                  </span>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-[11px] text-slate-400">No active promotional boosts applied to this item.</p>
          @endif
        </div>
      </div>

      <!-- 3. MODERATION TIMELINE & AUDIT HISTORY -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-shield-halved text-pp-600"></i> Moderation Audit Trail
          </h3>
          <span class="text-xs font-bold text-slate-400">{{ $moderations->count() }} Events</span>
        </div>

        @if($moderations->isNotEmpty())
          <div class="space-y-3">
            @foreach($moderations as $mod)
              <div class="p-3 rounded-2xl border text-xs space-y-1 {{ $mod->status === 'approved' ? 'bg-emerald-50/50 border-emerald-200' : ($mod->status === 'rejected' ? 'bg-rose-50/50 border-rose-200' : 'bg-slate-50 border-slate-200') }}">
                <div class="flex items-center justify-between">
                  <span class="font-extrabold uppercase text-[10px] {{ $mod->status === 'approved' ? 'text-emerald-800' : ($mod->status === 'rejected' ? 'text-rose-800' : 'text-slate-800') }}">
                    {{ str_replace('_', ' ', $mod->action ?: $mod->status) }}
                  </span>
                  <span class="text-[10px] text-slate-400 font-semibold">{{ $mod->created_at->format('M d, H:i') }}</span>
                </div>

                @if($mod->moderator)
                  <span class="text-[11px] text-slate-600 block">
                    Reviewed by: <strong class="text-slate-800">{{ $mod->moderator->name }}</strong>
                  </span>
                @endif

                @if($mod->reason)
                  <p class="text-[11px] text-rose-800 font-medium italic mt-1 bg-white/60 p-2 rounded-xl">
                    "{{ $mod->reason }}"
                  </p>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div class="p-6 text-center text-slate-400 text-xs">
            <i class="fas fa-clock-rotate-left text-2xl mb-1 block"></i>
            <span>No historical moderation records.</span>
          </div>
        @endif
      </div>

    </div>

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
              <h3 class="text-sm font-extrabold text-slate-900">Reject Listing #{{ $listing->id }}</h3>
              <p class="text-[11px] text-slate-500">Provide a clear explanation for the merchant.</p>
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
              placeholder="State clear reasons why this listing cannot be published..."
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
