<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type', // 'percentage' | 'fixed'
        'value',
        'min_order_amount',
        'max_discount',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null && $this->usage_limit > 0;
    }

    public function isLimitReached(): bool
    {
        return $this->hasUsageLimit() && $this->used_count >= $this->usage_limit;
    }

    public function usageRemaining(): ?int
    {
        return $this->hasUsageLimit() ? max(0, $this->usage_limit - $this->used_count) : null;
    }

    public function isValid(): bool
    {
        return $this->is_active && ! $this->isExpired() && ! $this->isLimitReached();
    }

    public function formattedValue(): string
    {
        if ($this->type === 'percentage') {
            return rtrim(rtrim((string) $this->value, '0'), '.') . '%';
        }

        return '₦' . number_format($this->value, 2);
    }

    /**
     * Validate whether this coupon is eligible for an amount.
     */
    public function validateEligibility(float $amount = 0.0): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is currently inactive.'];
        }

        if ($this->isExpired()) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->isLimitReached()) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum usage limit.'];
        }

        if ($amount > 0 && $this->min_order_amount > 0 && $amount < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum order amount for this coupon is ₦' . number_format($this->min_order_amount, 2) . '.',
            ];
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    /**
     * Calculate discount amount for a given order total.
     */
    public function calculateDiscount(float $amount): float
    {
        $check = $this->validateEligibility($amount);
        if (! $check['valid']) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = round($amount * ($this->value / 100), 2);
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return min($discount, $amount);
        }

        // Fixed amount discount
        return min((float) $this->value, $amount);
    }

    /**
     * Record usage of the coupon.
     */
    public function recordUsage(): void
    {
        $this->increment('used_count');
    }
}
