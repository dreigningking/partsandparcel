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

        return DB::transaction(function () use ($invoice, $reporter, $type, $description, $items) {
            $issue = Issue::create([
                'invoice_id' => $invoice->id,
                'reported_by' => $reporter->id,
                'type' => $type,
                'status' => 'open',
                'description' => $description,
            ]);

            foreach ($items as $itemData) {
                $invoiceItemId = $itemData['invoice_item_id'] ?? null;
                $reason = $itemData['reason'] ?? $type;
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
     * Vendors do NOT calculate or alter refund amounts.
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
     * Escalate issue to a Dispute for admin mediation if parties cannot agree.
     */
    public function openDispute(Issue $issue, User $opener, string $reason, array $evidence = []): Dispute
    {
        return DB::transaction(function () use ($issue, $opener, $reason, $evidence) {
            $dispute = Dispute::create([
                'issue_id' => $issue->id,
                'opened_by' => $opener->id,
                'status' => 'open',
                'reason' => $reason,
            ]);

            foreach ($evidence as $ev) {
                DisputeItem::create([
                    'dispute_id' => $dispute->id,
                    'itemable_type' => Issue::class,
                    'itemable_id' => $issue->id,
                    'claim' => $ev['claim'] ?? $reason,
                    'evidence' => is_array($ev['evidence'] ?? null) ? json_encode($ev['evidence']) : ($ev['evidence'] ?? null),
                ]);
            }

            // Ensure settlement is frozen
            if ($issue->invoice && $issue->invoice->settlement) {
                $this->escrowService->freezeForDispute($issue->invoice->settlement);
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
            $invoice = $dispute->issue?->invoice;

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

        // If no individual invoice items matched, use full invoice amount as fallback
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
