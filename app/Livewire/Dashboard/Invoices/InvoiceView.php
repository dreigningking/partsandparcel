<?php

namespace App\Livewire\Dashboard\Invoices;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\ListingReview;
use App\Models\Payment;
use App\Models\ServiceJob;
use App\Models\ServiceReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class InvoiceView extends Component
{
    public ?Invoice $invoice = null;

    // Payment Option selection ('platform' vs 'direct')
    public string $paymentOption = 'platform';

    // Coupon / Promo Code State (for platform escrow payments)
    public string $couponCode = '';
    public ?int $appliedCouponId = null;
    public float $couponDiscount = 0.0;
    public string $couponMessage = '';
    public bool $couponValid = false;

    // Review Form State
    public int $rating = 5;
    public string $reviewComment = '';
    public bool $reviewSubmitted = false;

    public function mount($invoice_id = null)
    {
        if ($invoice_id) {
            $this->invoice = Invoice::with(['buyer.primaryLocation', 'seller.bankAccounts', 'seller.primaryLocation', 'items.itemable'])
                ->where('id', $invoice_id)
                ->orWhere('invoice_number', $invoice_id)
                ->first();
        }
        if (! $this->invoice) {
            $this->invoice = Invoice::with(['buyer.primaryLocation', 'seller.bankAccounts', 'seller.primaryLocation', 'items.itemable'])->latest()->first();
        }

        if ($this->invoice && in_array($this->invoice->payment_method, ['direct', 'platform'])) {
            $this->paymentOption = $this->invoice->payment_method;
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

    public function setPaymentOption(string $method)
    {
        $this->paymentOption = in_array($method, ['platform', 'direct']) ? $method : 'platform';
        if ($this->paymentOption === 'direct') {
            $this->removeCoupon();
        }
    }

    public function applyCoupon()
    {
        $this->resetErrorBag('couponCode');
        $code = trim($this->couponCode);

        if (empty($code)) {
            $this->addError('couponCode', 'Please enter a coupon code.');
            return;
        }

        if ($this->paymentOption === 'direct') {
            $this->addError('couponCode', 'Coupons cannot be applied to direct seller transfers.');
            return;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            $this->addError('couponCode', 'Invalid coupon code. Please check and try again.');
            return;
        }

        $subtotal = (float) ($this->invoice->subtotal ?? 0);
        if (! $coupon->isValid($subtotal)) {
            $this->addError('couponCode', 'This coupon is expired, inactive, or requires a higher order amount.');
            return;
        }

        $this->appliedCouponId = $coupon->id;
        $this->couponDiscount = (float) $coupon->calculateDiscount($subtotal);
        $this->couponValid = true;
        $this->couponMessage = "Voucher '{$coupon->code}' applied: ₦" . number_format($this->couponDiscount) . ' discount!';
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->appliedCouponId = null;
        $this->couponDiscount = 0.0;
        $this->couponMessage = '';
        $this->couponValid = false;
        $this->resetErrorBag('couponCode');
    }

    public function payWithPlatformEscrow()
    {
        if (! $this->invoice || $this->invoice->status === 'paid' || $this->invoice->status === 'cancelled') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to complete payment.');
            return redirect()->route('login');
        }

        $subtotal = (float) $this->invoice->subtotal;
        $buyerUser = $this->invoice->buyer ?? $user;
        $escrowPercentage = method_exists($buyerUser, 'getEscrowPercentage') ? (float) $buyerUser->getEscrowPercentage() : 10.0;
        $escrowCap = method_exists($buyerUser, 'getEscrowCap') ? $buyerUser->getEscrowCap() : null;
        if (method_exists($buyerUser, 'calculateEscrowFee')) {
            $escrowFee = $buyerUser->calculateEscrowFee((float) $subtotal);
        } else {
            $rawFee = round($subtotal * ($escrowPercentage / 100), 2);
            $escrowFee = ($escrowCap !== null && $rawFee > $escrowCap) ? (float) $escrowCap : $rawFee;
        }
        $offerDiscount = (float) $this->invoice->discount;
        $couponDisc = $this->couponDiscount;
        $totalPayable = max(0.00, round($subtotal + $escrowFee - $offerDiscount - $couponDisc, 2));
        $commission = round(max(0.00, $subtotal - $offerDiscount - $couponDisc) * 0.05, 2);

        $this->invoice->update([
            'payment_method' => 'platform',
            'discount' => $offerDiscount + $couponDisc,
            'total' => $totalPayable,
            'commission' => $commission,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'paymentable_id' => $this->invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-' . strtoupper(Str::random(12)),
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => $totalPayable,
            'currency' => $user->currency ?? 'NGN',
            'metadata' => [
                'invoice_id' => $this->invoice->id,
                'coupon_code' => $this->appliedCouponId ? Coupon::find($this->appliedCouponId)?->code : null,
                'coupon_discount' => $couponDisc,
                'escrow_fee' => $escrowFee,
                'escrow_percentage' => $escrowPercentage,
                'escrow_cap' => $escrowCap,
            ],
        ]);

        app(\App\Services\Payment\EscrowService::class)->handlePaymentSuccessful($payment);

        if ($this->appliedCouponId) {
            Coupon::find($this->appliedCouponId)?->recordUsage();
        }

        $this->invoice->refresh();
        session()->flash('buyer_payment_success', "Payment of ₦" . number_format($totalPayable) . " confirmed with Parts & Parcel Escrow! Funds are securely locked in escrow pending inspection.");
    }

    public function confirmDirectTransferSent()
    {
        if (! $this->invoice || $this->invoice->status === 'paid' || $this->invoice->status === 'cancelled') {
            return;
        }

        $subtotal = (float) $this->invoice->subtotal;
        $offerDiscount = (float) $this->invoice->discount;
        $totalPayable = max(0.00, round($subtotal - $offerDiscount, 2));

        $this->invoice->update([
            'payment_method' => 'direct',
            'total' => $totalPayable,
        ]);

        $this->invoice->refresh();
        session()->flash('buyer_direct_notice', "Direct transfer recorded! Please transfer ₦" . number_format($totalPayable) . " directly to the seller's account. Keep your payment receipt.");
    }

    public function render()
    {
        $subtotal = (float) ($this->invoice?->subtotal ?? 0);
        $buyerUser = $this->invoice?->buyer ?? Auth::user();
        $escrowPercentage = ($buyerUser && method_exists($buyerUser, 'getEscrowPercentage')) 
            ? (float) $buyerUser->getEscrowPercentage() 
            : 10.0;
        $escrowCap = ($buyerUser && method_exists($buyerUser, 'getEscrowCap')) 
            ? $buyerUser->getEscrowCap() 
            : null;
        if ($buyerUser && method_exists($buyerUser, 'calculateEscrowFee')) {
            $escrowFee = $buyerUser->calculateEscrowFee((float) $subtotal);
        } else {
            $rawFee = round($subtotal * ($escrowPercentage / 100), 2);
            $escrowFee = ($escrowCap !== null && $rawFee > $escrowCap) ? (float) $escrowCap : $rawFee;
        }
        $activeEscrowFee = ($this->paymentOption === 'platform') ? $escrowFee : 0;
        $offerDiscount = (float) ($this->invoice?->discount ?? 0);
        $couponDisc = ($this->paymentOption === 'platform') ? $this->couponDiscount : 0;
        $totalDiscount = $offerDiscount + $couponDisc;
        $totalPayable = max(0.00, round($subtotal + $activeEscrowFee - $totalDiscount, 2));

        $sellerAcc = $this->invoice?->seller?->bankAccounts?->first();
        $sellerBank = [
            'bank_name' => $sellerAcc?->bank_name ?? 'Guaranty Trust Bank (GTBank)',
            'account_name' => $sellerAcc?->account_name ?? ($this->invoice?->seller?->business_name ?: ($this->invoice?->seller?->name ?? 'Verified Seller')),
            'account_number' => $sellerAcc?->account_number ?? '0123456789',
        ];

        return view('livewire.dashboard.invoices.invoice-view', [
            'invoice' => $this->invoice,
            'isSeller' => $this->isSeller,
            'isBuyer' => $this->isBuyer,
            'isWarrantyOver' => $this->isWarrantyOver,
            'paymentOption' => $this->paymentOption,
            'escrowPercentage' => $escrowPercentage,
            'escrowCap' => $escrowCap,
            'escrowFee' => $escrowFee,
            'activeEscrowFee' => $activeEscrowFee,
            'offerDiscount' => $offerDiscount,
            'couponDiscount' => $couponDisc,
            'totalPayable' => $totalPayable,
            'sellerBank' => $sellerBank,
        ]);
    }
}
