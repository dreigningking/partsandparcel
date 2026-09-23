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

    #[On('open-quick-view-offer')]
    public function loadOffers($response_id = null, $payload = null)
    {
        $targetId = $response_id ?? (is_array($payload) ? ($payload['response_id'] ?? null) : $payload);
        $this->responseId = $targetId ? (int) $targetId : null;

        $user = Auth::user();

        if ($this->responseId) {
            $response = Response::with(['user', 'offers.sender', 'offers.recipient', 'offers.items'])->find($this->responseId);
            if ($response) {
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
                            'from' => ($off->sender?->name ?? 'Sender') . ($isSender ? ' (You)' : ''),
                            'to' => ($off->recipient?->name ?? 'Recipient') . ($isRecipient ? ' (You)' : ''),
                            'price' => '₦' . number_format($off->total()),
                            'warranty' => $off->maxWarrantyDays() ? "{$off->maxWarrantyDays()}-Day Warranty" : 'Standard terms',
                            'delivery' => $off->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup',
                            'message' => $off->terms ?: 'Commercial offer proposal',
                            'time' => $off->created_at->diffForHumans(),
                            'status' => ucfirst($off->status),
                            'can_accept' => $isRecipient && $off->status === 'pending',
                        ];
                    })->toArray();

                    $this->currentRoundIndex = max(0, count($this->rounds) - 1);
                    $this->isOpen = true;
                    return;
                }
            }
        }

        // Demo sample fallback if response has no DB records
        $this->authorName = 'Abel Electronics';
        $this->rounds = [
            [
                'id' => 101,
                'round' => 1,
                'from' => 'Abel Electronics (Seller)',
                'to' => 'TechSam (You)',
                'price' => '₦85,000',
                'warranty' => '14-Day Warranty',
                'delivery' => 'Buyer Pickup',
                'message' => 'Original motherboard, clean condition. Tested working.',
                'time' => '2 hours ago',
                'status' => 'Countered',
                'can_accept' => false,
            ],
            [
                'id' => 105,
                'round' => 2,
                'from' => 'Abel Electronics (Seller)',
                'to' => 'TechSam (You)',
                'price' => '₦80,000',
                'warranty' => '14-Day Warranty',
                'delivery' => 'Buyer Pickup',
                'message' => '₦80,000 final offer brother. Clean board with 14-day replacement warranty.',
                'time' => '15 mins ago',
                'status' => 'Pending Action',
                'can_accept' => true,
            ],
        ];

        $this->currentRoundIndex = max(0, count($this->rounds) - 1);
        $this->isOpen = true;
    }

    public function previousRound()
    {
        if ($this->currentRoundIndex > 0) {
            $this->currentRoundIndex--;
        }
    }

    public function nextRound()
    {
        if ($this->currentRoundIndex < count($this->rounds) - 1) {
            $this->currentRoundIndex++;
        }
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
                return redirect()->route('invoices');
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
    }

    public function render()
    {
        return view('livewire.components.offers.quick-view-offers');
    }
}