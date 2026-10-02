<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_plan_id',
        'country_id',
        'price_monthly',
        'price_annual',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_annual' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getCurrencyAttribute(): string
    {
        return $this->country?->currency ?? 'NGN';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return $this->country?->currency_symbol ?? '₦';
    }
}
