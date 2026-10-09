@push('styles')
<style>
.sticky-buy { position: sticky; top: 88px; }
@media (max-width: 1023px) { .sticky-buy { position: static; } }
</style>
@endpush

@php
  $item = $listing->item;
  $model = $item?->deviceModel;
  $brand = $model?->brand;
  $category = $model?->category;
  $parentCategory = $category?->parent;
  $seller = $listing->seller;
  $location = $listing->location;

  $firstMedia = $allMedia[0] ?? null;
  $initialMediaUrl = $firstMedia?->url ?? ($listing->firstMediaUrl('images') ?: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=90');
  $initialMediaType = ($firstMedia && $firstMedia->is_video) ? 'video' : 'image';

  $starDist = $listing->starDistribution();
@endphp

<main class="max-w-[1440px] mx-auto px-4 lg:px-7" 
      x-data="{ 
        activeMediaUrl: '{{ $initialMediaUrl }}', 
        activeMediaType: '{{ $initialMediaType }}',
        activeThumbIndex: 0
      }">

  <!-- TOP BREADCRUMBS -->
  <div class="py-4 text-[11px] text-slate-500 flex gap-2 overflow-x-auto whitespace-nowrap">
    <a href="{{ route('welcome') }}" class="hover:text-slate-900">Home</a> › 
    @if($parentCategory)
      <a href="{{ route('category') }}?cat={{ $parentCategory->slug }}" class="hover:text-slate-900">{{ $parentCategory->name }}</a> › 
    @endif
    @if($category)
      <a href="{{ route('category') }}?cat={{ $category->slug }}" class="hover:text-slate-900">{{ $category->name }}</a> › 
    @endif
    @if($brand)
      <a href="{{ route('category') }}?cat={{ $category?->slug }}&brand={{ $brand->slug }}" class="hover:text-slate-900">{{ $brand->name }}</a> › 
    @endif
    @if($model)
      <span>{{ $model->name }}</span> › 
    @endif
    <b class="text-slate-900 truncate max-w-[200px]">{{ $item?->name ?? 'Listing Details' }}</b>
  </div>

  @if(session()->has('cart_success'))
    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-xl flex items-center gap-2 shadow-2xs">
      <i class="fas fa-check-circle text-emerald-600"></i>
      <span>{{ session('cart_success') }}</span>
      <a href="{{ route('cart') }}" class="underline ml-auto font-extrabold hover:text-emerald-950">View Cart →</a>
    </div>
  @endif

  @if(session()->has('wishlist_message'))
    <div class="mb-4 p-3 bg-pp-50 border border-pp-200 text-pp-800 text-xs font-bold rounded-xl flex items-center gap-2 shadow-2xs">
      <i class="fas fa-heart text-pp-600"></i>
      <span>{{ session('wishlist_message') }}</span>
      <a href="{{ route('wishlists') }}" class="underline ml-auto font-extrabold hover:text-pp-950">View Wishlist →</a>
    </div>
  @endif

  @if(Auth::check() && Auth::id() === $listing->user_id)
    <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-pp-50 via-white to-pp-50/70 border border-pp-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-pp-600 text-white grid place-items-center text-sm shrink-0 shadow-xs">
          <i class="fas fa-bullhorn"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs font-black text-slate-900">Owner Advertising &amp; Promotion Rates</span>
            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-pp-100 text-pp-800 uppercase">{{ $activeCountry->name ?? 'Nigeria' }}</span>
          </div>
          <p class="text-[11px] text-slate-600 mt-0.5">
            Drive targeted buyers to this listing at <strong>{{ $activeCountry->currency_symbol }}{{ number_format($activeCountry->clicks, 2) }} / click</strong> (CPC) or <strong>{{ $activeCountry->currency_symbol }}{{ number_format($activeCountry->views, 4) }} / view</strong> (Impressions).
          </p>
        </div>
      </div>
      <a href="{{ route('mylisting.view', $listing->id) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 shrink-0 self-start sm:self-auto cursor-pointer">
        <i class="fas fa-bolt text-amber-300"></i>
        <span>Promote in Seller Portal →</span>
      </a>
    </div>
  @endif

  <section class="grid lg:grid-cols-[1.7fr_.95fr_.8fr] gap-6">

    <!-- MEDIA GALLERY SECTION -->
    <div>
      <div class="grid grid-cols-[62px_1fr] gap-4">
        <!-- THUMBNAILS COLUMN -->
        <div class="space-y-3" id="thumbs">
          @if(!empty($allMedia))
            @foreach($allMedia as $index => $media)
              <button type="button" 
                      @click="activeMediaUrl = '{{ $media->url }}'; activeMediaType = '{{ $media->is_video ? 'video' : 'image' }}'; activeThumbIndex = {{ $index }}"
                      :class="activeThumbIndex === {{ $index }} ? 'border-2 border-pp-500 shadow-xs' : 'border border-slate-200 hover:border-slate-400'"
                      class="thumb relative w-[62px] h-[62px] rounded-lg p-1 bg-slate-900 overflow-hidden cursor-pointer transition">
                @if($media->is_video)
                  <div class="w-full h-full bg-slate-900 grid place-items-center text-white rounded">
                    <i class="fas fa-play text-xs text-pp-400"></i>
                  </div>
                @else
                  <img class="w-full h-full object-cover rounded" src="{{ $media->url }}" alt="Thumbnail {{ $index + 1 }}" />
                @endif
                @if($media->is_video)
                  <span class="absolute bottom-1 right-1 bg-slate-950/80 text-[8px] font-bold text-white px-1 rounded">VID</span>
                @endif
              </button>
            @endforeach
          @else
            <!-- FALLBACK DEFAULT THUMB -->
            <button class="thumb w-[62px] h-[62px] rounded-lg border-2 border-pp-500 p-1 bg-slate-50 grid place-items-center text-2xl">
              @if($item?->item_type === 'scrap') 🛠️ @elseif($item?->item_type === 'part') ⚙️ @else 💻 @endif
            </button>
          @endif
        </div>

        <!-- MAIN MEDIA FRAME (IMAGE OR VIDEO) -->
        <div class="aspect-square max-h-[510px] rounded-xl bg-slate-100 overflow-hidden relative shadow-xs flex items-center justify-center">
          
          <!-- VIDEO PLAYER -->
          <template x-if="activeMediaType === 'video'">
            <video :src="activeMediaUrl" controls autoplay muted playsinline class="w-full h-full object-contain bg-slate-950"></video>
          </template>

          <!-- IMAGE DISPLAY -->
          <template x-if="activeMediaType !== 'video'">
            <img id="mainImage" :src="activeMediaUrl" class="w-full h-full object-contain" alt="{{ $item?->name ?? 'Listing item image' }}" />
          </template>

          <!-- WISHLIST HEART BUTTON -->
          <button type="button" 
                  wire:click="toggleWishlist" 
                  title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                  class="absolute top-4 right-4 bg-white/95 hover:bg-white w-10 h-10 rounded-full shadow-md grid place-items-center text-xl transition cursor-pointer {{ $isWishlisted ? 'text-rose-600' : 'text-slate-400 hover:text-rose-600' }}">
            {{ $isWishlisted ? '♥' : '♡' }}
          </button>
        </div>
      </div>
      <p class="text-center text-[11px] text-slate-500 py-4">◉ Images &amp; videos represent the verified physical inventory asset.</p>
    </div>

    <!-- MIDDLE COLUMN: TITLE, PRICE & CORE ACTIONS -->
    <div>
      <!-- ITEM TYPE BADGE -->
      <div class="flex items-center gap-2 mb-3">
        @if($item?->item_type === 'scrap')
          <b class="bg-amber-600 text-white px-2.5 py-1 rounded-md text-[10px] uppercase font-extrabold tracking-wider">🛠️ SCRAP / SALVAGE</b>
        @elseif($item?->item_type === 'part')
          <b class="bg-emerald-600 text-white px-2.5 py-1 rounded-md text-[10px] uppercase font-extrabold tracking-wider">⚙️ SPARE PART</b>
        @else
          <b class="bg-slate-900 text-white px-2.5 py-1 rounded-md text-[10px] uppercase font-extrabold tracking-wider">💻 COMPLETE DEVICE</b>
        @endif
      </div>

      <!-- TITLE -->
      <h1 class="text-2xl font-extrabold leading-tight text-slate-950">
        {{ $item?->name ?? ($model?->name ?? 'Complete Device Unit') }}
      </h1>

      <!-- REVIEWS & SOLD BAR -->
      <div class="mt-3 text-xs flex items-center gap-2 text-slate-600">
        <b class="text-slate-900">{{ number_format($listing->averageRating(), 1) }}</b> 
        <span class="text-amber-400">★★★★★</span> 
        <a href="#details-tabs" onclick="switchDetailTab('reviews')" class="underline cursor-pointer hover:text-pp-600">({{ $listing->reviewsCount() }} reviews)</a> 
        <span class="text-slate-300">|</span> 
        <span class="font-bold text-slate-700">{{ $soldCount }} sold</span>
      </div>

      <!-- PRICE & NEGOTIABLE -->
      <div class="text-3xl font-black mt-5 text-slate-950">₦{{ number_format($listing->price) }}</div>
      @if($listing->is_negotiable)
        <div class="text-[11px] text-slate-500 mt-0.5">◉ Negotiable</div>
      @endif

      <!-- AVAILABILITY, LOCATION, POSTED -->
      <div class="mt-4 space-y-2 text-xs">
        <p><b>Availability:</b> 
          @if($listing->isAvailable())
            <span class="text-emerald-600 font-bold">{{ $listing->availableQuantity() }} available</span>
          @elseif($listing->availableQuantity() <= 0)
            <span class="text-rose-600 font-bold">Sold Out</span>
          @elseif(!$listing->latestModeration || $listing->latestModeration->status === 'pending')
            <span class="text-amber-600 font-bold">Pending Approval</span>
          @elseif($listing->latestModeration?->status === 'rejected')
            <span class="text-rose-600 font-bold">Rejected</span>
          @else
            <span class="text-slate-500 font-bold">Unavailable</span>
          @endif
        </p>
        <p><b>Location:</b> {{ $location?->city ?? 'Computer Village' }}, {{ $location?->state ?? 'Lagos' }} &nbsp;<span class="text-pp-600 font-bold cursor-pointer">View on map</span></p>
        <p><b>Posted:</b> {{ $listing->created_at->diffForHumans() }}</p>
      </div>

      <!-- CONDITION STATUS & NOTES CALLOUT -->
      <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs">
        <b>Condition: <span class="capitalize font-extrabold {{ in_array($item?->condition_status, ['faulty', 'scrap', 'damaged']) ? 'text-amber-700' : 'text-slate-900' }}">{{ str_replace('_', ' ', $item?->condition_status ?? 'Used') }}</span></b>
        <p class="text-slate-600 mt-1 leading-relaxed">
          {{ $item?->condition_notes ?: ($item?->description ?: 'Verified item in working order with standard specifications.') }}
        </p>
      </div>

      @php
        $canMakeOffer = (bool) ($listing->is_negotiable || $listing->is_warranty_negotiable || $listing->allow_shipping);
      @endphp

      <!-- PRIMARY ACTION BUTTONS -->
      <div class="mt-4 grid {{ $canMakeOffer ? 'grid-cols-2' : 'grid-cols-1' }} gap-2">
        <button type="button" 
                wire:click="addToCart" 
                class="h-11 border-2 border-pp-600 text-pp-700 hover:bg-pp-50 rounded-lg font-bold text-sm transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
          <span>🛒 Add to Cart</span>
        </button>

        @if($canMakeOffer)
          @auth
            <button type="button" 
                    @click="$dispatch('open-listing-offer', { listing_id: {{ $listing->id }}, seller_id: {{ $seller->id }}, seller_name: '{{ addslashes($seller->business_name ?? $seller->name) }}' })" 
                    class="h-11 bg-pp-600 text-white hover:bg-pp-700 rounded-lg font-bold text-sm transition cursor-pointer shadow-2xs">
              Make an Offer
            </button>
          @else
            <a href="{{ route('login') }}" 
               class="h-11 bg-pp-600 text-white hover:bg-pp-700 rounded-lg font-bold text-sm transition cursor-pointer shadow-2xs flex items-center justify-center text-center">
              Make an Offer
            </a>
          @endauth
        @endif
      </div>

      @if (session('cart_success'))
        <div class="mt-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center justify-between shadow-2xs animate-in fade-in duration-200">
          <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span>{{ session('cart_success') }}</span>
          </div>
          <a href="{{ route('cart') }}" class="text-pp-700 hover:text-pp-900 font-extrabold underline flex items-center gap-1">
            <span>View Cart</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
          </a>
        </div>
      @endif

      <!-- OFFER ON SPECIFIC COMPONENT (SCRAP ONLY WITH AVAILABLE CHILDREN) -->
      @if($item?->item_type === 'scrap' && $item?->children->filter(fn ($child) => $child->isAvailable())->isNotEmpty())
        <button type="button" 
                @click="$dispatch('open-listing-offer', { listing_id: {{ $listing->id }}, seller_id: {{ $seller->id }}, seller_name: '{{ addslashes($seller->business_name ?? $seller->name) }}', component_mode: true })" 
                class="w-full h-11 mt-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-bold text-xs transition cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
          <i class="fas fa-microchip text-pp-400"></i>
          <span>Offer on Specific Component (e.g. Board only)</span>
        </button>
      @endif

      <!-- MESSAGE / CHAT WITH SELLER -->
      <button type="button" 
              @click="$dispatch('open-conversation', { id: {{ $seller->id }} })" 
              class="w-full h-11 mt-2 border border-slate-200 rounded-lg font-bold text-xs hover:bg-slate-50 transition cursor-pointer flex items-center justify-center gap-2 shadow-2xs text-slate-800">
        <i class="fas fa-comment-dots text-pp-600"></i>
        <span>Message / Chat with Seller</span>
      </button>

      @if (session('report_success'))
        <div class="mt-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center gap-2">
          <i class="fas fa-check-circle text-emerald-600"></i>
          <span>{{ session('report_success') }}</span>
        </div>
      @endif

      <!-- WISHLIST & REPORT BUTTONS -->
      <div class="grid grid-cols-2 gap-2 mt-2">
        <button type="button" 
                wire:click="toggleWishlist" 
                class="h-11 border border-slate-200 rounded-lg font-bold text-xs hover:bg-slate-50 transition cursor-pointer shadow-2xs text-slate-700 flex items-center justify-center gap-1.5">
          <i class="{{ $isWishlisted ? 'fas fa-heart text-rose-500' : 'far fa-heart text-slate-400' }}"></i>
          <span>{{ $isWishlisted ? 'Saved in Wishlist' : 'Save to Wishlist' }}</span>
        </button>

        <button type="button" 
                wire:click="openReportModal" 
                class="h-11 border {{ $isReported ? 'border-amber-300 bg-amber-50 text-amber-800' : 'border-slate-200 text-slate-600 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' }} rounded-lg font-bold text-xs transition cursor-pointer shadow-2xs flex items-center justify-center gap-1.5"
                title="{{ $isReported ? 'View your submitted report' : 'Report issues with this listing' }}">
          <i class="fas {{ $isReported ? 'fa-flag-checkered text-amber-600' : 'fa-flag text-rose-500' }} text-xs"></i>
          <span>{{ $isReported ? 'Reported' : 'Report Listing' }}</span>
        </button>
      </div>

      <!-- WARRANTY & TRUST BANNER -->
      <div class="mt-4 p-4 bg-pp-50 border border-pp-100 rounded-xl text-xs space-y-1">
        <b class="text-pp-800 flex items-center gap-1.5">
          <span>🛡️</span>
          <span>{{ $listing->warranty_period_days ? $listing->warranty_period_days . ' Days Warranty' : 'Buy with Confidence' }}</span>
        </b>
        <p class="text-slate-600 leading-relaxed">
          {{ $listing->warranty_terms ?: 'All payments are escrow-protected by Parts & Parcel until you inspect and approve your delivery.' }}
        </p>
      </div>
    </div>

    <!-- RIGHT SIDEBAR: SELLER CARD & FULFILLMENT -->
    <aside class="space-y-3 sticky-buy">
      <!-- SELLER CARD (SELLER REVIEW REMOVED PER DIRECTIVE) -->
      <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-2xs">
        <h2 class="font-bold text-sm text-slate-900">Seller Information</h2>
        <div class="flex gap-3 mt-3">
          <a href="{{ route('user.profile', $seller->id) }}" class="w-11 h-11 rounded-full bg-pp-100 hover:bg-pp-200 text-pp-700 grid place-items-center font-bold text-sm shrink-0 transition">
            {{ strtoupper(substr($seller->business_name ?? $seller->name, 0, 2)) }}
          </a>
          <div class="overflow-hidden">
            <a href="{{ route('user.profile', $seller->id) }}" class="text-slate-900 hover:text-pp-600 font-extrabold text-xs block truncate transition">{{ $seller->business_name ?? $seller->name }}</a>
            @if($seller->is_verified)
              <p class="text-[10px] text-pp-600 font-bold flex items-center gap-1">
                <span>✓ Verified Seller</span>
              </p>
            @endif
            <p class="text-[10px] text-slate-500">Member since {{ $seller->created_at->format('M Y') }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 border-y border-slate-100 my-3 py-3 text-center text-[10px]">
          <div>
            <b class="text-sm font-extrabold text-slate-900">{{ $seller->listings()->where('is_published', true)->where('is_active', true)->count() }}</b>
            <span class="block font-medium text-slate-500">Active Listings</span>
          </div>
          <div>
            <b class="text-sm font-extrabold text-slate-900">{{ $soldCount }}</b>
            <span class="block font-medium text-slate-500">Completed Sales</span>
          </div>
        </div>

        <a href="{{ route('user.profile', $seller->id) }}" class="w-full block text-center border border-slate-200 hover:bg-slate-50 rounded-lg py-2 text-xs font-bold text-slate-700 transition cursor-pointer">
          View Seller Profile &amp; Catalog
        </a>
      </div>
      
      <!-- FULFILLMENT & DELIVERY -->
      <div class="border border-slate-200 rounded-xl p-4 text-[11px] bg-white shadow-2xs">
        <b class="text-slate-900 font-bold">Fulfillment &amp; Delivery Options</b>
        <div class="mt-3 space-y-3 text-slate-700">
          <p>
            📍 <b>Buyer pickup</b><br>
            <span class="ml-5 text-slate-500">Collect directly from seller workshop/store at {{ $location?->city ?? 'Computer Village' }}.</span>
          </p>
          @if($listing->allow_shipping)
            <p>
              🚚 <b>Seller delivery / Escrow courier</b><br>
              <span class="ml-5 text-slate-500">Shipped with inspection warranty before release of payment.</span>
            </p>
          @else
            <p class="text-amber-800 bg-amber-50 border border-amber-200/60 rounded-lg p-2 text-[10px]">
              <i class="fas fa-info-circle mr-1"></i> Local pickup only. Seller delivery / shipping is not offered for this listing.
            </p>
          @endif
        </div>
      </div>

      <!-- SHARE THIS LISTING -->
      <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-2xs">
        <b class="text-sm text-slate-900 font-bold">Share this listing</b>
        <div class="flex items-center gap-2 mt-3">
          <a href="https://api.whatsapp.com/send?text={{ urlencode(url()->current()) }}" target="_blank" title="Share on WhatsApp" class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 grid place-items-center hover:bg-emerald-600 hover:text-white transition shadow-2xs">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.156 4.225 4.399-1.155z"/></svg>
          </a>
          <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank" title="Share on X" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-800 border border-slate-200 grid place-items-center hover:bg-slate-900 hover:text-white transition shadow-2xs">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <button title="Copy Link" onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!')" class="w-9 h-9 rounded-xl bg-pp-50 text-pp-600 border border-pp-200 grid place-items-center hover:bg-pp-600 hover:text-white transition shadow-2xs cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
          </button>
        </div>
      </div>

      <!-- SAFETY TIP -->
      <div class="bg-pp-50 border border-pp-100 rounded-xl p-4 text-[11px]">
        <b class="text-pp-700 font-bold flex items-center gap-1.5">
          <svg class="w-4 h-4 text-pp-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Safety Tip</span>
        </b>
        <p class="text-slate-500 mt-1 leading-relaxed">Meet in verified workshop locations when picking up items. Use Parts &amp; Parcel escrow payment for full protection.</p>
      </div>
    </aside>
  </section>

  <!-- TABBED DETAILS SECTION -->
  <section id="details-tabs" class="mt-10">
    <!-- TAB NAVIGATION BAR -->
    <div class="border-b border-slate-200">
      <nav class="flex space-x-2 sm:space-x-6 overflow-x-auto whitespace-nowrap" aria-label="Details Tabs">
        <!-- TAB 1: DESCRIPTION & SPECS -->
        <button id="tab-desc-btn" onclick="switchDetailTab('desc')" class="detail-tab-btn py-3.5 px-4 font-bold text-xs sm:text-sm border-b-2 border-pp-600 text-pp-600 bg-pp-50/60 rounded-t-xl transition cursor-pointer flex items-center gap-2">
          <span>📝</span>
          <span>Description &amp; Specifications</span>
        </button>

        <!-- TAB 2: COMPONENT STATUS (SCRAP ONLY PER DIRECTIVE) -->
        @if($item?->item_type === 'scrap')
          <button id="tab-components-btn" onclick="switchDetailTab('components')" class="detail-tab-btn py-3.5 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-900 rounded-t-xl transition cursor-pointer flex items-center gap-2">
            <span>🛠️</span>
            <span>Component Status</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
              {{ $item?->children->count() }} parts
            </span>
          </button>
        @endif

        <!-- TAB 3: RELATED DISCUSSIONS -->
        <button id="tab-discussions-btn" onclick="switchDetailTab('discussions')" class="detail-tab-btn py-3.5 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-900 rounded-t-xl transition cursor-pointer flex items-center gap-2">
          <span>💬</span>
          <span>Related Discussions</span>
          <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">
            {{ $relatedDiscussions->count() }}
          </span>
        </button>

        <!-- TAB 4: REVIEWS -->
        <button id="tab-reviews-btn" onclick="switchDetailTab('reviews')" class="detail-tab-btn py-3.5 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-900 rounded-t-xl transition cursor-pointer flex items-center gap-2">
          <span>★</span>
          <span>Reviews</span>
          <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
            {{ $listing->reviewsCount() }}
          </span>
        </button>
      </nav>
    </div>

    <!-- TAB PANELS CONTAINER -->
    <div class="mt-6">
      
      <!-- TAB 1: DESCRIPTION & CATALOG SPECIFICATIONS -->
      <div id="panel-desc" class="detail-panel">
        <div class="grid lg:grid-cols-2 gap-6">
          
          <!-- LEFT COLUMN: DESCRIPTION -->
          <article class="border border-slate-200 rounded-xl p-5 h-full flex flex-col justify-between bg-white shadow-2xs">
            <div>
              <h2 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <span>📝</span>
                <span>Device Description</span>
              </h2>
              <div class="text-[11px] leading-relaxed text-slate-600 mt-3 whitespace-pre-line">
                {{ $listing->description ?: ($item?->description ?: 'No detailed overview description was provided by the seller.') }}
              </div>

              @if($item?->condition_notes)
                <div class="mt-4 pt-3 border-t border-slate-100">
                  <h3 class="text-xs font-bold text-slate-900">Condition &amp; Inspection Notes</h3>
                  <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $item->condition_notes }}</p>
                </div>
              @endif
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 text-[10px] text-slate-500 space-y-1">
              <p><b>Catalog ID:</b> #LST-{{ str_pad($listing->id, 5, '0', STR_PAD_LEFT) }}</p>
              <p><b>Condition:</b> {{ ucfirst(str_replace('_', ' ', $item?->condition_status ?? 'Used')) }}</p>
              <p><b>Warranty:</b> {{ $listing->warranty_period_days ? $listing->warranty_period_days . ' Days Testing Warranty' : 'Sold As-Is' }}</p>
            </div>
          </article>

          <!-- RIGHT COLUMN: SPECIFICATIONS (FROM CATALOG & SELLER DATA) -->
          <article class="border border-slate-200 rounded-xl p-5 h-full bg-white shadow-2xs">
            <h2 class="font-bold text-sm text-slate-900 flex items-center gap-2">
              <span>⚙️</span>
              <span>Catalog Specifications</span>
            </h2>
            <div class="mt-3 text-[11px] divide-y divide-slate-100 text-slate-700">
              <p class="py-2.5 grid grid-cols-2"><b>Brand / Make</b><span>{{ $brand?->name ?? 'General / OEM' }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Device Model</b><span>{{ $model?->name ?? ($item?->name ?? 'Unspecified') }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Category</b><span>{{ $category?->name ?? 'Electronics' }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Item Classification</b><span class="capitalize">{{ $item?->item_type ?? 'whole' }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Condition Grade</b><span class="capitalize">{{ str_replace('_', ' ', $item?->condition_status ?? 'Used') }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Warranty Period</b><span>{{ $listing->warranty_period_days ? $listing->warranty_period_days . ' Days' : 'No Warranty (As-Is)' }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Location Verified</b><span>{{ $location?->city ?? 'Lagos' }}, {{ $location?->state ?? 'Nigeria' }}</span></p>
              <p class="py-2.5 grid grid-cols-2"><b>Listed Quantity</b><span>{{ $listing->quantity }} unit(s)</span></p>
            </div>
          </article>

        </div>
      </div>

      <!-- TAB 2: COMPONENT STATUS (SCRAP ONLY; DAMAGE DETAILS CHECKLIST REMOVED PER DIRECTIVE) -->
      @if($item?->item_type === 'scrap')
        <div id="panel-components" class="detail-panel hidden space-y-6">
          <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-2xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div>
                <h2 class="font-bold text-sm text-slate-900">Harvested Components Matrix</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Disassembled internal sub-parts and their verified testing condition.</p>
              </div>
              <span class="text-[10px] font-extrabold px-2.5 py-1 rounded bg-amber-100 text-amber-900">
                {{ $item->children->count() }} Components Registered
              </span>
            </div>

            <div class="overflow-x-auto mt-4">
              <table class="min-w-[650px] w-full text-left text-xs border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
                    <th class="p-3">Component Part</th>
                    <th class="p-3">Testing Condition</th>
                    <th class="p-3">Availability</th>
                    <th class="p-3">Technician Harvest Notes</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @forelse($item->children as $child)
                    <tr class="hover:bg-slate-50/50 transition">
                      <td class="p-3 font-bold text-slate-900">{{ $child->name }}</td>
                      <td class="p-3">
                        @if(stripos($child->condition_status, 'work') !== false)
                          <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 text-[11px]">
                            <span>🟢</span> <span>{{ $child->condition_status }}</span>
                          </span>
                        @elseif(stripos($child->condition_status, 'repair') !== false)
                          <span class="inline-flex items-center gap-1 font-semibold text-amber-700 text-[11px]">
                            <span>🟡</span> <span>{{ $child->condition_status }}</span>
                          </span>
                        @else
                          <span class="inline-flex items-center gap-1 font-semibold text-slate-600 text-[11px]">
                            <span>⚪</span> <span>{{ $child->condition_status }}</span>
                          </span>
                        @endif
                      </td>
                      <td class="p-3">
                        <span class="font-extrabold text-[11px] {{ $child->isAvailable() ? 'text-emerald-600' : 'text-slate-400' }}">
                          {{ $child->isAvailable() ? '✓ Available' : 'Sold' }}
                        </span>
                      </td>
                      <td class="p-3 text-[11px] text-slate-600">
                        {{ $child->condition_notes ?: 'Tested OK by seller' }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="p-6 text-center text-slate-500 text-xs">
                        This scrap unit is listed as a complete carcass as-is. No individual components have been harvested or disassembled.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <div class="mt-4 bg-pp-50 border border-pp-100 p-3 rounded-xl text-[11px] text-pp-800 font-semibold flex items-center gap-2">
              <i class="fas fa-tools text-pp-600"></i>
              <span>To purchase specific components separately, use the "Offer on Specific Component" button above.</span>
            </div>
          </div>
        </div>
      @endif

      <!-- TAB 3: RELATED DISCUSSIONS (SHOW 6 RECENT DISCUSSIONS PER DIRECTIVE) -->
      <div id="panel-discussions" class="detail-panel hidden">
        <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-2xs">
          <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3">
            <div>
              <h2 class="font-bold text-sm text-slate-900">Related Discussions ({{ $relatedDiscussions->count() }})</h2>
              <p class="text-[11px] text-slate-500 mt-0.5">Technicians and buyers discussing parts, sourcing, or repairs related to this model.</p>
            </div>
            <a href="{{ route('community') }}" class="text-[11px] text-pp-600 font-bold hover:underline whitespace-nowrap">
              View all discussions →
            </a>
          </div>

          <!-- DISCUSSIONS GRID -->
          <div class="mt-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
              @forelse($relatedDiscussions as $discussion)
                <article class="border border-slate-200 rounded-xl p-4 hover:border-pp-300 transition bg-white flex flex-col justify-between">
                  <div>
                    <div class="flex items-center justify-between gap-2">
                      <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase {{ $discussion->type === 'service' ? 'bg-amber-100 text-amber-900' : 'bg-pp-50 text-pp-700' }}">
                        {{ $discussion->type === 'service' ? 'SERVICE' : ($discussion->type === 'advice' ? 'ADVICE' : 'DEVICE') }}
                      </span>
                      <span class="text-[9px] text-slate-400 font-semibold">{{ $discussion->responses_count }} responses</span>
                    </div>
                    <h3 class="font-bold text-xs mt-2 text-slate-900 line-clamp-2">
                      <a href="{{ route('community.request', ['id' => $discussion->id]) }}" class="hover:text-pp-600">
                        {{ $discussion->title }}
                      </a>
                    </h3>
                    <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $discussion->body }}</p>
                  </div>
                  <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[9px] text-slate-400">
                    <span>{{ $discussion->user->name ?? 'Community Member' }}</span>
                    <span>{{ $discussion->created_at->diffForHumans() }}</span>
                  </div>
                </article>
              @empty
                <div class="col-span-3 p-8 text-center text-slate-500 text-xs">
                  No community discussions yet for this model. Start the first conversation!
                </div>
              @endforelse
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3 border border-slate-200">
            <p class="text-[10px] text-slate-500">Need specific parts or diagnostic assistance? Ask the community technicians directly.</p>
            <a href="{{ route('community') }}" class="ml-3 shrink-0 bg-white border border-pp-200 text-pp-700 rounded-lg px-3 py-2 text-[10px] font-bold hover:bg-pp-50 transition cursor-pointer">
              Start a Discussion
            </a>
          </div>
        </div>
      </div>

      <!-- TAB 4: REVIEWS (SHOW 6 RECENT LISTING REVIEWS & BAR BREAKDOWN) -->
      <div id="panel-reviews" class="detail-panel hidden">
        <div id="reviews" class="border border-slate-200 rounded-xl p-5 bg-white shadow-2xs">
          <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h2 class="font-bold text-sm text-slate-900">Customer Reviews ({{ $listing->reviewsCount() }})</h2>
            <span class="text-[11px] text-slate-500">Verified Marketplace Orders</span>
          </div>
          
          <div class="mt-5 grid lg:grid-cols-[180px_220px_1fr] gap-6">
            <!-- RATING SCORE -->
            <div>
              <div class="text-5xl font-black text-slate-900">{{ number_format($listing->averageRating(), 1) }}</div>
              <div class="text-amber-400 text-xl mt-1">★★★★★</div>
              <span class="text-[11px] text-slate-500">{{ $listing->reviewsCount() }} total reviews</span>
            </div>

            <!-- STAR PERCENTAGE DISTRIBUTION -->
            <div class="space-y-2 text-[10px] text-slate-700">
              @for($star = 5; $star >= 1; $star--)
                <div class="flex items-center gap-2">
                  <b class="w-5 text-right">{{ $star }} ★</b>
                  <div class="h-2 flex-1 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-2 bg-amber-400 rounded-full transition-all duration-300" style="width: {{ $starDist[$star]['percentage'] }}%"></div>
                  </div>
                  <span class="w-7 text-right text-slate-500">{{ $starDist[$star]['count'] }}</span>
                </div>
              @endfor
            </div>

            <!-- REVIEWS LIST (SHOW SIX RECENT REVIEWS) -->
            <div class="max-h-[420px] overflow-y-auto custom-scrollbar p-1">
              <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($reviews as $rev)
                  <article class="border border-slate-200 rounded-xl p-4 text-[10px] bg-white flex flex-col justify-between">
                    <div>
                      <div class="flex items-center justify-between">
                        <b class="text-slate-900">{{ $rev->user->name ?? 'Verified Buyer' }}</b>
                        <span class="text-amber-400">
                          {{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}
                        </span>
                      </div>
                      <p class="text-slate-600 mt-2 leading-relaxed">{{ $rev->comment }}</p>
                    </div>
                    <p class="text-slate-400 mt-2 pt-2 border-t border-slate-100 text-[9px]">{{ $rev->created_at->diffForHumans() }}</p>
                  </article>
                @empty
                  <div class="col-span-3 p-8 text-center text-slate-500 text-xs">
                    No customer reviews have been submitted for this listing yet.
                  </div>
                @endforelse
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- SIMILAR LISTINGS SECTION -->
  <section class="mt-10 mb-10">
    <div class="flex justify-between items-center mb-4">
      <h2 class="font-bold text-sm text-slate-900">Similar Listings You Might Like</h2>
      <a href="{{ route('category') }}?cat={{ $category?->slug }}" class="text-[11px] text-pp-600 font-bold hover:underline">
        View all in {{ $category?->name ?? 'Category' }} →
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      @forelse($similarListings as $similar)
        @php
          $simItem = $similar->item;
          $simType = $simItem?->item_type ?? 'whole';
        @endphp
        <a href="{{ route('listing-details', $similar) }}" class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:shadow-card hover:-translate-y-0.5 transition block">
          <div class="h-32 bg-slate-50 grid place-items-center p-3 relative">
            <span class="absolute top-2 left-2 text-[8px] font-extrabold px-1.5 py-0.5 rounded {{ $simType === 'scrap' ? 'bg-amber-100 text-amber-800' : ($simType === 'part' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-900 text-white') }}">
              {{ strtoupper($simType) }}
            </span>
            @if($similar->firstMediaUrl('images'))
              <img src="{{ $similar->firstMediaUrl('images') }}" alt="{{ $simItem?->name ?? '' }}" class="h-full w-full object-contain" />
            @else
              <span class="text-4xl">
                @if($simType === 'scrap') 🛠️ @elseif($simType === 'part') ⚙️ @else 💻 @endif
              </span>
            @endif
          </div>
          <div class="p-3">
            <h3 class="font-bold text-[11px] text-slate-900 truncate">{{ $simItem?->name ?? 'Device Item' }}</h3>
            <strong class="block mt-1 text-xs text-slate-950">₦{{ number_format($similar->price) }}</strong>
            <p class="text-[9px] text-slate-500 mt-1 truncate">⌖ {{ $similar->location?->city ?? 'Lagos' }}</p>
          </div>
        </a>
      @empty
        <p class="text-xs text-slate-400 col-span-5 text-center py-4">No other listings available right now in this category.</p>
      @endforelse
    </div>
  </section>

</main>
 
 <!-- REPORT LISTING MODAL -->
 @if ($showReportModal)
   <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="report-modal-title" role="dialog" aria-modal="true">
     <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
       <!-- Backdrop -->
       <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closeReportModal"></div>
       <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

       <!-- Modal Card -->
       <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
         <div class="p-6">
           <div class="flex items-start gap-3">
             <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
               <i class="fas fa-flag text-base"></i>
             </div>
             <div class="min-w-0 flex-1">
               <h3 class="text-lg font-bold text-slate-950" id="report-modal-title">
                 Report This Listing
               </h3>
               <p class="text-xs text-slate-500 mt-0.5">
                 Help us keep Parts & Parcel safe. Submit a report if this listing violates marketplace standards.
               </p>
             </div>
             <button type="button" wire:click="closeReportModal" class="text-slate-400 hover:text-slate-600">
               <i class="fas fa-times"></i>
             </button>
           </div>

           <!-- Preset Reasons -->
           <div class="mt-4">
             <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
               Reason for Report <span class="text-rose-500">*</span>
             </label>
             <div class="flex flex-wrap gap-1.5">
               @foreach([
                 'Inaccurate or misleading information',
                 'Prohibited or dangerous item',
                 'Suspected counterfeit or fraud',
                 'Unresponsive seller or scam pricing',
                 'Copyright or image infringement',
                 'Other policy violation'
               ] as $reason)
                 <button
                   type="button"
                   wire:click="setPresetReportReason('{{ $reason }}')"
                   class="px-2.5 py-1 rounded-lg text-xs font-medium transition border {{ $reportTitle === $reason ? 'bg-rose-50 text-rose-700 border-rose-300 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}"
                 >
                   {{ $reason }}
                 </button>
               @endforeach
             </div>
             @error('reportTitle')
               <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
             @enderror
           </div>

           <!-- Description / Extra Details -->
           <div class="mt-4">
             <label for="reportDescription" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
               Additional Details (Optional)
             </label>
             <textarea
               id="reportDescription"
               wire:model="reportDescription"
               rows="3"
               placeholder="Please describe the issue in detail so our moderators can investigate..."
               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-xs focus:outline-hidden focus:ring-2 focus:ring-rose-500/30"
             ></textarea>
             @error('reportDescription')
               <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
             @enderror
           </div>
         </div>

         <!-- Modal Actions -->
         <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
           <button
             type="button"
             wire:click="closeReportModal"
             class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition"
           >
             Cancel
           </button>
           <button
             type="button"
             wire:click="submitReport"
             wire:loading.attr="disabled"
             class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5"
           >
             <i class="fas fa-paper-plane text-[10px]" wire:loading.remove wire:target="submitReport"></i>
             <i class="fas fa-spinner fa-spin text-[10px]" wire:loading wire:target="submitReport"></i>
             <span>Submit Report</span>
           </button>
         </div>
       </div>
     </div>
   </div>
 @endif

@push('scripts')
<script>
function switchDetailTab(tabName) {
  const tabs = ['desc', 'components', 'discussions', 'reviews'];
  tabs.forEach(t => {
    const btn = document.getElementById(`tab-${t}-btn`);
    const panel = document.getElementById(`panel-${t}`);
    if (t === tabName) {
      if (btn) {
        btn.classList.add('border-pp-600', 'text-pp-600', 'bg-pp-50/60', 'font-bold');
        btn.classList.remove('border-transparent', 'text-slate-500', 'font-semibold');
      }
      if (panel) panel.classList.remove('hidden');
    } else {
      if (btn) {
        btn.classList.remove('border-pp-600', 'text-pp-600', 'bg-pp-50/60', 'font-bold');
        btn.classList.add('border-transparent', 'text-slate-500', 'font-semibold');
      }
      if (panel) panel.classList.add('hidden');
    }
  });
}
</script>
@endpush
