<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ViewedEntity extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'device_type',
        'user_id',
        'viewable_id',
        'viewable_type',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}
