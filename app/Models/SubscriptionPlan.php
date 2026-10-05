<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'response_limit',
        'request_limit',
        'listing_limit',
        'escrow_percentage',
        'escrow_cap',
        'features',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'response_limit' => 'integer',
            'request_limit' => 'integer',
            'listing_limit' => 'integer',
            'escrow_percentage' => 'decimal:2',
            'escrow_cap' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SubscriptionPlanPrice::class);
    }

    /**
     * Get price definition for a specific currency and country.
     */
    public function scopeLocalPrice(Builder $query, ?int $country_id = null)
    {
        if (! $country_id) {
            $country_id = Country::firstWhere('is_default', true)?->id;
        }

        return $query->whereHas('prices', function ($q) use ($country_id) {
            $q->where('country_id', $country_id);
        })->first();
    }

    /**
     * Get price record for country or currency.
     */
    public function getPriceFor(?string $currency = 'NGN', ?string $countryCode = 'NG'): ?SubscriptionPlanPrice
    {
        if ($this->relationLoaded('prices')) {
            $matched = $this->prices->first(function ($p) use ($currency, $countryCode) {
                return ($countryCode && $p->country?->code === $countryCode)
                    || ($currency && $p->country?->currency === $currency);
            });
            if ($matched) {
                return $matched;
            }

            return $this->prices->first();
        }

        return $this->prices()
            ->whereHas('country', function ($q) use ($currency, $countryCode) {
                $q->when($countryCode, fn ($cq) => $cq->where('code', $countryCode))
                    ->when($currency, fn ($cq) => $cq->orWhere('currency', $currency));
            })
            ->first() ?? $this->prices()->first();
    }

    /**
     * Effective monthly price for currency.
     */
    public function getMonthlyPrice(string $currency = 'NGN', string $countryCode = 'NG'): float
    {
        $priceRecord = $this->getPriceFor($currency, $countryCode);

        return $priceRecord ? (float) $priceRecord->price_monthly : (float) ($this->price ?? 0.00);
    }

    public function getMonthlyPriceAttribute(): float
    {
        return $this->getMonthlyPrice();
    }

    public function getAnnualPrice(string $currency = 'NGN', string $countryCode = 'NG'): float
    {
        $priceRecord = $this->getPriceFor($currency, $countryCode);
        if ($priceRecord && (float) $priceRecord->price_annual > 0) {
            return (float) $priceRecord->price_annual;
        }

        if (isset($this->price_annual) && (float) $this->price_annual > 0) {
            return (float) $this->price_annual;
        }

        return (float) ($this->features['price_annual'] ?? ($this->getMonthlyPrice($currency, $countryCode) * 10));
    }

    public function getAnnualPriceAttribute(): float
    {
        return $this->getAnnualPrice();
    }

    public function getDailyRequestLimitAttribute(): int
    {
        return (int) ($this->request_limit ?: ($this->features['daily_request_limit'] ?? 1));
    }

    public function getDailyResponseLimitAttribute(): int
    {
        return (int) ($this->response_limit ?: ($this->features['daily_response_limit'] ?? 1));
    }

    public function getTotalListingLimitAttribute(): int
    {
        return (int) ($this->listing_limit ?: ($this->features['listing_limit'] ?? 10));
    }
}
