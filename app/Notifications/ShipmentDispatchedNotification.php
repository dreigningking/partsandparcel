<?php

namespace App\Notifications;

use App\Models\Shipment;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ShipmentDispatchedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $courier = $this->shipment->courier ?? 'Seller Dispatch';
        $tracking = $this->shipment->tracking_number ?: 'Local Transit';

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Parcel Dispatched',
            "Your order has been dispatched via {$courier} (Tracking: {$tracking}).",
            ['shipment_id' => (string) $this->shipment->id]
        );

        return [
            'type' => 'shipment_dispatched',
            'category' => 'transactions',
            'title' => 'Parcel Dispatched',
            'message' => "Your order has been dispatched via {$courier} (Tracking: {$tracking}).",
            'shipment_id' => $this->shipment->id,
            'action_url' => route('shipments.view', ['id' => $this->shipment->id]),
            'icon' => 'fas fa-truck-fast',
            'icon_color' => 'text-purple-600 bg-purple-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Parcel Dispatched',
            'message' => "Your order has been dispatched (Tracking: {$this->shipment->tracking_number}).",
            'shipment_id' => $this->shipment->id,
            'action_url' => route('shipments.view', ['id' => $this->shipment->id]),
        ]);
    }
}
