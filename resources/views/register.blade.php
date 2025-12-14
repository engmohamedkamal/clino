<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Helper Clinic Registration</title>
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
            <div class="col-lg-8 d-flex align-items-center ">
                <div class="w-100 px-4 px-md-5 m-5">

                    <h2 class="fw-bold text-primary mb-1">
                        Welcome to Helper Clinic
                    </h2>
                    <p class="text-muted mb-4">
                        please enter your details to sign in
                    </p>

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <!-- Registering For -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                I am registering for
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="register_for" id="self"
                                        value="self" {{ old('register_for', 'self') === 'self' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="self">
                                        my self
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="register_for" id="other"
                                        value="other" {{ old('register_for') === 'other' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="other">
                                        Other People
                                    </label>
                                </div>
                            </div>

                            @error('register_for')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <small class="text-muted d-block mb-1">
                                Notifications for appointment and reminders will be sent to this number
                            </small>

                            <div class="input-group">
                                <input type="tel" id="phone" name="phone" placeholder="Phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <!-- ID Number -->
                        <div class="mb-4">
                            <label class="form-label">ID Number</label>
                            <input type="text" name="id_number"
                                class="form-control @error('id_number') is-invalid @enderror"
                                value="{{ old('id_number') }}" placeholder="ID Number">
                            @error('id_number')
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
                            Register
                        </button>

                        <p class="text-center mt-3 text-muted">
                            Already registered?
                            <a href="{{ route('/')}}" class="text-decoration-none">
                                Login
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