<?php

namespace App\Jobs;

use App\Models\Discussion;
use App\Models\Offer;
use App\Models\Response;
use App\Models\User;
use App\Models\Watchlist;
use App\Notifications\DiscussionEditedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyDiscussionEditedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $discussionId,
        public int $editTimestamp
    ) {}

    public function handle(): void
    {
        // 1. Debounce check: Verify if a newer edit occurred since this job was queued
        $latestEditTimestamp = (int) Cache::get("discussion_edit_timestamp_{$this->discussionId}", 0);
        if ($latestEditTimestamp !== $this->editTimestamp) {
            // A newer edit has been made within the window; this job yields to the newer one.
            Log::info("Debouncing DiscussionEditedJob: Older edit job for discussion #{$this->discussionId} skipped.");
            return;
        }

        $discussion = Discussion::with(['user'])->find($this->discussionId);
        if (! $discussion) {
            return;
        }

        // 2. Collect all responders, watchers, and offer senders (excluding the request owner)
        $responderIds = Response::where('discussion_id', $discussion->id)
            ->pluck('user_id')
            ->unique();

        $watcherIds = Watchlist::where('watchable_type', Discussion::class)
            ->where('watchable_id', $discussion->id)
            ->pluck('user_id')
            ->unique();

        $offerSenderIds = Offer::where('discussion_id', $discussion->id)
            ->orWhereIn('response_id', Response::where('discussion_id', $discussion->id)->pluck('id'))
            ->pluck('sender_id')
            ->unique();

        $allRecipientIds = $responderIds
            ->merge($watcherIds)
            ->merge($offerSenderIds)
            ->unique()
            ->reject(fn ($id) => (int) $id === (int) $discussion->user_id)
            ->values();

        if ($allRecipientIds->isEmpty()) {
            Log::info("No recipients to notify for edited discussion #{$discussion->id}");
            return;
        }

        $recipients = User::whereIn('id', $allRecipientIds)->get();

        Notification::send($recipients, new DiscussionEditedNotification($discussion));
        Log::info("Sent DiscussionEditedNotification to {$recipients->count()} recipients for discussion #{$discussion->id}");
    }
}

