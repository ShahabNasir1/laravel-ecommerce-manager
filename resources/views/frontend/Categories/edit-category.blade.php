@extends('frontend.components.main')

@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Edit Category: {{ $category->category_name }}</h5>
        </div>
        <div class="ibox-content">



            <!-- Action mein category.update route aur pass ki gayi brand_id -->
            <form id="categoryForm" action="{{ route('categories.update', $category->category_id) }}" method="POST">
                <!-- CSRF Token security ke liye -->
                @csrf
                <!-- Laravel mein update form submit karne ke liye PUT method lazmi hai -->
                @method('PUT')

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">category Name</label>
                    <div class="col-sm-10">
                        <!-- value mein purana naam database se load ho kar aayega -->
                        <input type="text" name="categoryName" value="{{ old('categoryName', $category->category_name) }}" class="form-control required">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category Status</label>
                    <div class="col-sm-10">
                        <select class="form-control required" name="categoryStatus">
                            <option value="active" {{ old('categoryStatus', $category->category_status) === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('categoryStatus', $category->category_status) === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group row">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i>&nbsp;Update Category
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-white">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection