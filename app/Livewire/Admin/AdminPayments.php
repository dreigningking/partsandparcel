<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminPayments extends Component
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

    public ?int $selectedPaymentId = null;

    public function showPayment(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
    }

    public function closePayment(): void
    {
        $this->selectedPaymentId = null;
    }

    public function confirmPayment(int $paymentId): void
    {
        $payment = Payment::query()->findOrFail($paymentId);
        if ($payment->isCompleted()) {
            return;
        }

        app(\App\Services\Payments\CompletesPayment::class)->complete($payment, 'bank_transfer');
        session()->flash('status', __('Payment confirmed successfully.'));
        // Refresh selected payment to update view
        $this->selectedPaymentId = $payment->id;
    }

    public function render()
    {
        $payments = Payment::query()
            ->with(['user', 'currency'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('reference', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->dateFrom !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->paginate(10);

        $selectedPayment = $this->selectedPaymentId
            ? Payment::query()->with(['user', 'currency'])->find($this->selectedPaymentId)
            : null;

        return view('livewire.admin.admin-payments', [
            'payments' => $payments,
            'selectedPayment' => $selectedPayment,
            'statuses' => Payment::query()->distinct()->pluck('status')->filter()->values(),
        ]);
    }
}
