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
    protected string $guestCartSessionKey = 'guest_cart';

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
            ]
        );
    }

    /**
     * Add a listing to the buyer's split-cart for that listing's seller.
     * Supports both authenticated users and guests.
     */
    public function addToCart(?User $buyer, Listing $listing, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        if ($buyer) {
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

        // Guest cart stored in session, grouped by seller_id
        $guestCart = Session::get($this->guestCartSessionKey, []);
        $sellerId = $listing->user_id;

        if (! isset($guestCart[$sellerId])) {
            $guestCart[$sellerId] = [];
        }

        $existingQty = $guestCart[$sellerId][$listing->id]['quantity'] ?? 0;
        $guestCart[$sellerId][$listing->id] = [
            'listing_id' => $listing->id,
            'quantity' => $existingQty + $quantity,
            'unit_price' => (float) $listing->price,
        ];

        Session::put($this->guestCartSessionKey, $guestCart);

        // Return a transient CartItem for caller consistency
        $transientItem = new CartItem([
            'listing_id' => $listing->id,
            'quantity' => $existingQty + $quantity,
            'unit_price' => $listing->price,
        ]);
        $transientItem->setRelation('listing', $listing);

        return $transientItem;
    }

    /**
     * Update quantity of a cart item (handles both DB item IDs and guest items).
     */
    public function updateQuantity(int|string $cartItemId, int $quantity, ?User $buyer = null): ?CartItem
    {
        if ($buyer && is_numeric($cartItemId)) {
            $query = CartItem::query();
            $query->whereHas('cart', fn ($q) => $q->where('buyer_id', $buyer->id));

            $item = $query->find((int) $cartItemId);
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

        // Guest update in session
        $guestCart = Session::get($this->guestCartSessionKey, []);
        foreach ($guestCart as $sId => &$items) {
            foreach ($items as $listingId => &$itemData) {
                if ((string)$listingId === (string)$cartItemId || 'guest_' . $listingId === (string)$cartItemId) {
                    if ($quantity <= 0) {
                        unset($items[$listingId]);
                    } else {
                        $itemData['quantity'] = $quantity;
                    }
                    if (empty($items)) {
                        unset($guestCart[$sId]);
                    }
                    Session::put($this->guestCartSessionKey, $guestCart);
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Remove an item from the cart. If cart becomes empty, remove the cart.
     */
    public function removeItem(int|string $cartItemId, ?User $buyer = null): bool
    {
        if ($buyer && is_numeric($cartItemId)) {
            $query = CartItem::query();
            $query->whereHas('cart', fn ($q) => $q->where('buyer_id', $buyer->id));

            $item = $query->find((int) $cartItemId);
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

        // Guest remove from session
        $guestCart = Session::get($this->guestCartSessionKey, []);
        $removed = false;

        foreach ($guestCart as $sId => &$items) {
            foreach ($items as $listingId => $itemData) {
                if ((string)$listingId === (string)$cartItemId || 'guest_' . $listingId === (string)$cartItemId) {
                    unset($items[$listingId]);
                    $removed = true;
                    break;
                }
            }
            if (empty($items)) {
                unset($guestCart[$sId]);
            }
        }

        if ($removed) {
            Session::put($this->guestCartSessionKey, $guestCart);
        }

        return $removed;
    }

    /**
     * Merge guest session cart items into authenticated user's DB carts.
     */
    public function mergeGuestCart(User $buyer): void
    {
        $guestCart = Session::get($this->guestCartSessionKey, []);
        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $sellerId => $items) {
            $sellerIdInt = (int) $sellerId;
            $cart = $this->getOrCreateCart($buyer, $sellerIdInt);

            foreach ($items as $listingId => $data) {
                $listing = Listing::find($data['listing_id'] ?? $listingId);
                if (! $listing) {
                    continue;
                }

                $cartItem = CartItem::firstOrNew([
                    'cart_id' => $cart->id,
                    'listing_id' => $listing->id,
                ]);

                $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + ($data['quantity'] ?? 1);
                $cartItem->unit_price = $listing->price;
                $cartItem->save();
            }
        }

        Session::forget($this->guestCartSessionKey);
    }

    /**
     * Get all active carts for buyer (or guest in session) grouped by seller for the UI.
     */
    public function getGroupedCarts(?User $buyer): array
    {
        if ($buyer) {
            // First merge any guest items they may have added before signing in
            $this->mergeGuestCart($buyer);

            $carts = Cart::with(['seller.primaryLocation.state', 'items.listing.item', 'items.listing.media'])
                ->where('buyer_id', $buyer->id)
                ->where('status', 'active')
                ->get();

            $grouped = [];

            foreach ($carts as $cart) {
                if ($cart->items->isEmpty()) {
                    continue;
                }

                $seller = $cart->seller;
                $loc = $seller?->primaryLocation;
                $stateName = $loc?->state?->name ?? (is_string($loc?->state) ? $loc?->state : null);
                $locationName = $loc
                    ? collect([$loc->city, $stateName])->filter()->implode(', ')
                    : ($seller?->country_code === 'US' ? 'United States' : 'Computer Village, Ikeja, Lagos');

                if (empty($locationName)) {
                    $locationName = 'Computer Village, Ikeja, Lagos';
                }

                $items = [];
                foreach ($cart->items as $cartItem) {
                    $listing = $cartItem->listing;
                    $item = $listing?->item;
                    $items[] = [
                        'id' => $cartItem->id,
                        'listing_id' => $cartItem->listing_id,
                        'title' => $listing?->title ?? ($item?->name ?? "Item #{$cartItem->id}"),
                        'price' => (float) $cartItem->unit_price,
                        'quantity' => (int) $cartItem->quantity,
                        'specs' => $listing?->condition ? ucfirst($listing->condition) : 'Standard',
                        'icon' => $item?->item_type === 'part' ? '⚙️' : ($item?->item_type === 'scrap' ? '🛠️' : '💻'),
                    ];
                }

                $grouped[] = [
                    'id' => (string) $cart->seller_id,
                    'seller_id' => $cart->seller_id,
                    'seller_slug' => $seller?->slug ?: (string) $cart->seller_id,
                    'cart_id' => $cart->id,
                    'name' => $seller?->business_name ?: ($seller?->name ?: 'Seller #' . $cart->seller_id),
                    'avatar' => strtoupper(substr($seller?->business_name ?: ($seller?->name ?? 'S'), 0, 1)),
                    'avatar_bg' => 'bg-pp-600',
                    'location' => $locationName,
                    'badge' => $seller?->is_verified ? 'Verified Store' : 'Seller Escrow Protected',
                    'badge_bg' => 'bg-emerald-50 text-emerald-700',
                    'items' => $items,
                ];
            }

            return $grouped;
        }

        // Guest carts grouped from session
        $guestCart = Session::get($this->guestCartSessionKey, []);
        if (empty($guestCart)) {
            return [];
        }

        $grouped = [];

        foreach ($guestCart as $sellerId => $cartItems) {
            if (empty($cartItems)) {
                continue;
            }

            $seller = User::with('primaryLocation.state')->find($sellerId);
            $loc = $seller?->primaryLocation;
            $stateName = $loc?->state?->name ?? '';
            $locationName = $loc
                ? collect([$loc->city, $stateName])->filter()->implode(', ')
                : 'Computer Village, Ikeja, Lagos';

            if (empty($locationName)) {
                $locationName = 'Computer Village, Ikeja, Lagos';
            }

            $listingIds = array_keys($cartItems);
            $listings = Listing::with(['item', 'media'])->whereIn('id', $listingIds)->get()->keyBy('id');

            $items = [];
            foreach ($cartItems as $listingId => $itemData) {
                $listing = $listings->get($listingId);
                if (! $listing) {
                    continue;
                }
                $item = $listing->item;
                $items[] = [
                    'id' => 'guest_' . $listingId,
                    'listing_id' => $listing->id,
                    'title' => $listing->title ?? ($item?->name ?? "Item #{$listing->id}"),
                    'price' => (float) ($itemData['unit_price'] ?? $listing->price),
                    'quantity' => (int) ($itemData['quantity'] ?? 1),
                    'specs' => $listing->condition ? ucfirst($listing->condition) : 'Standard',
                    'icon' => $item?->item_type === 'part' ? '⚙️' : ($item?->item_type === 'scrap' ? '🛠️' : '💻'),
                ];
            }

            if (! empty($items)) {
                $grouped[] = [
                    'id' => (string) $sellerId,
                    'cart_id' => 'guest_' . $sellerId,
                    'name' => $seller?->business_name ?: ($seller?->name ?: 'Seller #' . $sellerId),
                    'avatar' => strtoupper(substr($seller?->business_name ?: ($seller?->name ?? 'S'), 0, 1)),
                    'avatar_bg' => 'bg-pp-600',
                    'location' => $locationName,
                    'badge' => $seller?->is_verified ? 'Verified Store' : 'Seller Escrow Protected',
                    'badge_bg' => 'bg-emerald-50 text-emerald-700',
                    'items' => $items,
                ];
            }
        }

        return $grouped;
    }

    /**
     * Get total count of cart items for badge display.
     */
    public function getCartCount(?User $buyer): int
    {
        $guestCart = Session::get($this->guestCartSessionKey, []);
        $guestCount = 0;
        foreach ($guestCart as $sellerItems) {
            foreach ($sellerItems as $item) {
                $guestCount += (int) ($item['quantity'] ?? 1);
            }
        }

        if (! $buyer) {
            return $guestCount;
        }

        $dbCount = (int) CartItem::whereHas('cart', fn ($q) => $q->where('buyer_id', $buyer->id)->where('status', 'active'))
            ->sum('quantity');

        return $dbCount + $guestCount;
    }

    /**
     * Clear items and remove cart for a specific seller after purchase.
     */
    public function clearSellerCart(?User $buyer, int $sellerId): void
    {
        if ($buyer) {
            $cart = Cart::where('buyer_id', $buyer->id)
                ->where('seller_id', $sellerId)
                ->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        }

        // Also clean up session if present
        $guestCart = Session::get($this->guestCartSessionKey, []);
        if (isset($guestCart[$sellerId])) {
            unset($guestCart[$sellerId]);
            Session::put($this->guestCartSessionKey, $guestCart);
        }
    }
}
