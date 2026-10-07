<?php

namespace App\Livewire\Components\Header;

use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    public int $cartCount = 0;
    public string $variant = 'desktop';

    public function mount(string $variant = 'desktop'): void
    {
        $this->variant = $variant;
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount(): void
    {
        $this->cartCount = app(CartService::class)->getCartCount(Auth::user());
    }

    public function render()
    {
        return view('livewire.components.header.cart-counter');
    }
}
