<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Advanced;
use App\Models\InvoiceCustomer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class InvoiceReportController extends Controller
{
    /**
     * Display the invoice report index page.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'advances']);

        // Apply filters if provided
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('customer_id')) {
            $query->where('invoice_customer_id', $request->customer_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'LIKE', '%' . $request->invoice_number . '%');
        }

        // Get ALL matching invoices for summary (before pagination)
        $allInvoices = (clone $query)->get();

        // Calculate summary totals from ALL filtered invoices (not just current page)
        $summary = [
            'total_invoices'      => $allInvoices->count(),
            'total_final_amount'  => $allInvoices->sum('final_amount'),
            'total_advance_paid'  => $allInvoices->sum(function ($invoice) {
                return $invoice->advances->sum('advance_amount');
            }),
            'total_due_balance'   => $allInvoices->sum(function ($invoice) {
                // Calculate due balance as final_amount minus total advance paid
                $totalAdvancePaid = $invoice->advances->sum('advance_amount');
                return $invoice->final_amount - $totalAdvancePaid;
            }),
        ];

        // Get paginated invoices for the table
        $invoices = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Calculate due balance for each invoice in the current page
        foreach ($invoices as $invoice) {
            $totalAdvancePaid = $invoice->advances->sum('advance_amount');
            $invoice->due_balance = $invoice->final_amount - $totalAdvancePaid;
        }

        // Get all customers for filter dropdown
        $customers = InvoiceCustomer::orderBy('name')->get();

        return view('user.reports.invoices.index', compact('invoices', 'summary', 'customers'));
    }

    /**
     * Generate PDF report.
     */
    public function generatePdf(Request $request)
    {
        $query = Invoice::with(['customer', 'advances']);

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('customer_id')) {
            $query->where('invoice_customer_id', $request->customer_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'LIKE', '%' . $request->invoice_number . '%');
        }

        $invoices = $query->orderBy('date', 'desc')->get();

        // Calculate due balance for each invoice
        foreach ($invoices as $invoice) {
            $totalAdvancePaid = $invoice->advances->sum('advance_amount');
            $invoice->due_balance = $invoice->final_amount - $totalAdvancePaid;
        }

        $summary = [
            'total_invoices'      => $invoices->count(),
            'total_final_amount'  => $invoices->sum('final_amount'),
            'total_advance_paid'  => $invoices->sum(function ($invoice) {
                return $invoice->advances->sum('advance_amount');
            }),
            'total_due_balance'   => $invoices->sum(function ($invoice) {
                $totalAdvancePaid = $invoice->advances->sum('advance_amount');
                return $invoice->final_amount - $totalAdvancePaid;
            }),
        ];

        $pdf = PDF::loadView('user.reports.invoices.pdf', compact('invoices', 'summary'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'invoice_report_' . date('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate CSV report.
     */
    public function generateCsv(Request $request)
    {
        $query = Invoice::with(['customer', 'advances']);

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('customer_id')) {
            $query->where('invoice_customer_id', $request->customer_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'LIKE', '%' . $request->invoice_number . '%');
        }

        $invoices = $query->orderBy('date', 'desc')->get();

        $csvData = [];

        $csvData[] = [
            'Invoice Number',
            'Date',
            'Customer Name',
            'Item Name',
            'Rate (LKR)',
            'Quantity',
            'Discount (%)',
            'Amount (LKR)',
            'Final Amount (LKR)',
            'Advance Paid (LKR)',
            'Due Balance (LKR)',
            'Payment Status',
        ];

        foreach ($invoices as $invoice) {
            $advancePaid = $invoice->advances->sum('advance_amount');
            $dueBalance  = $invoice->final_amount - $advancePaid; // Calculate due balance
            $status      = $dueBalance > 0 ? 'Pending' : 'Paid';

            $csvData[] = [
                $invoice->invoice_number,
                $invoice->date->format('Y-m-d'),
                $invoice->customer ? $invoice->customer->name : 'N/A',
                $invoice->item_name,
                number_format($invoice->rate, 2),
                $invoice->qty,
                $invoice->item_discount . '%',
                number_format($invoice->amount, 2),
                number_format($invoice->final_amount, 2),
                number_format($advancePaid, 2),
                number_format($dueBalance, 2),
                $status,
            ];
        }

        $csvData[] = [];
        $csvData[] = ['SUMMARY'];
        $csvData[] = ['Total Invoices:', $invoices->count()];
        $csvData[] = ['Total Final Amount:', 'LKR ' . number_format($invoices->sum('final_amount'), 2)];
        
        $totalAdvancePaid = $invoices->sum(fn($i) => $i->advances->sum('advance_amount'));
        $totalDueBalance = $invoices->sum(fn($i) => $i->final_amount - $i->advances->sum('advance_amount'));
        
        $csvData[] = [
            'Total Advance Paid:',
            'LKR ' . number_format($totalAdvancePaid, 2),
        ];
        $csvData[] = [
            'Total Due Balance:',
            'LKR ' . number_format($totalDueBalance, 2),
        ];

        $filename = 'invoice_report_' . date('Y-m-d_His') . '.csv';
        $handle   = fopen('php://temp', 'w+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export as Excel (via CSV format).
     */
    public function exportExcel(Request $request)
    {
        return $this->generateCsv($request);
    }
}