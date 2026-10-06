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

        $invoiceItem = \App\Models\InvoiceItem::where('itemable_type', \App\Models\Shipment::class)
            ->where('itemable_id', $this->shipment->id)
            ->first();
        $actionUrl = $invoiceItem ? route('invoices.view', ['invoice_id' => $invoiceItem->invoice_id, 'tab' => 'shipment']) : route('invoices');

        return [
            'type' => 'shipment_dispatched',
            'category' => 'transactions',
            'title' => 'Parcel Dispatched',
            'message' => "Your order has been dispatched via {$courier} (Tracking: {$tracking}).",
            'shipment_id' => $this->shipment->id,
            'action_url' => $actionUrl,
            'icon' => 'fas fa-truck-fast',
            'icon_color' => 'text-purple-600 bg-purple-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $invoiceItem = \App\Models\InvoiceItem::where('itemable_type', \App\Models\Shipment::class)
            ->where('itemable_id', $this->shipment->id)
            ->first();
        $actionUrl = $invoiceItem ? route('invoices.view', ['invoice_id' => $invoiceItem->invoice_id, 'tab' => 'shipment']) : route('invoices');

        return new BroadcastMessage([
            'title' => 'Parcel Dispatched',
            'message' => "Your order has been dispatched (Tracking: {$this->shipment->tracking_number}).",
            'shipment_id' => $this->shipment->id,
            'action_url' => $actionUrl,
        ]);
    }
}
