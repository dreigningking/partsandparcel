<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessMediaImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $mediaId;
    public ?array $dimensions;

    public function __construct(int $mediaId, ?array $dimensions = null)
    {
        $this->mediaId = $mediaId;
        $this->dimensions = $dimensions;
    }

    public function handle(): void
    {
        try {
            $media = Media::find($this->mediaId);
            if (!$media) {
                Log::warning("ProcessMediaImageJob: Media record #{$this->mediaId} not found.");
                return;
            }

            if (!$media->is_image) {
                Log::info("ProcessMediaImageJob: Media #{$this->mediaId} is not an image ({$media->media_type}). Skipping.");
                return;
            }

            $fullPath = $media->full_path;
            if (!$fullPath || !file_exists($fullPath)) {
                Log::warning("ProcessMediaImageJob: File not found at {$fullPath} for Media #{$this->mediaId}.");
                return;
            }

            $dimensions = $this->resolveDimensions($media);
            if (!$dimensions) {
                Log::info("ProcessMediaImageJob: No dimension requirements found for Media #{$this->mediaId}. Skipping.");
                return;
            }

            $targetWidth = (int) ($dimensions['width'] ?? 1000);
            $targetHeight = (int) ($dimensions['height'] ?? 1000);
            $bgColor = $dimensions['bg_color'] ?? 'ffffff';
            $quality = (int) ($dimensions['quality'] ?? 90);

            if ($targetWidth <= 0 || $targetHeight <= 0) {
                return;
            }

            $manager = new ImageManager(new Driver());
            $img = $manager->decodePath($fullPath);
            $currentWidth = $img->width();
            $currentHeight = $img->height();

            // Check if image already matches target dimensions
            if ($currentWidth === $targetWidth && $currentHeight === $targetHeight) {
                $media->update([
                    'width' => $currentWidth,
                    'height' => $currentHeight,
                    'is_processed' => true,
                ]);
                Log::info("ProcessMediaImageJob: Media #{$this->mediaId} already matches {$targetWidth}x{$targetHeight}. Marked processed.");
                return;
            }

            // Letterbox / contain within target dimensions with white background
            $img = $img->contain($targetWidth, $targetHeight, $bgColor, 'center');
            $img->save($fullPath, $quality);

            $media->update([
                'width' => $targetWidth,
                'height' => $targetHeight,
                'size' => file_exists($fullPath) ? filesize($fullPath) : $media->size,
                'is_processed' => true,
            ]);

            Log::info("ProcessMediaImageJob: Media #{$this->mediaId} ({$media->file_path}) successfully processed to {$targetWidth}x{$targetHeight}.");
        } catch (\Throwable $e) {
            Log::error("ProcessMediaImageJob error processing Media #{$this->mediaId}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    protected function resolveDimensions(Media $media): ?array
    {
        if (!empty($this->dimensions)) {
            return $this->dimensions;
        }

        // Check if parent mediable model defines dimension requirements
        $mediable = $media->mediable;
        if ($mediable && method_exists($mediable, 'getMediaDimensionRequirement')) {
            $req = $mediable->getMediaDimensionRequirement($media->collection);
            if ($req) {
                return $req;
            }
        }

        return config('media.default_dimensions');
    }
}
