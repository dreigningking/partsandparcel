<?php

namespace App\Livewire\Dashboard\Invoices;

use App\Models\Invoice;
use App\Models\ListingReview;
use App\Models\ServiceJob;
use App\Models\ServiceReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class InvoiceView extends Component
{
    public ?Invoice $invoice = null;

    // Review Form State
    public int $rating = 5;
    public string $reviewComment = '';
    public bool $reviewSubmitted = false;

    public function mount($invoice_id = null)
    {
        if ($invoice_id) {
            $this->invoice = Invoice::with(['buyer.primaryLocation', 'seller.bankAccounts', 'seller.primaryLocation', 'items.itemable'])->find($invoice_id);
        }
        if (! $this->invoice) {
            $this->invoice = Invoice::with(['buyer.primaryLocation', 'seller.bankAccounts', 'seller.primaryLocation', 'items.itemable'])->latest()->first();
        }
    }

    /**
     * Check if currently logged in user is the seller of this invoice.
     */
    public function getIsSellerProperty(): bool
    {
        if (! Auth::check() || ! $this->invoice) {
            return false;
        }
        return Auth::id() === (int) $this->invoice->seller_id;
    }

    /**
     * Check if currently logged in user is the buyer of this invoice.
     */
    public function getIsBuyerProperty(): bool
    {
        if (! Auth::check() || ! $this->invoice) {
            return false;
        }
        return Auth::id() === (int) $this->invoice->buyer_id;
    }

    /**
     * Check if the warranty period has passed.
     */
    public function getIsWarrantyOverProperty(): bool
    {
        if (! $this->invoice) {
            return false;
        }

        $maxWarrantyDays = $this->invoice->items->max('warranty_period_days') ?? 0;
        $issuedAt = $this->invoice->issued_at ?: $this->invoice->created_at;
        $warrantyEndsAt = $issuedAt ? $issuedAt->copy()->addDays($maxWarrantyDays) : now();

        return now()->greaterThanOrEqualTo($warrantyEndsAt) || request()->has('warranty_over');
    }

    /**
     * Seller confirms buyer paid direct bank transfer after warranty period.
     */
    public function confirmDirectPaymentReceived()
    {
        if (! $this->invoice) {
            return;
        }

        $user = Auth::user();
        // Allow seller or admin
        if (! $this->isSeller && (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin())) {
            session()->flash('error', 'Only the seller can confirm direct payment receipt.');
            return;
        }

        $this->invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        app(\App\Services\Commercial\NegotiationService::class)->handleInvoicePaid($this->invoice);

        $this->invoice->refresh();

        session()->flash('seller_success', 'Payment confirmed! This invoice is now marked as PAID. Your verified sales count and seller reputation have been updated.');
    }

    /**
     * Buyer reports "I didn't buy it".
     * Changes invoice status to 'cancelled' and paid_at to null.
     */
    public function reportDidNotBuy()
    {
        if (! $this->invoice) {
            return;
        }

        $user = Auth::user();
        if (! $this->isBuyer && (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin())) {
            session()->flash('error', 'Only the buyer can report non-purchase.');
            return;
        }

        $this->invoice->update([
            'status' => 'cancelled',
            'paid_at' => null,
        ]);

        $this->invoice->refresh();

        session()->flash('buyer_notice', 'Invoice #'.$this->invoice->invoice_number.' has been marked as cancelled and payment status revoked.');
    }

    /**
     * Buyer submits review for the purchased item or service.
     */
    public function submitReview()
    {
        if (! $this->invoice || ! $this->isBuyer) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:500',
        ]);

        // Record review for any items associated with listings
        foreach ($this->invoice->items as $item) {
            if ($item->itemable_type === \App\Models\Listing::class && $item->itemable_id) {
                ListingReview::updateOrCreate(
                    [
                        'listing_id' => $item->itemable_id,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'rating' => $this->rating,
                        'comment' => $this->reviewComment ?: 'Verified purchase transaction.',
                    ]
                );
            }
        }

        $this->reviewSubmitted = true;
        session()->flash('review_success', 'Thank you! Your verified rating and review have been recorded.');
    }

    public function render()
    {
        return view('livewire.dashboard.invoices.invoice-view', [
            'invoice' => $this->invoice,
            'isSeller' => $this->isSeller,
            'isBuyer' => $this->isBuyer,
            'isWarrantyOver' => $this->isWarrantyOver,
        ]);
    }
}
