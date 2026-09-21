<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'reference',
        'amount',
        'currency',
        'status',
        'provider',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payoutSettlements(): HasMany
    {
        return $this->hasMany(PayoutSettlement::class);
    }

    public function settlements()
    {
        return $this->belongsToMany(Settlement::class, 'payout_settlements')->withPivot('amount')->withTimestamps();
    }
}
