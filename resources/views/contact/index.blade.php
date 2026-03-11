@extends('layouts.app')    
@section('content')
 <link rel="stylesheet" href="{{asset("css/contact.css")}}">
<title>Contact Us</title>
<main class="contact-main py-5">

  <!-- heading -->
  <div class="container">
    <div class="text-center mb-4">
      <div class="mini-title">Online Inquiry Form</div>
      <div class="mini-sub">
        Please fill in the following details, and we’ll get back to you within 24 hours.
      </div>
    </div>
  </div>

 <div class="container">
  <div class="contact-wrap mx-auto">

    <!-- form card -->
   <div class="inquiry-card">
   @if(session('success'))
    <div id="successAlert" class="alert alert-success text-center">
      {{ session('success') }}
    </div>
@endif

  <form method="POST" novalidate action="{{ route('contact.store') }}">
    @csrf

    {{-- Service --}}
    <div class="mb-4">
      <label for="service" class="inquiry-label">Select Service</label>

      <select id="service" name="service" class="form-select inquiry-control @error('service') is-invalid @enderror">
        <option disabled {{ old('service') ? '' : 'selected' }}>Select your Service</option>

        @foreach ($services as $service)
          <option value="{{ $service->name }}" {{ old('service') == $service->name ? 'selected' : '' }}>
            {{ $service->name }}
          </option>
        @endforeach
      </select>

     
    </div>

    {{-- Message --}}
    <div class="mb-4">
      <label for="message" class="inquiry-label">Message</label>

      <textarea id="message" 
        rows="4"
        name="message"
        class="form-control inquiry-control inquiry-textarea @error('message') is-invalid @enderror"
        placeholder="Enter your Message">{{ old('message') }}</textarea>

    
    </div>

    {{-- Submit Button --}}
    <div class="text-center">
      <button type="submit" class="btn inquiry-btn">
        Send your Inquiry <i class="fa-solid fa-arrow-right ms-2"></i>
      </button>
    </div>

  </form>
</div>

<script>
    setTimeout(() => {
        let alertBox = document.getElementById('successAlert');
        if (alertBox) {
            alertBox.style.transition = "0.5s";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3000);
</script>

    <!-- two small cards -->
    <div class="row g-3 mt-3">
      <div class="col-12 col-md-6">
        <div class="info-card">
          <div class="info-head">
            <span class="info-ico"><i class="fa-solid fa-circle-check"></i></span>
            <div class="info-title">Our Response</div>
          </div>

          <p class="info-text">
            We understand the importance of timely responses, and our team is committed to addressing your inquiries
            promptly. Whether you have a specific request in mind, need advice on digital strategies, or want to explore
            partnership opportunities, we are here to assist you at every step.
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6">
        <div class="info-card">
          <div class="info-head">
            <span class="info-ico"><i class="fa-solid fa-shield-heart"></i></span>
            <div class="info-title">Privacy Assurance</div>
          </div>

          <p class="info-text">
            At Digix, we prioritize your privacy and protect your personal information in compliance with data
            protection regulations. Rest assured that your details will be only used for the purpose of addressing your
            inquiries and will not be shared with third parties without your consent.
          </p>
        </div>
      </div>
    </div>

  </div>
</div>


  <!-- care section -->
  <div class="container mt-5">
    <div class="text-center care-block">
      <div class="care-title">We’re Here to Care for You</div>
      <p class="care-text">
        Stay connected with Helper Clinic for the latest health updates, medical services, and important announcements.
      </p>

      <div class="care-social">
        <a href="{{ $setting->facebook }}" target="_blank" aria-label="facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="{{ $setting->instagram }}" target="_blank" aria-label="instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="{{ $setting->twitter }}" target="_blank" aria-label="twitter"><i class="fa-brands fa-twitter"></i></a>
      </div>

      <p class="care-foot">
        Follow us on social media and feel free to reach out — we’re always here to support your health journey.
      </p>
    </div>
  </div>

</main>
@endsection