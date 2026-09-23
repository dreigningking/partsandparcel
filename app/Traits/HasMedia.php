<?php

namespace App\Traits;

use App\Jobs\ProcessMediaImageJob;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasMedia
{
    public static function bootHasMedia(): void
    {
        static::deleted(function ($model) {
            // Delete all media associated with this model when model is deleted
            foreach ($model->media()->get() as $media) {
                $media->delete();
            }
        });
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order')->orderBy('id');
    }

    public function images(): MorphMany
    {
        return $this->media()->where('media_type', 'image');
    }

    public function videos(): MorphMany
    {
        return $this->media()->where('media_type', 'video');
    }

    /**
     * Attach a media file to this model.
     *
     * @param UploadedFile|string $file
     * @param string $collection
     * @param string|null $disk
     * @param array $customProperties
     * @return Media
     */
    public function attachMedia(
        UploadedFile|string $file,
        string $collection = 'default',
        ?string $disk = null,
        array $customProperties = []
    ): Media {
        $disk = $disk ?: config('media.disk', 'public');
        $folder = 'media/' . Str::snake(class_basename($this)) . '/' . $this->getKey();

        if ($file instanceof UploadedFile) {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
            $fileName = Str::uuid() . '.' . $extension;
            $filePath = $file->storeAs($folder, $fileName, $disk);
        } elseif (is_string($file) && file_exists($file)) {
            $originalName = basename($file);
            $mimeType = mime_content_type($file) ?: 'application/octet-stream';
            $size = filesize($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION) ?: 'bin';
            $fileName = Str::uuid() . '.' . $extension;
            $filePath = $folder . '/' . $fileName;
            Storage::disk($disk)->put($filePath, file_get_contents($file));
        } else {
            throw new \InvalidArgumentException("Invalid file provided to attachMedia.");
        }

        $mediaType = $this->determineMediaType($mimeType, $extension);
        $width = null;
        $height = null;

        if ($mediaType === 'image') {
            $absolutePath = Storage::disk($disk)->path($filePath);
            if (file_exists($absolutePath)) {
                $imageSize = @getimagesize($absolutePath);
                if ($imageSize) {
                    $width = (int) $imageSize[0];
                    $height = (int) $imageSize[1];
                }
            }
        }

        $media = $this->media()->create([
            'collection' => $collection,
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'is_processed' => false,
            'sort_order' => $this->media()->where('collection', $collection)->count(),
            'custom_properties' => $customProperties,
        ]);

        // Check if dimension transformation is required for images
        if ($mediaType === 'image') {
            $requirement = $this->getMediaDimensionRequirement($collection);
            if ($requirement) {
                $targetWidth = (int) ($requirement['width'] ?? 0);
                $targetHeight = (int) ($requirement['height'] ?? 0);

                // If dimensions do not match the recommendation, queue background job
                if ($targetWidth > 0 && $targetHeight > 0 && ($width !== $targetWidth || $height !== $targetHeight)) {
                    ProcessMediaImageJob::dispatch($media->id, $requirement);
                } else {
                    $media->update(['is_processed' => true]);
                }
            }
        }

        return $media;
    }

    /**
     * Define dimension requirements for model collections.
     * Override in models to enforce specific dimensions.
     *
     * Example:
     * return [
     *     'default' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
     *     'images' => ['width' => 1000, 'height' => 1000, 'bg_color' => 'ffffff', 'quality' => 90],
     * ];
     */
    public function mediaDimensionRequirements(): array
    {
        return [];
    }

    /**
     * Get dimension requirement for a collection, with fallback to wildcards.
     */
    public function getMediaDimensionRequirement(string $collection = 'default'): ?array
    {
        $reqs = $this->mediaDimensionRequirements();
        if (isset($reqs[$collection])) {
            return $reqs[$collection];
        }
        if (isset($reqs['*'])) {
            return $reqs['*'];
        }

        return null;
    }

    /**
     * Get the URL of the first media in a collection.
     */
    public function firstMediaUrl(string $collection = 'default', ?string $fallback = null): ?string
    {
        $first = $this->media()->where('collection', $collection)->first();
        return $first ? $first->url : $fallback;
    }

    /**
     * Get the URL of the first image in a collection.
     */
    public function firstImageUrl(string $collection = 'default', ?string $fallback = null): ?string
    {
        $first = $this->images()->where('collection', $collection)->first();
        return $first ? $first->url : $fallback;
    }

    /**
     * Clear all media in a given collection.
     */
    public function clearMediaCollection(string $collection = 'default'): void
    {
        foreach ($this->media()->where('collection', $collection)->get() as $media) {
            $media->delete();
        }
    }

    /**
     * Determine media type from mime string and extension.
     */
    protected function determineMediaType(?string $mimeType, ?string $extension = null): string
    {
        $ext = strtolower($extension ?? '');

        if (($mimeType && str_starts_with($mimeType, 'image/')) || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'])) {
            return 'image';
        }

        if (($mimeType && str_starts_with($mimeType, 'video/')) || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'flv', 'wmv'])) {
            return 'video';
        }

        return 'document';
    }
}
