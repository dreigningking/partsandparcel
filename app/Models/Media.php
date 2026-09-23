<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'collection',
        'name',
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'media_type',
        'size',
        'width',
        'height',
        'is_processed',
        'sort_order',
        'custom_properties',
    ];

    protected function casts(): array
    {
        return [
            'is_processed' => 'boolean',
            'custom_properties' => 'array',
            'width' => 'integer',
            'height' => 'integer',
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Media $media) {
            if ($media->file_path && Storage::disk($media->disk)->exists($media->file_path)) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
        });
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->media_type === 'image' || str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === 'video' || str_starts_with($this->mime_type ?? '', 'video/');
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('media_type', 'image');
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('media_type', 'video');
    }

    public function scopeCollection(Builder $query, string $name): Builder
    {
        return $query->where('collection', $name);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
