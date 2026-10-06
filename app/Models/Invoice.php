<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Replacement;
use App\Models\ReturnRecord;
use App\Models\Setting;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

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
        'currency',
        'payment_method',
        'commission',
        'status',
        'issued_at',
        'accepted_at',
        'paid_at',
        'completed_at',
        'due_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . strtoupper(\Illuminate\Support\Str::random(10));
            }

            if (empty($invoice->currency)) {
                $seller = $invoice->seller ?: User::with('country')->find($invoice->seller_id);
                $invoice->currency = $seller?->country?->currency ?: (Country::where('is_default', true)->value('currency') ?: 'NGN');
            }

            if (empty($invoice->due_at)) {
                $invoice->due_at = now()->addDays(static::defaultExpiryDays());
            }
        });
    }

    public static function defaultExpiryDays(): int
    {
        return (int) (Setting::getValue('invoice_expiry_days') ?: 3);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper($this->currency ?? 'NGN')) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'GHS' => 'GH₵',
            'KES' => 'KSh',
            'ZAR' => 'R',
            default => $this->seller?->country?->currency_symbol ?: ($this->currency ?? '₦'),
        };
    }

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

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function latestSuccessfulPayment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable')->where('status', 'successful')->latestOfMany();
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

    public function disputes(): HasManyThrough
    {
        return $this->hasManyThrough(Dispute::class, Issue::class);
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

    public function remainingTimeText(): string
    {
        $due = $this->due_at ?: ($this->issued_at ? $this->issued_at->copy()->addDays(static::defaultExpiryDays()) : $this->created_at?->copy()->addDays(static::defaultExpiryDays()));

        if (! $due || now()->greaterThanOrEqualTo($due)) {
            return 'Expired';
        }

        $diffHours = now()->diffInHours($due);
        if ($diffHours < 24) {
            return $diffHours . ' ' . Str::plural('hour', $diffHours) . ' left';
        }

        $diffDays = now()->diffInDays($due);
        return $diffDays . ' ' . Str::plural('day', $diffDays) . ' left';
    }

    public function isExpired(): bool
    {
        $due = $this->due_at ?: ($this->issued_at ? $this->issued_at->copy()->addDays(static::defaultExpiryDays()) : $this->created_at?->copy()->addDays(static::defaultExpiryDays()));
        return $due ? now()->greaterThan($due) : false;
    }

    public function outboundShipment(): ?Shipment
    {
        // 1. Through itemable_type == Shipment
        $item = $this->items->firstWhere('itemable_type', Shipment::class);
        if ($item && $item->itemable) {
            return $item->itemable;
        }

        // 2. Offer items
        if ($this->offer_id && $this->offer) {
            $offerItem = $this->offer->items->firstWhere('itemable_type', Shipment::class);
            if ($offerItem && $offerItem->itemable) {
                return $offerItem->itemable;
            }
        }

        // 3. Fallback shipment from seller to buyer
        return Shipment::where('sender_id', $this->seller_id)
            ->where('receiver_id', $this->buyer_id)
            ->latest()
            ->first();
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(ReturnRecord::class);
    }

    public function replacement(): HasOne
    {
        return $this->hasOne(Replacement::class);
    }

    public function returnShipment(): ?Shipment
    {
        // Check return record's shipment
        $return = $this->returnRecord()->with('shipment')->first();
        if ($return && $return->shipment) {
            return $return->shipment;
        }

        // Check replacement record's shipment
        $replacement = $this->replacement()->with('shipment')->first();
        if ($replacement && $replacement->shipment) {
            return $replacement->shipment;
        }

        // Direct return shipment (buyer to seller)
        return Shipment::where('sender_id', $this->buyer_id)
            ->where('receiver_id', $this->seller_id)
            ->latest()
            ->first();
    }

    public function hasServices(): bool
    {
        return $this->serviceJobs()->exists() || $this->items()->where('type', 'service')->exists();
    }

    public function hasWarranty(): bool
    {
        return $this->items()->where('warranty_period_days', '>', 0)->exists();
    }

    public function activeWarrantyEndsAt(): ?\Carbon\Carbon
    {
        $maxEndsAt = $this->items()->max('warranty_ends_at');
        if ($maxEndsAt) {
            return \Carbon\Carbon::parse($maxEndsAt);
        }

        $maxDays = $this->maxWarrantyDays();
        if ($maxDays && $maxDays > 0) {
            $base = $this->accepted_at ?: ($this->completed_at ?: now());
            return $base->copy()->addDays($maxDays);
        }

        return null;
    }

    public function isWithinWarranty(): bool
    {
        $endsAt = $this->activeWarrantyEndsAt();
        return $endsAt ? now()->lessThanOrEqualTo($endsAt) : false;
    }
}
