<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'invoice_id',
        'gross_amount',
        'commission',
        'refunds',
        'net_amount',
        'status',
        'eligible_at',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'commission' => 'decimal:2',
            'refunds' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'eligible_at' => 'datetime',
            'settled_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payoutSettlements(): HasMany
    {
        return $this->hasMany(PayoutSettlement::class);
    }

    public function isEligible(): bool
    {
        return $this->status === 'eligible' || ($this->eligible_at && $this->eligible_at->isPast() && $this->status === 'pending');
    }
}
