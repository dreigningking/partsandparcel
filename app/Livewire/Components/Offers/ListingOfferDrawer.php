<?php

namespace App\Livewire\Components\Offers;

use App\Models\Country;
use App\Models\Listing;
use App\Models\Location;
use App\Models\State;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ListingOfferDrawer extends Component
{
    public bool $isOpen = false;
    public int $currentStep = 1; // 1: Item, 2: Shipment, 3: Review

    // Listing Details
    public ?int $listingId = null;
    public ?int $sellerId = null;
    public string $sellerName = '';
    public string $sellerLocation = '';
    public string $listingTitle = '';
    public string $listingSpecs = '';
    public string $listingIcon = '📦';
    public ?string $listingImage = null;
    public float $askingPrice = 0.00;
    public int $maxQuantity = 1;
    public bool $isNegotiable = true;
    public bool $isWarrantyNegotiable = true;
    public bool $allowShipping = true;

    // Step 1: Item Proposal
    public string $proposedPrice = '';
    public int $quantity = 1;
    public ?int $listingWarrantyDays = 14;
    public string $listingWarrantyTerms = '';
    public int $proposedWarrantyDays = 14;
    public string $proposedWarrantyTerms = '';

    // Step 2: Shipment
    public string $deliveryMode = 'pickup'; // 'pickup' or 'seller_delivery'
    public ?int $deliveryAddressId = null;
    public array $savedAddresses = [];
    public bool $showNewAddressForm = false;
    public string $newAddressLabel = 'Home';
    public string $newAddressLine = '';
    public string $newCity = '';
    public string $newState = '';

    // Step 3: Review & Terms
    public string $offerNote = '';

    #[On('open-listing-offer')]
    #[On('open-make-offer')]
    public function loadListingOffer($listing_id = null, $seller_id = null, $seller_name = null)
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Please sign in to make an offer.');
            return redirect()->route('login');
        }

        $id = $listing_id ?? (is_array($listing_id) ? ($listing_id['listing_id'] ?? null) : null);
        if (! $id && is_array($seller_id) && isset($seller_id['listing_id'])) {
            $id = $seller_id['listing_id'];
        }

        if (! $id) {
            return;
        }

        $this->resetState();
        $this->listingId = (int) $id;

        $listing = Listing::with(['item.location', 'user.locations', 'media'])->find($this->listingId);
        if (! $listing) {
            $this->dispatch('flash-message', type: 'error', message: 'Target listing not found.');
            return;
        }

        $this->sellerId = (int) $listing->user_id;
        $this->sellerName = $listing->user?->business_name ?: ($listing->user?->name ?: ($seller_name ?: 'Seller'));
        $loc = $listing->user?->primaryLocation;
        $stateName = $loc?->state?->name ?? '';
        $this->sellerLocation = $loc ? collect([$loc->city, $stateName])->filter()->implode(', ') : 'Nigeria';

        $this->listingTitle = $listing->title ?? ($listing->item?->name ?? 'Listing #' . $listing->id);
        $this->listingSpecs = $listing->item?->condition_status ? ucfirst($listing->item->condition_status) : 'Tested Working';
        $this->listingIcon = $listing->item?->item_type === 'part' ? '⚙️' : ($listing->item?->item_type === 'scrap' ? '🛠️' : '💻');
        $this->listingImage = $listing->primaryImage?->url ?? null;
        $this->askingPrice = (float) $listing->price;
        $this->maxQuantity = max(1, $listing->availableQuantity());
        $this->quantity = 1;

        $this->isNegotiable = (bool) $listing->is_negotiable;
        $priceVal = (float) $listing->price;
        $this->proposedPrice = ($priceVal == (int) $priceVal) ? (string) (int) $priceVal : (string) $priceVal;

        $this->isWarrantyNegotiable = (bool) $listing->is_warranty_negotiable;
        $this->listingWarrantyDays = $listing->warranty_period_days ?? 14;
        $this->listingWarrantyTerms = $listing->warranty_terms ?: "{$this->listingWarrantyDays}-day replacement warranty";
        $this->proposedWarrantyDays = $this->listingWarrantyDays;
        $this->proposedWarrantyTerms = $this->listingWarrantyTerms;

        $this->allowShipping = (bool) $listing->allow_shipping;
        $this->deliveryMode = $this->allowShipping ? 'pickup' : 'pickup';

        $this->loadAddresses();

        $this->currentStep = 1;
        $this->isOpen = true;
    }

    public function resetState(): void
    {
        $this->reset([
            'listingId', 'sellerId', 'sellerName', 'sellerLocation',
            'listingTitle', 'listingSpecs', 'listingImage', 'askingPrice',
            'proposedPrice', 'quantity', 'maxQuantity', 'listingWarrantyDays',
            'listingWarrantyTerms', 'proposedWarrantyDays', 'proposedWarrantyTerms',
            'deliveryMode', 'deliveryAddressId', 'savedAddresses',
            'showNewAddressForm', 'newAddressLabel', 'newAddressLine',
            'newCity', 'newState', 'offerNote', 'currentStep'
        ]);
        $this->isNegotiable = true;
        $this->isWarrantyNegotiable = true;
        $this->allowShipping = true;
    }

    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->resetState();
    }

    public function loadAddresses(): void
    {
        $user = Auth::user();
        if ($user) {
            $locations = $user->locations()->with('state')->get();
            $this->savedAddresses = $locations->map(fn ($l) => [
                'id' => $l->id,
                'label' => $l->label ?: ($l->name ?: 'Address'),
                'address' => $l->address_line_1,
                'city' => $l->city,
                'state' => $l->state?->name ?? '',
            ])->toArray();

            if (! empty($this->savedAddresses)) {
                $this->deliveryAddressId = $this->savedAddresses[0]['id'];
                $this->showNewAddressForm = false;
            } else {
                $this->deliveryAddressId = null;
                $this->showNewAddressForm = true;
            }
        }
    }

    public function setWarrantyDays(int $days): void
    {
        if (! $this->isWarrantyNegotiable) {
            return;
        }
        $this->proposedWarrantyDays = $days;
        $this->proposedWarrantyTerms = "{$days}-day inspection and replacement warranty";
    }

    public function saveNewAddress(): void
    {
        $this->validate([
            'newAddressLine' => 'required|string|min:5',
        ]);

        $user = Auth::user();
        if ($user) {
            $stateId = null;
            if ($this->newState) {
                $stateId = State::where('country_id', $user->country_id)
                    ->where(function ($q) {
                        $q->where('name', $this->newState)->orWhere('code', $this->newState);
                    })->value('id');
            }

            $countryId = $user->country_id ?? Country::where('is_default', true)->value('id');

            $location = Location::create([
                'user_id' => $user->id,
                'label' => $this->newAddressLabel ?: 'Delivery Address',
                'address_line_1' => $this->newAddressLine,
                'city' => $this->newCity ?: 'City',
                'state_id' => $stateId,
                'country_id' => $countryId,
                'is_default' => empty($this->savedAddresses),
            ]);

            $this->loadAddresses();
            $this->deliveryAddressId = $location->id;
            $this->showNewAddressForm = false;
            $this->newAddressLine = '';
        }
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'proposedPrice' => 'required|numeric|min:1',
                'quantity' => 'required|integer|min:1|max:' . max(1, $this->maxQuantity),
            ]);
            $this->currentStep = 2;
            return;
        }

        if ($this->currentStep === 2) {
            if ($this->deliveryMode === 'seller_delivery' && empty($this->deliveryAddressId) && empty($this->savedAddresses)) {
                $this->addError('deliveryAddressId', 'Please provide or save a delivery address.');
                return;
            }
            $this->currentStep = 3;
            return;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 3) {
            $this->currentStep = $step;
        }
    }

    public function submitOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit an offer.');
            return redirect()->route('login');
        }

        $listing = Listing::find($this->listingId);
        if (! $listing) {
            $this->dispatch('flash-message', type: 'error', message: 'Target listing not found.');
            return;
        }

        $unitPrice = (float) str_replace(',', '', (string) $this->proposedPrice);
        if (! $this->isNegotiable) {
            $unitPrice = (float) $listing->price;
        }

        $originalSubtotal = (float) $listing->price * $this->quantity;
        $proposedSubtotal = $unitPrice * $this->quantity;
        $discount = max(0, round($originalSubtotal - $proposedSubtotal, 2));

        $deliveryMethod = ($this->deliveryMode === 'seller_delivery') ? 'seller_responsible' : 'buyer_responsible';

        try {
            $negotiationService = app(NegotiationService::class);
            $offer = $negotiationService->createOfferFromListing($user, $listing, [
                'price' => $unitPrice,
                'quantity' => $this->quantity,
                'discount' => $discount,
                'warranty_days' => $this->proposedWarrantyDays,
                'warranty_terms' => $this->proposedWarrantyTerms,
                'delivery_method' => $deliveryMethod,
                'terms' => $this->offerNote,
            ]);

            $this->closeDrawer();
            session()->flash('message', 'Offer proposal submitted successfully!');
            return redirect()->route('offers');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $cleanPrice = (float) str_replace(',', '', (string) $this->proposedPrice);
        $proposedTotal = $cleanPrice * max(1, $this->quantity);
        $originalTotal = $this->askingPrice * max(1, $this->quantity);
        $savings = max(0, $originalTotal - $proposedTotal);

        return view('livewire.components.offers.listing-offer-drawer', [
            'proposedTotal' => $proposedTotal,
            'originalTotal' => $originalTotal,
            'savings' => $savings,
        ]);
    }
}
