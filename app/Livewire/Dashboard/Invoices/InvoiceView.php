<?php

namespace App\Livewire\Dashboard\Invoices;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class InvoiceView extends Component
{
    public ?Invoice $invoice = null;

    public function mount($invoice_id = null)
    {
        if ($invoice_id) {
            $this->invoice = Invoice::with(['buyer', 'seller', 'items.listing'])->find($invoice_id);
        }
        if (! $this->invoice) {
            $this->invoice = Invoice::with(['buyer', 'seller', 'items.listing'])->latest()->first();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.invoices.invoice-view', [
            'invoice' => $this->invoice,
        ]);
    }
}

