<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DisputeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'itemable_type',
        'itemable_id',
        'claim',
        'evidence',
    ];

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
