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
            <h4>Customer Management</h4>
            <div>
                <button type="button" class="btn btn-primary" id="addCustomerBtn">
                    <i class="fas fa-plus"></i> Add New Customer
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Search Form -->
            <form action="{{ route('user.customer.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or phone number..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('user.customer.index') }}" class="btn btn-outline-danger">Clear</a>
                        @endif
                    </div>
                </div>
            </form>
                   
            <div class="table-responsive">
                <table class="table table-striped" id="customer-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>
                                @if($customer->phone_number)
                                    {{ $customer->phone_number }}
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('user.customer.show', $customer->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button class="btn btn-sm btn-warning edit-customer" 
                                        data-id="{{ $customer->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-customer" 
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No customers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer text-right">
            <nav class="d-inline-block" aria-label="Pagination">
                {{ $customers->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    </div>
</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createCustomerForm" method="POST" action="{{ route('user.customer.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="create_name">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="create_phone_number">Phone Number <small class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" id="create_phone_number" name="phone_number" placeholder="e.g., 071 234 5678">
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_customer_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone_number">Phone Number <small class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" id="edit_phone_number" name="phone_number">
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCustomerModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete_customer_name"></strong>? This action cannot be undone.</p>
                <p class="text-danger">This will permanently remove the customer and all their fabric calculations.</p>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteCustomerForm" method="POST" style="display: inline;">
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

        // Handle Add Customer button click
        $('#addCustomerBtn').on('click', function() {
            $('#createCustomerModal').modal('show');
        });

        // Handle Edit button click
        $(document).on('click', '.edit-customer', function() {
            var customerId = $(this).data('id');
            
            // Fetch customer data via AJAX
            $.get("{{ route('user.customer.edit', ':id') }}".replace(':id', customerId), function(data) {
                // Populate the edit form
                $('#edit_customer_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_phone_number').val(data.phone_number || '');
                
                // Set the form action URL
                var actionUrl = "{{ route('user.customer.update', ':id') }}";
                actionUrl = actionUrl.replace(':id', customerId);
                $('#editCustomerForm').attr('action', actionUrl);
                
                // Show the modal
                $('#editCustomerModal').modal('show');
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load customer data',
                    confirmButtonColor: '#0d6efd'
                });
            });
        });

        // Handle Delete button click
        $(document).on('click', '.delete-customer', function() {
            var customerId = $(this).data('id');
            var customerName = $(this).data('name');
            
            // Set the customer name in the confirmation message
            $('#delete_customer_name').text(customerName);
            
            // Set the form action URL
            var actionUrl = "{{ route('user.customer.destroy', ':id') }}";
            actionUrl = actionUrl.replace(':id', customerId);
            $('#deleteCustomerForm').attr('action', actionUrl);
            
            // Show the modal
            $('#deleteCustomerModal').modal('show');
        });

        // Clear modal forms when modals are hidden
        $('#createCustomerModal').on('hidden.bs.modal', function () {
            $('#createCustomerForm')[0].reset();
            $('#createCustomerForm button[type="submit"]').prop('disabled', false).html('Save Customer');
        });

        $('#editCustomerModal').on('hidden.bs.modal', function () {
            $('#editCustomerForm button[type="submit"]').prop('disabled', false).html('Update Customer');
        });

        // Handle form submissions
        $('#createCustomerForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        });

        $('#editCustomerForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        });

        $('#deleteCustomerForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
        });

        // Auto-focus on first input when modal opens
        $('#createCustomerModal').on('shown.bs.modal', function () {
            $('#create_name').focus();
        });
        
        $('#editCustomerModal').on('shown.bs.modal', function () {
            $('#edit_name').focus();
        });
    });
</script>
@endpush
@endsection