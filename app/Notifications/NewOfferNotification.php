<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewOfferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offer $offer) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $senderName = $this->offer->sender->name ?? 'A user';
        $amount = number_format((float) $this->offer->total_amount);

        // Attempt FCM push in background
        app(FcmService::class)->sendToUser(
            $notifiable,
            'New Offer Received',
            "{$senderName} submitted an offer of ₦{$amount}.",
            ['offer_id' => (string) $this->offer->id, 'type' => 'offer']
        );

        return [
            'type' => 'offer',
            'category' => 'offers',
            'title' => 'New Offer Received',
            'message' => "{$senderName} submitted an offer of ₦{$amount}.",
            'offer_id' => $this->offer->id,
            'action_url' => route('offers.view', ['id' => $this->offer->id]),
            'icon' => 'fas fa-handshake',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New Offer Received',
            'message' => "You received an offer of ₦" . number_format((float) $this->offer->total_amount),
            'offer_id' => $this->offer->id,
            'action_url' => route('offers.view', ['id' => $this->offer->id]),
        ]);
    }
}
