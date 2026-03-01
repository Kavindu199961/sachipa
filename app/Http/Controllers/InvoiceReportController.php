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
     * Build base query with filters applied.
     * Matches the same advance logic as InvoiceController.
     */
    private function buildQuery(Request $request)
    {
        // Query customers (same as InvoiceController) instead of invoices
        $query = InvoiceCustomer::with(['invoices', 'advances']);

        if ($request->filled('from_date')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            });
        }

        if ($request->filled('to_date')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            });
        }

        if ($request->filled('customer_id')) {
            $query->where('id', $request->customer_id);
        }

        if ($request->filled('invoice_number')) {
            $query->whereHas('invoices', function ($q) use ($request) {
                $q->where('invoice_number', 'LIKE', '%' . $request->invoice_number . '%');
            });
        }

        return $query;
    }

    /**
     * Attach computed totals to each customer — mirrors InvoiceController logic exactly.
     */
    private function attachTotals($customers)
    {
        foreach ($customers as $customer) {
            $customer->total_final_amount  = (float) ($customer->final_amount ?? 0);
            $customer->total_advance_amount = (float) $customer->advances->sum('advance_amount');
            $customer->due_amount          = $customer->total_final_amount - $customer->total_advance_amount;
        }
        return $customers;
    }

    /**
     * Build summary array from a collection that already has totals attached.
     */
    private function buildSummary($customers)
    {
        return [
            'total_invoices'     => $customers->count(),
            'total_final_amount' => $customers->sum('total_final_amount'),
            'total_advance_paid' => $customers->sum('total_advance_amount'),
            'total_due_balance'  => $customers->sum('due_amount'),
        ];
    }

    /**
     * Display the invoice report index page.
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        // Get ALL matching customers for summary totals (before pagination)
        $allCustomers = (clone $query)->get();
        $this->attachTotals($allCustomers);
        $summary = $this->buildSummary($allCustomers);

        // Paginated customers for the table
        $invoices = $query->latest()->paginate(15)->withQueryString();
        $this->attachTotals($invoices);

        $customers = InvoiceCustomer::orderBy('name')->get();

        return view('user.reports.invoices.index', compact('invoices', 'summary', 'customers'));
    }

    /**
     * Generate PDF report.
     */
    public function generatePdf(Request $request)
    {
        $customers = $this->buildQuery($request)->latest()->get();
        $this->attachTotals($customers);
        $summary = $this->buildSummary($customers);

        // Keep variable name as $invoices for view compatibility
        $invoices = $customers;

        $pdf = PDF::loadView('user.reports.invoices.pdf', compact('invoices', 'summary'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('invoice_report_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Generate CSV report.
     */
    public function generateCsv(Request $request)
    {
        $customers = $this->buildQuery($request)->latest()->get();
        $this->attachTotals($customers);

        $csvData   = [];
        $csvData[] = [
            'Customer Name',
            'Email',
            'Phone',
            'Location',
            'Final Amount (LKR)',
            'Advance Paid (LKR)',
            'Due Balance (LKR)',
            'Payment Status',
        ];

        foreach ($customers as $customer) {
            $status    = $customer->due_amount <= 0 ? 'Paid' : 'Pending';
            $csvData[] = [
                $customer->name                                  ?? 'N/A',
                $customer->email                                 ?? 'N/A',
                $customer->phone_number                          ?? 'N/A',
                $customer->location                              ?? 'N/A',
                number_format($customer->total_final_amount, 2),
                number_format($customer->total_advance_amount, 2),
                number_format($customer->due_amount, 2),
                $status,
            ];
        }

        // Summary section
        $summary   = $this->buildSummary($customers);
        $csvData[] = [];
        $csvData[] = ['SUMMARY'];
        $csvData[] = ['Total Customers:',     $summary['total_invoices']];
        $csvData[] = ['Total Final Amount:',  'LKR ' . number_format($summary['total_final_amount'], 2)];
        $csvData[] = ['Total Advance Paid:',  'LKR ' . number_format($summary['total_advance_paid'], 2)];
        $csvData[] = ['Total Due Balance:',   'LKR ' . number_format($summary['total_due_balance'], 2)];

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