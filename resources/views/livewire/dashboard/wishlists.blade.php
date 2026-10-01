<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Saved Wishlist Items &amp; Parts</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track saved parts, devices, and scraped components for upcoming purchases.</p>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-3 py-1.5 rounded-xl bg-pp-50 text-pp-700 font-extrabold text-xs border border-pp-200">
        {{ $wishlists->total() }} Saved {{ Str::plural('Item', $wishlists->total()) }}
      </span>
    </div>
  </div>

  @if (session()->has('message'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- WISHLIST CARDS GRID -->
  @if($wishlists->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-soft space-y-4 max-w-lg mx-auto my-6">
      <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 text-2xl grid place-items-center mx-auto shadow-inner">
        <i class="fas fa-heart"></i>
      </div>
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">Your Wishlist is Empty</h2>
        <p class="text-xs text-slate-500 mt-1">Save devices, components, or repair parts while browsing the marketplace to track prices and buy later.</p>
      </div>
      <div class="pt-2">
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs shadow-xs transition">
          <i class="fas fa-search text-xs"></i> Explore Marketplace
        </a>
      </div>
    </div>
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
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between hover:border-pp-200 transition">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              @if($itemType === 'scrap')
                <span class="px-2.5 py-0.5 rounded-full bg-amber-600 text-white text-[9px] font-extrabold tracking-wider uppercase">🛠️ SCRAP</span>
              @elseif($itemType === 'part')
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[9px] font-extrabold tracking-wider uppercase">⚙️ PART</span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white text-[9px] font-extrabold tracking-wider uppercase">💻 DEVICE</span>
              @endif

              <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
                {{ $listing?->availableQuantity() > 0 ? 'IN STOCK' : 'CHECK WITH SELLER' }}
              </span>
            </div>

            <!-- MEDIA OR ICON PREVIEW -->
            @if($mediaUrl)
              <div class="w-full h-36 rounded-2xl bg-slate-100 overflow-hidden relative">
                <img src="{{ $mediaUrl }}" alt="{{ $listing?->title }}" class="w-full h-full object-cover">
              </div>
            @else
              <div class="w-full h-36 rounded-2xl bg-slate-50 border border-slate-100 grid place-items-center text-4xl">
                {{ $itemType === 'part' ? '⚙️' : ($itemType === 'scrap' ? '🛠️' : '💻') }}
              </div>
            @endif

            <div>
              <a href="{{ $listing ? route('listing-details', $listing->id) : '#' }}" class="text-sm font-extrabold text-slate-900 hover:text-pp-600 transition line-clamp-1 block">
                {{ $listing?->title ?? ($item?->name ?? 'Marketplace Item') }}
              </a>
              <p class="text-xs text-pp-700 font-extrabold mt-1">₦{{ number_format($listing?->price ?? 0) }}</p>
              <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 truncate">
                <i class="fas fa-store text-slate-400 text-[10px]"></i>
                <span>Seller: {{ $seller?->business_name ?: ($seller?->name ?: 'Seller') }}</span>
              </p>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
            <!-- BUY NOW BUTTON -->
            <button wire:click="buyNow({{ $wishlist->id }})" class="flex-1 py-2 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-2xs transition cursor-pointer">
              Buy Now →
            </button>

            <!-- MOVE TO CART BUTTON -->
            <button wire:click="moveToCart({{ $wishlist->id }})" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs shadow-2xs transition cursor-pointer" title="Move to Cart">
              <i class="fas fa-shopping-cart text-xs"></i>
            </button>

            <!-- REMOVE BUTTON -->
            <button wire:click="removeFromWishlist({{ $wishlist->id }})" class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Remove from Wishlist">
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