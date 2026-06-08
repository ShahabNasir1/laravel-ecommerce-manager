@extends('frontend.components.main')
@section('breadcrumb-heading')
<h2>Add Category</h2>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a>Categories</a>
</li>
<li class="breadcrumb-item active">
    <strong>Add Category</strong>
</li>
@endsection

@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Add New Category</h5>
        </div>
        <div class="ibox-content">
            <form id="categoryForm" action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="categoryName" class="form-control required">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category Status</label>
                    <div class="col-sm-10">
                        <select class="form-control required" name="categoryStatus">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group row">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit" name="submit">
                            <i class="fa fa-check"></i>&nbsp;Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection