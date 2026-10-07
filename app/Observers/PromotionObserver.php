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
        $autoApprove = (bool) Setting::getValue('auto_approve_promotions', true);
        $status = $autoApprove ? 'approved' : 'pending';

        $existing = Moderation::where('moderatable_type', Promotion::class)
            ->where('moderatable_id', $promotion->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending' || (! $autoApprove && $action === 'updated')) {
                $existing->update([
                    'action' => $action,
                    'status' => $status,
                ]);
            }
            return;
        }

        // Create the moderation record
        Moderation::create([
            'moderatable_type' => Promotion::class,
            'moderatable_id' => $promotion->id,
            'action' => $action,
            'status' => $status,
            'reason' => null,
            'moderated_by' => $autoApprove ? ($promotion->user_id ?? null) : null,
        ]);
    }
}
