<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if govt_id_number or govt_id_expiry was changed
        if ($user->isDirty(['govt_id_number', 'govt_id_expiry'])) {
            $this->createModerationRecord($user, 'updated');
        }
    }

    /**
     * Create a moderation record for the user.
     */
    protected function createModerationRecord(User $user, string $action): void
    {
        Moderation::create([
            'moderatable_type' => User::class,
            'moderatable_id' => $user->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
