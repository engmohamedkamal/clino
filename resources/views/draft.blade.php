<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('home.title') }}</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('CSS/index.css') }}">
</head>

<body>

  {{-- Fixed Language Button --}}
  <div class="lang-fixed {{ app()->getLocale() == 'ar' ? 'lang-left' : 'lang-right' }}">
    <div class="dropdown">
      <a class="lang-btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
        {{ strtoupper(app()->getLocale()) }}
      </a>

      <ul class="dropdown-menu {{ app()->getLocale() == 'ar' ? 'dropdown-menu-start' : 'dropdown-menu-end' }}">
        <li>
          <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">
            {{ __('home.lang_en') }}
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('lang.switch', 'ar') }}">
            {{ __('home.lang_ar') }}
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white py-3 fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold nav-logo" href="#">{{ __('home.brand') }}</a>

      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav mx-auto gap-3">
          <li class="nav-item"><a class="nav-link active" href="#Home">{{ __('home.nav.home') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="#About">{{ __('home.nav.about') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">{{ __('home.nav.services') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="#Testimonials">{{ __('home.nav.testimonials') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">{{ __('home.nav.contact') }}</a></li>
        </ul>

        <div class="d-flex align-items-center gap-3">
          <a href="" class="dashboard-btn">
            <span>{{ __('home.nav.dashboard') }}</span>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="icon-btn" title="{{ __('home.nav.logout_title') }}">
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </form>
        </div>

      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="Home" class="hero-section">
    <div class="container rounded-5">
      <div class="row align-items-center">

        <div class="col-lg-6">
          <p class="text-white hero-badge">
            <i class="fa-solid fa-heart me-2 heart-icon"></i>
            {{ __('home.hero.badge') }}
          </p>

          <h1 class="fw-bolder mt-3 text-white">
            {{ __('home.hero.title_1') }}
            <span style="color: #FFFF00;">{{ __('home.hero.title_doctor') }}</span>
            {{ __('home.hero.title_2') }}
          </h1>

          <p class="mt-3 text-white">
            {{ __('home.hero.desc') }}
          </p>

          <div class="hero-actions mt-4">
            <a href="#services" class="view-services-btn">
              {{ __('home.hero.view_services') }}
              <i class="fa-solid {{ app()->getLocale() == 'ar' ? 'fa-arrow-left me-2' : 'fa-arrow-right ms-2' }}"></i>
            </a>

            <a href="#" class="play-btn {{ app()->getLocale() == 'ar' ? 'me-2' : 'ms-2' }}">
              <i class="fa-solid fa-play"></i>
            </a>
          </div>

          <div class="d-flex gap-3 mt-5 align-items-center custom-color">
            <a href="#" class="btn btn-light rounded-pill d-flex align-items-center">
              <i class="fa-solid fa-bed {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i> {{ __('home.hero.cases') }}
            </a>
            <a href="#" class="btn btn-light rounded-pill d-flex align-items-center">
              <i class="fa-solid fa-medal {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i> {{ __('home.hero.satisfaction') }}
            </a>
          </div>
        </div>

        <div class="col-lg-6 text-center">
          <img src="{{ asset('Images/Doctor.png') }}" class="img-fluid" alt="Doctor">
        </div>

      </div>
    </div>
  </section>

  <section class="stats-section">
    <div class="container">
      <div class="row">

        <div class="col-md-3">
          <div class="item text-center {{ app()->getLocale() == 'ar' ? 'border-left-line' : 'border-right-line' }}">
            <p>{{ __('home.stats.years') }}</p>
            <h3 data-count="200">0+</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center {{ app()->getLocale() == 'ar' ? 'border-left-line' : 'border-right-line' }}">
            <p>{{ __('home.stats.doctors') }}</p>
            <h3 data-count="15">0+</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center {{ app()->getLocale() == 'ar' ? 'border-left-line' : 'border-right-line' }}">
            <p>{{ __('home.stats.staff') }}</p>
            <h3 data-count="30">0+</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="item text-center">
            <p>{{ __('home.stats.capacity') }}</p>
          <h3 data-count="4000">0+</h3>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="About"
         class="about py-5 reveal-section"
         data-dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

  <div class="container">
    <div class="row g-4">

      <h2 class="fw-bold mb-0 text-center p-4 reveal-up">
        {{ __('home.about.title') }}
      </h2>

      <div class="col-lg-6 col-md-12 d-flex custom-font">
        <div class="about-text m-auto reveal-text">
          <h2 style="font-size: 2.8rem;" class="mb-2 fw-bolder">{{ __('home.about.vision_title') }}</h2>
          <p class="fw-medium mb-3">{{ __('home.about.vision_text') }}</p>

          <h2 style="font-size: 2.8rem;" class="mt-3 mb-2 fw-bolder">{{ __('home.about.mission_title') }}</h2>
          <p class="fw-medium mb-4">{{ __('home.about.mission_text') }}</p>

          <button class="read-more-btn">{{ __('home.about.read_more') }}</button>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-center">
        <div class="row g-3 w-75 mx-auto">
          <div class="col-12 reveal-img">
            <img src="{{ asset('Images/medium-shot-doctors-discussing.jpg') }}"
                 class="img-fluid rounded-4 w-100 about-img" alt="">
          </div>
          <div class="col-12 reveal-img">
            <img src="{{ asset('Images/group-healthcare-workers-analyzing-diagnostic-data-planning-treatment.jpg') }}"
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
      <h2 class="fw-bold mb-0">{{ __('home.services.title') }}</h2>
      <p class="mb-4">{{ __('home.services.subtitle') }}</p>

      <div class="row g-4">
@foreach ($services as $service )
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
        {{-- <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-tooth"></i></div>
            <h5>{{ __('home.services.items.dentistry.title') }}</h5>
            <p>{{ __('home.services.items.dentistry.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <h5>{{ __('home.services.items.general.title') }}</h5>
            <p>{{ __('home.services.items.general.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-brain"></i></div>
            <h5>{{ __('home.services.items.neuro.title') }}</h5>
            <p>{{ __('home.services.items.neuro.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <h5>{{ __('home.services.items.cardiology.title') }}</h5>
            <p>{{ __('home.services.items.cardiology.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-pills"></i></div>
            <h5>{{ __('home.services.items.pharmacy.title') }}</h5>
            <p>{{ __('home.services.items.pharmacy.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <h5>{{ __('home.services.items.staff.title') }}</h5>
            <p>{{ __('home.services.items.staff.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-dna"></i></div>
            <h5>{{ __('home.services.items.dna.title') }}</h5>
            <p>{{ __('home.services.items.dna.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-eye"></i></div>
            <h5>{{ __('home.services.items.eye.title') }}</h5>
            <p>{{ __('home.services.items.eye.desc') }}</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon"><i class="fa-solid fa-ambulance"></i></div>
            <h5>{{ __('home.services.items.aid.title') }}</h5>
            <p>{{ __('home.services.items.aid.desc') }}</p>
          </div>
        </div> --}}

      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section id="Testimonials" class="testimonials-section" dir="ltr">
    <div class="container text-center">

      <h2 class="fw-bold mb-2">
        <span class="highlight">{{ __('home.testimonials.title') }}</span>
        {{ __('home.testimonials.title_rest_1') }} <br>
        {{ __('home.testimonials.title_rest_2') }}
      </h2>

      <p class="subtitle">
        {{ __('home.testimonials.subtitle_1') }}<br>
        {{ __('home.testimonials.subtitle_2') }}
      </p>

      <div class="carousel-area">
        <div class="carousel-track">

          @foreach(__('home.testimonials.cards') as $card)
            <div class="testimonial-card">
              <i class="quote fa-solid fa-quote-left"></i>
              <div class="user">
                <div class="avatar"></div>
                <div>
                  <h6>{{ $card['name'] }}</h6>
                  <span>{{ $card['role'] }}</span>
                </div>
              </div>
              <p>{{ $card['text'] }}</p>
            </div>
          @endforeach

        </div>
      </div>

      <div class="carousel-arrows">
        <button class="arrow prev"><i class="fa-solid fa-arrow-left"></i></button>
        <button class="arrow next"><i class="fa-solid fa-arrow-right"></i></button>
      </div>

    </div>
  </section>

  <!-- footer -->
  <footer id="contact" class="text-white pt-5" dir="ltr">
    <div class="container">
      <div class="row align-items-start">

        <div class="col-md-3 text-center text-md-start">
          <h4 class="fw-bolder mb-3">{{ __('home.footer.brand') }}</h4>
          <p class="mb-3">{{ __('home.footer.desc') }}</p>

          <div class="social-icons">
            <a href="#" class="me-2"><i class="fab fa-facebook-f text-white"></i></a>
            <a href="#" class="me-2"><i class="fab fa-twitter text-white"></i></a>
            <a href="#"><i class="fab fa-instagram text-white"></i></a>
          </div>
        </div>

        <div class="col-md-6 ms-auto">
          <div class="row align-items-start">

            <div class="col-md-6">
              <h4 class="fw-bolder mb-3">{{ __('home.footer.about_title') }}</h4>
              <ul class="list-unstyled mb-0">
                <li class="mb-1"><a href="Services.html" class="text-white text-decoration-none">{{ __('home.footer.services') }}</a></li>
                <li class="mb-1"><a href="Testimonials.html" class="text-white text-decoration-none">{{ __('home.footer.testimonials') }}</a></li>
                <li><a href="Contact Us.html" class="text-white text-decoration-none">{{ __('home.footer.contact_us') }}</a></li>
              </ul>
            </div>

            <div class="col-md-6">
              <h4 class="fw-bolder mb-3">{{ __('home.footer.contact_title') }}</h4>

              <p class="mb-1">
                <a href="tel:+20123456789" class="text-white text-decoration-none">
                  +20 123 456 789
                </a>
              </p>

              <p class="mb-1">
                <a href="mailto:clinic@email.com" class="text-white text-decoration-none">
                  clinic@email.com
                </a>
              </p>

              <p class="mb-0">{{ __('home.footer.street') }}</p>
            </div>

          </div>
        </div>

      </div>

      <hr class="my-4 footer-hr">

      <p class="text-center mb-0">
        {{ __('home.footer.rights') }}
      </p>
    </div>
  </footer>

  <script src="{{ asset('JS/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('JS/index.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const section = document.querySelector(".stats-section");
    const counters = document.querySelectorAll(".stats-section h3");

    const runCounters = () => {
      counters.forEach(counter => {
        const target = parseInt(counter.dataset.count, 10);
        const hasPlus = counter.textContent.trim().includes("+");

        let start = 0;
        const duration = 1200;
        const startTime = performance.now();

        // reset
        counter.textContent = hasPlus ? "0+" : "0";

        const animate = (time) => {
          const progress = Math.min((time - startTime) / duration, 1);
          const value = Math.floor(progress * target);

          counter.textContent = value + (hasPlus ? "+" : "");

          if (progress < 1) {
            requestAnimationFrame(animate);
          } else {
            counter.textContent = target + (hasPlus ? "+" : "");
          }
        };

        requestAnimationFrame(animate);
      });
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          runCounters();
        }
      });
    }, { threshold: 0.85 });

    observer.observe(section);
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const section = document.querySelector("#About.reveal-section");
    if (!section) return;

    const parts = section.querySelectorAll(".reveal-up, .reveal-text, .reveal-img");

    const setDelays = () => {
      parts.forEach((el, i) => {
        el.style.transitionDelay = (i * 140) + "ms";
      });
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setDelays();
          section.classList.add("is-visible");
        } else {
          // لو عايزها تتكرر كل مرة تدخل/تطلع
          section.classList.remove("is-visible");
          parts.forEach(el => el.style.transitionDelay = "0ms");
        }
      });
    }, { threshold: 0.25 });

    observer.observe(section);
  });
</script>


</body>
</html>