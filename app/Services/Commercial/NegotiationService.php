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
use App\Models\ServiceJob;
use App\Models\Shipment;
use App\Models\ShipmentItem;
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
     * Create an offer originating directly from a listing.
     */
    public function createOfferFromListing(User $buyer, Listing $listing, array $data): Offer
    {
        $deliveryMethod = $this->mapDeliveryMethod($data['delivery_method'] ?? 'buyer_responsible');
        $discount = (float) ($data['discount'] ?? 0);
        $terms = $data['terms'] ?? $data['message'] ?? null;
        $price = isset($data['price']) ? (float) $data['price'] : (float) $listing->price;
        $warrantyPeriod = (int) ($data['warranty_days'] ?? $listing->warranty_period_days ?? 14);
        $warrantyTerms = $data['warranty_terms'] ?? $listing->warranty_terms ?? "{$warrantyPeriod}-day warranty";

        $offer = Offer::create([
            'sender_id' => $buyer->id,
            'recipient_id' => $listing->user_id,
            'delivery_method' => $deliveryMethod,
            'discount' => $discount,
            'terms' => $terms,
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
        ]);

        OfferItem::create([
            'offer_id' => $offer->id,
            'listing_id' => $listing->id,
            'description' => $listing->title ?? 'Listing Item',
            'type' => 'item',
            'quantity' => (int) ($data['quantity'] ?? 1),
            'unit_price' => $price,
            'warranty_period_days' => $warrantyPeriod,
            'warranty_terms' => $warrantyTerms,
        ]);

        if (! empty($data['request_repair'])) {
            OfferItem::create([
                'offer_id' => $offer->id,
                'listing_id' => null,
                'description' => $data['repair_service_type'] ?? 'Installation & Testing Service',
                'type' => 'service',
                'quantity' => 1,
                'unit_price' => (float) ($data['repair_price'] ?? 0),
                'warranty_period_days' => 14,
                'warranty_terms' => 'Workmanship warranty',
            ]);
        }

        $seller = User::find($listing->user_id);
        if ($seller && $seller->id !== $buyer->id) {
            $seller->notify(new \App\Notifications\NewOfferNotification($offer));
        }

        return $offer->load('items');
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
        $discount = (float) ($data['discount'] ?? 0);
        $terms = $data['terms'] ?? $data['message'] ?? null;
        $expiresAt = ! empty($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : now()->addHours(48);

        $offer = Offer::create([
            'sender_id' => $responder->id,
            'recipient_id' => $discussion->user_id,
            'discussion_id' => $discussionId,
            'response_id' => $responseId,
            'delivery_method' => $deliveryMethod,
            'discount' => $discount,
            'terms' => $terms,
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        if (! empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => $itemData['listing_id'] ?? null,
                    'description' => $itemData['description'] ?? 'Offer Item',
                    'type' => $itemData['type'] ?? 'service',
                    'quantity' => (int) ($itemData['quantity'] ?? 1),
                    'unit_price' => (float) ($itemData['unit_price'] ?? $itemData['price'] ?? 0),
                    'warranty_period_days' => $itemData['warranty_period_days'] ?? $itemData['warranty_days'] ?? null,
                    'warranty_terms' => $itemData['warranty_terms'] ?? null,
                ]);
            }
        } else {
            $price = (float) ($data['price'] ?? 0);
            $warrantyPeriod = (int) ($data['warranty_days'] ?? 14);
            $warrantyTerms = $data['warranty_terms'] ?? ($warrantyPeriod > 0 ? "{$warrantyPeriod}-day vendor warranty" : null);
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

            // Add pickup shipment if specified
            if (! empty($data['pickup_fee']) && (float) $data['pickup_fee'] > 0) {
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => null,
                    'description' => 'Pickup from Customer to Technician',
                    'type' => 'pickup',
                    'quantity' => 1,
                    'unit_price' => (float) $data['pickup_fee'],
                ]);
            }

            // Add delivery shipment if specified
            if (! empty($data['delivery_fee']) && (float) $data['delivery_fee'] > 0) {
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => null,
                    'description' => 'Delivery from Technician to Customer',
                    'type' => 'delivery',
                    'quantity' => 1,
                    'unit_price' => (float) $data['delivery_fee'],
                ]);
            }
        }

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

        if ($previousOffer->status !== 'pending') {
            throw new \InvalidArgumentException('Cannot counter an offer that is not in pending status.');
        }

        // Mark previous offer countered (immutable)
        $previousOffer->update(['status' => 'countered']);

        $deliveryMethod = ! empty($counterData['delivery_method'])
            ? $this->mapDeliveryMethod($counterData['delivery_method'])
            : $previousOffer->delivery_method;

        $rootOfferId = $previousOffer->parent_id ?: $previousOffer->id;

        $newOffer = Offer::create([
            'parent_id' => $rootOfferId,
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
                    'description' => $itemData['description'] ?? $itemData['title'] ?? 'Counter offer item',
                    'type' => $itemData['type'] ?? 'item',
                    'quantity' => (int) ($itemData['quantity'] ?? 1),
                    'unit_price' => (float) ($itemData['unit_price'] ?? 0),
                    'warranty_period_days' => $itemData['warranty_period_days'] ?? $itemData['warranty_days'] ?? null,
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
                    'unit_price' => $newUnitPrice !== null && $item->type !== 'pickup' && $item->type !== 'delivery' ? $newUnitPrice : $item->unit_price,
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
     * Edit an active pending offer before it has been countered or accepted.
     */
    public function editOffer(User $user, Offer $offer, array $data): Offer
    {
        if (! $offer->canBeEditedBy($user)) {
            throw new \InvalidArgumentException('Unauthorized: This offer cannot be edited because a counter-offer exists or it is not pending.');
        }

        $offer->update([
            'discount' => isset($data['discount']) ? (float) $data['discount'] : $offer->discount,
            'terms' => $data['terms'] ?? $data['message'] ?? $offer->terms,
            'delivery_method' => ! empty($data['delivery_method']) ? $this->mapDeliveryMethod($data['delivery_method']) : $offer->delivery_method,
        ]);

        if (! empty($data['items']) && is_array($data['items'])) {
            $offer->items()->delete();
            foreach ($data['items'] as $itemData) {
                OfferItem::create([
                    'offer_id' => $offer->id,
                    'listing_id' => $itemData['listing_id'] ?? null,
                    'description' => $itemData['description'] ?? 'Offer Item',
                    'type' => $itemData['type'] ?? 'item',
                    'quantity' => (int) ($itemData['quantity'] ?? 1),
                    'unit_price' => (float) ($itemData['unit_price'] ?? 0),
                    'warranty_period_days' => $itemData['warranty_period_days'] ?? $itemData['warranty_days'] ?? null,
                    'warranty_terms' => $itemData['warranty_terms'] ?? null,
                ]);
            }
        }

        return $offer->load('items');
    }

    /**
     * Accept an offer, generate directional shipments, and create an Invoice.
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
        if ($offer->cart_id && $offer->cart) {
            $buyerId = $offer->cart->buyer_id;
            $sellerId = $offer->cart->seller_id;
        } elseif ($offer->discussion_id && $offer->discussion) {
            $buyerId = $offer->discussion->user_id;
            $sellerId = ($offer->sender_id === $buyerId) ? $offer->recipient_id : $offer->sender_id;
        } else {
            $buyerId = $offer->recipient_id;
            $sellerId = $offer->sender_id;
        }

        $buyer = User::with('primaryLocation')->find($buyerId);
        $seller = User::with('primaryLocation')->find($sellerId);

        // Process Shipments for pickup and delivery offer items
        $shipmentsMap = []; // maps offer_item id => Shipment
        $itemItems = $offer->items->where('type', 'item');
        $serviceItems = $offer->items->where('type', 'service');
        $shipmentOfferItems = $offer->items->filter(fn($i) => in_array($i->type, ['pickup', 'delivery']));

        foreach ($shipmentOfferItems as $sItem) {
            // pickup: Buyer to Seller (customer device to technician)
            // delivery: Seller to Buyer (technician/seller returning or delivering to customer)
            if ($sItem->type === 'pickup') {
                $senderUser = $buyer;
                $receiverUser = $seller;
                $originLoc = $buyer?->primaryLocation;
                $destLoc = $seller?->primaryLocation;
            } else {
                $senderUser = $seller;
                $receiverUser = $buyer;
                $originLoc = $seller?->primaryLocation;
                $destLoc = $buyer?->primaryLocation;
            }

            $shipment = Shipment::create([
                'sender_id' => $senderUser?->id,
                'receiver_id' => $receiverUser?->id,
                'provider_name' => 'Parts & Parcel Logistics',
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'origin_location_id' => $originLoc?->id,
                'origin_contact_name' => $originLoc?->contact_name ?: $senderUser?->name,
                'origin_contact_phone' => $originLoc?->phone ?: $senderUser?->phone,
                'origin_address_line_1' => $originLoc?->address_line_1 ?: 'Origin Address',
                'origin_city' => $originLoc?->city ?: 'Lagos',
                'origin_state' => $originLoc?->state?->name ?? (is_string($originLoc?->state) ? $originLoc->state : 'Lagos'),
                'destination_location_id' => $destLoc?->id,
                'destination_contact_name' => $destLoc?->contact_name ?: $receiverUser?->name,
                'destination_contact_phone' => $destLoc?->phone ?: $receiverUser?->phone,
                'destination_address_line_1' => $destLoc?->address_line_1 ?: 'Destination Address',
                'destination_city' => $destLoc?->city ?: 'Lagos',
                'destination_state' => $destLoc?->state?->name ?? (is_string($destLoc?->state) ? $destLoc->state : 'Lagos'),
                'fee' => $sItem->subtotal(),
                'notes' => $sItem->description,
            ]);

            // Populate ShipmentItems matching business rules:
            // 1. If offer contains item, service, and shipment -> use item
            // 2. If offer contains service and shipment (no item) -> use service
            // 3. If offer contains item and shipment (no service) -> use item
            if ($itemItems->isNotEmpty()) {
                foreach ($itemItems as $it) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'itemable_type' => $it->listing_id ? Listing::class : null,
                        'itemable_id' => $it->listing_id,
                        'description' => $it->description,
                        'quantity' => $it->quantity,
                        'instruction' => 'Part/Component for shipment',
                    ]);
                }
            } elseif ($serviceItems->isNotEmpty()) {
                foreach ($serviceItems as $st) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'itemable_type' => null,
                        'itemable_id' => null,
                        'description' => $st->description,
                        'quantity' => $st->quantity,
                        'instruction' => 'Device/Equipment for service',
                    ]);
                }
            }

            $shipmentsMap[$sItem->id] = $shipment;
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
            if (in_array($oItem->type, ['pickup', 'delivery'])) {
                $itemableType = Shipment::class;
                $itemableId = isset($shipmentsMap[$oItem->id]) ? $shipmentsMap[$oItem->id]->id : null;
            } elseif ($oItem->type === 'item') {
                $itemableType = $oItem->listing_id ? Listing::class : null;
                $itemableId = $oItem->listing_id;
            } else {
                $itemableType = null;
                $itemableId = null;
            }

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'itemable_id' => $itemableId,
                'itemable_type' => $itemableType,
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

    /**
     * Handle invoice payment confirmation: creates a ServiceJob if discussion is a service request.
     */
    public function handleInvoicePaid(Invoice $invoice): ?ServiceJob
    {
        if (! $invoice->offer_id) {
            return null;
        }

        $offer = $invoice->offer ?? Offer::find($invoice->offer_id);
        if (! $offer || ! $offer->discussion_id) {
            return null;
        }

        $discussion = $offer->discussion ?? Discussion::find($offer->discussion_id);
        if (! $discussion || $discussion->type !== 'service') {
            return null;
        }

        $serviceItem = $offer->items()->where('type', 'service')->first();

        return ServiceJob::firstOrCreate(
            [
                'invoice_id' => $invoice->id,
                'offer_id' => $offer->id,
            ],
            [
                'customer_id' => $invoice->buyer_id,
                'provider_id' => $invoice->seller_id,
                'category_id' => $discussion->category_id,
                'brand_id' => $discussion->brand_id,
                'model_id' => $discussion->model_id,
                'title' => $serviceItem?->description ?: $discussion->title,
                'description' => $discussion->body,
                'status' => 'in_progress',
                'location_id' => $discussion->location_id,
                'warranty_period_days' => $serviceItem?->warranty_period_days ?? 14,
                'warranty_terms' => $serviceItem?->warranty_terms ?? 'Standard service warranty',
            ]
        );
    }
}
