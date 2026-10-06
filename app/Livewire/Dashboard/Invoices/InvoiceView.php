<?php

namespace App\Livewire\Dashboard\Invoices;

use App\Models\Coupon;
use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Item;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Payment;
use App\Models\Replacement;
use App\Models\ReturnRecord;
use App\Models\ServiceJob;
use App\Models\ServiceReview;
use App\Models\Settlement;
use App\Models\Shipment;
use App\Models\WarrantyClaim;
use App\Services\Payment\EscrowService;
use App\Services\PostSale\IssueResolutionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Commercial Invoice — Dashboard')]
class InvoiceView extends Component
{
    public ?Invoice $invoice = null;

    // Active navigation tab ('details', 'service', 'shipment', 'issue', 'dispute', 'replacement', 'refund', 'warranty')
    #[Url(as: 'tab')]
    public string $activeTab = 'details';

    // Payment Option selection ('platform' vs 'direct')
    public string $paymentOption = 'platform';

    // Coupon / Promo Code State (for platform escrow payments)
    public string $couponCode = '';
    public ?int $appliedCouponId = null;
    public float $couponDiscount = 0.0;
    public string $couponMessage = '';
    public bool $couponValid = false;

    // Financial Portfolio & Ledger State
    public bool $showFinancialSummary = true;
    public string $financialTab = 'earnings'; // 'earnings' or 'spendings'

    // Listing Item Review Form State
    public int $rating = 5;
    public string $reviewComment = '';
    public bool $reviewSubmitted = false;

    // Service Job Review Form State
    public int $serviceRating = 5;
    public string $serviceReviewComment = '';
    public bool $serviceReviewSubmitted = false;

    // Seller Outbound Shipment Modal
    public bool $showShipmentModal = false;
    public string $carrierName = 'Parts & Parcel Express';
    public string $trackingNumber = '';
    public string $dispatchNotes = '';
    public string $dispatchEvidence = '';

    // Buyer Delivery Confirmation Modal
    public bool $showReceiveConfirmModal = false;

    // Buyer Report Issue Modal (Itemized rejection)
    public bool $showIssueModal = false;
    public string $issueType = 'defective'; // damaged, defective, incompatibility, not_as_described, wrong_item, lost_or_missing
    public string $issueDescription = '';
    public array $selectedIssueItemIds = [];
    public array $issueItemReasons = [];
    public array $issueItemEvidences = [];
    public string $issueEvidence = '';

    // Seller Issue Response Modal
    public bool $showSellerIssueResponseModal = false;
    public ?int $activeIssueId = null;
    public string $sellerResolutionType = 'replacement'; // replacement, refund
    public bool $sellerRequiresReturn = true;
    public string $sellerReturnMethod = 'shipment'; // shipment, dropoff
    public string $sellerResponseNotes = '';

    // Seller Contest Issue Modal (Escalates to Dispute)
    public bool $showSellerContestModal = false;
    public ?int $contestIssueId = null;
    public string $contestReason = '';
    public string $contestEvidence = '';

    // Buyer Return Dispatch Modal
    public bool $showBuyerReturnModal = false;
    public ?int $activeReturnId = null;
    public string $returnCarrier = 'GIGM / Local Courier';
    public string $returnTrackingNumber = '';
    public string $returnNotes = '';
    public string $returnEvidence = '';

    // Seller Return Inspection / Rejection Modal (Escalates to Dispute)
    public bool $showSellerReturnRejectModal = false;
    public ?int $activeReturnRejectId = null;
    public string $returnRejectReason = '';
    public string $returnRejectEvidence = '';

    // Buyer Replacement Inspection / Rejection Modal (Escalates to Dispute)
    public bool $showBuyerReplacementRejectModal = false;
    public ?int $activeReplacementId = null;
    public string $replacementRejectReason = '';
    public string $replacementRejectEvidence = '';

    // Buyer Warranty Claim Modal
    public bool $showWarrantyModal = false;
    public ?int $warrantyItemId = null;
    public string $warrantyClaimType = 'defect'; // defect, hardware_failure, malfunction, wear_tear
    public string $warrantyReason = '';
    public string $warrantyEvidence = '';

    // Seller Warranty Response Modal (Escalates to Dispute if rejected)
    public bool $showSellerWarrantyModal = false;
    public ?int $activeWarrantyClaimId = null;
    public string $warrantyResolutionDecision = 'accept'; // accept, reject
    public string $warrantyRemedy = 'replacement'; // replacement, repair
    public string $warrantyResolutionNotes = '';
    public string $warrantyRejectEvidence = '';

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['details', 'service', 'shipment', 'issue', 'dispute', 'replacement', 'refund', 'warranty'])) {
            $this->activeTab = $tab;
        }
    }

    public function mount($invoice = null, $invoice_id = null): void
    {
        $identifier = $invoice ?? $invoice_id;
        $relations = [
            'buyer.primaryLocation',
            'seller.bankAccounts',
            'seller.primaryLocation',
            'seller.country',
            'items.itemable',
            'issues.items.invoiceItem',
            'issues.dispute',
            'issues.returnRecord.shipment',
            'issues.replacement.shipment',
            'serviceJobs.review',
            'serviceJobs.provider',
            'serviceJobs.location',
            'serviceJobs.brand',
            'serviceJobs.deviceModel',
            'refunds.payment',
            'refunds.items',
            'settlement.payment',
            'payments',
        ];

        if ($identifier instanceof Invoice) {
            $this->invoice = $identifier;
            if (! $this->invoice->relationLoaded('items')) {
                $this->invoice->load($relations);
            }
        } elseif ($identifier) {
            $this->invoice = Invoice::with($relations)
                ->where('id', $identifier)
                ->orWhere('invoice_number', $identifier)
                ->first();
        }

        if (! $this->invoice) {
            $this->invoice = Invoice::with($relations)->latest()->first();
        }

        if ($this->invoice && in_array($this->invoice->payment_method, ['direct', 'platform'])) {
            $this->paymentOption = $this->invoice->payment_method;
        }

        // Check if reviews already exist
        if ($this->invoice && Auth::check()) {
            $hasListingReview = ListingReview::where('user_id', Auth::id())
                ->whereIn('listing_id', $this->invoice->items->pluck('itemable_id'))
                ->exists();
            if ($hasListingReview) {
                $this->reviewSubmitted = true;
            }

            $serviceJob = $this->invoice->serviceJobs->first();
            if ($serviceJob && $serviceJob->review) {
                $this->serviceReviewSubmitted = true;
            }
        }
    }

    public function closeModals(): void
    {
        $this->showShipmentModal = false;
        $this->showReceiveConfirmModal = false;
        $this->showIssueModal = false;
        $this->showSellerIssueResponseModal = false;
        $this->showSellerContestModal = false;
        $this->showBuyerReturnModal = false;
        $this->showSellerReturnRejectModal = false;
        $this->showBuyerReplacementRejectModal = false;
        $this->showWarrantyModal = false;
        $this->showSellerWarrantyModal = false;
    }

    public function toggleFinancialSummary(): void
    {
        $this->showFinancialSummary = ! $this->showFinancialSummary;
    }

    public function setFinancialTab(string $tab): void
    {
        if (in_array($tab, ['earnings', 'spendings'])) {
            $this->financialTab = $tab;
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
        return (int) Auth::id() === (int) $this->invoice->seller_id;
    }

    /**
     * Check if currently logged in user is the buyer of this invoice.
     */
    public function getIsBuyerProperty(): bool
    {
        if (! Auth::check() || ! $this->invoice) {
            return false;
        }
        return (int) Auth::id() === (int) $this->invoice->buyer_id;
    }

    /**
     * Check if warranty period has passed.
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
     * Seller marks invoice direct payment received.
     */
    public function confirmDirectPaymentReceived(): void
    {
        if (! $this->invoice) {
            return;
        }

        $user = Auth::user();
        if (! $this->isSeller && (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin())) {
            session()->flash('error', 'Only the seller can confirm direct payment receipt.');
            return;
        }

        $this->invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Record settlement for seller for direct payment
        \App\Models\Settlement::firstOrCreate(
            ['invoice_id' => $this->invoice->id],
            [
                'seller_id' => $this->invoice->seller_id,
                'payment_id' => null,
                'amount' => (float) $this->invoice->total,
                'currency' => $this->invoice->currency ?? 'NGN',
                'status' => 'settled',
                'eligible_at' => now(),
                'settled_at' => now(),
            ]
        );

        app(\App\Services\Commercial\NegotiationService::class)->handleInvoicePaid($this->invoice);

        $this->invoice->refresh();

        session()->flash('seller_success', 'Payment confirmed! This invoice is now marked as PAID. Your verified sales count and seller reputation have been updated.');
    }

    /**
     * Buyer reports "I didn't buy it".
     */
    public function reportDidNotBuy(): void
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

        session()->flash('buyer_notice', 'Invoice #' . $this->invoice->invoice_number . ' has been marked as cancelled and payment status revoked.');
    }

    /**
     * Seller opens modal to mark package as shipped.
     */
    public function openShipmentModal(): void
    {
        if (empty($this->trackingNumber)) {
            $existing = $this->invoice->outboundShipment();
            $this->trackingNumber = $existing?->tracking_number ?: ('TRK-' . strtoupper(Str::random(10)));
            $this->carrierName = $existing?->provider_name ?: 'Parts & Parcel Express';
        }
        $this->showShipmentModal = true;
    }

    /**
     * Seller submits shipping evidence and marks package dispatched.
     */
    public function markAsShipped(): void
    {
        $this->validate([
            'carrierName' => 'required|string|max:100',
            'trackingNumber' => 'required|string|max:50',
            'dispatchNotes' => 'nullable|string|max:500',
            'dispatchEvidence' => 'nullable|string|max:1000',
        ]);

        $shipment = $this->invoice->outboundShipment();
        $originLoc = $this->invoice->seller?->primaryLocation;
        $destLoc = $this->invoice->buyer?->primaryLocation;

        if (! $shipment) {
            $shipment = Shipment::create([
                'sender_id' => $this->invoice->seller_id,
                'receiver_id' => $this->invoice->buyer_id,
                'provider_name' => $this->carrierName,
                'tracking_number' => $this->trackingNumber,
                'status' => 'dispatched',
                'origin_location_id' => $originLoc?->id,
                'origin_contact_name' => $originLoc?->contact_name ?: $this->invoice->seller?->name,
                'origin_contact_phone' => $originLoc?->phone ?: $this->invoice->seller?->phone,
                'origin_address_line_1' => $originLoc?->address_line_1 ?: 'Seller Dispatch Point',
                'origin_city' => $originLoc?->city ?: 'Lagos',
                'origin_state' => $originLoc?->state?->name ?? 'Lagos',
                'destination_location_id' => $destLoc?->id,
                'destination_contact_name' => $destLoc?->contact_name ?: $this->invoice->buyer?->name,
                'destination_contact_phone' => $destLoc?->phone ?: $this->invoice->buyer?->phone,
                'destination_address_line_1' => $destLoc?->address_line_1 ?: 'Customer Address',
                'destination_city' => $destLoc?->city ?: 'Lagos',
                'destination_state' => $destLoc?->state?->name ?? 'Lagos',
                'dispatched_at' => now(),
                'notes' => $this->dispatchNotes,
                'evidence' => $this->dispatchEvidence,
            ]);

            // Link to invoice item if delivery item exists
            $deliveryItem = $this->invoice->items()->whereIn('type', ['pickup', 'delivery'])->first();
            if ($deliveryItem) {
                $deliveryItem->update([
                    'itemable_id' => $shipment->id,
                    'itemable_type' => Shipment::class,
                ]);
            }
        } else {
            $shipment->update([
                'provider_name' => $this->carrierName,
                'tracking_number' => $this->trackingNumber,
                'status' => 'dispatched',
                'dispatched_at' => now(),
                'notes' => $this->dispatchNotes ?: $shipment->notes,
                'evidence' => $this->dispatchEvidence ?: $shipment->evidence,
            ]);
        }

        $this->showShipmentModal = false;
        $this->invoice->refresh();

        session()->flash('seller_success', "Package marked as shipped! Tracking #{$shipment->tracking_number} recorded. The buyer has been notified.");
    }

    /**
     * Buyer opens confirmation modal for package reception.
     */
    public function openReceiveConfirmModal(): void
    {
        $this->showReceiveConfirmModal = true;
    }

    /**
     * Buyer confirms physical package reception.
     */
    public function confirmPackageReceived(): void
    {
        $shipment = $this->invoice->outboundShipment();
        if ($shipment) {
            $shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        $this->showReceiveConfirmModal = false;
        $this->invoice->refresh();

        session()->flash('buyer_notice', 'Package reception confirmed! Please inspect your package now. If satisfied, click "Complete Deal" to release escrow funds, or "Report Something" if there is an issue.');
    }

    /**
     * Buyer completes deal: marks accepted, releases escrow, starts warranty window.
     */
    public function completeDeal(): void
    {
        $this->invoice->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'completed_at' => now(),
        ]);

        app(EscrowService::class)->startInspectionWarrantyWindow($this->invoice);

        $this->invoice->refresh();

        session()->flash('buyer_payment_success', 'Deal successfully completed! Escrow settlement has been finalized for the seller. You can now leave your verified review.');
    }

    /**
     * Buyer opens report issue modal.
     */
    public function openIssueModal(): void
    {
        $this->showReceiveConfirmModal = false;
        $this->issueType = 'defective';
        $this->issueDescription = '';
        $this->issueEvidence = '';
        $this->selectedIssueItemIds = $this->invoice ? $this->invoice->items->pluck('id')->map(fn($id) => (int) $id)->toArray() : [];
        $this->issueItemReasons = [];
        $this->issueItemEvidences = [];
        $this->showIssueModal = true;
    }

    /**
     * Buyer submits issue/rejection specifying itemized details.
     */
    public function submitReportIssue(): void
    {
        $this->validate([
            'issueType' => 'required|string',
            'issueDescription' => 'required|string|min:10|max:1000',
            'selectedIssueItemIds' => 'required|array|min:1',
        ], [
            'selectedIssueItemIds.required' => 'Please select at least one item you are reporting an issue with.',
            'selectedIssueItemIds.min' => 'Please select at least one item you are reporting an issue with.',
        ]);

        $items = [];
        foreach ($this->selectedIssueItemIds as $itemId) {
            $itemIdInt = (int) $itemId;
            $items[] = [
                'invoice_item_id' => $itemIdInt,
                'reason' => !empty($this->issueItemReasons[$itemIdInt]) ? $this->issueItemReasons[$itemIdInt] : $this->issueType,
                'evidence' => !empty($this->issueItemEvidences[$itemIdInt]) ? $this->issueItemEvidences[$itemIdInt] : $this->issueEvidence,
            ];
        }

        app(IssueResolutionService::class)->reportIssue(
            $this->invoice,
            Auth::user(),
            $this->issueType,
            $this->issueDescription,
            $items
        );

        $this->showIssueModal = false;
        $this->invoice->refresh();

        session()->flash('buyer_notice', 'Issue reported! Platform escrow funds have been frozen. The seller has been notified to propose a resolution (refund, replacement, or return).');
    }

    /**
     * Seller opens issue resolution modal.
     */
    public function openSellerIssueResponseModal(int $issueId): void
    {
        $this->activeIssueId = $issueId;
        $this->sellerResolutionType = 'replacement';
        $this->sellerRequiresReturn = true;
        $this->sellerReturnMethod = 'shipment';
        $this->sellerResponseNotes = '';
        $this->showSellerIssueResponseModal = true;
    }

    /**
     * Seller submits resolution proposal (refund / replacement with/without return).
     */
    public function sellerRespondToIssue(): void
    {
        $issue = Issue::findOrFail($this->activeIssueId);

        if ($this->sellerRequiresReturn) {
            $return = ReturnRecord::create([
                'invoice_id' => $this->invoice->id,
                'issue_id' => $issue->id,
                'buyer_id' => $this->invoice->buyer_id,
                'seller_id' => $this->invoice->seller_id,
                'status' => 'pending',
                'delivery_method' => $this->sellerReturnMethod,
                'notes' => $this->sellerResponseNotes ?: "Return requested for resolution: {$this->sellerResolutionType}",
            ]);

            if ($this->sellerReturnMethod === 'shipment') {
                $buyerLoc = $this->invoice->buyer?->primaryLocation;
                $sellerLoc = $this->invoice->seller?->primaryLocation;

                $returnShipment = Shipment::create([
                    'sender_id' => $this->invoice->buyer_id,
                    'receiver_id' => $this->invoice->seller_id,
                    'provider_name' => 'Return Logistics',
                    'tracking_number' => 'RET-' . strtoupper(Str::random(8)),
                    'status' => 'pending',
                    'origin_location_id' => $buyerLoc?->id,
                    'origin_contact_name' => $buyerLoc?->contact_name ?: $this->invoice->buyer?->name,
                    'origin_contact_phone' => $buyerLoc?->phone ?: $this->invoice->buyer?->phone,
                    'origin_address_line_1' => $buyerLoc?->address_line_1 ?: 'Buyer Return Address',
                    'origin_city' => $buyerLoc?->city ?: 'Lagos',
                    'origin_state' => $buyerLoc?->state?->name ?? 'Lagos',
                    'destination_location_id' => $sellerLoc?->id,
                    'destination_contact_name' => $sellerLoc?->contact_name ?: $this->invoice->seller?->name,
                    'destination_contact_phone' => $sellerLoc?->phone ?: $this->invoice->seller?->phone,
                    'destination_address_line_1' => $sellerLoc?->address_line_1 ?: 'Seller Return Address',
                    'destination_city' => $sellerLoc?->city ?: 'Lagos',
                    'destination_state' => $sellerLoc?->state?->name ?? 'Lagos',
                    'notes' => "Return for issue #{$issue->id}",
                ]);

                $return->update(['shipment_id' => $returnShipment->id]);
            }

            if ($this->sellerResolutionType === 'replacement') {
                Replacement::create([
                    'invoice_id' => $this->invoice->id,
                    'issue_id' => $issue->id,
                    'buyer_id' => $this->invoice->buyer_id,
                    'seller_id' => $this->invoice->seller_id,
                    'status' => 'pending_return',
                    'delivery_method' => $this->sellerReturnMethod,
                    'notes' => 'Replacement will be dispatched upon return receipt.',
                ]);
            }
        } else {
            app(IssueResolutionService::class)->sellerAcceptsIssue($issue, Auth::user(), $this->sellerResolutionType);
        }

        $this->showSellerIssueResponseModal = false;
        $this->invoice->refresh();

        session()->flash('seller_success', 'Resolution proposal recorded! Buyer has been notified.');
    }

    /**
     * Seller opens contest modal to disagree with buyer's issue.
     */
    public function openSellerContestModal(int $issueId): void
    {
        $this->contestIssueId = $issueId;
        $this->contestReason = '';
        $this->contestEvidence = '';
        $this->showSellerContestModal = true;
    }

    /**
     * Seller submits contestation -> Creates dispute on the issue.
     */
    public function sellerContestIssue(): void
    {
        $this->validate([
            'contestReason' => 'required|string|min:10|max:1000',
        ]);

        $issue = Issue::findOrFail($this->contestIssueId);
        $dispute = app(IssueResolutionService::class)->sellerContestsIssue(
            $issue,
            Auth::user(),
            $this->contestReason,
            $this->contestEvidence ?: null
        );

        $this->showSellerContestModal = false;
        $this->invoice->refresh();

        session()->flash('seller_success', "Issue contested! Dispute #DSP-{$dispute->id} has been opened for platform mediation. Escrow funds remain protected.");
    }

    /**
     * Buyer opens return dispatch modal.
     */
    public function openBuyerReturnModal(int $returnId): void
    {
        $this->activeReturnId = $returnId;
        $return = ReturnRecord::find($returnId);
        if ($return && $return->shipment) {
            $this->returnTrackingNumber = $return->shipment->tracking_number ?: ('RET-' . strtoupper(Str::random(8)));
        }
        $this->showBuyerReturnModal = true;
    }

    /**
     * Buyer confirms return package has been shipped/dropped off.
     */
    public function buyerConfirmReturnShipped(): void
    {
        $return = ReturnRecord::findOrFail($this->activeReturnId);
        $return->update([
            'status' => 'in_transit',
            'notes' => $this->returnNotes ?: $return->notes,
        ]);

        if ($return->shipment) {
            $return->shipment->update([
                'provider_name' => $this->returnCarrier ?: $return->shipment->provider_name,
                'tracking_number' => $this->returnTrackingNumber ?: $return->shipment->tracking_number,
                'status' => 'dispatched',
                'dispatched_at' => now(),
                'notes' => $this->returnNotes,
                'evidence' => $this->returnEvidence,
            ]);
        }

        $this->showBuyerReturnModal = false;
        $this->invoice->refresh();

        session()->flash('buyer_notice', 'Return dispatch recorded! The seller has been notified to track and confirm receipt of the return package.');
    }

    /**
     * Seller confirms receipt of returned item in satisfactory condition.
     */
    public function sellerConfirmReturnReceived(int $returnId): void
    {
        $return = ReturnRecord::findOrFail($returnId);
        $return->update([
            'status' => 'received',
            'received_at' => now(),
            'accepted_at' => now(),
        ]);

        if ($return->shipment) {
            $return->shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        $replacement = Replacement::where('issue_id', $return->issue_id)->first();
        if ($replacement) {
            $replacement->update(['status' => 'ready_for_dispatch']);
        } elseif ($return->issue) {
            app(IssueResolutionService::class)->sellerAcceptsIssue($return->issue, Auth::user(), 'refund');
        }

        $this->invoice->refresh();
        session()->flash('seller_success', 'Returned item marked as received and verified! Process advancing.');
    }

    /**
     * Seller opens return rejection modal (problem with returned goods).
     */
    public function openSellerReturnRejectModal(int $returnId): void
    {
        $this->activeReturnRejectId = $returnId;
        $this->returnRejectReason = '';
        $this->returnRejectEvidence = '';
        $this->showSellerReturnRejectModal = true;
    }

    /**
     * Seller rejects returned package condition -> Creates dispute on the return.
     */
    public function sellerRejectReturn(): void
    {
        $this->validate([
            'returnRejectReason' => 'required|string|min:10|max:1000',
        ]);

        $return = ReturnRecord::findOrFail($this->activeReturnRejectId);
        $dispute = app(IssueResolutionService::class)->sellerRejectsReturn(
            $return,
            Auth::user(),
            $this->returnRejectReason,
            $this->returnRejectEvidence ?: null
        );

        $this->showSellerReturnRejectModal = false;
        $this->invoice->refresh();

        session()->flash('seller_success', "Return condition rejected! Dispute #DSP-{$dispute->id} has been opened for platform mediation.");
    }

    /**
     * Buyer opens replacement rejection modal (problem with replacement unit).
     */
    public function openBuyerReplacementRejectModal(int $replacementId): void
    {
        $this->activeReplacementId = $replacementId;
        $this->replacementRejectReason = '';
        $this->replacementRejectEvidence = '';
        $this->showBuyerReplacementRejectModal = true;
    }

    /**
     * Buyer rejects delivered replacement -> Escalates to Dispute.
     */
    public function buyerRejectReplacement(): void
    {
        $this->validate([
            'replacementRejectReason' => 'required|string|min:10|max:1000',
        ]);

        $replacement = Replacement::findOrFail($this->activeReplacementId);
        $dispute = app(IssueResolutionService::class)->buyerRejectsReplacement(
            $replacement,
            Auth::user(),
            $this->replacementRejectReason,
            $this->replacementRejectEvidence ?: null
        );

        $this->showBuyerReplacementRejectModal = false;
        $this->invoice->refresh();

        session()->flash('buyer_notice', "Replacement rejected! Dispute #DSP-{$dispute->id} has been escalated to mediation.");
    }

    /**
     * Buyer accepts delivered replacement unit.
     */
    public function buyerAcceptReplacement(int $replacementId): void
    {
        $replacement = Replacement::findOrFail($replacementId);
        $replacement->update([
            'status' => 'delivered',
            'accepted_at' => now(),
        ]);

        if ($replacement->issue) {
            $replacement->issue->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
        }

        if ($replacement->warrantyClaim) {
            $replacement->warrantyClaim->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
        }

        if ($this->invoice->settlement) {
            app(EscrowService::class)->unfreezeAfterDispute($this->invoice->settlement);
        }

        $this->invoice->refresh();
        session()->flash('buyer_payment_success', 'Replacement unit accepted! Transaction issue resolved successfully.');
    }

    /**
     * Buyer opens warranty claim modal.
     */
    public function openWarrantyModal(?int $itemId = null): void
    {
        $this->warrantyItemId = $itemId;
        $this->warrantyClaimType = 'defect';
        $this->warrantyReason = '';
        $this->warrantyEvidence = '';
        $this->showWarrantyModal = true;
    }

    /**
     * Buyer submits warranty claim during active coverage.
     */
    public function submitWarrantyClaim(): void
    {
        $this->validate([
            'warrantyReason' => 'required|string|min:10|max:1000',
            'warrantyClaimType' => 'required|string',
        ]);

        $claim = app(IssueResolutionService::class)->fileWarrantyClaim(
            invoice: $this->invoice,
            buyer: Auth::user(),
            invoiceItemId: $this->warrantyItemId,
            description: $this->warrantyReason,
            evidence: $this->warrantyEvidence ?: null,
            claimType: $this->warrantyClaimType
        );

        $this->showWarrantyModal = false;
        $this->invoice->refresh();

        session()->flash('buyer_notice', "Warranty claim #CLM-{$claim->id} submitted! The seller has been notified to inspect and resolve your claim.");
    }

    /**
     * Seller opens warranty response modal.
     */
    public function openSellerWarrantyModal(int $claimId): void
    {
        $this->activeWarrantyClaimId = $claimId;
        $this->warrantyResolutionDecision = 'accept';
        $this->warrantyRemedy = 'replacement';
        $this->warrantyResolutionNotes = '';
        $this->warrantyRejectEvidence = '';
        $this->showSellerWarrantyModal = true;
    }

    /**
     * Seller responds to warranty claim (accepts remedy or refuses -> dispute).
     */
    public function sellerRespondToWarrantyClaim(): void
    {
        $claim = WarrantyClaim::findOrFail($this->activeWarrantyClaimId);

        if ($this->warrantyResolutionDecision === 'accept') {
            app(IssueResolutionService::class)->sellerAcceptsWarranty(
                claim: $claim,
                seller: Auth::user(),
                remedy: $this->warrantyRemedy,
                notes: $this->warrantyResolutionNotes ?: null
            );
            session()->flash('seller_success', 'Warranty claim accepted! Replacement terms have been recorded.');
        } else {
            $this->validate([
                'warrantyResolutionNotes' => 'required|string|min:10|max:1000',
            ]);

            $dispute = app(IssueResolutionService::class)->sellerRejectsWarranty(
                claim: $claim,
                seller: Auth::user(),
                reason: $this->warrantyResolutionNotes,
                evidence: $this->warrantyRejectEvidence ?: null
            );
            session()->flash('seller_success', "Warranty claim refused. Dispute #DSP-{$dispute->id} has been opened for platform mediation.");
        }

        $this->showSellerWarrantyModal = false;
        $this->invoice->refresh();
    }

    /**
     * Buyer submits review for the purchased item.
     */
    public function submitReview(): void
    {
        if (! $this->invoice || ! $this->isBuyer) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:500',
        ]);

        $items = $this->invoice->items()->get();

        foreach ($items as $item) {
            $listingId = null;
            if (($item->itemable_type === Listing::class || $item->itemable_type === 'listing') && $item->itemable_id) {
                $listingId = $item->itemable_id;
            } else {
                $itemId = ($item->itemable_type === Item::class || $item->itemable_type === 'item') ? $item->itemable_id : null;
                $listing = Listing::firstOrCreate(
                    $itemId ? ['item_id' => $itemId] : ['user_id' => $this->invoice->seller_id],
                    [
                        'user_id' => $this->invoice->seller_id,
                        'item_id' => $itemId,
                        'price' => $item->unit_price,
                        'quantity' => 1,
                        'is_published' => true,
                        'is_active' => true,
                    ]
                );
                $listingId = $listing->id;
            }

            if ($listingId) {
                ListingReview::updateOrCreate(
                    [
                        'listing_id' => $listingId,
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

    /**
     * Buyer submits review for service technician.
     */
    public function submitServiceReview(): void
    {
        if (! $this->invoice || ! $this->isBuyer) {
            return;
        }

        $this->validate([
            'serviceRating' => 'required|integer|min:1|max:5',
            'serviceReviewComment' => 'nullable|string|max:500',
        ]);

        $serviceJob = $this->invoice->serviceJobs()->first();

        if ($serviceJob) {
            ServiceReview::updateOrCreate(
                [
                    'service_job_id' => $serviceJob->id,
                    'reviewer_id' => Auth::id(),
                ],
                [
                    'provider_id' => $this->invoice->seller_id,
                    'rating' => $this->serviceRating,
                    'review' => $this->serviceReviewComment ?: 'Service completed successfully.',
                ]
            );
        }

        $this->serviceReviewSubmitted = true;
        session()->flash('review_success', 'Thank you! Your service review and technician rating have been recorded.');
    }

    public function setPaymentOption(string $method): void
    {
        $this->paymentOption = in_array($method, ['platform', 'direct']) ? $method : 'platform';
        if ($this->paymentOption === 'direct') {
            $this->removeCoupon();
        }
    }

    public function applyCoupon(): void
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

    public function removeCoupon(): void
    {
        $this->couponCode = '';
        $this->appliedCouponId = null;
        $this->couponDiscount = 0.0;
        $this->couponMessage = '';
        $this->couponValid = false;
        $this->resetErrorBag('couponCode');
    }

    public function payWithPlatformEscrow(): void
    {
        if (! $this->invoice || $this->invoice->status === 'paid' || $this->invoice->status === 'cancelled') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to complete payment.');
            $this->redirectRoute('login');
            return;
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
            'escrow_fee' => $escrowFee,
            'currency' => $user->currency ?? $this->invoice->currency ?? 'NGN',
            'metadata' => [
                'invoice_id' => $this->invoice->id,
                'coupon_code' => $this->appliedCouponId ? Coupon::find($this->appliedCouponId)?->code : null,
                'coupon_discount' => $couponDisc,
                'escrow_fee' => $escrowFee,
                'escrow_percentage' => $escrowPercentage,
                'escrow_cap' => $escrowCap,
            ],
        ]);

        app(EscrowService::class)->handlePaymentSuccessful($payment);

        if ($this->appliedCouponId) {
            Coupon::find($this->appliedCouponId)?->recordUsage();
        }

        $this->invoice->refresh();
        session()->flash('buyer_payment_success', 'Payment of ' . $this->invoice->currency_symbol . number_format($totalPayable) . ' confirmed with Parts & Parcel Escrow! Funds are securely locked in escrow pending shipment and inspection.');
    }

    public function confirmDirectTransferSent(): void
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
        session()->flash('buyer_direct_notice', 'Direct transfer recorded! Please transfer ' . $this->invoice->currency_symbol . number_format($totalPayable) . " directly to the seller's account. Keep your payment transfer receipt.");
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

        $outboundShipment = $this->invoice?->outboundShipment();
        $returnShipment = $this->invoice?->returnShipment();
        // Tab Data Collections
        $issues = $this->invoice?->issues()->with(['items.invoiceItem', 'reporter', 'dispute', 'returnRecord', 'replacement'])->latest()->get() ?? collect();
        $disputes = \App\Models\Dispute::where('invoice_id', $this->invoice?->id)->with(['opener', 'respondent', 'resolver', 'issue', 'warrantyClaim', 'replacement', 'returnRecord', 'items'])->latest()->get();
        $warrantyClaims = \App\Models\WarrantyClaim::where('invoice_id', $this->invoice?->id)->with(['item', 'invoiceItem', 'buyer', 'seller', 'dispute', 'replacement', 'returnRecord'])->latest()->get();
        $replacements = \App\Models\Replacement::where('invoice_id', $this->invoice?->id)->with(['shipment', 'issue', 'warrantyClaim', 'dispute'])->latest()->get();
        $refunds = $this->invoice?->refunds()->with(['payment', 'items'])->latest()->get() ?? collect();
        $serviceJobs = $this->invoice?->serviceJobs()->with(['provider', 'location', 'brand', 'deviceModel', 'review'])->get() ?? collect();

        $latestIssue = $issues->first();
        $latestReturn = $this->invoice?->returnRecord()->latest()->first();

        // Financial Ledger & Multi-Currency Portfolio
        $authUser = Auth::user();
        $userEarnings = $authUser ? $authUser->getEarningsByCurrency() : [];
        $userSpendings = $authUser ? $authUser->getSpendingsByCurrency() : [];
        $invoiceSettlement = $this->invoice?->settlement;
        if (! $invoiceSettlement && $this->invoice) {
            $invoiceSettlement = Settlement::with('payment')->where('invoice_id', $this->invoice->id)->first();
        }
        $invoicePayment = $invoiceSettlement?->payment ?: $this->invoice?->payments()->whereIn('status', ['successful', 'paid', 'held_in_escrow'])->latest()->first();

        // Dynamic Tab Availability Flags
        $hasShipments = (bool) ($outboundShipment || $returnShipment || ($this->invoice && $this->invoice->items->whereIn('type', ['pickup', 'delivery', 'return'])->isNotEmpty()));
        $hasIssues = $issues->isNotEmpty();
        $hasReplacements = $replacements->isNotEmpty();
        $hasRefunds = $refunds->isNotEmpty();
        $hasDisputes = $disputes->isNotEmpty();
        $hasWarranty = (bool) ($this->invoice && ($this->invoice->hasWarranty() || $warrantyClaims->isNotEmpty() || $issues->where('type', 'warranty_claim')->isNotEmpty()));
        $hasServices = (bool) ($this->invoice && ($this->invoice->hasServices() || $serviceJobs->isNotEmpty()));

        // Active tab validation / fallback
        $validTabs = ['details'];
        if ($hasServices) $validTabs[] = 'service';
        if ($hasShipments) $validTabs[] = 'shipment';
        if ($hasIssues || in_array($this->invoice?->status, ['paid', 'accepted'])) $validTabs[] = 'issue';
        if ($hasDisputes) $validTabs[] = 'dispute';
        if ($hasReplacements) $validTabs[] = 'replacement';
        if ($hasRefunds) $validTabs[] = 'refund';
        if ($hasWarranty) $validTabs[] = 'warranty';

        if (! in_array($this->activeTab, $validTabs)) {
            $this->activeTab = 'details';
        }

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
            'outboundShipment' => $outboundShipment,
            'returnShipment' => $returnShipment,
            'latestIssue' => $latestIssue,
            'latestReturn' => $latestReturn,
            'userEarnings' => $userEarnings,
            'userSpendings' => $userSpendings,
            'invoiceSettlement' => $invoiceSettlement,
            'invoicePayment' => $invoicePayment,
            'showFinancialSummary' => $this->showFinancialSummary,
            'financialTab' => $this->financialTab,
            'activeTab' => $this->activeTab,
            'hasShipments' => $hasShipments,
            'hasIssues' => $hasIssues,
            'hasReplacements' => $hasReplacements,
            'hasRefunds' => $hasRefunds,
            'hasDisputes' => $hasDisputes,
            'hasWarranty' => $hasWarranty,
            'hasServices' => $hasServices,
            'issues' => $issues,
            'disputes' => $disputes,
            'warrantyClaims' => $warrantyClaims,
            'replacements' => $replacements,
            'refunds' => $refunds,
            'serviceJobs' => $serviceJobs,
        ]);
    }
}
