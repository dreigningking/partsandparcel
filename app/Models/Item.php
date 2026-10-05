<?php

namespace App\Models;

use App\Observers\ItemObserver;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ItemObserver::class])]
class Item extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        'location_id',
        'model_id',
        'year',
        'name',
        'item_type',
        'condition_status',
        'condition_notes',
        'description'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Item::class, 'parent_id');
    }

    public function components(): HasMany
    {
        return $this->children();
    }

    public function listing(): HasOne
    {
        return $this->hasOne(Listing::class, 'item_id');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'item_id');
    }

    public function scopeWhole($query)
    {
        return $query->where('item_type', 'whole');
    }

    public function scopeParts($query)
    {
        return $query->where('item_type', 'part');
    }

    public function scopeScrap($query)
    {
        return $query->where('item_type', 'scrap');
    }

    public function getPrimaryImageAttribute(): ?Media
    {
        if ($this->relationLoaded('media')) {
            $direct = $this->media->first(fn($m) => $m->is_image) ?? $this->media->first();
            if ($direct) {
                return $direct;
            }
        } else {
            $direct = $this->images()->orderBy('sort_order')->first() ?? $this->media()->orderBy('sort_order')->first();
            if ($direct) {
                return $direct;
            }
        }

        // Fallback for harvested component parts to their parent donor unit
        if ($this->parent_id && $this->parent) {
            return $this->parent->primary_image;
        }

        return null;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primary_image?->url;
    }
}
