<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Services\Commercial\SubscriptionService;
use App\Services\Payment\EscrowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Payments & Escrow Transactions — Admin Console')]
class AdminPayments extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = 'all';

    #[Url(as: 'provider')]
    public string $provider = 'all';

    #[Url(as: 'type')]
    public string $type = 'all';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public int $perPage = 15;

    public ?int $selectedPaymentId = null;
    public bool $showDetailModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedProvider(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedSortDir(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->provider = 'all';
        $this->type = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function showPayment(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
        $this->showDetailModal = true;
    }

    public function closePayment(): void
    {
        $this->selectedPaymentId = null;
        $this->showDetailModal = false;
    }

    public function confirmPayment(int $paymentId): void
    {
        $payment = Payment::with(['paymentable', 'user'])->findOrFail($paymentId);

        if ($payment->isSuccessful()) {
            session()->flash('status', "Payment #{$payment->reference} is already marked as successful.");
            return;
        }

        try {
            if ($payment->paymentable_type === Subscription::class || ! empty($payment->metadata['subscription_plan_id'])) {
                app(SubscriptionService::class)->activateSubscription($payment);
            } elseif ($payment->paymentable_type === Promotion::class || ! empty($payment->metadata['listing_id'])) {
                $promotionId = $payment->paymentable_id ?? ($payment->metadata['promotion_id'] ?? null);
                $promotion = $promotionId ? Promotion::find($promotionId) : null;

                if ($promotion) {
                    $promotion->update(['status' => 'active']);
                }

                $payment->update([
                    'status' => 'successful',
                    'paid_at' => now(),
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'admin_confirmed_by' => auth()->id(),
                        'confirmed_at' => now()->toIso8601String(),
                        'channel' => 'manual_admin_confirmation',
                    ]),
                ]);
            } else {
                // Escrow order invoice payment
                app(EscrowService::class)->handlePaymentSuccessful($payment, [
                    'admin_confirmed_by' => auth()->id(),
                    'confirmed_at' => now()->toIso8601String(),
                    'channel' => 'manual_admin_confirmation',
                ]);
            }

            session()->flash('status', "Payment #{$payment->reference} confirmed successfully and marked as successful.");
        } catch (\Throwable $e) {
            Log::error("Failed to confirm payment #{$payment->id}: " . $e->getMessage());
            session()->flash('error', "Could not confirm payment: " . $e->getMessage());
        }

        $this->selectedPaymentId = $payment->id;
    }

    public function markAsFailed(int $paymentId): void
    {
        $payment = Payment::findOrFail($paymentId);

        $payment->update([
            'status' => 'failed',
            'metadata' => array_merge($payment->metadata ?? [], [
                'admin_rejected_by' => auth()->id(),
                'rejected_at' => now()->toIso8601String(),
                'reason' => 'Admin manual decline/unverified payment',
            ]),
        ]);

        session()->flash('status', "Payment #{$payment->reference} has been marked as failed.");
        $this->selectedPaymentId = $payment->id;
    }

    public function render()
    {
        $query = Payment::query()
            ->with(['user.country', 'paymentable', 'settlements'])
            ->when($this->search !== '', function (Builder $q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function (Builder $inner) use ($term) {
                    $inner->where('reference', 'like', $term)
                        ->orWhereHas('user', function (Builder $uq) use ($term) {
                            $uq->where('name', 'like', $term)
                                ->orWhere('email', 'like', $term)
                                ->orWhere('phone', 'like', $term)
                                ->orWhere('business_name', 'like', $term);
                        });
                });
            })
            ->when($this->status !== 'all', function (Builder $q) {
                if ($this->status === 'successful') {
                    $q->whereIn('status', ['successful', 'success', 'paid']);
                } else {
                    $q->where('status', $this->status);
                }
            })
            ->when($this->provider !== 'all', function (Builder $q) {
                $q->where('provider', $this->provider);
            })
            ->when($this->type !== 'all', function (Builder $q) {
                match ($this->type) {
                    'invoice' => $q->where('paymentable_type', Invoice::class),
                    'subscription' => $q->where('paymentable_type', Subscription::class),
                    'promotion' => $q->where('paymentable_type', Promotion::class),
                    default => null,
                };
            })
            ->when($this->dateFrom !== '', function (Builder $q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo !== '', function (Builder $q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            });

        // Direction & Column sorting
        $sortColumn = in_array($this->sortBy, ['created_at', 'amount', 'paid_at', 'escrow_fee']) ? $this->sortBy : 'created_at';
        $sortDirection = in_array(strtolower($this->sortDir), ['asc', 'desc']) ? strtolower($this->sortDir) : 'desc';
        $payments = $query->orderBy($sortColumn, $sortDirection)->paginate($this->perPage);

        // KPI metrics computed across all payments
        $kpi = [
            'totalCount' => Payment::count(),
            'totalVolume' => (float) Payment::whereIn('status', ['successful', 'success', 'paid', 'held_in_escrow'])->sum('amount'),
            'totalEscrowFees' => (float) Payment::whereIn('status', ['successful', 'success', 'paid'])->sum('escrow_fee'),
            'successfulCount' => Payment::whereIn('status', ['successful', 'success', 'paid'])->count(),
            'heldEscrowCount' => Payment::where('status', 'held_in_escrow')->count(),
            'pendingCount' => Payment::where('status', 'pending')->count(),
            'failedCount' => Payment::whereIn('status', ['failed', 'declined', 'cancelled'])->count(),
        ];

        // Available dynamic providers and statuses in database
        $availableProviders = Payment::query()->distinct()->whereNotNull('provider')->pluck('provider')->filter()->values();

        $selectedPayment = null;
        if ($this->selectedPaymentId) {
            $selectedPayment = Payment::query()
                ->with(['user.country', 'paymentable', 'settlements'])
                ->find($this->selectedPaymentId);
        }

        return view('livewire.admin.admin-payments', [
            'payments' => $payments,
            'selectedPayment' => $selectedPayment,
            'kpi' => $kpi,
            'availableProviders' => $availableProviders,
        ]);
    }
}
