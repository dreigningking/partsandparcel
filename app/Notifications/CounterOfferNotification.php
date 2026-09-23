<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CounterOfferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offer $offer) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $senderName = $this->offer->sender->name ?? 'A seller';
        $amount = number_format((float) $this->offer->total_amount);

        app(FcmService::class)->sendToUser(
            $notifiable,
            'New Counter-Offer Received',
            "{$senderName} submitted a counter-offer of ₦{$amount}.",
            ['offer_id' => (string) $this->offer->id]
        );

        return [
            'type' => 'counter_offer',
            'category' => 'offers',
            'title' => 'New Counter-Offer Received',
            'message' => "{$senderName} submitted a counter-proposal of ₦{$amount}.",
            'offer_id' => $this->offer->id,
            'action_url' => route('offers.view', ['id' => $this->offer->id]),
            'icon' => 'fas fa-handshake',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New Counter-Offer Received',
            'message' => "Counter-offer of ₦" . number_format((float) $this->offer->total_amount),
            'offer_id' => $this->offer->id,
            'action_url' => route('offers.view', ['id' => $this->offer->id]),
        ]);
    }
}
