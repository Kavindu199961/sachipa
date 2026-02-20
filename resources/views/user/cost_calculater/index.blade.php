@extends('layouts.app')

@section('content')

<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    --light-bg: #f8f9fa;
    --border-radius: 12px;
}

.container {
    max-width: 1200px;
    padding: 15px;
}

/* Card Styling */
.card {
    border: none;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    padding: 1.5rem !important;
    border-bottom: none;
}

.card-header h5 {
    font-size: 1.8rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.card-body {
    padding: 2rem;
    background: #ffffff;
}

/* Table Styling */
.table {
    margin-bottom: 0;
    font-size: 1.1rem;
}

.table thead th {
    background: linear-gradient(135deg, #34495e, #2c3e50) !important;
    color: white;
    font-size: 1.2rem;
    font-weight: 500;
    padding: 1rem;
    border: none;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 2px solid #eef2f7;
}

.table tbody tr:hover {
    background-color: #f0f7ff;
    transform: scale(1.01);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    font-size: 1.1rem;
}

/* Form Input Styling */
.form-control {
    border: 2px solid #e0e6ed;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    background: var(--light-bg);
    font-weight: 500;
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    background: white;
}

.form-control[readonly] {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-color: var(--success-color);
    color: #2e7d32;
    font-weight: 600;
    cursor: default;
}

/* Others Row Special Styling */
tr:last-child td {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    border-bottom: none;
}

tr:last-child .form-control[readonly] {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    border-color: #ff9800;
    color: #e65100;
}

/* Grand Total Styling */
.text-end {
    background: linear-gradient(135deg, #f5f7fa, #e8ecf1);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    margin-top: 2rem !important;
}

.text-end h5 {
    font-size: 2rem !important;
    margin-bottom: 0;
    color: var(--primary-color);
}

.text-end .text-success {
    font-size: 2.2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--success-color), #219a52);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-left: 1rem;
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 10px;
    }
    
    .card-header h5 {
        font-size: 1.4rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table {
        font-size: 0.95rem;
    }
    
    .table thead th {
        font-size: 0.9rem;
        padding: 0.75rem;
        white-space: nowrap;
    }
    
    .table td {
        padding: 0.75rem 0.5rem;
    }
    
    .form-control {
        padding: 0.5rem;
        font-size: 0.95rem;
        min-width: 70px;
    }
    
    /* Make rate and qty appear in one row on mobile */
    #shop-table {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #shop-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    
    #shop-table td:nth-child(2),
    #shop-table td:nth-child(3) {
        width: 120px;
    }
    
    .text-end {
        padding: 1rem;
    }
    
    .text-end h5 {
        font-size: 1.3rem !important;
    }
    
    .text-end .text-success {
        font-size: 1.5rem;
    }
}

/* Extra Small Devices */
@media (max-width: 480px) {
    .table thead th {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .table td {
        padding: 0.5rem 0.3rem;
    }
    
    .form-control {
        padding: 0.4rem;
        font-size: 0.85rem;
        min-width: 60px;
    }
    
    .card-header h5 {
        font-size: 1.2rem;
    }
    
    .text-end h5 {
        font-size: 1.1rem !important;
    }
    
    .text-end .text-success {
        font-size: 1.3rem;
    }
}

/* Touch-friendly inputs */
@media (hover: none) and (pointer: coarse) {
    .form-control {
        min-height: 44px; /* Apple's recommended touch target size */
    }
    
    .table td {
        padding: 0.5rem 0.3rem;
    }
}

/* Animation for total updates */
@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

.total, .other-total {
    animation: highlight 1s ease;
}

/* Loading state for inputs */
.form-control:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Custom scrollbar for table */
#shop-table::-webkit-scrollbar {
    height: 6px;
}

#shop-table::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#shop-table::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 10px;
}

#shop-table::-webkit-scrollbar-thumb:hover {
    background: var(--primary-color);
}
</style>

<div class="container mt-4">
    <div class="card shadow">
        
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">
                <i class="fas fa-tshirt me-2"></i>
                Curtain Cost Calculator (LKR)
                <i class="fas fa-calculator ms-2"></i>
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="shop-table">
                    
                    <thead class="thead-dark table-dark text-white">
                        <tr class="text-white">
                            <th class="text-white"><i class="fas fa-tag me-1 text-white"></i> Item</th>
                            <th class="text-white"><i class="fas fa-money-bill-wave me-1 text-white"></i> Rate (LKR)</th>
                            <th class="text-white"><i class="fas fa-sort-amount-up me-1 text-white"></i> Qty</th>
                            <th class="text-white"><i class="fas fa-coins me-1 text-white"></i> Total (LKR)</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                        $items = [
                            "STRIP",
                            "ILETS",
                            "TICK MATERIAL 1",
                            "TICK MATERIAL 2",
                            "POLE",
                            "RAILINGS",
                            "FITTINGS",
                            "TIE BACKS",
                            "TAILORING"
                        ];
                        @endphp

                        @foreach($items as $index => $item)
                        <tr>
                            <td>
                                <strong>{{ $item }}</strong>
                                @if($index == 4 || $index == 5)
                                    <br><small class="text-muted">(per meter)</small>
                                @endif
                            </td>
                            <td>
                                <input type="number" class="form-control rate" 
                                       placeholder="Rate" min="0" step="0.01">
                            </td>
                            <td>
                                <input type="number" class="form-control qty" 
                                       placeholder="Qty" min="0" step="0.01">
                            </td>
                            <td>
                                <input type="text" class="form-control total" 
                                       readonly placeholder="0.00">
                            </td>
                        </tr>
                        @endforeach

                        <!-- Others (No Qty) -->
                        <tr>
                            <td>
                                <strong>Others</strong>
                                <br><small class="text-muted">(additional items)</small>
                            </td>
                            <td>
                                <input type="number" class="form-control other-rate" 
                                       placeholder="Amount" min="0" step="0.01">
                            </td>
                            <td class="text-center text-muted">
                                <span class="badge bg-secondary">-</span>
                            </td>
                            <td>
                                <input type="text" class="form-control other-total" 
                                       readonly placeholder="0.00">
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Grand Total -->
            <div class="text-end mt-3">
                <h5 class="fw-bold">
                    <i class="fas fa-chart-line me-2"></i>
                    Grand Total: 
                    <span class="text-success">
                        LKR <span id="grandTotal">0.00</span>
                    </span>
                </h5>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    All amounts are in Sri Lankan Rupees (LKR)
                </small>
            </div>

        </div>
    </div>
</div>

<!-- Add Font Awesome for icons (add to your layout's head section) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
function calculateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('#shop-table tbody tr').forEach(function(row) {
        let rate = row.querySelector('.rate');
        let qty = row.querySelector('.qty');
        let totalField = row.querySelector('.total');

        if (rate && qty && totalField) {
            let r = parseFloat(rate.value) || 0;
            let q = parseFloat(qty.value) || 0;
            let total = r * q;

            totalField.value = total ? total.toFixed(2) : '0.00';
            
            // Add animation class
            totalField.classList.add('highlight');
            setTimeout(() => totalField.classList.remove('highlight'), 300);
            
            grandTotal += total;
        }
    });

    // Others
    let otherRate = document.querySelector('.other-rate');
    let otherTotal = document.querySelector('.other-total');

    if (otherRate && otherTotal) {
        let otherValue = parseFloat(otherRate.value) || 0;
        otherTotal.value = otherValue ? otherValue.toFixed(2) : '0.00';
        
        // Add animation class
        otherTotal.classList.add('highlight');
        setTimeout(() => otherTotal.classList.remove('highlight'), 300);
        
        grandTotal += otherValue;
    }

    // Update grand total with animation
    let grandTotalElement = document.getElementById('grandTotal');
    grandTotalElement.innerText = grandTotal.toFixed(2);
    
    // Add pulse animation to grand total
    grandTotalElement.style.transition = 'transform 0.3s ease';
    grandTotalElement.style.transform = 'scale(1.1)';
    setTimeout(() => {
        grandTotalElement.style.transform = 'scale(1)';
    }, 200);
}

// Add input validation
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('rate') || e.target.classList.contains('qty') || e.target.classList.contains('other-rate')) {
        // Ensure non-negative values
        if (e.target.value < 0) {
            e.target.value = 0;
        }
        calculateTotals();
    }
});

// Add keyboard support for better UX
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && (e.target.classList.contains('rate') || e.target.classList.contains('qty'))) {
        e.preventDefault();
        let inputs = Array.from(document.querySelectorAll('.rate, .qty'));
        let index = inputs.indexOf(e.target);
        if (index > -1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    }
});

// Initialize with zeros
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>

@endsection