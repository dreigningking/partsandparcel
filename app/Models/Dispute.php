<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'issue_id',
        'return_id',
        'replacement_id',
        'warranty_claim_id',
        'refund_id',
        'opened_by',
        'respondent_id',
        'type',
        'status',
        'decision',
        'refund_amount',
        'require_return',
        'reason',
        'evidence',
        'resolution',
        'resolution_notes',
        'internal_notes',
        'respondent_defense',
        'respondent_defended_at',
        'return_shipment_id',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'require_return' => 'boolean',
            'respondent_defended_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function returnRecord(): BelongsTo
    {
        return $this->belongsTo(ReturnRecord::class, 'return_id');
    }

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(Replacement::class);
    }

    public function warrantyClaim(): BelongsTo
    {
        return $this->belongsTo(WarrantyClaim::class);
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DisputeItem::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(DisputeEvidence::class);
    }

    public function returnShipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'return_shipment_id');
    }

    /**
     * Determine dispute origin context from whichever entity ID is bound.
     */
    public function originCategory(): string
    {
        if ($this->warranty_claim_id) {
            return 'Warranty Claim';
        }
        if ($this->replacement_id) {
            return 'Replacement Unit';
        }
        if ($this->return_id) {
            return 'Return Condition';
        }
        if ($this->issue_id) {
            return 'Transaction Issue';
        }
        return 'Invoice Transaction';
    }

    /**
     * Human-readable label for dispute classification type.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'rejection_contested' => 'Seller Contests Issue',
            'replacement_defective' => 'Defective Replacement',
            'return_fraud_abuse' => 'Return Fraud or Abuse',
            'warranty_denial' => 'Warranty Claim Denied',
            'mutual_deadlock' => 'Negotiation Deadlock',
            default => ucwords(str_replace('_', ' ', (string) $this->type)),
        };
    }

    /**
     * Synthesize full chronological invoice & post-sale lifecycle timeline
     * from all associated domain tables.
     */
    public function generateLifecycleTimeline(): array
    {
        $timeline = [];
        $invoice = $this->invoice;

        // 1. Invoice Issued
        if ($invoice) {
            $issuedTime = $invoice->issued_at ?: $invoice->created_at;
            if ($issuedTime) {
                $timeline[] = [
                    'title' => "Invoice Issued (#INV-{$invoice->invoice_number})",
                    'description' => "Order generated for " . $invoice->currencySymbol() . number_format((float) $invoice->total, 2) . " with " . ($invoice->delivery_method ? str_replace('_', ' ', $invoice->delivery_method) : 'platform') . " fulfillment.",
                    'time' => $issuedTime->format('M d, Y · H:i'),
                    'timestamp' => $issuedTime->timestamp,
                    'icon' => 'fa-file-invoice',
                    'color' => 'slate',
                ];
            }

            // 2. Escrow Payment Confirmed
            $paidTime = $invoice->paid_at ?: $invoice->latestSuccessfulPayment?->paid_at;
            if ($paidTime) {
                $settlementRef = $invoice->settlement?->id ? "#STL-{$invoice->settlement->id}" : "Escrow Vault";
                $timeline[] = [
                    'title' => 'Escrow Payment Confirmed',
                    'description' => "Buyer deposited " . $invoice->currencySymbol() . number_format((float) $invoice->total, 2) . " into platform escrow vault ({$settlementRef}).",
                    'time' => $paidTime->format('M d, Y · H:i'),
                    'timestamp' => $paidTime->timestamp,
                    'icon' => 'fa-shield-halved',
                    'color' => 'emerald',
                ];
            }

            // 3. Outbound Shipment Dispatched
            if ($invoice->shipped_at) {
                $timeline[] = [
                    'title' => 'Outbound Courier Shipment Dispatched',
                    'description' => 'Seller handed package over to platform logistics courier.',
                    'time' => $invoice->shipped_at->format('M d, Y · H:i'),
                    'timestamp' => $invoice->shipped_at->timestamp,
                    'icon' => 'fa-truck',
                    'color' => 'blue',
                ];
            }

            // 4. Delivered
            if ($invoice->delivered_at) {
                $timeline[] = [
                    'title' => 'Package Delivered to Destination',
                    'description' => 'Shipment arrived at destination address for customer physical inspection.',
                    'time' => $invoice->delivered_at->format('M d, Y · H:i'),
                    'timestamp' => $invoice->delivered_at->timestamp,
                    'icon' => 'fa-box-open',
                    'color' => 'indigo',
                ];
            }
        }

        // 5. Issue Reported / Delivery Rejected
        if ($this->issue && $this->issue->created_at) {
            $timeline[] = [
                'title' => 'Delivery Rejected / Issue Reported (#ISS-' . str_pad((string) $this->issue->id, 4, '0', STR_PAD_LEFT) . ')',
                'description' => 'Buyer reported ' . ucwords(str_replace('_', ' ', $this->issue->type)) . ': "' . ($this->issue->description ?: 'Issue flagged on item') . '".',
                'time' => $this->issue->created_at->format('M d, Y · H:i'),
                'timestamp' => $this->issue->created_at->timestamp,
                'icon' => 'fa-circle-xmark',
                'color' => 'rose',
            ];
        }

        // 6. Return Initiated
        if ($this->returnRecord && $this->returnRecord->created_at) {
            $timeline[] = [
                'title' => 'Return Record Created (#RET-' . str_pad((string) $this->returnRecord->id, 4, '0', STR_PAD_LEFT) . ')',
                'description' => 'Return condition: ' . ucfirst($this->returnRecord->status) . ($this->returnRecord->rejection_reason ? " (Contested: {$this->returnRecord->rejection_reason})" : ''),
                'time' => $this->returnRecord->created_at->format('M d, Y · H:i'),
                'timestamp' => $this->returnRecord->created_at->timestamp,
                'icon' => 'fa-rotate-left',
                'color' => 'amber',
            ];
        }

        // 7. Replacement Initiated
        if ($this->replacement && $this->replacement->created_at) {
            $timeline[] = [
                'title' => 'Replacement Process Created (#REP-' . str_pad((string) $this->replacement->id, 4, '0', STR_PAD_LEFT) . ')',
                'description' => 'Replacement unit status: ' . ucfirst($this->replacement->status),
                'time' => $this->replacement->created_at->format('M d, Y · H:i'),
                'timestamp' => $this->replacement->created_at->timestamp,
                'icon' => 'fa-repeat',
                'color' => 'blue',
            ];
        }

        // 8. Warranty Claim
        if ($this->warrantyClaim && $this->warrantyClaim->created_at) {
            $timeline[] = [
                'title' => 'Warranty Claim Filed (#CLM-' . str_pad((string) $this->warrantyClaim->id, 4, '0', STR_PAD_LEFT) . ')',
                'description' => 'Claim type: ' . ucfirst($this->warrantyClaim->claim_type) . '. ' . $this->warrantyClaim->description,
                'time' => $this->warrantyClaim->created_at->format('M d, Y · H:i'),
                'timestamp' => $this->warrantyClaim->created_at->timestamp,
                'icon' => 'fa-shield',
                'color' => 'purple',
            ];
        }

        // 9. Dispute Contested & Opened
        if ($this->created_at) {
            $openerName = $this->opener?->name ?: 'Complainant';
            $timeline[] = [
                'title' => 'Dispute Escalated & Opened (#DSP-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT) . ')',
                'description' => "{$openerName} escalated case. Escrow funds locked for platform arbitration. Statement: \"{$this->reason}\"",
                'time' => $this->created_at->format('M d, Y · H:i'),
                'timestamp' => $this->created_at->timestamp,
                'icon' => 'fa-scale-balanced',
                'color' => 'purple',
            ];
        }

        // 10. Respondent Defense Submitted
        if ($this->respondent_defended_at && $this->respondent_defense) {
            $respondentName = $this->respondent?->name ?: 'Respondent';
            $timeline[] = [
                'title' => "Formal Defense Submitted by {$respondentName}",
                'description' => "Rebuttal: \"{$this->respondent_defense}\"",
                'time' => $this->respondent_defended_at->format('M d, Y · H:i'),
                'timestamp' => $this->respondent_defended_at->timestamp,
                'icon' => 'fa-shield',
                'color' => 'slate',
            ];
        }

        // 11. Evidence Requests & Submissions
        if ($this->relationLoaded('evidences') || $this->evidences()->exists()) {
            foreach ($this->evidences as $evidence) {
                // Request event
                if ($evidence->created_at) {
                    $timeline[] = [
                        'title' => "Admin Opened Evidence Requisition #REQ-{$evidence->id}",
                        'description' => "Requested from {$evidence->targetLabel()}: \"{$evidence->title}\"",
                        'time' => $evidence->created_at->format('M d, Y · H:i'),
                        'timestamp' => $evidence->created_at->timestamp,
                        'icon' => 'fa-camera',
                        'color' => 'blue',
                    ];
                }

                // Submission event
                if ($evidence->submitted_at) {
                    $timeline[] = [
                        'title' => "Evidence Submitted for #REQ-{$evidence->id}",
                        'description' => $evidence->party_notes ?: "Party submitted requested exhibits for arbitrator verification.",
                        'time' => $evidence->submitted_at->format('M d, Y · H:i'),
                        'timestamp' => $evidence->submitted_at->timestamp,
                        'icon' => 'fa-check',
                        'color' => 'emerald',
                    ];
                }
            }
        }

        // 12. Final Binding Ruling
        if ($this->resolved_at) {
            $verdictTitle = match ($this->decision) {
                'buyer_favor' => 'Arbitration Verdict: Ruled in Buyer Favor',
                'seller_favor' => 'Arbitration Verdict: Ruled in Seller Favor',
                'split' => 'Arbitration Verdict: Split Settlement Ruling',
                default => 'Arbitration Verdict Executed & Closed',
            };

            $timeline[] = [
                'title' => $verdictTitle,
                'description' => $this->resolution_notes ?: ($this->resolution ?: 'Binding decision declared by platform arbitration panel.'),
                'time' => $this->resolved_at->format('M d, Y · H:i'),
                'timestamp' => $this->resolved_at->timestamp,
                'icon' => 'fa-gavel',
                'color' => 'emerald',
            ];
        }

        // Sort timeline chronologically
        usort($timeline, fn ($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));

        return $timeline;
    }
}
