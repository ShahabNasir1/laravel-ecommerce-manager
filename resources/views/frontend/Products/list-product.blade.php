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
<style>
    .badge-spacing { margin-right: 3px; margin-bottom: 3px; display: inline-block; }
    .product-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e7eaec; }
</style>
@endpush

<div class="ibox ">
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->product_id }}</td>
                        <td><strong>{{ $product->product_name }}</strong></td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($product->description, 50, '...') }}</td>
                        <td>
                            @if($product->images->isNotEmpty())
                                <img src="{{ asset('storage/products/' . $product->images->first()->image_url) }}" 
                                     class="product-thumbnail" alt="Product Image" loading="lazy">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>
                            @forelse($product->sizes as $size)
                                <span class="badge badge-dark badge-spacing">{{ $size->size_name }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                        <td>
                            @forelse($product->colors as $color)
                                <span class="badge badge-dark badge-spacing">{{ $color->color_name }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                        <td>
                            @if($product->product_status == 'active')
                                <span class="badge badge-primary">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>


                        <td>
                            <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-paste"></i> Edit
                            </a>

                            <!-- UPDATE HERE: data-target ko dynamic ID di hai -->
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $product->product_id }}">
                                <i class="fa fa-trash"></i> Delete
                            </button>

                            <!-- UPDATE HERE: id ko dynamic kiya aur body mein form lagaya -->
                            <div class="modal fade" id="deleteModal{{ $product->product_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-left">
                                            Do you want to delete the product <strong>{{ $product->product_name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                                            
                                            <!-- Secure Form for Laravel Delete Resource Route -->
                                            <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" style="display: inline;">
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
                    title: 'Product_List'
                },
                {
                    extend: 'pdf',
                    title: 'Product_List'
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