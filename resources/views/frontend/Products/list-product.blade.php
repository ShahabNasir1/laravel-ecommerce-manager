@extends('frontend.components.main')
@section('breadcrumb-heading')
<h2>List Products</h2>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a>Products</a>
</li>
<li class="breadcrumb-item active">
    <strong>List Products</strong>
</li>
@endsection
@section('content')

@push('css')
<link rel="stylesheet" href="assets/css/dataTable/datatables.min.css">
@endpush

<div class="ibox ">
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                    <tr>
                        <th>Product ID </th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Description</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
               <tbody>
                
               </tbody>
               
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
                    title: 'ExampleFile'
                },
                {
                    extend: 'pdf',
                    title: 'ExampleFile'
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