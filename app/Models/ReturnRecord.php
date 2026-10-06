<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReturnRecord extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'invoice_id',
        'issue_id',
        'warranty_claim_id',
        'buyer_id',
        'seller_id',
        'status',
        'delivery_method',
        'shipment_id',
        'notes',
        'rejection_reason',
        'rejection_evidence',
        'received_at',
        'accepted_at',
        'disputed_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'accepted_at' => 'datetime',
            'disputed_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function warrantyClaim(): BelongsTo
    {
        return $this->belongsTo(WarrantyClaim::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class, 'return_id');
    }
}
