<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Listing;
use App\Models\Moderation;

class ItemObserver
{
    public static bool $seeding = false;

    public function updated(Item $item): void
    {
        if (self::$seeding) {
            return;
        }

        // Substantive content changes on the item (ignoring timestamp and soft deletes)
        $changes = array_diff(array_keys($item->getChanges()), [
            'updated_at',
            'deleted_at',
        ]);

        if (empty($changes)) {
            return;
        }

        // Find all listings associated with this item
        $listings = $item->listings()->get();
        if ($listings->isEmpty() && $item->listing) {
            $listings = collect([$item->listing]);
        }

        foreach ($listings as $listing) {
            $alreadyPending = Moderation::where('moderatable_type', Listing::class)
                ->where('moderatable_id', $listing->id)
                ->where('status', 'pending')
                ->exists();

            if (!$alreadyPending) {
                Moderation::create([
                    'moderatable_type' => Listing::class,
                    'moderatable_id' => $listing->id,
                    'action' => 'updated',
                    'status' => 'pending',
                    'reason' => null,
                    'moderated_by' => null,
                ]);
            }
        }
    }
}
