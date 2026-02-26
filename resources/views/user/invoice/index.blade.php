@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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
                            $dueAmount   = $customer->due_amount ?? 0;
                            $statusClass = $dueAmount <= 0
                                ? 'success'
                                : ($dueAmount < $customer->total_final_amount / 2 ? 'warning' : 'danger');
                            $statusText  = $dueAmount <= 0 ? 'Paid' : ($dueAmount < $customer->total_final_amount / 2 ? 'Partial' : 'Due');
                        @endphp
                        <tr class="{{ $dueAmount > 0 ? 'table-' . $statusClass : '' }}">
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
                            <td colspan="9" class="text-center">No invoices found</td>
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
                                            <th width="130px">Discount (%)</th>
                                            <th width="140px">Amount (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $items = [
                                            'STIIP'          => 'Stiiip Item',
                                            'ILETS'          => 'Ilets Item',
                                            'TICKS MATERIAL 1' => 'Ticks Material Type 1',
                                            'TICKS MATERIAL 2' => 'Ticks Material Type 2',
                                            'POLES'          => 'Poles',
                                            'RAILINGS'       => 'Railings',
                                            'FITTINGS'       => 'Fittings',
                                            'TIE BACKS'      => 'Tie Backs',
                                            'TAILORING'      => 'Tailoring Service',
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
                                                       name="discount[{{ $itemValue }}]"
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
                                            <td colspan="5" class="text-end fw-bold">Sub Total (items):</td>
                                            <td class="fw-bold">LKR <span id="subtotal">0.00</span></td>
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
                                {{-- Sub Total (read-only mirror of item sum) --}}
                                <div class="col-md-4">
                                    <label class="form-label">Items Sub Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" class="form-control bg-light"
                                               id="subtotal_amount" readonly value="0">
                                        {{-- submitted: items sub total --}}
                                        <input type="hidden" name="subtotal" id="subtotal_hidden" value="0">
                                    </div>
                                </div>

                                {{-- Total Amount — editable override, or auto from items --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Total Amount
                                        <small class="text-muted fw-normal">(override or auto)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" min="0" value="0"
                                               class="form-control" id="total_amount_input"
                                               placeholder="Auto-filled from items">
                                        {{-- SUBMITTED to controller --}}
                                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
                                    </div>
                                    <small class="text-muted">Leave 0 to use items sub total</small>
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
                                    <small class="text-info">
                                        = Total Amount − (Total Amount × Discount% / 100)
                                    </small>
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
                                               id="due_balance" name="due_balance" value="">
                                    </div>
                                    <small class="text-muted">= Final Amount − Advance</small>
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
                                        <div class="fw-bold">Total Amount</div>
                                        <div class="fs-5">LKR <span id="sum-subtotal">0.00</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Discount</div>
                                        <div class="fs-5"><span id="sum-discount">0</span>%</div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Final Amount</div>
                                        <div class="fs-5 text-success fw-bold">LKR <span id="sum-final">0.00</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">Due Balance</div>
                                        <div class="fs-5 text-danger">LKR <span id="sum-due">—</span></div>
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
                            <td class="text-end fw-bold" id="display_final_amount">$0.00</td>
                        </tr>
                        <tr>
                            <th>Total Advance Paid:</th>
                            <td class="text-end" id="display_paid_amount">$0.00</td>
                        </tr>
                        <tr class="table-warning">
                            <th>Current Due Balance:</th>
                            <td class="text-end fw-bold" id="display_due_balance">$0.00</td>
                        </tr>
                    </table>

                    <div class="mb-3">
                        <label class="form-label">
                            Advance Amount <span class="text-danger">*</span>
                            <small class="text-muted">(max: LKR <span id="max-advance-amount">0.00</span>)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" required
                                   class="form-control" id="advance_amount_modal" name="advance_amount"
                                   placeholder="Enter amount">
                        </div>
                        {{-- "Pay Full Amount" quick-fill --}}
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════
    //  CREATE INVOICE — calculation engine
    // ══════════════════════════════════════════

    const itemCheckboxes      = document.querySelectorAll('.item-checkbox');
    const selectAllBtn        = document.getElementById('selectAllItemsBtn');
    const deselectAllBtn      = document.getElementById('deselectAllItemsBtn');
    const subtotalSpan        = document.getElementById('subtotal');
    const subtotalAmountInput = document.getElementById('subtotal_amount');
    const subtotalHidden      = document.getElementById('subtotal_hidden');
    const totalAmountInput    = document.getElementById('total_amount_input');   // visible editable
    const totalAmountHidden   = document.getElementById('total_amount_hidden');  // submitted to controller
    const finalDiscountInput  = document.getElementById('final_discount');
    const finalAmountInput    = document.getElementById('final_amount');
    const advanceAmountInput  = document.getElementById('advance_amount');
    const dueBalanceInput     = document.getElementById('due_balance');

    // summary bar
    const sumCount    = document.getElementById('sum-count');
    const sumSubtotal = document.getElementById('sum-subtotal');
    const sumDiscount = document.getElementById('sum-discount');
    const sumFinal    = document.getElementById('sum-final');
    const sumDue      = document.getElementById('sum-due');

    /** Enable/disable a row's inputs when its checkbox toggled */
    function toggleRow(checkbox) {
        const row      = checkbox.closest('tr');
        const rate     = row.querySelector('.item-rate');
        const qty      = row.querySelector('.item-qty');
        const discount = row.querySelector('.item-discount');
        const isOn     = checkbox.checked;

        [rate, qty, discount].forEach(el => el.disabled = !isOn);
        row.classList.toggle('row-selected', isOn);

        if (!isOn) {
            rate.value     = '0';
            qty.value      = '1';
            discount.value = '0';
            calcRowAmount(row);
        }
    }

    /** Calculate a single row amount = rate * qty * (1 - item_discount/100) */
    function calcRowAmount(row) {
        const rate  = parseFloat(row.querySelector('.item-rate').value)     || 0;
        const qty   = parseFloat(row.querySelector('.item-qty').value)      || 1;
        const disc  = parseFloat(row.querySelector('.item-discount').value) || 0;
        const amt   = rate * qty * (1 - disc / 100);
        row.querySelector('.item-amount').value = amt.toFixed(2);
        return amt;
    }

    /** Master recalculation — called on every input change */
    function calculateAll() {
        // Step 1: Sum all selected item amounts
        let itemsSubTotal = 0;
        let selectedCount = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                itemsSubTotal += calcRowAmount(cb.closest('tr'));
                selectedCount++;
            }
        });

        // Update items sub-total display
        subtotalSpan.textContent  = itemsSubTotal.toFixed(2);
        subtotalAmountInput.value = itemsSubTotal.toFixed(2);
        subtotalHidden.value      = itemsSubTotal.toFixed(2);

        // Step 2: Total Amount = override if user typed > 0, else items sub total
        const overrideVal = parseFloat(totalAmountInput.value) || 0;
        const totalAmount = overrideVal > 0 ? overrideVal : itemsSubTotal;

        // Sync the hidden input that gets submitted
        totalAmountHidden.value = totalAmount.toFixed(2);

        // Step 3: Final Amount = Total Amount - (Total Amount × Discount% / 100)
        const disc          = parseFloat(finalDiscountInput.value) || 0;
        const discountAmt   = totalAmount * (disc / 100);
        const finalAmount   = totalAmount - discountAmt;

        finalAmountInput.value = finalAmount.toFixed(2);

        // Step 4: Due Balance = Final Amount - Advance (only if advance is entered)
        const advRaw   = advanceAmountInput.value.trim();
        const advance  = advRaw !== '' ? (parseFloat(advRaw) || 0) : null;

        if (advance !== null) {
            const due = finalAmount - advance;
            dueBalanceInput.value = (due >= 0 ? due : 0).toFixed(2);
        } else {
            dueBalanceInput.value = '';
        }

        // Step 5: Update summary bar
        sumCount.textContent    = selectedCount;
        sumSubtotal.textContent = totalAmount.toFixed(2);
        sumDiscount.textContent = disc;
        sumFinal.textContent    = finalAmount.toFixed(2);
        sumDue.textContent      = advance !== null
            ? (Math.max(0, finalAmount - advance)).toFixed(2)
            : '—';
    }

    // ── Wire up all events ──
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            toggleRow(this);
            calculateAll();
        });
    });

    document.querySelectorAll('.item-rate, .item-qty, .item-discount').forEach(input => {
        input.addEventListener('input', function () {
            if (this.closest('tr').querySelector('.item-checkbox').checked) {
                calculateAll();
            }
        });
    });

    totalAmountInput?.addEventListener('input', calculateAll);
    finalDiscountInput?.addEventListener('input', calculateAll);

    advanceAmountInput?.addEventListener('input', function () {
        calculateAll();
        const finalAmt = parseFloat(finalAmountInput.value) || 0;
        const adv      = parseFloat(this.value) || 0;
        this.classList.toggle('is-invalid', adv > finalAmt);
    });

    selectAllBtn?.addEventListener('click', function () {
        itemCheckboxes.forEach(cb => { cb.checked = true; toggleRow(cb); });
        calculateAll();
    });

    deselectAllBtn?.addEventListener('click', function () {
        itemCheckboxes.forEach(cb => { cb.checked = false; toggleRow(cb); });
        calculateAll();
    });

    // ── Form submit validation ──
    document.getElementById('createInvoiceForm')?.addEventListener('submit', function (e) {
        const checked  = Array.from(itemCheckboxes).filter(cb => cb.checked);
        const finalAmt = parseFloat(finalAmountInput.value) || 0;
        const advRaw   = advanceAmountInput.value.trim();
        const advAmt   = advRaw !== '' ? (parseFloat(advRaw) || 0) : 0;

        if (checked.length === 0) {
            e.preventDefault();
            return alert('Please select at least one item.');
        }
        if (finalAmt <= 0) {
            e.preventDefault();
            return alert('Final amount must be greater than zero. Enter rates for the selected items.');
        }
        if (advRaw !== '' && advAmt > finalAmt) {
            e.preventDefault();
            return alert('Advance amount cannot exceed the final amount of LKR ' + finalAmt.toFixed(2));
        }
        if (!confirm('Create this invoice?')) {
            e.preventDefault();
        }
    });

    // ── Initialise: disable all rows ──
    itemCheckboxes.forEach(cb => toggleRow(cb));

    // ══════════════════════════════════════════
    //  ADD ADVANCE MODAL
    // ══════════════════════════════════════════
    let currentDueBalance = 0;

    document.querySelectorAll('.add-advance-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const customerId   = this.dataset.id;
            const customerName = this.dataset.name;
            const finalAmt     = parseFloat(this.dataset.final);
            const advPaid      = parseFloat(this.dataset.advance);
            const due          = parseFloat(this.dataset.due);

            currentDueBalance = due;

            document.getElementById('advance-customer-name').textContent   = customerName || '(No name)';
            document.getElementById('display_final_amount').textContent    = 'LKR ' + finalAmt.toFixed(2);
            document.getElementById('display_paid_amount').textContent     = 'LKR ' + advPaid.toFixed(2);
            document.getElementById('display_due_balance').textContent     = 'LKR ' + due.toFixed(2);
            document.getElementById('max-advance-amount').textContent      = due.toFixed(2);

            document.getElementById('advanceForm').action = `/invoices/${customerId}/add-advance`;

            const amtInput = document.getElementById('advance_amount_modal');
            amtInput.value = '';
            amtInput.max   = due;

            const errDiv = document.getElementById('advance-error');
            const okDiv  = document.getElementById('advance-ok');
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
        amtInput.dispatchEvent(new Event('input'));
    });

    // Validate modal advance input live
    document.getElementById('advance_amount_modal')?.addEventListener('input', function () {
        const val    = parseFloat(this.value) || 0;
        const errDiv = document.getElementById('advance-error');
        const okDiv  = document.getElementById('advance-ok');
        const btn    = document.getElementById('submitAdvanceBtn');

        if (val > currentDueBalance) {
            this.classList.add('is-invalid');
            errDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> Amount cannot exceed due balance of LKR ${currentDueBalance.toFixed(2)}`;
            errDiv.classList.remove('d-none');
            okDiv.classList.add('d-none');
            btn.disabled = true;
        } else if (val <= 0) {
            this.classList.remove('is-invalid');
            errDiv.classList.add('d-none');
            okDiv.classList.add('d-none');
            btn.disabled = false;
        } else {
            this.classList.remove('is-invalid');
            errDiv.classList.add('d-none');
            okDiv.innerHTML = `<i class="fas fa-check-circle"></i> Amount valid. Remaining balance after payment: LKR ${(currentDueBalance - val).toFixed(2)}`;
            okDiv.classList.remove('d-none');
            btn.disabled = false;
        }
    });

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
});
</script>
@endpush