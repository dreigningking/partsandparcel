<?php

namespace App\Livewire\Dashboard\Shipments;

use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class ShipmentView extends Component
{
    public ?Shipment $shipment = null;

    public function mount($shipment_id = null): void
    {
        if ($shipment_id) {
            $this->shipment = Shipment::with(['originLocation', 'destinationLocation', 'sender', 'receiver', 'items'])
                ->where('slug', $shipment_id)
                ->orWhere('id', is_numeric($shipment_id) ? (int) $shipment_id : null)
                ->first();
        }

        if (! $this->shipment) {
            $this->shipment = Shipment::with(['originLocation', 'destinationLocation', 'sender', 'receiver', 'items'])
                ->latest()
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.shipments.shipment-view', [
            'shipment' => $this->shipment,
        ]);
    }
}
