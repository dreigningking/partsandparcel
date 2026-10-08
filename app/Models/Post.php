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

    public const HELP_TOPICS = [
        'Buying & Making Offers',
        'Escrow & Secure Payments',
        'Shipping & Deliveries',
        'Seller Tiers & Plans',
        'Community Requests (RFQs)',
        'Disputes & Mediation',
    ];

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
        'is_help',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_help' => 'boolean',
        ];
    }

    public function scopeBlog(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_help', false);
    }

    public function scopeHelp(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_help', true);
    }

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeForTopic(\Illuminate\Database\Eloquent\Builder $query, string $topic): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('tags', 'like', '%' . $topic . '%');
    }

    public function getHelpTopicAttribute(): ?string
    {
        if (! $this->is_help) {
            return null;
        }

        $tagList = array_map('trim', explode(',', (string) $this->tags));
        foreach (self::HELP_TOPICS as $topic) {
            if (in_array($topic, $tagList, true) || stripos((string) $this->tags, $topic) !== false) {
                return $topic;
            }
        }

        return $tagList[0] ?? 'General Help';
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

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
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
