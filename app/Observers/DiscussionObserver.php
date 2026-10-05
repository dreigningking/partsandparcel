<?php

namespace App\Observers;

use App\Models\Discussion;
use App\Models\Moderation;

class DiscussionObserver
{
    public static bool $seeding = false;

    public function created(Discussion $discussion): void
    {
        if (self::$seeding) {
            return;
        }

        $this->createModerationRecord($discussion, 'created');
    }

    public function updated(Discussion $discussion): void
    {
        if (self::$seeding) {
            return;
        }

        // Substantive content changes (ignoring timestamp and moderation status updates)
        $changes = array_diff(array_keys($discussion->getChanges()), [
            'updated_at',
            'status',
        ]);

        if (!empty($changes)) {
            $this->createModerationRecord($discussion, 'updated');
        }
    }

    protected function createModerationRecord(Discussion $discussion, string $action): void
    {
        $alreadyPending = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $discussion->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return;
        }

        $autoApprove = false;
        if ($action === 'created') {
            $settingVal = \App\Models\Setting::where('name', 'auto_approve_discussion')->value('value');
            $autoApprove = in_array((string) $settingVal, ['1', 'true', 'yes'], true);
        }

        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $discussion->id,
            'action' => $action,
            'status' => $autoApprove ? 'approved' : 'pending',
            'reason' => null,
            'moderated_by' => $autoApprove ? ($discussion->user_id ?? null) : null,
        ]);
    }
}
