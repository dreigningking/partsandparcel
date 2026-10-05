<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Invoices — Admin Control Center')]
class AdminInvoices extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'method')]
    public string $paymentMethod = '';

    public ?int $selectedInvoiceId = null;

    public function showInvoice(int $id): void
    {
        $this->selectedInvoiceId = $id;
    }

    public function closeInvoice(): void
    {
        $this->selectedInvoiceId = null;
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->with(['buyer', 'seller', 'items.itemable'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('buyer', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('seller', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->paymentMethod !== '', fn (Builder $query) => $query->where('payment_method', $this->paymentMethod))
            ->latest()
            ->paginate(12);

        $selectedInvoice = $this->selectedInvoiceId
            ? Invoice::with(['buyer', 'seller', 'items.itemable', 'payments', 'settlement'])->find($this->selectedInvoiceId)
            : null;

        return view('livewire.admin.admin-invoices', [
            'search' => $this->search,
            'status' => $this->status,
            'paymentMethod' => $this->paymentMethod,
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'totalVolume' => Invoice::where('status', 'paid')->sum('total'),
            'paidCount' => Invoice::where('status', 'paid')->count(),
            'pendingCount' => Invoice::whereIn('status', ['issued', 'pending'])->count(),
        ]);
    }
}
