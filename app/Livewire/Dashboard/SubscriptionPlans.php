<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Subscription Plans — Dashboard')]
class SubscriptionPlans extends Component
{
    public function render()
    {
        return view('livewire.dashboard.subscription-plans');
    }
}
