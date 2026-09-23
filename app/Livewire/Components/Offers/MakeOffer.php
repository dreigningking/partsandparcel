<?php

namespace App\Livewire\Components\Offers;

use App\Models\Cart;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MakeOffer extends Component
{
    public bool $isOpen = false;
    public ?string $sellerId = null;
    public string $sellerName = 'Seller';
    public ?int $listingId = null;

    // Delivery mode ('pickup' vs 'seller_delivery')
    public string $deliveryMode = 'pickup';
    public ?int $deliveryAddressId = null;

    // Address management
    public array $savedAddresses = [];
    public bool $showNewAddressForm = false;
    public string $newAddressLabel = 'Home';
    public string $newAddressLine = '';
    public string $newCity = 'Ikeja';
    public string $newState = 'Lagos';

    // Items in the offer
    public array $cartItems = [];
    public array $selectedItemIds = [];

    // Price & Terms
    public string $proposedPrice = '';
    public string $offerNote = '';
    public int $warrantyDays = 14;
    public string $warrantyTerms = '14-day replacement and inspection warranty';

    // Optional repair / services
    public bool $requestRepair = false;
    public string $repairServiceType = 'Installation & Testing';
    public string $repairDetails = '';

    #[On('open-make-offer')]
    public function loadOfferDrawer($payload = null)
    {
        $user = Auth::user();

        // Extract payload parameters
        if (is_array($payload)) {
            $this->sellerId = isset($payload['seller_id']) ? (string) $payload['seller_id'] : null;
            $this->sellerName = $payload['seller_name'] ?? 'Seller';
            $this->listingId = isset($payload['listing_id']) ? (int) $payload['listing_id'] : null;
        } elseif (is_numeric($payload)) {
            $this->sellerId = (string) $payload;
        }

        // Resolve seller name if user ID given
        if ($this->sellerId && is_numeric($this->sellerId)) {
            $seller = User::find($this->sellerId);
            if ($seller) {
                $this->sellerName = $seller->business_name ?: $seller->name;
            }
        }

        // Load saved addresses for logged in user
        $this->loadAddresses();

        // Load items: from single listing OR from buyer's split-cart
        $this->loadItems();

        $this->isOpen = true;
    }

    public function loadAddresses()
    {
        $user = Auth::user();
        if ($user) {
            $addresses = Location::where('user_id', $user->id)->get();
            if ($addresses->isNotEmpty()) {
                $this->savedAddresses = $addresses->map(fn ($a) => [
                    'id' => $a->id,
                    'label' => $a->name ?: 'Address',
                    'address' => $a->address_line_1,
                    'city' => $a->city,
                    'state' => $a->state,
                ])->toArray();
                $this->deliveryAddressId = $this->savedAddresses[0]['id'];
                return;
            }
        }

        $this->savedAddresses = [
            ['id' => 1, 'label' => 'Home', 'address' => '12 Computer Village Rd', 'city' => 'Ikeja', 'state' => 'Lagos'],
            ['id' => 2, 'label' => 'Workshop / Shop', 'address' => 'Plaza 3, Shop 14, Computer Village', 'city' => 'Ikeja', 'state' => 'Lagos']
        ];
        $this->deliveryAddressId = 1;
    }

    public function loadItems()
    {
        $user = Auth::user();
        $this->cartItems = [];
        $this->selectedItemIds = [];

        // Case 1: Triggered for a specific listing
        if ($this->listingId) {
            $listing = Listing::with('user')->find($this->listingId);
            if ($listing) {
                $this->sellerId = (string) $listing->user_id;
                $this->sellerName = $listing->user?->business_name ?: $listing->user?->name ?: $this->sellerName;
                $this->cartItems = [
                    [
                        'id' => 1,
                        'listing_id' => $listing->id,
                        'title' => $listing->title ?? $listing->description ?? ($listing->assetable?->name ?? 'Listing #' . $listing->id),
                        'specs' => $listing->condition ? ucfirst($listing->condition) : 'Tested',
                        'price' => (float) $listing->price,
                        'quantity' => 1,
                        'icon' => '📦'
                    ]
                ];
                $this->selectedItemIds = [1];
                $this->proposedPrice = (string) $listing->price;
                return;
            }
        }

        // Case 2: From Cart for a specific seller
        if ($user && $this->sellerId && is_numeric($this->sellerId)) {
            $cart = Cart::with(['items.listing'])
                ->where('buyer_id', $user->id)
                ->where('seller_id', $this->sellerId)
                ->where('status', 'active')
                ->first();

            if ($cart && $cart->items->isNotEmpty()) {
                $this->cartItems = $cart->items->map(fn ($item) => [
                    'id' => $item->id,
                    'listing_id' => $item->listing_id,
                    'title' => $item->listing?->title ?? "Item #{$item->id}",
                    'specs' => $item->listing?->condition ? ucfirst($item->listing->condition) : 'Tested',
                    'price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'icon' => '💻'
                ])->toArray();

                $this->selectedItemIds = collect($this->cartItems)->pluck('id')->toArray();
                $subtotal = collect($this->cartItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
                $this->proposedPrice = (string) $subtotal;
                return;
            }
        }

        // Fallback demo item if guest / no DB cart
        $this->cartItems = [
            [
                'id' => 101,
                'listing_id' => null,
                'title' => 'HP EliteBook 840 G5 Motherboard',
                'specs' => 'Tested Working · Grade A',
                'price' => 85000,
                'quantity' => 1,
                'icon' => '💻'
            ]
        ];
        $this->selectedItemIds = [101];
        $this->proposedPrice = '80000';
    }

    public function toggleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItemIds)) {
            $this->selectedItemIds = array_values(array_diff($this->selectedItemIds, [$itemId]));
        } else {
            $this->selectedItemIds[] = $itemId;
        }

        $newSubtotal = collect($this->cartItems)
            ->filter(fn ($i) => in_array($i['id'], $this->selectedItemIds))
            ->sum(fn ($i) => $i['price'] * $i['quantity']);

        $this->proposedPrice = (string) $newSubtotal;
    }

    public function saveNewAddress()
    {
        if (trim($this->newAddressLine) === '') {
            return;
        }

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
                'state' => $this->newState,
            ];
            $this->deliveryAddressId = $newId;
        }

        $this->showNewAddressForm = false;
        $this->newAddressLine = '';
    }

    public function setWarrantyDays(int $days)
    {
        $this->warrantyDays = $days;
        $this->warrantyTerms = "{$days}-day inspection and replacement warranty";
    }

    public function submitPackageOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit a negotiated offer.');
            return redirect()->route('login');
        }

        if (empty($this->selectedItemIds)) {
            session()->flash('error', 'Please select at least one item to include in your offer.');
            return;
        }

        $sellerId = (int) $this->sellerId;
        $selectedItems = collect($this->cartItems)
            ->filter(fn ($i) => in_array($i['id'], $this->selectedItemIds))
            ->values();

        $originalSubtotal = $selectedItems->sum(fn ($i) => $i['price'] * $i['quantity']);
        $proposedNum = (float) str_replace(',', '', $this->proposedPrice);
        $discount = max(0, $originalSubtotal - $proposedNum);

        $customItems = [];
        foreach ($selectedItems as $sItem) {
            $customItems[] = [
                'listing_id' => $sItem['listing_id'],
                'description' => $sItem['title'],
                'type' => 'item',
                'quantity' => $sItem['quantity'],
                'unit_price' => $sItem['price'],
                'warranty_days' => $this->warrantyDays,
                'warranty_terms' => $this->warrantyTerms,
            ];
        }

        $offer = app(NegotiationService::class)->createOfferFromCart($user, $sellerId, [
            'delivery_method' => $this->deliveryMode === 'seller_delivery' ? 'seller_responsible' : 'buyer_responsible',
            'discount' => $discount,
            'terms' => $this->offerNote ?: 'Custom negotiated offer proposal',
            'warranty_days' => $this->warrantyDays,
            'warranty_terms' => $this->warrantyTerms,
            'items' => $this->selectedItemIds,
            'custom_items' => $customItems,
            'request_repair' => $this->requestRepair,
            'repair_service_type' => $this->repairServiceType,
            'repair_details' => $this->repairDetails,
        ]);

        $this->isOpen = false;
        session()->flash('message', "Custom offer submitted successfully to {$this->sellerName}! Ref: OFF-{$offer->id}");

        return redirect()->route('offers');
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $selectedSubtotal = collect($this->cartItems)
            ->filter(fn ($i) => in_array($i['id'], $this->selectedItemIds))
            ->sum(fn ($i) => $i['price'] * $i['quantity']);

        $proposedNum = (float) str_replace(',', '', $this->proposedPrice);
        $savings = max(0, $selectedSubtotal - $proposedNum);

        return view('livewire.components.offers.make-offer', [
            'selectedSubtotal' => $selectedSubtotal,
            'savings' => $savings,
        ]);
    }
}