@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <!-- Stock Items -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card p-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="font-15">Stock Items</h5>
                    <h2 class="mb-2 font-18">{{ $stockCount }}</h2>
                    <p class="mb-0">
                        <span class="col-green">{{ $stockGrowth }}%</span> 
                        @if($stockGrowth > 0) Increase @else Decrease @endif
                    </p>
                </div>
                <div class="col-6 text-end">
                    <img src="/assets/img/banner/1.png" alt="Stock Items" style="width: 96px; height: 96px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Total Invoices (Unique) -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <a href="{{ route('invoices.index') }}" style="text-decoration: none; color: inherit;">
            <div class="card p-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="font-15">Total Invoices</h5>
                        <h2 class="mb-2 font-18">{{ $invoiceCount }}</h2>
                        <p class="mb-0">
                            <span class="col-green">{{ $invoiceGrowth }}%</span> 
                            @if($invoiceGrowth > 0) Increase @else Decrease @endif
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <img src="/assets/img/banner/3.png" alt="Total Invoices" style="width: 96px; height: 96px;">
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card p-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="font-15">Total Revenue</h5>
                    <h2 class="mb-2 font-18">LKR {{ number_format($totalRevenue, 2) }}</h2>
                    <p class="mb-0">
                        <span class="col-green">From {{ $invoiceCount }} invoices</span>
                    </p>
                </div>
                <div class="col-6 text-end">
                    <img src="/assets/img/banner/4.png" alt="Total Revenue" style="width: 96px; height: 96px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Average Invoice Value -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card p-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="font-15">Avg Invoice Value</h5>
                    <h2 class="mb-2 font-18">LKR {{ number_format($averageInvoiceValue, 2) }}</h2>
                    <p class="mb-0">
                        <span class="col-orange">Per invoice</span>
                    </p>
                </div>
                <div class="col-6 text-end">
                    <img src="/assets/img/banner/2.png" alt="Average Invoice" style="width: 96px; height: 96px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overview and Chart -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Billing Dashboard Overview</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Chart -->
                    <div class="col-lg-9">
                        <div id="invoiceChart"></div>
                        <div class="row text-center mt-3">
                            <div class="col-md-4">
                                <i data-feather="box" class="col-green"></i>
                                <h5 class="mb-0">{{ $stockCount }}</h5>
                                <p class="text-muted font-14">Stock Items</p>
                            </div>
                            <div class="col-md-4">
                                <i data-feather="dollar-sign" class="col-blue"></i>
                                <h5 class="mb-0">{{ $invoiceCount }}</h5>
                                <p class="text-muted font-14">Total Invoices</p>
                            </div>
                            <div class="col-md-4">
                                <i data-feather="trending-up" class="col-orange"></i>
                                <h5 class="mb-0">LKR {{ number_format($averageInvoiceValue, 0) }}</h5>
                                <p class="text-muted font-14">Avg. Invoice Value</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="col-lg-3">
                        <h6 class="mb-3">Quick Stats</h6>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="trending-up" class="text-success me-2"></i>
                            <div>
                                <h6 class="mb-0">{{ $invoiceGrowth }}%</h6>
                                <small class="text-muted">Invoice Growth This Month</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="box" class="text-primary me-2"></i>
                            <div>
                                <h6 class="mb-0">{{ $totalStockQuantity }}</h6>
                                <small class="text-muted">Total Items in Stock</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="trending-up" class="text-info me-2"></i>
                            <div>
                                <h6 class="mb-0">{{ $stockGrowth }}%</h6>
                                <small class="text-muted">Stock Growth This Month</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="dollar-sign" class="text-warning me-2"></i>
                            <div>
                                <h6 class="mb-0">LKR {{ number_format($totalRevenue, 0) }}</h6>
                                <small class="text-muted">Total Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Invoices',
                data: @json($chartData)
            }],
            xaxis: {
                categories: @json($chartCategories)
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#4361ee'],
            markers: {
                size: 5,
                colors: ['#4361ee'],
                strokeColors: '#fff',
                strokeWidth: 2
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                },
            }
        };

        var chart = new ApexCharts(document.querySelector("#invoiceChart"), options);
        chart.render();
    });
</script>
@endsection