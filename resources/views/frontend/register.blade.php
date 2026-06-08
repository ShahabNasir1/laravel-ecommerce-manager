\<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce | Register</title>
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
            <h3>Register to EC+</h3>
            <p>Create account</p>

            <form class="m-t" role="form" method="POST" action="{{ route('register.submit') }}">
                @csrf

                <!-- Display Validation Errors if any -->
                @if ($errors->any())
                    <div class="alert alert-danger text-left p-2">
                        <ul class="margin-bottom-none" style="list-style: none; padding-left: 5px;">
                            @foreach ($errors->all() as $error)
                                <li><small><i class="fa fa-times-circle"></i> {{ $error }}</small></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FIX 1: Added 'required' class to inputs -->
                <div class="form-group">
                    <input type="text" name="name" class="form-control required" placeholder="Name" value="{{ old('name') }}" >
                </div>

                <div class="form-group">
                    <input type="email" name="email" class="form-control required" placeholder="Email" value="{{ old('email') }}" >
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control required" placeholder="Password" >
                </div>

                <!-- Checkbox field container structured for error placement -->
                <div class="form-group text-left">
                    <div class="checkbox i-checks">
                        <label> 
                            <input type="checkbox" class="required-check" style="margin-top: 2px;"><i></i> Agree the terms and policy 
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary block full-width m-b">Register</button>

                <p class="text-muted text-center"><small>Already have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="{{ route('login') }}">Login</a>
            </form>
            <p class="m-t"> <small>Ecommerce &copy; 2026</small> </p>
        </div>
    </div>

    <script src="{{ url('assets/js/mainScript/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/popper.min.js') }}"></script>
    <script src="{{ url('assets/js/mainScript/bootstrap.js') }}"></script>
    
    <script>
        document.addEventListener("submit", function (e) {
            let form = e.target;

            // Text fields target karne ke liye
            let requiredFields = form.querySelectorAll(".required");
            // Checkbox target karne ke liye
            let requiredCheck = form.querySelector(".required-check");

            let isValid = true;

            // Purane errors remove karein
            form.querySelectorAll(".error-msg").forEach(function (msg) {
                msg.remove();
            });

            // 1. Text Fields Validation (Name, Email, Password)
            requiredFields.forEach(function (field) {
                if (field.disabled) return;

                let value = (field.value || "").trim();

                if (value === "") {
                    isValid = false;

                    // Red border aur soft alert background for Inspinia theme
                    field.style.borderColor = "#ed5565";
                    field.style.backgroundColor = "#fcf2f3";

                    let error = document.createElement("div");
                    error.className = "error-msg";
                    error.style.color = "#ed5565";
                    error.style.fontSize = "11px";
                    error.style.textAlign = "left";
                    error.style.marginTop = "4px";
                    error.style.fontWeight = "600";
                    
                    let fieldName = field.getAttribute("placeholder") || "This field";
                    error.innerHTML = `<i class="fa fa-times-circle"></i> ${fieldName} is required.`;

                    // Exact input element ke niche insert karein
                    field.after(error);
                } else {
                    field.style.borderColor = "";
                    field.style.backgroundColor = "";
                }
            });

            // 2. Checkbox Validation (Terms & Policy)
            if (requiredCheck && !requiredCheck.disabled && !requiredCheck.checked) {
                isValid = false;

                let error = document.createElement("div");
                error.className = "error-msg";
                error.style.color = "#ed5565";
                error.style.fontSize = "11px";
                error.style.textAlign = "left";
                error.style.marginTop = "4px";
                error.style.fontWeight = "600";
                error.innerHTML = `<i class="fa fa-times-circle"></i> You must agree to the terms and policy.`;

                // Isko checkbox ke poore parent container ke bad lagayein taake layout hilay bina dikhe
                requiredCheck.closest('.form-group').appendChild(error);
            }

            // Agar validation fail hui to form submit hone se rokein
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>