<?php

namespace App\Livewire\Marketplace;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CartPage extends Component
{
    // Seller Grouped Carts Array
    public $cartGrouped = [
        [
            'id' => 'adam',
            'name' => 'Adam Computers',
            'avatar' => 'A',
            'avatar_bg' => 'bg-pp-600',
            'location' => 'Computer Village, Ikeja Lagos',
            'badge' => 'Seller Escrow Protected',
            'badge_bg' => 'bg-pp-50 text-pp-700',
            'items' => [
                [
                    'id' => 'item_101',
                    'title' => 'HP EliteBook 840 G5 Laptop',
                    'specs' => 'Intel i5 · 8GB RAM · 256GB SSD',
                    'price' => 280000,
                    'quantity' => 1,
                    'icon' => '💻'
                ]
            ]
        ],
        [
            'id' => 'seth',
            'name' => 'Seth Electronics',
            'avatar' => 'S',
            'avatar_bg' => 'bg-emerald-600',
            'location' => 'Ikeja, Lagos',
            'badge' => 'Specialized Battery Dealer',
            'badge_bg' => 'bg-emerald-50 text-emerald-700',
            'items' => [
                [
                    'id' => 'item_201',
                    'title' => 'HP EliteBook 840 G5 Battery',
                    'specs' => 'Original Units',
                    'price' => 25000,
                    'quantity' => 2,
                    'icon' => '⚡'
                ]
            ]
        ]
    ];

    // Alias getter for backward compatibility
    public function getSellerCartsProperty()
    {
        return $this->cartGrouped;
    }

    // Saved Addresses
    public $savedAddresses = [
        [
            'id' => 1,
            'label' => 'Home',
            'address' => '14 Allen Avenue, Ikeja, Lagos',
            'city' => 'Ikeja',
            'state' => 'Lagos'
        ],
        [
            'id' => 2,
            'label' => 'Office',
            'address' => '5 Adeola Odeku Street, Victoria Island, Lagos',
            'city' => 'Victoria Island',
            'state' => 'Lagos'
        ]
    ];

    // Offer Drawer State
    public $showOfferDrawer = false;
    public $activeSellerId = 'adam';
    public $activeSellerName = 'Adam Computers';
    public $activeSellerLocation = 'Computer Village, Ikeja Lagos';
    public $offerItems = ['item_101'];
    
    // Service Requests
    public $requestDelivery = true;
    public $deliveryAddressId = 1;
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

    public $proposedPrice = 280000;
    public $offerNote = '';

    public function openPackageOffer($sellerId)
    {
        $cart = collect($this->cartGrouped)->firstWhere('id', $sellerId);
        if (!$cart) return;

        $this->activeSellerId = $cart['id'];
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

        $newId = count($this->savedAddresses) + 1;
        $this->savedAddresses[] = [
            'id' => $newId,
            'label' => $this->newAddressLabel,
            'address' => $this->newAddressLine,
            'city' => $this->newCity,
            'state' => $this->newState
        ];

        $this->deliveryAddressId = $newId;
        $this->showNewAddressForm = false;
        $this->newAddressLine = '';
    }

    public function submitPackageOffer()
    {
        $this->showOfferDrawer = false;
        session()->flash('message', 'Package offer and service requests submitted successfully to ' . $this->activeSellerName . '!');
    }

    public function updateQuantity($sellerId, $itemId, $delta)
    {
        foreach ($this->cartGrouped as &$cart) {
            if ($cart['id'] === $sellerId) {
                foreach ($cart['items'] as &$item) {
                    if ($item['id'] === $itemId) {
                        $item['quantity'] = max(1, $item['quantity'] + $delta);
                    }
                }
            }
        }
    }

    public function removeItem($sellerId, $itemId)
    {
        foreach ($this->cartGrouped as &$cart) {
            if ($cart['id'] === $sellerId) {
                $cart['items'] = array_values(array_filter($cart['items'], fn($i) => $i['id'] !== $itemId));
            }
        }
        $this->cartGrouped = array_values(array_filter($this->cartGrouped, fn($c) => count($c['items']) > 0));
    }

    public function render()
    {
        return view('livewire.marketplace.cart-page');
    }
}