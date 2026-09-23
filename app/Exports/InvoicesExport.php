<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?int $userId;
    protected ?string $status;

    public function __construct(?int $userId = null, ?string $status = null)
    {
        $this->userId = $userId;
        $this->status = $status;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        $query = Invoice::query()->with(['buyer', 'seller', 'items']);

        if ($this->userId) {
            $query->where(function ($q) {
                $q->where('buyer_id', $this->userId)
                  ->orWhere('seller_id', $this->userId);
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Issue Date',
            'Buyer Name',
            'Buyer Email',
            'Seller Name',
            'Subtotal (NGN)',
            'Tax (NGN)',
            'Commission (NGN)',
            'Total (NGN)',
            'Payment Method',
            'Status',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->created_at ? $invoice->created_at->format('Y-m-d H:i') : '',
            $invoice->buyer?->name ?? 'N/A',
            $invoice->buyer?->email ?? 'N/A',
            $invoice->seller?->name ?? 'N/A',
            number_format($invoice->subtotal, 2, '.', ''),
            number_format($invoice->tax, 2, '.', ''),
            number_format($invoice->commission, 2, '.', ''),
            number_format($invoice->total, 2, '.', ''),
            ucfirst($invoice->payment_method ?? 'Escrow'),
            ucfirst($invoice->status),
        ];
    }
}
