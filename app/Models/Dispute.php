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
        'reason',
        'evidence',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
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
}
