<?php

namespace App\Observers;

use App\Jobs\ProcessMediaImageJob;
use App\Models\Media;

class MediaObserver
{
    public function created(Media $media): void
    {
        // If already marked processed, do nothing
        if ($media->is_processed) {
            return;
        }

        // Only images need dimension checks
        if (!$media->is_image) {
            return;
        }

        $mediable = $media->mediable;
        if ($mediable && method_exists($mediable, 'getMediaDimensionRequirement')) {
            $requirement = $mediable->getMediaDimensionRequirement($media->collection);
            if ($requirement) {
                $targetWidth = (int) ($requirement['width'] ?? 0);
                $targetHeight = (int) ($requirement['height'] ?? 0);

                if ($targetWidth > 0 && $targetHeight > 0) {
                    if ($media->width !== $targetWidth || $media->height !== $targetHeight) {
                        ProcessMediaImageJob::dispatch($media->id, $requirement);
                    } else {
                        $media->updateQuietly(['is_processed' => true]);
                    }
                }
            }
        }
    }
}
