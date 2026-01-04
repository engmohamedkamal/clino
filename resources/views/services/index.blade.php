@extends('layouts.app')
@section('content')
  <link rel="stylesheet" href="{{{ asset('CSS/service.css') }}}">
  <title>Our Services</title>


  <!-- Services Main Section -->
  <section class="container text-center Services">
    <h1 class="fw-bold  mb-3">
      Our Medical Services
    </h1>
    <p class="text-muted mx-auto mb-4" style="max-width: 640px;">
      We offer a wide range of professional medical services to cater to all your health needs.
      From routine check-ups to complex surgeries, our expert team is here for you.
    </p>

    <!-- Filter Pills -->


    <!-- Services Grid -->
    <div class="row g-4 text-start">
      <!-- Cardiology (highlight) -->
      @foreach ($services as $service)
  <div class="col-12 col-md-6 col-lg-4 service-item" data-category="surgery">
    <a href="{{ route('services.doctors', $service->id) }}" class="text-decoration-none text-reset d-block">
      <div class="service-card">
        <div class="service-icon-circle">
          <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}">
        </div>

        <h3 class="h5 fw-bold mb-2">{{ $service->name }}</h3>
        <p class="small mb-0">{{ $service->description }}</p>
      </div>
    </a>
  </div>
@endforeach

    </div>
    <div class="mt-4 custom-pagination">
      {{ $services->links('pagination::bootstrap-5') }}
    </div>


  </section>
  <!-- Featured Service Section -->
  <section id="featured-service" class="featured-service-section">
    <div class="container">
      <div class="featured-card row g-4 align-items-center">

        <!-- Text -->
        <div class="col-12 col-lg-6">
          <span class="featured-label d-inline-block mb-2">
            FEATURED SERVICE
          </span>

          <h2 class="featured-title mb-3">
            Advanced Cardiac Care &amp;<br />
            Surgery Center
          </h2>

          <p class="featured-text mb-4">
            Our cardiology department is equipped with state-of-the-art technology for
            accurate diagnosis and effective treatment of heart conditions. We specialize
            in minimally invasive procedures that ensure faster recovery times.
          </p>

          <div class="d-flex flex-wrap ">
            <a href="{{ route('appointment') }}" class="btn btn-featured-primary  fw-semibold">
              Book Consultation
            </a>

          </div>
        </div>

        <!-- Image -->
        <div class="col-12 col-lg-6">
          <div class="featured-image-wrapper">
            <img src="{{asset('Images/tablet.png')}}" alt="Doctor checking reports" class="featured-image" />
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="container text-center Works">
    <h2 class="h3 fw-bold  mb-2">How It Works</h2>
    <p class="text-muted mb-5">
      Your journey to better health in 4 simple steps
    </p>

    <div class="position-relative">
      <!-- الخط الواصل بين الخطوات على الشاشات الكبيرة -->
      <div class="steps-line d-none d-lg-block "></div>

      <div class="row g-4 g-lg-5 justify-content-center">
        <div class="col-12 col-md-6 col-lg-3 step-item">
          <div class="d-flex flex-column align-items-center">
            <div class="step-circle">
              <i class="fa-regular fa-calendar-check"></i>
            </div>
            <h3 class="h6 fw-bold mb-1">1. Book Appointment</h3>
            <p class="small text-muted mb-0" style="max-width: 220px;">
              Schedule a visit online or call our dedicated support line.
            </p>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 step-item">
          <div class="d-flex flex-column align-items-center">
            <div class="step-circle">
              <i class="fa-solid fa-user-doctor"></i>
            </div>
            <h3 class="h6 fw-bold mb-1">2. Meet Doctor</h3>
            <p class="small text-muted mb-0" style="max-width: 220px;">
              Consult with our specialists to discuss your symptoms.
            </p>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 step-item">
          <div class="d-flex flex-column align-items-center">
            <div class="step-circle">
              <i class="fa-solid fa-notes-medical"></i>
            </div>
            <h3 class="h6 fw-bold mb-1">3. Diagnosis</h3>
            <p class="small text-muted mb-0" style="max-width: 220px;">
              Undergo necessary tests to get an accurate health assessment.
            </p>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 step-item">
          <div class="d-flex flex-column align-items-center">
            <div class="step-circle">
              <i class="fa-solid fa-briefcase-medical"></i>
            </div>
            <h3 class="h6 fw-bold mb-1">4. Treatment</h3>
            <p class="small text-muted mb-0" style="max-width: 220px;">
              Receive personalized care and regular follow-ups for recovery.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- CTA -->
  <section class="cta-section text-center CTA">
    <div class="container">
      <h2 class="h3 fw-bold  mb-2">
        Ready to prioritize your health?
      </h2>
      <p class="text-muted mb-4">
        Don't wait until it's too late. Our expert team is ready to provide you with the best care possible.
      </p>
      <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
        <a href="{{ route('appointment') }}" class=" btn btn-featured-primary  fw-semibold">
          Book an Appointment Now
        </a>
        <a href="{{ route('contact.index') }}" class="btn btn-featured-primary  fw-semibold">
          Contact Support
        </a>
      </div>
    </div>
  </section>


@endsection