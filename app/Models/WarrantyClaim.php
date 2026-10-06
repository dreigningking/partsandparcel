<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'invoice_item_id',
        'buyer_id',
        'seller_id',
        'status',
        'claim_type',
        'description',
        'evidence',
        'seller_notes',
        'responded_at',
        'resolved_at',
        'disputed_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'disputed_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function replacement(): HasOne
    {
        return $this->hasOne(Replacement::class);
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(ReturnRecord::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }
}
