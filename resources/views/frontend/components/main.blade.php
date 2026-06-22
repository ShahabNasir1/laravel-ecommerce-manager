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
        // Expose helpers globally so children scripts can utilize them
        window.toggleError = function(input, errorMessage) {
            const $el = $(input);
            let $container = $el.closest('.form-group, .col-sm-10, .col-md-12');
            if ($container.length === 0) $container = $el.parent();

            $container.find('.invalid-feedback-custom').remove();
            $el.removeClass('is-invalid');

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').find('.select2-selection').css('border-color', '');
            }

            if (errorMessage) {
                $el.addClass('is-invalid');
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.next('.select2-container').find('.select2-selection').css('border-color', '#ed5565');
                }
                $container.append(`<div class="invalid-feedback-custom" style="color: #ed5565; font-size: 85%; margin-top: 5px; font-weight: bold;">${errorMessage}</div>`);
            }
        };

        $(document).ready(function() {
            const validators = {
                text: (val) => (val && val.trim().length > 0 ? null : "This field is required11111."),
                textarea: (val) => (val && val.trim().length >= 10 ? null : "Must be at least 10 characters."),
                email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) ? null : "Invalid email address.",
                password: (val) => (val && val.length >= 8 && /\d/.test(val)) ? null : "Password must be at least 8 characters and contain a number.",
                checkbox: (el) => ($(el).is(':checked') ? null : "You must check this box."),
                select2: (val) => {
                    if (Array.isArray(val)) return val.length > 0 ? null : "Please select at least one option.";
                    return val && val.trim() !== "" ? null : "Please select an option.";
                },
                file: (el, constraints) => {
                    const files = el.files;
                    if (!files || files.length === 0) return "No file selected.";
                    const maxMb = constraints.maxSize || 5;
                    if (files[0].size > maxMb * 1024 * 1024) return `File exceeds maximum size of ${maxMb}MB.`;
                    return null;
                }
            };

            /**
             * Global Validation Execution Core Engine
             * Exposing this to window allows child AJAX scripts to stop requests BEFORE dispatch
             */
            window.validateForm = function($form) {
                let isFormValid = true;

                $form.find('[data-validate]').each(function() {
                    const input = this;
                    const type = $(input).data('validate');
                    const value = $(input).val();
                    let error = null;

                    if (!validators[type]) return;

                    if (type === 'checkbox') {
                        error = validators.checkbox(input);
                    } else if (type === 'file') {
                        const maxSize = $(input).data('max-size');
                        error = validators.file(input, { maxSize });
                    } else if ($(input).hasClass('select2-hidden-accessible')) {
                        error = validators.select2(value);
                    } else {
                        error = validators[type](value);
                    }

                    if (error) {
                        isFormValid = false;
                        window.toggleError(input, error);
                    } else {
                        window.toggleError(input, null);
                    }
                });

                if (!isFormValid) {
                    $('html, body').animate({
                        scrollTop: $form.find('.is-invalid').first().offset().top - 100
                    }, 300);
                    $form.find('.is-invalid').first().focus();
                }

                return isFormValid;
            };

            // Catch-all submission handling for standard synchronous forms
            $(document).on('submit', 'form', function(e) {
                const $form = $(this);
                
                // If validation fails, kill event lifecycle immediately
                if (!window.validateForm($form)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            });

            // Real-time error flushing for interactive state updates
            $(document).on('input change', '[data-validate]', function() {
                window.toggleError(this, null);
            });
        });
    </script>

    @stack('js')
</body>

</html>