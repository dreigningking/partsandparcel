<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'buyer_id',
        'seller_id',
        'cart_id',
        'offer_id',
        'delivery_method',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'commission',
        'status',
        'issued_at',
        'accepted_at',
        'paid_at',
        'completed_at',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'commission' => 'decimal:2',
            'issued_at' => 'datetime',
            'accepted_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestSuccessfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'successful')->latestOfMany();
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function serviceJobs(): HasMany
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function isPlatformEscrow(): bool
    {
        return $this->payment_method === 'platform';
    }

    public function isDirectPayment(): bool
    {
        return $this->payment_method === 'direct';
    }

    public function maxWarrantyDays(): ?int
    {
        return $this->items->max('warranty_period_days');
    }
}
