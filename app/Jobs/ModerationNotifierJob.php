<?php

namespace App\Jobs;

use App\Models\Discussion;
use App\Models\Listing;
use App\Models\Moderation;
use App\Models\PostComment;
use App\Models\User;
use App\Notifications\ModerationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ModerationNotifierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?int $moderationId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pendingListings = Moderation::where('status', 'pending')
            ->where('moderatable_type', Listing::class)
            ->count();

        $pendingDiscussions = Moderation::where('status', 'pending')
            ->where('moderatable_type', Discussion::class)
            ->count();

        $pendingComments = Moderation::where('status', 'pending')
            ->where('moderatable_type', PostComment::class)
            ->count();

        $totalPending = Moderation::where('status', 'pending')->count();

        if ($totalPending > 0) {
            $breakdown = [
                'listings' => $pendingListings,
                'discussions' => $pendingDiscussions,
                'comments' => $pendingComments,
            ];

            // Notify all staff/admins
            $admins = User::whereNotNull('role_id')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ModerationNotification($totalPending, $breakdown));
            }
        }
    }
}
