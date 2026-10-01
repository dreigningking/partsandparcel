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
        'features',
        'is_active',
        'is_default'
    ];

    protected function casts(): array
    {
        return [
            'response_limit' => 'integer',
            'request_limit' => 'integer',
            'listing_limit' => 'integer',
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
    public function scopeLocalPrice(Builder $query, int $country_id)
    {

        if (!$country_id) {
            $country_id = Country::firstWhere('is_default',true)->id;
        }
        return $query->whereHas('prices', function ($query) use ($country_id) {
            $query->where('country_id', $country_id);
        })->first();

    }

    /**
     * Effective monthly price for currency.
     */
    public function getMonthlyPrice(string $currency = 'NGN', string $countryCode = 'NG'): float
    {
        $priceRecord = $this->getPriceFor($currency, $countryCode);
        return $priceRecord ? (float) $priceRecord->price_monthly : (float) $this->price;
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

        if ((float) $this->price_annual > 0) {
            return (float) $this->price_annual;
        }

        return (float) ($this->features['price_annual'] ?? ($this->price * 10));
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
