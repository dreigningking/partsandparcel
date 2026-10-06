<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'front_image',
        'back_image',
        'selfie_image',
        'liveness_images',
    ];

    protected function casts(): array
    {
        return [
            'liveness_images' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getStatusAttribute(): string
    {
        $status = $this->moderation?->status ?? 'pending';
        return $status === 'approved' ? 'verified' : $status;
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

    public function getLivenessVerifiedAttribute(): bool
    {
        return !empty($this->liveness_images) || !empty($this->selfie_image);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'passport', 'international_passport' => 'International Passport',
            'drivers_license' => "Driver's License",
            'national_id' => 'National ID Card',
            'voters_card' => "Voter's Card",
            'govt_id' => 'Government Issued Photo ID',
            default => ucwords(str_replace('_', ' ', $this->document_type ?? 'Government ID')),
        };
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->isVerified();
    }

    public function isVerified(): bool
    {
        return in_array($this->status, ['approved', 'verified']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getFrontImageUrlAttribute(): ?string
    {
        if (! $this->front_image) {
            return null;
        }

        return str_starts_with($this->front_image, 'http') ? $this->front_image : asset('storage/' . $this->front_image);
    }

    public function getBackImageUrlAttribute(): ?string
    {
        if (! $this->back_image) {
            return null;
        }

        return str_starts_with($this->back_image, 'http') ? $this->back_image : asset('storage/' . $this->back_image);
    }

    public function getSelfieImageUrlAttribute(): ?string
    {
        if (! $this->selfie_image) {
            return null;
        }

        return str_starts_with($this->selfie_image, 'http') ? $this->selfie_image : asset('storage/' . $this->selfie_image);
    }
}
