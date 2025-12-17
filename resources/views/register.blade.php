<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('register.title') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('CSS/login.css') }}">
</head>

<body>
       <div class="lang-fixed {{ app()->getLocale() == 'ar' ? 'lang-left' : 'lang-right' }}">
        <div class="dropdown">
         <a class="lang-btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
                {{ strtoupper(app()->getLocale()) }}
            </a>

            <ul class="dropdown-menu {{ app()->getLocale() == 'ar' ? 'dropdown-menu-start' : 'dropdown-menu-end' }}">
                <li>

                    <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">
                        {{ __('login.lang_en') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('lang.switch', 'ar') }}">
                        {{ __('login.lang_ar') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
<div class="container-fluid auth-wrap">
    <div class="row auth-wrap">

        {{-- LEFT --}}
        <div class="col-lg-6 left-panel">
            <div class="left-content">
                <div class="brand">
                    <div class="brand-badge">🏥</div>
                    <div style="font-size:22px;">{{ __('register.brand') }}</div>
                </div>

                <h2 class="left-title">{{ __('register.left_title') }}</h2>

                <p class="left-sub">
                    {{ __('register.left_desc_1') }}<br>
                    {{ __('register.left_desc_2') }}<br>
                    {{ __('register.left_desc_3') }}
                </p>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-6 right-panel">

       
            <div class="form-box">
                <h1 class="welcome">{{ __('register.welcome') }}</h1>
                <div class="welcome-sub">{{ __('register.subtitle') }}</div>

                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="name">{{ __('register.name_label') }}</label>
                        <input
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            type="text"
                            placeholder="{{ __('register.name_placeholder') }}"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                        >
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mobile Number --}}
                    <div class="mb-3">
                        <label class="form-label" for="phone">{{ __('register.phone_label') }}</label>
                        <div class="d-flex gap-3">
                            <input
                                class="form-control @error('phone') is-invalid @enderror"
                                id="phone"
                                name="phone"
                                type="text"
                                placeholder="{{ __('register.phone_placeholder') }}"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                            >
                        </div>

                        @error('phone')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('register.email_label') }}</label>
                        <input
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            type="email"
                            placeholder="{{ __('register.email_placeholder') }}"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('register.password_label') }}</label>

                        <div class="position-relative">
                            <input
                                class="form-control pe-5 @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                type="password"
                                placeholder="{{ __('register.password_placeholder') }}"
                                autocomplete="new-password"
                                required
                            >

                            <button type="button"
                                    class="toggle-pass"
                                    id="togglePassword"
                                    aria-label="Show password"
                                    aria-pressed="false">
                                {{-- eye --}}
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>

                                {{-- eye-off --}}
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                    <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"></path>
                                    <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                                    <path d="M6.61 6.61A14.27 14.27 0 0 0 2 12s3 8 10 8a9.74 9.74 0 0 0 5.39-1.61"></path>
                                    <line x1="2" y1="2" x2="22" y2="22"></line>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button class="w-100 login-btn" type="submit">
                        {{ __('register.submit') }}
                    </button>
                </form>

                <p class="text-center mt-3 text-muted">
                    {{ __('register.already_registered') }}
                    <a href="{{ route('/') }}" class="text-decoration-none">
                        {{ __('register.login') }}
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const passInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePassword");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    toggleBtn.addEventListener("click", () => {
        const isHidden = passInput.type === "password";
        passInput.type = isHidden ? "text" : "password";

        eyeOpen.style.display = isHidden ? "none" : "block";
        eyeClosed.style.display = isHidden ? "block" : "none";

        toggleBtn.setAttribute("aria-pressed", String(isHidden));
        toggleBtn.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
    });
</script>

</body>
</html>
