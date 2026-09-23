<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Component extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'item_id',
        'name',
        'condition_status',
        'serial_number',
        'status',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'assetable');
    }
}
