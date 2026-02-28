@extends('layouts.app')

@section('title', 'Invoice Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice Report</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('reports.invoices.pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('reports.invoices.csv', request()->all()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-csv"></i> CSV
                            </a>
                            <a href="{{ route('reports.invoices.excel', request()->all()) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-excel"></i> Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('reports.invoices.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control"
                                           value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control"
                                           value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer_id" class="form-control select2">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Invoice Number</label>
                                    <input type="text" name="invoice_number" class="form-control"
                                           placeholder="Search..." value="{{ request('invoice_number') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('reports.invoices.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Summary Cards (totals across ALL filtered results, not just this page) --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $summary['total_invoices'] }}</h3>
                                    <p>Total Invoices</p>
                                </div>
                                <div class="icon"><i class="fas fa-file-invoice"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>LKR {{ number_format($summary['total_final_amount'], 2) }}</h3>
                                    <p>Total Final Amount</p>
                                </div>
                                <div class="icon"><i class="fas fa-money-bill"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>LKR {{ number_format($summary['total_advance_paid'], 2) }}</h3>
                                    <p>Total Advance Paid</p>
                                </div>
                                <div class="icon"><i class="fas fa-credit-card"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>LKR {{ number_format($summary['total_due_balance'], 2) }}</h3>
                                    <p>Total Due Balance</p>
                                </div>
                                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                            </div>
                        </div>
                    </div>

                    {{-- Invoices Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Item Name</th>
                                    <th class="text-right">Rate</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Final Amount</th>
                                    <th class="text-right">Advance Paid</th>
                                    <th class="text-right">Due Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $index => $invoice)
                                    @php
                                        $advancePaid = $invoice->advances->sum('advance_amount');
                                        $dueBalance = $invoice->final_amount - $advancePaid; // Calculate due balance
                                        $hasAdvances = $invoice->advances->count() > 0;

                                        /*
                                         * Status logic:
                                         *  - No advances recorded  → Unpaid  (full amount still owed)
                                         *  - Has advances, due > 0 → Pending (partial payment)
                                         *  - Has advances, due = 0 → Paid    (fully settled)
                                         */
                                        if (! $hasAdvances) {
                                            $status      = 'Unpaid';
                                            $statusClass = 'danger';
                                        } elseif ($dueBalance > 0) {
                                            $status      = 'Pending';
                                            $statusClass = 'warning';
                                        } else {
                                            $status      = 'Paid';
                                            $statusClass = 'success';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $invoices->firstItem() + $index }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->date ? $invoice->date->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                                        <td>{{ $invoice->item_name }}</td>
                                        <td class="text-right">{{ number_format($invoice->rate, 2) }}</td>
                                        <td class="text-right">{{ $invoice->qty }}</td>
                                        <td class="text-right">{{ $invoice->item_discount }}%</td>
                                        <td class="text-right">{{ number_format($invoice->amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($invoice->final_amount, 2) }}</td>

                                        {{-- Advance Paid --}}
                                        <td class="text-right">
                                            @if($hasAdvances)
                                                <span class="text-success">{{ number_format($advancePaid, 2) }}</span>
                                            @else
                                                <span class="text-muted">0.00</span>
                                            @endif
                                        </td>

                                        {{-- Due Balance --}}
                                        <td class="text-right">
                                            @if(! $hasAdvances)
                                                {{-- No payment recorded — full amount is due --}}
                                                <span class="text-danger">
                                                    {{ number_format($invoice->final_amount, 2) }}
                                                </span>
                                            @elseif($dueBalance > 0)
                                                <span class="text-danger">{{ number_format($dueBalance, 2) }}</span>
                                            @else
                                                <span class="text-success">0.00</span>
                                            @endif
                                        </td>

                                        {{-- Status Badge --}}
                                        <td>
                                            <span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center">No invoices found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @php
                                    // Page-level totals
                                    $pageFinal = $invoices->sum('final_amount');
                                    $pageAdvance = $invoices->sum(fn($i) => $i->advances->sum('advance_amount'));
                                    $pageDue = $invoices->sum(fn($i) => $i->final_amount - $i->advances->sum('advance_amount'));
                                @endphp
                                <tr class="table-active font-weight-bold">
                                    <th colspan="9" class="text-right">Totals (This Page):</th>
                                    <th class="text-right">{{ number_format($pageFinal, 2) }}</th>
                                    <th class="text-right">{{ number_format($pageAdvance, 2) }}</th>
                                    <th class="text-right">{{ number_format($pageDue, 2) }}</th>
                                    <th></th>
                                </tr>
                                <tr class="table-info font-weight-bold">
                                    <th colspan="9" class="text-right">Totals (All Filtered):</th>
                                    <th class="text-right">{{ number_format($summary['total_final_amount'], 2) }}</th>
                                    <th class="text-right">{{ number_format($summary['total_advance_paid'], 2) }}</th>
                                    <th class="text-right">{{ number_format($summary['total_due_balance'], 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="row">
                        <div class="col-md-12">
                            {{ $invoices->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
    }
    .small-box > .inner { padding: 10px; }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px;
        white-space: nowrap;
    }
    .small-box p { font-size: 1rem; }
    .small-box .icon {
        color: rgba(0,0,0,.15);
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: all .3s linear;
    }
    .badge { padding: 5px 10px; font-size: .75rem; font-weight: 600; }
    .text-success { color: #28a745 !important; font-weight: bold; }
    .text-danger  { color: #dc3545 !important; font-weight: bold; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ theme: 'classic', placeholder: 'Select a customer' });
    });
</script>
@endpush