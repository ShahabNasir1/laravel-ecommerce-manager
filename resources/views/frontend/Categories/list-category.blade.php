@extends('frontend.components.main')

@section('breadcrumb-heading')
<h2>List Categories</h2>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a>Categories</a></li>
<li class="breadcrumb-item active"><strong>List Categories</strong></li>
@endsection

@section('content')
@push('css')
<link rel="stylesheet" href="assets/css/dataTable/datatables.min.css">
@endpush

<!-- top: 297px;
    display: block;
    /* margin-top: 20px; */
    position: absolute; -->

<div class="ibox">
    <div class="ibox-content">
        <div id="tableLoadingMsg" class="d-none position-absolute w-100 h-100" style="background: rgba(255,255,255,0.7); z-index: 9;">
            <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                <div class="font-weight-bold">
                    <i class="fa fa-spinner fa-spin"></i>
                    Loading data from server, please wait...
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="categoriesAjaxTable">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Added at</th>
                        <th>Updated at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
                Do you want to delete the category <strong id="deleteCategoryName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <form id="deleteCategoryForm" method="POST" style="display: inline;">
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
        // 1. Show the loading span immediately when the page initializes
        $('#tableLoadingMsg').removeClass('d-none');

        // 2. Initialize DataTables
        var table = $('#categoriesAjaxTable').DataTable({
            processing: false, // Turn off native text overlay to avoid layout duplication
            serverSide: true,
            ajax: {
                url: "{{ route('categories.index') }}",
                type: "GET"
            },
            columns: [{
                    data: 'category_id'
                },
                {
                    data: 'category_name'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at_formatted'
                },
                {
                    data: 'updated_at_formatted'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: 'copy'
                },
                {
                    extend: 'csv'
                },
                {
                    extend: 'excel',
                    title: 'Categories_Export'
                },
                {
                    extend: 'pdf',
                    title: 'Categories_Export'
                },
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg').css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ],
            // 3. Hide the loading span after the initial page data finishes rendering
            initComplete: function(settings, json) {
                $('#tableLoadingMsg').addClass('d-none');
            }
        });

        // 4. Track subsequent engine actions (searching, sorting, paging)
        table.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                // Server is working: show the span
                $('#tableLoadingMsg').removeClass('d-none');
            } else {
                // Data has arrived: hide the span
                $('#tableLoadingMsg').addClass('d-none');
            }
        });

        // Event Listener for Delete Modals
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deleteRoute = "{{ route('categories.destroy', ':id') }}".replace(':id', id);

            $('#deleteCategoryName').text(name);
            $('#deleteCategoryForm').attr('action', deleteRoute);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush
@endsection