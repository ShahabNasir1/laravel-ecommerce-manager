@extends('frontend.components.main')

@section('breadcrumb-heading')
<h2>List Sizes</h2>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">
    <a>Sizes</a>
</li>
<li class="breadcrumb-item active">
    <strong>List Sizes</strong>
</li>
@endsection

@section('content')

@push('css')
<link rel="stylesheet" href="assets/css/dataTable/datatables.min.css">
@endpush

<button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#addSizeModal">
    <i class="fas fa-plus"></i> Add New Size
</button>

<div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Size</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="addErrorBag" class="alert alert-danger d-none m-3"></div>
            
            <form id="addSizeForm" action="{{ route('sizes.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="sizeName">Size Name</label>
                        <input type="text" name="sizeName" id="sizeName" class="form-control" placeholder="e.g., Large, XL, 42" data-validate="text">
                    </div>

                    <div class="form-group mt-3">
                        <label for="sizeStatus">Status</label>
                        <select class="form-control" name="sizeStatus" id="sizeStatus" data-validate="text">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Size</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ibox">
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="sizesAjaxTable">
                <thead>
                    <tr>
                        <th>Size ID</th>
                        <th>Size Name</th>
                        <th>Size Status</th>
                        <th>Added At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Size</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="editErrorBag" class="alert alert-danger d-none m-3"></div>
            
            <form id="editSizeForm" method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Size Name</label>
                        <input type="text" name="sizeName" id="editSizeName" class="form-control" data-validate="text">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Size Status</label>
                        <select class="form-control" name="sizeStatus" id="editSizeStatus" data-validate="text">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                Do you want to delete the size <strong id="deleteSizeName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <form id="deleteSizeForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete It</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="assets/js/dataTable/datatables.min.js"></script>
<script src="assets/js/dataTable/dataTables.bootstrap4.min.js"></script>
@push('js')
<script src="assets/js/dataTable/datatables.min.js"></script>
<script src="assets/js/dataTable/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Server-Side Asynchronous DataTable
        var table = $('#sizesAjaxTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ordering: false,
            ajax: {
                url: "{{ route('sizes.index') }}",
                type: "GET"
            },
            columns: [
                { data: 'size_id' },
                { data: 'size_name' },
                { data: 'size_status', orderable: false, searchable: false },
                { data: 'created_at_formatted' },
                { data: 'updated_at_formatted' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy' },
                { extend: 'csv' },
                { extend: 'excel', title: 'Sizes_Export' },
                { extend: 'pdf', title: 'Sizes_Export' },
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg').css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ]
        });

        // ==========================================
        // SUBMIT LISTENER FOR ADD NEW FORM (AJAX)
        // ==========================================
        $('#addSizeForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);

            // Call the global master validation engine directly
            if (typeof window.validateForm === 'function' && !window.validateForm(form)) {
                return false;
            }

            var actionUrl = form.attr('action');
            $('#addErrorBag').addClass('d-none').html('');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('#addSizeModal').modal('hide');
                        form[0].reset(); 
                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback-custom').remove();
                        table.ajax.reload(null, false); 
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        $('#addErrorBag').removeClass('d-none').html(errorHtml);
                    } else {
                        alert('Something went wrong processing your new entry.');
                    }
                }
            });
        });

        // ==========================================
        // CLICK LISTENER FOR EDIT MODAL
        // ==========================================
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var fetchUrl = "{{ route('sizes.show', ':id') }}".replace(':id', id);
            var updateUrl = "{{ route('sizes.update', ':id') }}".replace(':id', id);

            $('#editErrorBag').addClass('d-none').html('');
            
            $('#editSizeForm').find('.is-invalid').removeClass('is-invalid');
            $('#editSizeForm').find('.invalid-feedback-custom').remove();
            $('#editSizeForm').attr('action', updateUrl);

            $.ajax({
                url: fetchUrl,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        $('#editSizeName').val(response.data.size_name);
                        $('#editSizeStatus').val(response.data.size_status);
                        $('#editModal').modal('show');
                    }
                },
                error: function() {
                    alert('Failed to retrieve size data from the server.');
                }
            });
        });

        // ==========================================
        // SUBMIT LISTENER FOR EDIT FORM (AJAX PUT)
        // ==========================================
        $('#editSizeForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);

            // Call the global master validation engine directly
            if (typeof window.validateForm === 'function' && !window.validateForm(form)) {
                return false;
            }

            var actionUrl = form.attr('action');

            $.ajax({
                url: actionUrl,
                type: "POST", 
                data: form.serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        table.ajax.reload(null, false); 
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        $('#editErrorBag').removeClass('d-none').html(errorHtml);
                    } else {
                        alert('Something went wrong processing updates.');
                    }
                }
            });
        });

        // ==========================================
        // CLICK LISTENER FOR DELETE MODAL
        // ==========================================
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deleteRoute = "{{ route('sizes.destroy', ':id') }}".replace(':id', id);
            
            $('#deleteSizeName').text(name);
            $('#deleteSizeForm').attr('action', deleteRoute);
            $('#deleteModal').modal('show');
        });

        // ==========================================
        // SUBMIT LISTENER FOR DELETE FORM (AJAX)
        // ==========================================
        $('#deleteSizeForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = form.attr('action');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        alert(response.message || 'Deletion execution rejected.');
                    }
                },
                error: function() {
                    alert('Server-side connectivity failure.');
                }
            });
        });
    });
</script>
@endpush
@endpush
@endsection