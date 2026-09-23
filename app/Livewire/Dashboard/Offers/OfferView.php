<?php

namespace App\Livewire\Dashboard\Offers;

use App\Models\Offer;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OfferView extends Component
{
    public $offerId = 'OFF-9021';
    public $offer = null;
    public $showCounterDrawer = false;
    public $expandedRound = null;

    // Counter-Offer Builder Inputs
    public $counterPrice = 0;
    public $counterDiscount = 0;
    public $warrantyPeriod = 14;
    public $warrantyTerms = '14-day replacement and testing warranty.';
    public $counterNotes = '';

    public function mount($offer_id = null)
    {
        $idParam = $offer_id ?? request()->route('offer_id') ?? request()->query('id', 'OFF-9021');
        $this->offerId = (string) $idParam;
        $this->loadOffer();
    }

    public function loadOffer()
    {
        $cleanId = preg_replace('/[^0-9]/', '', $this->offerId);
        if ($cleanId && is_numeric($cleanId)) {
            $dbOffer = Offer::with(['sender.primaryLocation', 'recipient.primaryLocation', 'items.listing', 'parent.items', 'counterOffers.items', 'cart', 'discussion'])
                ->find($cleanId);

            if ($dbOffer) {
                $this->offer = $dbOffer;
                $this->counterPrice = (float) $dbOffer->total();
                $this->counterDiscount = (float) $dbOffer->discount;
                $this->warrantyPeriod = $dbOffer->maxWarrantyDays() ?: 14;
                $this->warrantyTerms = $dbOffer->items->first()?->warranty_terms ?? "{$this->warrantyPeriod}-day warranty";
                $this->counterNotes = $dbOffer->terms ? "Countering: {$dbOffer->terms}" : '';
            }
        }
    }

    public function getNegotiationRounds(): array
    {
        if (! $this->offer) {
            // Fallback sample negotiation history if viewing demo
            return [
                [
                    'id' => 9019,
                    'round_number' => 1,
                    'sender' => 'TechSam (Buyer)',
                    'recipient' => 'Adam Computers (Seller)',
                    'is_current' => false,
                    'total' => 250000,
                    'discount' => 0,
                    'warranty_days' => 14,
                    'delivery_method' => 'buyer_responsible',
                    'terms' => 'Offering ₦250,000 for the bare HP EliteBook 840 G5 laptop.',
                    'items' => collect([
                        (object) [
                            'description' => 'HP EliteBook 840 G5 Laptop',
                            'type' => 'item',
                            'quantity' => 1,
                            'unit_price' => 250000,
                            'warranty_period_days' => 14,
                            'warranty_terms' => 'Basic testing warranty',
                        ]
                    ]),
                    'status' => 'countered',
                    'time' => '3 hours ago',
                ],
                [
                    'id' => 9020,
                    'round_number' => 2,
                    'sender' => 'TechSam (Buyer)',
                    'recipient' => 'Adam Computers (Seller)',
                    'is_current' => false,
                    'total' => 255000,
                    'discount' => 0,
                    'warranty_days' => 14,
                    'delivery_method' => 'buyer_responsible',
                    'terms' => 'Can we do ₦255,000 for the laptop and RAM upgrade? I will handle pickup myself.',
                    'items' => collect([
                        (object) [
                            'description' => 'HP EliteBook 840 G5 Laptop',
                            'type' => 'item',
                            'quantity' => 1,
                            'unit_price' => 245000,
                            'warranty_period_days' => 14,
                            'warranty_terms' => 'Basic testing warranty',
                        ],
                        (object) [
                            'description' => '16GB DDR4 RAM Upgrade Service',
                            'type' => 'service',
                            'quantity' => 1,
                            'unit_price' => 10000,
                            'warranty_period_days' => 14,
                            'warranty_terms' => 'RAM testing warranty',
                        ]
                    ]),
                    'status' => 'countered',
                    'time' => '2 hours ago',
                ],
            ];
        }

        // Trace root
        $root = $this->offer;
        while ($root->parent_id && $root->parent) {
            $root = $root->parent;
        }

        // Trace forward
        $chain = collect([$root]);
        $current = $root;
        while ($child = Offer::with(['sender', 'recipient', 'items'])->where('parent_id', $current->id)->first()) {
            $chain->push($child);
            $current = $child;
        }

        return $chain->values()->map(function ($off, $index) {
            return [
                'id' => $off->id,
                'round_number' => $index + 1,
                'sender' => $off->sender?->business_name ?: $off->sender?->name ?: 'Party A',
                'recipient' => $off->recipient?->business_name ?: $off->recipient?->name ?: 'Party B',
                'is_current' => ($off->id === $this->offer->id),
                'total' => $off->total(),
                'discount' => (float) $off->discount,
                'warranty_days' => $off->maxWarrantyDays(),
                'delivery_method' => $off->delivery_method,
                'terms' => $off->terms,
                'items' => $off->items,
                'status' => $off->status,
                'time' => $off->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    public function toggleRound($round)
    {
        if ($this->expandedRound === $round) {
            $this->expandedRound = null;
        } else {
            $this->expandedRound = $round;
        }
    }

    public function openCounterDrawer()
    {
        $this->showCounterDrawer = true;
    }

    public function closeCounterDrawer()
    {
        $this->showCounterDrawer = false;
    }

    public function submitCounterOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit a counter-offer.');
            return redirect()->route('login');
        }

        if ($this->offer) {
            try {
                $counter = app(NegotiationService::class)->submitCounterOffer($user, $this->offer, [
                    'price' => (float) $this->counterPrice,
                    'discount' => (float) $this->counterDiscount,
                    'warranty_days' => (int) $this->warrantyPeriod,
                    'warranty_terms' => $this->warrantyTerms,
                    'terms' => $this->counterNotes,
                ]);

                $this->offerId = 'OFF-' . $counter->id;
                $this->loadOffer();
                $this->showCounterDrawer = false;
                session()->flash('message', "Counter-offer #OFF-{$counter->id} submitted successfully!");
                return redirect()->route('offers.view', ['offer_id' => 'OFF-' . $counter->id]);
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                return;
            }
        }

        $this->showCounterDrawer = false;
        session()->flash('message', 'Counter offer submitted successfully!');
    }

    public function acceptOffer()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to accept this offer.');
            return redirect()->route('login');
        }

        if ($this->offer) {
            try {
                $invoice = app(NegotiationService::class)->acceptOffer($user, $this->offer);
                session()->flash('message', "Offer accepted! Invoice {$invoice->invoice_number} generated for payment.");
                return redirect()->route('invoices');
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                return;
            }
        }

        session()->flash('message', 'Offer accepted! Reserving items for checkout.');
        return redirect()->route('checkout');
    }

    public function render()
    {
        $rounds = $this->getNegotiationRounds();

        return view('livewire.dashboard.offers.offer-view', [
            'rounds' => $rounds,
        ]);
    }
}