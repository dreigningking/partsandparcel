<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminSubscriptions extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'start_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public ?int $selectedPaymentId = null;

    public function showInvoice(int $subscriptionId): void
    {
        $this->selectedPaymentId = Payment::query()
            ->where('paymentable_type', Subscription::class)
            ->where('paymentable_id', $subscriptionId)
            ->latest('id')
            ->value('id');
    }

    public function closeInvoice(): void
    {
        $this->selectedPaymentId = null;
    }

    public function render()
    {
        $sortBy = in_array($this->sortBy, ['start_at', 'end_at'], true) ? $this->sortBy : 'start_at';

        $subscriptions = Subscription::query()
            ->with(['user', 'plan'])
            ->withCount('subscribedProperties')
            ->when($this->search !== '', function (Builder $query) {
                $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->dateFrom !== '', fn (Builder $query) => $query->whereDate('start_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn (Builder $query) => $query->whereDate('start_at', '<=', $this->dateTo))
            ->orderBy($sortBy, $this->sortDir)
            ->paginate(10);

        $invoicePayment = $this->selectedPaymentId
            ? Payment::query()->with(['user', 'currency'])->find($this->selectedPaymentId)
            : null;

        return view('livewire.admin.admin-subscriptions', [
            'subscriptions' => $subscriptions,
            'invoicePayment' => $invoicePayment,
            'statuses' => Subscription::query()->distinct()->pluck('status')->filter()->values(),
        ]);
    }
}
