<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sachipa Curtain - Invoice Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #2c2c2c;
            background: #fff;
            padding: 25px 30px;
        }

        /* ── HEADER ── */
        .header-wrap {
            border-bottom: 3px solid #8B1A2B;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header-top {
            display: table;
            width: 100%;
        }
        .header-logo-col {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .header-meta-col {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .company-name {
            font-size: 26px;
            font-weight: 700;
            color: #8B1A2B;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .company-tagline {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 3px;
        }
        .report-meta {
            font-size: 9px;
            color: #666;
            line-height: 1.6;
        }
        .report-meta span {
            display: inline-block;
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 1px 6px;
            margin-top: 2px;
            font-weight: 600;
            color: #444;
        }

        /* ── DATE RANGE BADGE ── */
        .date-range-bar {
            background: linear-gradient(135deg, #8B1A2B 0%, #c0392b 100%);
            color: #fff;
            padding: 7px 14px;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 10px;
            display: table;
            width: 100%;
        }
        .date-range-bar .left  { display: table-cell; vertical-align: middle; }
        .date-range-bar .right { display: table-cell; text-align: right; vertical-align: middle; }
        .date-range-bar strong { font-size: 11px; }

        /* ── SUMMARY CARDS ── */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 18px;
        }
        .summary-table td {
            width: 25%;
            padding: 12px 10px;
            border-radius: 5px;
            text-align: center;
            vertical-align: middle;
        }
        .card-total-inv  { background: #EAF0FB; border: 1px solid #C5D5F0; }
        .card-final-amt  { background: #E8F5E9; border: 1px solid #A5D6A7; }
        .card-advance    { background: #FFF8E1; border: 1px solid #FFE082; }
        .card-due        { background: #FDECEA; border: 1px solid #EF9A9A; }
        .card-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 16px;
            font-weight: 700;
            color: #222;
        }
        .card-value.blue  { color: #1565C0; }
        .card-value.green { color: #2E7D32; }
        .card-value.amber { color: #E65100; }
        .card-value.red   { color: #C62828; }
        .card-sub {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }

        /* ── TABLE ── */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #8B1A2B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-left: 4px;
            border-left: 3px solid #8B1A2B;
            padding-left: 7px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.data-table thead tr {
            background: #8B1A2B;
            color: #fff;
        }
        table.data-table thead th {
            padding: 8px 7px;
            font-size: 9.5px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.3px;
            border: none;
        }
        table.data-table thead th.left { text-align: left; }
        table.data-table tbody tr:nth-child(even) { background: #fafafa; }
        table.data-table tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data-table tbody tr:hover           { background: #fff3f3; }
        table.data-table tbody td {
            padding: 7px 7px;
            font-size: 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        table.data-table tbody td.right { text-align: right; }
        table.data-table tbody td.center { text-align: center; }

        /* ── TFOOT ── */
        table.data-table tfoot tr {
            background: #2c2c2c;
            color: #fff;
        }
        table.data-table tfoot td {
            padding: 8px 7px;
            font-size: 10px;
            font-weight: 700;
            border: none;
        }
        table.data-table tfoot td.right { text-align: right; }

        /* ── STATUS BADGES ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-paid    { background: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7; }
        .badge-partial { background: #FFF8E1; color: #E65100; border: 1px solid #FFE082; }
        .badge-due     { background: #FDECEA; color: #C62828; border: 1px solid #EF9A9A; }

        /* Row highlight by status */
        .row-paid    td { border-left: 3px solid #4CAF50; }
        .row-partial td { border-left: 3px solid #FF9800; }
        .row-due     td { border-left: 3px solid #F44336; }

        /* ── FOOTER ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 30px;
            border-top: 2px solid #8B1A2B;
            background: #fff;
            display: table;
            width: 100%;
        }
        .footer .left  { display: table-cell; font-size: 8.5px; color: #888; vertical-align: middle; }
        .footer .right { display: table-cell; text-align: right; font-size: 8.5px; color: #888; vertical-align: middle; }
        .footer strong { color: #8B1A2B; }

        /* ── NO DATA ── */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 12px;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════
         HEADER
    ═══════════════════════════════ --}}
    <div class="header-wrap">
        <div class="header-top">
            <div class="header-logo-col">
                <div class="company-name">Sachipa Curtain</div>
                <div class="company-tagline">Premium Curtain &amp; Interior Solutions</div>
            </div>
            <div class="header-meta-col">
                <div class="report-title">Invoice Report</div>
                <div class="report-meta">
                    Generated: {{ now()->format('d M Y, h:i A') }}<br>
                    @if(request('from_date') || request('to_date'))
                        <span>
                            {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All' }}
                            &nbsp;→&nbsp;
                            {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Now' }}
                        </span>
                    @else
                        <span>All Dates</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════
         DATE RANGE BAR
    ═══════════════════════════════ --}}
    <div class="date-range-bar">
        <div class="left">
            <strong>Report Period:</strong>&nbsp;
            @if(request('from_date') && request('to_date'))
                {{ \Carbon\Carbon::parse(request('from_date'))->format('d F Y') }}
                &nbsp;—&nbsp;
                {{ \Carbon\Carbon::parse(request('to_date'))->format('d F Y') }}
            @elseif(request('from_date'))
                From {{ \Carbon\Carbon::parse(request('from_date'))->format('d F Y') }} onwards
            @elseif(request('to_date'))
                Up to {{ \Carbon\Carbon::parse(request('to_date'))->format('d F Y') }}
            @else
                All Time — Complete History
            @endif
        </div>
        <div class="right">
            Total Records: <strong>{{ $summary['total_invoices'] }}</strong>
        </div>
    </div>

    {{-- ═══════════════════════════════
         SUMMARY CARDS
    ═══════════════════════════════ --}}
    <table class="summary-table">
        <tr>
            <td class="card-total-inv">
                <div class="card-label">Total Customers</div>
                <div class="card-value blue">{{ $summary['total_invoices'] }}</div>
                <div class="card-sub">invoiced accounts</div>
            </td>
            <td class="card-final-amt">
                <div class="card-label">Total Final Amount</div>
                <div class="card-value green">LKR {{ number_format($summary['total_final_amount'], 2) }}</div>
                <div class="card-sub">gross revenue</div>
            </td>
            <td class="card-advance">
                <div class="card-label">Total Advance Paid</div>
                <div class="card-value amber">LKR {{ number_format($summary['total_advance_paid'], 2) }}</div>
                <div class="card-sub">payments received</div>
            </td>
            <td class="card-due">
                <div class="card-label">Total Due Balance</div>
                <div class="card-value red">LKR {{ number_format($summary['total_due_balance'], 2) }}</div>
                <div class="card-sub">outstanding amount</div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════
         DATA TABLE
    ═══════════════════════════════ --}}
    <div class="section-title">Customer Invoice Details</div>

    @if($invoices->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th class="left" style="width:18%">Customer Name</th>
                <th class="left" style="width:14%">Email</th>
                <th style="width:11%">Phone</th>
                <th style="width:12%">Location</th>
                <th style="width:13%">Final Amount (LKR)</th>
                <th style="width:13%">Advance Paid (LKR)</th>
                <th style="width:11%">Due Balance (LKR)</th>
                <th style="width:8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $customer)
                @php
                    $due         = $customer->due_amount ?? 0;
                    $finalAmt    = $customer->total_final_amount ?? 0;
                    $advancePaid = $customer->total_advance_amount ?? 0;

                    if ($due <= 0) {
                        $statusClass = 'badge-paid';
                        $statusText  = 'Paid';
                        $rowClass    = 'row-paid';
                    } elseif ($advancePaid > 0) {
                        $statusClass = 'badge-partial';
                        $statusText  = 'Partial';
                        $rowClass    = 'row-partial';
                    } else {
                        $statusClass = 'badge-due';
                        $statusText  = 'Due';
                        $rowClass    = 'row-due';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $customer->name ?? 'N/A' }}</strong></td>
                    <td>{{ $customer->email ?? '—' }}</td>
                    <td class="center">{{ $customer->phone_number ?? '—' }}</td>
                    <td class="center">{{ Str::limit($customer->location ?? '—', 15) }}</td>
                    <td class="right"><strong>{{ number_format($finalAmt, 2) }}</strong></td>
                    <td class="right">{{ number_format($advancePaid, 2) }}</td>
                    <td class="right">
                        <strong>{{ number_format(max($due, 0), 2) }}</strong>
                    </td>
                    <td class="center">
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right; letter-spacing:0.5px; text-transform:uppercase; font-size:9px;">
                    Grand Totals
                </td>
                <td class="right">LKR {{ number_format($summary['total_final_amount'], 2) }}</td>
                <td class="right">LKR {{ number_format($summary['total_advance_paid'], 2) }}</td>
                <td class="right">LKR {{ number_format($summary['total_due_balance'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="no-data">No invoice records found for the selected criteria.</div>
    @endif

    {{-- ═══════════════════════════════
         FOOTER
    ═══════════════════════════════ --}}
    <div class="footer">
        <div class="left">
            <strong>Sachipa Curtain</strong> &nbsp;|&nbsp; System Generated Report &nbsp;|&nbsp; Confidential
        </div>
        <div class="right">
            Printed on {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

</body>
</html>