<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Listing extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        'assetable_type',
        'assetable_id',
        'location_id',
        'quantity',
        'reserved_quantity',
        'sold_quantity',
        'price',
        'status',
        'warranty_period_days',
        'warranty_terms',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'sold_quantity' => 'integer',
            'price' => 'decimal:2',
            'warranty_period_days' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity - $this->sold_quantity);
    }

    public function scopeInCurrentCountry($query, ?string $countryCode = null)
    {
        $code = strtoupper($countryCode ?? session('current_location.country_code', 'NG'));

        return $query->whereHas('seller', fn ($q) => $q->where('country_code', $code));
    }

    public function mediaDimensionRequirements(): array
    {
        return [
            'default' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
            'images' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
        ];
    }
}
