<?php

namespace App\Livewire\Marketplace\Community;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CommunityRequest extends Component
{
    public $isOwner = true; // Set to true for request owner view, false for public viewers

    public $responseText = '';
    public $composerOfferPrice = '';
    public $composerOfferWarranty = '14 days';
    public $composerOfferDelivery = 'Buyer pickup';
    public $composerOfferMessage = '';
    public $isOfferActive = false;

    // Report modal inputs
    public $reportReason = 'spam';
    public $reportDetails = '';

    public $responses = [];

    public function mount()
    {
        $this->responses = [
            [
                'id' => 1,
                'author' => 'Abel Electronics',
                'verified' => true,
                'location' => 'Computer Village, Ikeja',
                'time' => '2 hours ago',
                'text' => "I have the motherboard you're looking for. It's from an EliteBook 840 G5 with a working motherboard. You can test it before buying.",
                'negotiation' => [
                    [
                        'id' => 101,
                        'from' => 'Abel Electronics',
                        'to' => 'TechSam',
                        'price' => '₦85,000',
                        'warranty' => '14-day warranty',
                        'delivery' => 'Buyer pickup',
                        'message' => 'Original motherboard, clean condition. Tested working.',
                        'time' => '2 hours ago',
                        'status' => 'declined',
                        'edited' => false
                    ],
                    [
                        'id' => 105,
                        'from' => 'Abel Electronics',
                        'to' => 'TechSam',
                        'price' => '₦80,000',
                        'warranty' => '14-day warranty',
                        'delivery' => 'Buyer pickup',
                        'message' => '₦80k final offer brother. Clean board with 14-day replacement warranty.',
                        'time' => '15 mins ago',
                        'status' => 'pending',
                        'edited' => false
                    ]
                ]
            ],
            [
                'id' => 2,
                'author' => 'Seth Tech Hub',
                'verified' => true,
                'location' => 'Oregun, Ikeja',
                'time' => '1 hour ago',
                'text' => "We have 2 units of HP 840 G5 motherboards available at our shop. Core i5 8th Gen, tested with 30 days warranty.",
                'negotiation' => [
                    [
                        'id' => 201,
                        'from' => 'Seth Tech Hub',
                        'to' => 'TechSam',
                        'price' => '₦90,000',
                        'warranty' => '30-day warranty',
                        'delivery' => 'Seller delivery',
                        'message' => 'Grade A tested motherboard with 30 days warranty. Free delivery in Ikeja.',
                        'time' => '1 hour ago',
                        'status' => 'declined',
                        'edited' => false
                    ]
                ]
            ],
            [
                'id' => 3,
                'author' => 'Adam Spare Parts',
                'verified' => true,
                'location' => 'Computer Village',
                'time' => '45 mins ago',
                'text' => "I have a scrapped 840 G5 unit with good motherboard. Selling as-is or tested.",
                'negotiation' => [
                    [
                        'id' => 301,
                        'from' => 'Adam Spare Parts',
                        'to' => 'TechSam',
                        'price' => '₦70,000',
                        'warranty' => '7-day warranty',
                        'delivery' => 'Buyer pickup',
                        'message' => '₦70,000 as-is tested working. Bring your laptop to test at shop.',
                        'time' => '45 mins ago',
                        'status' => 'pending',
                        'edited' => false
                    ]
                ]
            ],
            [
                'id' => 4,
                'author' => 'Kano Micro Systems',
                'verified' => false,
                'location' => 'Kano (Ships nationwide)',
                'time' => '30 mins ago',
                'text' => "Available in Kano. Waybill to Lagos takes 24 hours via GIG Logistics.",
                'negotiation' => []
            ],
            [
                'id' => 5,
                'author' => 'FixIt Depot',
                'verified' => true,
                'location' => 'Surulere, Lagos',
                'time' => '20 mins ago',
                'text' => "If you can't find a replacement board, we offer board-level repair for 840 G5.",
                'negotiation' => []
            ]
        ];
    }

    public function toggleOfferComposer()
    {
        $this->isOfferActive = !$this->isOfferActive;
    }

    public function submitResponse()
    {
        if (trim($this->responseText) === '') return;

        $newResponse = [
            'id' => count($this->responses) + 1,
            'author' => 'My Tech Shop (You)',
            'verified' => true,
            'location' => 'Computer Village, Ikeja',
            'time' => 'Just now',
            'text' => $this->responseText,
            'negotiation' => []
        ];

        if ($this->isOfferActive && $this->composerOfferPrice) {
            $newResponse['negotiation'][] = [
                'id' => rand(500, 999),
                'from' => 'My Tech Shop (You)',
                'to' => 'TechSam',
                'price' => '₦' . number_format((float) str_replace(',', '', $this->composerOfferPrice)),
                'warranty' => $this->composerOfferWarranty,
                'delivery' => $this->composerOfferDelivery,
                'message' => $this->composerOfferMessage ?: $this->responseText,
                'time' => 'Just now',
                'status' => 'pending',
                'edited' => false
            ];
        }

        array_unshift($this->responses, $newResponse);

        $this->responseText = '';
        $this->composerOfferPrice = '';
        $this->composerOfferMessage = '';
        $this->isOfferActive = false;

        session()->flash('message', 'Your response has been posted successfully!');
    }

    public function openOffer($responseId)
    {
        $this->dispatch('open-quick-view-offer', response_id: $responseId);
    }

    public function openVendorChat($vendorName)
    {
        $id = strtolower(strtok($vendorName, ' '));
        $this->dispatch('open-conversation', id: $id);
    }

    public function render()
    {
        return view('livewire.marketplace.community.community-request');
    }
}