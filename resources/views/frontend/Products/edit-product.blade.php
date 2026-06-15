@extends('frontend.components.main')
@section('breadcrumb-heading')
<h2>Edit Product</h2>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('products.index') }}">Products</a>
</li>
<li class="breadcrumb-item active">
    <strong>Edit Product</strong>
</li>
@endsection
@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Edit Product: {{ $product->product_name }}</h5>
        </div>
        <div class="ibox-content">
            <form id="productForm" action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div id="deleted-images-container"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category</label>
                    <div class="col-sm-4">
                        <select name="productCategory" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('productCategory', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="col-sm-2 col-form-label">Brand</label>
                    <div class="col-sm-4">
                        <select name="productBrand" class="form-control">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ old('productBrand', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Product Name</label>
                    <div class="col-sm-4">
                        <input type="text" name="productName" value="{{ old('productName', $product->product_name) }}" class="form-control required">
                    </div>

                    <label for="select" class="col-sm-2 col-form-label">Select Colors</label>
                    <div class="col-sm-4">
                        <select class="form-control select2" name="colors[]" multiple="multiple" id="select">
                            @foreach($colors as $color)
                            <option value="{{ $color->color_id }}" 
                                {{ (is_array(old('colors', $product->colors->pluck('color_id')->toArray())) && in_array($color->color_id, old('colors', $product->colors->pluck('color_id')->toArray()))) ? 'selected' : '' }}>
                                {{ $color->color_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Product Sizes</label>
                    <div class="col-sm-10 mt-2">
                        @foreach($sizes as $size)
                        <div class="form-check form-check-inline" style="display: inline-block; margin-right: 15px;">
                            <input class="form-check-input"
                                type="checkbox"
                                name="size[]"
                                id="size_{{ $size->size_id }}"
                                value="{{ $size->size_id }}"
                                {{ (is_array(old('size', $product->sizes->pluck('size_id')->toArray())) && in_array($size->size_id, old('size', $product->sizes->pluck('size_id')->toArray()))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="size_{{ $size->size_id }}">{{ $size->size_name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-4">
                        <textarea name="productDescription" class="form-control required" rows="3">{{ old('productDescription', $product->description) }}</textarea>
                    </div>

                    <label class="col-sm-2 col-form-label">Product Assets</label>
                    <div class="col-sm-4">
                        @if($product->images && $product->images->count() > 0)
                            <div class="current-images-heading mb-2"><strong>Current Images:</strong></div>
                            <div class="d-flex flex-wrap gap-2 mb-3" style="display: flex; flex-wrap: wrap; gap: 15px;">
                                @foreach($product->images as $img)
                                    <div class="existing-img-wrapper" id="img_container_{{ $img->image_id }}" style="position: relative; display: inline-block;">
                                        <img src="{{ asset('storage/products/' . $img->image_url) }}" style="width:80px; height:80px; object-fit:cover; border:1px solid #ddd; border-radius:5px;">
                                        <button type="button" class="btn btn-danger btn-xs" 
                                                style="position: absolute; top: -5px; right: -5px; border-radius: 50%; padding: 1px 5px; font-size: 10px;"
                                                onclick="markImageForDeletion('{{ $img->image_id }}')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div id="image-upload-container">
                            <div class="img-input-row mb-3">
                                <label class="small text-muted d-block">Upload Additional Images</label>
                                <div class="img-input-group">
                                    <div class="preview-wrapper" style="display:none; position:relative; margin-bottom:10px;">
                                        <img class="img-preview" src="#" style="width:120px; height:120px; object-fit:cover; border:1px solid #ddd; border-radius:5px;">
                                        <button type="button" class="btn btn-danger btn-xs"
                                            style="position:absolute; top:-5px; left:105px; border-radius:50%; padding:2px 6px;"
                                            onclick="let row = this.closest('.img-input-row'); row.querySelector('input[type=file]').value = ''; row.querySelector('.preview-wrapper').style.display = 'none';">
                                            x
                                        </button>
                                    </div>
                                    <div class="input-group" style="width: 300px;">
                                        <input type="file" name="productPic[]" class="form-control" onchange="updatePreview(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2" type="button" onclick="addNewRow()">
                            <i class="fa fa-plus"></i> Add Image Field
                        </button>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-4">
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="form-control required" step="0.01">
                    </div>

                    <label class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-4">
                        <select class="form-control required" name="productStatus">
                            <option value="active" {{ old('productStatus', $product->product_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('productStatus', $product->product_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-check"></i> Update Product
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    function markImageForDeletion(imageId) {
        if (confirm('Are you sure you want to delete this image?')) {
            // Remove image preview element dynamically from the UI
            const element = document.getElementById('img_container_' + imageId);
            if (element) {
                element.remove();
            }

            // Append a hidden input field into the form tracking this deleted image's primary key ID
            const container = document.getElementById('deleted-images-container');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'deleted_images[]';
            hiddenInput.value = imageId;
            container.appendChild(hiddenInput);
        }
    }

    function updatePreview(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            let row = input.closest('.img-input-row');

            reader.onload = function(e) {
                let previewWrapper = row.querySelector('.preview-wrapper');
                let imgPreview = row.querySelector('.img-preview');
                imgPreview.src = e.target.result;
                previewWrapper.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addNewRow() {
        let container = document.getElementById('image-upload-container');
        let newRow = document.createElement('div');
        newRow.className = 'img-input-row mb-3';
        newRow.innerHTML = `
            <div class="img-input-group">
                <div class="preview-wrapper" style="display:none; position:relative; margin-bottom:10px;">
                    <img class="img-preview" src="#" style="width:120px; height:120px; object-fit:cover; border:1px solid #ddd; border-radius:5px;">
                    <button type="button" class="btn btn-danger btn-xs"
                        style="position:absolute; top:-5px; left:105px; border-radius:50%; padding:2px 6px;"
                        onclick="this.closest('.img-input-row').remove();">
                        x
                    </button>
                </div>
                <div class="input-group" style="width: 300px;">
                    <input type="file" name="productPic[]" class="form-control" onchange="updatePreview(this)">
                </div>
            </div>
        `;
        container.appendChild(newRow);
    }

    $(document).ready(function() {
        if ($.isFunction($.fn.select2)) {
            $('.select2').select2({
                placeholder: "Select Colors",
                allowClear: true
            });
        }
    });
</script>
@endpush