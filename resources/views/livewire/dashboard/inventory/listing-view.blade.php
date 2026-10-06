<div class="space-y-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('mylistings') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Listings
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Listing Ref: #LST-{{ str_pad($listing->id, 5, '0', STR_PAD_LEFT) }}</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 flex items-center gap-2 sm:gap-3 flex-wrap">
        <span>{{ $listing->item?->name ?: ($listing->item?->deviceModel?->name ?? "Marketplace Listing #{$listing->id}") }}</span>
        
        <!-- Published Status Badge -->
        @if($listing->is_published)
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase">
            Published
          </span>
        @else
          <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[10px] uppercase">
            Draft / Hidden
          </span>
        @endif

        <!-- Moderation Approval Status Badge -->
        @if($approvalStatus === 'approved')
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-check-circle text-[9px]"></i> Approved
          </span>
        @elseif($approvalStatus === 'pending')
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-clock text-[9px]"></i> Moderation Pending
          </span>
        @elseif($approvalStatus === 'rejected')
          <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] uppercase flex items-center gap-1">
            <i class="fas fa-ban text-[9px]"></i> Rejected
          </span>
        @endif

        <!-- Listing Overall Status Badge -->
        @if($listingStatus === 'live')
          <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 font-extrabold text-[10px] uppercase">
            Live
          </span>
        @elseif($listingStatus === 'sold out')
          <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[10px] uppercase">
            Sold Out
          </span>
        @elseif($listingStatus === 'inactive')
          <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[10px] uppercase">
            Inactive
          </span>
        @endif

        <!-- Stock Indicator -->
        @php $availStock = $listing->availableQuantity(); @endphp
        @if($availStock <= 2 && $availStock > 0)
          <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px] uppercase">
            Low Stock ({{ $availStock }} left)
          </span>
        @endif
      </h1>
      <p class="text-xs text-slate-500 mt-1">
        Created on {{ $listing->created_at->format('M d, Y') }} · Last updated {{ $listing->updated_at->diffForHumans() }}
      </p>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
      @if($listing->item_id)
        <a
          href="{{ route('item.view', $listing->item_id) }}"
          class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs shadow-2xs transition flex items-center gap-1.5"
        >
          <i class="fas fa-box text-slate-400 text-xs"></i>
          <span>Inventory Item</span>
        </a>
      @endif

      <a
        href="{{ route('listing-details', $listing->slug ?: $listing->id) }}"
        target="_blank"
        class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs shadow-2xs transition flex items-center gap-1.5"
      >
        <i class="fas fa-external-link-alt text-pp-600 text-xs"></i>
        <span>Public View</span>
      </a>

      <button
        type="button"
        wire:click="togglePublish"
        class="px-3.5 py-2 rounded-xl border {{ $listing->is_published ? 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800' }} font-extrabold text-xs transition flex items-center gap-1.5"
      >
        <i class="fas {{ $listing->is_published ? 'fa-pause' : 'fa-play' }} text-xs"></i>
        <span>{{ $listing->is_published ? 'Pause' : 'Publish' }}</span>
      </button>

      <button
        type="button"
        wire:click="setActiveTab('promotions')"
        class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5"
      >
        <i class="fas fa-bolt text-xs"></i>
        <span>Boost Listing</span>
      </button>

      <button
        type="button"
        wire:click="openEditModal"
        class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5"
      >
        <i class="fas fa-pen text-xs"></i>
        <span>Edit</span>
      </button>
    </div>
  </div>

  <!-- FLASH & ALERT MESSAGES -->
  @if (session('message') || session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
        <span>{{ session('message') ?: session('success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
        <i class="fas fa-times text-xs"></i>
      </button>
    </div>
  @endif

  @if (session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
        <i class="fas fa-times text-xs"></i>
      </button>
    </div>
  @endif

  <!-- NAVIGATION TABS -->
  <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
    <button
      type="button"
      wire:click="setActiveTab('overview')"
      class="px-4 py-3 text-xs font-extrabold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $activeTab === 'overview' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}"
    >
      <i class="fas fa-chart-pie text-xs"></i>
      <span>Overview &amp; Analytics</span>
    </button>

    <button
      type="button"
      wire:click="setActiveTab('promotions')"
      class="px-4 py-3 text-xs font-extrabold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $activeTab === 'promotions' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}"
    >
      <i class="fas fa-bullhorn text-xs"></i>
      <span>Promotions &amp; Boosts</span>
      @if($ongoingPromotions->isNotEmpty())
        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">
          {{ $ongoingPromotions->count() }} Active
        </span>
      @endif
    </button>

    <button
      type="button"
      wire:click="setActiveTab('reviews')"
      class="px-4 py-3 text-xs font-extrabold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $activeTab === 'reviews' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}"
    >
      <i class="fas fa-star text-xs"></i>
      <span>Customer Reviews</span>
      <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
        {{ $listing->reviews->count() }}
      </span>
    </button>

    <button
      type="button"
      wire:click="setActiveTab('reports')"
      class="px-4 py-3 text-xs font-extrabold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $activeTab === 'reports' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}"
    >
      <i class="fas fa-shield-alt text-xs"></i>
      <span>Reports &amp; Moderation</span>
      @if($listing->reports->isNotEmpty())
        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-black">
          {{ $listing->reports->count() }}
        </span>
      @endif
    </button>

    <button
      type="button"
      wire:click="setActiveTab('specs')"
      class="px-4 py-3 text-xs font-extrabold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $activeTab === 'specs' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}"
    >
      <i class="fas fa-microchip text-xs"></i>
      <span>Hardware &amp; Specs</span>
    </button>
  </div>

  <!-- ============================================================== -->
  <!-- TAB 1: OVERVIEW & ANALYTICS                                    -->
  <!-- ============================================================== -->
  @if($activeTab === 'overview')
    <div class="space-y-6">

      <!-- 8-METRIC HIGH-DENSITY GRID -->
      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
        
        <!-- 1. Price -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Unit Price</span>
          <span class="text-base sm:text-lg font-black text-slate-950 block truncate">₦{{ number_format((float) $listing->price, 0) }}</span>
          <span class="text-[10px] {{ $listing->is_negotiable ? 'text-pp-600 font-bold' : 'text-slate-400' }} block">
            {{ $listing->is_negotiable ? 'Negotiable' : 'Fixed' }}
          </span>
        </div>

        <!-- 2. Stock Available -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Available</span>
          <span class="text-base sm:text-lg font-black {{ $availStock <= 0 ? 'text-rose-600' : 'text-slate-900' }} block">
            {{ $availStock }} Units
          </span>
          <span class="text-[10px] text-slate-400 block">{{ $listing->quantity }} Total</span>
        </div>

        <!-- 3. Closed Orders -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Closed Orders</span>
          <span class="text-base sm:text-lg font-black text-emerald-600 block">{{ $closedOrdersCount }}</span>
          <span class="text-[10px] text-slate-400 block">Paid Invoices</span>
        </div>

        <!-- 4. Open Orders -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Open Orders</span>
          <span class="text-base sm:text-lg font-black text-amber-600 block">{{ $openOrdersCount }}</span>
          <span class="text-[10px] text-slate-400 block">Pending Escrow</span>
        </div>

        <!-- 5. Offer Inquiries -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Offers</span>
          <span class="text-base sm:text-lg font-black text-purple-600 block">{{ $offersCount }}</span>
          <span class="text-[10px] text-slate-400 block">Buyer Offers</span>
        </div>

        <!-- 6. Total Sales Revenue -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Gross Sales</span>
          <span class="text-base sm:text-lg font-black text-slate-900 block truncate">₦{{ number_format($grossRevenue, 0) }}</span>
          <span class="text-[10px] text-emerald-600 font-bold block">{{ $listing->sold_quantity ?? 0 }} Sold</span>
        </div>

        <!-- 7. Impressions / Views -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Views</span>
          <span class="text-base sm:text-lg font-black text-sky-600 block">{{ number_format($viewsCount) }}</span>
          <span class="text-[10px] text-slate-400 block">{{ $wishlistsCount }} Saves</span>
        </div>

        <!-- 8. Promotion Status -->
        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-2xs space-y-1">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Promotions</span>
          <span class="text-base sm:text-lg font-black {{ $ongoingPromotions->isNotEmpty() ? 'text-amber-500' : 'text-slate-400' }} block">
            {{ $ongoingPromotions->isNotEmpty() ? $ongoingPromotions->count() . ' Active' : 'None' }}
          </span>
          <span class="text-[10px] text-slate-500 block truncate">
            {{ $totalAchievedClicks }} Clicks · {{ $totalAchievedViews }} Views
          </span>
        </div>

      </div>

      <!-- MAIN DASHBOARD SPLIT -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT (8 COLS): Promotion Banner, Specs Preview, Order Summary -->
        <div class="lg:col-span-8 space-y-6">

          <!-- PROMOTIONS ACHIEVEMENT HIGHLIGHT CARD -->
          @if($ongoingPromotions->isNotEmpty())
            <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white shadow-soft space-y-4">
              <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                  <span class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                    <i class="fas fa-fire"></i>
                  </span>
                  <div>
                    <h3 class="text-sm font-extrabold">Active Promotion Campaign Running</h3>
                    <p class="text-[11px] text-amber-100">Your listing is being prioritized in marketplace search and category feeds.</p>
                  </div>
                </div>
                <button
                  type="button"
                  wire:click="setActiveTab('promotions')"
                  class="px-3.5 py-1.5 rounded-xl bg-white text-amber-700 hover:bg-amber-50 font-extrabold text-xs shadow-xs transition"
                >
                  Manage Campaigns &rarr;
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-white/20">
                @foreach($ongoingPromotions as $promo)
                  <div class="p-3.5 rounded-2xl bg-black/15 backdrop-blur-xs space-y-2">
                    <div class="flex items-center justify-between text-xs">
                      <span class="font-extrabold uppercase tracking-wider text-[10px] text-amber-200">
                        {{ ucfirst($promo->type) }} Campaign #{{ $promo->id }}
                      </span>
                      <span class="px-2 py-0.5 rounded-full bg-emerald-400 text-slate-950 font-black text-[9px] uppercase">
                        Active
                      </span>
                    </div>
                    <div class="flex items-baseline justify-between">
                      <span class="text-xl font-black">{{ number_format($promo->achieved_count) }}</span>
                      <span class="text-xs text-amber-200">delivered so far</span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @else
            <!-- BOOST CTA -->
            <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-soft flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <div class="flex items-center gap-2 text-amber-400 text-xs font-bold uppercase tracking-wider">
                  <i class="fas fa-bolt"></i> Boost Marketplace Visibility
                </div>
                <h3 class="text-base font-extrabold">Get More Eyes &amp; Direct Buyers on This Listing</h3>
                <p class="text-xs text-slate-400 max-w-lg">
                  Launch targeted pay-per-click or impression campaigns starting at minimal rates for your region.
                </p>
              </div>
              <button
                type="button"
                wire:click="setActiveTab('promotions')"
                class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition shrink-0 flex items-center gap-2"
              >
                <i class="fas fa-rocket text-xs"></i>
                <span>Buy Promotions</span>
              </button>
            </div>
          @endif

          <!-- COMMERCIAL & DELIVERY TERMS -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center justify-between">
              <span class="flex items-center gap-2">
                <i class="fas fa-file-contract text-pp-600"></i>
                <span>Commercial &amp; Fulfillment Terms</span>
              </span>
              <button type="button" wire:click="openEditModal" class="text-xs font-bold text-pp-600 hover:underline">
                Edit Terms
              </button>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Warranty Period</span>
                <span class="text-sm font-bold text-slate-900">
                  @if(($listing->warranty_period_days ?? 0) > 0)
                    {{ $listing->warranty_period_days }} Days Warranty
                    @if($listing->is_warranty_negotiable)
                      <span class="text-xs text-pp-600 font-semibold">(Negotiable)</span>
                    @endif
                  @else
                    No Warranty Offered
                  @endif
                </span>
              </div>

              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Shipping &amp; Fulfillment</span>
                <span class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                  @if($listing->allow_shipping)
                    <i class="fas fa-truck text-emerald-600"></i>
                    <span>Courier Delivery Available</span>
                  @else
                    <i class="fas fa-handshake text-amber-600"></i>
                    <span>Local Pickup Only</span>
                  @endif
                </span>
              </div>
            </div>

            @if(!empty($listing->warranty_terms))
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Warranty Policy</span>
                <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">{{ $listing->warranty_terms }}</p>
              </div>
            @endif
          </div>

          <!-- UNDERLYING ASSET SNAPSHOT -->
          @if($listing->item)
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
              <div class="flex items-center justify-between">
                <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                  <i class="fas fa-microchip text-pp-600"></i>
                  <span>Linked Hardware Asset (Item #ITM-{{ str_pad($listing->item->id, 4, '0', STR_PAD_LEFT) }})</span>
                </h2>
                <a href="{{ route('item.view', $listing->item->id) }}" class="text-xs font-bold text-pp-600 hover:underline">
                  Full Asset Details &rarr;
                </a>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                  <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Brand</span>
                  <span class="text-xs font-bold text-slate-800">{{ $listing->item->deviceModel?->brand?->name ?? 'N/A' }}</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                  <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Model</span>
                  <span class="text-xs font-bold text-slate-800">{{ $listing->item->deviceModel?->name ?? 'N/A' }}</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                  <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Condition</span>
                  <span class="text-xs font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $listing->item->condition_status ?? 'used')) }}</span>
                </div>
              </div>
            </div>
          @endif

        </div>

        <!-- RIGHT (4 COLS): Media Snapshot & Share -->
        <div class="lg:col-span-4 space-y-6">

          <!-- MEDIA GALLERY -->
          <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center justify-between">
              <span>Listing Media</span>
              <span class="text-xs text-slate-400 font-normal">
                {{ ($listing->item?->media?->count() ?? 0) + ($listing->media?->count() ?? 0) }} Photo(s)
              </span>
            </h3>

            @php
              $allMedia = collect()
                ->concat($listing->media ?? [])
                ->concat($listing->item?->media ?? [])
                ->unique('id');
            @endphp

            @if($allMedia->isNotEmpty())
              <div class="space-y-2">
                <div class="aspect-4/3 rounded-2xl bg-slate-100 overflow-hidden border border-slate-200 relative">
                  <img
                    src="{{ $allMedia->first()->original_url ?? asset('storage/' . $allMedia->first()->file_path) }}"
                    alt="{{ $listing->item?->name }}"
                    class="w-full h-full object-cover"
                  />
                  <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-slate-950/70 text-white text-[9px] font-bold uppercase">
                    Featured
                  </span>
                </div>

                @if($allMedia->count() > 1)
                  <div class="grid grid-cols-4 gap-2">
                    @foreach($allMedia->skip(1)->take(4) as $med)
                      <div class="aspect-square rounded-xl bg-slate-100 overflow-hidden border border-slate-200">
                        <img
                          src="{{ $med->original_url ?? asset('storage/' . $med->file_path) }}"
                          alt="Listing thumbnail"
                          class="w-full h-full object-cover"
                        />
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            @else
              <div class="p-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                  <i class="fas fa-image"></i>
                </div>
                <p class="text-xs text-slate-500 font-medium">No photos attached yet.</p>
              </div>
            @endif
          </div>

          <!-- DIRECT SHARE & MARKETPLACE LINK -->
          <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-3">
            <h3 class="text-sm font-extrabold text-slate-900">Direct Marketplace Link</h3>
            <p class="text-[11px] text-slate-500 leading-normal">
              Share with prospective buyers across WhatsApp, Instagram, or direct message.
            </p>

            @php
              $publicUrl = route('listing-details', $listing->slug ?: $listing->id);
            @endphp

            <div class="flex items-center gap-2">
              <input
                type="text"
                readonly
                value="{{ $publicUrl }}"
                id="publicListingUrlInput"
                class="w-full text-xs font-mono text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 select-all focus:outline-none"
              />
              <button
                type="button"
                onclick="navigator.clipboard.writeText(document.getElementById('publicListingUrlInput').value); alert('Listing link copied to clipboard!');"
                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition shrink-0"
                title="Copy URL"
              >
                <i class="fas fa-copy"></i>
              </button>
            </div>
          </div>

          <!-- SELLER SAFETY GUARANTEE -->
          <div class="p-5 rounded-3xl bg-slate-900 text-white space-y-3 shadow-soft">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fas fa-shield-halved"></i>
              </div>
              <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-emerald-400">Parts &amp; Parcel Escrow</h4>
                <span class="text-[11px] text-slate-400 font-medium">Guaranteed Seller Settlement</span>
              </div>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
              When buyers pay via Platform Escrow, payments are secured upfront and settled directly to your registered bank account upon buyer confirmation or warranty expiry.
            </p>
          </div>

        </div>

      </div>

    </div>
  @endif

  <!-- ============================================================== -->
  <!-- TAB 2: PROMOTIONS & CAMPAIGN BOOSTS                            -->
  <!-- ============================================================== -->
  @if($activeTab === 'promotions')
    <div class="space-y-6">

      <!-- ONGOING & PAST PROMOTION CAMPAIGNS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div>
            <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
              <i class="fas fa-bullhorn text-pp-600"></i>
              <span>Promotional Campaigns for This Listing</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Track achievements, delivered views, and inbound clicks generated by your boost campaigns.
            </p>
          </div>
          <span class="text-xs font-bold text-slate-400">
            {{ $promotions->count() }} Campaign(s) Total
          </span>
        </div>

        @if($promotions->isEmpty())
          <div class="p-8 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-pp-50 text-pp-600 flex items-center justify-center mx-auto text-lg">
              <i class="fas fa-bolt"></i>
            </div>
            <div class="space-y-1">
              <h3 class="text-sm font-extrabold text-slate-900">No Promotions Launched Yet</h3>
              <p class="text-xs text-slate-500 max-w-md mx-auto">
                Promoted listings receive up to 5x more clicks, priority search placement, and top spots on category feeds. Purchase views or clicks below to start boosting.
              </p>
            </div>
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($promotions as $promo)
              <div class="p-5 rounded-2xl border {{ $promo->status === 'active' ? 'border-amber-200 bg-amber-50/30' : 'border-slate-200 bg-slate-50/50' }} space-y-3">
                <div class="flex items-center justify-between">
                  <span class="px-2.5 py-0.5 rounded-full {{ $promo->type === 'clicks' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }} font-extrabold text-[10px] uppercase flex items-center gap-1">
                    <i class="fas {{ $promo->type === 'clicks' ? 'fa-mouse-pointer' : 'fa-eye' }} text-[9px]"></i>
                    <span>{{ ucfirst($promo->type) }} Campaign</span>
                  </span>

                  @if($promo->status === 'active')
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] uppercase">Active</span>
                  @elseif($promo->status === 'completed')
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] uppercase">Completed</span>
                  @elseif($promo->status === 'pending')
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px] uppercase">Pending</span>
                  @else
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] uppercase">{{ ucfirst($promo->status) }}</span>
                  @endif
                </div>

                <div class="space-y-1">
                  <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($promo->achieved_count) }}</span>
                    @php
                      $purchased = (int) ($promo->payments?->metadata['quantity'] ?? 0);
                    @endphp
                    @if($purchased > 0)
                      <span class="text-xs text-slate-500 font-semibold">of {{ number_format($purchased) }} target</span>
                    @else
                      <span class="text-xs text-slate-500 font-semibold">delivered</span>
                    @endif
                  </div>

                  @if($purchased > 0)
                    @php $pct = min(100, round(($promo->achieved_count / $purchased) * 100)); @endphp
                    <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                      <div class="h-full bg-pp-600 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                  @endif
                </div>

                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                  <span>Started {{ $promo->created_at->format('M d, Y') }}</span>
                  @if($promo->payments)
                    <span class="font-mono text-[10px] text-slate-400 truncate max-w-[120px]">{{ $promo->payments->reference }}</span>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- PURCHASE NEW PROMOTION SECTION -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-soft space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-lg font-extrabold text-slate-950 flex items-center gap-2">
              <i class="fas fa-bolt text-amber-500"></i>
              <span>Purchase New Promotion Boost</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Select your campaign type, quantity, and apply any promo coupons to activate instant visibility.
            </p>
          </div>
          <div class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center gap-1.5 self-start sm:self-auto">
            <i class="fas fa-globe-africa text-pp-600 text-xs"></i>
            <span>{{ $countryName }} Plan</span>
          </div>
        </div>

        @if($purchaseError)
          <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-600"></i>
            <span>{{ $purchaseError }}</span>
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          <!-- LEFT (7 COLS): Promotion Configurator -->
          <div class="lg:col-span-7 space-y-6">

            <!-- 1. CAMPAIGN TYPE SELECTION (SINGLE TYPE ONLY) -->
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-wider text-slate-700 block">
                1. Select Promotion Type <span class="text-slate-400 font-normal">(One type per campaign)</span>
              </label>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Clicks Option -->
                <button
                  type="button"
                  wire:click="setPromoType('clicks')"
                  class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $promoType === 'clicks' ? 'border-pp-600 bg-pp-50/40 ring-2 ring-pp-600/20' : 'border-slate-200 hover:border-slate-300 bg-white' }}"
                >
                  <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-xl {{ $promoType === 'clicks' ? 'bg-pp-600 text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-xs">
                      <i class="fas fa-mouse-pointer"></i>
                    </span>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $promoType === 'clicks' ? 'bg-pp-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                      High Intent
                    </span>
                  </div>
                  <div>
                    <h4 class="text-sm font-extrabold text-slate-900">Promotional Clicks (PPC)</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pay only when interested buyers click directly into your listing.</p>
                  </div>
                  <div class="pt-2 border-t border-slate-200/60 flex items-baseline justify-between text-xs">
                    <span class="text-slate-500 text-[11px]">Unit Rate:</span>
                    <span class="font-extrabold text-slate-900">{{ $currencySymbol }}{{ number_format($sellerCountry->clicks, 2) }} / click</span>
                  </div>
                </button>

                <!-- Views Option -->
                <button
                  type="button"
                  wire:click="setPromoType('views')"
                  class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 cursor-pointer {{ $promoType === 'views' ? 'border-pp-600 bg-pp-50/40 ring-2 ring-pp-600/20' : 'border-slate-200 hover:border-slate-300 bg-white' }}"
                >
                  <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-xl {{ $promoType === 'views' ? 'bg-pp-600 text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-xs">
                      <i class="fas fa-eye"></i>
                    </span>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $promoType === 'views' ? 'bg-pp-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                      Maximum Reach
                    </span>
                  </div>
                  <div>
                    <h4 class="text-sm font-extrabold text-slate-900">Promotional Impressions (PPI)</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Showcase your listing banner at the top of category &amp; search feeds.</p>
                  </div>
                  <div class="pt-2 border-t border-slate-200/60 flex items-baseline justify-between text-xs">
                    <span class="text-slate-500 text-[11px]">Unit Rate:</span>
                    <span class="font-extrabold text-slate-900">{{ $currencySymbol }}{{ number_format($sellerCountry->views, 4) }} / view</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- 2. QUANTITY ADJUSTER -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <label class="text-xs font-black uppercase tracking-wider text-slate-700">
                  2. Choose Volume <span class="text-slate-400 font-normal">({{ $promoType === 'clicks' ? 'Clicks' : 'Views' }})</span>
                </label>
                <span class="text-xs text-slate-500">
                  Min: <strong class="text-slate-900">{{ number_format($this->getMinQuantity()) }}</strong>
                </span>
              </div>

              <div class="flex items-center gap-3">
                <button
                  type="button"
                  wire:click="decrementPromoQuantity({{ $promoType === 'clicks' ? 5 : 1000 }})"
                  class="w-11 h-11 rounded-2xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-black text-base flex items-center justify-center transition shrink-0"
                >
                  <i class="fas fa-minus text-xs"></i>
                </button>

                <input
                  type="number"
                  wire:model.live.debounce.300ms="promoQuantity"
                  min="{{ $this->getMinQuantity() }}"
                  class="w-full text-center text-lg font-black text-slate-900 border border-slate-200 rounded-2xl py-2.5 focus:border-pp-600 focus:outline-none"
                />

                <button
                  type="button"
                  wire:click="incrementPromoQuantity({{ $promoType === 'clicks' ? 5 : 1000 }})"
                  class="w-11 h-11 rounded-2xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-black text-base flex items-center justify-center transition shrink-0"
                >
                  <i class="fas fa-plus text-xs"></i>
                </button>
              </div>

              <!-- Quick Add Step Chips -->
              <div class="flex items-center gap-2 flex-wrap pt-1">
                <span class="text-[11px] font-bold text-slate-400">Quick add:</span>
                @if($promoType === 'clicks')
                  <button type="button" wire:click="incrementPromoQuantity(10)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+10</button>
                  <button type="button" wire:click="incrementPromoQuantity(25)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+25</button>
                  <button type="button" wire:click="incrementPromoQuantity(50)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+50</button>
                  <button type="button" wire:click="incrementPromoQuantity(100)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+100</button>
                @else
                  <button type="button" wire:click="incrementPromoQuantity(2000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+2,000</button>
                  <button type="button" wire:click="incrementPromoQuantity(5000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+5,000</button>
                  <button type="button" wire:click="incrementPromoQuantity(10000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+10,000</button>
                  <button type="button" wire:click="incrementPromoQuantity(25000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] transition">+25,000</button>
                @endif
              </div>
            </div>

            <!-- 3. COUPON VOUCHER INPUT -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
              <label class="text-xs font-black uppercase tracking-wider text-slate-700 block">
                3. Promotional Coupon / Discount Code
              </label>

              @if($couponValid && $appliedCouponId)
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs">
                      <i class="fas fa-ticket"></i>
                    </span>
                    <span class="text-xs font-extrabold text-emerald-900">{{ $couponMessage }}</span>
                  </div>
                  <button
                    type="button"
                    wire:click="removeCoupon"
                    class="text-xs font-bold text-rose-600 hover:underline cursor-pointer"
                  >
                    Remove
                  </button>
                </div>
              @else
                <div class="flex items-center gap-2">
                  <div class="relative w-full">
                    <i class="fas fa-ticket absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input
                      type="text"
                      wire:model="couponCode"
                      wire:keydown.enter.prevent="applyCoupon"
                      placeholder="e.g. BOOST20 or SAVE50"
                      class="w-full text-xs font-bold uppercase rounded-2xl border border-slate-200 pl-8 pr-3 py-2.5 focus:border-pp-600 focus:outline-none"
                    />
                  </div>
                  <button
                    type="button"
                    wire:click="applyCoupon"
                    class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition shrink-0 cursor-pointer"
                  >
                    Apply
                  </button>
                </div>
                @error('couponCode')
                  <span class="text-[11px] text-rose-500 font-bold block">{{ $message }}</span>
                @enderror
              @endif
            </div>

            <!-- 4. GATEWAY PROVIDER -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
              <label class="text-xs font-black uppercase tracking-wider text-slate-700 block">
                4. Payment Provider
              </label>

              <div class="grid grid-cols-2 gap-3">
                <label class="p-3.5 rounded-2xl border cursor-pointer flex items-center justify-between transition {{ $paymentProvider === 'paystack' ? 'border-pp-600 bg-pp-50/30' : 'border-slate-200 hover:border-slate-300' }}">
                  <div class="flex items-center gap-2.5">
                    <input
                      type="radio"
                      name="payment_gateway"
                      value="paystack"
                      wire:model.live="paymentProvider"
                      class="text-pp-600 focus:ring-pp-500"
                    />
                    <span class="text-xs font-extrabold text-slate-900">Paystack</span>
                  </div>
                  <span class="text-[10px] font-bold text-slate-400 uppercase">Cards · Transfer</span>
                </label>

                <label class="p-3.5 rounded-2xl border cursor-pointer flex items-center justify-between transition {{ $paymentProvider === 'flutterwave' ? 'border-pp-600 bg-pp-50/30' : 'border-slate-200 hover:border-slate-300' }}">
                  <div class="flex items-center gap-2.5">
                    <input
                      type="radio"
                      name="payment_gateway"
                      value="flutterwave"
                      wire:model.live="paymentProvider"
                      class="text-pp-600 focus:ring-pp-500"
                    />
                    <span class="text-xs font-extrabold text-slate-900">Flutterwave</span>
                  </div>
                  <span class="text-[10px] font-bold text-slate-400 uppercase">Cards · Mobile</span>
                </label>
              </div>
            </div>

          </div>

          <!-- RIGHT (5 COLS): Order Summary & Checkout Card -->
          <div class="lg:col-span-5">
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-5">
              <h3 class="text-sm font-extrabold text-slate-900 flex items-center justify-between">
                <span>Campaign Summary</span>
                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-pp-100 text-pp-800">
                  Instant Activation
                </span>
              </h3>

              <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Selected Type:</span>
                  <span class="font-extrabold text-slate-900 uppercase">{{ ucfirst($promoType) }}</span>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Quantity Ordered:</span>
                  <span class="font-extrabold text-slate-900">{{ number_format($promoQuantity) }} {{ $promoType }}</span>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Unit Price:</span>
                  <span class="font-bold text-slate-800">
                    {{ $currencySymbol }}{{ $promoType === 'clicks' ? number_format($sellerCountry->clicks, 2) : number_format($sellerCountry->views, 4) }}
                  </span>
                </div>

                <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                  <span class="text-slate-600 font-bold">Subtotal:</span>
                  <span class="font-extrabold text-slate-900">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</span>
                </div>

                @if($couponDiscount > 0)
                  <div class="flex items-center justify-between text-emerald-600 font-bold">
                    <span>Coupon Discount:</span>
                    <span>-{{ $currencySymbol }}{{ number_format($couponDiscount, 2) }}</span>
                  </div>
                @endif

                <div class="pt-3 border-t-2 border-slate-200 flex items-baseline justify-between">
                  <span class="text-sm font-black text-slate-900">Total Payable:</span>
                  <span class="text-2xl font-black text-slate-950">
                    {{ $currencySymbol }}{{ number_format($totalAmount, 2) }}
                  </span>
                </div>
              </div>

              <button
                type="button"
                wire:click="processPromotionPayment"
                wire:loading.attr="disabled"
                class="w-full py-3.5 px-4 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
              >
                <span wire:loading.remove>
                  <i class="fas fa-lock text-xs mr-1"></i>
                  <span>Pay {{ $currencySymbol }}{{ number_format($totalAmount, 2) }} &amp; Boost Now</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                  <i class="fas fa-spinner fa-spin text-xs"></i>
                  <span>Connecting Secure Gateway...</span>
                </span>
              </button>

              <div class="text-[11px] text-slate-500 text-center space-y-1">
                <div class="flex items-center justify-center gap-2 text-emerald-600 font-bold">
                  <i class="fas fa-shield-alt"></i> Verified Payment Gateway
                </div>
                <p>Transactions are processed via encrypted SSL. Campaigns commence automatically upon successful confirmation.</p>
              </div>

            </div>
          </div>

        </div>

      </div>

    </div>
  @endif

  <!-- ============================================================== -->
  <!-- TAB 3: CUSTOMER REVIEWS                                        -->
  <!-- ============================================================== -->
  @if($activeTab === 'reviews')
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-soft space-y-6">
      <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-4">
        <div>
          <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            <span>Verified Customer Reviews</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Feedback and ratings submitted by buyers after receiving and testing their orders.
          </p>
        </div>
        <span class="text-xs font-bold text-slate-400">
          {{ $listing->reviews->count() }} Review(s)
        </span>
      </div>

      @if($listing->reviews->isEmpty())
        <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 text-center space-y-2">
          <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
            <i class="fas fa-comment-dots"></i>
          </div>
          <h3 class="text-xs font-extrabold text-slate-700">No Reviews Recorded Yet</h3>
          <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
            Once orders are placed and delivered, verified buyer ratings will appear here automatically.
          </p>
        </div>
      @else
        <div class="space-y-3">
          @foreach($listing->reviews as $rev)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-pp-100 text-pp-700 font-extrabold text-xs flex items-center justify-center">
                    {{ strtoupper(substr($rev->user?->name ?? 'U', 0, 2)) }}
                  </div>
                  <div>
                    <h4 class="text-xs font-bold text-slate-900">{{ $rev->user?->name ?? 'Verified Buyer' }}</h4>
                    <span class="text-[10px] text-slate-400">{{ $rev->created_at->format('M d, Y') }}</span>
                  </div>
                </div>
                <div class="flex items-center gap-1 text-amber-400 text-xs">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= ($rev->rating ?? 5) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                  @endfor
                </div>
              </div>
              @if($rev->comment)
                <p class="text-xs text-slate-700 leading-relaxed bg-white p-3 rounded-xl border border-slate-100">
                  {{ $rev->comment }}
                </p>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  <!-- ============================================================== -->
  <!-- TAB 4: AUDIT & BUYER REPORTS                                   -->
  <!-- ============================================================== -->
  @if($activeTab === 'reports')
    <div class="space-y-6">

      <!-- MODERATION APPROVAL STATUS CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-3">
        <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
          <i class="fas fa-stamp text-pp-600"></i>
          <span>Listing Moderation Approval Status</span>
        </h2>

        <div class="flex items-center gap-3 p-4 rounded-2xl {{ $approvalStatus === 'approved' ? 'bg-emerald-50 border border-emerald-200' : ($approvalStatus === 'rejected' ? 'bg-rose-50 border border-rose-200' : 'bg-amber-50 border border-amber-200') }}">
          <div class="w-10 h-10 rounded-xl {{ $approvalStatus === 'approved' ? 'bg-emerald-600 text-white' : ($approvalStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white') }} flex items-center justify-center text-sm shrink-0">
            <i class="fas {{ $approvalStatus === 'approved' ? 'fa-check' : ($approvalStatus === 'rejected' ? 'fa-ban' : 'fa-hourglass-half') }}"></i>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider {{ $approvalStatus === 'approved' ? 'text-emerald-900' : ($approvalStatus === 'rejected' ? 'text-rose-900' : 'text-amber-900') }}">
              {{ ucfirst($approvalStatus) }}
            </h4>
            <p class="text-[11px] {{ $approvalStatus === 'approved' ? 'text-emerald-700' : ($approvalStatus === 'rejected' ? 'text-rose-700' : 'text-amber-700') }}">
              @if($approvalStatus === 'approved')
                This listing meets all marketplace quality, serial verification, and authenticity requirements.
              @elseif($approvalStatus === 'rejected')
                This listing was flagged during compliance review. Please update inaccurate specs or photos.
              @else
                Listing is pending administrative review. It remains visible according to your published status.
              @endif
            </p>
          </div>
        </div>
      </div>

      <!-- BUYER REPORTS LOG -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-flag text-rose-500"></i>
            <span>Buyer Reports &amp; Inquiries</span>
          </h2>
          <span class="text-xs font-bold text-slate-400">
            {{ $listing->reports->count() }} Report(s)
          </span>
        </div>

        @if($listing->reports->isEmpty())
          <div class="p-8 rounded-2xl bg-emerald-50/40 border border-emerald-100 text-center space-y-2">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-base">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="text-xs font-extrabold text-emerald-900">Zero Incident Reports</h3>
            <p class="text-[11px] text-emerald-700 max-w-sm mx-auto">
              No buyers have lodged reports against this listing. Continue maintaining transparent condition descriptions.
            </p>
          </div>
        @else
          <div class="space-y-3">
            @foreach($listing->reports as $rep)
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] uppercase">
                      {{ $rep->title ?: 'Issue Lodged' }}
                    </span>
                    <span class="text-xs font-bold text-slate-600">
                      Reported by {{ $rep->user?->name ?? 'Anonymous Buyer' }}
                    </span>
                  </div>
                  <span class="text-[11px] text-slate-400">
                    {{ $rep->created_at->format('M d, Y · h:i A') }}
                  </span>
                </div>
                <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100 leading-relaxed">
                  "{{ $rep->description ?: 'No additional notes provided.' }}"
                </p>
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>
  @endif

  <!-- ============================================================== -->
  <!-- TAB 5: HARDWARE & ITEM SPECS                                   -->
  <!-- ============================================================== -->
  @if($activeTab === 'specs')
    <div class="space-y-6">

      @if($listing->item)
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-soft space-y-6">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-microchip text-pp-600"></i>
                <span>Hardware Specifications — {{ $listing->item->name ?: 'Item #' . $listing->item->id }}</span>
              </h2>
              <p class="text-xs text-slate-500 mt-0.5">Asset Reference #ITM-{{ str_pad($listing->item->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <a href="{{ route('item.view', $listing->item->id) }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 font-extrabold text-xs text-slate-700 transition">
              Inspect in Inventory &rarr;
            </a>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Brand</span>
              <span class="text-xs font-bold text-slate-800">{{ $listing->item->deviceModel?->brand?->name ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Model</span>
              <span class="text-xs font-bold text-slate-800">{{ $listing->item->deviceModel?->name ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Category</span>
              <span class="text-xs font-bold text-slate-800">{{ $listing->item->deviceModel?->category?->name ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Condition Status</span>
              <span class="text-xs font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $listing->item->condition_status ?? 'used')) }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Manufacturing Year</span>
              <span class="text-xs font-bold text-slate-800">{{ $listing->item->year ?: 'Not specified' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Serial / IMEI</span>
              <span class="text-xs font-bold text-slate-800 font-mono">{{ $listing->item->serial_number ?: 'Not Recorded' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Warehouse Location</span>
              <span class="text-xs font-bold text-slate-800">{{ $listing->item->location?->name ?? 'Default' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Asset Type</span>
              <span class="text-xs font-bold text-slate-800 uppercase">{{ $listing->item->item_type }}</span>
            </div>
          </div>

          @if($listing->item->description)
            <div class="space-y-1 pt-2">
              <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Item Description</span>
              <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100 whitespace-pre-line">
                {{ $listing->item->description }}
              </p>
            </div>
          @endif
        </div>
      @endif

    </div>
  @endif

  <!-- QUICK EDIT MODAL -->
  @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-100">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-pen text-pp-600"></i>
            <span>Edit Listing #LST-{{ str_pad($listing->id, 5, '0', STR_PAD_LEFT) }}</span>
          </h3>
          <button
            type="button"
            wire:click="closeEditModal"
            class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- Modal Form -->
        <form wire:submit="updateListing" class="space-y-4">
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Price -->
            <div class="space-y-1">
              <label class="text-xs font-extrabold text-slate-700">Listing Price (₦) <span class="text-rose-500">*</span></label>
              <input
                type="number"
                step="0.01"
                min="0"
                wire:model="editPrice"
                class="w-full text-xs font-bold rounded-xl border border-slate-200 px-3 py-2.5 focus:border-pp-600 focus:outline-none"
              />
              @error('editPrice') <span class="text-[11px] text-rose-500 font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Total Quantity -->
            <div class="space-y-1">
              <label class="text-xs font-extrabold text-slate-700">Total Quantity <span class="text-rose-500">*</span></label>
              <input
                type="number"
                min="0"
                wire:model="editQuantity"
                class="w-full text-xs font-bold rounded-xl border border-slate-200 px-3 py-2.5 focus:border-pp-600 focus:outline-none"
              />
              @error('editQuantity') <span class="text-[11px] text-rose-500 font-bold">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
            <!-- Negotiable Checkbox -->
            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-slate-50 border border-slate-200/60">
              <input
                type="checkbox"
                wire:model="editIsNegotiable"
                class="rounded border-slate-300 text-pp-600 focus:ring-pp-500"
              />
              <span class="text-xs font-bold text-slate-800">Allow Price Offers</span>
            </label>

            <!-- Allow Shipping Checkbox -->
            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-slate-50 border border-slate-200/60">
              <input
                type="checkbox"
                wire:model="editAllowShipping"
                class="rounded border-slate-300 text-pp-600 focus:ring-pp-500"
              />
              <span class="text-xs font-bold text-slate-800">Support Shipping</span>
            </label>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Warranty Days -->
            <div class="space-y-1">
              <label class="text-xs font-extrabold text-slate-700">Warranty Days</label>
              <input
                type="number"
                min="0"
                wire:model="editWarrantyPeriodDays"
                class="w-full text-xs font-bold rounded-xl border border-slate-200 px-3 py-2.5 focus:border-pp-600 focus:outline-none"
                placeholder="e.g. 30"
              />
            </div>

            <!-- Warranty Negotiable -->
            <div class="flex items-end pb-1">
              <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-slate-50 border border-slate-200/60 w-full">
                <input
                  type="checkbox"
                  wire:model="editIsWarrantyNegotiable"
                  class="rounded border-slate-300 text-pp-600 focus:ring-pp-500"
                />
                <span class="text-xs font-bold text-slate-800">Warranty Negotiable</span>
              </label>
            </div>
          </div>

          <!-- Warranty Terms -->
          <div class="space-y-1">
            <label class="text-xs font-extrabold text-slate-700">Warranty Terms &amp; Conditions</label>
            <textarea
              rows="3"
              wire:model="editWarrantyTerms"
              class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-pp-600 focus:outline-none"
              placeholder="Specify warranty coverage, return policies, or exclusions..."
            ></textarea>
          </div>

          <!-- Publish State -->
          <div class="pt-1">
            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-slate-50 border border-slate-200/60">
              <input
                type="checkbox"
                wire:model="is_published"
                class="rounded border-slate-300 text-pp-600 focus:ring-pp-500"
              />
              <span class="text-xs font-bold text-slate-800">Published to Marketplace Search &amp; Catalog</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button
              type="button"
              wire:click="closeEditModal"
              class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2"
            >
              <i class="fas fa-check text-xs"></i>
              <span>Save Changes</span>
            </button>
          </div>

        </form>

      </div>
    </div>
  @endif

</div>