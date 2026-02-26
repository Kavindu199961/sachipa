@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Flash messages --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
                background: '#f8f9fa',
                iconColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true,
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
                background: '#f8f9fa',
                iconColor: '#dc3545'
            });
        </script>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Invoice Management</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="fas fa-plus"></i> Create Invoice
            </button>
        </div>

        <div class="card-body">
            {{-- Search --}}
            <form action="{{ route('invoices.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by customer name, email or phone number..."
                           value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
                    @if(request('search'))
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-danger">Clear</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped" id="invoice-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Location</th>
                            <th>Final Amount (LKR)</th>
                            <th>Advance Paid (LKR)</th>
                            <th>Due Amount (LKR)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        @php
                            $dueAmount = $customer->due_amount ?? 0;
                            $totalFinalAmount = $customer->total_final_amount ?? 0;
                            $statusClass = $dueAmount <= 0 
                                ? 'success' 
                                : ($dueAmount < $totalFinalAmount / 2 ? 'warning' : 'danger');
                            $statusText = $dueAmount <= 0 ? 'Paid' : ($dueAmount < $totalFinalAmount / 2 ? 'Partial' : 'Due');
                            $firstInvoice = $customer->invoices->first();
                        @endphp
                        <tr>
                            <td><strong>{{ $firstInvoice->invoice_number ?? 'N/A' }}</strong></td>
                            <td>{{ $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y') : 'N/A' }}</td>
                            <td><strong>{{ $customer->name ?? 'N/A' }}</strong></td>
                            <td>{{ $customer->email ?? '--' }}</td>
                            <td>{{ $customer->phone_number ?? '--' }}</td>
                            <td>{{ Str::limit($customer->location ?? '--', 20) }}</td>
                            <td>LKR {{ number_format($customer->total_final_amount, 2) }}</td>
                            <td>LKR {{ number_format($customer->total_advance_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $statusClass }}">
                                    LKR {{ number_format($dueAmount, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $customer->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $customer->id) }}" class="btn btn-sm btn-success" target="_blank" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                                <button class="btn btn-sm btn-warning add-advance-btn"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-final="{{ $customer->total_final_amount }}"
                                        data-advance="{{ $customer->total_advance_amount }}"
                                        data-due="{{ $dueAmount }}"
                                        title="Add Advance">
                                    <i class="fas fa-money-bill"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-invoice"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No invoices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            {{ $customers->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CREATE INVOICE MODAL
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="max-height: 95vh;">
            <form action="{{ route('invoices.store') }}" method="POST" id="createInvoiceForm" style="display:flex; flex-direction:column; height:100%; min-height:0;">
                @csrf
                <div class="modal-header bg-primary text-white flex-shrink-0">
                    <h5 class="modal-title" id="createInvoiceModalLabel">
                        <i class="fas fa-file-invoice"></i> Create New Invoice
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="overflow-y: auto; flex: 1 1 auto; min-height: 0;">
                    {{-- ── Customer Info ── --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Customer Name <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control" name="customer_name" placeholder="Enter customer name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control" name="phone_number" placeholder="Enter phone number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-muted">(Optional)</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email address">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control" name="location" placeholder="Enter location">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Items Table ── --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-box"></i> Select Items & Enter Details</h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-success" id="selectAllItemsBtn">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="deselectAllItemsBtn">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="45px" class="text-center">✓</th>
                                            <th>Item Name</th>
                                            <th width="140px">Rate (LKR)</th>
                                            <th width="100px">Qty</th>
                                            <th width="130px">Item Discount (%)</th>
                                            <th width="140px">Amount (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $items = [
                                            'STIIP' => 'Stiiip Item',
                                            'ILETS' => 'Ilets Item',
                                            'TICKS MATERIAL 1' => 'Ticks Material Type 1',
                                            'TICKS MATERIAL 2' => 'Ticks Material Type 2',
                                            'POLES' => 'Poles',
                                            'RAILINGS' => 'Railings',
                                            'FITTINGS' => 'Fittings',
                                            'TIE BACKS' => 'Tie Backs',
                                            'TAILORING' => 'Tailoring Service',
                                        ];
                                        @endphp

                                        @foreach($items as $itemValue => $itemLabel)
                                        <tr class="item-row" data-item="{{ $itemValue }}">
                                            <td class="text-center align-middle">
                                                <input class="form-check-input item-checkbox" type="checkbox"
                                                       name="items[]" value="{{ $itemValue }}"
                                                       id="item_{{ $loop->index }}">
                                            </td>
                                            <td class="align-middle">
                                                <label for="item_{{ $loop->index }}" class="fw-bold mb-0 cursor-pointer">
                                                    {{ $itemValue }}
                                                    @if($itemLabel !== $itemValue)
                                                        <br><small class="text-muted fw-normal">{{ $itemLabel }}</small>
                                                    @endif
                                                </label>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" value="0"
                                                       class="form-control form-control-sm item-rate"
                                                       name="rate[{{ $itemValue }}]"
                                                       placeholder="0.00" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="1" min="1" value="1"
                                                       class="form-control form-control-sm item-qty"
                                                       name="qty[{{ $itemValue }}]"
                                                       placeholder="1" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="100" value="0"
                                                       class="form-control form-control-sm item-discount"
                                                       name="item_discount[{{ $itemValue }}]"
                                                       placeholder="0" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" value="0" readonly
                                                       class="form-control form-control-sm item-amount bg-light"
                                                       name="amount[{{ $itemValue }}]">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Items Sub Total:</td>
                                            <td class="fw-bold">LKR <span id="items_subtotal">0.00</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ── Payment Details ── --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-calculator"></i> Payment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Invoice Date --}}
                                <div class="col-md-4">
                                    <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control"
                                           id="invoice_date" name="invoice_date"
                                           value="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Total Amount (Auto-calculated from items or manually overridden) --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Total Amount
                                        <small class="text-muted fw-normal">(Auto from items or override)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" min="0" value="0"
                                               class="form-control" id="total_amount_input"
                                               name="total_amount"
                                               placeholder="Auto from items">
                                    </div>
                                    <small class="text-muted">Leave 0 to use items total</small>
                                </div>

                                {{-- Final Discount % --}}
                                <div class="col-md-4">
                                    <label class="form-label">Final Discount (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">%</span>
                                        <input type="number" step="0.01" min="0" max="100" value="0"
                                               class="form-control" id="final_discount" name="final_discount">
                                    </div>
                                    <small class="text-muted">Applied to Total Amount</small>
                                </div>

                                {{-- Final Amount = Total Amount − (Total Amount × Discount%) --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-success">
                                        Final Amount <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">LKR</span>
                                        <input type="number" step="0.01" readonly
                                               class="form-control fw-bold bg-light"
                                               id="final_amount" name="final_amount" value="0">
                                    </div>
                                </div>

                                {{-- Advance Amount (optional) --}}
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Advance Amount
                                        <span class="text-muted small">(Optional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" min="0" value=""
                                               class="form-control" id="advance_amount"
                                               name="advance_amount" placeholder="Leave blank if none">
                                    </div>
                                </div>

                                {{-- Due Balance --}}
                                <div class="col-md-4">
                                    <label class="form-label">Due Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" readonly
                                               class="form-control bg-light"
                                               id="due_balance" value="0">
                                    </div>
                                </div>

                                {{-- Advance Date --}}
                                <div class="col-md-4">
                                    <label class="form-label">Advance Payment Date</label>
                                    <input type="date" class="form-control"
                                           id="advance_date" name="advance_date"
                                           value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            {{-- Live Summary Bar --}}
                            <div class="alert alert-success mt-3 mb-0" id="invoice-summary">
                                <div class="row text-center small">
                                    <div class="col">
                                        <div class="fw-bold">Items Selected</div>
                                        <div class="fs-5" id="sum-count">0</div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Items Total</div>
                                        <div class="fs-5">LKR <span id="sum-items-total">0.00</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Total Amount</div>
                                        <div class="fs-5">LKR <span id="sum-total">0.00</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Final Discount</div>
                                        <div class="fs-5"><span id="sum-discount">0</span>%</div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Final Amount</div>
                                        <div class="fs-5 text-success fw-bold">LKR <span id="sum-final">0.00</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Due Balance</div>
                                        <div class="fs-5 text-danger">LKR <span id="sum-due">0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitInvoiceBtn">
                        <i class="fas fa-save"></i> Create Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     ADD ADVANCE MODAL
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addAdvanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="advanceForm">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-money-bill-wave"></i> Add Advance Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center mb-3" id="advance-customer-name"></h5>

                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <th>Final Amount:</th>
                            <td class="text-end fw-bold" id="display_final_amount">LKR 0.00</td>
                        </tr>
                        <tr>
                            <th>Total Advance Paid:</th>
                            <td class="text-end" id="display_paid_amount">LKR 0.00</td>
                        </tr>
                        <tr class="table-warning">
                            <th>Current Due Balance:</th>
                            <td class="text-end fw-bold" id="display_due_balance">LKR 0.00</td>
                        </tr>
                    </table>

                    <div class="mb-3">
                        <label class="form-label">
                            Advance Amount <span class="text-danger">*</span>
                            <small class="text-muted">(max: LKR <span id="max-advance-amount">0.00</span>)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                            <input type="number" step="0.01" min="0.01" required
                                   class="form-control" id="advance_amount_modal" name="advance_amount"
                                   placeholder="Enter amount">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-warning mt-1" id="payFullBtn">
                            <i class="fas fa-check-double"></i> Pay Full Due Amount
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="adv_date" name="date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="alert alert-danger d-none" id="advance-error"></div>
                    <div class="alert alert-success d-none" id="advance-ok"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitAdvanceBtn">
                        <i class="fas fa-check"></i> Add Advance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     DELETE MODAL
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Delete invoice for <strong id="delete-customer-name"></strong>?</p>
                <p class="text-danger small">This cannot be undone. All items and advance payments will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="" method="POST" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .table-striped tbody tr.table-success { background-color: #d4edda; }
    .table-striped tbody tr.table-warning { background-color: #fff3cd; }
    .table-striped tbody tr.table-danger  { background-color: #f8d7da; }
    .badge { padding: 5px 10px; font-size: 12px; }
    .btn-close-white { filter: invert(1); }
    .item-row { transition: background-color .2s; }
    .item-row:hover { background-color: #f8f9fa; }
    .item-row.row-selected { background-color: #e8f4ff; }
    #items-table input[type="number"] { min-width: 90px; }
    /* Fix modal scrolling */
    #createInvoiceModal .modal-dialog { height: calc(100% - 3.5rem); }
    #createInvoiceModal .modal-content { height: 100%; display: flex; flex-direction: column; }
    #createInvoiceModal .modal-body { flex: 1 1 auto; overflow-y: auto !important; min-height: 0; }
    .is-invalid { border-color: #dc3545; }
    .is-invalid:focus { box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════
    //  CREATE INVOICE — calculation engine
    // ══════════════════════════════════════════

    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAllBtn = document.getElementById('selectAllItemsBtn');
    const deselectAllBtn = document.getElementById('deselectAllItemsBtn');
    const itemsSubtotalSpan = document.getElementById('items_subtotal');
    const totalAmountInput = document.getElementById('total_amount_input');
    const finalDiscountInput = document.getElementById('final_discount');
    const finalAmountInput = document.getElementById('final_amount');
    const advanceAmountInput = document.getElementById('advance_amount');
    const dueBalanceInput = document.getElementById('due_balance');

    // summary bar elements
    const sumCount = document.getElementById('sum-count');
    const sumItemsTotal = document.getElementById('sum-items-total');
    const sumTotal = document.getElementById('sum-total');
    const sumDiscount = document.getElementById('sum-discount');
    const sumFinal = document.getElementById('sum-final');
    const sumDue = document.getElementById('sum-due');

    /** Calculate a single row amount = (rate * qty) - ((rate * qty) * item_discount/100) */
    function calcRowAmount(row) {
        const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
        const qty = parseFloat(row.querySelector('.item-qty').value) || 1;
        const disc = parseFloat(row.querySelector('.item-discount').value) || 0;
        
        const subTotal = rate * qty;
        const discountAmount = subTotal * (disc / 100);
        const amount = subTotal - discountAmount;
        
        row.querySelector('.item-amount').value = amount.toFixed(2);
        return amount;
    }

    /** Calculate items subtotal from selected rows */
    function calculateItemsSubtotal() {
        let itemsTotal = 0;
        let selectedCount = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                itemsTotal += calcRowAmount(cb.closest('tr'));
                selectedCount++;
            }
        });

        itemsSubtotalSpan.textContent = itemsTotal.toFixed(2);
        return { itemsTotal, selectedCount };
    }

    /** Master recalculation */
    function calculateAll() {
        // Calculate items subtotal
        const { itemsTotal, selectedCount } = calculateItemsSubtotal();

        // Get total amount (either manual override or items total)
        const manualTotal = parseFloat(totalAmountInput.value) || 0;
        const totalAmount = manualTotal > 0 ? manualTotal : itemsTotal;

        // Calculate final amount after discount
        const finalDiscount = parseFloat(finalDiscountInput.value) || 0;
        const discountAmount = totalAmount * (finalDiscount / 100);
        const finalAmount = totalAmount - discountAmount;

        // Update final amount field
        finalAmountInput.value = finalAmount.toFixed(2);

        // Calculate due balance if advance is entered
        const advanceRaw = advanceAmountInput.value.trim();
        const advance = advanceRaw !== '' ? (parseFloat(advanceRaw) || 0) : 0;
        const dueBalance = finalAmount - advance;
        dueBalanceInput.value = (dueBalance >= 0 ? dueBalance : 0).toFixed(2);

        // Update summary bar
        sumCount.textContent = selectedCount;
        sumItemsTotal.textContent = itemsTotal.toFixed(2);
        sumTotal.textContent = totalAmount.toFixed(2);
        sumDiscount.textContent = finalDiscount;
        sumFinal.textContent = finalAmount.toFixed(2);
        sumDue.textContent = (dueBalance >= 0 ? dueBalance : 0).toFixed(2);

        // Validate advance amount
        if (advanceRaw !== '' && advance > finalAmount) {
            advanceAmountInput.classList.add('is-invalid');
        } else {
            advanceAmountInput.classList.remove('is-invalid');
        }
    }

    /** Enable/disable row inputs based on checkbox */
    function toggleRow(checkbox) {
        const row = checkbox.closest('tr');
        const rate = row.querySelector('.item-rate');
        const qty = row.querySelector('.item-qty');
        const discount = row.querySelector('.item-discount');
        const isChecked = checkbox.checked;

        [rate, qty, discount].forEach(el => el.disabled = !isChecked);
        row.classList.toggle('row-selected', isChecked);

        if (!isChecked) {
            rate.value = '0';
            qty.value = '1';
            discount.value = '0';
            calcRowAmount(row);
        }
    }

    // ── Event Listeners ──
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            toggleRow(this);
            calculateAll();
        });
    });

    // Input events for rate, qty, discount
    document.querySelectorAll('.item-rate, .item-qty, .item-discount').forEach(input => {
        input.addEventListener('input', function () {
            if (this.closest('tr').querySelector('.item-checkbox').checked) {
                calculateAll();
            }
        });
    });

    totalAmountInput.addEventListener('input', calculateAll);
    finalDiscountInput.addEventListener('input', calculateAll);
    
    advanceAmountInput.addEventListener('input', function () {
        calculateAll();
    });

    // Select/Deselect All buttons
    selectAllBtn.addEventListener('click', function () {
        itemCheckboxes.forEach(cb => {
            cb.checked = true;
            toggleRow(cb);
        });
        calculateAll();
    });

    deselectAllBtn.addEventListener('click', function () {
        itemCheckboxes.forEach(cb => {
            cb.checked = false;
            toggleRow(cb);
        });
        calculateAll();
    });

    // Form submit validation - REMOVED ALERT AND CONFIRM
    document.getElementById('createInvoiceForm').addEventListener('submit', function (e) {
        const checkedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
        const finalAmount = parseFloat(finalAmountInput.value) || 0;
        const advanceRaw = advanceAmountInput.value.trim();
        const advance = advanceRaw !== '' ? (parseFloat(advanceRaw) || 0) : 0;

        if (checkedItems.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please select at least one item.',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (finalAmount <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Final amount must be greater than zero. Please enter rates for selected items.',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (advanceRaw !== '' && advance > finalAmount) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Advance amount cannot exceed the final amount of LKR ' + finalAmount.toFixed(2),
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK'
            });
            return;
        }

        // No confirm dialog - submit directly
        return true;
    });

    // Initial calculation
    calculateAll();

    // ══════════════════════════════════════════
    //  ADD ADVANCE MODAL
    // ══════════════════════════════════════════
    let currentDueBalance = 0;

    document.querySelectorAll('.add-advance-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const customerId = this.dataset.id;
            const customerName = this.dataset.name;
            const finalAmt = parseFloat(this.dataset.final);
            const advPaid = parseFloat(this.dataset.advance);
            const due = parseFloat(this.dataset.due);

            currentDueBalance = due;

            document.getElementById('advance-customer-name').textContent = customerName || '(No name)';
            document.getElementById('display_final_amount').textContent = 'LKR ' + finalAmt.toFixed(2);
            document.getElementById('display_paid_amount').textContent = 'LKR ' + advPaid.toFixed(2);
            document.getElementById('display_due_balance').textContent = 'LKR ' + due.toFixed(2);
            document.getElementById('max-advance-amount').textContent = due.toFixed(2);

            document.getElementById('advanceForm').action = `/invoices/${customerId}/add-advance`;

            const amtInput = document.getElementById('advance_amount_modal');
            amtInput.value = '';
            amtInput.max = due;

            const errDiv = document.getElementById('advance-error');
            const okDiv = document.getElementById('advance-ok');
            errDiv.classList.add('d-none');
            okDiv.classList.add('d-none');

            const submitBtn = document.getElementById('submitAdvanceBtn');
            if (due <= 0) {
                errDiv.innerHTML = '<i class="fas fa-info-circle"></i> This invoice is fully paid. No due balance remaining.';
                errDiv.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }

            new bootstrap.Modal(document.getElementById('addAdvanceModal')).show();
        });
    });

    // Pay full amount quick-fill
    document.getElementById('payFullBtn')?.addEventListener('click', function () {
        const amtInput = document.getElementById('advance_amount_modal');
        amtInput.value = currentDueBalance.toFixed(2);
        validateAdvanceInput();
    });

    // Validate modal advance input live
    function validateAdvanceInput() {
        const amtInput = document.getElementById('advance_amount_modal');
        const val = parseFloat(amtInput.value) || 0;
        const errDiv = document.getElementById('advance-error');
        const okDiv = document.getElementById('advance-ok');
        const btn = document.getElementById('submitAdvanceBtn');

        if (val > currentDueBalance) {
            amtInput.classList.add('is-invalid');
            errDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> Amount cannot exceed due balance of LKR ${currentDueBalance.toFixed(2)}`;
            errDiv.classList.remove('d-none');
            okDiv.classList.add('d-none');
            btn.disabled = true;
        } else if (val <= 0) {
            amtInput.classList.remove('is-invalid');
            errDiv.classList.add('d-none');
            okDiv.classList.add('d-none');
            btn.disabled = false;
        } else {
            amtInput.classList.remove('is-invalid');
            errDiv.classList.add('d-none');
            okDiv.innerHTML = `<i class="fas fa-check-circle"></i> Amount valid. Remaining balance after payment: LKR ${(currentDueBalance - val).toFixed(2)}`;
            okDiv.classList.remove('d-none');
            btn.disabled = false;
        }
    }

    document.getElementById('advance_amount_modal')?.addEventListener('input', validateAdvanceInput);

    // ══════════════════════════════════════════
    //  DELETE MODAL
    // ══════════════════════════════════════════
    document.querySelectorAll('.delete-invoice').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('delete-customer-name').textContent = this.dataset.name || 'this customer';
            document.getElementById('deleteForm').action = `/invoices/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Fix modal close button issue
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    });
});
</script>
@endpush