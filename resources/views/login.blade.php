<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Helper Clinic Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
        }

        .left-section {
            background: linear-gradient(rgba(13, 110, 253, .75), rgba(13, 110, 253, .75)),
                url('صورة تسجيل الدخول.jpg') center/cover no-repeat;
            color: #fff;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row min-vh-100">

            <!-- Left Section -->
            <div class="col-lg-4 d-none d-lg-flex align-items-center left-section p-5">
                <div>
                    <h2 class="fw-bold mb-3">
                        Enter your personal details to register at the clinic
                    </h2>
                    <p class="fs-5">
                        and receive medical follow-up services, appointment notifications,
                        and reminders.
                    </p>
                </div>
            </div>

            <!-- Right Section -->
            <div class="col-lg-8 d-flex align-items-center">
                <div class="w-100 px-4 px-md-5">

                    <h2 class="fw-bold text-primary mb-1">
                        Welcome to Helper Clinic
                    </h2>
                    <p class="text-muted mb-4">
                        please enter your details to sign in
                    </p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if(session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                        <!-- Full Name OR Phone -->
                        <div class="mb-3">
                            <label class="form-label">Name or Phone</label>
                            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                value="{{ old('login') }}" placeholder="Enter name or phone" autofocus>

                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-5">
                            Login
                        </button>



                        <p class="text-center mt-3 text-muted">
                            Don`t Have Account ?
                            <a href="{{ route('register') }}" class="text-decoration-none">
                                Register 
                            </a>
                        </p>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>