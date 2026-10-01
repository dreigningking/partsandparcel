<?php

namespace App\Livewire\Dashboard;

use App\Models\Wishlist;
use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class Wishlists extends Component
{
    use WithPagination;

    public function removeFromWishlist($wishlistId)
    {
        $item = Wishlist::where('user_id', Auth::id())->find($wishlistId);
        if ($item) {
            $item->delete();
            session()->flash('message', 'Item removed from your wishlist.');
        }
    }

    public function moveToCart($wishlistId)
    {
        $wishlistItem = Wishlist::with('listing')->where('user_id', Auth::id())->find($wishlistId);
        if ($wishlistItem && $wishlistItem->listing) {
            app(CartService::class)->addToCart(Auth::user(), $wishlistItem->listing, 1);
            $wishlistItem->delete();
            $this->dispatch('cart-updated');
            session()->flash('message', 'Item moved to your cart!');
        }
    }

    public function buyNow($wishlistId)
    {
        $wishlistItem = Wishlist::with('listing')->where('user_id', Auth::id())->find($wishlistId);
        if ($wishlistItem && $wishlistItem->listing) {
            app(CartService::class)->addToCart(Auth::user(), $wishlistItem->listing, 1);
            return redirect()->route('checkout', ['seller' => $wishlistItem->listing->user_id]);
        }
    }

    public function render()
    {
        $wishlists = Wishlist::with([
            'listing.item.deviceModel.brand',
            'listing.item.deviceModel.category',
            'listing.seller.primaryLocation',
            'listing.media',
            'listing.location',
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(9);

        return view('livewire.dashboard.wishlists', [
            'wishlists' => $wishlists,
        ]);
    }
}
