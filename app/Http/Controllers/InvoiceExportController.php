<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class InvoiceExportController extends Controller
{
    /**
     * Export invoices to Excel or CSV.
     */
    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $userId = $user && ! $user->isAdmin() ? $user->id : null;
        $status = $request->query('status');
        $format = $request->query('format', 'xlsx');

        $fileName = 'invoices-' . date('Y-m-d') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');

        return Excel::download(new InvoicesExport($userId, $status), $fileName);
    }

    /**
     * Download or view invoice as PDF.
     */
    public function exportPdf(Invoice $invoice, Request $request)
    {
        $user = $request->user();

        // Authorization check: only buyer, seller, or admin can access
        if ($user && ! $user->isAdmin() && $invoice->buyer_id !== $user->id && $invoice->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to invoice document.');
        }

        $invoice->load(['buyer', 'seller', 'items.listing']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        if ($request->query('stream')) {
            return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
        }

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
