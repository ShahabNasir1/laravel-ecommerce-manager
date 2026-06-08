<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ecommerce</title>

    <link href="{{ url('assets/css/mainCSS/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/animate.css') }}" rel="stylesheet">
    <link href="{{ url('assets/css/mainCSS/style.css') }}" rel="stylesheet">
</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name">EC+</h1>
            </div>
            <h3>Welcome to Ecommerce</h3>

            <form class="m-t" role="form" method="post" action="{{ route('login.submit') }}">
                @csrf
                @if ($errors->has('email'))
                <div class="alert alert-danger p-2 text-left">
                    <i class="fa fa-times-circle"></i> {{ $errors->first('email') }}
                </div>
                @endif
                
                <!-- FIX 1: Added 'required' class to make it detectable by JS -->
                <div class="form-group">
                    <input type="email" name="email" class="form-control required" placeholder="Email" value="{{ old('email') }}">
                </div>
                
                <!-- FIX 2: Added 'required' class to make it detectable by JS -->
                <div class="form-group">
                    <input type="password" name="password" class="form-control required" placeholder="Password">
                </div>
                
                <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                <a href="#"><small>Forgot password?</small></a>
                <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="{{ route('register') }}">Create an account</a>
            </form>
            <p class="m-t">Ecommerce <small>&copy; 2026</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{ url('assets/js/mainScript/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/popper.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/bootstrap.js') }}"></script>

    <!-- Optimized Inspinia-Friendly JavaScript Validation -->
    <script>
        document.addEventListener("submit", function (e) {
            let form = e.target;

            // Yeh selector ab inputs ko detect kar payega kyunke humne class "required" add kar di hai
            let requiredFields = form.querySelectorAll(
                ".required, input[required], select[required], textarea[required]"
            );

            if (!requiredFields.length) return;

            let isValid = true;

            // Purane error messages ko remove karein
            form.querySelectorAll(".error-msg").forEach(function (msg) {
                msg.remove();
            });

            requiredFields.forEach(function (field) {
                if (field.disabled) return;

                let value = (field.value || "").trim();

                if (value === "") {
                    isValid = false;

                    // Inspinia native styling (Halka pink background aur red border)
                    field.style.borderColor = "#ed5565";
                    field.style.backgroundColor = "#fcf2f3";

                    // Error message container setting up
                    let error = document.createElement("div");
                    error.className = "error-msg";
                    error.style.color = "#ed5565";
                    error.style.fontSize = "11px";
                    error.style.textAlign = "left"; // Text center design ko override karne ke liye
                    error.style.marginTop = "4px";
                    error.style.fontWeight = "600";
                    
                    // Har field ke placeholder ke mutabiq dynamic message
                    let fieldName = field.getAttribute("placeholder") || "This field";
                    error.innerHTML = `<i class="fa fa-times-circle"></i> ${fieldName} is required.`;

                    // FIX 3: Parent node par append karne ke bajaye input tag ke exact niche append karein
                    field.after(error);

                } else {
                    // Valid hone par styles reset
                    field.style.borderColor = "";
                    field.style.backgroundColor = "";
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>