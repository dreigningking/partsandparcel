<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'contact_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'country_id',
        'state_id',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'is_default',
        'utility_bill_path',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function listings(): HasManyThrough
    {
        return $this->hasManyThrough(Listing::class, Item::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function fullAddress(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state?->name,
            $this->country?->name,
        ])->filter()->implode(', ');
    }

    public function moderations(): MorphMany
    {
        return $this->morphMany(Moderation::class, 'moderatable');
    }

    public function moderation(): MorphOne
    {
        return $this->morphOne(Moderation::class, 'moderatable')->latestOfMany();
    }

    public function latestModeration(): MorphOne
    {
        return $this->moderation();
    }

    public function getVerificationStatusAttribute(): string
    {
        if ($this->moderation) {
            return $this->moderation->status === 'approved' ? 'verified' : $this->moderation->status;
        }

        if (empty($this->utility_bill_path)) {
            return 'unverified';
        }

        return 'pending';
    }

    public function getIsVerifiedAttribute(): bool
    {
        return in_array($this->verification_status, ['approved', 'verified']);
    }

    public function getRejectionReasonAttribute(): ?string
    {
        return $this->moderation?->reason;
    }

    public function getReviewedByAttribute(): ?int
    {
        return $this->moderation?->moderated_by;
    }

    public function getVerifiedAtAttribute()
    {
        return $this->moderation && in_array($this->moderation->status, ['approved', 'verified'])
            ? $this->moderation->updated_at
            : null;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function getUtilityBillUrlAttribute(): ?string
    {
        if (! $this->utility_bill_path) {
            return null;
        }

        return str_starts_with($this->utility_bill_path, 'http') ? $this->utility_bill_path : asset('storage/' . $this->utility_bill_path);
    }
}
