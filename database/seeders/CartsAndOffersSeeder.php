<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartsAndOffersSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNull('role_id')->get();
        if ($users->count() < 3) {
            $this->call(DemoUsersSeeder::class);
            $users = User::whereNull('role_id')->get();
        }

        $listings = Listing::with('item')->where('is_published', true)->where('is_active', true)->get();
        if ($listings->isEmpty()) {
            $this->call(DemoItemsAndListingsSeeder::class);
            $listings = Listing::with('item')->where('is_published', true)->where('is_active', true)->get();
        }

        // =========================================================================
        // 1. CARTS & CART ITEMS
        // STRICT RULE: Ensure that a user does NOT add his own listing to cart.
        // =========================================================================
        $buyers = $users->shuffle()->take(6);

        foreach ($buyers as $buyer) {
            // Find listings that do NOT belong to this buyer
            $availableListings = $listings->filter(fn($l) => $l->user_id !== $buyer->id);

            // Group available listings by seller so each cart corresponds to a distinct buyer-seller pair
            $listingsBySeller = $availableListings->groupBy('user_id');

            foreach ($listingsBySeller->take(2) as $sellerId => $sellerListings) {
                // Ensure buyer is not seller
                if ($buyer->id === $sellerId) {
                    continue;
                }

                $cart = Cart::updateOrCreate(
                    [
                        'buyer_id' => $buyer->id,
                        'seller_id' => $sellerId,
                    ],
                    [
                        'status' => 'active',
                    ]
                );

                // Add 1 or 2 items to this cart
                foreach ($sellerListings->take(2) as $listing) {
                    // Double check: user never adds their own listing to cart
                    if ($listing->user_id === $buyer->id) {
                        continue;
                    }

                    CartItem::updateOrCreate(
                        [
                            'cart_id' => $cart->id,
                            'listing_id' => $listing->id,
                        ],
                        [
                            'quantity' => 1,
                            'unit_price' => $listing->price,
                        ]
                    );
                }
            }
        }

        // =========================================================================
        // 2. OFFERS & OFFER ITEMS
        // STRICT RULE: Offers can be made on items where EITHER:
        //  - price is negotiable ($listing->is_negotiable == true), OR
        //  - warranty is negotiable ($listing->is_warranty_negotiable == true), OR
        //  - shipment is allowed ($listing->allow_shipping == true).
        // A user cannot make an offer on their own listing (sender_id != recipient_id).
        // =========================================================================
        $eligibleListings = $listings->filter(function ($listing) {
            return $listing->is_negotiable
                || $listing->is_warranty_negotiable
                || $listing->allow_shipping;
        });

        $offerStatuses = ['pending', 'accepted', 'countered'];

        foreach ($eligibleListings->take(8) as $index => $listing) {
            $sellerId = $listing->user_id;

            // Pick a prospective buyer who is NOT the seller
            $potentialBuyers = $users->filter(fn($u) => $u->id !== $sellerId);
            if ($potentialBuyers->isEmpty()) {
                continue;
            }
            $buyer = $potentialBuyers->random();

            $status = $offerStatuses[$index % count($offerStatuses)];
            $itemName = $listing->item?->name ?? 'Auto/Tech Spare Part';

            // Calculate offered terms based on what is negotiable
            $negotiatedPrice = $listing->price;
            $discountAmount = 0.00;
            $termsNotes = [];

            if ($listing->is_negotiable) {
                // Propose a reasonable discount (5% - 10%)
                $discountAmount = round($listing->price * 0.08, 2);
                $negotiatedPrice = max(1000, $listing->price - $discountAmount);
                $termsNotes[] = 'Proposing ₦' . number_format($negotiatedPrice, 2) . ' for direct escrow checkout.';
            }

            $offeredWarrantyDays = (int) ($listing->warranty_period_days ?: 7);
            if ($listing->is_warranty_negotiable) {
                $offeredWarrantyDays = max(14, $offeredWarrantyDays + 7);
                $termsNotes[] = "Requesting {$offeredWarrantyDays} days testing warranty.";
            }

            $deliveryMethod = 'buyer_responsible';
            if ($listing->allow_shipping) {
                $deliveryMethod = 'platform_responsible';
                $termsNotes[] = 'Requesting doorstep delivery via platform verified shipping logistics.';
            }

            $offerTerms = implode(' ', $termsNotes);

            $offer = Offer::updateOrCreate(
                [
                    'sender_id' => $buyer->id,
                    'recipient_id' => $sellerId,
                    'terms' => $offerTerms,
                ],
                [
                    'parent_id' => null,
                    'discussion_id' => null,
                    'response_id' => null,
                    'cart_id' => null,
                    'delivery_method' => $deliveryMethod,
                    'discount' => $discountAmount,
                    'status' => $status,
                    'expires_at' => now()->addDays(rand(3, 7)),
                ]
            );

            OfferItem::updateOrCreate(
                [
                    'offer_id' => $offer->id,
                    'listing_id' => $listing->id,
                ],
                [
                    'description' => $itemName,
                    'type' => 'item',
                    'quantity' => 1,
                    'unit_price' => $negotiatedPrice,
                    'warranty_period_days' => $offeredWarrantyDays,
                    'warranty_terms' => "Testing warranty of {$offeredWarrantyDays} days covering operational and electronic defects.",
                    'warranty_starts_at' => now(),
                    'warranty_ends_at' => now()->addDays($offeredWarrantyDays),
                ]
            );

            // If the offer was countered, create the seller's counter-offer child record
            if ($status === 'countered') {
                $counterDiscount = round($discountAmount / 2, 2);
                $counterPrice = $listing->price - $counterDiscount;
                $counterTerms = "Seller counter-offer: Can accept ₦" . number_format($counterPrice, 2) . " with {$offeredWarrantyDays} days testing warranty.";

                $counterOffer = Offer::updateOrCreate(
                    [
                        'parent_id' => $offer->id,
                        'sender_id' => $sellerId,
                        'recipient_id' => $buyer->id,
                    ],
                    [
                        'discussion_id' => null,
                        'response_id' => null,
                        'cart_id' => null,
                        'delivery_method' => $deliveryMethod,
                        'discount' => $counterDiscount,
                        'terms' => $counterTerms,
                        'status' => 'pending',
                        'expires_at' => now()->addDays(3),
                    ]
                );

                OfferItem::updateOrCreate(
                    [
                        'offer_id' => $counterOffer->id,
                        'listing_id' => $listing->id,
                    ],
                    [
                        'description' => $itemName . ' (Counter Offer)',
                        'type' => 'item',
                        'quantity' => 1,
                        'unit_price' => $counterPrice,
                        'warranty_period_days' => $offeredWarrantyDays,
                        'warranty_terms' => "Counter-offered warranty terms of {$offeredWarrantyDays} days.",
                        'warranty_starts_at' => now(),
                        'warranty_ends_at' => now()->addDays($offeredWarrantyDays),
                    ]
                );
            }
        }
    }
}
