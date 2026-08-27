<?php

namespace App\Livewire\Dashboard\Offers;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OfferView extends Component
{
    public $offerId = 'OFF-9021';
    public $showCounterDrawer = false;
    public $expandedRound = 3; // Latest round expanded by default

    // Counter-Offer Builder Inputs
    public $counterPrice = 265000;
    public $counterDiscount = 5000;
    public $warrantyPeriod = 14;
    public $warrantyTerms = '14-day full functional testing warranty included.';
    public $counterNotes = 'I can install the 16GB RAM upgrade and arrange express delivery if we agree on ₦265,000.';

    public function mount()
    {
        $this->offerId = request()->query('id', 'OFF-9021');
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
        $this->showCounterDrawer = false;
        session()->flash('message', 'Counter offer submitted successfully to buyer!');
    }

    public function acceptOffer()
    {
        session()->flash('message', 'Offer accepted! Reserving items for checkout.');
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.dashboard.offers.offer-view');
    }
}