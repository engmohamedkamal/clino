@extends('layouts.app')
@section('content')
  <!-- Hero Section -->
  <title>Home</title>
  <section id="Home" class="hero-section ">
    <div class="container rounded-5 ">
      <div class="row align-items-center">

        <div class="col-lg-6">
          <p class="text-white hero-badge">
            <i class="fa-solid fa-heart me-2 heart-icon"></i>
            {{$setting->slogan ?? ''}}
          </p>

          <h1 class="fw-bolder mt-3 text-white">
            Secure Your <span style="color: #FFFF00;">Doctor</span> Visit Anytime, Anywhere
          </h1>

          <p class="mt-3 text-white">
            Easily schedule a medical consultation with your preferred doctor at a time that suits you best.
          </p>


          <div class="d-flex gap-3 mt-5 align-items-center custom-color">
            <a class="btn btn-light rounded-pill d-flex align-items-center hero-chip">
              <i class="fa-solid fa-stethoscope me-2"></i>Specialists in 15+ Fields
            </a>
            <a class="btn btn-light rounded-pill d-flex align-items-center hero-chip">
              <i class="fa-solid fa-user-doctor me-2"></i> 200+ Medical Staff
            </a>
          </div>

          <div class="d-flex gap-3 mt-3 align-items-center custom-color">
            <a class="btn btn-light rounded-pill d-flex align-items-center hero-chip">
              <i class="fa-solid fa-bed me-2"></i> 3K The cases in the hospital
            </a>
            <a class="btn btn-light rounded-pill d-flex align-items-center hero-chip">
              <i class="fa-solid fa-medal me-2"></i> 98% Customer Satisfaction
            </a>
          </div>

        </div>

        <div class="col-lg-6 text-center">
          <img src="Images/Doctor.png" class="img-fluid" alt="Doctor">
        </div>

      </div>
    </div>
  </section>

  <section class="stats-section">
    <div class="container">
      <div class="row">

        <div class="col-md-3">
          <div class="item text-center border-right-line">
            <p>Years Experience</p>
            <h3>15</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center border-right-line">
            <p>Export Doctors</p>
            <h3>30+</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center border-right-line">
            <p>Medical Staff</p>
            <h3>200+</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center">
            <p>Patient Capacity</p>
            <h3>4000+</h3>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- About (Vision & Mission) -->
  <section id="About" class="about py-5">
    <div class="container">

      <div class="about-pill">
        <i class="fa-solid fa-circle-info"></i>
        <span>About Us</span>
      </div>

      <div class="row g-4">




        <!-- Text -->
        <div class="col-lg-6 col-md-12 d-flex custom-font">
          <div class="about-text m-auto"> <!-- m-auto هيوسّط النص عموديًا -->
            <h2 style="font-size:  2.8rem;" class="  mb-2 fw-bolder ">Our Vision</h2>
            <p class="fw-medium mb-3">
              {{$setting->vision ?? ''}}
            </p>

            <h2 style="font-size: 2.8rem;" class=" mt-3 mb-2 fw-bolder ">Our Mission</h2>
            <p class="fw-medium mb-4 ">
              {{$setting->mission ?? ''}}
            </p>
            <a href="{{ route('about') }}" class="read-more-btn">
              Read More
            </a>

          </div>
        </div>

        <!-- Images -->
        <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-center">
          <div class="row g-3 w-75 mx-auto">
            <div class="col-12">
              <img src="Images/medium-shot-doctors-discussing.jpg" class="img-fluid rounded-4 w-100 about-img" alt="">
            </div>
            <div class="col-12">
              <img src="Images/group-healthcare-workers-analyzing-diagnostic-data-planning-treatment.jpg"
                class="img-fluid rounded-4 w-100 about-img" alt="">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Services -->
  <section id="services" class="services-section text-center">
    <div class="container">

      <div class="services-bar">
        <div class="services-pill">
          <i class="fa-solid fa-circle-info"></i>
          <span>Services</span>
        </div>

        <a href="{{ route('our.service') }}" class="btn services-btn">
          View Services
        </a>
      </div>



      <p class="mb-4">Comprehensive Healthcare Services Delivered With Compassion And Excellence</p>

      <div class="row g-4">
        @foreach ($services as $service)
          <div class="col-md-4">
            <div class="service-card text-center">
              <div class="service-img">
                <img src="{{ $service->image }}" alt="{{ $service->name }}">
              </div>

              <h5>{{ $service->name }}</h5>
              <p>{{ \Illuminate\Support\Str::limit($service->description, 100) }}</p>
            </div>
          </div>
        @endforeach
      </div>

    </div>
    </div>
  </section>


  <!-- Testimonials -->

  <section id="Testimonials" class="testimonials-section">
    <div class="container text-center">

      <h2 class="fw-bold mb-2">
        <span class="highlight">Testimonials</span> that <br> Speak to Our Results
      </h2>
      <p class="subtitle">
        Check out our patients' testimonials to see why they love<br /> coming to us
        and how we can help you.
      </p>

      <div class="carousel-area">
        <div class="carousel-track">

                @forelse($feedbacks as $feedback)
  <div class="testimonial-card">
    <i class="quote fa-solid fa-quote-left"></i>

    <div class="user">
      <div class="">
        <img src="{{ asset('face.png') }}" width="50" height="50" alt="">
      </div>

      <div>
        <h6>{{ optional($feedback->user)->name ?? 'Unknown User' }}</h6>
      </div>
    </div>

    <p>{{ \Illuminate\Support\Str::limit($feedback->comment, 150) }}</p>
  </div>
@empty
  <p class="text-center text-muted">No feedback found yet.</p>
@endforelse

        </div>
      </div>

      <!-- arrows تحت -->
      <div class="carousel-arrows">
        <button class="arrow prev">
          <i class="fa-solid fa-arrow-left"></i>
        </button>
        <button class="arrow next">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>

    </div>
  </section>
@endsection