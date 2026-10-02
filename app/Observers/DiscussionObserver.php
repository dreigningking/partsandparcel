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

        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $discussion->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
