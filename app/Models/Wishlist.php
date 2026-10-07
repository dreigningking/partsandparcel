<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Check if the wishlisted listing has stock available.
     */
    public function isAvailable(): bool
    {
        return (bool) (
            $this->listing
            && $this->listing->availableQuantity() > 0
        );
    }

    /**
     * Check if the wishlisted listing has sold out.
     */
    public function isSoldOut(): bool
    {
        return (bool) (
            $this->listing
            && $this->listing->availableQuantity() <= 0
        );
    }

    /**
     * Check if stock is low (1 or 2 remaining).
     */
    public function isLowStock(): bool
    {
        return (bool) (
            $this->isAvailable()
            && $this->listing->availableQuantity() <= 2
        );
    }

    /**
     * Check if the listing is unavailable.
     */
    public function isUnavailable(): bool
    {
        return (bool) (! $this->listing);
    }

    /**
     * Get stock status key: 'in_stock', 'low_stock', 'sold_out', or 'unavailable'.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->isUnavailable()) {
            return 'unavailable';
        }

        if ($this->isSoldOut()) {
            return 'sold_out';
        }

        if ($this->isLowStock()) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * User-friendly status badge text.
     */
    public function getStockBadgeLabelAttribute(): string
    {
        if ($this->isUnavailable()) {
            return 'UNAVAILABLE';
        }

        if ($this->isSoldOut()) {
            return 'SOLD OUT';
        }

        $stock = $this->listing?->availableQuantity() ?? 0;
        if ($this->isLowStock()) {
            return "ONLY {$stock} LEFT";
        }

        return "IN STOCK ({$stock})";
    }

    /**
     * Tailwind CSS classes for the stock status badge.
     */
    public function getStockBadgeClassesAttribute(): string
    {
        return match ($this->stock_status) {
            'sold_out' => 'bg-rose-100 text-rose-800 border-rose-200',
            'unavailable' => 'bg-slate-100 text-slate-700 border-slate-200',
            'low_stock' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    // --- SCOPES ---

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('listing', function ($q) {
            $q->whereRaw('(quantity - reserved_quantity - sold_quantity) > 0');
        });
    }

    public function scopeSoldOut(Builder $query): Builder
    {
        return $query->whereHas('listing', function ($q) {
            $q->whereRaw('(quantity - reserved_quantity - sold_quantity) <= 0');
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $trimmed = trim($term);

        return $query->whereHas('listing', function ($q) use ($trimmed) {
            $q->where('title', 'like', "%{$trimmed}%")
                ->orWhereHas('item', function ($iq) use ($trimmed) {
                    $iq->where('name', 'like', "%{$trimmed}%")
                        ->orWhereHas('deviceModel', function ($dm) use ($trimmed) {
                            $dm->where('name', 'like', "%{$trimmed}%")
                                ->orWhereHas('brand', function ($bq) use ($trimmed) {
                                    $bq->where('name', 'like', "%{$trimmed}%");
                                });
                        });
                });
        });
    }

    public function scopeFilterItemType(Builder $query, ?string $type): Builder
    {
        if (blank($type) || $type === 'all') {
            return $query;
        }

        return $query->whereHas('listing.item', function ($q) use ($type) {
            $q->where('item_type', $type);
        });
    }
}
