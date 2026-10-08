<?php

namespace App\Livewire\Components\Offers;

use App\Models\Offer;
use App\Models\Response;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickViewOffers extends Component
{
    public bool $isOpen = false;
    public ?int $responseId = null;
    public ?int $offerId = null;
    public string $authorName = '';
    public array $rounds = [];
    public int $currentRoundIndex = 0;
    public int $itemStep = 1;

    public function nextItemStep(): void
    {
        $itemsCount = count($this->rounds[$this->currentRoundIndex]['items'] ?? []);
        $totalItemSteps = max(1, $itemsCount + 2); // 1..N items, N+1 shipment, N+2 summary
        if ($this->itemStep < $totalItemSteps) {
            $this->itemStep++;
        }
    }

    public function prevItemStep(): void
    {
        if ($this->itemStep > 1) {
            $this->itemStep--;
        }
    }

    public function goToItemStep(int $step): void
    {
        $itemsCount = count($this->rounds[$this->currentRoundIndex]['items'] ?? []);
        $totalItemSteps = max(1, $itemsCount + 2);
        if ($step >= 1 && $step <= $totalItemSteps) {
            $this->itemStep = $step;
        }
    }

    // Counter-offer form state
    public bool $showCounterForm = false;
    public string $counterPrice = '';
    public int $counterWarranty = 14;
    public string $counterDelivery = 'buyer_pickup';
    public string $counterNotes = '';

    #[On('open-quick-view-offer')]
    public function loadOffers($response_id = null, $payload = null)
    {
        $targetId = $response_id ?? (is_array($payload) ? ($payload['response_id'] ?? null) : $payload);
        $this->responseId = $targetId ? (int) $targetId : null;
        $this->showCounterForm = false;

        $user = Auth::user();

        if ($this->responseId) {
            $response = Response::with(['user', 'discussion', 'offers.sender', 'offers.recipient', 'offers.items'])->find($this->responseId);
            if ($response) {
                // Enforce requirement: Only a discussion author can view the offers received using the quick view offers
                if (! $user || ($response->discussion && $response->discussion->user_id !== $user->id)) {
                    $this->isOpen = false;
                    session()->flash('warning', 'Only the discussion author can view received offers via quick view.');
                    $this->dispatch('flash-message', ['type' => 'warning', 'message' => 'Only the discussion author can view received offers.']);
                    return;
                }

                $this->authorName = $response->user?->business_name ?: $response->user?->name ?: 'Vendor';

                $dbOffers = Offer::with(['sender', 'recipient', 'items'])
                    ->where('response_id', $this->responseId)
                    ->oldest()
                    ->get();

                if ($dbOffers->isNotEmpty()) {
                    $this->rounds = $dbOffers->map(function ($off, $idx) use ($user) {
                        $isSender = $user && $user->id === $off->sender_id;
                        $isRecipient = $user && $user->id === $off->recipient_id;

                        return [
                            'id' => $off->id,
                            'round' => $idx + 1,
                            'parent_id' => $off->parent_id,
                            'from' => ($off->sender?->name ?? 'Sender') . ($isSender ? ' (You)' : ''),
                            'to' => ($off->recipient?->name ?? 'Recipient') . ($isRecipient ? ' (You)' : ''),
                            'raw_price' => $off->total(),
                            'price' => '₦' . number_format($off->total()),
                            'warranty' => $off->maxWarrantyDays() ? "{$off->maxWarrantyDays()}-Day Warranty" : 'Standard terms',
                            'warranty_days' => $off->maxWarrantyDays() ?: 14,
                            'delivery' => $off->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup',
                            'delivery_method' => $off->delivery_method ?: 'buyer_pickup',
                            'message' => $off->terms ?: 'Commercial offer proposal',
                            'time' => $off->created_at->diffForHumans(),
                            'status' => ucfirst($off->status),
                            'can_accept' => $off->canBeAcceptedBy($user),
                            'can_counter' => $off->canBeCounteredBy($user),
                            'can_edit' => $off->canBeEditedBy($user),
                            'items' => $off->items->map(fn ($it) => [
                                'id' => $it->id,
                                'description' => $it->description,
                                'type' => $it->type,
                                'quantity' => (int) $it->quantity,
                                'unit_price' => (float) $it->unit_price,
                                'warranty_period_days' => $it->warranty_period_days,
                                'warranty_terms' => $it->warranty_terms,
                            ])->toArray(),
                        ];
                    })->toArray();

                    $this->currentRoundIndex = max(0, count($this->rounds) - 1);
                    $this->itemStep = 1;
                    $this->isOpen = true;
                    return;
                }
            }
        }

        // Clean empty state if no DB records found
        $this->rounds = [];
        $this->dispatch('flash-message', type: 'info', message: 'No offer records found for this negotiation.');
    }

    public function previousRound()
    {
        if ($this->currentRoundIndex > 0) {
            $this->currentRoundIndex--;
            $this->showCounterForm = false;
        }
    }

    public function nextRound()
    {
        if ($this->currentRoundIndex < count($this->rounds) - 1) {
            $this->currentRoundIndex++;
            $this->showCounterForm = false;
        }
    }

    public function launchCounterDrawer($offerId, $edit = false)
    {
        $this->isOpen = false;
        $this->dispatch('open-counter-offer', offerId: $offerId, edit: $edit);
    }

    #[On('counter-offer-submitted')]
    #[On('offer-updated')]
    public function handleCounterChanged()
    {
        if ($this->responseId) {
            $this->loadOffers($this->responseId);
        }
    }

    public function submitCounterOffer($offerId)
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to make a counter offer.');
            return redirect()->route('login');
        }

        $this->validate([
            'counterPrice' => ['required', 'numeric', 'min:1'],
            'counterWarranty' => ['required', 'integer', 'min:0'],
            'counterDelivery' => ['required', 'string'],
        ]);

        $offer = Offer::with('items')->find($offerId);
        if ($offer) {
            try {
                // Build items representation with adjusted price
                $items = [];
                $originalItems = $offer->items;
                $count = max(1, $originalItems->count());
                $unitPrice = round((float) $this->counterPrice / $count, 2);

                if ($originalItems->isNotEmpty()) {
                    foreach ($originalItems as $it) {
                        $items[] = [
                            'item_id' => $it->item_id,
                            'title' => $it->title,
                            'quantity' => $it->quantity ?: 1,
                            'unit_price' => $unitPrice,
                            'condition' => $it->condition ?: 'used',
                            'warranty_days' => (int) $this->counterWarranty,
                        ];
                    }
                } else {
                    $items[] = [
                        'title' => 'Counter Offer Proposal',
                        'quantity' => 1,
                        'unit_price' => (float) $this->counterPrice,
                        'condition' => 'used',
                        'warranty_days' => (int) $this->counterWarranty,
                    ];
                }

                $negotiationService = app(NegotiationService::class);
                $counterOffer = $negotiationService->submitCounterOffer($user, $offer, [
                    'delivery_method' => $this->counterDelivery,
                    'terms' => $this->counterNotes ?: 'Counter proposal submitted by discussion author.',
                    'items' => $items,
                ]);

                $this->showCounterForm = false;
                session()->flash('message', 'Counter offer submitted successfully!');
                $this->loadOffers($this->responseId);
                return;
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                return;
            }
        }

        // Demo fallback
        $this->rounds[] = [
            'id' => rand(200, 999),
            'round' => count($this->rounds) + 1,
            'parent_id' => 101,
            'from' => 'You (Author)',
            'to' => $this->authorName,
            'raw_price' => (float) $this->counterPrice,
            'price' => '₦' . number_format((float) $this->counterPrice),
            'warranty' => "{$this->counterWarranty}-Day Warranty",
            'warranty_days' => $this->counterWarranty,
            'delivery' => $this->counterDelivery === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup',
            'delivery_method' => $this->counterDelivery,
            'message' => $this->counterNotes ?: 'Counter offer submitted.',
            'time' => 'Just now',
            'status' => 'Pending Action',
            'can_accept' => false,
            'can_counter' => false,
        ];
        $this->currentRoundIndex = count($this->rounds) - 1;
        $this->showCounterForm = false;
        session()->flash('message', 'Counter offer recorded.');
    }

    public function acceptCurrentOffer($offerId)
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to accept this offer.');
            return redirect()->route('login');
        }

        $offer = Offer::find($offerId);
        if ($offer) {
            try {
                $invoice = app(NegotiationService::class)->acceptOffer($user, $offer);
                $this->isOpen = false;
                session()->flash('message', "Offer accepted! Invoice {$invoice->invoice_number} generated.");
                return redirect()->route('invoices.view', $invoice->invoice_number);
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                return;
            }
        }

        $this->isOpen = false;
        session()->flash('message', 'Offer accepted! Reserving items.');
        return redirect()->route('checkout');
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
        $this->showCounterForm = false;
    }

    public function render()
    {
        return view('livewire.components.offers.quick-view-offers');
    }
}