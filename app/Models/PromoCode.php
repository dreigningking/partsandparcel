<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
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

    /**
     * Validate whether this promo code is eligible for an amount.
     */
    public function validateEligibility(float $amount = 0.0): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'This promo code is currently inactive.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This promo code has expired.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This promo code has reached its maximum usage limit.'];
        }

        if ($amount > 0 && $this->min_order_amount > 0 && $amount < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum order amount for this promo code is ₦' . number_format($this->min_order_amount, 2) . '.',
            ];
        }

        return ['valid' => true, 'message' => 'Promo code applied successfully!'];
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
     * Record usage of the promo code.
     */
    public function recordUsage(): void
    {
        $this->increment('used_count');
    }
}
