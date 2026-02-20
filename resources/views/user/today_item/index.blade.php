@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <!-- Success Message -->
            @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    showConfirmButton: true,
                    confirmButtonColor: '#0d6efd',
                    background: '#f8f9fa'
                });
            </script>
            @endif

            @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    showConfirmButton: true,
                    confirmButtonColor: '#d33',
                    background: '#f8f9fa'
                });
            </script>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Today's Item Selection (LKR)</h4>
                    <div>
                        <!-- <span class="badge bg-info text-white p-2 me-2" style="font-size: 16px;">
                            <i class="fas fa-shopping-cart"></i> 
                            Temporary Items: <span id="temp-count">{{ count(session()->get('today_items_temp', [])) }}</span>
                        </span>
                        <span class="badge bg-success text-white p-2 me-2" style="font-size: 16px;">
                            <i class="fas fa-check-circle"></i> 
                            Saved Today: <span id="saved-count">{{ $todayItems->count() }}</span>
                        </span>
                        <span class="badge bg-warning text-dark p-2" style="font-size: 16px;">
                            <i class="fas fa-money-bill-wave"></i> 
                            Total: <span id="grand-total">LKR {{ number_format($totalToday + collect(session()->get('today_items_temp', []))->sum('total_cost'), 2) }}</span>
                        </span> -->
                    </div>
                </div>

                <div class="card-body">
                    <!-- Item Selection Form -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockSelect">Select Item</label>
                                        <select class="form-control select2" id="stockSelect" style="width: 100%;">
                                            <option value="">-- Search and Select Item --</option>
                                            @foreach($stocks as $stock)
                                                <option value="{{ $stock->id }}" 
                                                        data-code="{{ $stock->item_code }}"
                                                        data-name="{{ $stock->item_name }}"
                                                        data-cost="{{ $stock->cost }}"
                                                        data-qty="{{ $stock->quantity }}"
                                                        data-barcode="{{ $stock->barcode }}">
                                                    {{ $stock->item_name }} ({{ $stock->item_code ?? 'No Code' }}) - Available: {{ $stock->quantity }} - LKR {{ number_format($stock->cost, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="itemQuantity">Quantity</label>
                                        <input type="number" class="form-control" id="itemQuantity" min="1" value="1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary form-control" id="addItemBtn">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick scan by barcode -->
                            <div class="row mt-2">
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="barcodeInput" placeholder="Scan or enter barcode...">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" id="scanBarcodeBtn">
                                                <i class="fas fa-barcode"></i> Find
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-secondary form-control" id="clearSelectionBtn">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Selected Item Details</h5>
                                    <div id="selectedItemDetails" class="text-muted">
                                        <p class="mb-1"><i class="fas fa-box"></i> No item selected</p>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <small>Item Total:</small>
                                            <h5 id="itemTotal">LKR 0.00</h5>
                                        </div>
                                        <div class="col-6">
                                            <small>Available:</small>
                                            <h5 id="availableQty">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Temporary Items Table (Not Saved Yet) -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock"></i> Temporary Items (Not Saved)
                                <small class="text-dark">Add multiple items then save all</small>
                            </h5>
                            <div>
                                <span class="badge bg-dark text-white me-2" id="tempTotal">LKR 0.00</span>
                                <button type="button" class="btn btn-sm btn-danger" id="clearTempBtn" style="display: none;">
                                    <i class="fas fa-trash"></i> Clear Temp
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tempItemsTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Cost (LKR)</th>
                                            <th>Quantity</th>
                                            <th>Total (LKR)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tempItemsBody">
                                        @php $tempItems = session()->get('today_items_temp', []); @endphp
                                        @forelse($tempItems as $item)
                                        <tr data-temp-id="{{ $item['id'] }}">
                                            <td>{{ $item['item_code'] ?? '--' }}</td>
                                            <td>{{ $item['item_name'] }}</td>
                                            <td class="cost">LKR {{ number_format($item['cost'], 2) }}</td>
                                            <td class="quantity">{{ $item['quantity'] }}</td>
                                            <td class="total">LKR {{ number_format($item['total_cost'], 2) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger remove-temp-item" 
                                                        data-id="{{ $item['id'] }}"
                                                        data-name="{{ $item['item_name'] }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No temporary items added yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Subtotal:</th>
                                            <th id="temp-footer-total">LKR {{ number_format(collect($tempItems)->sum('total_cost'), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-4">
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-success btn-lg" id="saveAllBtn" 
                                    {{ empty($tempItems) ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i> Save All Items (Reduce Stock)
                            </button>
                        </div>
                    </div>

                    <!-- Date Search Section for Saved Items -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-search"></i> Search Saved Items by Date
                                    </h5>
                                    <div>
                                        <span class="badge bg-light text-dark" id="searchResultCount">
                                            @if(isset($viewingDate) && $viewingDate != date('Y-m-d'))
                                                Showing: {{ date('Y-m-d', strtotime($viewingDate)) }}
                                            @else
                                                Today: {{ date('Y-m-d') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="searchDate">Select Date</label>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="searchDate" 
                                                       value="{{ isset($viewingDate) ? $viewingDate : date('Y-m-d') }}"
                                                       max="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-info form-control" id="searchDateBtn">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-secondary form-control" id="resetDateBtn">
                                                    <i class="fas fa-redo"></i> Today
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Items Found</small>
                                                            <h5 id="foundCount">{{ $todayItems->count() }}</h5>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Total (LKR)</small>
                                                            <h5 id="foundTotal">LKR {{ number_format($totalToday, 2) }}</h5>
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

                    <!-- Saved Items Table (Already Saved) -->
                    <div class="card border-success">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle"></i> 
                                Saved Items 
                                <span id="savedItemsTitle">
                                    @if(isset($viewingDate) && $viewingDate != date('Y-m-d'))
                                        ({{ date('Y-m-d', strtotime($viewingDate)) }})
                                    @else
                                        (Today - {{ date('Y-m-d') }})
                                    @endif
                                </span>
                            </h5>
                            <div>
                                <!-- <span class="badge bg-light text-dark me-2" id="savedTotal">LKR {{ number_format($totalToday, 2) }}</span>
                                @if($todayItems->count() > 0 && (!isset($viewingDate) || $viewingDate == date('Y-m-d')))
                                    <button type="button" class="btn btn-sm btn-danger" id="clearAllSavedBtn">
                                        <i class="fas fa-trash-alt"></i> Clear All
                                    </button>
                                @endif -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="savedItemsTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Cost (LKR)</th>
                                            <th>Quantity</th>
                                            <th>Total (LKR)</th>
                                            <th>Date & Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="savedItemsBody">
                                        @forelse($todayItems as $item)
                                        <tr data-saved-id="{{ $item->id }}" data-date="{{ $item->created_at->format('Y-m-d') }}">
                                            <td>{{ $item->item_code ?? '--' }}</td>
                                            <td>{{ $item->item_name }}</td>
                                            <td>LKR {{ number_format($item->cost, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>LKR {{ number_format($item->total_cost, 2) }}</td>
                                            <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                @if(!isset($viewingDate) || $viewingDate == date('Y-m-d'))
                                                    <button class="btn btn-sm btn-danger remove-saved-item" 
                                                            data-id="{{ $item->id }}"
                                                            data-name="{{ $item->item_name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">View Only</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                @if(isset($viewingDate))
                                                    No items found for {{ date('Y-m-d', strtotime($viewingDate)) }}
                                                @else
                                                    No items saved today
                                                @endif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total:</th>
                                            <th colspan="3" id="footerTotal">LKR {{ number_format($totalToday, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Export Options (Optional) -->
                            @if($todayItems->count() > 0 && isset($viewingDate))
                            <div class="row mt-3">
                                <div class="col-12 text-right">
                                    <button type="button" class="btn btn-sm btn-success" id="exportExcelBtn">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" id="exportPdfBtn">
                                        <i class="fas fa-file-pdf"></i> Export to PDF
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Add these for export functionality (optional) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: '-- Search and Select Item --',
        allowClear: true,
        width: '100%'
    });

    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Stock selection change
    $('#stockSelect').on('change', function() {
        let selected = $(this).find(':selected');
        if (selected.val()) {
            let itemName = selected.data('name');
            let itemCode = selected.data('code');
            let cost = parseFloat(selected.data('cost'));
            let available = parseInt(selected.data('qty'));
            
            $('#selectedItemDetails').html(`
                <p class="mb-1"><strong>${itemName}</strong></p>
                <p class="mb-1"><small>Code: ${itemCode || 'N/A'}</small></p>
                <p class="mb-0"><small>Price: LKR ${cost.toFixed(2)}</small></p>
            `);
            
            $('#availableQty').text(available);
            $('#itemQuantity').attr('max', available);
            updateItemTotal();
        } else {
            $('#selectedItemDetails').html('<p class="mb-1"><i class="fas fa-box"></i> No item selected</p>');
            $('#availableQty').text('0');
            $('#itemTotal').text('LKR 0.00');
        }
    });

    // Quantity change
    $('#itemQuantity').on('input', function() {
        updateItemTotal();
    });

    function updateItemTotal() {
        let selected = $('#stockSelect').find(':selected');
        if (selected.val()) {
            let cost = parseFloat(selected.data('cost')) || 0;
            let qty = parseInt($('#itemQuantity').val()) || 0;
            let maxQty = parseInt(selected.data('qty')) || 0;
            
            if (qty > maxQty) {
                $('#itemQuantity').val(maxQty);
                qty = maxQty;
            }
            
            let total = qty * cost;
            $('#itemTotal').text('LKR ' + total.toFixed(2));
        }
    }

    // Add item button
    $('#addItemBtn').click(function() {
        let stockId = $('#stockSelect').val();
        let quantity = parseInt($('#itemQuantity').val());
        
        if (!stockId) {
            Swal.fire('Error', 'Please select an item', 'error');
            return;
        }
        
        if (!quantity || quantity < 1) {
            Swal.fire('Error', 'Please enter valid quantity', 'error');
            return;
        }
        
        let selected = $('#stockSelect').find(':selected');
        let maxQty = parseInt(selected.data('qty'));
        
        if (quantity > maxQty) {
            Swal.fire('Error', `Only ${maxQty} items available`, 'error');
            return;
        }
        
        // Add to temporary session
        $.post('{{ route("user.today_item.add-to-temp") }}', {
            stock_id: stockId,
            quantity: quantity
        }).done(function(response) {
            if (response.success) {
                updateTempTable(response);
                $('#stockSelect').val('').trigger('change');
                $('#itemQuantity').val(1);
                $('#barcodeInput').val('');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Added!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to add item', 'error');
        });
    });

    // Scan barcode
    $('#scanBarcodeBtn').click(function() {
        let barcode = $('#barcodeInput').val().trim();
        if (!barcode) {
            Swal.fire('Error', 'Please enter barcode', 'error');
            return;
        }
        
        // Find stock by barcode
        $.get('{{ url("user/today-item/get-stock-by-barcode") }}/' + barcode)
            .done(function(response) {
                if (response.success) {
                    // Select the item in dropdown
                    let option = $(`#stockSelect option[value="${response.stock.id}"]`);
                    if (option.length) {
                        $('#stockSelect').val(response.stock.id).trigger('change');
                        $('#barcodeInput').val('');
                    } else {
                        Swal.fire('Error', 'Item not available in list', 'error');
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Item not found', 'error');
            });
    });

    // Remove temporary item
    $(document).on('click', '.remove-temp-item', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        Swal.fire({
            title: 'Remove Item?',
            text: `Remove ${name} from temporary list?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("user.today_item.remove-from-temp", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            updateTempTable(response);
                            Swal.fire('Removed!', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to remove item', 'error');
                    }
                });
            }
        });
    });

    // Clear temporary items
    $('#clearTempBtn').click(function() {
        Swal.fire({
            title: 'Clear Temporary Items?',
            text: 'This will remove all items from temporary list',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('{{ route("user.today_item.clear-temp") }}')
                    .done(function(response) {
                        if (response.success) {
                            updateTempTable({ items: [], total: 0, count: 0 });
                            Swal.fire('Cleared!', response.message, 'success');
                        }
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to clear items', 'error');
                    });
            }
        });
    });

    // Save all items
    $('#saveAllBtn').click(function() {
        Swal.fire({
            title: 'Save All Items?',
            text: 'This will save all temporary items and reduce stock quantities',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, save all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                
                $.post('{{ route("user.today_item.save-all") }}')
                    .done(function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to save items', 'error');
                        $('#saveAllBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save All Items');
                    });
            }
        });
    });

    // Date Search Functionality
    $('#searchDateBtn').click(function() {
        let selectedDate = $('#searchDate').val();
        
        if (!selectedDate) {
            Swal.fire('Error', 'Please select a date', 'error');
            return;
        }
        
        // Show loading
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');
        $('#savedItemsBody').html(`
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </td>
            </tr>
        `);
        
        $.post('{{ route("user.today_item.get-items-by-date") }}', {
            date: selectedDate
        }).done(function(response) {
            if (response.success) {
                updateSavedItemsTable(response);
            } else {
                Swal.fire('Error', response.message, 'error');
                location.reload();
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to fetch items', 'error');
            location.reload();
        }).always(function() {
            $('#searchDateBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Search');
        });
    });

    // Reset to today
    $('#resetDateBtn').click(function() {
        $('#searchDate').val('{{ date('Y-m-d') }}');
        location.reload();
    });

    // Remove saved item
    $(document).on('click', '.remove-saved-item', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        Swal.fire({
            title: 'Remove Saved Item?',
            text: `Remove ${name} from today's list and restore stock?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("user.today_item.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Removed!', response.message, 'success');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to remove item', 'error');
                    }
                });
            }
        });
    });

    // Clear all saved items
    $('#clearAllSavedBtn').click(function() {
        Swal.fire({
            title: 'Clear All Saved Items?',
            text: 'This will remove all saved items from today and restore stock',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('{{ route("user.today_item.clear-all-saved") }}')
                    .done(function(response) {
                        if (response.success) {
                            Swal.fire('Cleared!', response.message, 'success');
                            location.reload();
                        }
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to clear items', 'error');
                    });
            }
        });
    });

    // Update temporary table function
    function updateTempTable(data) {
        let tbody = $('#tempItemsBody');
        tbody.empty();
        
        if (data.items && data.items.length > 0) {
            data.items.forEach(item => {
                tbody.append(`
                    <tr data-temp-id="${item.id}">
                        <td>${item.item_code || '--'}</td>
                        <td>${item.item_name}</td>
                        <td class="cost">LKR ${parseFloat(item.cost).toFixed(2)}</td>
                        <td class="quantity">${item.quantity}</td>
                        <td class="total">LKR ${parseFloat(item.total_cost).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-danger remove-temp-item" 
                                    data-id="${item.id}"
                                    data-name="${item.item_name}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
            
            $('#temp-footer-total').text('LKR ' + parseFloat(data.total).toFixed(2));
            $('#tempTotal').text('LKR ' + parseFloat(data.total).toFixed(2));
            $('#temp-count').text(data.count);
            $('#clearTempBtn').show();
            $('#saveAllBtn').prop('disabled', false);
        } else {
            tbody.html('<tr><td colspan="6" class="text-center">No temporary items added yet</td></tr>');
            $('#temp-footer-total').text('LKR 0.00');
            $('#tempTotal').text('LKR 0.00');
            $('#temp-count').text('0');
            $('#clearTempBtn').hide();
            $('#saveAllBtn').prop('disabled', true);
        }
        
        // Update grand total
        let savedTotal = parseFloat('{{ $totalToday }}') || 0;
        let tempTotal = data.total || 0;
        let grandTotal = savedTotal + tempTotal;
        $('#grand-total').text('LKR ' + grandTotal.toFixed(2));
    }

    // Update saved items table from AJAX response
    function updateSavedItemsTable(data) {
        let tbody = $('#savedItemsBody');
        tbody.empty();
        
        if (data.items && data.items.length > 0) {
            data.items.forEach(item => {
                tbody.append(`
                    <tr data-saved-id="${item.id}" data-date="${item.created_at.split(' ')[0]}">
                        <td>${item.item_code || '--'}</td>
                        <td>${item.item_name}</td>
                        <td>LKR ${item.cost}</td>
                        <td>${item.quantity}</td>
                        <td>LKR ${item.total_cost}</td>
                        <td>${item.created_at}</td>
                        <td>
                            <span class="badge bg-secondary">View Only</span>
                        </td>
                    </tr>
                `);
            });
            
            $('#foundCount').text(data.count);
            $('#foundTotal').text('LKR ' + data.total);
            $('#savedTotal').text('LKR ' + data.total);
            $('#footerTotal').text('LKR ' + data.total);
            $('#savedItemsTitle').text(`(${data.date})`);
            $('#searchResultCount').text('Showing: ' + data.date);
            
            // Hide clear all button for past dates
            $('#clearAllSavedBtn').hide();
        } else {
            tbody.html(`
                <tr>
                    <td colspan="7" class="text-center">
                        No items found for ${data.date}
                    </td>
                </tr>
            `);
            
            $('#foundCount').text('0');
            $('#foundTotal').text('LKR 0.00');
            $('#savedTotal').text('LKR 0.00');
            $('#footerTotal').text('LKR 0.00');
            $('#savedItemsTitle').text(`(${data.date})`);
            $('#searchResultCount').text('Showing: ' + data.date);
            $('#clearAllSavedBtn').hide();
        }
    }

    // Clear selection button
    $('#clearSelectionBtn').click(function() {
        $('#stockSelect').val('').trigger('change');
        $('#itemQuantity').val(1);
        $('#barcodeInput').val('');
    });

    // Export to Excel
    $('#exportExcelBtn').click(function() {
        let table = document.getElementById('savedItemsTable');
        let wb = XLSX.utils.table_to_book(table, {sheet: "Saved Items"});
        let date = $('#searchDate').val() || '{{ date('Y-m-d') }}';
        XLSX.writeFile(wb, `saved_items_${date}.xlsx`);
    });

    // Export to PDF
    $('#exportPdfBtn').click(function() {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF();
        
        doc.text('Saved Items Report', 14, 15);
        doc.text(`Date: ${$('#searchDate').val() || '{{ date('Y-m-d') }}'}`, 14, 22);
        
        doc.autoTable({ 
            html: '#savedItemsTable',
            startY: 30,
            theme: 'striped',
            headStyles: { fillColor: [40, 167, 69] }
        });
        
        doc.save(`saved_items_${$('#searchDate').val() || '{{ date('Y-m-d') }}'}.pdf`);
    });

    // Auto-submit on date change (optional)
    $('#searchDate').on('change', function() {
        // Uncomment below to auto-search on date change
        // $('#searchDateBtn').click();
    });
});
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
.card {
    margin-bottom: 20px;
}
.badge {
    font-size: 14px;
    padding: 8px 12px;
}
.table td, .table th {
    vertical-align: middle;
}
.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}
#searchDate {
    border-color: #17a2b8;
}
#searchDate:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}
</style>
@endpush
@endsection