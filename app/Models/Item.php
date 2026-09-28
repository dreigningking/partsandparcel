<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Item extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        'parent_id',
        'location_id',
        'model_id',
        'item_type',
        'name',
        'condition_status',
        'condition_notes',
        'description',
        'status',
        'acquired_at',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'datetime',
        ];
    }

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

    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'assetable');
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

    public function mediaDimensionRequirements(): array
    {
        return [
            'default' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
            'images' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
        ];
    }
}
