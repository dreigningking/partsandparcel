<?php

namespace App\Models;

use App\Observers\PromotionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy([PromotionObserver::class])]
class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'type',
        'achieved_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'achieved_count' => 'integer',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    public function hasPaidPayment(): bool
    {
        return $this->payments()->whereIn('status', ['completed', 'success'])->exists();
    }

    public function pendingPayment(): ?Payment
    {
        return $this->payments()->where('status', 'pending')->latest()->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
