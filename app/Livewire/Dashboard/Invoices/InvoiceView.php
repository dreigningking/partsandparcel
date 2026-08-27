<?php

namespace App\Livewire\Dashboard\Invoices;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class InvoiceView extends Component
{
    public function render()
    {
        return view('livewire.dashboard.invoices.invoice-view');
    }
}
