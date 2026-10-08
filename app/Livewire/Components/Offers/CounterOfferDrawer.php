<?php

namespace App\Livewire\Components\Offers;

use App\Models\Offer;
use App\Models\OfferItem;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CounterOfferDrawer extends Component
{
    public bool $isOpen = false;
    public bool $isEditMode = false;
    public ?int $offerId = null;

    // Wizard navigation
    public int $currentStep = 1;
    public int $totalSteps = 1;

    // Items list for step-by-step counter
    public array $items = [];

    // Shipment fees
    public bool $hasPickup = false;
    public ?int $pickupItemId = null;
    public float $originalPickupFee = 0.00;
    public ?float $counterPickupFee = null;

    public bool $hasDelivery = false;
    public ?int $deliveryItemId = null;
    public float $originalDeliveryFee = 0.00;
    public ?float $counterDeliveryFee = null;

    // Special service requests added during counter
    public array $specialRequests = [];
    public string $newServiceTitle = '';
    public string $newServicePrice = '';
    public int $newServiceWarrantyDays = 14;

    // Notes and terms
    public string $counterNotes = '';
    public float $counterDiscount = 0.00;
    public string $deliveryMethod = 'seller_responsible';

    // Summary calculations
    public float $originalTotal = 0.00;
    public float $counterTotal = 0.00;

    #[On('open-counter-offer')]
    public function openDrawer($offerId, $edit = false)
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to negotiate offers.');
            return redirect()->route('login');
        }

        $this->offerId = (int) $offerId;
        $this->isEditMode = (bool) $edit;

        $offer = Offer::with(['items.listing', 'sender', 'recipient'])->find($this->offerId);
        if (! $offer) {
            $this->dispatch('flash-message', type: 'error', message: 'Offer record not found.');
            return;
        }

        // Verify permissions
        if ($this->isEditMode) {
            if (! $offer->canBeEditedBy($user)) {
                $this->dispatch('flash-message', type: 'error', message: 'This offer cannot be edited because a counter-offer exists or it has already been resolved.');
                return;
            }
        } else {
            if (! $offer->canBeCounteredBy($user)) {
                $this->dispatch('flash-message', type: 'error', message: 'Only the recipient of the active pending offer can submit a counter-offer.');
                return;
            }
        }

        $this->loadOfferData($offer);
        $this->currentStep = 1;
        $this->isOpen = true;
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
        $this->reset([
            'offerId', 'isEditMode', 'currentStep', 'totalSteps',
            'items', 'hasPickup', 'pickupItemId', 'originalPickupFee', 'counterPickupFee',
            'hasDelivery', 'deliveryItemId', 'originalDeliveryFee', 'counterDeliveryFee',
            'specialRequests', 'newServiceTitle', 'newServicePrice',
            'counterNotes', 'counterDiscount', 'originalTotal', 'counterTotal'
        ]);
    }

    protected function loadOfferData(Offer $offer)
    {
        $this->items = [];
        $this->hasPickup = false;
        $this->hasDelivery = false;
        $this->specialRequests = [];
        $this->counterDiscount = (float) $offer->discount;
        $this->counterNotes = $offer->terms ?? '';
        $this->deliveryMethod = $offer->delivery_method ?? 'seller_responsible';

        foreach ($offer->items as $item) {
            if ($item->type === 'pickup') {
                $this->hasPickup = true;
                $this->pickupItemId = $item->id;
                $this->originalPickupFee = (float) $item->unit_price;
                $this->counterPickupFee = (float) $item->unit_price;
            } elseif ($item->type === 'delivery') {
                $this->hasDelivery = true;
                $this->deliveryItemId = $item->id;
                $this->originalDeliveryFee = (float) $item->unit_price;
                $this->counterDeliveryFee = (float) $item->unit_price;
            } else {
                $listing = $item->listing;
                $this->items[] = [
                    'id' => $item->id,
                    'listing_id' => $item->listing_id,
                    'title' => $item->description,
                    'type' => $item->type, // 'item' or 'service'
                    'quantity' => (int) $item->quantity,
                    'original_price' => (float) $item->unit_price,
                    'counter_price' => (float) $item->unit_price,
                    'original_warranty_days' => $item->warranty_period_days ?? 14,
                    'counter_warranty_days' => $item->warranty_period_days ?? 14,
                    'original_warranty_terms' => $item->warranty_terms ?? '',
                    'counter_warranty_terms' => $item->warranty_terms ?? '',
                    'is_negotiable' => $listing ? (bool) $listing->is_negotiable : true,
                    'is_warranty_negotiable' => $listing ? (bool) $listing->is_warranty_negotiable : true,
                ];
            }
        }

        $this->calculateTotalSteps();
        $this->recalculateTotals();
    }

    protected function calculateTotalSteps()
    {
        // Steps count = count of regular items + (1 if pickup) + (1 if delivery) + 1 for Special Requests & Review
        $steps = count($this->items);
        if ($this->hasPickup) {
            $steps++;
        }
        if ($this->hasDelivery) {
            $steps++;
        }
        $steps++; // Special requests / review step
        $this->totalSteps = max(1, $steps);
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->recalculateTotals();
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->recalculateTotals();
        }
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->recalculateTotals();
        }
    }

    public function addSpecialRequest()
    {
        if (trim($this->newServiceTitle) === '') {
            return;
        }

        $price = (float) str_replace(',', '', $this->newServicePrice);
        $this->specialRequests[] = [
            'description' => trim($this->newServiceTitle),
            'type' => 'service',
            'quantity' => 1,
            'unit_price' => $price,
            'warranty_period_days' => $this->newServiceWarrantyDays,
            'warranty_terms' => "{$this->newServiceWarrantyDays}-day service workmanship warranty",
        ];

        $this->newServiceTitle = '';
        $this->newServicePrice = '';
        $this->recalculateTotals();
    }

    public function removeSpecialRequest(int $index)
    {
        if (isset($this->specialRequests[$index])) {
            unset($this->specialRequests[$index]);
            $this->specialRequests = array_values($this->specialRequests);
            $this->recalculateTotals();
        }
    }

    public function addNewItemToCounter(): void
    {
        $this->items[] = [
            'id' => 'new_' . uniqid(),
            'listing_id' => null,
            'title' => 'Additional Item / Component',
            'type' => 'item',
            'quantity' => 1,
            'original_price' => 0.00,
            'counter_price' => 0.00,
            'original_warranty_days' => 14,
            'counter_warranty_days' => 14,
            'original_warranty_terms' => '14-day replacement warranty',
            'counter_warranty_terms' => '14-day replacement warranty',
            'is_negotiable' => true,
            'is_warranty_negotiable' => true,
            'is_new' => true,
        ];
        $this->calculateTotalSteps();
        $this->currentStep = count($this->items);
        $this->recalculateTotals();
    }

    public function removeItemFromCounter(int $index): void
    {
        if (isset($this->items[$index]) && ! empty($this->items[$index]['is_new'])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calculateTotalSteps();
            $this->currentStep = max(1, min($this->currentStep, $this->totalSteps));
            $this->recalculateTotals();
        }
    }

    public function recalculateTotals()
    {
        $itemsTotal = collect($this->items)->sum(fn ($i) => (float) ($i['counter_price'] ?? 0) * (int) ($i['quantity'] ?? 1));
        $pickupTotal = $this->hasPickup ? (float) ($this->counterPickupFee ?? 0) : 0;
        $deliveryTotal = $this->hasDelivery ? (float) ($this->counterDeliveryFee ?? 0) : 0;
        $specialTotal = collect($this->specialRequests)->sum(fn ($s) => (float) ($s['unit_price'] ?? 0));

        $subtotal = $itemsTotal + $pickupTotal + $deliveryTotal + $specialTotal;
        $this->counterTotal = max(0, $subtotal - (float) $this->counterDiscount);
    }

    public function submit()
    {
        $user = Auth::user();
        $offer = Offer::find($this->offerId);
        if (! $user || ! $offer) {
            $this->dispatch('flash-message', type: 'error', message: 'Unable to process negotiation.');
            return;
        }

        // Build itemized payload
        $payloadItems = [];

        foreach ($this->items as $it) {
            $payloadItems[] = [
                'listing_id' => $it['listing_id'],
                'description' => $it['title'],
                'type' => $it['type'],
                'quantity' => (int) $it['quantity'],
                'unit_price' => (float) $it['counter_price'],
                'warranty_period_days' => (int) $it['counter_warranty_days'],
                'warranty_terms' => $it['counter_warranty_terms'],
            ];
        }

        if ($this->hasPickup && $this->counterPickupFee !== null) {
            $payloadItems[] = [
                'listing_id' => null,
                'description' => 'Pickup from Customer to Technician',
                'type' => 'pickup',
                'quantity' => 1,
                'unit_price' => (float) $this->counterPickupFee,
            ];
        }

        if ($this->hasDelivery && $this->counterDeliveryFee !== null) {
            $payloadItems[] = [
                'listing_id' => null,
                'description' => 'Delivery from Technician to Customer',
                'type' => 'delivery',
                'quantity' => 1,
                'unit_price' => (float) $this->counterDeliveryFee,
            ];
        }

        foreach ($this->specialRequests as $sr) {
            $payloadItems[] = $sr;
        }

        $negotiationService = app(NegotiationService::class);

        try {
            if ($this->isEditMode) {
                $updatedOffer = $negotiationService->editOffer($user, $offer, [
                    'items' => $payloadItems,
                    'discount' => $this->counterDiscount,
                    'terms' => $this->counterNotes,
                    'delivery_method' => $this->deliveryMethod,
                ]);

                $this->dispatch('flash-message', type: 'success', message: 'Your offer proposal has been updated successfully.');
                $this->dispatch('offer-updated', offerId: $updatedOffer->id);
            } else {
                $counterOffer = $negotiationService->submitCounterOffer($user, $offer, [
                    'items' => $payloadItems,
                    'discount' => $this->counterDiscount,
                    'terms' => $this->counterNotes,
                    'delivery_method' => $this->deliveryMethod,
                ]);

                $this->dispatch('flash-message', type: 'success', message: 'Counter-offer submitted successfully!');
                $this->dispatch('counter-offer-submitted', offerId: $counterOffer->id);
            }

            $this->closeDrawer();
        } catch (\Throwable $e) {
            $this->dispatch('flash-message', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $offer = $this->offerId ? Offer::with(['sender', 'recipient'])->find($this->offerId) : null;

        // Determine step view target
        $itemsCount = count($this->items);
        $pickupStepNum = $this->hasPickup ? $itemsCount + 1 : null;
        $deliveryStepNum = $this->hasDelivery ? ($this->hasPickup ? $itemsCount + 2 : $itemsCount + 1) : null;
        $reviewStepNum = $this->totalSteps;

        return view('livewire.components.offers.counter-offer-drawer', [
            'offer' => $offer,
            'itemsCount' => $itemsCount,
            'pickupStepNum' => $pickupStepNum,
            'deliveryStepNum' => $deliveryStepNum,
            'reviewStepNum' => $reviewStepNum,
        ]);
    }
}
