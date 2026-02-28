<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header h3 {
            margin: 5px 0;
            color: #666;
            font-weight: normal;
        }
        .summary-cards {
            margin-bottom: 30px;
            width: 100%;
        }
        .summary-card {
            width: 23%;
            float: left;
            margin: 0 1%;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-align: center;
        }
        .summary-card h4 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #343a40;
            color: white;
            padding: 10px;
            font-size: 11px;
            text-align: center;
        }
        td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            color: #666;
            font-size: 10px;
            padding: 10px 0;
            border-top: 1px solid #dee2e6;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #dc3545;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice Report</h1>
        <h3>Generated on: {{ now()->format('d M Y H:i:s') }}</h3>
        @if(request('from_date') && request('to_date'))
            <h3>Period: {{ request('from_date') }} to {{ request('to_date') }}</h3>
        @endif
    </div>

    <div class="summary-cards clearfix">
        <div class="summary-card">
            <h4>Total Invoices</h4>
            <div class="value">{{ $summary['total_invoices'] }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Final Amount</h4>
            <div class="value">LKR {{ number_format($summary['total_final_amount'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Advance Paid</h4>
            <div class="value">LKR {{ number_format($summary['total_advance_paid'], 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Due Balance</h4>
            <div class="value">LKR {{ number_format($summary['total_due_balance'], 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Rate</th>
                <th>Qty</th>
                <th>Disc%</th>
                <th>Amount</th>
                <th>Final Amt</th>
                <th>Advance</th>
                <th>Due</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                @php
                    $advancePaid = $invoice->advances->sum('advance_amount');
                    $dueBalance = $invoice->advances->sum('due_balance');
                    $status = $dueBalance > 0 ? 'Pending' : 'Paid';
                @endphp
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->date->format('d M Y') }}</td>
                    <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                    <td>{{ $invoice->item_name }}</td>
                    <td class="text-right">{{ number_format($invoice->rate, 2) }}</td>
                    <td class="text-right">{{ $invoice->qty }}</td>
                    <td class="text-right">{{ $invoice->item_discount }}%</td>
                    <td class="text-right">{{ number_format($invoice->amount, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->final_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($advancePaid, 2) }}</td>
                    <td class="text-right">{{ number_format($dueBalance, 2) }}</td>
                    <td class="text-center {{ $status == 'Paid' ? 'status-paid' : 'status-pending' }}">
                        {{ $status }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Totals:</th>
                <th class="text-right">{{ number_format($invoices->sum('final_amount'), 2) }}</th>
                <th class="text-right">{{ number_format($invoices->sum(function($i) { return $i->advances->sum('advance_amount'); }), 2) }}</th>
                <th class="text-right">{{ number_format($invoices->sum(function($i) { return $i->advances->sum('due_balance'); }), 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a system-generated report. For any queries, please contact the administrator.</p>
    </div>
</body>
</html>