@extends('layouts.dash')
@section('dash-content')
  <link rel="stylesheet" href="{{asset('CSS/reports.css')}}">
    <main class="rp-main mb-3">

  <!-- Top Bar -->
  
     <header class="topbar  ">
        <div class="d-flex align-items-center gap-2">
          <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div class="page-title">Reports</div>
        </div>

        <div class="search-wrap">
          <input class="form-control search-input" type="text" placeholder="Search type of keywords" />
          <i class="fa-solid fa-magnifying-glass search-ico"></i>
        </div>

        <button class="btn icon-btn" type="button" aria-label="notifications">
          <i class="fa-regular fa-bell"></i>
        </button>
      </header>


  <!-- Stats -->
  <section class="row g-3 rp-stats">
    <div class="col-12 col-md-6 col-xl-4">
      <div class="rp-stat-card">
        <div>
          <div class="rp-stat-label">Total Reports</div>
          <div class="rp-stat-value">24</div>
        </div>
        <div class="rp-stat-ico rp-bg-blue">
          <i class="bi bi-folder2"></i>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4">
      <div class="rp-stat-card">
        <div>
          <div class="rp-stat-label">Last Visit Date</div>
          <div class="rp-stat-value">Oct 24, 2023</div>
        </div>
        <div class="rp-stat-ico rp-bg-green">
          <i class="bi bi-calendar-check"></i>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4">
      <div class="rp-stat-card">
        <div>
          <div class="rp-stat-label">Active Prescriptions</div>
          <div class="rp-stat-value">3</div>
        </div>
        <div class="rp-stat-ico rp-bg-purple">
          <i class="bi bi-capsule"></i>
        </div>
      </div>
    </div>
  </section>

  <!-- Tabs + Filters Wrapper -->
  <section class="rp-block">

    <!-- Tabs -->
    <nav class="rp-tabs" aria-label="Reports Tabs">
      <button class="rp-tab" type="button">
        <i class="bi bi-folder2"></i>
        <span>All Reports</span>
      </button>

      <button class="rp-tab" type="button">
        <i class="bi bi-file-earmark-text"></i>
        <span>Prescriptions</span>
      </button>

      <button class="rp-tab" type="button">
        <i class="bi bi-image"></i>
        <span>Radiology</span>
      </button>

      <button class="rp-tab" type="button">
        <i class="bi bi-beaker"></i>
        <span>Lab Results</span>
      </button>
    </nav>

    <hr class="rp-sep">

    <!-- Filters Row (Bootstrap Grid) -->
  <div class="row g-2 align-items-center rp-filters">

  <!-- Search -->
  <div class="col">
    <div class="rp-input">
      <i class="bi bi-search"></i>
      <input type="text" class="form-control" placeholder="Search by report">
    </div>
  </div>

  <!-- Date -->
  <div class="col-auto">
    <button class="rp-date" type="button">
      <span class="rp-date-left">
        <span class="rp-date-ico">
          <i class="bi bi-calendar3"></i>
        </span>
        <span class="rp-date-text">
          <small>DATE PERIOD</small>
          <strong>Oct 01 - Oct 31, 2023</strong>
        </span>
      </span>
      <i class="bi bi-chevron-down"></i>
    </button>
  </div>

  <!-- Actions -->
  <div class="col-auto">
    <div class="d-flex gap-2">
      <button class="rp-square-btn" type="button">
        <i class="bi bi-arrow-down-up"></i>
      </button>

      <button class="rp-filter-btn" type="button">
        <i class="bi bi-funnel"></i>
        <span>Filter</span>
      </button>
    </div>
  </div>

  <div class="col-auto">
    <button class="rp-add-btn" type="button" aria-label="Add">
      <i class="bi bi-plus-lg"></i>
    </button>
  </div>

</div>

    
  </section>

  <!-- Cards Grid -->

<section class="row g-3 rp-grid">

  <!-- Card 1 -->
  <div class="col-12 col-md-6 col-xl-4">
    <article class="rp-card">
      <div class="rp-card-body">

        <div class="rp-card-head">
          <div class="rp-card-ico rp-ico-blue">
            <i class="bi bi-file-earmark-text"></i>
          </div>

          <div class="rp-card-main">
            <div class="rp-card-title">
              <h6>General Checkup</h6>
            </div>
            <div class="rp-card-meta">Prescription • Oct 20, 2023</div>
          </div>
        </div>

        <div class="rp-card-row">
          <i class="bi bi-person"></i>
          <span>Dr. Sarah Smith</span>
        </div>

        <div class="rp-card-row">
          <i class="bi bi-clipboard2"></i>
          <span>Amoxicillin 500mg (2x Daily), Paracetamol 650mg (SOS)</span>
        </div>

      </div>

      <footer class="rp-card-foot rp-foot-purple">
        <span class="rp-id">ID: 882109</span>

        <div class="rp-foot-actions">
          <span class="rp-pill rp-pill-done">Completed</span>

          <button class="rp-foot-btn" type="button" aria-label="View">
            <i class="bi bi-eye"></i>
          </button>
          <button class="rp-foot-btn" type="button" aria-label="Download">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </footer>
    </article>
  </div>

  <!-- Card 2 -->
  <div class="col-12 col-md-6 col-xl-4">
    <article class="rp-card">
      <div class="rp-card-body">

        <div class="rp-card-head">
          <div class="rp-card-ico rp-ico-purple">
            <i class="bi bi-image"></i>
          </div>

          <div class="rp-card-main">
            <div class="rp-card-title">
              <h6>Chest X-Ray</h6>
            </div>
            <div class="rp-card-meta">Radiology • Oct 15, 2023</div>
          </div>
        </div>

        <div class="rp-card-row">
          <i class="bi bi-person"></i>
          <span>Dr. James Ray</span>
        </div>

        <div class="rp-card-row">
          <i class="bi bi-card-text"></i>
          <span>PA View - No active lesions found. Lungs clear.</span>
        </div>

      </div>

      <footer class="rp-card-foot rp-foot-purple">
        <span class="rp-id">ID: XR-2921</span>

        <div class="rp-foot-actions">
          <span class="rp-pill rp-pill-done">Completed</span>

          <button class="rp-foot-btn" type="button" aria-label="View">
            <i class="bi bi-eye"></i>
          </button>
          <button class="rp-foot-btn" type="button" aria-label="Download">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </footer>
    </article>
  </div>

  <!-- Card 3 (Pending) -->
<div class="col-12 col-md-6 col-xl-4">
  <article class="rp-card">
    <div class="rp-card-body">

      <div class="rp-card-head">
        <div class="rp-card-ico rp-ico-orange">
          <!-- ICON -->
          <i class="bi bi-droplet"></i>
        </div>

        <div class="rp-card-main">
          <div class="rp-card-title">
            <h6>Blood Analysis</h6>
          </div>
          <div class="rp-card-meta">Lab Result • Oct 10, 2023</div>
        </div>
      </div>

      <div class="rp-card-row">
        <i class="bi bi-building"></i>
        <span>Central Pathology Lab</span>
      </div>

      <div class="rp-card-row">
        <i class="bi bi-list-check"></i>
        <span>Complete Blood Count (CBC), Lipid Profile</span>
      </div>

    </div>

    <footer class="rp-card-foot rp-foot-purple">
      <span class="rp-id">ID: LAB-9922</span>

      <div class="rp-foot-actions">
        <span class="rp-pill rp-pill-pending">Pending</span>

        <button class="rp-foot-btn is-disabled" type="button" disabled>
          <i class="bi bi-eye-slash"></i>
        </button>
        <button class="rp-foot-btn is-disabled" type="button" disabled>
          <i class="bi bi-download"></i>
        </button>
      </div>
    </footer>
  </article>
</div>


</section>


</main>
@endsection