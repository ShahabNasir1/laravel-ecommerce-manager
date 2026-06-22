<!-- @extends('frontend.components.main')
@section('breadcrumb-heading')
<h2>Add Size</h2>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a>Sizes</a>
</li>
<li class="breadcrumb-item active">
    <strong>Add Size</strong>
</li>
@endsection
@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Add New Size</h5>
        </div>
        <div class="ibox-content">
            <form id="sizeForm" action="{{ route('sizes.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Size Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="sizeName" class="form-control required">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Size Status</label>
                    <div class="col-sm-10">
                        <select class="form-control required" name="sizeStatus">
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

@endsection -->