<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entityType = class_basename($this->report->reportable_type);
        $title = $this->report->title;
        $notes = $this->report->resolution_notes ?: 'Your report has been reviewed and resolved by our administration team.';

        return (new MailMessage)
            ->subject(__('Update: Your report regarding :type has been reviewed', ['type' => $entityType]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? 'Valued User']))
            ->line(__('Thank you for helping keep the Parts & Parcel marketplace safe and trusted.'))
            ->line(__('We have reviewed your report regarding :type (Reason: :title).', [
                'type' => $entityType,
                'title' => $title,
            ]))
            ->line(__('Admin Resolution Notes:'))
            ->line("\"{$notes}\"")
            ->line(__('Status: :status', ['status' => ucfirst($this->report->status)]));
    }

    public function toDatabase(object $notifiable): array
    {
        $entityType = class_basename($this->report->reportable_type);

        return [
            'type' => 'report_resolved',
            'category' => 'reports',
            'title' => "Report on {$entityType} Resolved",
            'message' => "Admin resolved your report: {$this->report->resolution_notes}",
            'resolution_notes' => $this->report->resolution_notes,
            'report_id' => $this->report->id,
            'icon' => 'fas fa-shield-alt',
            'icon_color' => 'text-emerald-600 bg-emerald-50',
        ];
    }
}
