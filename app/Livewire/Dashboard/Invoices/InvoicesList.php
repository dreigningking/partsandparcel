<?php

namespace App\Livewire\Dashboard\Invoices;

use App\Models\Country;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Commercial Invoices — Dashboard')]
class InvoicesList extends Component
{
    use WithPagination;

    #[Url(as: 'scope')]
    public string $scope = 'all'; // 'all', 'buying', 'selling'

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'currency')]
    public string $currency = '';

    #[Url(as: 'payment_method')]
    public string $paymentMethod = '';

    #[Url(as: 'source')]
    public string $source = ''; // 'marketplace', 'community'

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'contains')]
    public string $contains = '';

    public function setScope(string $scope): void
    {
        if (in_array($scope, ['all', 'buying', 'selling'])) {
            $this->scope = $scope;
            $this->resetPage();
        }
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCurrency(): void { $this->resetPage(); }
    public function updatingPaymentMethod(): void { $this->resetPage(); }
    public function updatingSource(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingContains(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->currency = '';
        $this->paymentMethod = '';
        $this->source = '';
        $this->status = '';
        $this->contains = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $userId = $user?->id;

        // Base query for invoices involving the current user
        $baseQuery = Invoice::query();
        if ($userId) {
            $baseQuery->where(function (Builder $q) use ($userId) {
                $q->where('buyer_id', $userId)
                  ->orWhere('seller_id', $userId);
            });
        }

        // Counts for the 3 scope tabs
        $allCount = (clone $baseQuery)->count();
        $buyingCount = $userId ? Invoice::where('buyer_id', $userId)->count() : 0;
        $sellingCount = $userId ? Invoice::where('seller_id', $userId)->count() : 0;

        // Filter by scope
        $query = Invoice::query();
        if ($userId) {
            if ($this->scope === 'buying') {
                $query->where('buyer_id', $userId);
            } elseif ($this->scope === 'selling') {
                $query->where('seller_id', $userId);
            } else {
                $query->where(function (Builder $q) use ($userId) {
                    $q->where('buyer_id', $userId)
                      ->orWhere('seller_id', $userId);
                });
            }
        }

        // Search by reference or other party name / business name
        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('invoice_number', 'like', $searchTerm)
                  ->orWhereHas('buyer', function ($b) use ($searchTerm) {
                      $b->where('name', 'like', $searchTerm)
                        ->orWhere('business_name', 'like', $searchTerm);
                  })
                  ->orWhereHas('seller', function ($s) use ($searchTerm) {
                      $s->where('name', 'like', $searchTerm)
                        ->orWhere('business_name', 'like', $searchTerm);
                  });
            });
        }

        // Filter by currency
        if ($this->currency !== '') {
            $query->where('currency', $this->currency);
        }

        // Filter by payment method
        if ($this->paymentMethod !== '') {
            $query->where('payment_method', $this->paymentMethod);
        }

        // Filter by source: marketplace (cart) vs community (discussions)
        if ($this->source === 'community') {
            $query->whereHas('offer', fn ($q) => $q->whereNotNull('discussion_id'));
        } elseif ($this->source === 'marketplace') {
            $query->where(function (Builder $q) {
                $q->whereNotNull('cart_id')
                  ->orWhereHas('offer', fn ($sub) => $sub->whereNotNull('cart_id')->orWhereNull('discussion_id'))
                  ->orWhereNull('offer_id');
            });
        }

        // Filter by status
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        // Filter by contains
        if ($this->contains !== '') {
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
        }

        // Filter by date range
        if ($this->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo !== '') {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Compute metrics for the current filtered/scoped set
        $totalVolume = (clone $query)->sum('total') ?: 0;
        $escrowVolume = (clone $query)->where('payment_method', 'platform')->sum('total') ?: 0;
        $directVolume = (clone $query)->where('payment_method', 'direct')->sum('total') ?: 0;
        $totalInvoicesCount = (clone $query)->count();

        // Get distinct currencies available for filtering
        $availableCurrencies = Country::where('is_active', true)->pluck('currency')
            ->merge(Invoice::distinct()->pluck('currency'))
            ->filter()
            ->unique()
            ->values();

        // Paginate results with relations
        $invoices = $query->with([
            'buyer',
            'seller.country',
            'offer.discussion',
            'offer.cart',
            'items',
        ])
        ->latest()
        ->paginate(10);

        return view('livewire.dashboard.invoices.invoices-list', [
            'invoices' => $invoices,
            'allCount' => $allCount,
            'buyingCount' => $buyingCount,
            'sellingCount' => $sellingCount,
            'totalVolume' => $totalVolume,
            'escrowVolume' => $escrowVolume,
            'directVolume' => $directVolume,
            'totalInvoicesCount' => $totalInvoicesCount,
            'availableCurrencies' => $availableCurrencies,
            'scope' => $this->scope,
            'search' => $this->search,
            'currency' => $this->currency,
            'paymentMethod' => $this->paymentMethod,
            'source' => $this->source,
            'status' => $this->status,
            'contains' => $this->contains,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'currentUserId' => $userId,
        ]);
    }
}
