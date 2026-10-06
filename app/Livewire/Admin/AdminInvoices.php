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

    #[Url(as: 'contains')]
    public string $contains = '';

    public ?int $selectedInvoiceId = null;

    public function updatingContains(): void
    {
        $this->resetPage();
    }

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
            ->when($this->contains !== '', function (Builder $query) {
                match ($this->contains) {
                    'shipment' => $query->where(function (Builder $q) {
                        $q->whereHas('items', function ($itemQuery) {
                            $itemQuery->where('itemable_type', \App\Models\Shipment::class)
                                      ->orWhereIn('type', ['pickup', 'delivery', 'return']);
                        })->orWhereHas('returns', function ($rq) {
                            $rq->whereNotNull('shipment_id');
                        })->orWhereHas('replacements', function ($rq) {
                            $rq->whereNotNull('shipment_id');
                        })->orWhereHas('offer.items', function ($itemQuery) {
                            $itemQuery->where('itemable_type', \App\Models\Shipment::class);
                        });
                    }),
                    'issue' => $query->whereHas('issues', function (Builder $q) {
                        $q->where('type', '!=', 'warranty_claim');
                    }),
                    'refund' => $query->whereHas('refunds'),
                    'replacement' => $query->whereHas('replacements'),
                    'return' => $query->where(function (Builder $q) {
                        $q->whereHas('returns')
                          ->orWhereHas('issues', fn ($iq) => $iq->where('resolution_action', 'return_refund'));
                    }),
                    'warranty', 'warranty_claim' => $query->where(function (Builder $q) {
                        $q->whereHas('issues', fn ($iq) => $iq->where('type', 'warranty_claim'))
                          ->orWhereHas('items', fn ($iq) => $iq->where('warranty_period_days', '>', 0));
                    }),
                    'service' => $query->where(function (Builder $q) {
                        $q->whereHas('serviceJobs')
                          ->orWhereHas('items', fn ($iq) => $iq->where('type', 'service'));
                    }),
                    default => null,
                };
            })
            ->latest()
            ->paginate(12);

        $selectedInvoice = $this->selectedInvoiceId
            ? Invoice::with(['buyer', 'seller', 'items.itemable', 'payments', 'settlement'])->find($this->selectedInvoiceId)
            : null;

        return view('livewire.admin.admin-invoices', [
            'search' => $this->search,
            'status' => $this->status,
            'paymentMethod' => $this->paymentMethod,
            'contains' => $this->contains,
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'totalVolume' => Invoice::where('status', 'paid')->sum('total'),
            'paidCount' => Invoice::where('status', 'paid')->count(),
            'pendingCount' => Invoice::whereIn('status', ['issued', 'pending'])->count(),
        ]);
    }
}
