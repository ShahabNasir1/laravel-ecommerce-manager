@extends('frontend.components.main')

@section('content')

<div class="col-lg-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Edit Brand: {{ $brand->brand_name }}</h5>
        </div>
        <div class="ibox-content">
            
           

            <!-- Action mein brands.update route aur pass ki gayi brand_id -->
            <form id="brandForm" action="{{ route('brands.update', $brand->brand_id) }}" method="POST">
                <!-- CSRF Token security ke liye -->
                @csrf
                <!-- Laravel mein update form submit karne ke liye PUT method lazmi hai -->
                @method('PUT')

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Brand Name</label>
                    <div class="col-sm-10">
                        <!-- value mein purana naam database se load ho kar aayega -->
                        <input type="text" name="brandName" value="{{ old('brandName', $brand->brand_name) }}" class="form-control required">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Brand Status</label>
                    <div class="col-sm-10">
                        <select class="form-control required" name="brandStatus">
                            <!-- Database ke 'active' status ko form ke '1' option se dynamic match kiya -->
                            <option value="active" {{ old('brandStatus', $brand->brand_status) === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('brandStatus', $brand->brand_status) === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                
                <div class="form-group row">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i>&nbsp;Update Brand
                        </button>
                        <a href="{{ route('brands.index') }}" class="btn btn-white">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection