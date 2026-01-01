@extends('layouts.dash')
@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/addReports.css') }}" />

      <main class="main">

      <!-- Topbar -->
      <header class="topbar">
        <div class="d-flex align-items-center gap-2">
          <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div class="page-title">Add Reports</div>
        </div>

        <div class="search-wrap">
          <input class="form-control search-input" type="text" placeholder="Search type of keywords" />
          <i class="fa-solid fa-magnifying-glass search-ico"></i>
        </div>

        <button class="btn icon-btn" type="button" aria-label="notifications">
          <i class="fa-regular fa-bell"></i>
        </button>
      </header>


      <!-- Content (fits viewport, no page scroll) -->


<!-- Reports Form Content فقط -->
<section class="rp-wrap py-4">
  <div class="container">

    <div class="rp-panel mx-auto">
      <form class="rp-form">

        <div class="row g-4">

          <!-- Left -->
          <div class="col-12 col-md-6">
            <label class="rp-label">Type of examination</label>
            <div class="rp-field">
              <select class="rp-control">
                <option selected disabled>Select</option>
                <option>Lab Result</option>
                <option>X-Ray</option>
                <option>Prescription</option>
              </select>
              <i class="bi bi-chevron-down rp-sfx"></i>
            </div>

            <label class="rp-label mt-4">Examination Date</label>
            <div class="rp-field">
              <input class="rp-control" type="date" placeholder="mm/dd/yyyy" />
              <i class="bi bi-calendar3 rp-sfx"></i>
            </div>
          </div>

          <!-- Right -->
          <div class="col-12 col-md-6">
            <label class="rp-label">status</label>
            <div class="rp-field">
              <select class="rp-control">
                <option selected disabled>Select</option>
                <option>Pending</option>
                <option>Completed</option>
                <option>Cancelled</option>
              </select>
              <i class="bi bi-chevron-down rp-sfx"></i>
            </div>

            <label class="rp-label mt-4">Examination image</label>
            <div class="rp-field">
              <input class="rp-control" type="file" />
              <i class="bi bi-image rp-sfx"></i>
            </div>
          </div>

          <!-- About -->
          <div class="col-12">
            <label class="rp-label">About</label>
            <textarea class="rp-control rp-textarea" placeholder="about"></textarea>
          </div>

          <!-- Save -->
          <div class="col-12">
            <button class="rp-save" type="submit">Save</button>
          </div>

        </div>
      </form>
    </div>

  </div>
</section>


    </main>

@endsection