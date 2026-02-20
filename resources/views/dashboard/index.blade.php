@extends('layouts.app')

@section('content')

<div class="row mb-3">


 <!-- Stock Items -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
      
            <div class="card p-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="font-15">Stock Items</h5>
                        <h2 class="mb-2 font-18"></h2>
                        <p class="mb-0"><span class="col-green">{{ rand(25, 45) }}%</span> Increase</p>
                    </div>
                    <div class="col-6 text-end">
                        <img src="/assets/img/banner/1.png" alt="Stock Items" style="width: 96px; height: 96px;">
                    </div>
                </div>
            </div>
        </a>
       
    </div>

     <!-- Invoices with Stock -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
    
        <a href="" style="text-decoration: none; color: inherit;">
     
            <div class="card p-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="font-15">Invoices with Stock</h5>
                        <h2 class="mb-2 font-18"></h2>
                        <p class="mb-0"><span class="col-orange">{{ rand(5, 15) }}%</span> Growth</p>
                    </div>
                    <div class="col-6 text-end">
                        <img src="/assets/img/banner/2.png" alt="Invoices with Stock" style="width: 96px; height: 96px;">
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Invoices -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        
            <div class="card p-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="font-15">Total Invoices</h5>
                        <h2 class="mb-2 font-18"></h2>
                        <p class="mb-0"><span class="col-green">{{ rand(15, 30) }}%</span> Increase</p>
                    </div>
                    <div class="col-6 text-end">
                        <img src="/assets/img/banner/3.png" alt="Total Invoices" style="width: 96px; height: 96px;">
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Estimates -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
       
        <a href="" style="text-decoration: none; color: inherit;">
      
            <div class="card p-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="font-15">Total Estimates</h5>
                        <h2 class="mb-2 font-18"></h2>
                        <p class="mb-0"><span class="col-green">{{ rand(10, 25) }}%</span> Increase</p>
                    </div>
                    <div class="col-6 text-end">
                        <img src="/assets/img/banner/4.png" alt="Total Estimates" style="width: 96px; height: 96px;">
                    </div>
                </div>
            </div>
        </a>
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
                            <div class="col-md-3">
                                <i data-feather="file-text" class="col-green"></i>
                                <h5 class="mb-0"></h5>
                                <p class="text-muted font-14">Total Estimates</p>
                            </div>
                            <div class="col-md-3">
                                <i data-feather="shopping-cart" class="col-orange"></i>
                                <h5 class="mb-0"></h5>
                                <p class="text-muted font-14">Invoices with Stock</p>
                            </div>
                            <div class="col-md-3">
                                <i data-feather="dollar-sign" class="col-blue"></i>
                                <h5 class="mb-0"></h5>
                                <p class="text-muted font-14">Total Invoices</p>
                            </div>
                            <div class="col-md-3">
                                <i data-feather="box" class="col-purple"></i>
                                <h5 class="mb-0"></h5>
                                <p class="text-muted font-14">Stock Items</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="col-lg-3">
                        <h6 class="mb-3">Quick Stats</h6>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="trending-up" class="text-success me-2"></i>
                            <div>
                                <h6 class="mb-0"></h6>
                                <small class="text-muted">This Month</small>
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
                height: 350
            },
            series: [{
                name: 'Invoices',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
            }
        };

        var chart = new ApexCharts(document.querySelector("#invoiceChart"), options);
        chart.render();
    });
</script>
@endsection