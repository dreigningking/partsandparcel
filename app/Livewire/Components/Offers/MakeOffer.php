<?php

namespace App\Livewire\Components\Offers;

use App\Models\Cart;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class MakeOffer extends Component
{
    public bool $isOpen = false;
    public ?string $sellerId = null;
    public string $sellerName = 'Seller';
    public ?string $cartId = null;
    public ?int $listingId = null;

    // Wizard state
    public int $currentStep = 1;
    public int $totalSteps = 3;

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

    // Itemized list of items included in this offer
    public array $offerItems = [];

    // Negotiation & Shipping Capability Flags
    public bool $allowShipping = true;

    // Optional repair / services (Step N + 2)
    public bool $requestRepair = false;
    public string $repairServiceType = 'Installation & Testing';
    public string $repairDetails = '';
    public string $offerNote = '';

    #[On('open-make-offer')]
    public function loadOfferDrawer($payload = null): void
    {
        // Reset state
        $this->cartId = null;
        $this->sellerId = null;
        $this->listingId = null;
        $this->offerItems = [];

        // Extract payload parameters
        if (is_array($payload)) {
            $this->sellerId = isset($payload['seller_id']) ? (string) $payload['seller_id'] : null;
            $this->sellerName = $payload['seller_name'] ?? 'Seller';
            $this->listingId = isset($payload['listing_id']) ? (int) $payload['listing_id'] : null;
            $this->cartId = isset($payload['cart_id']) ? (string) $payload['cart_id'] : null;
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

        // Load saved addresses for user
        $this->loadAddresses();

        // Load items: from single listing OR from buyer's cart for this seller
        $this->loadItems();

        // Dynamically compute wizard steps: Step 1..N for items, Step N+1 for Shipment, Step N+2 for Special Request
        $itemCount = count($this->offerItems);
        $this->totalSteps = max(3, $itemCount + 2);

        $this->currentStep = 1;
        $this->isOpen = true;
    }

    public function loadAddresses(): void
    {
        $user = Auth::user();
        if ($user) {
            $addresses = Location::with('state')->where('user_id', $user->id)->get();
            if ($addresses->isNotEmpty()) {
                $this->savedAddresses = $addresses->map(fn ($a) => [
                    'id' => $a->id,
                    'label' => $a->label ?: ($a->name ?: 'Address'),
                    'address' => $a->address_line_1,
                    'city' => $a->city,
                    'state' => $a->state?->name ?? (is_string($a->state) ? $a->state : ''),
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

    public function loadItems(): void
    {
        $user = Auth::user();
        $this->offerItems = [];

        // Case 1: Triggered from a single listing details page
        if ($this->listingId) {
            $listing = Listing::with(['user', 'item.media', 'media'])->find($this->listingId);
            if ($listing) {
                $this->sellerId = (string) $listing->user_id;
                $this->sellerName = $listing->user?->business_name ?: ($listing->user?->name ?: $this->sellerName);

                $listingDays = $listing->warranty_period_days;
                $listingTerms = $listing->warranty_terms ?: ($listingDays ? "{$listingDays}-day replacement and inspection warranty" : 'Standard inspection warranty');

                $this->offerItems = [
                    [
                        'id' => 1,
                        'listing_id' => $listing->id,
                        'title' => $listing->title ?? ($listing->item?->name ?? 'Listing #' . $listing->id),
                        'specs' => $listing->condition ? ucfirst($listing->condition) : 'Tested',
                        'price' => (float) $listing->price,
                        'quantity' => 1,
                        'icon' => '📦',
                        'is_negotiable' => (bool) $listing->is_negotiable,
                        'proposed_price' => (string) $listing->price,
                        'is_warranty_negotiable' => (bool) $listing->is_warranty_negotiable,
                        'listing_warranty_days' => $listingDays,
                        'listing_warranty_terms' => $listingTerms,
                        'proposed_warranty_days' => $listingDays ?? 14,
                        'proposed_warranty_terms' => $listingTerms,
                        'allow_shipping' => (bool) $listing->allow_shipping,
                    ]
                ];

                $this->allowShipping = (bool) $listing->allow_shipping;
                if (! $this->allowShipping) {
                    $this->deliveryMode = 'pickup';
                }
                return;
            }
        }

        // Case 2: From Cart for a specific seller (via cart_id or seller_id)
        if ($user) {
            $cartQuery = Cart::with(['items.listing.item', 'items.listing.media'])
                ->where('buyer_id', $user->id)
                ->where('status', 'active');

            if ($this->cartId && is_numeric($this->cartId)) {
                $cartQuery->where('id', (int) $this->cartId);
            } elseif ($this->sellerId && is_numeric($this->sellerId)) {
                $cartQuery->where('seller_id', (int) $this->sellerId);
            }

            $cart = $cartQuery->first();

            if ($cart && $cart->items->isNotEmpty()) {
                foreach ($cart->items as $item) {
                    $listing = $item->listing;
                    $listingDays = $listing?->warranty_period_days;
                    $listingTerms = $listing?->warranty_terms ?: ($listingDays ? "{$listingDays}-day replacement and inspection warranty" : 'Standard inspection warranty');

                    $this->offerItems[] = [
                        'id' => $item->id,
                        'listing_id' => $item->listing_id,
                        'title' => $listing?->title ?? ($listing?->item?->name ?? "Item #{$item->id}"),
                        'specs' => $listing?->condition ? ucfirst($listing->condition) : 'Tested',
                        'price' => (float) $item->unit_price,
                        'quantity' => (int) $item->quantity,
                        'icon' => $listing?->item?->item_type === 'part' ? '⚙️' : ($listing?->item?->item_type === 'scrap' ? '🛠️' : '💻'),
                        'is_negotiable' => (bool) ($listing?->is_negotiable ?? true),
                        'proposed_price' => (string) $item->unit_price,
                        'is_warranty_negotiable' => (bool) ($listing?->is_warranty_negotiable ?? false),
                        'listing_warranty_days' => $listingDays,
                        'listing_warranty_terms' => $listingTerms,
                        'proposed_warranty_days' => $listingDays ?? 14,
                        'proposed_warranty_terms' => $listingTerms,
                        'allow_shipping' => (bool) ($listing?->allow_shipping ?? true),
                    ];
                }

                $this->allowShipping = collect($this->offerItems)->contains(fn ($i) => !empty($i['allow_shipping']));
                if (! $this->allowShipping) {
                    $this->deliveryMode = 'pickup';
                }
                return;
            }
        }

        // Case 3: Guest cart session for this seller
        $guestCart = Session::get('guest_cart', []);
        $sellerKey = $this->sellerId ?? '';
        if (isset($guestCart[$sellerKey]) && ! empty($guestCart[$sellerKey])) {
            $sellerItems = $guestCart[$sellerKey];
            $listingIds = array_keys($sellerItems);
            $listings = Listing::with(['item', 'media'])->whereIn('id', $listingIds)->get()->keyBy('id');

            foreach ($sellerItems as $lid => $itemData) {
                $listing = $listings->get($lid);
                if (! $listing) continue;

                $listingDays = $listing->warranty_period_days;
                $listingTerms = $listing->warranty_terms ?: ($listingDays ? "{$listingDays}-day replacement and inspection warranty" : 'Standard inspection warranty');

                $this->offerItems[] = [
                    'id' => 'guest_' . $lid,
                    'listing_id' => $listing->id,
                    'title' => $listing->title ?? ($listing->item?->name ?? "Item #{$listing->id}"),
                    'specs' => $listing->condition ? ucfirst($listing->condition) : 'Standard',
                    'price' => (float) ($itemData['unit_price'] ?? $listing->price),
                    'quantity' => (int) ($itemData['quantity'] ?? 1),
                    'icon' => $listing->item?->item_type === 'part' ? '⚙️' : ($listing->item?->item_type === 'scrap' ? '🛠️' : '💻'),
                    'is_negotiable' => (bool) $listing->is_negotiable,
                    'proposed_price' => (string) ($itemData['unit_price'] ?? $listing->price),
                    'is_warranty_negotiable' => (bool) $listing->is_warranty_negotiable,
                    'listing_warranty_days' => $listingDays,
                    'listing_warranty_terms' => $listingTerms,
                    'proposed_warranty_days' => $listingDays ?? 14,
                    'proposed_warranty_terms' => $listingTerms,
                    'allow_shipping' => (bool) $listing->allow_shipping,
                ];
            }

            $this->allowShipping = collect($this->offerItems)->contains(fn ($i) => !empty($i['allow_shipping']));
            if (! $this->allowShipping) {
                $this->deliveryMode = 'pickup';
            }
            return;
        }

        // Fallback demo items if no DB or session items found
        $this->offerItems = [
            [
                'id' => 101,
                'listing_id' => null,
                'title' => 'HP EliteBook 840 G5 Motherboard',
                'specs' => 'Tested Working · Grade A',
                'price' => 85000,
                'quantity' => 1,
                'icon' => '💻',
                'is_negotiable' => true,
                'proposed_price' => '80000',
                'is_warranty_negotiable' => true,
                'listing_warranty_days' => 14,
                'listing_warranty_terms' => '14-day replacement and inspection warranty',
                'proposed_warranty_days' => 14,
                'proposed_warranty_terms' => '14-day replacement and inspection warranty',
                'allow_shipping' => true,
            ]
        ];
        $this->allowShipping = true;
    }

    public function setItemWarrantyDays(int $index, int $days): void
    {
        if (! isset($this->offerItems[$index])) return;

        if (! ($this->offerItems[$index]['is_warranty_negotiable'] ?? false)) {
            return;
        }

        $this->offerItems[$index]['proposed_warranty_days'] = $days;
        $this->offerItems[$index]['proposed_warranty_terms'] = "{$days}-day inspection and replacement warranty";
    }

    public function nextStep(): void
    {
        $itemCount = count($this->offerItems);

        // If currently on an item step (1..N)
        if ($this->currentStep <= $itemCount) {
            $itemIdx = $this->currentStep - 1;
            $item = $this->offerItems[$itemIdx];

            if ($item['is_negotiable']) {
                $proposed = (float) str_replace(',', '', (string) ($item['proposed_price'] ?? 0));
                if ($proposed <= 0) {
                    session()->flash('error', "Please enter a valid proposed price for {$item['title']}.");
                    return;
                }
            }

            if ($item['is_warranty_negotiable']) {
                $days = (int) ($item['proposed_warranty_days'] ?? 0);
                if ($days < 0) {
                    session()->flash('error', "Please enter valid warranty days for {$item['title']}.");
                    return;
                }
            }
        }

        // If currently on the Shipment step (N+1)
        if ($this->currentStep === $itemCount + 1) {
            if ($this->deliveryMode === 'seller_delivery' && empty($this->deliveryAddressId)) {
                session()->flash('error', 'Please select or add a delivery destination address.');
                return;
            }
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    public function saveNewAddress(): void
    {
        if (trim($this->newAddressLine) === '') {
            return;
        }

        $user = Auth::user();
        if ($user) {
            $location = Location::create([
                'user_id' => $user->id,
                'name' => $this->newAddressLabel,
                'label' => $this->newAddressLabel,
                'address_line_1' => $this->newAddressLine,
                'city' => $this->newCity,
                'state_id' => null,
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

    public function submitPackageOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit a negotiated offer.');
            return redirect()->route('login');
        }

        if (empty($this->offerItems)) {
            session()->flash('error', 'No items found in this cart to submit an offer for.');
            return;
        }

        $sellerId = (int) $this->sellerId;
        $originalSubtotal = collect($this->offerItems)->sum(fn ($i) => (float) $i['price'] * (int) $i['quantity']);
        $proposedSubtotal = collect($this->offerItems)->sum(fn ($i) => (float) str_replace(',', '', (string) $i['proposed_price']) * (int) $i['quantity']);
        $discount = max(0, $originalSubtotal - $proposedSubtotal);

        if (! $this->allowShipping) {
            $this->deliveryMode = 'pickup';
        }

        $customItems = [];
        foreach ($this->offerItems as $sItem) {
            $customItems[] = [
                'listing_id' => $sItem['listing_id'],
                'description' => $sItem['title'],
                'type' => 'item',
                'quantity' => (int) $sItem['quantity'],
                'unit_price' => (float) str_replace(',', '', (string) $sItem['proposed_price']),
                'warranty_period_days' => (int) ($sItem['proposed_warranty_days'] ?? 14),
                'warranty_terms' => $sItem['proposed_warranty_terms'] ?? 'Standard warranty terms',
            ];
        }

        $maxWarranty = collect($this->offerItems)->max('proposed_warranty_days') ?? 14;

        $offer = app(NegotiationService::class)->createOfferFromCart($user, $sellerId, [
            'delivery_method' => $this->deliveryMode === 'seller_delivery' ? 'seller_responsible' : 'buyer_responsible',
            'discount' => $discount,
            'terms' => $this->offerNote ?: 'Custom negotiated offer proposal',
            'warranty_days' => $maxWarranty,
            'warranty_terms' => "Includes agreed item warranties",
            'custom_items' => $customItems,
            'request_repair' => $this->requestRepair,
            'repair_service_type' => $this->repairServiceType,
            'repair_details' => $this->repairDetails,
        ]);

        $this->isOpen = false;
        session()->flash('message', "Custom offer submitted successfully to {$this->sellerName}! Ref: OFF-{$offer->id}");

        return redirect()->route('offers');
    }

    public function closeDrawer(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $originalSubtotal = collect($this->offerItems)->sum(fn ($i) => (float) $i['price'] * (int) $i['quantity']);
        $proposedSubtotal = collect($this->offerItems)->sum(fn ($i) => (float) str_replace(',', '', (string) $i['proposed_price']) * (int) $i['quantity']);
        $savings = max(0, $originalSubtotal - $proposedSubtotal);

        return view('livewire.components.offers.make-offer', [
            'originalSubtotal' => $originalSubtotal,
            'proposedSubtotal' => $proposedSubtotal,
            'savings' => $savings,
            'itemCount' => count($this->offerItems),
        ]);
    }
}