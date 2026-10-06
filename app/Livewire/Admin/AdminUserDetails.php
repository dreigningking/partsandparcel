<?php

namespace App\Livewire\Admin;

use App\Models\BankAccount;
use App\Models\Location;
use App\Models\Moderation;
use App\Models\User;
use App\Models\Verification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('User Details & Standing — Admin Control Center')]
class AdminUserDetails extends Component
{
    public User $user;

    // Moderation & Rejection Modal State
    public bool $showRejectModal = false;
    public string $selectedRejectType = ''; // 'verification', 'location', 'user'
    public ?int $selectedRejectId = null;
    public string $rejectionReason = '';
    public string $presetReason = '';

    // Document Image Lightbox Modal State
    public ?string $previewImageModalUrl = null;
    public ?string $previewImageModalTitle = null;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function suspend(): void
    {
        if (auth()->id() === $this->user->id) {
            session()->flash('error', __('You cannot suspend your own admin account.'));
            return;
        }

        $this->user->update(['suspended_at' => now()]);
        $this->user->refresh();
        session()->flash('status', __('User account has been suspended.'));
    }

    public function unsuspend(): void
    {
        $this->user->update(['suspended_at' => null]);
        $this->user->refresh();
        session()->flash('status', __('User account has been unsuspended.'));
    }

    public function delete(): mixed
    {
        if (auth()->id() === $this->user->id) {
            session()->flash('error', __('You cannot delete your own admin account.'));
            return null;
        }

        $this->user->delete();
        session()->flash('status', __('User account has been permanently deleted.'));

        return redirect()->route('admin.users');
    }

    public function approveVerification(?int $verificationId = null): void
    {
        $verification = $verificationId
            ? $this->user->verifications()->find($verificationId)
            : $this->user->verification;

        if ($verification) {
            $moderation = $verification->moderation ?: new Moderation([
                'moderatable_type' => Verification::class,
                'moderatable_id' => $verification->id,
                'action' => 'verified',
            ]);

            $moderation->status = 'approved';
            $moderation->reason = null;
            $moderation->moderated_by = auth()->id();
            $moderation->save();
        }

        $this->user->update(['is_verified' => true]);
        $this->user->refresh();

        session()->flash('status', __('User identity verification approved successfully.'));
    }

    public function approveLocation(int $locationId): void
    {
        $location = $this->user->locations()->findOrFail($locationId);

        $moderation = $location->moderation ?: new Moderation([
            'moderatable_type' => Location::class,
            'moderatable_id' => $location->id,
            'action' => 'verified',
        ]);

        $moderation->status = 'approved';
        $moderation->reason = null;
        $moderation->moderated_by = auth()->id();
        $moderation->save();

        if ($this->user->fresh()->is_fully_verified) {
            $this->user->update(['is_verified' => true]);
        }

        $this->user->refresh();

        session()->flash('status', __("Location ':label' has been verified and approved.", ['label' => $location->label]));
    }

    public function openRejectModal(string $type, ?int $id = null): void
    {
        $this->selectedRejectType = $type;
        $this->selectedRejectId = $id;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->resetErrorBag();
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedRejectType = '';
        $this->selectedRejectId = null;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->resetErrorBag();
    }

    public function setPresetReason(string $reason): void
    {
        $this->presetReason = $reason;
        $this->rejectionReason = $reason;
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'rejectionReason.required' => 'Please select or provide a detailed rejection reason.',
        ]);

        if ($this->selectedRejectType === 'verification') {
            $verification = $this->selectedRejectId
                ? $this->user->verifications()->find($this->selectedRejectId)
                : $this->user->verification;

            if ($verification) {
                $moderation = $verification->moderation ?: new Moderation([
                    'moderatable_type' => Verification::class,
                    'moderatable_id' => $verification->id,
                    'action' => 'verified',
                ]);

                $moderation->status = 'rejected';
                $moderation->reason = $this->rejectionReason;
                $moderation->moderated_by = auth()->id();
                $moderation->save();
            }

            $this->user->update(['is_verified' => false]);
            session()->flash('status', __('Identity verification submission has been rejected.'));

        } elseif ($this->selectedRejectType === 'location') {
            $location = $this->user->locations()->findOrFail($this->selectedRejectId);

            $moderation = $location->moderation ?: new Moderation([
                'moderatable_type' => Location::class,
                'moderatable_id' => $location->id,
                'action' => 'verified',
            ]);

            $moderation->status = 'rejected';
            $moderation->reason = $this->rejectionReason;
            $moderation->moderated_by = auth()->id();
            $moderation->save();

            session()->flash('status', __("Location ':label' verification has been rejected.", ['label' => $location->label]));

        } elseif ($this->selectedRejectType === 'user') {
            $this->user->update(['is_verified' => false]);
            session()->flash('status', __('User identity status has been set to unverified.'));
        }

        $this->closeRejectModal();
        $this->user->refresh();
    }

    public function verifyBankAccount(int $bankAccountId): void
    {
        $account = $this->user->bankAccounts()->findOrFail($bankAccountId);
        $account->update(['verified_at' => now()]);
        $this->user->refresh();

        session()->flash('status', __("Bank account for ':bank' has been verified.", ['bank' => $account->bank_name]));
    }

    public function unverifyBankAccount(int $bankAccountId): void
    {
        $account = $this->user->bankAccounts()->findOrFail($bankAccountId);
        $account->update(['verified_at' => null]);
        $this->user->refresh();

        session()->flash('status', __("Bank account for ':bank' has been set to unverified.", ['bank' => $account->bank_name]));
    }

    public function openImagePreview(string $url, string $title): void
    {
        $this->previewImageModalUrl = $url;
        $this->previewImageModalTitle = $title;
    }

    public function closeImagePreview(): void
    {
        $this->previewImageModalUrl = null;
        $this->previewImageModalTitle = null;
    }

    public function render()
    {
        // Eager load relations
        $this->user->load([
            'country',
            'role',
            'activeSubscription.plan',
            'locations.state',
            'locations.country',
            'locations.moderation',
            'verifications.moderation',
            'verification.moderation',
            'bankAccounts',
            'listings' => fn ($q) => $q->latest()->take(6),
        ]);

        // Engagement Metrics
        $discussionsCount = $this->user->discussions()->count();
        $responsesCount = $this->user->responses()->count();
        $servicesAsProviderCount = $this->user->serviceJobsAsProvider()->count();
        $servicesAsCustomerCount = $this->user->serviceJobsAsCustomer()->count();
        $totalServicesCount = $servicesAsProviderCount + $servicesAsCustomerCount;

        $listingsCount = $this->user->listings()->count();
        $liveListingsCount = $this->user->listings()
            ->where('is_published', true)
            ->where('is_active', true)
            ->whereDoesntHave('latestModeration', fn ($m) => $m->where('status', 'rejected'))
            ->count();

        // Payments Sent (Direct Payments + Paid Buyer Invoices)
        $directPaymentsCount = $this->user->payments()->where('status', 'successful')->count();
        $directPaymentsSum = (float) $this->user->payments()->where('status', 'successful')->sum('amount');
        $buyerInvoicesCount = $this->user->buyerInvoices()->whereIn('status', ['paid', 'completed', 'escrow_locked'])->count();
        $buyerInvoicesSum = (float) $this->user->buyerInvoices()->whereIn('status', ['paid', 'completed', 'escrow_locked'])->sum('total');

        $paymentsSentCount = $directPaymentsCount + $buyerInvoicesCount;
        $paymentsSentTotal = $directPaymentsSum + $buyerInvoicesSum;

        // Payments Received (Paid Seller Invoices / Orders)
        $paymentsReceivedCount = $this->user->sellerInvoices()->whereIn('status', ['paid', 'completed', 'escrow_locked'])->count();
        $paymentsReceivedTotal = (float) $this->user->sellerInvoices()->whereIn('status', ['paid', 'completed', 'escrow_locked'])->sum('total');

        // Other engagements
        $sentOffersCount = $this->user->sentOffers()->count();
        $receivedOffersCount = $this->user->receivedOffers()->count();
        $reviewsCount = $this->user->listingReviews()->count();
        $averageRating = $this->user->listingReviews()->avg('rating') ?: 5.0;

        return view('livewire.admin.admin-user-details', [
            'user' => $this->user,
            'discussionsCount' => $discussionsCount,
            'responsesCount' => $responsesCount,
            'servicesAsProviderCount' => $servicesAsProviderCount,
            'servicesAsCustomerCount' => $servicesAsCustomerCount,
            'totalServicesCount' => $totalServicesCount,
            'listingsCount' => $listingsCount,
            'liveListingsCount' => $liveListingsCount,
            'paymentsSentCount' => $paymentsSentCount,
            'paymentsSentTotal' => $paymentsSentTotal,
            'paymentsReceivedCount' => $paymentsReceivedCount,
            'paymentsReceivedTotal' => $paymentsReceivedTotal,
            'sentOffersCount' => $sentOffersCount,
            'receivedOffersCount' => $receivedOffersCount,
            'reviewsCount' => $reviewsCount,
            'averageRating' => round($averageRating, 1),
            'showRejectModal' => $this->showRejectModal,
            'selectedRejectType' => $this->selectedRejectType,
            'rejectionReason' => $this->rejectionReason,
            'previewImageModalUrl' => $this->previewImageModalUrl,
            'previewImageModalTitle' => $this->previewImageModalTitle,
        ]);
    }
}
