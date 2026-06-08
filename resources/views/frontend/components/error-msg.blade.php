<!-- CENTRALIZED ERROR BOX -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 5px solid #ed5565; margin-bottom: 20px;">
        <div class="d-flex align-items-center">
            <div style="margin-right: 15px;">
                <i class="fa fa-exclamation-triangle fa-2x"></i>
            </div>
            <div>
                <h5 class="alert-heading font-bold" style="margin-bottom: 5px;">Form Validation Failed:</h5>
                <ul style="margin-bottom: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif