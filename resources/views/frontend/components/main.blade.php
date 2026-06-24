<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce</title>

    <link href="{{ url('assets/css/mainCSS/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/font-awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="{{ url('assets/css/mainCSS/animate.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/custom.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/style.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('css')
</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            @include('frontend.components.sidebar')
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                @include('frontend.components.topbar')
            </div>
            <div class="row wrapper border-bottom white-bg page-heading">
                @include('frontend.components.breadcrumb')
            </div>

            @if ($errors->any())
            <div class="row alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 5px solid #ed5565; margin-bottom: 20px;">
                <div class="col-lg-12 d-flex align-items-center">
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

            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="footer">
                @include('frontend.components.footer')
            </div>
        </div>
    </div>

    <script src="{{ url('assets/js/mainScript/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/popper.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/bootstrap.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/jquery.metisMenu.js') }}"></script>

    <script src="{{ url('assets/js/customPlugins/inspinia.js') }}"></script>
    <script src="{{ url('assets/js/customPlugins/pace.min.js') }}"></script>
    <script src="{{ url('assets/js/iCheck/icheck.min.js') }}"></script>
    <script src="{{ url('assets/js/validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    window.BASE_URL = "{{ url('/') }}";
</script>

    <script>
        // Global Error UI Toggling
        window.toggleError = function(input, errorMessage) {
            const $el = $(input);
            let $container = $el.closest('.form-group, .col-sm-10, .col-md-12');
            if ($container.length === 0) $container = $el.parent();

            $container.find('.invalid-feedback-custom').remove();
            $el.removeClass('is-invalid');

            const isSelect2 = $el.hasClass('select2-hidden-accessible');
            if (isSelect2) $el.next('.select2-container').find('.select2-selection').css('border-color', '');

            if (errorMessage) {
                $el.addClass('is-invalid');
                if (isSelect2) $el.next('.select2-container').find('.select2-selection').css('border-color', '#ed5565');
                $container.append(`<div class="invalid-feedback-custom" style="color: #ed5565; font-size: 85%; margin-top: 5px; font-weight: bold;">${errorMessage}</div>`);
            }
        };

        $(document).ready(function() {
            // Main Validation Engine
            window.validateForm = function($form) {
                let isFormValid = true;

                // Sirf required fields par loop chalega
                $form.find('input[required], textarea[required], select[required], .required').each(function() {
                    const input = this,
                        $input = $(this),
                        value = $input.val();
                    const tagName = input.tagName.toLowerCase(),
                        type = input.type || 'text';
                    let error = null;

                    // Inline If-Else Validation Logic
                    if (tagName === 'select' || $input.hasClass('select2-hidden-accessible')) {
                        if (Array.isArray(value) ? value.length === 0 : !value || !value.trim()) error = "Please select an option.";
                    } else if (tagName === 'textarea') {
                        if (!value || value.trim().length < 10) error = "Must be at least 10 characters.";
                    } else if (type === 'checkbox') {
                        if (!$input.is(':checked')) error = "You must check this box.";
                    } else if (type === 'file') {
                        if (!input.files || input.files.length === 0) error = "No file selected.";
                        else if (input.files[0].size > ($input.data('max-size') || 5) * 1024 * 1024) error = "File size exceeded.";
                    } else if (type === 'email') {
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) error = "Invalid email address.";
                    } else if (type === 'password') {
                        if (!value || value.length < 8 || !/\d/.test(value)) error = "Password must be 8+ chars with a number.";
                    } else {
                        if (!value || !value.trim()) error = "This field is required1.";
                    }

                    if (error) {
                        isFormValid = false;
                        window.toggleError(input, error);
                    } else {
                        window.toggleError(input, null);
                    }
                });

                // Auto-Scroll
                if (!isFormValid) {
                    const $firstInvalid = $form.find('.is-invalid').first();
                    if ($firstInvalid.length) {
                        $('html, body').animate({
                            scrollTop: $firstInvalid.offset().top - 100
                        }, 300);
                        $firstInvalid.focus();
                    }
                }
                return isFormValid;
            };

            // Event Handlers
            $(document).on('submit', 'form', function(e) {
                if (!window.validateForm($(this))) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            });

            $(document).on('input change', 'input, textarea, select', function() {
                if (this.hasAttribute('required') || $(this).hasClass('required')) window.toggleError(this, null);
            });
        });
    </script>

    @stack('js')
</body>

</html>