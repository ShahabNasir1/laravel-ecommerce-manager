@extends('frontend.components.main')

@section('content')

@push('css')
<link rel="stylesheet" href="assets/css/dataTable/datatables.min.css">
@endpush

<!-- Success Message Display karne ke liye -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="ibox ">
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                    <tr>
                        <th>Brand ID</th>
                        <th>Brand Name</th>
                        <th>Brand Status</th>
                        <th>Added Date</th>
                        <th>Updated Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach($allBrands as $brand)
                    <tr class="gradeX">
                        <td>{{ $brand->brand_id }}</td>
                        <td>{{ $brand->brand_name }}</td>
                        <td>
                            <!-- Status ke mutabiq green ya red label -->
                            @if($brand->brand_status == 'active')
                            <span class="label label-primary">Active</span>
                            @else
                            <span class="label label-danger">Inactive</span>
                            @endif
                        </td>
                        <!-- Dates ko behtar format mein dikhane ke liye format() use kiya -->
                        <td>{{ $brand->created_at ? $brand->created_at->format('d-M-Y H:i A') : 'N/A' }}</td>
                        <td>{{ $brand->updated_at ? $brand->updated_at->format('d-M-Y H:i A') : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('brands.edit', $brand->brand_id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-paste"></i> Edit
                            </a>

                            <!-- UPDATE HERE: data-target ko dynamic ID di hai -->
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $brand->brand_id }}">
                                <i class="fa fa-trash"></i> Delete
                            </button>

                            <!-- UPDATE HERE: id ko dynamic kiya aur body mein form lagaya -->
                            <div class="modal fade" id="deleteModal{{ $brand->brand_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-left">
                                            Do you want to delete the brand <strong>{{ $brand->brand_name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                                            
                                            <!-- Secure Form for Laravel Delete Resource Route -->
                                            <form action="{{ route('brands.destroy', $brand->brand_id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Yes, Delete It</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th>Brand ID</th>
                        <th>Brand Name</th>
                        <th>Brand Status</th>
                        <th>Added Date</th>
                        <th>Updated Date</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="assets/js/dataTable/datatables.min.js"></script>
<script src="assets/js/dataTable/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dataTables-example').DataTable({
            pageLength: 25,
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
                    title: 'BrandsList'
                },
                {
                    extend: 'pdf',
                    title: 'BrandsList'
                },
                {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });
    });
</script>
@endpush

@endsection