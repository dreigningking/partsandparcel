<?php

namespace App\Services\Commercial;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create active cart for a specific buyer and seller.
     */
    public function getOrCreateCart(User $buyer, int $sellerId): Cart
    {
        return Cart::firstOrCreate(
            [
                'buyer_id' => $buyer->id,
                'seller_id' => $sellerId,
                'status' => 'active',
            ],
            [
                'expires_at' => now()->addDays(7),
            ]
        );
    }

    /**
     * Add a listing to the buyer's split-cart for that listing's seller.
     */
    public function addToCart(User $buyer, Listing $listing, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateCart($buyer, $listing->user_id);

        $cartItem = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'listing_id' => $listing->id,
        ]);

        $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
        $cartItem->unit_price = $listing->price;
        $cartItem->save();

        return $cartItem;
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateQuantity(int $cartItemId, int $quantity, ?User $buyer = null): ?CartItem
    {
        $query = CartItem::query();
        if ($buyer) {
            $query->whereHas('cart', fn ($q) => $q->where('buyer_id', $buyer->id));
        }

        $item = $query->find($cartItemId);
        if (! $item) {
            return null;
        }

        if ($quantity <= 0) {
            $this->removeItem($cartItemId, $buyer);
            return null;
        }

        $item->update(['quantity' => $quantity]);
        return $item;
    }

    /**
     * Remove an item from the cart. If cart becomes empty, remove the cart.
     */
    public function removeItem(int $cartItemId, ?User $buyer = null): bool
    {
        $query = CartItem::query();
        if ($buyer) {
            $query->whereHas('cart', fn ($q) => $q->where('buyer_id', $buyer->id));
        }

        $item = $query->find($cartItemId);
        if (! $item) {
            return false;
        }

        $cart = $item->cart;
        $item->delete();

        if ($cart && $cart->items()->count() === 0) {
            $cart->delete();
        }

        return true;
    }

    /**
     * Get all active carts for buyer grouped by seller for the UI.
     */
    public function getGroupedCarts(?User $buyer): array
    {
        if (! $buyer) {
            return [];
        }

        $carts = Cart::with(['seller.primaryLocation', 'items.listing'])
            ->where('buyer_id', $buyer->id)
            ->where('status', 'active')
            ->get();

        $grouped = [];

        foreach ($carts as $cart) {
            if ($cart->items->isEmpty()) {
                continue;
            }

            $seller = $cart->seller;
            $locationName = $seller?->primaryLocation?->city
                ? "{$seller->primaryLocation->city}, {$seller->primaryLocation->state}"
                : ($seller?->country_code === 'US' ? 'United States' : 'Lagos, Nigeria');

            $items = [];
            foreach ($cart->items as $cartItem) {
                $listing = $cartItem->listing;
                $items[] = [
                    'id' => $cartItem->id,
                    'listing_id' => $cartItem->listing_id,
                    'title' => $listing?->title ?? "Item #{$cartItem->id}",
                    'price' => (float) $cartItem->unit_price,
                    'quantity' => (int) $cartItem->quantity,
                    'specs' => $listing?->condition ? ucfirst($listing->condition) : 'Standard',
                    'icon' => '📦',
                ];
            }

            $grouped[] = [
                'id' => (string) $cart->seller_id,
                'cart_id' => $cart->id,
                'name' => $seller?->business_name ?: ($seller?->name ?: 'Seller #' . $cart->seller_id),
                'avatar' => strtoupper(substr($seller?->name ?? 'S', 0, 1)),
                'avatar_bg' => 'bg-pp-600',
                'location' => $locationName,
                'badge' => $seller?->is_verified ? 'Verified Store' : 'Seller Escrow Protected',
                'badge_bg' => 'bg-emerald-50 text-emerald-700',
                'items' => $items,
            ];
        }

        return $grouped;
    }

    /**
     * Clear items and remove cart for a specific seller after purchase.
     */
    public function clearSellerCart(User $buyer, int $sellerId): void
    {
        $cart = Cart::where('buyer_id', $buyer->id)
            ->where('seller_id', $sellerId)
            ->first();

        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }
}
