@extends('frontend.components.main')
@section('breadcrumb-heading')
<h2>Add Product</h2>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a>Products</a>
</li>
<li class="breadcrumb-item active">
    <strong>Add Product</strong>
</li>
@endsection
@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Add New Product</h5>
        </div>
        <div class="ibox-content">
            <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category</label>
                    <div class="col-sm-4">
                        <select class="form-control required" name="productCategory">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('productCategory') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="col-sm-2 col-form-label">Brand</label>
                    <div class="col-sm-4">
                        <select class="form-control required" name="productBrand">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('productBrand') == $brand->id ? 'selected' : '' }}>
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
                        <input type="text" name="productName" value="{{ old('productName') }}" class="form-control required">
                    </div>

                    <label for="select" class="col-sm-2 col-form-label">Select Colors</label>
                    <div class="col-sm-4">
                        <select class="form-control select2" name="colors[]" multiple="multiple" id="select">
                            @foreach($colors as $color)
                                <option value="{{ $color->color_id }}" {{ (is_array(old('colors')) && in_array($color->color_id, old('colors'))) ? 'selected' : '' }}>
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
                                <input class="form-check-input" type="checkbox" name="size[]" id="size_{{ $size->id }}" value="{{ $size->id }}"
                                    {{ (is_array(old('size')) && in_array($size->id, old('size'))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="size_{{ $size->id }}">{{ $size->size_name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-4">
                        <textarea name="productDescription" class="form-control required" rows="3">{{ old('productDescription') }}</textarea>
                    </div>

                    <label class="col-sm-2 col-form-label">Product's Picture</label>
                    <div class="col-sm-4">
                        <div id="image-upload-container">
                            <div class="img-input-row mb-3">
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
                            <i class="fa fa-plus"></i> Add Image
                        </button>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-4">
                        <input type="number" name="price" value="{{ old('price') }}" class="form-control required" step="0.01">
                    </div>

                    <label class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-4">
                        <select class="form-control required" name="productStatus">
                            <option value="active" {{ old('productStatus') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('productStatus') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-check"></i> Add Product
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
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