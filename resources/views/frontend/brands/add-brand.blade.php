@extends('frontend.components.main')

@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Add New Brand</h5>
        </div>
        <div class="ibox-content">
            
            <!-- 1. Action mein store route aur method POST set kiya -->
            <form id="brandForm" action="{{ route('brands.store') }}" method="POST">
                
                <!-- 2. CSRF Token add kiya (Laravel security ke liye zaroori hai) -->
                @csrf

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Brand Name</label>
                    <div class="col-sm-10">
                        <input type="text" name="brandName" value="{{ old('brandName') }}" class="form-control required">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Brand Status</label>
                    <div class="col-sm-10">
                        <select class="form-control required" name="brandStatus">
                            <option value="">Select Status</option>
                             <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group row">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-check"></i>&nbsp;Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection