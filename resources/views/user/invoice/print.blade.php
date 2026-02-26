<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $customer->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #333;
        }
        .invoice-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .company-details {
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .company-details div {
            margin: 3px 0;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        .invoice-info span {
            margin: 0 15px;
        }
        .invoice-info i {
            color: #007bff;
            margin-right: 5px;
        }
        .customer-info {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .customer-info strong {
            color: #007bff;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            float: right;
            width: 350px;
            margin-top: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        .summary table {
            width: 100%;
            border: none;
            margin-bottom: 0;
        }
        .summary table td, .summary table th {
            border: none;
            padding: 8px 5px;
            background: transparent;
        }
        .summary table tr.total-row {
            border-top: 2px solid #007bff;
            font-weight: bold;
            font-size: 16px;
        }
        .summary table tr.discount-row {
            color: #28a745;
        }
        .summary table tr.due-row {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
        .conditions {
            margin-top: 30px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
            font-size: 13px;
        }
        .conditions h4 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        .conditions ul {
            margin: 0;
            padding-left: 20px;
        }
        .conditions li {
            margin: 5px 0;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
            th {
                background-color: #f0f0f0 !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Header with Shop Details --}}
        <div class="header">
            @php
                $shop = \App\Models\MyShopDetail::first();
                $firstInvoice = $customer->invoices->first();
            @endphp
            <div class="company-name">{{ $shop->shop_name ?? 'Your Company Name' }}</div>
            <div class="company-details">
                @if($shop && $shop->description)
                    <div>{{ $shop->description }}</div>
                @endif
                @if($shop && $shop->address)
                    <div>{{ $shop->address }}</div>
                @endif
                <div>
                    @if($shop && $shop->hotline)
                        <span>Phone: {{ $shop->hotline }}</span>
                    @endif
                    @if($shop && $shop->email)
                        <span> | Email: {{ $shop->email }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="invoice-title">
            INVOICE
        </div>
        
        {{-- Invoice Number and Date Information --}}
        <div class="invoice-info">
            <span><i class="fas fa-hashtag"></i> Invoice #: <strong>{{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
            <span><i class="fas fa-calendar-alt"></i> Date: <strong>{{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d F Y') : $customer->created_at->format('d F Y') }}</strong></span>
        </div>

        {{-- Customer Information --}}
        <div class="customer-info">
            <strong>BILL TO:</strong><br>
            {{ $customer->name ?? 'N/A' }}<br>
            @if($customer->phone_number) Phone: {{ $customer->phone_number }}<br> @endif
            @if($customer->email) Email: {{ $customer->email }}<br> @endif
            @if($customer->location) Address: {{ $customer->location }} @endif
        </div>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th class="text-right">Rate (LKR)</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Amount (LKR)</th>
                    <th class="text-right">Discount (%)</th>
                    <th class="text-right">Final Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer->invoices as $index => $invoice)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoice->item_name }}</td>
                    <td class="text-right">{{ number_format($invoice->rate, 2) }}</td>
                    <td class="text-right">{{ $invoice->qty }}</td>
                    <td class="text-right">{{ number_format($invoice->rate * $invoice->qty, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->item_discount ?? 0, 2) }}%</td>
                    <td class="text-right">{{ number_format($invoice->final_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="6" class="text-right">Total:</td>
                    <td class="text-right">LKR {{ number_format($customer->invoices->sum('final_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Summary with Final Discount --}}
        <div class="summary">
            <table>
                <tr>
                    <th>Sub Total:</th>
                    <td class="text-right">LKR {{ number_format($customer->invoices->sum('amount'), 2) }}</td>
                </tr>
                @if($customer->total_amount != $customer->invoices->sum('amount'))
                <tr>
                    <th>Total Amount (Adjusted):</th>
                    <td class="text-right">LKR {{ number_format($customer->total_amount, 2) }}</td>
                </tr>
                @endif
                @if($customer->final_discount > 0)
                <tr class="discount-row">
                    <th>Final Discount ({{ number_format($customer->final_discount, 2) }}%):</th>
                    <td class="text-right">- LKR {{ number_format($customer->total_amount * ($customer->final_discount / 100), 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <th>Final Amount:</th>
                    <td class="text-right">LKR {{ number_format($customer->final_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Advance Paid:</th>
                    <td class="text-right">LKR {{ number_format($customer->total_advance_amount, 2) }}</td>
                </tr>
                <tr class="due-row">
                    <th>Due Amount:</th>
                    <td class="text-right">LKR {{ number_format($customer->due_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right" style="padding-top: 10px;">
                        @php
                            $statusClass = $customer->due_amount <= 0 ? 'success' : ($customer->due_amount < $customer->final_amount / 2 ? 'warning' : 'danger');
                            $statusText = $customer->due_amount <= 0 ? 'PAID' : ($customer->due_amount < $customer->final_amount / 2 ? 'PARTIAL' : 'DUE');
                        @endphp
                        <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        {{-- Advance Payment History --}}
        @if($customer->advances->count() > 0)
        <div style="margin-top: 40px;">
            <h4 style="color: #007bff; margin-bottom: 15px;">Advance Payment History</h4>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-right">Amount (LKR)</th>
                        <th class="text-right">Due Balance After (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->advances as $advance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($advance->date)->format('d M Y') }}</td>
                        <td class="text-right">{{ number_format($advance->advance_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($advance->due_balance, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Invoice Metadata --}}
        <div style="margin-top: 20px; font-size: 12px; color: #666; padding: 10px; background: #f9f9f9; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between;">
                <div><strong>Invoice Number:</strong> {{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Invoice Date:</strong> {{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : $customer->created_at->format('d M Y') }}</div>
                <div><strong>Created:</strong> {{ $customer->created_at->format('d M Y h:i A') }}</div>
            </div>
        </div>

        {{-- Terms and Conditions --}}
        @if($shop && ($shop->condition_1 || $shop->condition_2 || $shop->condition_3))
        <div class="conditions">
            <h4>Terms & Conditions</h4>
            <ul>
                @if($shop->condition_1)
                    <li>{{ $shop->condition_1 }}</li>
                @endif
                @if($shop->condition_2)
                    <li>{{ $shop->condition_2 }}</li>
                @endif
                @if($shop->condition_3)
                    <li>{{ $shop->condition_3 }}</li>
                @endif
            </ul>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer generated invoice - valid without signature.</p>
            <p>Invoice #: {{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }} | Date: {{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : $customer->created_at->format('d M Y') }}</p>
        </div>

        {{-- Print Controls --}}
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px; font-size: 16px;">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <button onclick="window.close()" style="padding: 10px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto print when page loads
            window.print();
        }
    </script>
</body>
</html>