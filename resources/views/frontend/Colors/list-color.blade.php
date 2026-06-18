@extends('frontend.components.main')

@section('breadcrumb-heading')
<h2>List Colors</h2>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a>Colors</a></li>
<li class="breadcrumb-item active"><strong>List Colors</strong></li>
@endsection

@section('content')
@push('css')
<link rel="stylesheet" href="assets/css/dataTable/datatables.min.css">
@endpush

<div class="ibox">
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="colorsAjaxTable">
                <thead>
                    <tr>
                        <th>Color ID</th>
                        <th>Color Name</th>
                        <th>Color Status</th>
                        <th>Added At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Color ID</th>
                        <th>Color Name</th>
                        <th>Color Status</th>
                        <th>Added At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="editErrorBag" class="alert alert-danger d-none m-3"></div>


            <!-- // edit popup in color -->
            <form id="editColorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Color Name</label>
                        <input type="text" name="colorName" id="editColorName" class="form-control required">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Color Status</label>
                        <select class="form-control required" name="colorStatus" id="editColorStatus">
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
                Do you want to delete the color <strong id="deleteColorName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <form id="deleteColorForm" method="POST" style="display: inline;">
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
<script>
    $(document).ready(function() {
        // Initialize DataTables
        var table = $('#colorsAjaxTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('colors.index') }}",
                type: "GET"
            },
            columns: [
                { data: 'color_id' },
                { data: 'color_name' },
                { data: 'color_status', orderable: false, searchable: false },
                { data: 'created_at_formatted' },
                { data: 'updated_at_formatted' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy' },
                { extend: 'csv' },
                { extend: 'excel', title: 'Colors_Export' },
                { extend: 'pdf', title: 'Colors_Export' },
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg').css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ]
        });

        // 1. CLICK EVENT FOR EDIT BUTTON (Fetches active record variables asynchronously)
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var fetchUrl = "{{ route('colors.show', ':id') }}".replace(':id', id);
            var updateUrl = "{{ route('colors.update', ':id') }}".replace(':id', id);

            $('#editErrorBag').addClass('d-none').html('');
            $('#editColorForm').attr('action', updateUrl);

            $.ajax({
                url: fetchUrl,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        $('#editColorName').val(response.data.color_name);
                        $('#editColorStatus').val(response.data.color_status);
                        $('#editModal').modal('show');
                    }
                },
                error: function() {
                    alert('Failed to fetch data from server.');
                }
            });
        });

        // 2. SUBMIT EVENT FOR EDIT FORM (Asynchronous PUT updates)
        $('#editColorForm').on('submit', function(e) {
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
                        alert('Something went wrong. Please try again.');
                    }
                }
            });
        });

        // 3. CLICK EVENT FOR DELETE BUTTON (Populates contextual deletion modal text)
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deleteRoute = "{{ route('colors.destroy', ':id') }}".replace(':id', id);
            
            $('#deleteColorName').text(name);
            $('#deleteColorForm').attr('action', deleteRoute);
            $('#deleteModal').modal('show');
        });

        // 4. SUBMIT EVENT FOR DELETE FORM (Asynchronous execution execution to bypass window redirection loops)
        $('#deleteColorForm').on('submit', function(e) {
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
                        alert(response.message || 'Deletion operation rejected by the server.');
                    }
                },
                error: function() {
                    alert('An unexpected server-side layout error occurred.');
                }
            });
        });
    });
</script>
@endpush
@endsection