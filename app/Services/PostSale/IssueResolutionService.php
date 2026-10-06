<?php

namespace App\Services\PostSale;

use App\Jobs\RefundPaymentJob;
use App\Models\Dispute;
use App\Models\DisputeItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Issue;
use App\Models\IssueItem;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Replacement;
use App\Models\ReplacementItem;
use App\Models\ReturnItem;
use App\Models\ReturnRecord;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Services\Payment\EscrowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IssueResolutionService
{
    public function __construct(
        protected EscrowService $escrowService
    ) {}

    /**
     * Buyer reports an issue on an invoice (e.g. damaged, wrong_item, missing, defective).
     * Automatically freezes escrow settlement funds.
     */
    public function reportIssue(
        Invoice $invoice,
        User $reporter,
        string $type,
        string $description,
        array $items = []
    ): Issue {
        if ($invoice->buyer_id !== $reporter->id && ! $reporter->isAdmin()) {
            throw ValidationException::withMessages([
                'reporter' => 'Only the buyer or an administrator can report an issue on this invoice.',
            ]);
        }

        $validTypes = ['damaged', 'defective', 'incompatibility', 'not_as_described', 'wrong_item', 'lost_or_missing'];
        $cleanType = in_array($type, $validTypes, true) ? $type : 'defective';

        return DB::transaction(function () use ($invoice, $reporter, $cleanType, $description, $items) {
            $issue = Issue::create([
                'invoice_id' => $invoice->id,
                'reported_by' => $reporter->id,
                'type' => $cleanType,
                'status' => 'open',
                'description' => $description,
            ]);

            foreach ($items as $itemData) {
                $invoiceItemId = $itemData['invoice_item_id'] ?? null;
                $reason = $itemData['reason'] ?? $cleanType;
                $evidence = is_array($itemData['evidence'] ?? null)
                    ? json_encode($itemData['evidence'])
                    : ($itemData['evidence'] ?? null);

                IssueItem::create([
                    'issue_id' => $issue->id,
                    'invoice_item_id' => $invoiceItemId,
                    'reason' => $reason,
                    'evidence' => $evidence,
                ]);
            }

            // Freeze escrow funds while issue is being investigated
            if ($invoice->settlement) {
                $this->escrowService->freezeForDispute($invoice->settlement);
            }

            return $issue;
        });
    }

    /**
     * Calculate automatic per-item refund amount according to platform policy.
     */
    public function calculateItemRefund(InvoiceItem $invoiceItem, int $quantity = 1): float
    {
        $qty = min($quantity, $invoiceItem->quantity);
        return round((float) $invoiceItem->unit_price * $qty, 2);
    }

    /**
     * Seller accepts responsibility: platform automatically processes refund, replacement, or return.
     */
    public function sellerAcceptsIssue(Issue $issue, User $seller, string $resolutionType = 'refund'): mixed
    {
        $invoice = $issue->invoice;

        if ($invoice->seller_id !== $seller->id && ! $seller->isAdmin()) {
            throw ValidationException::withMessages([
                'seller' => 'Only the seller or an administrator can accept responsibility for this issue.',
            ]);
        }

        return DB::transaction(function () use ($issue, $invoice, $resolutionType) {
            $result = null;

            if ($resolutionType === 'refund') {
                $result = $this->createAndDispatchRefund($issue);
            } elseif ($resolutionType === 'replacement') {
                $result = $this->createReplacementRecord($issue);
            } elseif ($resolutionType === 'return') {
                $result = $this->createReturnRecord($issue);
            }

            $issue->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            // Unfreeze escrow settlement if no other open issues/disputes remain
            if ($invoice->settlement) {
                $this->escrowService->unfreezeAfterDispute($invoice->settlement);
            }

            return $result;
        });
    }

    /**
     * Seller disagrees / contests reported issue -> Escalate to Dispute.
     */
    public function sellerContestsIssue(
        Issue $issue,
        User $seller,
        string $reason,
        ?string $evidence = null
    ): Dispute {
        $invoice = $issue->invoice;

        if ($invoice->seller_id !== $seller->id && ! $seller->isAdmin()) {
            throw ValidationException::withMessages([
                'seller' => 'Only the seller or an administrator can contest this issue.',
            ]);
        }

        return DB::transaction(function () use ($issue, $invoice, $seller, $reason, $evidence) {
            $issue->update(['status' => 'escalated']);

            return $this->openDispute(
                invoice: $invoice,
                opener: $seller,
                respondent: $issue->reporter,
                type: 'rejection_contested',
                reason: $reason,
                issueId: $issue->id,
                evidence: $evidence
            );
        });
    }

    /**
     * Buyer inspects replacement unit and rejects it (defective, wrong part, damaged).
     */
    public function buyerRejectsReplacement(
        Replacement $replacement,
        User $buyer,
        string $reason,
        ?string $evidence = null
    ): Dispute {
        $invoice = $replacement->invoice;

        if ($replacement->buyer_id !== $buyer->id && ! $buyer->isAdmin()) {
            throw ValidationException::withMessages([
                'buyer' => 'Only the buyer or an administrator can reject this replacement.',
            ]);
        }

        return DB::transaction(function () use ($replacement, $invoice, $buyer, $reason, $evidence) {
            $replacement->update([
                'status' => 'disputed',
                'rejection_reason' => $reason,
                'rejection_evidence' => $evidence,
                'rejected_at' => now(),
                'disputed_at' => now(),
            ]);

            return $this->openDispute(
                invoice: $invoice,
                opener: $buyer,
                respondent: $replacement->seller,
                type: 'replacement_defective',
                reason: $reason,
                issueId: $replacement->issue_id,
                replacementId: $replacement->id,
                warrantyClaimId: $replacement->warranty_claim_id,
                evidence: $evidence
            );
        });
    }

    /**
     * Seller inspects returned package and rejects it (counterfeit swap, empty box, buyer damage).
     */
    public function sellerRejectsReturn(
        ReturnRecord $return,
        User $seller,
        string $reason,
        ?string $evidence = null
    ): Dispute {
        $invoice = $return->invoice;

        if ($return->seller_id !== $seller->id && ! $seller->isAdmin()) {
            throw ValidationException::withMessages([
                'seller' => 'Only the seller or an administrator can reject this return package.',
            ]);
        }

        return DB::transaction(function () use ($return, $invoice, $seller, $reason, $evidence) {
            $return->update([
                'status' => 'disputed',
                'rejection_reason' => $reason,
                'rejection_evidence' => $evidence,
                'disputed_at' => now(),
            ]);

            return $this->openDispute(
                invoice: $invoice,
                opener: $seller,
                respondent: $return->buyer,
                type: 'return_fraud_abuse',
                reason: $reason,
                issueId: $return->issue_id,
                warrantyClaimId: $return->warranty_claim_id,
                returnId: $return->id,
                evidence: $evidence
            );
        });
    }

    /**
     * Buyer files a warranty claim during active warranty coverage window.
     */
    public function fileWarrantyClaim(
        Invoice $invoice,
        User $buyer,
        ?int $invoiceItemId,
        string $description,
        ?string $evidence = null,
        string $claimType = 'defect'
    ): WarrantyClaim {
        if ($invoice->buyer_id !== $buyer->id && ! $buyer->isAdmin()) {
            throw ValidationException::withMessages([
                'buyer' => 'Only the buyer or an administrator can file a warranty claim on this invoice.',
            ]);
        }

        $validClaimTypes = ['defect', 'hardware_failure', 'malfunction', 'wear_tear'];
        $cleanClaimType = in_array($claimType, $validClaimTypes, true) ? $claimType : 'defect';

        return DB::transaction(function () use ($invoice, $buyer, $invoiceItemId, $description, $evidence, $cleanClaimType) {
            $claim = WarrantyClaim::create([
                'invoice_id' => $invoice->id,
                'invoice_item_id' => $invoiceItemId,
                'buyer_id' => $buyer->id,
                'seller_id' => $invoice->seller_id,
                'status' => 'pending',
                'claim_type' => $cleanClaimType,
                'description' => $description,
                'evidence' => $evidence,
            ]);

            return $claim;
        });
    }

    /**
     * Seller accepts warranty claim and approves replacement/repair terms.
     */
    public function sellerAcceptsWarranty(
        WarrantyClaim $claim,
        User $seller,
        string $remedy = 'replacement',
        ?string $notes = null
    ): mixed {
        $invoice = $claim->invoice;

        if ($claim->seller_id !== $seller->id && ! $seller->isAdmin()) {
            throw ValidationException::withMessages([
                'seller' => 'Only the seller or an administrator can accept this warranty claim.',
            ]);
        }

        return DB::transaction(function () use ($claim, $invoice, $remedy, $notes) {
            $claim->update([
                'status' => 'accepted',
                'seller_notes' => $notes,
                'responded_at' => now(),
            ]);

            if ($remedy === 'replacement') {
                $replacement = Replacement::create([
                    'invoice_id' => $invoice->id,
                    'warranty_claim_id' => $claim->id,
                    'buyer_id' => $claim->buyer_id,
                    'seller_id' => $claim->seller_id,
                    'status' => 'pending',
                    'delivery_method' => $invoice->delivery_method ?? 'shipment',
                    'notes' => $notes ?: "Warranty replacement unit for claim #{$claim->id}",
                ]);

                if ($claim->item) {
                    ReplacementItem::create([
                        'replacement_id' => $replacement->id,
                        'invoice_item_id' => $claim->invoice_item_id,
                        'description' => $claim->item->description,
                        'quantity' => 1,
                    ]);
                }

                return $replacement;
            }

            return $claim;
        });
    }

    /**
     * Seller rejects/refuses warranty claim -> Escalate to Dispute.
     */
    public function sellerRejectsWarranty(
        WarrantyClaim $claim,
        User $seller,
        string $reason,
        ?string $evidence = null
    ): Dispute {
        $invoice = $claim->invoice;

        if ($claim->seller_id !== $seller->id && ! $seller->isAdmin()) {
            throw ValidationException::withMessages([
                'seller' => 'Only the seller or an administrator can respond to this warranty claim.',
            ]);
        }

        return DB::transaction(function () use ($claim, $invoice, $seller, $reason, $evidence) {
            $claim->update([
                'status' => 'disputed',
                'seller_notes' => $reason,
                'responded_at' => now(),
                'disputed_at' => now(),
            ]);

            return $this->openDispute(
                invoice: $invoice,
                opener: $claim->buyer,
                respondent: $seller,
                type: 'warranty_denial',
                reason: "Seller refused warranty claim: {$reason}. Fault description: {$claim->description}",
                warrantyClaimId: $claim->id,
                evidence: $evidence ?: $claim->evidence
            );
        });
    }

    /**
     * Generalized Dispute creation for platform mediation.
     */
    public function openDispute(
        Invoice $invoice,
        User $opener,
        User $respondent,
        string $type,
        string $reason,
        ?int $issueId = null,
        ?int $replacementId = null,
        ?int $warrantyClaimId = null,
        ?int $returnId = null,
        ?string $evidence = null,
        array $disputeItems = []
    ): Dispute {
        return DB::transaction(function () use (
            $invoice, $opener, $respondent, $type, $reason,
            $issueId, $replacementId, $warrantyClaimId, $returnId,
            $evidence, $disputeItems
        ) {
            $dispute = Dispute::create([
                'invoice_id' => $invoice->id,
                'issue_id' => $issueId,
                'replacement_id' => $replacementId,
                'warranty_claim_id' => $warrantyClaimId,
                'return_id' => $returnId,
                'opened_by' => $opener->id,
                'respondent_id' => $respondent->id,
                'type' => $type,
                'status' => 'open',
                'reason' => $reason,
                'evidence' => $evidence,
            ]);

            foreach ($disputeItems as $di) {
                DisputeItem::create([
                    'dispute_id' => $dispute->id,
                    'itemable_type' => $di['itemable_type'] ?? Invoice::class,
                    'itemable_id' => $di['itemable_id'] ?? $invoice->id,
                    'claim' => $di['claim'] ?? $reason,
                    'evidence' => is_array($di['evidence'] ?? null) ? json_encode($di['evidence']) : ($di['evidence'] ?? null),
                ]);
            }

            // Ensure settlement is frozen
            if ($invoice->settlement) {
                $this->escrowService->freezeForDispute($invoice->settlement);
            }

            return $dispute;
        });
    }

    /**
     * Platform administrator mediates and resolves the dispute.
     */
    public function adminResolveDispute(
        Dispute $dispute,
        User $admin,
        string $decision, // 'buyer_favor' | 'seller_favor' | 'split'
        ?float $refundAmount = null,
        ?string $resolutionNotes = null
    ): Dispute {
        return DB::transaction(function () use ($dispute, $admin, $decision, $refundAmount, $resolutionNotes) {
            $invoice = $dispute->invoice ?? $dispute->issue?->invoice;

            if ($decision === 'buyer_favor' && $invoice) {
                $calculatedAmount = $refundAmount ?? (float) $invoice->total;

                $refund = Refund::create([
                    'invoice_id' => $invoice->id,
                    'payment_id' => $invoice->latestSuccessfulPayment?->id,
                    'buyer_id' => $invoice->buyer_id,
                    'seller_id' => $invoice->seller_id,
                    'amount' => $calculatedAmount,
                    'status' => 'pending',
                    'reason' => "Dispute resolved in buyer favor: {$resolutionNotes}",
                ]);

                RefundPaymentJob::dispatch($refund->id);
            }

            $dispute->update([
                'status' => 'resolved',
                'resolved_by' => $admin->id,
                'resolved_at' => now(),
                'resolution' => "Decision: {$decision}. Notes: {$resolutionNotes}",
            ]);

            if ($dispute->issue) {
                $dispute->issue->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            }

            if ($dispute->warrantyClaim) {
                $dispute->warrantyClaim->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            }

            if ($dispute->returnRecord) {
                $dispute->returnRecord->update([
                    'status' => ($decision === 'buyer_favor' ? 'accepted' : 'received'),
                    'accepted_at' => now(),
                ]);
            }

            if ($dispute->replacement) {
                $dispute->replacement->update([
                    'status' => ($decision === 'buyer_favor' ? 'disputed' : 'accepted'),
                    'accepted_at' => ($decision === 'seller_favor' ? now() : null),
                ]);
            }

            // Unfreeze escrow settlement
            if ($invoice && $invoice->settlement) {
                $this->escrowService->unfreezeAfterDispute($invoice->settlement);
            }

            return $dispute;
        });
    }

    /**
     * Helper to compute refund and dispatch RefundPaymentJob.
     */
    protected function createAndDispatchRefund(Issue $issue): Refund
    {
        $invoice = $issue->invoice;
        $totalRefund = 0.0;

        $refundItemsData = [];
        foreach ($issue->items as $issueItem) {
            if ($issueItem->invoiceItem) {
                $amount = $this->calculateItemRefund($issueItem->invoiceItem);
                $totalRefund += $amount;
                $refundItemsData[] = [
                    'invoice_item_id' => $issueItem->invoice_item_id,
                    'quantity' => 1,
                    'amount' => $amount,
                ];
            }
        }

        if ($totalRefund <= 0) {
            $totalRefund = (float) $invoice->total;
        }

        $refund = Refund::create([
            'invoice_id' => $invoice->id,
            'payment_id' => $invoice->latestSuccessfulPayment?->id,
            'buyer_id' => $invoice->buyer_id,
            'seller_id' => $invoice->seller_id,
            'amount' => $totalRefund,
            'status' => 'pending',
            'reason' => "Issue accepted by seller: {$issue->description}",
        ]);

        foreach ($refundItemsData as $itemData) {
            RefundItem::create(array_merge($itemData, ['refund_id' => $refund->id]));
        }

        RefundPaymentJob::dispatch($refund->id);

        return $refund;
    }

    /**
     * Helper to create replacement record.
     */
    protected function createReplacementRecord(Issue $issue): Replacement
    {
        $invoice = $issue->invoice;

        $replacement = Replacement::create([
            'invoice_id' => $invoice->id,
            'issue_id' => $issue->id,
            'buyer_id' => $invoice->buyer_id,
            'seller_id' => $invoice->seller_id,
            'status' => 'pending',
            'delivery_method' => $invoice->delivery_method ?? 'shipment',
            'notes' => "Replacement approved for issue #{$issue->id}",
        ]);

        foreach ($issue->items as $issueItem) {
            ReplacementItem::create([
                'replacement_id' => $replacement->id,
                'invoice_item_id' => $issueItem->invoice_item_id,
                'description' => $issueItem->invoiceItem?->description ?? 'Replacement Item',
                'quantity' => 1,
            ]);
        }

        return $replacement;
    }

    /**
     * Helper to create return record.
     */
    protected function createReturnRecord(Issue $issue): ReturnRecord
    {
        $invoice = $issue->invoice;

        $return = ReturnRecord::create([
            'invoice_id' => $invoice->id,
            'issue_id' => $issue->id,
            'buyer_id' => $invoice->buyer_id,
            'seller_id' => $invoice->seller_id,
            'status' => 'pending',
            'delivery_method' => $invoice->delivery_method ?? 'shipment',
            'notes' => "Return approved for issue #{$issue->id}",
        ]);

        foreach ($issue->items as $issueItem) {
            ReturnItem::create([
                'return_id' => $return->id,
                'invoice_item_id' => $issueItem->invoice_item_id,
                'quantity' => 1,
                'condition_status' => $issue->type,
                'notes' => $issueItem->reason,
            ]);
        }

        return $return;
    }
}
