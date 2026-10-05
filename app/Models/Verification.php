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
        'liveness_verified',
        'status',
        'rejection_reason',
        'reviewed_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'liveness_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function moderations(): MorphMany
    {
        return $this->morphMany(Moderation::class, 'moderatable');
    }

    public function latestModeration(): MorphOne
    {
        return $this->morphOne(Moderation::class, 'moderatable')->latestOfMany();
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'passport' => 'International Passport',
            'drivers_license' => "Driver's License",
            'national_id' => 'National ID Card',
            'voters_card' => "Voter's Card",
            'govt_id' => 'Government Issued Photo ID',
            default => ucwords(str_replace('_', ' ', $this->document_type ?? 'Government ID')),
        };
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
