<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\PostComment;

class PostCommentObserver
{
    public static bool $seeding = false;

    public function created(PostComment $postComment): void
    {
        if (self::$seeding) {
            return;
        }

        $this->createModerationRecord($postComment, 'created');
    }

    public function updated(PostComment $postComment): void
    {
        if (self::$seeding) {
            return;
        }

        // Substantive content changes on the comment (ignoring timestamps)
        $changes = array_diff(array_keys($postComment->getChanges()), [
            'updated_at',
        ]);

        if (!empty($changes)) {
            $this->createModerationRecord($postComment, 'updated');
        }
    }

    protected function createModerationRecord(PostComment $postComment, string $action): void
    {
        $alreadyPending = Moderation::where('moderatable_type', PostComment::class)
            ->where('moderatable_id', $postComment->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return;
        }

        Moderation::create([
            'moderatable_type' => PostComment::class,
            'moderatable_id' => $postComment->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
