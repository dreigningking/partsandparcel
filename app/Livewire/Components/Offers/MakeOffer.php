<?php

namespace App\Livewire\Components\Offers;

use Livewire\Attributes\On;
use Livewire\Component;

class MakeOffer extends Component
{
    public bool $isOpen = false;
    public ?string $sellerId = null;
    public string $sellerName = 'Adam Computers Ltd';

    public bool $requestDelivery = true;
    public int $deliveryAddressId = 1;
    public string $proposedPrice = '';
    public string $offerNote = '';

    public array $savedAddresses = [
        ['id' => 1, 'label' => 'Home Address', 'address' => '12 Computer Village Rd, Ikeja, Lagos'],
        ['id' => 2, 'label' => 'Office Address', 'address' => '45 Allen Avenue, Ikeja, Lagos'],
        ['id' => 3, 'label' => 'Workshop / Shop', 'address' => 'Plaza 3, Shop 14, Computer Village, Ikeja']
    ];

    #[On('open-make-offer')]
    public function loadOfferDrawer($payload = null)
    {
        if (is_array($payload)) {
            $this->sellerId = $payload['seller_id'] ?? null;
            $this->sellerName = $payload['seller_name'] ?? 'Adam Computers Ltd';
        } elseif (is_string($payload)) {
            $this->sellerId = $payload;
        }

        $this->isOpen = true;
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function submitPackageOffer()
    {
        $this->isOpen = false;
        session()->flash('message', 'Package offer & delivery request submitted successfully to ' . $this->sellerName);
        return redirect()->route('offers');
    }

    public function render()
    {
        return view('livewire.components.offers.make-offer');
    }
}