@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('CSS/about.css') }}">
<title>About Us</title>
<section class="stats-section" id="statsSection">
  <div class="container rounded-3">
    <div class="row">

      <div class="col-md-3">
        <div class="item text-center border-right-line">
          <p>Years Experience</p>
          <h3 class="counter" data-count="15">0+</h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="item text-center border-right-line">
          <p>Expert Doctors</p>
          <h3 class="counter" data-count="30">0+</h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="item text-center border-right-line">
          <p>Medical Staff</p>
          <h3 class="counter" data-count="200">0+</h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="item text-center">
          <p>Patient Capacity</p>
          <h3 class="counter" data-count="4000">0+</h3>
        </div>
      </div>

    </div>
  </div>
</section>



    <!-- Vision -->
    <section class="vm-section">
        <div class="container">
            <div class="row align-items-start gy-4">
                <!-- Text (Left) -->
                <div class="col-12 col-lg-7">
                    <h2 class="vm-title">Our Vision</h2>
                    <p class="vm-text">
                       {{$setting->vision ?? ''}}
                    </p>
                </div>

                <!-- Quotes (Right) -->
                <div class="col-12 col-lg-5 d-flex justify-content-lg-end">
                    <div class="vm-quotes" aria-hidden="true">
                        <span class="q">“</span>
                        <span class="q">”</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission -->
    <section class="vm-section vm-section--space">
        <div class="container">
            <div class="row align-items-start gy-4">
                <!-- Text (Left) -->
                <div class="col-12 col-lg-7">
                    <h2 class="vm-title">Our Mission</h2>
                    <p class="vm-text">
                       {{$setting->mission ?? ''}}
                    </p>
                </div>

                <!-- Quotes (Right) -->
                <div class="col-12 col-lg-5 d-flex justify-content-lg-end">
                    <div class="vm-quotes" aria-hidden="true">
                        <span class="q">“</span>
                        <span class="q">”</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section py-4 py-md-5" id="team">
        <div class="container">

            <div class="text-center mb-4">
                <h3 class="team-title">
                    Our <span class="team-pill">Team Members</span>
                </h3>
                <p class="team-sub">
                    At Helper Clinic, we provide trusted medical care through experienced professionals and modern
                    technology.
                    Our goal is to ensure every patient receives safe, comfortable, and reliable healthcare in a supportive
                    environment.
                </p>
            </div>

            <!-- GRID -->
            <div class="row g-4 justify-content-center" id="teamGrid">
                <!-- كرر team-item بقى (100 دكتور عادي) -->
                @foreach ($doctors as $doctor )
                    <div class="col-12 col-sm-6 col-lg-3 team-item">
                    <div class="team-card">
                        <div class="team-avatar"><img src="{{asset('storage/'.$doctor->image)}}" alt="doctor"></div>
                        <div class="team-name">{{ $doctor->user->name ?? '' }}</div>
                        <div class="team-role">
    {{ is_array($doctor->Specialization) ? implode(' • ', $doctor->Specialization) : $doctor->Specialization }}
</div>

                        <div class="team-social">
                            <a href="{{ $doctor->facebook ?? '' }}" aria-label="facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="{{ $doctor->instagram ?? '' }}" aria-label="instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="{{ $doctor->twitter ?? '' }}" aria-label="twitter"><i class="fa-brands fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>

            <!-- arrows + page indicator -->
            <div class="team-controls mt-4">
                <button class="arrow-btn" id="teamPrev" type="button" aria-label="previous">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <div class="team-page" id="teamPageText">1 / 1</div>

                <button class="arrow-btn" id="teamNext" type="button" aria-label="next">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

        </div>
    </section>
<script>
      (function () {
  const grid = document.getElementById("teamGrid");
  const prevBtn = document.getElementById("teamPrev");
  const nextBtn = document.getElementById("teamNext");
  const pageText = document.getElementById("teamPageText");

  if (!grid || !prevBtn || !nextBtn || !pageText) return;

  const items = Array.from(grid.querySelectorAll(".team-item"));
  let page = 0;

 

  function perPage() {
    const w = window.innerWidth;
    if (w >= 992) return 8;  // lg+
    if (w >= 576) return 4;  // sm - md
    return 2;                // xs
  }

  function render() {
    const pp = perPage();
    const totalPages = Math.max(1, Math.ceil(items.length / pp));
    if (page > totalPages - 1) page = totalPages - 1;
    if (page < 0) page = 0;

    const start = page * pp;
    const end = start + pp;

    items.forEach((el, i) => {
      el.classList.toggle("d-none", !(i >= start && i < end));
    });

    pageText.textContent = `${page + 1} / ${totalPages}`;

    prevBtn.disabled = page === 0;
    nextBtn.disabled = page === totalPages - 1;
  }

  nextBtn.addEventListener("click", () => {
    page++;
    render();
  });

  prevBtn.addEventListener("click", () => {
    page--;
    render();
  });

  window.addEventListener("resize", render);

  // init
  render();
})();

document.addEventListener("DOMContentLoaded", function () {

  const section = document.getElementById("statsSection");
  const counters = section.querySelectorAll(".counter");

  function resetCounters() {
    counters.forEach(c => {
      if (!c.dataset.init) {
        c.dataset.plus = c.textContent.includes("+") ? "1" : "0";
        c.dataset.init = "1";
      }
      c.textContent = "0" + (c.dataset.plus === "1" ? "+" : "");
    });
  }

  function animateCounter(counter) {
    const target = +counter.dataset.count;
    const hasPlus = counter.dataset.plus === "1";
    const duration = 900;
    const start = performance.now();

    function tick(now) {
      const progress = Math.min((now - start) / duration, 1);
      const value = Math.floor(progress * target);
      counter.textContent = value + (hasPlus ? "+" : "");

      if (progress < 1) {
        requestAnimationFrame(tick);
      } else {
        counter.textContent = target + (hasPlus ? "+" : "");
      }
    }

    requestAnimationFrame(tick);
  }

  resetCounters();

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        resetCounters();
        counters.forEach(animateCounter);
      }
    });
  }, { threshold: 0.4 });

  observer.observe(section);

});
</script>


@endsection