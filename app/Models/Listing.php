<?php

namespace App\Models;

use App\Models\Report;
use App\Observers\ListingObserver;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

#[ObservedBy([ListingObserver::class])]
class Listing extends Model
{
    use HasFactory, HasMedia, Sluggable;

    protected $fillable = [
        'user_id',
        'item_id',
        'slug',
        'quantity',
        'reserved_quantity',
        'sold_quantity',
        'price',
        'is_negotiable',
        'is_published',
        'is_active',
        'warranty_period_days',
        'is_warranty_negotiable',
        'warranty_terms',
        'allow_shipping'
    ];

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

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_source',
            ],
        ];
    }

    public function getSlugSourceAttribute(): string
    {
        $itemName = $this->item?->name;
        if (! $itemName && $this->item_id) {
            $itemName = Item::where('id', $this->item_id)->value('name');
        }
        return $itemName ?: 'listing';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'slug' || $field === null) {
            return $this->where('slug', $value)
                ->orWhere('id', is_numeric($value) ? (int) $value : null)
                ->first();
        }

        return parent::resolveRouteBinding($value, $field);
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


    public function getAssetableAttribute(): ?Item
    {
        return $this->item;
    }

    public function getTitleAttribute(): string
    {
        return $this->attributes['title'] ?? ($this->item?->name ?? "Listing #{$this->id}");
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

    public function isAvailable(): bool
    {
        return (bool) (
            $this->is_published
            && $this->is_active
            && $this->latestModeration?->status === 'approved'
            && $this->quantity > 0
            && $this->availableQuantity() > 0
        );
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->whereRaw('(COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0) - COALESCE(sold_quantity, 0)) > 0')
            ->approved();
    }

    public function scopeInCurrentCountry(Builder $query, ?int $countryId = null)
    {
        $countryId = $countryId ?? session('current_location.country_id');

        return $query->whereHas('seller', fn ($q) => $q->where('country_id', $countryId));
    }

    public function getPrimaryImageAttribute(): ?Media
    {
        // 1. Direct media if any exists (legacy fallback)
        if ($this->relationLoaded('media') && $this->media->isNotEmpty()) {
            $direct = $this->media->first(fn($m) => $m->is_image) ?? $this->media->first();
            if ($direct) {
                return $direct;
            }
        } elseif ($this->media()->exists()) {
            $direct = $this->images()->orderBy('sort_order')->first() ?? $this->media()->orderBy('sort_order')->first();
            if ($direct) {
                return $direct;
            }
        }

        // 2. Associated item's primary image from Media model
        return $this->item?->primary_image;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primary_image?->url;
    }

    
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }


    public function watchers(): MorphMany
    {
        return $this->morphMany(Watchlist::class, 'watchable');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(ViewedEntity::class, 'viewable');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }


    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function activePromotion(): HasOne
    {
        return $this->hasOne(Promotion::class)
            ->where('status', 'active')
            ->whereColumn('achieved_count', '<', 'target_count');
    }

    public function invoiceItems(): MorphMany
    {
        return $this->morphMany(InvoiceItem::class, 'itemable');
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', true);
    }


    public function moderations(): MorphMany
    {
        return $this->morphMany(Moderation::class, 'moderatable');
    }
    
    public function latestModeration(): MorphOne
    {
        return $this->morphOne(Moderation::class, 'moderatable')->latestOfMany();
    }



    public function scopeModerationStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('latestModeration', function (Builder $m) use ($status) {
            $m->where('status', $status);
        });
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'rejected');
    }

    public function scopeWithOverviewStats(Builder $query): Builder
    {
        return $query->withCount([
            'viewedByUsers',
            'savedByUsers',
            'alerts',
            'reports',
            'promotions',
        ]);
    }

    
    protected function getFullAddressAttribute()
    {
        return collect([
            $this->address,
            $this->neighborhood,
            $this->city,
            $this->state?->name,
            $this->country?->name,
        ])->filter()->implode(', ');
        
    }

    protected function getStatusAttribute()
    {
        if (! $this->is_published) {
            return 'draft';
        }
        if (! $this->is_active){
            return 'inactive';
        }
        if (! $this->latestModeration || $this->latestModeration->status == 'pending'){
            return 'pending';
        }
        if ($this->latestModeration->status == 'rejected'){
            return 'rejected';
        }
        if ($this->availableQuantity() <= 0){
            return 'sold out';
        }
        if ($this->latestModeration->status == 'approved'){
            return 'live';
        }
    }

    public function getCurrencyAttribute()
    {
        return $this->user->country->currency->symbol ?? '$';
    }

    public function featureValue(string $feature): ?string
    {
        return $this->features->firstWhere('feature', $feature)?->value;
    }

    public function getFormattedPriceAttribute(): ?string
    {
        $raw = $this->attributes['price'] ?? null;
        if ($raw === null) {
            return null;
        }

        $value = (float) $raw;

        // Define thresholds and their suffixes
        $thresholds = [
            1_000_000_000 => 'B',
            1_000_000 => 'M',
            1_000 => 'K',
        ];

        foreach ($thresholds as $threshold => $suffix) {
            if (abs($value) >= $threshold) {
                $divided = $value / $threshold;

                // Round to 1 decimal place and remove trailing zeros
                $formatted = rtrim(rtrim(number_format($divided, 1), '0'), '.');

                return $formatted . $suffix;
            }
        }

        // For numbers less than 1000, return as is
        return (string) $value;
    }
}

