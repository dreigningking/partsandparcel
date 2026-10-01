<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\Property;
use App\Models\SubscribedProperty;

class SubscribedPropertyObserver
{
    public function created(SubscribedProperty $subscribedProperty): void
    {
        $property = $subscribedProperty->property;
        if ($property && $property->is_published) {
            $existingModeration = Moderation::query()
                ->where('moderatable_type', Property::class)
                ->where('moderatable_id', $property->id)
                ->where('status', 'pending')
                ->first();

            if (! $existingModeration) {
                Moderation::create([
                    'moderatable_type' => Property::class,
                    'moderatable_id' => $property->id,
                    'action' => 'subscription',
                    'status' => 'approved',
                    'reason' => null,
                    'moderated_by' => null,
                ]);
            }
        }
    }
}
