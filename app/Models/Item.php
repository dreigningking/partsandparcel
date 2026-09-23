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
        'model_id',
        'serial_number',
        'condition_status',
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

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'assetable');
    }

    public function mediaDimensionRequirements(): array
    {
        return [
            'default' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
            'images' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
        ];
    }
}
