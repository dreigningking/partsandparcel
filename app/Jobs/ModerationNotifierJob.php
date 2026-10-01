<?php

namespace App\Jobs;

use App\Models\Moderation;
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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pendingCount = Moderation::where('status', 'pending')->count();

        if ($pendingCount > 0) {
            // Notify admins (you can adjust the notification method as needed)
            // For example, sending an email to all admins
            $admins = User::where('role_id', '!=', null)->get();
            Notification::send($admins, new ModerationNotification($pendingCount));
        }
    }
}
