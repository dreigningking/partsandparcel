<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'paymentable_id',
        'paymentable_type',
        'reference',
        'provider',
        'status',
        'amount',
        'escrow_fee',
        'currency',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'escrow_fee' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function settlement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Settlement::class);
    }

    public function isHeldInEscrow(): bool
    {
        return $this->status === 'held_in_escrow';
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, ['successful', 'success', 'paid']);
    }

    public function isCompleted(): bool
    {
        return $this->isSuccessful();
    }

    public function getEscrowFeesAttribute(): float
    {
        return (float) ($this->attributes['escrow_fee'] ?? 0);
    }

    public function getInvoiceAttribute(): ?Invoice
    {
        if ($this->paymentable_type === Invoice::class || $this->paymentable instanceof Invoice) {
            return $this->paymentable;
        }
        return null;
    }

    public function getInvoiceIdAttribute(): ?int
    {
        return ($this->paymentable_type === Invoice::class) ? (int) $this->paymentable_id : null;
    }

    public function getSubscriptionAttribute(): ?Subscription
    {
        if ($this->paymentable_type === Subscription::class || $this->paymentable instanceof Subscription) {
            return $this->paymentable;
        }
        return null;
    }

    public function getSubscriptionIdAttribute(): ?int
    {
        return ($this->paymentable_type === Subscription::class) ? (int) $this->paymentable_id : null;
    }

    public function getPromotionAttribute(): ?Promotion
    {
        if ($this->paymentable_type === Promotion::class || $this->paymentable instanceof Promotion) {
            return $this->paymentable;
        }
        return null;
    }

    public function getPromotionIdAttribute(): ?int
    {
        return ($this->paymentable_type === Promotion::class) ? (int) $this->paymentable_id : null;
    }

    public function getReceiptUrlAttribute(): ?string
    {
        $receipt = $this->metadata['receipt'] ?? $this->metadata['receipt_path'] ?? $this->metadata['proof_of_payment'] ?? null;
        if (! $receipt) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($receipt, ['http://', 'https://'])) {
            return $receipt;
        }
        return asset('storage/' . $receipt);
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->paymentable_type === Invoice::class || $this->paymentable instanceof Invoice) {
            return 'Marketplace Escrow';
        }
        if ($this->paymentable_type === Subscription::class || $this->paymentable instanceof Subscription) {
            return 'Subscription Upgrade';
        }
        if ($this->paymentable_type === Promotion::class || $this->paymentable instanceof Promotion) {
            return 'Listing Promotion';
        }
        if (! empty($this->metadata['payment_type'])) {
            return ucwords(str_replace('_', ' ', $this->metadata['payment_type']));
        }
        return 'Standard Transaction';
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
}
