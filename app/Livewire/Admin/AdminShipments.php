<?php

namespace App\Livewire\Admin;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Shipments & Tracking — Admin Control Center')]
class AdminShipments extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $selectedShipmentId = null;

    public function showShipment(int $id): void
    {
        $this->selectedShipmentId = $id;
    }

    public function closeShipment(): void
    {
        $this->selectedShipmentId = null;
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->status = $newStatus;
        if ($newStatus === 'delivered' && !$shipment->delivered_at) {
            $shipment->delivered_at = now();
        } elseif ($newStatus === 'dispatched' && !$shipment->dispatched_at) {
            $shipment->dispatched_at = now();
        }
        $shipment->save();
        session()->flash('status', __("Shipment #{$shipment->tracking_number} updated to {$newStatus}."));
    }

    public function render()
    {
        $shipments = Shipment::query()
            ->with(['sender', 'receiver', 'originLocation', 'destinationLocation', 'items'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('tracking_number', 'like', '%' . $this->search . '%')
                        ->orWhere('provider_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('sender', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('receiver', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(12);

        $selectedShipment = $this->selectedShipmentId
            ? Shipment::with(['sender', 'receiver', 'originLocation', 'destinationLocation', 'items'])->find($this->selectedShipmentId)
            : null;

        return view('livewire.admin.admin-shipments', [
            'status' => $this->status,
            'search' => $this->search,
            'shipments' => $shipments,
            'selectedShipment' => $selectedShipment,
            'totalCount' => Shipment::count(),
            'pendingCount' => Shipment::where('status', 'pending')->count(),
            'dispatchedCount' => Shipment::where('status', 'dispatched')->count(),
            'deliveredCount' => Shipment::where('status', 'delivered')->count(),
        ]);
    }
}
