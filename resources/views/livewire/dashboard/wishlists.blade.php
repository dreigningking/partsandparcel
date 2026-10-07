<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Saved Wishlist Items &amp; Parts</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track saved parts, devices, and scraped components. You'll receive instant notifications if items sell out or get restocked.</p>
    </div>

    <!-- QUICK STAT COUNTERS -->
    <div class="flex flex-wrap items-center gap-2">
      <button wire:click="$set('stockFilter', 'all')" 
              class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $stockFilter === 'all' ? 'bg-pp-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        All Saved ({{ $totalCount }})
      </button>
      <button wire:click="$set('stockFilter', 'in_stock')" 
              class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 {{ $stockFilter === 'in_stock' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' }}">
        <i class="fas fa-check-circle text-[10px]"></i> In Stock ({{ $inStockCount }})
      </button>
      <button wire:click="$set('stockFilter', 'sold_out')" 
              class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 {{ $stockFilter === 'sold_out' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}">
        <i class="fas fa-exclamation-circle text-[10px]"></i> Sold Out ({{ $soldOutCount }})
      </button>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('message'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-rose-600"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('info'))
    <div class="p-3.5 bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-info-circle text-sky-600"></i>
        <span>{{ session('info') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-sky-700 hover:text-sky-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- RESTOCK NOTIFICATION BANNER (When sold-out items exist) -->
  @if($soldOutCount > 0 && $stockFilter !== 'in_stock')
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
      <div class="flex items-start sm:items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-amber-500 text-white grid place-items-center shrink-0 shadow-xs">
          <i class="fas fa-bell"></i>
        </div>
        <div>
          <h4 class="text-xs font-extrabold text-slate-900">Restock Notifications Active</h4>
          <p class="text-[11px] text-slate-600 mt-0.5">
            You have <span class="font-extrabold text-rose-700">{{ $soldOutCount }} {{ Str::plural('item', $soldOutCount) }}</span> currently sold out. You will automatically receive in-app and email alerts the moment the seller restocks.
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        @if($stockFilter !== 'sold_out')
          <button wire:click="$set('stockFilter', 'sold_out')" 
                  class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-amber-900 hover:bg-amber-100 text-xs font-extrabold transition cursor-pointer">
            Filter Sold Out
          </button>
        @endif
        <button wire:click="clearSoldOut" 
                class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold transition shadow-2xs cursor-pointer"
                title="Remove all sold out items from wishlist">
          Clear Sold Out
        </button>
      </div>
    </div>
  @endif

  <!-- CONTROLS & FILTER TOOLBAR -->
  @if($totalCount > 0)
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-soft space-y-3">
      <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- SEARCH INPUT -->
        <div class="relative flex-1">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <i class="fas fa-search text-xs"></i>
          </div>
          <input type="text" 
                 wire:model.live.debounce.300ms="search" 
                 placeholder="Search by part title, brand, model..." 
                 class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-pp-500 focus:bg-white transition">
          @if($search)
            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
              <i class="fas fa-times-circle text-xs"></i>
            </button>
          @endif
        </div>

        <!-- FILTER DROPDOWNS -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
          <!-- ITEM TYPE -->
          <select wire:model.live="itemType" 
                  class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-700 focus:outline-none focus:border-pp-500 cursor-pointer">
            <option value="all">All Item Types</option>
            <option value="device">Devices (Laptops/Phones)</option>
            <option value="part">Functional Parts</option>
            <option value="scrap">Scrap / Donor Components</option>
          </select>

          <!-- SORT BY -->
          <select wire:model.live="sortBy" 
                  class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-700 focus:outline-none focus:border-pp-500 cursor-pointer">
            <option value="latest">Newest Added</option>
            <option value="oldest">Oldest Added</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>

          <!-- RESET FILTERS IF ACTIVE -->
          @if($search || $stockFilter !== 'all' || $itemType !== 'all' || $sortBy !== 'latest')
            <button wire:click="resetFilters" 
                    class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-extrabold transition cursor-pointer shrink-0" 
                    title="Reset all filters">
              <i class="fas fa-undo text-[10px]"></i> Reset
            </button>
          @endif
        </div>
      </div>

      <!-- BATCH ACTIONS ROW -->
      <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
        <span class="text-slate-500 font-medium">
          Showing <span class="font-extrabold text-slate-900">{{ $wishlists->count() }}</span> of <span class="font-extrabold text-slate-900">{{ $wishlists->total() }}</span> results
        </span>

        <div class="flex items-center gap-3">
          @if($inStockCount > 0)
            <button wire:click="addAllInStockToCart" 
                    wire:loading.attr="disabled"
                    class="text-xs font-extrabold text-pp-600 hover:text-pp-800 transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-cart-plus text-[11px]"></i> Add All In Stock to Cart
            </button>
          @endif

          <button wire:click="clearWishlist" 
                  wire:confirm="Are you sure you want to clear your entire wishlist?"
                  class="text-xs font-extrabold text-rose-500 hover:text-rose-700 transition flex items-center gap-1 cursor-pointer">
            <i class="fas fa-trash-alt text-[10px]"></i> Clear All
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- WISHLIST CARDS GRID -->
  @if($wishlists->isEmpty())
    @if($totalCount > 0)
      <!-- FILTERED EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center shadow-soft space-y-3 max-w-md mx-auto my-6">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 text-xl grid place-items-center mx-auto">
          <i class="fas fa-search"></i>
        </div>
        <div>
          <h2 class="text-base font-extrabold text-slate-900">No Matching Saved Items</h2>
          <p class="text-xs text-slate-500 mt-1">No wishlist items matched your search query or filter criteria.</p>
        </div>
        <div class="pt-2">
          <button wire:click="resetFilters" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
            Reset Filters
          </button>
        </div>
      </div>
    @else
      <!-- TRULY EMPTY WISHLIST -->
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-soft space-y-4 max-w-lg mx-auto my-6">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 text-2xl grid place-items-center mx-auto shadow-inner">
          <i class="fas fa-heart"></i>
        </div>
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Your Wishlist is Empty</h2>
          <p class="text-xs text-slate-500 mt-1">Save devices, components, or repair parts while browsing the marketplace. You will automatically receive alerts if saved items are running low, sell out, or are restocked!</p>
        </div>
        <div class="pt-2">
          <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition">
            <i class="fas fa-search text-xs"></i> Explore Marketplace
          </a>
        </div>
      </div>
    @endif
  @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($wishlists as $wishlist)
        @php
          $listing = $wishlist->listing;
          $item = $listing?->item;
          $seller = $listing?->seller;
          $firstMedia = $listing?->media?->first();
          $mediaUrl = $firstMedia?->url ?? ($listing?->firstMediaUrl('images') ?? null);
          $itemType = $item?->item_type ?? 'device';
          $isSoldOut = $wishlist->isSoldOut();
          $isLowStock = $wishlist->isLowStock();
          $isAvailable = $wishlist->isAvailable();
          $availableStock = $listing?->availableQuantity() ?? 0;
        @endphp

        <div class="bg-white rounded-3xl border {{ $isSoldOut ? 'border-rose-200 bg-rose-50/10' : 'border-slate-200' }} p-6 space-y-4 shadow-soft flex flex-col justify-between hover:border-pp-300 transition group">
          <div class="space-y-3">
            <!-- BADGES ROW -->
            <div class="flex items-center justify-between">
              @if($itemType === 'scrap')
                <span class="px-2.5 py-0.5 rounded-full bg-amber-600 text-white text-[9px] font-extrabold tracking-wider uppercase">🛠️ SCRAP</span>
              @elseif($itemType === 'part')
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[9px] font-extrabold tracking-wider uppercase">⚙️ PART</span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white text-[9px] font-extrabold tracking-wider uppercase">💻 DEVICE</span>
              @endif

              <!-- STOCK STATUS BADGE -->
              @if($isSoldOut)
                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold flex items-center gap-1 border border-rose-200">
                  <i class="fas fa-ban text-[9px]"></i> SOLD OUT
                </span>
              @elseif($isLowStock)
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold flex items-center gap-1 border border-amber-200">
                  <i class="fas fa-bolt text-[9px]"></i> ONLY {{ $availableStock }} LEFT
                </span>
              @elseif($isAvailable)
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold flex items-center gap-1 border border-emerald-200">
                  <i class="fas fa-check-circle text-[9px]"></i> IN STOCK ({{ $availableStock }})
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold border border-slate-200">
                  UNAVAILABLE
                </span>
              @endif
            </div>

            <!-- MEDIA OR ICON PREVIEW -->
            <div class="relative">
              @if($mediaUrl)
                <div class="w-full h-36 rounded-2xl bg-slate-100 overflow-hidden relative {{ $isSoldOut ? 'opacity-70 grayscale-50' : '' }}">
                  <img src="{{ $mediaUrl }}" alt="{{ $listing?->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
              @else
                <div class="w-full h-36 rounded-2xl bg-slate-50 border border-slate-100 grid place-items-center text-4xl {{ $isSoldOut ? 'opacity-70' : '' }}">
                  {{ $itemType === 'part' ? '⚙️' : ($itemType === 'scrap' ? '🛠️' : '💻') }}
                </div>
              @endif

              @if($isSoldOut)
                <div class="absolute inset-0 bg-slate-950/20 backdrop-blur-[1px] rounded-2xl grid place-items-center">
                  <div class="px-3 py-1 rounded-xl bg-rose-600/90 text-white text-[11px] font-extrabold shadow-md flex items-center gap-1.5">
                    <i class="fas fa-bell text-[10px]"></i> Restock Alert Active
                  </div>
                </div>
              @endif
            </div>

            <!-- LISTING DETAILS -->
            <div>
              <a href="{{ $listing ? route('listing-details', $listing->slug ?: $listing->id) : '#' }}" 
                 class="text-sm font-extrabold text-slate-900 hover:text-pp-600 transition line-clamp-1 block">
                {{ $listing?->title ?? ($item?->name ?? 'Marketplace Item') }}
              </a>

              @if($item?->deviceModel)
                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                  {{ $item->deviceModel->brand?->name }} · {{ $item->deviceModel->name }}
                </p>
              @endif

              <p class="text-xs text-pp-700 font-extrabold mt-1">₦{{ number_format($listing?->price ?? 0) }}</p>

              <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 truncate">
                <i class="fas fa-store text-slate-400 text-[10px]"></i>
                <span>Seller: {{ $seller?->business_name ?: ($seller?->name ?: 'Verified Seller') }}</span>
              </p>
            </div>
          </div>

          <!-- ACTION BUTTONS -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
            @if($isAvailable)
              <!-- BUY NOW BUTTON -->
              <button wire:click="buyNow({{ $wishlist->id }})" 
                      class="flex-1 py-2 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-2xs transition cursor-pointer">
                Buy Now →
              </button>

              <!-- MOVE TO CART BUTTON -->
              <button wire:click="moveToCart({{ $wishlist->id }})" 
                      class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs shadow-2xs transition cursor-pointer" 
                      title="Move to Cart">
                <i class="fas fa-shopping-cart text-xs"></i>
              </button>
            @else
              <!-- SOLD OUT NOTIFICATION INDICATOR -->
              <div class="flex-1 py-2 px-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-extrabold text-center flex items-center justify-center gap-1.5 select-none"
                   title="You will receive a notification when this item is restocked.">
                <i class="fas fa-bell text-[10px]"></i> Restock Alert On
              </div>
            @endif

            <!-- REMOVE BUTTON -->
            <button wire:click="removeFromWishlist({{ $wishlist->id }})" 
                    class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer" 
                    title="Remove from Wishlist">
              <i class="fas fa-trash-alt text-xs"></i>
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <!-- PAGINATION -->
    <div class="pt-4">
      {{ $wishlists->links() }}
    </div>
  @endif

</div>