<?php

namespace App\Livewire\Marketplace;

use App\Models\Cart;
use App\Models\Location;
use App\Models\User;
use App\Services\Commercial\CartService;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CartPage extends Component
{
    // Seller Grouped Carts Array
    public $cartGrouped = [];

    // Saved Addresses
    public $savedAddresses = [];

    // Offer Drawer State
    public $showOfferDrawer = false;
    public $activeSellerId = '';
    public $activeSellerName = '';
    public $activeSellerLocation = '';
    public $offerItems = [];
    
    // Service Requests inside offer
    public $requestDelivery = true;
    public $deliveryAddressId = null;
    public $showNewAddressForm = false;
    public $newAddressLabel = 'Home';
    public $newAddressLine = '';
    public $newCity = 'Ikeja';
    public $newState = 'Lagos';

    public $requestRepair = false;
    public $repairServiceType = 'Board Installation & Testing';
    public $repairDetails = '';

    public $requestWarranty = true;
    public $requestedWarrantyTerm = '14-Day Seller Warranty';

    public $proposedPrice = 0;
    public $offerNote = '';

    public function mount()
    {
        $this->loadCart();
        $this->loadAddresses();
    }

    public function loadCart()
    {
        $user = Auth::user();
        $cartService = app(CartService::class);

        if ($user) {
            $grouped = $cartService->getGroupedCarts($user);

            // If user has database cart items, use them
            if (! empty($grouped)) {
                $this->cartGrouped = $grouped;
                return;
            }
        }

        // Fallback demo cart for browsing demonstration if no DB items
        $this->cartGrouped = [
            [
                'id' => '2',
                'name' => 'Adam Computers',
                'avatar' => 'A',
                'avatar_bg' => 'bg-pp-600',
                'location' => 'Computer Village, Ikeja Lagos',
                'badge' => 'Seller Escrow Protected',
                'badge_bg' => 'bg-pp-50 text-pp-700',
                'items' => [
                    [
                        'id' => 101,
                        'listing_id' => 1,
                        'title' => 'HP EliteBook 840 G5 Laptop',
                        'specs' => 'Intel i5 · 8GB RAM · 256GB SSD',
                        'price' => 280000,
                        'quantity' => 1,
                        'icon' => '💻'
                    ]
                ]
            ]
        ];
    }

    public function loadAddresses()
    {
        $user = Auth::user();
        if ($user) {
            $addresses = Location::where('user_id', $user->id)->get();
            if ($addresses->isNotEmpty()) {
                $this->savedAddresses = $addresses->map(fn ($a) => [
                    'id' => $a->id,
                    'label' => $a->name ?? 'Address',
                    'address' => $a->address_line_1,
                    'city' => $a->city,
                    'state' => $a->state,
                ])->toArray();
                $this->deliveryAddressId = $this->savedAddresses[0]['id'] ?? null;
                return;
            }
        }

        $this->savedAddresses = [
            [
                'id' => 1,
                'label' => 'Home',
                'address' => '14 Allen Avenue, Ikeja, Lagos',
                'city' => 'Ikeja',
                'state' => 'Lagos'
            ]
        ];
        $this->deliveryAddressId = 1;
    }

    public function openPackageOffer($sellerId)
    {
        $cart = collect($this->cartGrouped)->firstWhere('id', (string) $sellerId);
        if (!$cart) return;

        $this->activeSellerId = (string) $cart['id'];
        $this->activeSellerName = $cart['name'];
        $this->activeSellerLocation = $cart['location'];
        
        $this->offerItems = collect($cart['items'])->pluck('id')->toArray();
        
        $itemsTotal = collect($cart['items'])->sum(fn($i) => $i['price'] * $i['quantity']);
        $this->proposedPrice = $itemsTotal;

        $this->showOfferDrawer = true;
    }

    public function closePackageOffer()
    {
        $this->showOfferDrawer = false;
    }

    public function toggleOfferItem($itemId)
    {
        if (in_array($itemId, $this->offerItems)) {
            $this->offerItems = array_diff($this->offerItems, [$itemId]);
        } else {
            $this->offerItems[] = $itemId;
        }

        $cart = collect($this->cartGrouped)->firstWhere('id', $this->activeSellerId);
        if ($cart) {
            $itemsTotal = collect($cart['items'])
                ->filter(fn($i) => in_array($i['id'], $this->offerItems))
                ->sum(fn($i) => $i['price'] * $i['quantity']);
            $this->proposedPrice = $itemsTotal;
        }
    }

    public function addAddress()
    {
        if (trim($this->newAddressLine) === '') return;

        $user = Auth::user();
        if ($user) {
            $location = Location::create([
                'user_id' => $user->id,
                'name' => $this->newAddressLabel,
                'address_line_1' => $this->newAddressLine,
                'city' => $this->newCity,
                'state' => $this->newState,
                'country_code' => $user->country_code ?? 'NG',
            ]);

            $this->loadAddresses();
            $this->deliveryAddressId = $location->id;
        } else {
            $newId = count($this->savedAddresses) + 1;
            $this->savedAddresses[] = [
                'id' => $newId,
                'label' => $this->newAddressLabel,
                'address' => $this->newAddressLine,
                'city' => $this->newCity,
                'state' => $this->newState
            ];
            $this->deliveryAddressId = $newId;
        }

        $this->showNewAddressForm = false;
        $this->newAddressLine = '';
    }

    public function submitPackageOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit a custom offer.');
            return redirect()->route('login');
        }

        $negotiationService = app(NegotiationService::class);

        $cart = collect($this->cartGrouped)->firstWhere('id', $this->activeSellerId);
        $sellerId = (int) $this->activeSellerId;

        $selectedCartItems = collect($cart['items'] ?? [])
            ->filter(fn ($i) => in_array($i['id'], $this->offerItems))
            ->values();

        $originalSubtotal = $selectedCartItems->sum(fn ($i) => $i['price'] * $i['quantity']);
        $discount = max(0, $originalSubtotal - (float) $this->proposedPrice);

        $offer = $negotiationService->createOfferFromCart($user, $sellerId, [
            'delivery_method' => $this->requestDelivery ? 'seller_delivery' : 'pickup',
            'discount' => $discount,
            'terms' => $this->offerNote ?: 'Custom cart offer proposal',
            'warranty_days' => 14,
            'warranty_terms' => $this->requestedWarrantyTerm,
            'items' => $this->offerItems,
            'request_repair' => $this->requestRepair,
            'repair_service_type' => $this->repairServiceType,
            'repair_details' => $this->repairDetails,
        ]);

        $this->showOfferDrawer = false;
        session()->flash('message', 'Package offer and proposals submitted successfully! Ref: OFF-' . $offer->id);

        return redirect()->route('offers');
    }

    public function updateQuantity($sellerId, $itemId, $newQuantity)
    {
        $user = Auth::user();
        $targetQty = max(1, (int) $newQuantity);

        if ($user) {
            $item = \App\Models\CartItem::find($itemId);
            if ($item) {
                app(CartService::class)->updateQuantity($itemId, $targetQty, $user);
                $this->loadCart();
                return;
            }
        }

        // Memory array update
        foreach ($this->cartGrouped as &$cart) {
            if ((string)$cart['id'] === (string)$sellerId) {
                foreach ($cart['items'] as &$item) {
                    if ($item['id'] == $itemId) {
                        $item['quantity'] = $targetQty;
                    }
                }
            }
        }
    }

    public function removeItem($sellerId, $itemId)
    {
        $user = Auth::user();
        if ($user) {
            $item = \App\Models\CartItem::find($itemId);
            if ($item) {
                app(CartService::class)->removeItem($itemId, $user);
                $this->loadCart();
                return;
            }
        }

        // Memory array update
        foreach ($this->cartGrouped as &$cart) {
            if ((string)$cart['id'] === (string)$sellerId) {
                $cart['items'] = array_values(array_filter($cart['items'], fn($i) => $i['id'] != $itemId));
            }
        }
        $this->cartGrouped = array_values(array_filter($this->cartGrouped, fn($c) => count($c['items']) > 0));
    }

    public function render()
    {
        return view('livewire.marketplace.cart-page');
    }
}