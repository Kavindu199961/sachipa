@extends('layouts.app')

@section('content')
<div class="col-12 col-md-12 col-lg-12">
    <!-- Success Message -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '',
            text: '{{ session('success') }}',
            showConfirmButton: true,
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK',
            background: '#f8f9fa',
            iconColor: '#28a745'
        });
    </script>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Customer Details: {{ $customer->name }}</h4>
            <div>
                <a href="{{ route('user.customer.index') }}" class="btn btn-success">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button type="button" class="btn btn-primary" id="addFabricBtn" data-toggle="modal" data-target="#addFabricModal">
                    <i class="fas fa-plus"></i> Add Fabric Calculation
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Customer Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">Name</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td>{{ $customer->phone_number ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Registered Date</th>
                            <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Summary</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">Total Calculations</th>
                            <td>{{ $fabricCalculations->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Fabric Calculations -->
            <h5 class="mb-3">Fabric Calculations</h5>
            
            @if($fabricCalculations->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Stick</th>
                            <th>One Rali (cm)</th>
                            <th>Two Rali (cm)</th>
                            <th>Tree Rali (cm)</th>
                            <th>Four Rali (cm)</th>
                            <th>Ilets</th>
                            <th>Sum (1+4)</th>
                            <th>Sum (2+3)</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fabricCalculations as $index => $fabric)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $fabric->stick ?? '--' }}</td>
                            <td>{{ $fabric->one_rali ? number_format($fabric->one_rali, 2) : '--' }}</td>
                            <td>{{ $fabric->two_rali ? number_format($fabric->two_rali, 2) : '--' }}</td>
                            <td>{{ $fabric->tree_rali ? number_format($fabric->tree_rali, 2) : '--' }}</td>
                            <td>{{ $fabric->four_rali ? number_format($fabric->four_rali, 2) : '--' }}</td>
                            <td>{{ $fabric->ilets ? number_format($fabric->ilets, 0) : '--' }}</td>
                            <td>{{ number_format($fabric->sum_one_four, 2) }}</td>
                            <td>{{ number_format($fabric->sum_two_tree, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($fabric->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-fabric" 
                                        data-id="{{ $fabric->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-fabric" 
                                        data-id="{{ $fabric->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-primary">
                        <tr>
                            <th>Totals</th>
                            <th>{{ number_format($totals['stick'], 1) }}</th>
                            <th>{{ number_format($totals['one_rali'], 2) }}</th>
                            <th>{{ number_format($totals['two_rali'], 2) }}</th>
                            <th>{{ number_format($totals['tree_rali'], 2) }}</th>
                            <th>{{ number_format($totals['four_rali'], 2) }}</th>
                            <th>{{ number_format($totals['ilets'], 0) }}</th>
                            <th>{{ number_format($totals['one_rali'] + $totals['four_rali'], 2) }}</th>
                            <th>{{ number_format($totals['two_rali'] + $totals['tree_rali'], 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                No fabric calculations found for this customer. Click "Add Fabric Calculation" to add one.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Fabric Calculation Modal -->
<div class="modal fade" id="addFabricModal" tabindex="-1" role="dialog" aria-labelledby="addFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFabricModalLabel">Add Fabric Calculation for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addFabricForm" method="POST" action="{{ route('user.customer.fabric.storeMultiple', $customer->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addNewRow">
                            <i class="fas fa-plus"></i> Add New Row
                        </button>
                        <small class="text-muted ml-2">Click to add multiple fabric calculations</small>
                    </div>
                    
                    <div id="fabric-rows-container">
                        <!-- First Row (Template) -->
                        <div class="fabric-row border rounded p-3 mb-3" data-row-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Row #1</h6>
                                <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                            <div class="form-row">
                                <!-- Stick Column -->
                                <div class="col-12 col-sm-6 col-md-2 mb-2">
                                    <div class="form-group mb-0">
                                        <label class="small">Stick</label>
                                        <input type="number" step="0.01" min="0"
                                               class="form-control form-control-sm stick-input"
                                               name="rows[0][stick]"
                                               placeholder="Stick"
                                               data-field="stick"
                                               data-row="0"
                                               autofocus>
                                        <small class="text-muted">As entered</small>
                                    </div>
                                </div>
                                
                                <!-- One Rali Column -->
                                <div class="col-12 col-sm-6 col-md-2 mb-2">
                                    <div class="form-group mb-0">
                                        <label class="small">One Rali</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0"
                                                   class="form-control form-control-sm rali-input"
                                                   name="rows[0][one_rali]"
                                                   placeholder="One"
                                                   data-field="one_rali"
                                                   data-row="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="one_rali" style="min-width: 45px;">0</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">×34: <span class="rali-saved" data-saved="one_rali">0</span></small>
                                    </div>
                                </div>
                                
                                <!-- Two Rali Column -->
                                <div class="col-12 col-sm-6 col-md-2 mb-2">
                                    <div class="form-group mb-0">
                                        <label class="small">Two Rali</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0"
                                                   class="form-control form-control-sm rali-input"
                                                   name="rows[0][two_rali]"
                                                   placeholder="Two"
                                                   data-field="two_rali"
                                                   data-row="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="two_rali" style="min-width: 45px;">0</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">×34: <span class="rali-saved" data-saved="two_rali">0</span></small>
                                    </div>
                                </div>
                                
                                <!-- Tree Rali Column -->
                                <div class="col-12 col-sm-6 col-md-2 mb-2">
                                    <div class="form-group mb-0">
                                        <label class="small">Tree Rali</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0"
                                                   class="form-control form-control-sm rali-input"
                                                   name="rows[0][tree_rali]"
                                                   placeholder="Tree"
                                                   data-field="tree_rali"
                                                   data-row="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="tree_rali" style="min-width: 45px;">0</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">×34: <span class="rali-saved" data-saved="tree_rali">0</span></small>
                                    </div>
                                </div>
                                
                                <!-- Four Rali Column -->
                                <div class="col-12 col-sm-6 col-md-2 mb-2">
                                    <div class="form-group mb-0">
                                        <label class="small">Four Rali</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0"
                                                   class="form-control form-control-sm rali-input"
                                                   name="rows[0][four_rali]"
                                                   placeholder="Four"
                                                   data-field="four_rali"
                                                   data-row="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="four_rali" style="min-width: 45px;">0</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">×34: <span class="rali-saved" data-saved="four_rali">0</span></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-success py-2 mb-0">
                                        <strong>Ilets:</strong> <span class="ilets-preview">0.00</span>
                                        <small class="text-muted">(Sum of Ralis ÷ 17)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">Add Calculations</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Fabric Calculation Modal -->
<div class="modal fade" id="editFabricModal" tabindex="-1" role="dialog" aria-labelledby="editFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFabricModalLabel">Edit Fabric Calculation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFabricForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <!-- Stick Column -->
                        <div class="col-12 col-sm-6 col-md-2 mb-2">
                            <div class="form-group mb-0">
                                <label for="edit_stick" class="small">Stick</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="edit_stick" name="stick" placeholder="Stick">
                                <small class="text-muted">As entered</small>
                            </div>
                        </div>
                        
                        <!-- One Rali Column -->
                        <div class="col-12 col-sm-6 col-md-2 mb-2">
                            <div class="form-group mb-0">
                                <label for="edit_one_rali" class="small">One Rali</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_one_rali" name="one_rali" placeholder="One">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white p-1" id="edit_one_rali_display" style="min-width: 45px;">0</span>
                                    </div>
                                </div>
                                <small class="text-muted">×34: <span id="edit_one_rali_saved">0</span></small>
                            </div>
                        </div>
                        
                        <!-- Two Rali Column -->
                        <div class="col-12 col-sm-6 col-md-2 mb-2">
                            <div class="form-group mb-0">
                                <label for="edit_two_rali" class="small">Two Rali</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_two_rali" name="two_rali" placeholder="Two">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white p-1" id="edit_two_rali_display" style="min-width: 45px;">0</span>
                                    </div>
                                </div>
                                <small class="text-muted">×34: <span id="edit_two_rali_saved">0</span></small>
                            </div>
                        </div>
                        
                        <!-- Tree Rali Column -->
                        <div class="col-12 col-sm-6 col-md-2 mb-2">
                            <div class="form-group mb-0">
                                <label for="edit_tree_rali" class="small">Tree Rali</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_tree_rali" name="tree_rali" placeholder="Tree">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white p-1" id="edit_tree_rali_display" style="min-width: 45px;">0</span>
                                    </div>
                                </div>
                                <small class="text-muted">×34: <span id="edit_tree_rali_saved">0</span></small>
                            </div>
                        </div>
                        
                        <!-- Four Rali Column -->
                        <div class="col-12 col-sm-6 col-md-2 mb-2">
                            <div class="form-group mb-0">
                                <label for="edit_four_rali" class="small">Four Rali</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_four_rali" name="four_rali" placeholder="Four">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white p-1" id="edit_four_rali_display" style="min-width: 45px;">0</span>
                                    </div>
                                </div>
                                <small class="text-muted">×34: <span id="edit_four_rali_saved">0</span></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="alert alert-success py-2 mb-0">
                                <strong>Ilets:</strong> <span id="edit_ilets_preview">0.00</span> <small class="text-muted">(Sum of Ralis ÷ 17)</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update Calculation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Fabric Confirmation Modal -->
<div class="modal fade" id="deleteFabricModal" tabindex="-1" role="dialog" aria-labelledby="deleteFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFabricModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this fabric calculation? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteFabricForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // CSRF token for AJAX requests
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ─── Field order for Enter-key navigation ───────────────────────────────
    var fieldOrder = ['stick', 'one_rali', 'two_rali', 'tree_rali', 'four_rali'];

    // ─── Build a fresh row HTML using a row index ────────────────────────────
    function buildRowHtml(idx) {
        return `
        <div class="fabric-row border rounded p-3 mb-3" data-row-index="${idx}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Row #${idx + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <div class="form-row">

                <!-- Stick -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <div class="form-group mb-0">
                        <label class="small">Stick</label>
                        <input type="number" step="0.01" min="0"
                               class="form-control form-control-sm stick-input"
                               name="rows[${idx}][stick]"
                               placeholder="Stick"
                               data-field="stick"
                               data-row="${idx}">
                        <small class="text-muted">As entered</small>
                    </div>
                </div>

                <!-- One Rali -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <div class="form-group mb-0">
                        <label class="small">One Rali</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm rali-input"
                                   name="rows[${idx}][one_rali]"
                                   placeholder="One"
                                   data-field="one_rali"
                                   data-row="${idx}">
                            <div class="input-group-append">
                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="one_rali" style="min-width:45px;">0</span>
                            </div>
                        </div>
                        <small class="text-muted">×34: <span class="rali-saved" data-saved="one_rali">0</span></small>
                    </div>
                </div>

                <!-- Two Rali -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <div class="form-group mb-0">
                        <label class="small">Two Rali</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm rali-input"
                                   name="rows[${idx}][two_rali]"
                                   placeholder="Two"
                                   data-field="two_rali"
                                   data-row="${idx}">
                            <div class="input-group-append">
                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="two_rali" style="min-width:45px;">0</span>
                            </div>
                        </div>
                        <small class="text-muted">×34: <span class="rali-saved" data-saved="two_rali">0</span></small>
                    </div>
                </div>

                <!-- Tree Rali -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <div class="form-group mb-0">
                        <label class="small">Tree Rali</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm rali-input"
                                   name="rows[${idx}][tree_rali]"
                                   placeholder="Tree"
                                   data-field="tree_rali"
                                   data-row="${idx}">
                            <div class="input-group-append">
                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="tree_rali" style="min-width:45px;">0</span>
                            </div>
                        </div>
                        <small class="text-muted">×34: <span class="rali-saved" data-saved="tree_rali">0</span></small>
                    </div>
                </div>

                <!-- Four Rali -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <div class="form-group mb-0">
                        <label class="small">Four Rali</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm rali-input"
                                   name="rows[${idx}][four_rali]"
                                   placeholder="Four"
                                   data-field="four_rali"
                                   data-row="${idx}">
                            <div class="input-group-append">
                                <span class="input-group-text bg-info text-white p-1 rali-display" data-display="four_rali" style="min-width:45px;">0</span>
                            </div>
                        </div>
                        <small class="text-muted">×34: <span class="rali-saved" data-saved="four_rali">0</span></small>
                    </div>
                </div>

            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="alert alert-success py-2 mb-0">
                        <strong>Ilets:</strong> <span class="ilets-preview">0.00</span>
                        <small class="text-muted">(Sum of Ralis ÷ 17)</small>
                    </div>
                </div>
            </div>
        </div>`;
    }

    // ─── Add New Row button ──────────────────────────────────────────────────
    $('#addNewRow').on('click', function () {
        var newIdx = $('#fabric-rows-container .fabric-row').length;
        $('#fabric-rows-container').append(buildRowHtml(newIdx));
        // Focus first input of newly added row
        $('#fabric-rows-container .fabric-row:last .stick-input').focus();
    });

    // ─── Remove Row ──────────────────────────────────────────────────────────
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.fabric-row').remove();
        reindexRows();
    });

    // ─── Reindex all rows after removal ─────────────────────────────────────
    function reindexRows() {
        $('#fabric-rows-container .fabric-row').each(function (i) {
            var $row = $(this);
            $row.attr('data-row-index', i);
            $row.find('h6').text('Row #' + (i + 1));

            // Update name + data-row for every input
            $row.find('input[data-field]').each(function () {
                var field = $(this).attr('data-field');
                $(this).attr('name', 'rows[' + i + '][' + field + ']')
                       .attr('data-row', i);
            });
        });
    }

    // ─── Live calculations for Add modal ─────────────────────────────────────
    $(document).on('input', '#fabric-rows-container .rali-input', function () {
        updateRowCalculations($(this).closest('.fabric-row'));
    });

    function updateRowCalculations($row) {
        var fields   = ['one_rali', 'two_rali', 'tree_rali', 'four_rali'];
        var total    = 0;

        fields.forEach(function (field) {
            var val    = parseFloat($row.find('input[data-field="' + field + '"]').val()) || 0;
            var saved  = val * 34;
            total     += saved;
            $row.find('.rali-display[data-display="'  + field + '"]').text(saved.toFixed(2));
            $row.find('.rali-saved[data-saved="'      + field + '"]').text(saved.toFixed(2));
        });

        var ilets = total > 0 ? total / 17 : 0;
        $row.find('.ilets-preview').text(ilets.toFixed(2));
    }

    // ─── Enter-key navigation inside Add modal rows ──────────────────────────
    // Navigates: stick → one_rali → two_rali → tree_rali → four_rali
    // On four_rali Enter: adds a new row and focuses its stick input
    $(document).on('keydown', '#fabric-rows-container input[data-field]', function (e) {
        if (e.which !== 13) return; // Only Enter key
        e.preventDefault();

        var $input    = $(this);
        var field     = $input.attr('data-field');
        var $row      = $input.closest('.fabric-row');
        var fieldIdx  = fieldOrder.indexOf(field);

        if (fieldIdx < fieldOrder.length - 1) {
            // Move to next field in the same row
            var nextField = fieldOrder[fieldIdx + 1];
            $row.find('input[data-field="' + nextField + '"]').focus();
        } else {
            // Last field (four_rali) — add a new row and go to its stick
            var newIdx = $('#fabric-rows-container .fabric-row').length;
            $('#fabric-rows-container').append(buildRowHtml(newIdx));
            $('#fabric-rows-container .fabric-row:last .stick-input').focus();
        }
    });

    // ─── Handle Edit button click ────────────────────────────────────────────
    $(document).on('click', '.edit-fabric', function () {
        var fabricId   = $(this).data('id');
        var customerId = '{{ $customer->id }}';

        $.get("{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId + "/edit", function (data) {
            $('#edit_stick').val(data.display_stick || '');
            $('#edit_one_rali').val(data.display_one_rali || '');
            $('#edit_two_rali').val(data.display_two_rali || '');
            $('#edit_tree_rali').val(data.display_tree_rali || '');
            $('#edit_four_rali').val(data.display_four_rali || '');

            updateEditCalculations();

            $('#editFabricForm').attr('action', "{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId);
            $('#editFabricModal').modal('show');
        }).fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load fabric calculation data', confirmButtonColor: '#0d6efd' });
        });
    });

    // Enter-key navigation for Edit modal
    $('#editFabricModal').on('keydown', 'input', function (e) {
        if (e.which !== 13) return;
        e.preventDefault();
        var editOrder = ['edit_stick', 'edit_one_rali', 'edit_two_rali', 'edit_tree_rali', 'edit_four_rali'];
        var currentId = $(this).attr('id');
        var idx       = editOrder.indexOf(currentId);
        if (idx >= 0 && idx < editOrder.length - 1) {
            $('#' + editOrder[idx + 1]).focus();
        }
    });

    function updateEditCalculations() {
        var fields = ['one_rali', 'two_rali', 'tree_rali', 'four_rali'];
        var total  = 0;
        fields.forEach(function (field) {
            var val   = parseFloat($('#edit_' + field).val()) || 0;
            var saved = val * 34;
            total    += saved;
            $('#edit_' + field + '_display').text(saved.toFixed(2));
            $('#edit_' + field + '_saved').text(saved.toFixed(2));
        });
        var ilets = total > 0 ? total / 17 : 0;
        $('#edit_ilets_preview').text(ilets.toFixed(2));
    }

    $('#edit_one_rali, #edit_two_rali, #edit_tree_rali, #edit_four_rali').on('input', function () {
        updateEditCalculations();
    });

    // ─── Handle Delete button click ──────────────────────────────────────────
    $(document).on('click', '.delete-fabric', function () {
        var fabricId   = $(this).data('id');
        var customerId = '{{ $customer->id }}';
        $('#deleteFabricForm').attr('action', "{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId);
        $('#deleteFabricModal').modal('show');
    });

    // ─── Helper: force-clean Bootstrap modal leftovers ───────────────────────
    function cleanupModal() {
        // Remove any lingering backdrops
        $('.modal-backdrop').remove();
        // Remove classes Bootstrap adds to <body>
        $('body').removeClass('modal-open').css('padding-right', '');
        // Ensure all modals are truly hidden
        $('.modal').removeClass('show').hide().removeAttr('aria-modal').attr('aria-hidden', 'true');
    }

    // ─── Reset Add modal on close ────────────────────────────────────────────
    $('#addFabricModal').on('hidden.bs.modal', function () {
        $('#fabric-rows-container').html(buildRowHtml(0));
        $('#fabric-rows-container .remove-row').hide();
        $('#addSubmitBtn').prop('disabled', false).html('Add Calculations');
        cleanupModal();
    });

    $('#editFabricModal').on('hidden.bs.modal', function () {
        $('#editSubmitBtn').prop('disabled', false).html('Update Calculation');
        cleanupModal();
    });

    $('#deleteFabricModal').on('hidden.bs.modal', function () {
        cleanupModal();
    });

    // ─── Disable submit buttons on submission ────────────────────────────────
    $('#addFabricForm').on('submit', function () {
        $('#addSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
    });
    $('#editFabricForm').on('submit', function () {
        $('#editSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    });
    $('#deleteFabricForm').on('submit', function () {
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
    });

    // ─── Auto-focus on modal open ────────────────────────────────────────────
    $('#addFabricModal').on('shown.bs.modal', function () {
        $('#fabric-rows-container .fabric-row:first .stick-input').focus();
    });
    $('#editFabricModal').on('shown.bs.modal', function () {
        $('#edit_stick').focus();
    });

});
</script>
@endpush