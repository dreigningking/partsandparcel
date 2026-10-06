<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\Listing;
use App\Models\SubscribedListing;

class SubscribedListingObserver
{
    public function created(SubscribedListing $subscribedListing): void
    {
        $listing = $subscribedListing->listing;
        if ($listing && $listing->is_published) {
            $existingModeration = Moderation::query()
                ->where('moderatable_type', Listing::class)
                ->where('moderatable_id', $listing->id)
                ->where('status', 'pending')
                ->first();

            if (! $existingModeration) {
                Moderation::create([
                    'moderatable_type' => Listing::class,
                    'moderatable_id' => $listing->id,
                    'action' => 'subscription',
                    'status' => 'approved',
                    'reason' => null,
                    'moderated_by' => null,
                ]);
            }
        }
    }
}
