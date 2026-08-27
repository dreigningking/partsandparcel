<?php

namespace App\Livewire\Marketplace;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CheckoutPage extends Component
{
    public $sellerId = 'adam';
    public $sellerName = 'Adam Computers';
    public $sellerLocation = 'Computer Village, Ikeja, Lagos';

    // Selected Saved Address ID
    public $selectedAddressId = 1;

    // Delivery Handling Method selection
    public $deliveryMethod = 'pickup';

    // Payment Option selection ('escrow' vs 'direct_seller')
    public $paymentMethod = 'escrow';

    // Fee Configuration
    public $escrowFee = 1500;

    // Seller Bank Details for Direct Transfer
    public $sellerBank = [
        'bank_name' => 'GTBank (Guaranty Trust Bank)',
        'account_name' => 'Adam Computers Ltd',
        'account_number' => '0123456789'
    ];

    // Cart Items for Checkout
    public $cartItems = [
        [
            'id' => 'item_101',
            'title' => 'HP EliteBook 840 G5 Laptop',
            'specs' => 'Intel i5 · 8GB RAM · 256GB SSD',
            'price' => 280000,
            'quantity' => 1,
            'icon' => '💻'
        ]
    ];

    public function selectDeliveryMethod($method)
    {
        if ($method === 'integrated') {
            session()->flash('warning', 'Platform Integrated Courier API is coming soon! Please select Self-Pickup or Community Delivery for now.');
            return;
        }

        $this->deliveryMethod = $method;
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = $method;
    }

    public function placeOrder()
    {
        if ($this->paymentMethod === 'escrow') {
            session()->flash('message', 'Payment successful via Parts & Parcel Escrow! Funds held safely until item inspection.');
        } else {
            session()->flash('message', 'Direct payment marked completed! Please notify seller with your transfer proof.');
        }

        return redirect()->route('welcome');
    }

    public function render()
    {
        $itemSubtotal = collect($this->cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $deliveryFee = 0;
        $activeEscrowFee = ($this->paymentMethod === 'escrow') ? $this->escrowFee : 0;
        $totalPayable = $itemSubtotal + $deliveryFee + $activeEscrowFee;

        return view('livewire.marketplace.checkout-page', [
            'itemSubtotal' => $itemSubtotal,
            'deliveryFee' => $deliveryFee,
            'activeEscrowFee' => $activeEscrowFee,
            'totalPayable' => $totalPayable
        ]);
    }
}