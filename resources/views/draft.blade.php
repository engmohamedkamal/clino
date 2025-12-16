<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clino</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <link rel="stylesheet" href="CSS/bootstrap.min.css">
  <link rel="stylesheet" href="CSS/index.css">
</head>

<body>


  <!-- Navbar -->

  <nav class="navbar navbar-expand-lg bg-white py-3">
    <div class="container">
      <a class="navbar-brand fw-bold  nav-logo" href="#">Helper Clinic</a>

      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav mx-auto gap-3 ">
          <li class="nav-item"><a class="nav-link active " href="#Home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#About">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="#Testimonials">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
        </ul>

        <a class="btn btn-primary rounded-pill px-4" href="#">Dashboard</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->

  <section id="Home" class="hero-section ">
    <div class="container rounded-5 ">
      <div class="row align-items-center">

        <div class="col-lg-6">
          <span class="badge  text-white mb-5">Your health, your choice</span>

          <h1 class="fw-bolder mt-3 text-white">
            Secure Your <span style="color: #FFFF00;">Doctor</span> Visit Anytime, Anywhere
          </h1>

          <p class="mt-3 text-white">
            Easily schedule a medical consultation with your preferred doctor at a time that suits you best.
          </p>


          <div class="d-flex gap-3 mt-5 align-items-center custom-color">
            <a href="#" class=" btn btn-light rounded-pill d-flex align-items-center">
              <i class="fa-solid fa-bed me-2"></i> 3K The cases in the hospital
            </a>
            <a href="#" class="btn btn-light rounded-pill  d-flex align-items-center">
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


      <div class="row g-4">



        <h2 class="fw-bold mb-0 text-center p-4">About Us</h2>
        <!-- Text -->
        <div class="col-lg-6 col-md-12 d-flex custom-font">
          <div class="about-text m-auto"> <!-- m-auto هيوسّط النص عموديًا -->
            <h2 style="font-size:  2.8rem;" class="  mb-2 fw-bolder ">Our Vision</h2>
            <p class="fw-medium mb-3">
              To become a trusted healthcare provider that sets new standards in medical care
              by combining advanced technology, professional expertise, and a patient-centered
              approach—ensuring better health and well-being for every patient we serve.
            </p>

            <h2 style="font-size: 2.8rem;" class=" mt-3 mb-2 fw-bolder ">Our Mission</h2>
            <p class="fw-medium mb-4 ">
              Our mission is to deliver high-quality, reliable, and accessible healthcare services
              through experienced medical professionals, modern medical technologies, and a seamless
              patient experience—while prioritizing safety, comfort, and long-term trust.
            </p>


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

      <h2 class="fw-bold mb-0">Our Services</h2>
      <p class="mb-4">Comprehensive Healthcare Services Delivered With Compassion And Excellence</p>

      <div class="row g-4">

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-tooth"></i>
            </div>
            <h5>Dentistry</h5>
            <p>Get consultation from our Dentistry team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-stethoscope"></i>
            </div>
            <h5>General Diagnosis</h5>
            <p>Get consultation from our General team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-brain"></i>
            </div>
            <h5>Neuro Surgery</h5>
            <p>Get consultation from our Neuro team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <h5>Cardiology</h5>
            <p>Get consultation from our Cardiology team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-pills"></i> <!-- Pharmacy -->
            </div>
            <h5>Pharmacy</h5>
            <p>Get consultation from our Pharmacy team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-user-doctor"></i> <!-- Trained Staff -->
            </div>
            <h5>Trained Staff</h5>
            <p>Get consultation from our Trained staff team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-dna"></i> <!-- DNA Mapping -->
            </div>
            <h5>DNA Mapping</h5>
            <p>Get consultation from our DNA Mapping team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-eye"></i> <!-- Ophthalmology -->
            </div>
            <h5>Ophthalmology</h5>
            <p>Get consultation from our Ophthalmology team</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="service-card">
            <div class="service-icon">
              <i class="fa-solid fa-ambulance"></i> <!-- Medical Aid -->
            </div>
            <h5>Medical Aid</h5>
            <p>Get consultation from our Emergency Medical Aid Team</p>
          </div>
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

          <!-- Card 1 -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>John Smith</h6>
                <span>CEO of Redbird Company</span>
              </div>
            </div>
            <p>
              It was a pleasure to work with them. They provided valuable insights
              and excellent solutions.
            </p>
          </div>

          <!-- Card 2 -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>Sarah Lee</h6>
                <span>Marketing Manager</span>
              </div>
            </div>
            <p>
              Working with Katalyst Studio has been an incredible experience.
              They truly listened to our needs and delivered a stunning design
              that exceeded our expectations.
            </p>
          </div>

          <!-- Card 3 (Active أول ما يفتح) -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>Alan Baker</h6>
                <span>CEO of Redbird Company</span>
              </div>
            </div>
            <p>
              Working with Katalyst Studio has been an incredible experience.
              They truly listened to our needs and delivered a stunning design
              that exceeded our expectations.
            </p>
          </div>

          <!-- Card 4 -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>Theresa Webb</h6>
                <span>CEO of Redbird Company</span>
              </div>
            </div>
            <p>
              From start to finish, the experience was smooth, professional,
              and within budget.
            </p>
          </div>

          <!-- Card 5 -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>Michael Brown</h6>
                <span>Product Manager</span>
              </div>
            </div>
            <p>
              Highly recommend them. Excellent communication and results.
            </p>
          </div>

          <!-- Card 6 -->
          <div class="testimonial-card">
            <i class="quote fa-solid fa-quote-left"></i>
            <div class="user">
              <div class="avatar"></div>
              <div>
                <h6>Emma Wilson</h6>
                <span>UI/UX Designer</span>
              </div>
            </div>
            <p>
              Great attention to details and very friendly team.
            </p>
          </div>

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

  <!-- footer -->
 <footer id="contact" class="text-white pt-5">
  <div class="container">
    <div class="row align-items-start">

      <!-- Helper Clinic -->
      <div class="col-md-3 text-center text-md-start">
        <h4 class="fw-bolder mb-3">Helper Clinic</h4>
        <p class="mb-3">The ultimate destination for all medical needs.</p>

        <div class="social-icons">
          <a href="#" class="me-2"><i class="fab fa-facebook-f text-white"></i></a>
          <a href="#" class="me-2"><i class="fab fa-twitter text-white"></i></a>
          <a href="#"><i class="fab fa-instagram text-white"></i></a>
        </div>
      </div>

      <!-- Right side -->
      <div class="col-md-6 ms-auto">
        <div class="row align-items-start">

          <!-- About -->
          <div class="col-md-6">
            <h4 class="fw-bolder mb-3">About Us</h4>
            <ul class="list-unstyled mb-0">
              <li class="mb-1"><a href="#services" class="text-white text-decoration-none">Services</a></li>
              <li class="mb-1"><a href="#Testimonials" class="text-white text-decoration-none">Testimonials</a></li>
              <li><a href="Contact Us.html" class="text-white text-decoration-none">Contact Us</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div class="col-md-6">
            <h4 class="fw-bolder mb-3">Contact</h4>

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

            <p class="mb-0">Elmahkama Street</p>
          </div>

        </div>
      </div>

    </div>

    <hr class="my-4 footer-hr">

    <p class="text-center mb-0">
      © 2024 Helper Clinic. All Rights Reserved
    </p>
  </div>
</footer>



  <!-- Bootstrap JS -->
  <script src="JS/bootstrap.bundle.min.js"></script>
  <script src="JS/index.js"></script>
</body>

</html>