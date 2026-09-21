<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'listing_id',
        'description',
        'type',
        'quantity',
        'unit_price',
        'warranty_period_days',
        'warranty_terms',
        'warranty_starts_at',
        'warranty_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'warranty_period_days' => 'integer',
            'warranty_starts_at' => 'datetime',
            'warranty_ends_at' => 'datetime',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function subtotal(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
