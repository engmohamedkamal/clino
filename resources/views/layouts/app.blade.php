<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
   <link rel="shortcut icon"
      href="{{ $setting?->logo
                ? asset('storage/' . $setting->logo)
                : asset('images/favicon.ico') }}"
      type="image/x-icon" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('CSS/index.css')}}">
 


</head>

<body>

  <nav class="navbar navbar-expand-lg bg-white py-3 fixed-top">
    <div class="container">
     @if($setting?->logo)
  <img src="{{ asset('storage/' . $setting->logo) }}"
       alt="Logo"
       width="100"
       class="navbar-brand fw-bold nav-logo">
@endif


      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse " id="nav">
        <ul class="navbar-nav mx-auto gap-3 ancor ">
          <li class="nav-item"><a class="nav-link  " href="{{ route('home') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('our.service') }}">Services</a></li>
          {{-- <li class="nav-item"><a class="nav-link" href="#Testimonials">Testimonials</a></li> --}}
          <li class="nav-item"><a class="nav-link" href="{{ route('contact.index') }}">Contact Us</a></li>
        </ul>
      </div>

      <div class="sideBtn d-flex align-items-center">

        @php
  $user = auth()->user();

  $dashboardRoute = match ($user->role) {
      'admin'   => route('dashboard'),
      'doctor'  => $user->doctorInfo
                      ? route('doctor-info.show', $user->doctorInfo->id)
                      : route('doctor-info.create'),
      'patient' => route('patient-info.my'),
      default   => route('dashboard'),
  };
@endphp

<a class="btn btn-primary rounded-pill px-4 me-2" href="{{ $dashboardRoute }}">
  Dashboard
</a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn p-0 border-0 bg-transparent">
            <i class="fa-solid fa-right-from-bracket border border-primary rounded-circle p-2 text-primary"></i>
          </button>
        </form>

      </div>


    </div>
  </nav>
  @yield('content')

  <!-- footer -->
  <footer id="contact" class="site-footer text-white pt-5">
    <div class="container">
      <div class="row g-4 align-items-start">

        <!-- Helper Clinic -->
        <div class="col-12 col-md-4 col-lg-3">
          <h4 class="fw-bolder mb-3">{{ $setting->name ?? '' }}</h4>
          <p class="mb-3 footer-desc">
            {{ $setting->slogan ?? '' }}
          </p>

          <div class="social-icons">
            <a href="{{ $setting->facebook ?? '' }}" class="me-2" aria-label="facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="{{ $setting->twitter ?? '' }}" class="me-2" aria-label="twitter"><i class="fab fa-twitter"></i></a>
            <a href="{{ $setting->instagram ?? '' }}" aria-label="instagram"><i class="fab fa-instagram"></i></a>
          </div>
        </div>

        <!-- Explore -->
        <div class="col-12 col-md-4 col-lg-3">
          <h5 class="fw-bolder mb-3">Explore</h5>
          <ul class="list-unstyled mb-0 footer-links">
            <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
            <li class="mb-2"><a href="{{ route('about') }}">About Us</a></li>
            <li class="mb-2"><a href="{{ route('our.service') }}">Services</a></li>
            {{-- <li class="mb-2"><a href="#Testimonials">Testimonials</a></li> --}}
            <li><a href="{{ route('contact.index') }}">Contact Us</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="col-12 col-md-4 col-lg-3">
          <h5 class="fw-bolder mb-3">Contact</h5>

          <div class="footer-contact">
            <div class="fc-row">
              <i class="fa-solid fa-phone"></i>
              <a href="tel:+2{{ $setting->phone ?? '' }}">{{ $setting->phone ?? '' }}</a>
            </div>

            <div class="fc-row">
              <i class="fa-solid fa-envelope"></i>
              <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '' }}</a>
            </div>

            <div class="fc-row">
              <i class="fa-solid fa-location-dot"></i>
              <span>{{ $setting->address ?? '' }}</span>
            </div>
          </div>
        </div>

        <!-- Map -->
        <div class="col-12 col-lg-3">
          <div class="footer-map">
            <iframe title="Clinic location" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=Elmahkama%20Street&output=embed">
            </iframe>
          </div>
        </div>

      </div>

      <hr class="my-4 footer-hr">

      <p class="text-center mb-0 footer-copy">
        Copyright 2024 Favorite Doctor, All Rights Reserved
      </p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="JS/bootstrap.bundle.min.js"></script>
  <script src="{{asset('JS/index.js')}}"></script>

</body>

</html>