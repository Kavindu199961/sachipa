@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Invoice Details - {{ $customer->name ?? 'N/A' }}</h3>
                    <div>
                        <a href="{{ route('invoices.print', $customer->id) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $customer->phone_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $customer->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $customer->location ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Summary</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Final Amount</th>
                                    <td>{{ number_format($customer->total_final_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Advance Amount</th>
                                    <td>{{ number_format($customer->total_advance_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Due Amount</th>
                                    <td>{{ number_format($customer->due_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5>Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Rate</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Item Discount</th>
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
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th>{{ number_format($customer->total_final_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($customer->advances->count() > 0)
                    <h5 class="mt-4">Advance Payments</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Advance Amount</th>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection