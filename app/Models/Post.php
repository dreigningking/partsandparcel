<?php

namespace App\Models;

use App\Models\ViewedEntity;
use App\Models\Watchlist;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(PostComment::class)->approved();
    }

    public function views(): MorphMany
    {
        return $this->morphMany(ViewedEntity::class, 'viewable');
    }

    public function watchlists(): MorphMany
    {
        return $this->morphMany(Watchlist::class, 'watchable');
    }

    public function isWatchedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->watchlists()->where('user_id', $user->id)->exists();
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        $media = $this->images()->whereIn('collection', ['featured', 'featured_image', 'default'])->first() 
            ?: $this->images()->first();

        return $media?->url;
    }

    public function getFeaturedVideoUrlAttribute(): ?string
    {
        $media = $this->videos()->whereIn('collection', ['featured', 'featured_video', 'default'])->first() 
            ?: $this->videos()->first();

        return $media?->url;
    }

    public function getTagsListAttribute(): array
    {
        if (empty($this->tags)) {
            return [];
        }
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->tags))));
    }
}
