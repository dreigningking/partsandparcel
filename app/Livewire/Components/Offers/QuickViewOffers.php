<?php

namespace App\Livewire\Components\Offers;

use Livewire\Attributes\On;
use Livewire\Component;

class QuickViewOffers extends Component
{
    public bool $isOpen = false;
    public ?int $responseId = null;
    public string $authorName = '';
    public array $rounds = [];
    public int $currentRoundIndex = 0;

    #[On('open-quick-view-offer')]
    public function loadOffers($payload)
    {
        $responseId = is_array($payload) ? ($payload['response_id'] ?? null) : $payload;
        $this->responseId = $responseId;

        if ($responseId == 1) {
            $this->authorName = 'Abel Electronics';
            $this->rounds = [
                [
                    'round' => 1,
                    'from' => 'Abel Electronics (Seller)',
                    'price' => '₦85,000',
                    'warranty' => '14-Day Warranty',
                    'delivery' => 'Buyer Pickup',
                    'message' => 'Original motherboard, clean condition. Tested working.',
                    'time' => '2 hours ago',
                    'status' => 'Countered'
                ],
                [
                    'round' => 2,
                    'from' => 'TechSam (Buyer / You)',
                    'price' => '₦75,000',
                    'warranty' => '14-Day Warranty',
                    'delivery' => 'Buyer Pickup',
                    'message' => 'Can we do ₦75,000? I will come pick it up today at Computer Village.',
                    'time' => '1 hour ago',
                    'status' => 'Countered'
                ],
                [
                    'round' => 3,
                    'from' => 'Abel Electronics (Seller)',
                    'price' => '₦80,000',
                    'warranty' => '14-Day Warranty',
                    'delivery' => 'Buyer Pickup',
                    'message' => '₦80,000 final offer brother. Clean board with 14-day replacement warranty.',
                    'time' => '15 mins ago',
                    'status' => 'Pending Action'
                ],
            ];
        } elseif ($responseId == 2) {
            $this->authorName = 'Seth Tech Hub';
            $this->rounds = [
                [
                    'round' => 1,
                    'from' => 'Seth Tech Hub (Seller)',
                    'price' => '₦90,000',
                    'warranty' => '30-Day Warranty',
                    'delivery' => 'Seller Delivery Included',
                    'message' => 'Grade A tested motherboard with 30 days warranty. Free delivery in Ikeja.',
                    'time' => '1 hour ago',
                    'status' => 'Countered'
                ],
                [
                    'round' => 2,
                    'from' => 'TechSam (Buyer / You)',
                    'price' => '₦82,000',
                    'warranty' => '30-Day Warranty',
                    'delivery' => 'Buyer Pickup',
                    'message' => 'Interested at ₦82,000 if I come pick it up.',
                    'time' => '20 mins ago',
                    'status' => 'Pending Action'
                ],
            ];
        } else {
            $this->authorName = 'Adam Spare Parts';
            $this->rounds = [
                [
                    'round' => 1,
                    'from' => 'Adam Spare Parts (Seller)',
                    'price' => '₦70,000',
                    'warranty' => '7-Day Warranty',
                    'delivery' => 'Buyer Pickup',
                    'message' => '₦70,000 as-is tested working. Bring your laptop to test at shop.',
                    'time' => '45 mins ago',
                    'status' => 'Pending Action'
                ],
            ];
        }

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

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.offers.quick-view-offers');
    }
}