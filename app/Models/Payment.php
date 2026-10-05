<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'paymentable_id',
        'paymentable_type',
        'reference',
        'provider',
        'status',
        'amount',
        'currency',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isHeldInEscrow(): bool
    {
        return $this->status === 'held_in_escrow';
    }

    public function getInvoiceAttribute(): ?Invoice
    {
        if ($this->paymentable_type === Invoice::class || $this->paymentable instanceof Invoice) {
            return $this->paymentable;
        }
        return null;
    }

    public function getInvoiceIdAttribute(): ?int
    {
        return ($this->paymentable_type === Invoice::class) ? (int) $this->paymentable_id : null;
    }
}
