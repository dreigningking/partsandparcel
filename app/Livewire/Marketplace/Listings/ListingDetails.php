<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\CartItem;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Wishlist;
use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ListingDetails extends Component
{
    public Listing $listing;
    public int $soldCount = 0;
    public $relatedDiscussions;
    public $similarListings;
    public $reviews;
    public array $allMedia = [];
    public bool $isWishlisted = false;
    public string $title = 'Listing Details — Parts & Parcel';

    public function mount(Listing $listing)
    {
        $this->listing = $listing->load([
            'item.deviceModel.category.parent',
            'item.deviceModel.brand',
            'item.children',
            'location',
            'seller.primaryLocation',
            'media',
            'item.media',
            'reviews.user',
        ]);

        $this->title = (($this->listing->item?->name ?? $this->listing->item?->name) ?? 'Listing Details') . ' — Parts & Parcel';

        // 1. Sold Count: calculate from cart_items where cart has paid invoice, fallback to listing.sold_quantity
        $paidCount = CartItem::where('listing_id', $this->listing->id)
            ->whereHas('cart.invoice', fn($q) => $q->whereNotNull('paid_at'))
            ->sum('quantity');
        $this->soldCount = (int) ($paidCount > 0 ? $paidCount : ($this->listing->sold_quantity ?? 0));

        // 2. All Media (Listing media + Item media)
        $itemMedia = $this->listing->item?->media ?? collect();
        $mediaColl = $this->listing->media->concat($itemMedia)->unique('id')->values();
        $this->allMedia = $mediaColl->all();

        // 3. Wishlist State
        if (Auth::check()) {
            $this->isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('listing_id', $this->listing->id)
                ->exists();
        }

        // 4. Related Discussions (up to 6)
        $item = $this->listing->item;
        $catId = $item?->deviceModel?->category_id;
        $brandId = $item?->deviceModel?->brand_id;
        $modelId = $item?->model_id;

        $this->relatedDiscussions = Discussion::with(['user', 'category', 'brand', 'deviceModel', 'responses'])
            ->withCount('responses')
            ->when($catId || $brandId || $modelId, function ($q) use ($catId, $brandId, $modelId) {
                $q->where(function ($sub) use ($catId, $brandId, $modelId) {
                    if ($modelId) {
                        $sub->orWhere('model_id', $modelId);
                    }
                    if ($brandId) {
                        $sub->orWhere('brand_id', $brandId);
                    }
                    if ($catId) {
                        $sub->orWhere('category_id', $catId);
                    }
                });
            })
            ->latest()
            ->take(6)
            ->get();

        // 5. Similar Listings (up to 5)
        $this->similarListings = Listing::with(['item.deviceModel.brand', 'location', 'media', 'item.media'])
            ->where('id', '!=', $this->listing->id)
            ->where('status', 'active')
            ->when($catId || $brandId, function ($q) use ($catId, $brandId) {
                $q->whereHas('item.deviceModel', function ($dm) use ($catId, $brandId) {
                    if ($catId) {
                        $dm->where('category_id', $catId);
                    }
                    if ($brandId) {
                        $dm->orWhere('brand_id', $brandId);
                    }
                });
            })
            ->latest()
            ->take(5)
            ->get();

        // 6. Reviews (up to 6)
        $this->reviews = $this->listing->reviews()->with('user')->latest()->take(6)->get();
    }

    public function addToCart(CartService $cartService)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $cartService->addToCart($user, $this->listing, 1);
        $this->dispatch('cart-updated');
        session()->flash('cart_success', 'Item added to your cart!');
    }

    public function toggleWishlist()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $existing = Wishlist::where('user_id', $user->id)
            ->where('listing_id', $this->listing->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'listing_id' => $this->listing->id,
            ]);
            $this->isWishlisted = true;
        }
    }

    public function render()
    {
        return view('livewire.marketplace.listings.listing-details');
    }
}
