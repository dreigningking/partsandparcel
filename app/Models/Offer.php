<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'sender_id',
        'recipient_id',
        'discussion_id',
        'response_id',
        'cart_id',
        'delivery_method',
        'discount',
        'terms',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'parent_id');
    }

    public function counterOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'parent_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function serviceJobs(): HasMany
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function conversations(): MorphMany
    {
        return $this->morphMany(Conversation::class, 'contextable');
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->subtotal());
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - (float) $this->discount);
    }

    public function maxWarrantyDays(): ?int
    {
        return $this->items->max('warranty_period_days');
    }

    public function getRootId(): int
    {
        return $this->parent_id ?: $this->id;
    }

    public function getLatestOffer(): Offer
    {
        $rootId = $this->getRootId();
        return Offer::where('id', $rootId)
            ->orWhere('parent_id', $rootId)
            ->latest('id')
            ->first() ?? $this;
    }

    public function isLatest(): bool
    {
        return $this->id === $this->getLatestOffer()->id;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (! $user) return false;
        return $this->status === 'pending'
            && $this->sender_id === $user->id
            && $this->isLatest();
    }

    public function canBeCounteredBy(?User $user): bool
    {
        if (! $user) return false;
        return $this->status === 'pending'
            && $this->recipient_id === $user->id
            && $this->isLatest();
    }

    public function canBeAcceptedBy(?User $user): bool
    {
        if (! $user) return false;
        return $this->status === 'pending'
            && $this->recipient_id === $user->id
            && $this->isLatest();
    }
}
