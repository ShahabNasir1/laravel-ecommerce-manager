<!DOCTYPE html>
<html>


<!-- Mirrored from webapplayers.com/inspinia_admin-v2.9.2/table_data_tables.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 13 Sep 2019 10:06:06 GMT -->

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce</title>
    <!-- Pehle Framework ki CSS load karein -->
    <link href="{{ url('assets/css/mainCSS/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/font-awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Phir Inspinia aur Custom CSS load karein -->
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
            <!-- <div class="row">
                <div class="col-lg-12 alert alert-danger">Error message will be here></div>
            </div> -->
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
    <!-- 1. jQuery hamesha sabse pehle load hogi (Har cheez is par chalti hai) -->
    <script src="{{ url('assets/js/mainScript/jquery-3.1.1.min.js') }}"></script>

    <!-- 2. Popper.js (Bootstrap dropdowns aur tooltips ke liye zaroori hai) -->
    <script src="{{ url('assets/js/mainScript/popper.min.js') }}"></script>

    <!-- 3. Bootstrap Core JS -->
    <script src="{{ url('assets/js/mainScript/bootstrap.js') }}"></script>

    <!-- 4. Slimscroll aur MetisMenu (Inspinia ke sidebar dropdowns ka asal engine) -->
    <script src="{{ url('assets/js/mainScript/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/jquery.metisMenu.js') }}"></script>

    <!-- 5. Inspinia Core Script aur doosre third-party plugins -->
    <script src="{{ url('assets/js/customPlugins/inspinia.js') }}"></script>
    <script src="{{ url('assets/js/customPlugins/pace.min.js') }}"></script>
    <script src="{{ url('assets/js/iCheck/icheck.min.js') }}"></script>
    <script src="{{ url('assets/js/validate.js') }}"></script>

    <!-- 6. Select2 Plugin (Agar use kar rahe hain to) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('js')
</body>

</html>