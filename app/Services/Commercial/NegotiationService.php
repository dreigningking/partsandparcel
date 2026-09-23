<?php

namespace App\Services\Commercial;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Response;
use App\Models\User;
use Illuminate\Support\Str;

class NegotiationService
{
    /**
     * Map friendly delivery method string to database enum value.
     */
    public function mapDeliveryMethod(?string $method): string
    {
        return match (strtolower($method ?? '')) {
            'seller_delivery', 'seller delivery', 'seller_responsible' => 'seller_responsible',
            'platform_delivery', 'platform_responsible' => 'platform_responsible',
            default => 'buyer_responsible',
        };
    }

    /**
     * Create an offer originating from the buyer's split-cart.
     */
    public function createOfferFromCart(User $buyer, int $sellerId, array $data): Offer
    {
        $cart = Cart::where('buyer_id', $buyer->id)
            ->where('seller_id', $sellerId)
            ->where('status', 'active')
            ->first();

        $deliveryMethod = $this->mapDeliveryMethod($data['delivery_method'] ?? 'buyer_responsible');
        $discount = (float) ($data['discount'] ?? 0);
        $terms = $data['terms'] ?? $data['notes'] ?? null;
        $warrantyPeriod = (int) ($data['warranty_days'] ?? 14);
        $warrantyTerms = $data['warranty_terms'] ?? 'Standard seller inspection warranty';

        $offer = Offer::create([
            'sender_id' => $buyer->id,
            'recipient_id' => $sellerId,
            'cart_id' => $cart?->id,
            'delivery_method' => $deliveryMethod,
            'discount' => $discount,
            'terms' => $terms,
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
        ]);

        // Add selected items from cart
        $selectedItemIds = $data['items'] ?? ($cart ? $cart->items->pluck('id')->toArray() : []);
        $cartItems = $cart ? $cart->items()->whereIn('id', $selectedItemIds)->get() : collect();

        if ($cartItems->isNotEmpty()) {
            foreach ($cartItems as $cItem) {
                $listing = $cItem->listing;
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => $cItem->listing_id,
                    'description' => $listing?->title ?? "Item #{$cItem->id}",
                    'type' => 'item',
                    'quantity' => $cItem->quantity,
                    'unit_price' => $cItem->unit_price,
                    'warranty_period_days' => $warrantyPeriod,
                    'warranty_terms' => $warrantyTerms,
                ]);
            }
        } elseif (! empty($data['custom_items'])) {
            foreach ($data['custom_items'] as $cItem) {
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => $cItem['listing_id'] ?? null,
                    'description' => $cItem['description'] ?? 'Product Offer Item',
                    'type' => $cItem['type'] ?? 'item',
                    'quantity' => $cItem['quantity'] ?? 1,
                    'unit_price' => $cItem['unit_price'] ?? 0,
                    'warranty_period_days' => $cItem['warranty_days'] ?? $warrantyPeriod,
                    'warranty_terms' => $cItem['warranty_terms'] ?? $warrantyTerms,
                ]);
            }
        }

        // Optional repair service line item
        if (! empty($data['request_repair']) && ! empty($data['repair_service_type'])) {
            OfferItem::create([
                'offer_id' => $offer->id,
                'listing_id' => null,
                'description' => $data['repair_service_type'] . (! empty($data['repair_details']) ? ' - ' . $data['repair_details'] : ''),
                'type' => 'service',
                'quantity' => 1,
                'unit_price' => (float) ($data['repair_price'] ?? 0),
                'warranty_period_days' => (int) ($data['repair_warranty_days'] ?? 14),
                'warranty_terms' => 'Repair workmanship warranty',
            ]);
        }

        // Optional delivery fee line item
        if ($deliveryMethod === 'seller_responsible' && ! empty($data['delivery_fee'])) {
            OfferItem::create([
                'offer_id' => $offer->id,
                'listing_id' => null,
                'description' => 'Seller Dispatch Delivery Fee',
                'type' => 'delivery',
                'quantity' => 1,
                'unit_price' => (float) $data['delivery_fee'],
                'warranty_period_days' => null,
                'warranty_terms' => null,
            ]);
        }

        $seller = User::find($sellerId);
        if ($seller && $seller->id !== $buyer->id) {
            $seller->notify(new \App\Notifications\NewOfferNotification($offer));
        }

        return $offer->load('items');
    }

    /**
     * Create an offer attached to a community response.
     */
    public function createOfferFromResponse(User $responder, int $discussionId, int $responseId, array $data): Offer
    {
        $discussion = Discussion::findOrFail($discussionId);
        $deliveryMethod = $this->mapDeliveryMethod($data['delivery_method'] ?? 'buyer_responsible');
        $price = (float) ($data['price'] ?? 0);
        $warrantyPeriod = (int) ($data['warranty_days'] ?? 14);
        $warrantyTerms = $data['warranty_terms'] ?? ($warrantyPeriod > 0 ? "{$warrantyPeriod}-day vendor warranty" : null);

        $offer = Offer::create([
            'sender_id' => $responder->id,
            'recipient_id' => $discussion->user_id,
            'discussion_id' => $discussionId,
            'response_id' => $responseId,
            'delivery_method' => $deliveryMethod,
            'discount' => (float) ($data['discount'] ?? 0),
            'terms' => $data['terms'] ?? $data['message'] ?? null,
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
        ]);

        $itemDescription = ! empty($data['description'])
            ? $data['description']
            : "Proposal for: {$discussion->title}";

        OfferItem::create([
            'offer_id' => $offer->id,
            'listing_id' => $data['listing_id'] ?? null,
            'description' => $itemDescription,
            'type' => $data['type'] ?? ($discussion->type === 'service' ? 'service' : 'item'),
            'quantity' => (int) ($data['quantity'] ?? 1),
            'unit_price' => $price,
            'warranty_period_days' => $warrantyPeriod,
            'warranty_terms' => $warrantyTerms,
        ]);

        return $offer->load('items');
    }

    /**
     * Submit a counter-offer in response to an existing offer.
     */
    public function submitCounterOffer(User $user, Offer $previousOffer, array $counterData): Offer
    {
        // Must be the recipient of the previous offer to counter it
        if ($user->id !== $previousOffer->recipient_id) {
            throw new \InvalidArgumentException('Unauthorized: Only the recipient can counter an active offer.');
        }

        // Mark previous offer countered
        $previousOffer->update(['status' => 'countered']);

        $deliveryMethod = ! empty($counterData['delivery_method'])
            ? $this->mapDeliveryMethod($counterData['delivery_method'])
            : $previousOffer->delivery_method;

        $newOffer = Offer::create([
            'parent_id' => $previousOffer->id,
            'sender_id' => $user->id,
            'recipient_id' => $previousOffer->sender_id,
            'cart_id' => $previousOffer->cart_id,
            'discussion_id' => $previousOffer->discussion_id,
            'response_id' => $previousOffer->response_id,
            'delivery_method' => $deliveryMethod,
            'discount' => (float) ($counterData['discount'] ?? $previousOffer->discount),
            'terms' => $counterData['terms'] ?? $counterData['message'] ?? $previousOffer->terms,
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
        ]);

        // Duplicate and adjust items
        if (! empty($counterData['items'])) {
            foreach ($counterData['items'] as $itemData) {
                OfferItem::create([
                    'offer_id' => $newOffer->id,
                    'listing_id' => $itemData['listing_id'] ?? null,
                    'description' => $itemData['description'],
                    'type' => $itemData['type'] ?? 'item',
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_price' => (float) $itemData['unit_price'],
                    'warranty_period_days' => $itemData['warranty_period_days'] ?? 14,
                    'warranty_terms' => $itemData['warranty_terms'] ?? null,
                ]);
            }
        } else {
            // Default: inherit existing items with updated proposed total if provided
            $newUnitPrice = isset($counterData['price']) ? (float) $counterData['price'] : null;

            foreach ($previousOffer->items as $item) {
                OfferItem::create([
                    'offer_id' => $newOffer->id,
                    'listing_id' => $item->listing_id,
                    'description' => $item->description,
                    'type' => $item->type,
                    'quantity' => $item->quantity,
                    'unit_price' => $newUnitPrice !== null ? $newUnitPrice : $item->unit_price,
                    'warranty_period_days' => $counterData['warranty_days'] ?? $item->warranty_period_days,
                    'warranty_terms' => $counterData['warranty_terms'] ?? $item->warranty_terms,
                ]);
            }
        }

        if ($newOffer->recipient && $newOffer->recipient_id !== $user->id) {
            $newOffer->recipient->notify(new \App\Notifications\CounterOfferNotification($newOffer));
        }

        return $newOffer->load('items');
    }

    /**
     * Accept an offer and automatically generate an Invoice.
     */
    public function acceptOffer(User $user, Offer $offer): Invoice
    {
        if ($user->id !== $offer->recipient_id) {
            throw new \InvalidArgumentException('Unauthorized: Only the recipient can accept this offer.');
        }

        if ($offer->status === 'accepted') {
            return $offer->invoice ?? Invoice::where('offer_id', $offer->id)->firstOrFail();
        }

        $offer->update(['status' => 'accepted']);

        // Determine Buyer and Seller
        // If from cart: cart buyer is buyer, cart seller is seller
        // If from discussion: discussion author is buyer/requester, responder is seller/provider
        if ($offer->cart_id && $offer->cart) {
            $buyerId = $offer->cart->buyer_id;
            $sellerId = $offer->cart->seller_id;
        } elseif ($offer->discussion_id && $offer->discussion) {
            $buyerId = $offer->discussion->user_id;
            $sellerId = ($offer->sender_id === $buyerId) ? $offer->recipient_id : $offer->sender_id;
        } else {
            // Recipient accepted: if sender initiated request, determine roles by discussion or fallback
            $buyerId = $offer->recipient_id;
            $sellerId = $offer->sender_id;
        }

        $subtotal = $offer->subtotal();
        $discount = (float) $offer->discount;
        $total = max(0, $subtotal - $discount);
        $commission = round($total * 0.05, 2); // 5% platform commission

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'cart_id' => $offer->cart_id,
            'offer_id' => $offer->id,
            'delivery_method' => $offer->delivery_method,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => 0.00,
            'total' => $total,
            'payment_method' => 'platform',
            'commission' => $commission,
            'status' => 'issued',
            'issued_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        // Create invoice items
        foreach ($offer->items as $oItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'itemable_id' => $oItem->listing_id,
                'itemable_type' => $oItem->listing_id ? Listing::class : null,
                'type' => $oItem->type,
                'description' => $oItem->description,
                'quantity' => $oItem->quantity,
                'unit_price' => $oItem->unit_price,
                'amount' => $oItem->subtotal(),
                'warranty_period_days' => $oItem->warranty_period_days,
                'warranty_terms' => $oItem->warranty_terms,
            ]);
        }

        // If from cart, clear buyer's cart for this seller
        if ($offer->cart_id) {
            app(CartService::class)->clearSellerCart(User::find($buyerId), $sellerId);
        }

        $otherParty = ($user->id === $offer->sender_id) ? $offer->recipient : $offer->sender;
        if ($otherParty) {
            $otherParty->notify(new \App\Notifications\OfferAcceptedNotification($offer, $invoice));
        }
        if ($invoice->buyer && $invoice->buyer_id !== $user->id) {
            $invoice->buyer->notify(new \App\Notifications\InvoiceIssuedNotification($invoice));
        }

        return $invoice->load('items');
    }
}
