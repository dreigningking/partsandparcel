<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'invoice_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'eligible_at',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'eligible_at' => 'datetime',
            'settled_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function payoutSettlements(): HasMany
    {
        return $this->hasMany(PayoutSettlement::class);
    }

    public function getNetAmountAttribute(): float
    {
        return (float) ($this->attributes['amount'] ?? 0);
    }

    public function setNetAmountAttribute($value): void
    {
        $this->attributes['amount'] = $value;
    }

    public function getGrossAmountAttribute(): float
    {
        return (float) ($this->attributes['amount'] ?? 0);
    }

    public function setGrossAmountAttribute($value): void
    {
        if (! isset($this->attributes['amount']) || empty($this->attributes['amount'])) {
            $this->attributes['amount'] = $value;
        }
    }

    public function setCommissionAttribute($value): void
    {
        // Legacy compatibility
    }

    public function setRefundsAttribute($value): void
    {
        // Legacy compatibility
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper($this->currency ?? 'NGN')) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'GHS' => 'GH₵',
            'KES' => 'KSh',
            'ZAR' => 'R',
            default => $this->currency ?? '₦',
        };
    }

    public function isEligible(): bool
    {
        return $this->status === 'eligible' || ($this->eligible_at && $this->eligible_at->isPast() && $this->status === 'pending');
    }

    public function isSettled(): bool
    {
        return $this->status === 'settled';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
