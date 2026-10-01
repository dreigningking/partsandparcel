<?php

namespace App\Models;

use App\Observers\PromotionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

// Register observers
#[ObservedBy([PromotionObserver::class])]
class Promotion extends Model
{

    protected $fillable = [
        'user_id',
        'listing_id',
        'type',
        'achieved_count',
        'status'
    ];

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

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function isEditable(): bool
    {
        return $this->isPendingPayment() && ! $this->hasPaidPayment();
    }

    public function isLocked(): bool
    {
        return $this->hasPaidPayment();
    }

    public function currentProgress(): int
    {
        if (! $this->target_type) {
            return 0;
        }

        return (int) ($this->usage[$this->target_type] ?? 0);
    }

    public function isCompleted(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        if ((int) $this->target_count <= 0) {
            return false;
        }

        return $this->currentProgress() >= (int) $this->target_count;
    }

    public function progressPercent(): int
    {
        if ((int) $this->target_count <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->currentProgress() / (int) $this->target_count) * 100));
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->hasPaidPayment()
            && ! $this->isCompleted();
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, ['active', 'pending_content'], true)
            && $this->hasPaidPayment()
            && ! $this->isCompleted();
    }
}
