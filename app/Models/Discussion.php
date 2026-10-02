<?php

namespace App\Models;

use App\Observers\DiscussionObserver;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy([DiscussionObserver::class])]
class Discussion extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        'type',
        'category_id',
        'brand_id',
        'model_id',
        'location_id',
        'budget',
        'title',
        'body',
        'attachments',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    public function getFulfillmentAttribute(): string
    {
        return $this->attachments['fulfillment'] ?? 'Flexible';
    }

    public function getUrgencyAttribute(): string
    {
        return $this->attachments['urgency'] ?? 'Flexible';
    }

    public function getLocationTextAttribute(): string
    {
        if ($this->location) {
            return "{$this->location->city}, {$this->location->state}";
        }
        if ($this->user?->primaryLocation?->city) {
            return "{$this->user->primaryLocation->city}, {$this->user->primaryLocation->state}";
        }
        return $this->attachments['location'] ?? 'Nigeria';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function conversations(): MorphMany
    {
        return $this->morphMany(Conversation::class, 'contextable');
    }

    public function moderations(): MorphMany
    {
        return $this->morphMany(Moderation::class, 'moderatable');
    }

    public function latestModeration(): MorphOne
    {
        return $this->morphOne(Moderation::class, 'moderatable')->latestOfMany();
    }

    public function scopeModerationStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('latestModeration', function (Builder $m) use ($status) {
            $m->where('status', $status);
        });
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $this->scopeModerationStatus($query, 'rejected');
    }

    public function scopeInCurrentCountry($query, ?string $countryCode = null)
    {
        $code = strtoupper($countryCode ?? session('current_location.country_code', 'NG'));

        return $query->whereHas('user', fn ($q) => $q->where('country_code', $code));
    }
}
