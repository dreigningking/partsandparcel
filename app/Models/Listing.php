<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        'item_id',
        'slug',
        'location_id',
        'quantity',
        'reserved_quantity',
        'sold_quantity',
        'price',
        'is_negotiable',
        'status',
        'warranty_period_days',
        'is_warranty_negotiable',
        'warranty_terms',
        'allow_shipping',
        'description',
    ];

    public function setAssetableIdAttribute($value): void
    {
        $this->attributes['item_id'] = $value;
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'sold_quantity' => 'integer',
            'price' => 'decimal:2',
            'is_negotiable' => 'boolean',
            'warranty_period_days' => 'integer',
            'is_warranty_negotiable' => 'boolean',
            'allow_shipping' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            if (empty($listing->slug)) {
                $itemName = $listing->item?->name;
                if (!$itemName && $listing->item_id) {
                    $itemName = Item::where('id', $listing->item_id)->value('name');
                }
                $listing->slug = static::generateUniqueSlug($itemName ?: 'listing');
            }
        });
    }

    public static function generateUniqueSlug(?string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name ?: 'listing');
        if (empty($base)) {
            $base = 'listing';
        }

        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function assetable(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getAssetableAttribute(): ?Item
    {
        return $this->item;
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

    public function reviews(): HasMany
    {
        return $this->hasMany(ListingReview::class);
    }

    public function averageRating(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 5.0;
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public function starDistribution(): array
    {
        $total = $this->reviewsCount();
        $distribution = [
            5 => ['count' => 0, 'percentage' => 0],
            4 => ['count' => 0, 'percentage' => 0],
            3 => ['count' => 0, 'percentage' => 0],
            2 => ['count' => 0, 'percentage' => 0],
            1 => ['count' => 0, 'percentage' => 0],
        ];

        if ($total > 0) {
            $grouped = $this->reviews()
                ->selectRaw('rating, count(*) as cnt')
                ->groupBy('rating')
                ->pluck('cnt', 'rating');

            foreach ($distribution as $star => $data) {
                $count = (int) $grouped->get($star, 0);
                $distribution[$star] = [
                    'count' => $count,
                    'percentage' => round(($count / $total) * 100),
                ];
            }
        }

        return $distribution;
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
