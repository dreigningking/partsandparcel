<?php

namespace App\Models;

use App\Observers\PostCommentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy([PostCommentObserver::class])]
class PostComment extends Model
{
    protected $fillable = [
        'post_id',
        'name',
        'email',
        'comment',
    ];
    
    protected function casts(): array
    {
        return [];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
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

    public function getStatusAttribute(): string
    {
        return $this->latestModeration?->status ?? 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
