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
                <button type="button" class="btn btn-primary" id="addFabricBtn">
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFabricModalLabel">Add Fabric Calculation for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addFabricForm" method="POST" action="{{ route('user.customer.fabric.store', $customer->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Rali values will be automatically multiplied by 34 when saved. Stick values are saved as entered.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stick">Stick Value</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="stick" name="stick" placeholder="Enter stick value">
                                <small class="text-muted">Saved as entered (no multiplication)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="one_rali">One Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="one_rali" name="one_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="one_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="one_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="two_rali">Two Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="two_rali" name="two_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="two_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="two_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tree_rali">Tree Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="tree_rali" name="tree_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="tree_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="tree_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="four_rali">Four Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="four_rali" name="four_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="four_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="four_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <strong>Ilets Calculation:</strong> <span id="ilets_preview">0.00</span> (Sum of all Ralis ÷ 17)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Calculation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Fabric Calculation Modal -->
<div class="modal fade" id="editFabricModal" tabindex="-1" role="dialog" aria-labelledby="editFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Rali values will be automatically multiplied by 34 when saved. Stick values are saved as entered.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_stick">Stick Value</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_stick" name="stick" placeholder="Enter stick value">
                                <small class="text-muted">Saved as entered (no multiplication)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_one_rali">One Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_one_rali" name="one_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="edit_one_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="edit_one_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_two_rali">Two Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_two_rali" name="two_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="edit_two_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="edit_two_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tree_rali">Tree Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_tree_rali" name="tree_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="edit_tree_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="edit_tree_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_four_rali">Four Rali (Input Value)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit_four_rali" name="four_rali">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white" id="edit_four_rali_display">0.00</span>
                                    </div>
                                </div>
                                <small class="text-muted">Saved as: <span id="edit_four_rali_saved">0.00</span> (×34)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <strong>Ilets Calculation:</strong> <span id="edit_ilets_preview">0.00</span> (Sum of all Ralis ÷ 17)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Calculation</button>
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
            <div class="modal-footer bg-whitesmoke br">
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

@push('scripts')
<script>
    $(document).ready(function () {
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle Add Fabric button click
        $('#addFabricBtn').on('click', function() {
            $('#addFabricModal').modal('show');
        });

        // Live calculation for Add Form
        $('#one_rali, #two_rali, #tree_rali, #four_rali').on('input', function() {
            updateLiveCalculations('', '#');
        });

        // Live calculation for Edit Form
        $('#edit_one_rali, #edit_two_rali, #edit_tree_rali, #edit_four_rali').on('input', function() {
            updateLiveCalculations('edit_', '#edit_');
        });

        function updateLiveCalculations(prefix, selectorPrefix) {
            // Get values
            var oneRali = parseFloat($(selectorPrefix + 'one_rali').val()) || 0;
            var twoRali = parseFloat($(selectorPrefix + 'two_rali').val()) || 0;
            var treeRali = parseFloat($(selectorPrefix + 'tree_rali').val()) || 0;
            var fourRali = parseFloat($(selectorPrefix + 'four_rali').val()) || 0;
            
            // Calculate saved values (multiply by 34)
            var oneRaliSaved = oneRali * 34;
            var twoRaliSaved = twoRali * 34;
            var treeRaliSaved = treeRali * 34;
            var fourRaliSaved = fourRali * 34;
            
            // Update display fields
            $(selectorPrefix + 'one_rali_display').text(oneRaliSaved.toFixed(2));
            $(selectorPrefix + 'two_rali_display').text(twoRaliSaved.toFixed(2));
            $(selectorPrefix + 'tree_rali_display').text(treeRaliSaved.toFixed(2));
            $(selectorPrefix + 'four_rali_display').text(fourRaliSaved.toFixed(2));
            
            $(selectorPrefix + 'one_rali_saved').text(oneRaliSaved.toFixed(2));
            $(selectorPrefix + 'two_rali_saved').text(twoRaliSaved.toFixed(2));
            $(selectorPrefix + 'tree_rali_saved').text(treeRaliSaved.toFixed(2));
            $(selectorPrefix + 'four_rali_saved').text(fourRaliSaved.toFixed(2));
            
            // Calculate ilets
            var totalRali = oneRaliSaved + twoRaliSaved + treeRaliSaved + fourRaliSaved;
            var ilets = totalRali > 0 ? totalRali / 17 : 0;
            $(selectorPrefix + 'ilets_preview').text(ilets.toFixed(2));
        }

        // Handle Edit button click
        $(document).on('click', '.edit-fabric', function() {
            var fabricId = $(this).data('id');
            var customerId = '{{ $customer->id }}';
            
            // Fetch fabric data via AJAX
            $.get("{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId + "/edit", function(data) {
                // Populate the edit form with display values
                $('#edit_stick').val(data.display_stick || '');
                $('#edit_one_rali').val(data.display_one_rali || '');
                $('#edit_two_rali').val(data.display_two_rali || '');
                $('#edit_tree_rali').val(data.display_tree_rali || '');
                $('#edit_four_rali').val(data.display_four_rali || '');
                
                // Update live calculations
                updateLiveCalculations('edit_', '#edit_');
                
                // Set the form action URL
                var actionUrl = "{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId;
                $('#editFabricForm').attr('action', actionUrl);
                
                // Show the modal
                $('#editFabricModal').modal('show');
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load fabric calculation data',
                    confirmButtonColor: '#0d6efd'
                });
            });
        });

        // Handle Delete button click
        $(document).on('click', '.delete-fabric', function() {
            var fabricId = $(this).data('id');
            var customerId = '{{ $customer->id }}';
            
            // Set the form action URL
            var actionUrl = "{{ url('user/customer') }}/" + customerId + "/fabric/" + fabricId;
            $('#deleteFabricForm').attr('action', actionUrl);
            
            // Show the modal
            $('#deleteFabricModal').modal('show');
        });

        // Clear modal forms when modals are hidden
        $('#addFabricModal').on('hidden.bs.modal', function () {
            $('#addFabricForm')[0].reset();
            $('#addFabricForm button[type="submit"]').prop('disabled', false).html('Add Calculation');
            updateLiveCalculations('', '#');
        });

        $('#editFabricModal').on('hidden.bs.modal', function () {
            $('#editFabricForm button[type="submit"]').prop('disabled', false).html('Update Calculation');
        });

        // Handle form submissions
        $('#addFabricForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        });

        $('#editFabricForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        });

        $('#deleteFabricForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
        });

        // Auto-focus on first input when modal opens
        $('#addFabricModal').on('shown.bs.modal', function () {
            $('#stick').focus();
        });
        
        $('#editFabricModal').on('shown.bs.modal', function () {
            $('#edit_stick').focus();
        });
    });
</script>
@endpush
@endsection