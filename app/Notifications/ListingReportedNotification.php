<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\ListingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingReportedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public ListingReport $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemName = $this->listing->item?->name ?? "Listing #{$this->listing->id}";

        return (new MailMessage)
            ->subject(__('Notice: Your listing ":item" has been reported', ['item' => $itemName]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('A buyer has submitted a report regarding your listing ":item".', ['item' => $itemName]))
            ->line(__('Report Category: :title', ['title' => $this->report->title]))
            ->line(__('Details provided: :desc', ['desc' => $this->report->description ?: 'No additional details provided.']))
            ->line(__('Our moderation team reviews all reports. Please ensure your listing details, photos, condition, and pricing comply with platform guidelines.'))
            ->action(__('View Listing in Dashboard'), route('mylisting.view', $this->listing->id));
    }
}
