<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #334155;
            margin: 0;
            padding: 30px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #4634b7;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .invoice-meta {
            font-size: 11px;
            color: #64748b;
            text-align: right;
            margin-top: 4px;
        }
        .parties-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .party-box {
            width: 48%;
            vertical-align: top;
        }
        .party-label {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .party-detail {
            font-size: 11px;
            color: #64748b;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 40%;
            margin-left: auto;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 12px;
        }
        .total-row {
            border-top: 2px solid #e2e8f0;
            font-weight: bold;
            font-size: 15px !important;
            color: #4634b7;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-escrow { background: #ede9fe; color: #5b21b6; }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">Parts &amp; Parcel</div>
                <div class="brand-subtitle">Automotive &amp; Tech Components Escrow Marketplace</div>
            </td>
            <td>
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">#{{ $invoice->invoice_number }}</div>
                <div class="invoice-meta">Date: {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : now()->format('M d, Y') }}</div>
                <div class="invoice-meta">
                    Status: 
                    @if($invoice->status === 'paid')
                        <span class="badge badge-paid">PAID &amp; SECURED</span>
                    @else
                        <span class="badge badge-pending">{{ strtoupper($invoice->status) }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="parties-table">
        <tr>
            <td class="party-box">
                <div class="party-label">Billed To (Buyer)</div>
                <div class="party-name">{{ $invoice->buyer?->name ?? 'Customer' }}</div>
                <div class="party-detail">{{ $invoice->buyer?->email ?? '' }}</div>
                <div class="party-detail">{{ $invoice->buyer?->phone ?? '' }}</div>
            </td>
            <td class="party-box">
                <div class="party-label">Fulfilled By (Seller)</div>
                <div class="party-name">{{ $invoice->seller?->business_name ?: ($invoice->seller?->name ?? 'Vendor') }}</div>
                <div class="party-detail">{{ $invoice->seller?->email ?? '' }}</div>
                <div class="party-detail">{{ $invoice->seller?->phone ?? '' }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->title ?? ($item->listing?->title ?? 'Platform Item') }}</strong>
                        @if($item->condition)
                            <div style="font-size: 10px; color: #64748b;">Condition: {{ ucfirst($item->condition) }}</div>
                        @endif
                        @if($item->warranty_period_days)
                            <div style="font-size: 10px; color: #059669;">Warranty: {{ $item->warranty_period_days }} Days Escrow Protected</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">₦{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right"><strong>₦{{ number_format($item->total_price, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td>
                        <strong>Transaction Invoice</strong>
                        <div style="font-size: 10px; color: #64748b;">Custom order or accepted proposal</div>
                    </td>
                    <td class="text-right">1</td>
                    <td class="text-right">₦{{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-right"><strong>₦{{ number_format($invoice->subtotal, 2) }}</strong></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">₦{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">-₦{{ number_format($invoice->discount, 2) }}</td>
            </tr>
        @endif
        @if($invoice->tax > 0)
            <tr>
                <td>Tax:</td>
                <td class="text-right">₦{{ number_format($invoice->tax, 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td><strong>Total Amount:</strong></td>
            <td class="text-right"><strong>₦{{ number_format($invoice->total, 2) }}</strong></td>
        </tr>
    </table>

    <div style="background-color: #f8fafc; border-radius: 8px; padding: 12px 16px; border: 1px solid #e2e8f0; font-size: 11px;">
        <strong style="color: #4634b7;">Escrow Protection Guarantee:</strong>
        Funds are securely held in escrow until the inspection warranty period concludes without active disputes.
    </div>

    <div class="footer">
        Thank you for choosing Parts &amp; Parcel. This is a computer-generated document. No signature is required.
    </div>
</body>
</html>
