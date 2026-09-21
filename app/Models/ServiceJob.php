<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'provider_id',
        'item_id',
        'external_item_description',
        'offer_id',
        'invoice_id',
        'title',
        'description',
        'status',
        'location_id',
        'scheduled_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'warranty_period_days',
        'warranty_terms',
        'warranty_starts_at',
        'warranty_ends_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'warranty_period_days' => 'integer',
            'warranty_starts_at' => 'datetime',
            'warranty_ends_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(ServiceReview::class);
    }
}
