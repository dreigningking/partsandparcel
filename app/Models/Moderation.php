<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Moderation extends Model
{
    protected $fillable = [
        'moderated_by',
        'status',
        'reason',
        'moderatable_type',
        'moderatable_id',
        'action',
    ];

    public function moderatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->moderatable_type) {
            Listing::class, 'Listing', 'listing' => 'Listing',
            Discussion::class, 'Discussion', 'discussion' => 'Discussion',
            PostComment::class, 'PostComment', 'post_comment' => 'Post Comment',
            Item::class, 'Item', 'item' => 'Item',
            Location::class, 'Location', 'location' => 'Location (Address Proof)',
            Verification::class, 'Verification', 'verification' => 'User Identity KYC',
            default => class_basename($this->moderatable_type ?? 'Item'),
        };
    }

    public function getItemTitleAttribute(): string
    {
        $item = $this->moderatable;
        if (!$item) {
            return "Item #{$this->moderatable_id}";
        }

        if ($item instanceof Listing) {
            return $item->item?->name ?? $item->title ?? "Listing #{$item->id}";
        }

        if ($item instanceof Discussion) {
            return $item->title ?? "Discussion #{$item->id}";
        }

        if ($item instanceof PostComment) {
            $postTitle = $item->post?->title ?? "Post #{$item->post_id}";
            return "Comment on: {$postTitle}";
        }

        if ($item instanceof Location) {
            return "Address: {$item->label} ({$item->city}, {$item->state?->name})";
        }

        if ($item instanceof Verification) {
            return "ID Verification: {$item->document_type_label} (" . ($item->document_number ?: 'No ID #') . ")";
        }

        return $item->name ?? $item->title ?? "Item #{$this->moderatable_id}";
    }

    public function getAuthorNameAttribute(): string
    {
        $item = $this->moderatable;
        if (!$item) {
            return '—';
        }

        if ($item instanceof Listing) {
            return $item->user?->name ?? '—';
        }

        if ($item instanceof Discussion) {
            return $item->user?->name ?? '—';
        }

        if ($item instanceof PostComment) {
            return $item->name ?? '—';
        }

        if ($item instanceof Location) {
            return $item->user?->name ?? $item->contact_name ?? '—';
        }

        if ($item instanceof Verification) {
            return $item->user?->name ?? '—';
        }

        return $item->user?->name ?? '—';
    }

    public function getAuthorEmailAttribute(): string
    {
        $item = $this->moderatable;
        if (!$item) {
            return '—';
        }

        if ($item instanceof Listing) {
            return $item->user?->email ?? '—';
        }

        if ($item instanceof Discussion) {
            return $item->user?->email ?? '—';
        }

        if ($item instanceof PostComment) {
            return $item->email ?? '—';
        }

        if ($item instanceof Location) {
            return $item->user?->email ?? '—';
        }

        if ($item instanceof Verification) {
            return $item->user?->email ?? '—';
        }

        return $item->user?->email ?? '—';
    }

    public function getModeratedAtAttribute()
    {
        return $this->status !== 'pending' ? $this->updated_at : null;
    }
}
