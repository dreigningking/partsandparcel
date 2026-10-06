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
    $status = $listing->status ?? ($listing->is_published ? 'live' : 'draft');
    if ($availableQty <= 0 && $status === 'live') {
        $status = 'sold_out';
    }

    $modStatus = $latestMod?->status ?? ($listing->is_published ? 'approved' : 'pending');

    $allMedia = collect();
    if ($listing->media) {
        $allMedia = $allMedia->merge($listing->media);
    }
    if ($asset && $asset->media) {
        $allMedia = $allMedia->merge($asset->media);
    }

    $openReports = $listing->reports->whereIn('status', ['pending', 'open']);
    $loc = $asset?->location ?? $listing->user?->primaryLocation;
  @endphp

  <!-- TOP BREADCRUMB & HEADER BAR -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1.5 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.properties') }}" class="text-pp-600 hover:underline flex items-center gap-1 font-extrabold">
          <i class="fas fa-arrow-left text-[10px]"></i> Listings
        </a>
        <span>/</span>
        <span class="text-slate-400">#{{ $listing->id }}</span>
        <span>/</span>
        <span class="text-slate-700 font-extrabold truncate max-w-[240px]">{{ $itemTitle }}</span>
      </div>

      <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-2xl font-black text-slate-950">
          {{ $itemTitle }}
        </h1>

        <!-- PUBLICATION STATUS BADGE -->
        @if($listing->is_published && ($status === 'live' || $status === 'active'))
          <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span> Published Live
          </span>
        @elseif($status === 'sold_out')
          <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            Sold Out (0 Qty)
          </span>
        @else
          <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-eye-slash text-[10px]"></i> Unpublished Draft
          </span>
        @endif
      </div>

      <p class="text-xs text-slate-500 mt-1">
        Catalog Identifier: <code class="text-slate-800 font-bold bg-slate-100 px-1.5 py-0.5 rounded">ID: {{ $listing->id }}</code>
        · Slug: <code class="text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded">{{ $listing->slug }}</code>
        · Registered {{ $listing->created_at->format('M d, Y · H:i') }}
      </p>
    </div>

    <!-- ADMIN QUICK ACTIONS -->
    <div class="flex items-center gap-2 flex-wrap">
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

      <!-- Toggle Publish Visibility -->
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

  <!-- TOP KPI RIBBON (FEATURING CURRENT MODERATION STATUS & APPROVE / REJECT BUTTONS) -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

    <!-- KPI 1: MODERATION STATUS & QUICK MODERATION ACTIONS (USER SPECIFIED HIGHLIGHT) -->
    <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl border-2 {{ $modStatus === 'approved' ? 'border-emerald-200 shadow-emerald-50/50' : ($modStatus === 'rejected' ? 'border-rose-200 shadow-rose-50/50' : 'border-amber-300 shadow-amber-50/50') }} p-4 shadow-sm flex flex-col justify-between gap-3 xl:col-span-2">
      <div class="flex items-start justify-between gap-2">
        <div>
          <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">
            Current Moderation Status
          </span>
          <div class="flex items-center gap-2">
            @if($modStatus === 'approved')
              <span class="px-2.5 py-1 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs font-black inline-flex items-center gap-1.5">
                <i class="fas fa-check-circle text-emerald-600"></i> Approved
              </span>
            @elseif($modStatus === 'rejected')
              <span class="px-2.5 py-1 rounded-lg bg-rose-100 border border-rose-300 text-rose-900 text-xs font-black inline-flex items-center gap-1.5">
                <i class="fas fa-ban text-rose-600"></i> Rejected
              </span>
            @else
              <span class="px-2.5 py-1 rounded-lg bg-amber-100 border border-amber-300 text-amber-900 text-xs font-black inline-flex items-center gap-1.5 animate-pulse">
                <i class="fas fa-clock text-amber-600"></i> Pending Review
              </span>
            @endif
          </div>
        </div>

        <!-- Moderation Info caption -->
        <span class="text-[10px] text-slate-500 text-right leading-tight">
          @if($latestMod)
            Action by {{ $latestMod->moderator?->name ?? 'Admin' }}<br>
            <span class="text-slate-400">{{ $latestMod->created_at->diffForHumans() }}</span>
          @else
            Awaiting first<br>moderation action
          @endif
        </span>
      </div>

      <!-- MODERATION BUTTONS ON TOP KPI RIBBON -->
      <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70">
        <button
          type="button"
          wire:click="approve"
          wire:confirm="Approve this listing and publish it to the live marketplace?"
          class="flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer"
          title="Approve and activate listing"
        >
          <i class="fas fa-check text-[11px]"></i>
          <span>Approve Listing</span>
        </button>

        <button
          type="button"
          wire:click="openRejectModal"
          class="flex-1 py-1.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer"
          title="Reject listing or state violations"
        >
          <i class="fas fa-ban text-[11px]"></i>
          <span>Reject / Flag</span>
        </button>
      </div>
    </div>

    <!-- KPI 2: MARKETPLACE VIEWS (ViewedEntity) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Marketplace Views</span>
        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
          <i class="fas fa-eye text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-slate-900 leading-none">
          {{ number_format($viewsCount) }}
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          {{ number_format($uniqueViewers) }} unique member viewers
        </p>
      </div>
    </div>

    <!-- KPI 3: UNITS SOLD (InvoiceItem) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Times Sold</span>
        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
          <i class="fas fa-shopping-bag text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-emerald-700 leading-none">
          {{ number_format($totalSoldUnits) }} <span class="text-xs text-slate-500 font-bold">units</span>
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          {{ $timesSold }} successful order lines
        </p>
      </div>
    </div>

    <!-- KPI 4: GROSS REVENUE (InvoiceItem) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Gross Sales</span>
        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
          <i class="fas fa-receipt text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-xl font-black text-slate-900 leading-none truncate" title="₦{{ number_format($grossRevenue, 2) }}">
          ₦{{ number_format($grossRevenue, 0) }}
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1">
          Settled via Escrow
        </p>
      </div>
    </div>

    <!-- KPI 5: BUYER REVIEWS & RATING (ListingReview) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Buyer Rating</span>
        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
          <i class="fas fa-star text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-slate-900 leading-none flex items-center gap-1.5">
          <span>{{ number_format($avgRating, 1) }}</span>
          <span class="text-amber-400 text-base">★</span>
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          {{ $reviewsCount }} customer review(s)
        </p>
      </div>
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

  <!-- USER REPORTS ALERT BANNER & OPEN REPORTS (Report) -->
  @if($openReports->isNotEmpty())
    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 font-bold text-xs space-y-4 shadow-2xs">
      <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
            <i class="fas fa-flag text-sm"></i>
          </div>
          <div>
            <span class="font-black text-rose-900 block text-sm">Flagged Item: {{ $openReports->count() }} Unresolved Report(s) Filed</span>
            <span class="text-rose-700 text-[11px] font-medium">Community members have reported this listing for policy violations or inaccurate specs.</span>
          </div>
        </div>
        <button
          type="button"
          wire:click="setTab('trust')"
          class="px-3.5 py-1.5 rounded-xl bg-white border border-rose-300 hover:bg-rose-100 text-rose-900 font-black text-xs transition cursor-pointer shadow-xs"
        >
          View Full Trust Audit
        </button>
      </div>

      <!-- Open Reports Cards (User Reports & Flags) -->
      <div class="space-y-3 pt-2 border-t border-rose-200/60">
        <h3 class="text-xs font-black uppercase tracking-wider text-rose-950 flex items-center gap-1.5">
          <i class="fas fa-exclamation-triangle text-rose-600"></i> User Reports &amp; Flags
        </h3>

        @foreach($openReports as $rep)
          <div class="p-4 rounded-xl bg-white border border-rose-200 space-y-2">
            <div class="flex items-start justify-between gap-2">
              <div>
                <span class="text-xs font-black text-slate-900 block">{{ $rep->title ?: 'Listing Violation Report' }}</span>
                <span class="text-[10px] text-slate-400 font-medium">
                  Reported by <strong class="text-slate-700">{{ $rep->user?->name ?? 'Community Member' }}</strong> · {{ $rep->created_at->diffForHumans() }}
                </span>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800 animate-pulse">
                Pending Review
              </span>
            </div>

            @if($rep->description)
              <p class="text-xs text-slate-700 font-normal leading-relaxed bg-slate-50/70 p-2.5 rounded-lg border border-slate-100">
                {{ $rep->description }}
              </p>
            @endif

            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 justify-end">
              <button
                type="button"
                wire:click="dismissReport({{ $rep->id }})"
                class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer"
              >
                Dismiss Flag
              </button>
              <button
                type="button"
                wire:click="resolveReport({{ $rep->id }})"
                class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition cursor-pointer shadow-xs"
              >
                Mark Resolved
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- OPTION A: TABBED NAVIGATION BAR -->
  <div class="border-b border-slate-200">
    <nav class="flex items-center gap-2 overflow-x-auto pb-px">
      <!-- TAB 1: OVERVIEW & SPECS -->
      <button
        type="button"
        wire:click="setTab('overview')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'overview' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
      >
        <i class="fas fa-sliders-h"></i>
        <span>Overview &amp; Specs</span>
      </button>

      <!-- TAB 2: SALES & INVOICES (InvoiceItem) -->
      <button
        type="button"
        wire:click="setTab('sales')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'sales' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
      >
        <i class="fas fa-receipt"></i>
        <span>Sales &amp; Invoices</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $timesSold > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
          {{ $timesSold }}
        </span>
      </button>

      <!-- TAB 3: REVIEWS & RATINGS (ListingReview) -->
      <button
        type="button"
        wire:click="setTab('reviews')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'reviews' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
      >
        <i class="fas fa-star"></i>
        <span>Reviews &amp; Ratings</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $reviewsCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
          {{ $reviewsCount }}
        </span>
      </button>

      <!-- TAB 4: TRUST & MODERATION (Moderation, Report, Promotion) -->
      <button
        type="button"
        wire:click="setTab('trust')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'trust' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
      >
        <i class="fas fa-shield-alt"></i>
        <span>Trust, Reports &amp; Moderation</span>
        @if($openReports->isNotEmpty())
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 animate-pulse">
            {{ $openReports->count() }}
          </span>
        @else
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600">
            {{ $moderations->count() }}
          </span>
        @endif
      </button>
    </nav>
  </div>

  <!-- TAB CONTENTS CONTAINER -->
  <div class="mt-2">

    <!-- ========================================== -->
    <!-- TAB 1: OVERVIEW & SPECIFICATIONS           -->
    <!-- (Listing, Item, Brand, Category, DeviceModel, Location, Media) -->
    <!-- ========================================== -->
    @if($activeTab === 'overview')
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: PRODUCT SPECS, PHOTOS & HARDWARE (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- 1. PHOTO GALLERY & MEDIA CAROUSEL (Media) -->
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
                    <img
                      src="{{ $mediaItem->url }}"
                      alt="Listing inspection photo"
                      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                      loading="lazy"
                    />
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-extrabold gap-1">
                      <i class="fas fa-search-plus"></i> View
                    </div>
                    @if($loop->first)
                      <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-pp-600 text-white text-[10px] font-black uppercase">
                        Primary
                      </span>
                    @endif
                  </a>
                @endforeach
              </div>
            @else
              <div class="p-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-center">
                <i class="fas fa-image text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs font-bold text-slate-600">No media photos uploaded</p>
                <p class="text-[11px] text-slate-400">Merchant has not attached image proof for this marketplace listing.</p>
              </div>
            @endif
          </div>

          <!-- 2. DEVICE & VEHICLE COMPATIBILITY (Brand, Category, DeviceModel, Item) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-microchip text-pp-600"></i> Asset &amp; Compatibility Specifications
              </h3>
              <span class="text-xs text-slate-400 font-semibold">Taxonomy &amp; Engineering Specs</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Item Asset Name -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Asset Name</span>
                <span class="text-sm font-extrabold text-slate-900 block">{{ $itemTitle }}</span>
              </div>

              <!-- Item Type -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Physical Asset Type</span>
                <span class="text-sm font-extrabold text-slate-900 capitalize block flex items-center gap-1.5">
                  @if($itemType === 'whole')
                    <i class="fas fa-car text-blue-500"></i> Whole Unit
                  @elseif($itemType === 'scrap')
                    <i class="fas fa-recycle text-amber-500"></i> Scrap / Salvage
                  @else
                    <i class="fas fa-cogs text-emerald-500"></i> Component / Part
                  @endif
                </span>
              </div>

              <!-- Brand (Brand) -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Brand / Manufacturer</span>
                <span class="text-sm font-extrabold text-slate-900 block flex items-center gap-1.5">
                  <i class="fas fa-tag text-slate-400 text-xs"></i>
                  <span>{{ $brandName }}</span>
                </span>
              </div>

              <!-- Category (Category) -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Category</span>
                <span class="text-sm font-extrabold text-slate-900 block flex items-center gap-1.5">
                  <i class="fas fa-folder text-slate-400 text-xs"></i>
                  <span>{{ $categoryName }}</span>
                </span>
              </div>

              <!-- Device Model (DeviceModel) -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Device / Vehicle Model</span>
                <span class="text-sm font-extrabold text-slate-900 block flex items-center gap-1.5">
                  <i class="fas fa-car-side text-slate-400 text-xs"></i>
                  <span>{{ $modelName }}</span>
                </span>
              </div>

              <!-- Year of Manufacture (Item) -->
              <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Year / Model Year</span>
                <span class="text-sm font-extrabold text-slate-900 block">
                  {{ $asset?->year ?: 'All Compatible Years' }}
                </span>
              </div>
            </div>

            <!-- Parent or Children Assemblies if any -->
            @if($isComponent && $asset?->parent)
              <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex items-center justify-between">
                <div>
                  <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700 block">Sub-component of Parent Asset</span>
                  <span class="text-xs font-black text-indigo-950">{{ $asset->parent->name ?: 'Parent Unit #' . $asset->parent->id }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-200/70 text-indigo-900 text-[10px] font-black">
                  Parent ID: {{ $asset->parent->id }}
                </span>
              </div>
            @elseif($asset && $asset->children->isNotEmpty())
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">Disassembled Sub-Parts ({{ $asset->children->count() }})</span>
                <div class="flex flex-wrap gap-1.5">
                  @foreach($asset->children as $child)
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700">
                      {{ $child->name }}
                    </span>
                  @endforeach
                </div>
              </div>
            @endif
          </div>

          <!-- 3. CONDITION & TECHNICAL NOTES (Item) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-clipboard-check text-pp-600"></i> Condition &amp; Technical Notes
              </h3>
              <!-- Condition Badge -->
              <span class="px-3 py-1 rounded-full text-xs font-black uppercase
                {{ $condition === 'new' ? 'bg-emerald-100 text-emerald-800' : ($condition === 'refurbished' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                Condition: {{ ucfirst($condition) }}
              </span>
            </div>

            @if($asset?->condition_notes)
              <div>
                <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Condition Inspection Notes</label>
                <div class="p-3.5 rounded-2xl bg-amber-50/50 border border-amber-200/70 text-xs font-medium text-amber-950 whitespace-pre-line">
                  {{ $asset->condition_notes }}
                </div>
              </div>
            @endif

            @if($asset?->description)
              <div>
                <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Seller Item Description</label>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                  {{ $asset->description }}
                </div>
              </div>
            @else
              <p class="text-xs text-slate-400 italic">No custom description provided by merchant.</p>
            @endif
          </div>

        </div>

        <!-- RIGHT COLUMN: PRICING, INVENTORY, LOCATION & COMMERCIAL TERMS (1 COL) -->
        <div class="space-y-6">

          <!-- COMMERCIAL INVENTORY & PRICE (Listing) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-tag text-pp-600"></i> Pricing &amp; Inventory
              </h3>
              <button
                type="button"
                wire:click="startEditStock"
                class="text-xs font-black text-pp-600 hover:text-pp-700 cursor-pointer flex items-center gap-1"
              >
                <i class="fas fa-edit"></i> Edit Qty
              </button>
            </div>

            <!-- Price & Negotiable -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
              <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Unit Listing Price</span>
              <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-950">
                  ₦{{ number_format($listing->price, 2) }}
                </span>
                @if($listing->is_negotiable)
                  <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                    Negotiable
                  </span>
                @else
                  <span class="px-2.5 py-0.5 rounded-md bg-slate-200 text-slate-700 text-[10px] font-black uppercase">
                    Fixed Price
                  </span>
                @endif
              </div>
            </div>

            <!-- Stock Breakdown -->
            <div class="grid grid-cols-2 gap-2 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] text-slate-400 font-bold block uppercase">Total Stock</span>
                <span class="font-black text-slate-900 text-base">{{ $listing->quantity }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] text-slate-400 font-bold block uppercase">Available</span>
                <span class="font-black text-emerald-600 text-base">{{ $availableQty }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] text-slate-400 font-bold block uppercase">Reserved</span>
                <span class="font-black text-amber-600 text-base">{{ $listing->reserved_quantity }}</span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] text-slate-400 font-bold block uppercase">Sold Units</span>
                <span class="font-black text-blue-600 text-base">{{ $listing->sold_quantity }}</span>
              </div>
            </div>

            <!-- Edit Stock Modal/Inline Form -->
            @if($isEditingStock)
              <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3">
                <label class="text-[11px] font-black text-amber-900 block">Adjust Total Stock Quantity</label>
                <input
                  type="number"
                  wire:model="newQuantity"
                  min="0"
                  class="w-full px-3 py-2 rounded-xl bg-white border border-amber-300 text-slate-900 text-sm font-bold focus:ring-2 focus:ring-amber-500"
                />
                @error('newQuantity') <span class="text-rose-600 text-xs font-bold block">{{ $message }}</span> @enderror
                <div class="flex items-center gap-2 justify-end">
                  <button
                    type="button"
                    wire:click="$set('isEditingStock', false)"
                    class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-300 cursor-pointer"
                  >
                    Cancel
                  </button>
                  <button
                    type="button"
                    wire:click="saveStock"
                    class="px-3 py-1.5 rounded-xl bg-amber-600 text-white text-xs font-black hover:bg-amber-700 cursor-pointer shadow-xs"
                  >
                    Save Stock
                  </button>
                </div>
              </div>
            @endif

            <!-- Commercial Flags -->
            <div class="pt-2 border-t border-slate-100 space-y-2 text-xs font-bold text-slate-600">
              <div class="flex items-center justify-between">
                <span>Shipping Allowed:</span>
                <span class="{{ $listing->allow_shipping ? 'text-emerald-600' : 'text-slate-400' }}">
                  <i class="fas {{ $listing->allow_shipping ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                  {{ $listing->allow_shipping ? 'Yes, Available' : 'Pickup Only' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span>Active State:</span>
                <span class="{{ $listing->is_active ? 'text-emerald-600' : 'text-rose-600' }}">
                  <i class="fas {{ $listing->is_active ? 'fa-circle' : 'fa-circle-notch' }}"></i>
                  {{ $listing->is_active ? 'Active' : 'Disabled' }}
                </span>
              </div>
            </div>
          </div>

          <!-- WARRANTY POLICY (Listing) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
              <i class="fas fa-shield-alt text-pp-600"></i> Warranty Protection Policy
            </h3>
            
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-500 font-bold">Warranty Period:</span>
              <span class="font-black text-slate-900">
                {{ $listing->warranty_period_days ? $listing->warranty_period_days . ' Days' : 'No Warranty Provided' }}
              </span>
            </div>

            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-500 font-bold">Warranty Negotiable:</span>
              <span class="font-extrabold {{ $listing->is_warranty_negotiable ? 'text-emerald-600' : 'text-slate-500' }}">
                {{ $listing->is_warranty_negotiable ? 'Yes' : 'No' }}
              </span>
            </div>

            @if($listing->warranty_terms)
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600 mt-2">
                <span class="font-black block text-slate-800 mb-0.5">Custom Terms:</span>
                {{ $listing->warranty_terms }}
              </div>
            @endif
          </div>

          <!-- PHYSICAL STORAGE & DISPATCH LOCATION (Location) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-pp-600"></i> Dispatch &amp; Storage Location
              </h3>
              @if($loc)
                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                  Verified
                </span>
              @endif
            </div>

            @if($loc)
              <div class="space-y-2 text-xs">
                @if($loc->label)
                  <div class="font-black text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="fas fa-warehouse text-slate-400"></i> {{ $loc->label }}
                  </div>
                @endif

                <p class="text-slate-600 font-medium leading-relaxed">
                  {{ $loc->address_line_1 }}
                  @if($loc->address_line_2)<br>{{ $loc->address_line_2 }}@endif
                  <br>{{ $loc->city }}{{ $loc->state?->name ? ', ' . $loc->state->name : '' }}
                  {{ $loc->postal_code ? ' · ' . $loc->postal_code : '' }}
                  <br><span class="font-bold text-slate-800">{{ $loc->country?->name ?? 'Nigeria' }}</span>
                </p>

                @if($loc->contact_name || $loc->phone)
                  <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500 font-medium space-y-1">
                    @if($loc->contact_name)
                      <div>Contact: <strong class="text-slate-800">{{ $loc->contact_name }}</strong></div>
                    @endif
                    @if($loc->phone)
                      <div>Phone: <strong class="text-slate-800">{{ $loc->phone }}</strong></div>
                    @endif
                  </div>
                @endif
              </div>
            @else
              <div class="p-4 rounded-2xl bg-slate-50 text-center text-xs text-slate-400 italic">
                No specific physical warehouse linked. Using seller default address.
              </div>
            @endif
          </div>

        </div>

      </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: SALES & INVOICES (InvoiceItem)      -->
    <!-- ========================================== -->
    @if($activeTab === 'sales')
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-6">
        
        <!-- Header & Summary Cards -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
              <i class="fas fa-file-invoice-dollar text-emerald-600"></i> Customer Orders &amp; Invoices
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              Every invoice order item placed by buyers for this marketplace listing.
            </p>
          </div>

          <!-- Metric Pills -->
          <div class="flex items-center gap-3">
            <div class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">
              Total Units Sold: <strong class="text-emerald-700 font-black">{{ $totalSoldUnits }}</strong>
            </div>
            <div class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold">
              Total Revenue: <strong class="text-emerald-700 font-black">₦{{ number_format($grossRevenue, 2) }}</strong>
            </div>
          </div>
        </div>

        @if($salesHistory->isNotEmpty())
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
              <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">
                <tr>
                  <th class="py-3 px-4">Invoice #</th>
                  <th class="py-3 px-4">Buyer</th>
                  <th class="py-3 px-4">Order Date</th>
                  <th class="py-3 px-4 text-center">Qty</th>
                  <th class="py-3 px-4">Unit Price</th>
                  <th class="py-3 px-4">Total Amount</th>
                  <th class="py-3 px-4 text-center">Status</th>
                  <th class="py-3 px-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach($salesHistory as $saleItem)
                  @php
                    $inv = $saleItem->invoice;
                    $buyer = $inv?->buyer;
                  @endphp
                  <tr class="hover:bg-slate-50/70 transition">
                    <!-- Invoice # -->
                    <td class="py-3.5 px-4 font-black text-slate-900">
                      <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <a href="{{ $inv ? route('admin.invoices.show', $inv->id) : '#' }}" class="text-pp-600 hover:underline">
                          {{ $inv?->invoice_number ?: ('#INV-' . str_pad($saleItem->invoice_id, 5, '0', STR_PAD_LEFT)) }}
                        </a>
                      </div>
                    </td>

                    <!-- Buyer -->
                    <td class="py-3.5 px-4">
                      <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-pp-100 text-pp-700 font-black text-[10px] flex items-center justify-center shrink-0">
                          {{ strtoupper(substr($buyer?->name ?? 'B', 0, 1)) }}
                        </div>
                        <div>
                          <span class="font-extrabold text-slate-900 block leading-tight">{{ $buyer?->name ?? 'Guest Buyer' }}</span>
                          <span class="text-[10px] text-slate-400">{{ $buyer?->email }}</span>
                        </div>
                      </div>
                    </td>

                    <!-- Order Date -->
                    <td class="py-3.5 px-4 text-slate-500 font-medium">
                      {{ $saleItem->created_at->format('M d, Y · H:i') }}
                    </td>

                    <!-- Quantity -->
                    <td class="py-3.5 px-4 text-center font-black text-slate-900">
                      {{ $saleItem->quantity }}
                    </td>

                    <!-- Unit Price -->
                    <td class="py-3.5 px-4 font-bold text-slate-800">
                      ₦{{ number_format($saleItem->unit_price, 2) }}
                    </td>

                    <!-- Total Amount -->
                    <td class="py-3.5 px-4 font-black text-emerald-700">
                      ₦{{ number_format($saleItem->amount, 2) }}
                    </td>

                    <!-- Status -->
                    <td class="py-3.5 px-4 text-center">
                      @php
                        $invStatus = $inv?->status ?? 'paid';
                      @endphp
                      <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                        {{ $invStatus === 'completed' || $invStatus === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($invStatus === 'escrow' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700') }}">
                        {{ ucfirst($invStatus) }}
                      </span>
                    </td>

                    <!-- Action Link -->
                    <td class="py-3.5 px-4 text-right">
                      @if($inv)
                        <a
                          href="{{ route('admin.invoices.show', $inv->id) }}"
                          class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition inline-flex items-center gap-1"
                        >
                          <span>Inspect</span>
                          <i class="fas fa-chevron-right text-[9px]"></i>
                        </a>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <!-- Empty State -->
          <div class="p-12 text-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl">
              <i class="fas fa-receipt"></i>
            </div>
            <h4 class="text-sm font-black text-slate-800">No Orders Recorded Yet</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
              This listing has not recorded any completed or pending invoice sales yet. Orders will appear here in real-time.
            </p>
          </div>
        @endif

      </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: REVIEWS & RATINGS (ListingReview)   -->
    <!-- ========================================== -->
    @if($activeTab === 'reviews')
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 5-STAR DISTRIBUTION CARD (1 COL) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
          <div class="border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-chart-bar text-amber-500"></i> Satisfaction Rating Breakdown
            </h3>
          </div>

          <div class="text-center py-2">
            <div class="text-4xl font-black text-slate-950 flex items-center justify-center gap-2">
              <span>{{ number_format($avgRating, 1) }}</span>
              <span class="text-amber-400 text-3xl">★</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-1">
              Calculated from {{ $reviewsCount }} customer review(s)
            </p>
          </div>

          <!-- Progress Bars -->
          <div class="space-y-2 pt-2 border-t border-slate-100">
            @foreach([5, 4, 3, 2, 1] as $star)
              @php
                $starData = $starDistribution[$star] ?? ['count' => 0, 'percentage' => 0];
              @endphp
              <div class="flex items-center gap-2 text-xs">
                <span class="w-7 font-black text-slate-700 text-right">{{ $star }} ★</span>
                <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                  <div
                    class="h-full bg-amber-400 rounded-full"
                    style="width: {{ $starData['percentage'] }}%"
                  ></div>
                </div>
                <span class="w-10 text-[11px] font-bold text-slate-400 text-right">
                  {{ $starData['count'] }}
                </span>
              </div>
            @endforeach
          </div>
        </div>

        <!-- REVIEWS FEED (2 COLS) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
          <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-comments text-pp-600"></i> Verified Buyer Reviews ({{ $reviewsCount }})
            </h3>
            <span class="text-xs text-slate-400 font-semibold">Public Feedback</span>
          </div>

          @if($reviews->isNotEmpty())
            <div class="divide-y divide-slate-100 space-y-4">
              @foreach($reviews as $rev)
                <div class="pt-4 first:pt-0 space-y-2">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                      <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center">
                        {{ strtoupper(substr($rev->user?->name ?? 'U', 0, 1)) }}
                      </div>
                      <div>
                        <span class="text-xs font-black text-slate-900 block">{{ $rev->user?->name ?? 'Verified Buyer' }}</span>
                        <span class="text-[10px] text-slate-400">{{ $rev->created_at->diffForHumans() }}</span>
                      </div>
                    </div>

                    <!-- Rating Stars -->
                    <div class="flex items-center text-amber-400 text-xs">
                      @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $rev->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                      @endfor
                      <span class="ml-1.5 font-black text-slate-800 text-[11px]">{{ $rev->rating }}.0</span>
                    </div>
                  </div>

                  @if($rev->comment)
                    <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3 rounded-2xl border border-slate-100">
                      {{ $rev->comment }}
                    </p>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <!-- Empty State -->
            <div class="p-12 text-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 space-y-3">
              <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-xl">
                <i class="fas fa-star-half-alt"></i>
              </div>
              <h4 class="text-sm font-black text-slate-800">No Reviews Submitted Yet</h4>
              <p class="text-xs text-slate-400 max-w-sm mx-auto">
                No buyers have reviewed this item yet. Verified reviews will be listed here after buyers finalize their orders.
              </p>
            </div>
          @endif

        </div>

      </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 4: TRUST, REPORTS & MODERATION         -->
    <!-- (Moderation, Report, Promotion, Seller)    -->
    <!-- ========================================== -->
    @if($activeTab === 'trust')
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: USER REPORTS & PROMOTIONS (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- 1. USER REPORTS & FLAGS (Report) -->
          <div id="reports-section" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-flag text-rose-600"></i> User Reports &amp; Flags ({{ $listing->reports->count() }})
              </h3>
              <span class="text-xs text-slate-400 font-semibold">Community Integrity</span>
            </div>

            @if($listing->reports->isNotEmpty())
              <div class="space-y-3">
                @foreach($listing->reports as $rep)
                  <div class="p-4 rounded-2xl border {{ $rep->status === 'resolved' ? 'bg-slate-50 border-slate-200' : ($rep->status === 'dismissed' ? 'bg-slate-50/60 border-slate-200' : 'bg-rose-50/50 border-rose-200') }} space-y-2">
                    <div class="flex items-start justify-between gap-2">
                      <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black flex items-center justify-center">
                          <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                          <span class="text-xs font-black text-slate-900 block">{{ $rep->title ?: 'Listing Violation Report' }}</span>
                          <span class="text-[10px] text-slate-400">
                            Reported by <strong>{{ $rep->user?->name ?? 'Community Member' }}</strong> · {{ $rep->created_at->diffForHumans() }}
                          </span>
                        </div>
                      </div>

                      <!-- Report Status Badge -->
                      <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase
                        {{ $rep->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($rep->status === 'dismissed' ? 'bg-slate-200 text-slate-700' : 'bg-rose-100 text-rose-800 animate-pulse') }}">
                        {{ ucfirst($rep->status) }}
                      </span>
                    </div>

                    @if($rep->description)
                      <p class="text-xs text-slate-700 bg-white/80 p-3 rounded-xl border border-slate-200/60 leading-relaxed">
                        {{ $rep->description }}
                      </p>
                    @endif

                    @if($rep->resolution_notes)
                      <div class="text-[11px] text-slate-500 bg-white p-2.5 rounded-xl border border-slate-200 font-medium">
                        <strong>Resolution Notes:</strong> {{ $rep->resolution_notes }}
                        @if($rep->resolvedBy)
                          <span class="text-slate-400 block text-[10px]">Resolved by {{ $rep->resolvedBy->name }}</span>
                        @endif
                      </div>
                    @endif

                    <!-- Moderation controls on report -->
                    @if($rep->status === 'pending' || $rep->status === 'open')
                      <div class="flex items-center gap-2 pt-2 border-t border-slate-200/50 justify-end">
                        <button
                          type="button"
                          wire:click="dismissReport({{ $rep->id }})"
                          class="px-3 py-1 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer"
                        >
                          Dismiss Flag
                        </button>
                        <button
                          type="button"
                          wire:click="resolveReport({{ $rep->id }})"
                          class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition cursor-pointer shadow-xs"
                        >
                          Mark Resolved
                        </button>
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            @else
              <div class="p-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-center">
                <i class="fas fa-shield-heart text-3xl text-emerald-400 mb-2"></i>
                <p class="text-xs font-bold text-slate-700">Zero Community Reports</p>
                <p class="text-[11px] text-slate-400">No buyer or merchant has flagged this item for review.</p>
              </div>
            @endif
          </div>

          <!-- 2. PROMOTIONS & MARKETING BOOSTS (Promotion) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-bullhorn text-pp-600"></i> Promotional Campaigns &amp; Boosts ({{ $listing->promotions->count() }})
              </h3>
              <span class="text-xs text-slate-400 font-semibold">Ad Impressions &amp; Revenue</span>
            </div>

            @if($listing->promotions->isNotEmpty())
              <div class="space-y-3">
                @foreach($listing->promotions as $promo)
                  <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-black">
                        <i class="fas fa-rocket text-xs"></i>
                      </div>
                      <div>
                        <div class="flex items-center gap-2">
                          <span class="text-xs font-black text-slate-900 capitalize">{{ $promo->type ?: 'Marketplace Boost' }}</span>
                          <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                            {{ $promo->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $promo->status }}
                          </span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium">
                          Achieved Impressions / Reach: <strong>{{ number_format($promo->achieved_count) }}</strong>
                        </span>
                      </div>
                    </div>

                    <div class="text-right">
                      @if($promo->hasPaidPayment())
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black">
                          <i class="fas fa-check"></i> Paid
                        </span>
                      @else
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black">
                          Payment Pending
                        </span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="p-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-center">
                <i class="fas fa-bullhorn text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs font-bold text-slate-600">No Promotion Boosts Active</p>
                <p class="text-[11px] text-slate-400">This listing has not purchased spotlight or featured promotion campaigns.</p>
              </div>
            @endif
          </div>

        </div>

        <!-- RIGHT COLUMN: MODERATION AUDIT TRAIL & SELLER PROFILE (1 COL) -->
        <div class="space-y-6">

          <!-- 1. MODERATION AUDIT TRAIL (Moderation) -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-history text-pp-600"></i> Moderation Audit Trail ({{ $moderations->count() }})
              </h3>
            </div>

            @if($moderations->isNotEmpty())
              <div class="space-y-3">
                @foreach($moderations as $mod)
                  <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                      <span class="px-2 py-0.5 rounded-md font-black text-[10px] uppercase
                        {{ $mod->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($mod->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700') }}">
                        {{ $mod->status }}
                      </span>
                      <span class="text-[10px] text-slate-400">{{ $mod->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="font-bold text-slate-800 text-[11px]">
                      {{ $mod->action ?: 'Moderation Action' }}
                    </div>

                    @if($mod->moderator)
                      <div class="text-[10px] text-slate-500 font-medium">
                        Admin: <strong>{{ $mod->moderator->name }}</strong>
                      </div>
                    @endif

                    @if($mod->reason)
                      <div class="p-2 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 italic">
                        "{{ $mod->reason }}"
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-xs text-slate-400 italic">No historical moderation entries recorded yet.</p>
            @endif
          </div>

          <!-- 2. MERCHANT / SELLER PROFILE CARD (User) -->
          @php
            $seller = $listing->user;
          @endphp
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-store text-pp-600"></i> Merchant Account Profile
              </h3>
              @if($seller?->is_verified)
                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-black uppercase">
                  Verified KYC
                </span>
              @endif
            </div>

            @if($seller)
              <div class="space-y-3 text-xs">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white font-black text-sm flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($seller->name, 0, 1)) }}
                  </div>
                  <div>
                    <span class="font-black text-slate-950 text-sm block">{{ $seller->name }}</span>
                    <span class="text-slate-400 text-[11px]">{{ $seller->email }}</span>
                  </div>
                </div>

                <div class="pt-2 border-t border-slate-100 space-y-1.5 font-medium text-slate-600">
                  <div class="flex justify-between">
                    <span>Phone:</span>
                    <strong class="text-slate-900">{{ $seller->phone ?: 'Not provided' }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Country:</span>
                    <strong class="text-slate-900">{{ $seller->country?->name ?? 'Nigeria' }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Member Since:</span>
                    <strong class="text-slate-900">{{ $seller->created_at->format('M Y') }}</strong>
                  </div>
                  @if($seller->subscriptions->isNotEmpty())
                    <div class="flex justify-between">
                      <span>Plan:</span>
                      <strong class="text-pp-700 font-black">{{ $seller->subscriptions->first()->plan?->name ?? 'Pro Plan' }}</strong>
                    </div>
                  @endif
                </div>

                <div class="pt-2">
                  <a
                    href="{{ route('admin.users.show', $seller->id) }}"
                    class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs text-center block transition"
                  >
                    Inspect Merchant Details
                  </a>
                </div>
              </div>
            @endif
          </div>

        </div>

      </div>
    @endif

  </div>

  <!-- QUICK REJECTION MODAL -->
  @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
            <i class="fas fa-ban text-rose-600"></i> Reject Marketplace Listing
          </h3>
          <button
            type="button"
            wire:click="$set('showRejectModal', false)"
            class="text-slate-400 hover:text-slate-600 cursor-pointer"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <p class="text-xs text-slate-600">
          State the specific reason for rejecting listing <strong>#{{ $listing->id }}</strong>. This feedback will be logged to moderation records and sent to the merchant.
        </p>

        <!-- Preset Reasons Select -->
        <div>
          <label class="text-[11px] font-black uppercase text-slate-500 block mb-1">Preset Violations</label>
          <select
            wire:model.live="presetReason"
            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-rose-500"
          >
            <option value="">-- Choose a standard violation reason --</option>
            <option value="Prohibited or counterfeit item strictly disallowed on platform.">Prohibited / Counterfeit Goods</option>
            <option value="Misleading price or unrealistic commercial valuation.">Unrealistic or Inaccurate Pricing</option>
            <option value="Poor quality inspection photos or lack of real device proof.">Low Quality / Missing Photos</option>
            <option value="Duplicate listing entry found in marketplace catalog.">Duplicate Listing</option>
            <option value="Inaccurate technical specifications and vehicle compatibility details.">Inaccurate Compatibility Specs</option>
            <option value="Direct off-platform payment or contact information posted.">Prohibited Off-Platform Contact</option>
          </select>
        </div>

        <!-- Custom Reason Textarea -->
        <div>
          <label class="text-[11px] font-black uppercase text-slate-500 block mb-1">
            Rejection Explanation <span class="text-rose-600">*</span>
          </label>
          <textarea
            wire:model="rejectionReason"
            rows="4"
            placeholder="Type comprehensive feedback for the merchant regarding what needs to be fixed..."
            class="w-full px-3.5 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-rose-500 font-medium"
          ></textarea>
          @error('rejectionReason')
            <span class="text-rose-600 text-xs font-bold block mt-1">{{ $message }}</span>
          @enderror
        </div>

        <!-- Modal Actions -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
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
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs transition cursor-pointer shadow-xs flex items-center gap-1.5"
          >
            <i class="fas fa-ban text-[11px]"></i>
            <span>Confirm Rejection</span>
          </button>
        </div>
      </div>
    </div>
  @endif

</div>
