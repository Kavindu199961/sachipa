<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $customer->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 8px;
            background: #fff;
            color: #333;
            font-size: 10px;
        }
        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 10px 14px;
            overflow: hidden;
        }
        .header {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #007bff;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 2px;
        }
        .company-details {
            color: #666;
            line-height: 1.4;
            font-size: 9px;
        }
        .company-details div { margin: 1px 0; }
        .invoice-title {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 4px 0 2px 0;
        }
        .invoice-info {
            text-align: center;
            margin-bottom: 6px;
            font-size: 9px;
            color: #666;
        }
        .invoice-info span { margin: 0 10px; }
        .customer-info {
            margin-bottom: 6px;
            padding: 5px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            font-size: 9px;
            line-height: 1.5;
        }
        .customer-info strong {
            color: #007bff;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 5px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        tfoot td {
            background-color: #f2f2f2 !important;
            font-weight: bold;
        }

        /* Summary layout: items table full width, then summary below */
        .summary-section {
            width: 100%;
            margin-top: 4px;
            display: flex;
            justify-content: flex-end;
        }
        .summary {
            width: 320px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .summary table {
            width: 100%;
            border: none;
            margin-bottom: 0;
            font-size: 9px;
        }
        .summary table td,
        .summary table th {
            border: none;
            padding: 3px 4px;
            background: transparent;
        }
        .summary table tr.total-row {
            border-top: 1.5px solid #007bff;
            font-weight: bold;
            font-size: 10px;
        }
        .summary table tr.discount-row { color: #28a745; }
        .summary table tr.due-row {
            color: #dc3545;
            font-weight: bold;
        }

        /* Advance payments table */
        .advance-section { margin-top: 8px; }
        .advance-section h4 {
            color: #007bff;
            margin: 0 0 4px 0;
            font-size: 10px;
        }

        /* Invoice meta */
        .invoice-meta {
            margin-top: 6px;
            font-size: 8px;
            color: #666;
            padding: 5px 8px;
            background: #f9f9f9;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }

        .conditions {
            margin-top: 6px;
            padding: 6px 10px;
            background: #f9f9f9;
            border-left: 3px solid #007bff;
            font-size: 8px;
        }
        .conditions h4 {
            margin: 0 0 4px 0;
            color: #007bff;
            font-size: 9px;
        }
        .conditions ul {
            margin: 0;
            padding-left: 14px;
        }
        .conditions li {
            margin: 2px 0;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger  { background: #dc3545; color: white; }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }
        .footer p { margin: 2px 0; }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body { padding: 0; margin: 0; }
            .invoice-container {
                box-shadow: none;
                padding: 10px 14px;
            }
            .no-print { display: none !important; }
            th {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">

        @php
            $shop = \App\Models\MyShopDetail::first();
            $firstInvoice = $customer->invoices->first();

            /* ── Detect which optional columns have any non-zero value ── */
            $hasRate     = $customer->invoices->sum('rate') != 0;
            $hasQty      = $customer->invoices->sum('qty')  != 0;
            $hasDiscount = $customer->invoices->contains(fn($i) => ($i->item_discount ?? 0) != 0);
        @endphp

        {{-- Header --}}
        <div class="header">
            <div class="company-name">{{ $shop->shop_name ?? 'Your Company Name' }}</div>
            <div class="company-details">
                @if($shop && $shop->description)<div>{{ $shop->description }}</div>@endif
                @if($shop && $shop->address)<div>{{ $shop->address }}</div>@endif
                <div>
                    @if($shop && $shop->hotline)Phone: {{ $shop->hotline }}@endif
                    @if($shop && $shop->email) | Email: {{ $shop->email }}@endif
                </div>
            </div>
        </div>

        <div class="invoice-title">INVOICE</div>

        <div class="invoice-info">
            <span>Invoice #: <strong>{{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
            <span>Date: <strong>{{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d F Y') : $customer->created_at->format('d F Y') }}</strong></span>
        </div>

        {{-- Customer Info --}}
        <div class="customer-info">
            <strong>BILL TO:</strong>&nbsp;
            {{ $customer->name ?? 'N/A' }}
            @if($customer->phone_number) &nbsp;|&nbsp; Phone: {{ $customer->phone_number }} @endif
            @if($customer->email)        &nbsp;|&nbsp; Email: {{ $customer->email }}        @endif
            @if($customer->location)     &nbsp;|&nbsp; Address: {{ $customer->location }}   @endif
        </div>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    @if($hasRate)     <th class="text-right">Rate (LKR)</th>     @endif
                    @if($hasQty)      <th class="text-right">Qty</th>            @endif
                    @if($hasRate && $hasQty) <th class="text-right">Amount (LKR)</th> @endif
                    @if($hasDiscount) <th class="text-right">Discount (%)</th>   @endif
                    <th class="text-right">Final Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer->invoices as $index => $invoice)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoice->item_name }}</td>
                    @if($hasRate)     <td class="text-right">{{ number_format($invoice->rate, 2) }}</td>                        @endif
                    @if($hasQty)      <td class="text-right">{{ $invoice->qty }}</td>                                           @endif
                    @if($hasRate && $hasQty) <td class="text-right">{{ number_format($invoice->rate * $invoice->qty, 2) }}</td> @endif
                    @if($hasDiscount) <td class="text-right">{{ number_format($invoice->item_discount ?? 0, 2) }}%</td>         @endif
                    <td class="text-right">{{ number_format($invoice->final_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ 2 + ($hasRate?1:0) + ($hasQty?1:0) + ($hasRate&&$hasQty?1:0) + ($hasDiscount?1:0) }}"
                        class="text-right">Total:</td>
                    <td class="text-right">LKR {{ number_format($customer->invoices->sum('final_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Summary below table, right-aligned --}}
        <div class="summary-section">
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
                        <td colspan="2" class="text-right" style="padding-top:4px;">
                            @php
                                $statusClass = $customer->due_amount <= 0 ? 'success' : ($customer->due_amount < $customer->final_amount / 2 ? 'warning' : 'danger');
                                $statusText  = $customer->due_amount <= 0 ? 'PAID'    : ($customer->due_amount < $customer->final_amount / 2 ? 'PARTIAL' : 'DUE');
                            @endphp
                            <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Advance Payment History --}}
        @if($customer->advances->count() > 0)
        <div class="advance-section">
            <h4>Advance Payment History</h4>
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

        {{-- Invoice Meta --}}
        <div class="invoice-meta">
            <div><strong>Invoice #:</strong> {{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div><strong>Invoice Date:</strong> {{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : $customer->created_at->format('d M Y') }}</div>
            <div><strong>Created:</strong> {{ $customer->created_at->format('d M Y h:i A') }}</div>
        </div>

        {{-- Terms & Conditions --}}
        @if($shop && ($shop->condition_1 || $shop->condition_2 || $shop->condition_3))
        <div class="conditions">
            <h4>Terms &amp; Conditions</h4>
            <ul>
                @if($shop->condition_1)<li>{{ $shop->condition_1 }}</li>@endif
                @if($shop->condition_2)<li>{{ $shop->condition_2 }}</li>@endif
                @if($shop->condition_3)<li>{{ $shop->condition_3 }}</li>@endif
            </ul>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer generated invoice – valid without signature.</p>
            <p>Invoice #: {{ $firstInvoice->invoice_number ?? str_pad($customer->id, 6, '0', STR_PAD_LEFT) }} | Date: {{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : $customer->created_at->format('d M Y') }}</p>
        </div>

        {{-- Print Controls --}}
        <div class="no-print" style="text-align:center; margin-top:12px;">
            <button onclick="window.print()" style="padding:8px 24px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; margin-right:8px; font-size:13px;">
                🖨️ Print Invoice
            </button>
            <button onclick="window.location.href='{{ route('invoices.index') }}'" style="padding:8px 24px; background:#6c757d; color:white; border:none; border-radius:5px; cursor:pointer; font-size:13px;">
              ✕ Close
             </button>
        </div>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>