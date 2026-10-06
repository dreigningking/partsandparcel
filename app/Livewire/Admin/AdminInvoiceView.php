<?php

namespace App\Livewire\Admin;

use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Refund;
use App\Models\Replacement;
use App\Models\Settlement;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Invoice Management — Admin Control Center')]
class AdminInvoiceView extends Component
{
    public Invoice $invoice;

    #[Url(as: 'tab')]
    public string $activeTab = 'details';

    // Admin Action Modals & State
    public bool $showEscrowReleaseModal = false;
    public bool $showCancelModal = false;
    public bool $showShipmentStatusModal = false;
    public ?int $selectedShipmentId = null;
    public string $newShipmentStatus = 'in_transit';
    public string $adminNotes = '';

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->loadRelations();
    }

    protected function loadRelations(): void
    {
        $this->invoice->loadMissing([
            'buyer.primaryLocation',
            'seller.bankAccounts',
            'seller.primaryLocation',
            'seller.country',
            'items.itemable',
            'issues.items.invoiceItem',
            'issues.dispute',
            'issues.returnRecord.shipment',
            'issues.replacement.shipment',
            'serviceJobs.review',
            'serviceJobs.provider',
            'serviceJobs.location',
            'serviceJobs.brand',
            'serviceJobs.deviceModel',
            'refunds.payment',
            'refunds.items',
            'settlement.payment',
            'payments',
        ]);
    }

    public function switchTab(string $tab): void
    {
        $validTabs = ['details', 'shipment', 'service', 'issue', 'dispute', 'replacement', 'refund', 'warranty'];
        if (in_array($tab, $validTabs)) {
            $this->activeTab = $tab;
        }
    }

    public function openShipmentStatusModal(int $shipmentId): void
    {
        $this->selectedShipmentId = $shipmentId;
        $shipment = Shipment::find($shipmentId);
        if ($shipment) {
            $this->newShipmentStatus = $shipment->status;
        }
        $this->showShipmentStatusModal = true;
    }

    public function updateShipmentStatus(): void
    {
        if (! $this->selectedShipmentId) {
            return;
        }

        $shipment = Shipment::findOrFail($this->selectedShipmentId);
        $oldStatus = $shipment->status;
        $shipment->status = $this->newShipmentStatus;

        if ($this->newShipmentStatus === 'dispatched' && ! $shipment->dispatched_at) {
            $shipment->dispatched_at = now();
        } elseif ($this->newShipmentStatus === 'delivered' && ! $shipment->delivered_at) {
            $shipment->delivered_at = now();
        }

        $shipment->save();

        session()->flash('status', "Shipment #{$shipment->tracking_number} status updated from '{$oldStatus}' to '{$this->newShipmentStatus}'.");
        $this->showShipmentStatusModal = false;
        $this->selectedShipmentId = null;
        $this->loadRelations();
    }

    public function confirmEscrowRelease(): void
    {
        try {
            $settlement = $this->invoice->settlement ?: Settlement::where('invoice_id', $this->invoice->id)->first();
            if ($settlement) {
                $settlement->status = 'settled';
                $settlement->settled_at = now();
                $settlement->save();
            }

            $this->invoice->status = 'accepted';
            $this->invoice->save();

            session()->flash('status', "Escrow funds released for Invoice #{$this->invoice->invoice_number}. Settlement marked as settled.");
        } catch (\Throwable $e) {
            Log::error("Failed to release escrow for invoice #{$this->invoice->id}: " . $e->getMessage());
            session()->flash('error', "Could not release escrow: " . $e->getMessage());
        }

        $this->showEscrowReleaseModal = false;
        $this->loadRelations();
    }

    public function confirmCancelInvoice(): void
    {
        $this->invoice->status = 'cancelled';
        $this->invoice->save();

        session()->flash('status', "Invoice #{$this->invoice->invoice_number} has been cancelled.");
        $this->showCancelModal = false;
        $this->loadRelations();
    }

    public function render()
    {
        $outboundShipment = $this->invoice->outboundShipment();
        $returnShipment = $this->invoice->returnShipment();

        $allShipments = collect();
        if ($outboundShipment) {
            $allShipments->push($outboundShipment);
        }
        if ($returnShipment && (! $outboundShipment || $returnShipment->id !== $outboundShipment->id)) {
            $allShipments->push($returnShipment);
        }

        // Additional standalone shipments matching buyer/seller
        $additionalShipments = Shipment::where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('sender_id', $this->invoice->seller_id)
                    ->where('receiver_id', $this->invoice->buyer_id);
            })->orWhere(function ($sub) {
                $sub->where('sender_id', $this->invoice->buyer_id)
                    ->where('receiver_id', $this->invoice->seller_id);
            });
        })->whereNotIn('id', $allShipments->pluck('id')->filter()->all())->get();

        $allShipments = $allShipments->merge($additionalShipments);

        $issues = $this->invoice->issues()->with(['items.invoiceItem', 'reporter', 'dispute', 'returnRecord', 'replacement'])->latest()->get();
        $disputes = Dispute::where('invoice_id', $this->invoice->id)->with(['opener', 'respondent', 'resolver', 'issue', 'warrantyClaim', 'replacement', 'returnRecord', 'items'])->latest()->get();
        $warrantyClaims = \App\Models\WarrantyClaim::where('invoice_id', $this->invoice->id)->with(['item', 'invoiceItem', 'buyer', 'seller', 'dispute', 'replacement', 'returnRecord'])->latest()->get();
        $replacements = Replacement::where('invoice_id', $this->invoice->id)->with(['shipment', 'issue', 'warrantyClaim', 'dispute'])->latest()->get();
        $returnRecords = \App\Models\ReturnRecord::where('invoice_id', $this->invoice->id)->with(['shipment', 'issue', 'buyer', 'seller'])->latest()->get();
        $refunds = $this->invoice->refunds()->with(['payment', 'items'])->latest()->get();
        $serviceJobs = $this->invoice->serviceJobs()->with(['provider', 'location', 'brand', 'deviceModel', 'review'])->get();

        $hasShipments = $allShipments->isNotEmpty() || $this->invoice->items->whereIn('type', ['pickup', 'delivery', 'return'])->isNotEmpty();
        $hasServices = $serviceJobs->isNotEmpty() || $this->invoice->hasServices();
        $hasIssues = $issues->isNotEmpty();
        $hasDisputes = $disputes->isNotEmpty();
        $hasReplacements = $replacements->isNotEmpty();
        $hasRefunds = $refunds->isNotEmpty();
        $hasWarranty = $this->invoice->hasWarranty() || $warrantyClaims->isNotEmpty() || $issues->where('type', 'warranty_claim')->isNotEmpty();

        $settlement = $this->invoice->settlement ?: Settlement::where('invoice_id', $this->invoice->id)->first();
        $payment = $settlement?->payment ?: $this->invoice->payments()->whereIn('status', ['successful', 'paid', 'held_in_escrow'])->latest()->first();

        return view('livewire.admin.admin-invoice-view', [
            'invoice' => $this->invoice,
            'outboundShipment' => $outboundShipment,
            'returnShipment' => $returnShipment,
            'allShipments' => $allShipments,
            'issues' => $issues,
            'disputes' => $disputes,
            'warrantyClaims' => $warrantyClaims,
            'replacements' => $replacements,
            'returnRecords' => $returnRecords,
            'refunds' => $refunds,
            'serviceJobs' => $serviceJobs,
            'settlement' => $settlement,
            'payment' => $payment,
            'hasShipments' => $hasShipments,
            'hasServices' => $hasServices,
            'hasIssues' => $hasIssues,
            'hasDisputes' => $hasDisputes,
            'hasReplacements' => $hasReplacements,
            'hasRefunds' => $hasRefunds,
            'hasWarranty' => $hasWarranty,
        ]);
    }
}
