@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>My Shop Details</h4>
            <div>
                @if(!$hasShop)
                    <button type="button" class="btn btn-primary" id="addShopBtn">
                        <i class="fas fa-plus"></i> Add Shop Details
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="shop-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Shop Name</th>
                            <th>Description</th>
                            <th>Address</th>
                            <th>Hotline</th>
                            <th>Email</th>
                            <th>Logo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($shop)
                        <tr>
                            <td>{{ $shop->shop_name }}</td>
                            <td>{{ Str::limit($shop->description, 20) }}</td>
                            <td>{{ Str::limit($shop->address, 20) }}</td>
                            <td>{{ $shop->hotline }}</td>
                            <td>{{ $shop->email }}</td>
                            <td>
                                @if($shop->logo_image)
                                    <img src="{{ asset('storage/'.$shop->logo_image) }}" style="width: 50px; height: 50px; object-fit: cover;" class="img-thumbnail">
                                @else
                                    No Logo
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-shop" 
                                        data-id="{{ $shop->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="7" class="text-center">No shop details found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Shop Modal -->
<div class="modal fade" id="createShopModal" tabindex="-1" role="dialog" aria-labelledby="createShopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createShopModalLabel">Add Shop Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createShopForm" method="POST" action="{{ route('user.myshop.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_shop_name">Shop Name *</label>
                                <input type="text" class="form-control" id="create_shop_name" name="shop_name" required>
                            </div>
                            <div class="form-group">
                                <label for="create_description">Description</label>
                                <textarea class="form-control" id="create_description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="create_address">Address *</label>
                                <textarea class="form-control" id="create_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_hotline">Hotline *</label>
                                <input type="text" class="form-control" id="create_hotline" name="hotline" required>
                            </div>
                            <div class="form-group">
                                <label for="create_email">Email *</label>
                                <input type="email" class="form-control" id="create_email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="create_logo_image">Logo Image</label>
                                <input type="file" class="form-control-file" id="create_logo_image" name="logo_image" accept="image/*">
                                <div class="logo-preview-container mt-2">
                                    <p>Logo Preview:</p>
                                    <div class="logo-preview-wrapper" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                        <img id="create_logo_preview" src="#" alt="Logo Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_condition_1">Condition 1</label>
                                <textarea class="form-control" id="create_condition_1" name="condition_1" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_condition_2">Condition 2</label>
                                <textarea class="form-control" id="create_condition_2" name="condition_2" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_condition_3">Condition 3</label>
                                <textarea class="form-control" id="create_condition_3" name="condition_3" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="createShopSubmitBtn">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Shop Modal -->
<div class="modal fade" id="editShopModal" tabindex="-1" role="dialog" aria-labelledby="editShopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShopModalLabel">Edit Shop Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editShopForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_shop_name">Shop Name *</label>
                                <input type="text" class="form-control" id="edit_shop_name" name="shop_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_description">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit_address">Address *</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_hotline">Hotline *</label>
                                <input type="text" class="form-control" id="edit_hotline" name="hotline" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_logo_image">Logo Image</label>
                                <input type="file" class="form-control-file" id="edit_logo_image" name="logo_image" accept="image/*">
                                <div class="logo-preview-container mt-2">
                                    <p>New Logo Preview:</p>
                                    <div class="logo-preview-wrapper" style="width: 100px; height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                                        <img id="edit_logo_preview" src="#" alt="Logo Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                    </div>
                                    <small class="text-muted">Recommended size: 100x100 pixels</small>
                                </div>
                                <div id="current-logo" class="mt-2">
                                    <p>Current Logo:</p>
                                    <div style="width: 100px; height: 100px; border: 1px solid #eee; display: flex; align-items: center; justify-content: center;">
                                        <img id="current_logo_image" src="" style="max-width: 100%; max-height: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_condition_1">Condition 1</label>
                                <textarea class="form-control" id="edit_condition_1" name="condition_1" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_condition_2">Condition 2</label>
                                <textarea class="form-control" id="edit_condition_2" name="condition_2" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_condition_3">Condition 3</label>
                                <textarea class="form-control" id="edit_condition_3" name="condition_3" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editShopSubmitBtn">Update Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

<!-- Error Message -->
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        showConfirmButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'OK',
        background: '#f8f9fa',
        iconColor: '#dc3545'
    });
</script>
@endif
<script>
$(document).ready(function() {
    // Show create modal
    $('#addShopBtn').click(function() {
        $('#createShopModal').modal('show');
    });

    // Logo preview for create form
    $("#create_logo_image").change(function() {
        previewImage(this, '#create_logo_preview');
    });

    // Logo preview for edit form
    $("#edit_logo_image").change(function() {
        previewImage(this, '#edit_logo_preview');
    });

    // Show edit modal with event delegation for dynamically loaded content
    $(document).on('click', '.edit-shop', function(e) {
        e.preventDefault();
        var shopId = $(this).data('id');
        
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("user.myshop.show", ":id") }}'.replace(':id', shopId),
            type: 'GET',
            success: function(data) {
                // Reset button state
                $('.edit-shop').prop('disabled', false).html('<i class="fas fa-edit"></i>');
                
                // Populate form fields
                $('#edit_shop_name').val(data.shop_name || '');
                $('#edit_description').val(data.description || '');
                $('#edit_address').val(data.address || '');
                $('#edit_hotline').val(data.hotline || '');
                $('#edit_email').val(data.email || '');
                $('#edit_condition_1').val(data.condition_1 || '');
                $('#edit_condition_2').val(data.condition_2 || '');
                $('#edit_condition_3').val(data.condition_3 || '');
                
                // Show current logo if exists
                if(data.logo_image) {
                    $('#current_logo_image').attr('src', '/storage/' + data.logo_image);
                    $('#current-logo').show();
                } else {
                    $('#current-logo').hide();
                }
                
                // Reset new logo preview
                $('#edit_logo_preview').attr('src', '#').hide();
                $('#edit_logo_image').val('');
                
                // Set form action
                $('#editShopForm').attr('action', '{{ route("user.myshop.update", ":id") }}'.replace(':id', shopId));
                
                // Show modal
                $('#editShopModal').modal('show');
            },
            error: function(xhr, status, error) {
                // Reset button state
                $('.edit-shop').prop('disabled', false).html('<i class="fas fa-edit"></i>');
                
                console.error('Error fetching shop data:', error);
                alert('Error loading shop details. Please try again.');
            }
        });
    });

    // Add spinner for create form submission
    $('#createShopForm').on('submit', function() {
        $('#createShopSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    });

    // Add spinner for edit form submission
    $('#editShopForm').on('submit', function() {
        $('#editShopSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    });

    // Reset form states when modals are closed
    $('#createShopModal').on('hidden.bs.modal', function() {
        $('#createShopSubmitBtn').prop('disabled', false).html('Save Details');
        $('#createShopForm')[0].reset();
        $('#create_logo_preview').attr('src', '#').hide();
    });

    $('#editShopModal').on('hidden.bs.modal', function() {
        $('#editShopSubmitBtn').prop('disabled', false).html('Update Details');
        $('#editShopForm')[0].reset();
        $('#edit_logo_preview').attr('src', '#').hide();
        $('#current-logo').hide();
    });

    // Function to preview uploaded image
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result);
                $(previewId).show();
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            $(previewId).attr('src', '#').hide();
        }
    }
});
</script>
@endpush