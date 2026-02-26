@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Invoice Details</h3>
                        @php
                            $firstInvoice = $customer->invoices->first();
                        @endphp
                        @if($firstInvoice)
                        <div class="mt-1">
                            <span class="badge bg-info">Invoice #: {{ $firstInvoice->invoice_number ?? 'N/A' }}</span>
                            <span class="badge bg-secondary ms-2">Date: {{ $firstInvoice->date ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : 'N/A' }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('invoices.print', $customer->id) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Invoice Header with Number and Date --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Invoice Number:</strong> 
                                        <span class="ms-2">{{ $firstInvoice->invoice_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Invoice Date:</strong> 
                                        <span class="ms-2">{{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d F Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Information and Payment Summary --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th width="120">Name:</th>
                                            <td><strong>{{ $customer->name ?? 'N/A' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $customer->phone_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $customer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location:</th>
                                            <td>{{ $customer->location ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th width="160">Sub Total (Items):</th>
                                            <td class="text-end">LKR {{ number_format($customer->invoices->sum('amount'), 2) }}</td>
                                        </tr>
                                        @if($customer->total_amount != $customer->invoices->sum('amount'))
                                        <tr>
                                            <th>Total Amount (Adjusted):</th>
                                            <td class="text-end">LKR {{ number_format($customer->total_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($customer->final_discount > 0)
                                        <tr class="text-success">
                                            <th>Final Discount ({{ number_format($customer->final_discount, 2) }}%):</th>
                                            <td class="text-end">- LKR {{ number_format($customer->total_amount * ($customer->final_discount / 100), 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="table-active">
                                            <th><strong>Final Amount:</strong></th>
                                            <td class="text-end"><strong>LKR {{ number_format($customer->final_amount, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Advance Paid:</th>
                                            <td class="text-end">LKR {{ number_format($customer->total_advance_amount, 2) }}</td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <th>Due Amount:</th>
                                            <td class="text-end {{ $customer->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                <strong>LKR {{ number_format($customer->due_amount, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-end pt-2">
                                                @php
                                                    $statusClass = $customer->due_amount <= 0 ? 'success' : ($customer->due_amount < $customer->final_amount / 2 ? 'warning' : 'danger');
                                                    $statusText = $customer->due_amount <= 0 ? 'PAID' : ($customer->due_amount < $customer->final_amount / 2 ? 'PARTIAL' : 'DUE');
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }} fs-6">{{ $statusText }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Invoice Items --}}
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box"></i> Invoice Items</h5>
                            @if($firstInvoice)
                            <small>Invoice: {{ $firstInvoice->invoice_number }}</small>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th class="text-end">Rate (LKR)</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Amount (LKR)</th>
                                            <th class="text-end">Item Discount (%)</th>
                                            <th class="text-end">Final Amount (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customer->invoices as $index => $invoice)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $invoice->item_name }}</td>
                                            <td class="text-end">{{ number_format($invoice->rate, 2) }}</td>
                                            <td class="text-end">{{ $invoice->qty }}</td>
                                            <td class="text-end">{{ number_format($invoice->rate * $invoice->qty, 2) }}</td>
                                            <td class="text-end">{{ number_format($invoice->item_discount ?? 0, 2) }}%</td>
                                            <td class="text-end"><strong>{{ number_format($invoice->final_amount, 2) }}</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="6" class="text-end">Items Total:</th>
                                            <th class="text-end">LKR {{ number_format($customer->invoices->sum('final_amount'), 2) }}</th>
                                        </tr>
                                        @if($customer->final_discount > 0)
                                        <tr>
                                            <th colspan="6" class="text-end text-success">Final Discount Applied:</th>
                                            <th class="text-end text-success">{{ number_format($customer->final_discount, 2) }}%</th>
                                        </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <th colspan="6" class="text-end">Final Total:</th>
                                            <th class="text-end">LKR {{ number_format($customer->final_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Advance Payments --}}
                    @if($customer->advances->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Advance Payment History</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th class="text-end">Advance Amount (LKR)</th>
                                            <th class="text-end">Due Balance After (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customer->advances as $index => $advance)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($advance->date)->format('d M Y') }}</td>
                                            <td class="text-end">{{ number_format($advance->advance_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($advance->due_balance, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="2" class="text-end">Total Advances:</th>
                                            <th class="text-end">LKR {{ number_format($customer->advances->sum('advance_amount'), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Invoice Summary Card --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Invoice Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded bg-light">
                                                <div class="text-muted small">Sub Total</div>
                                                <div class="h4">LKR {{ number_format($customer->invoices->sum('amount'), 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded bg-light">
                                                <div class="text-muted small">Final Discount</div>
                                                <div class="h4">{{ number_format($customer->final_discount, 2) }}%</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded bg-success text-white">
                                                <div class="small">Final Amount</div>
                                                <div class="h4">LKR {{ number_format($customer->final_amount, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded {{ $customer->due_amount > 0 ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                                <div class="small">Due Amount</div>
                                                <div class="h4">LKR {{ number_format($customer->due_amount, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Invoice Details Footer --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fas fa-hashtag"></i> <strong>Invoice #:</strong> {{ $firstInvoice->invoice_number ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <i class="fas fa-calendar-alt"></i> <strong>Invoice Date:</strong> {{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <i class="fas fa-clock"></i> <strong>Last Updated:</strong> {{ $customer->updated_at->format('d M Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .card-header {
        font-weight: 600;
    }
    .badge {
        padding: 8px 15px;
        font-size: 14px;
    }
    .text-end {
        text-align: right;
    }
    .table tfoot th {
        background-color: #e9ecef;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .border {
        border: 1px solid #dee2e6 !important;
    }
    .rounded {
        border-radius: 0.375rem !important;
    }
</style>
@endpush