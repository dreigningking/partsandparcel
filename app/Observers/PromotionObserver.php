<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\Promotion;
use App\Models\Setting;

class PromotionObserver
{
    /**
     * Handle the Promotion "created" event.
     */
    public function created(Promotion $promotion): void
    {
        $this->createModerationRecord($promotion, 'created');
    }

    /**
     * Handle the Promotion "updated" event.
     */
    public function updated(Promotion $promotion): void
    {
        $this->createModerationRecord($promotion, 'updated');
    }

    /**
     * Check conditions and create a moderation record if needed.
     */
    protected function createModerationRecord(Promotion $promotion, string $action): void
    {
        // Check if the promotion has a plan and the plan type is blog_post or newsletter
        $plan = $promotion->plan;
        if (! $plan || ! in_array($plan->type, ['blog_post', 'newsletter'], true)) {
            return;
        }

        // Check the setting for content.generation
        $contentGeneration = Setting::getValue('content.generation', 'auto');
        if ($contentGeneration !== 'manual') {
            return;
        }

        // Create the moderation record
        Moderation::create([
            'moderatable_type' => Promotion::class,
            'moderatable_id' => $promotion->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
