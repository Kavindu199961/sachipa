<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $customer->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .summary table {
            width: 100%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div>Address Line 1, Address Line 2</div>
        <div>Phone: 123-456-7890 | Email: info@company.com</div>
    </div>

    <div class="invoice-title">INVOICE</div>

    <div class="customer-info">
        <strong>Bill To:</strong><br>
        {{ $customer->name ?? 'N/A' }}<br>
        {{ $customer->phone_number ?? '' }}<br>
        {{ $customer->email ?? '' }}<br>
        {{ $customer->location ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Rate</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Discount</th>
                <th>Final Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer->invoices as $invoice)
            <tr>
                <td>{{ $invoice->item_name }}</td>
                <td>{{ number_format($invoice->rate, 2) }}</td>
                <td>{{ $invoice->qty }}</td>
                <td>{{ number_format($invoice->amount, 2) }}</td>
                <td>{{ number_format($invoice->item_discount ?? 0, 2) }}</td>
                <td>{{ number_format($invoice->final_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <th>Total Final Amount:</th>
                <td>{{ number_format($customer->total_final_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Total Advance Paid:</th>
                <td>{{ number_format($customer->total_advance_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Due Amount:</th>
                <td>{{ number_format($customer->due_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($customer->advances->count() > 0)
    <div style="margin-top: 30px;">
        <strong>Advance Payment History:</strong>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Due Balance After</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer->advances as $advance)
                <tr>
                    <td>{{ $advance->date }}</td>
                    <td>{{ number_format($advance->advance_amount, 2) }}</td>
                    <td>{{ number_format($advance->due_balance, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer generated invoice.</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Invoice</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>