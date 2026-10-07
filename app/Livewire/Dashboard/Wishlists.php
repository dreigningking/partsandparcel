<?php

namespace App\Livewire\Dashboard;

use App\Models\Wishlist;
use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class Wishlists extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'stock')]
    public string $stockFilter = 'all'; // 'all', 'in_stock', 'sold_out'

    #[Url(as: 'type')]
    public string $itemType = 'all'; // 'all', 'device', 'part', 'scrap'

    #[Url(as: 'sort')]
    public string $sortBy = 'latest'; // 'latest', 'oldest', 'price_asc', 'price_desc'

    public int $perPage = 9;

    public function getListeners(): array
    {
        $userId = Auth::id();
        $listeners = [
            'wishlist-updated' => '$refresh',
            'cart-updated' => '$refresh',
            'notifications-updated' => '$refresh',
        ];

        if ($userId) {
            $listeners["echo-private:user.{$userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated"] = '$refresh';
        }

        return $listeners;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatedItemType(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'stockFilter', 'itemType', 'sortBy']);
        $this->resetPage();
    }

    public function removeFromWishlist(int $wishlistId): void
    {
        $item = Wishlist::where('user_id', Auth::id())->find($wishlistId);
        if ($item) {
            $item->delete();
            $this->dispatch('wishlist-updated');
            session()->flash('message', 'Item removed from your wishlist.');
        }
    }

    public function moveToCart(int $wishlistId): void
    {
        $wishlistItem = Wishlist::with('listing')->where('user_id', Auth::id())->find($wishlistId);

        if (! $wishlistItem || ! $wishlistItem->listing) {
            session()->flash('error', 'Item could not be found.');
            return;
        }

        if (! $wishlistItem->isAvailable()) {
            session()->flash('error', 'This item is currently sold out or unavailable and cannot be added to cart.');
            return;
        }

        app(CartService::class)->addToCart(Auth::user(), $wishlistItem->listing, 1);
        $wishlistItem->delete();

        $this->dispatch('cart-updated');
        $this->dispatch('wishlist-updated');
        session()->flash('message', 'Item moved to your cart!');
    }

    public function buyNow(int $wishlistId)
    {
        $wishlistItem = Wishlist::with('listing')->where('user_id', Auth::id())->find($wishlistId);

        if (! $wishlistItem || ! $wishlistItem->listing) {
            session()->flash('error', 'Item could not be found.');
            return;
        }

        if (! $wishlistItem->isAvailable()) {
            session()->flash('error', 'This item is currently sold out or unavailable.');
            return;
        }

        app(CartService::class)->addToCart(Auth::user(), $wishlistItem->listing, 1);
        $this->dispatch('cart-updated');

        return redirect()->route('checkout', ['seller' => $wishlistItem->listing->user_id]);
    }

    public function clearSoldOut(): void
    {
        $userId = Auth::id();
        $soldOutItems = Wishlist::with('listing')
            ->where('user_id', $userId)
            ->soldOut()
            ->get();

        $count = $soldOutItems->count();
        if ($count === 0) {
            // Also check for unavailable items (e.g. inactive or unpublished)
            $all = Wishlist::with('listing')->where('user_id', $userId)->get();
            $soldOutItems = $all->filter(fn ($w) => ! $w->isAvailable());
            $count = $soldOutItems->count();
        }

        foreach ($soldOutItems as $item) {
            $item->delete();
        }

        $this->dispatch('wishlist-updated');
        session()->flash('message', "{$count} sold-out item(s) removed from your wishlist.");
    }

    public function addAllInStockToCart(): void
    {
        $userId = Auth::id();
        $inStockItems = Wishlist::with('listing')
            ->where('user_id', $userId)
            ->inStock()
            ->get();

        $count = 0;
        $cartService = app(CartService::class);
        $user = Auth::user();

        foreach ($inStockItems as $item) {
            if ($item->listing && $item->isAvailable()) {
                $cartService->addToCart($user, $item->listing, 1);
                $item->delete();
                $count++;
            }
        }

        if ($count > 0) {
            $this->dispatch('cart-updated');
            $this->dispatch('wishlist-updated');
            session()->flash('message', "{$count} available item(s) moved to your cart!");
        } else {
            session()->flash('info', 'No in-stock items available to move to cart.');
        }
    }

    public function clearWishlist(): void
    {
        Wishlist::where('user_id', Auth::id())->delete();
        $this->dispatch('wishlist-updated');
        session()->flash('message', 'All items cleared from your wishlist.');
    }

    public function render()
    {
        $userId = Auth::id();

        // Stock stats for the current user
        $totalCount = Wishlist::where('user_id', $userId)->count();
        $inStockCount = Wishlist::where('user_id', $userId)->inStock()->count();
        $soldOutCount = Wishlist::where('user_id', $userId)->soldOut()->count();

        // Build dynamic query
        $query = Wishlist::with([
            'listing.item.deviceModel.brand',
            'listing.item.deviceModel.category',
            'listing.item.location',
            'listing.seller.primaryLocation',
            'listing.media',
        ])
        ->where('user_id', $userId);

        // Filter by stock status
        if ($this->stockFilter === 'in_stock') {
            $query->inStock();
        } elseif ($this->stockFilter === 'sold_out') {
            $query->soldOut();
        }

        // Filter by item type
        if ($this->itemType !== 'all') {
            $query->filterItemType($this->itemType);
        }

        // Search
        if (filled($this->search)) {
            $query->search($this->search);
        }

        // Sorting
        if ($this->sortBy === 'price_asc') {
            $query->join('listings', 'wishlists.listing_id', '=', 'listings.id')
                ->select('wishlists.*')
                ->orderBy('listings.price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $query->join('listings', 'wishlists.listing_id', '=', 'listings.id')
                ->select('wishlists.*')
                ->orderBy('listings.price', 'desc');
        } elseif ($this->sortBy === 'oldest') {
            $query->oldest('wishlists.created_at');
        } else {
            $query->latest('wishlists.created_at');
        }

        $wishlists = $query->paginate($this->perPage);

        return view('livewire.dashboard.wishlists', [
            'wishlists' => $wishlists,
            'totalCount' => $totalCount,
            'inStockCount' => $inStockCount,
            'soldOutCount' => $soldOutCount,
        ]);
    }
}
