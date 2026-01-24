<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />

  <link rel="shortcut icon"
    href="{{ $setting?->logo ? asset('storage/' . $setting->logo) : asset('images/favicon.ico') }}"
    type="image/x-icon" />

  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Helper Clinic - Dashboard</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('CSS/dashBoard.css') }}" />
</head>

<body>

@php
  $role = auth()->user()->role ?? '';

  $nav = [];

  // ================= ADMIN =================
  if ($role === 'admin') {
    $nav = [
      ['route' => 'dashboard', 'icon' => 'fa-solid fa-border-all', 'label' => 'Dashboard'],
      ['route' => 'appointment.show', 'icon' => 'fa-regular fa-calendar', 'label' => 'Appointment'],

     
      ['route' => 'patients.index', 'icon' => 'fa-solid fa-users', 'label' => 'Patient'],

      ['route' => 'admin.doctors.services.bulkEdit', 'icon' => 'fa-solid fa-stethoscope', 'label' => 'Doctor Services'],
    
      ['route' => 'invoices.index', 'icon' => 'fa-solid fa-circle-plus', 'label' => 'Invoices'],
      ['route' => 'service-invoices.index', 'icon' => 'fa-solid fa-file-invoice-dollar', 'label' => 'Service Invoices'],
      ['route' => 'patient-transfers.index', 'icon' => 'fa-solid fa-right-left', 'label' => 'Patient Transfers'],

      ['route' => 'diagnoses.index', 'icon' => 'fa-solid fa-notes-medical', 'label' => 'Diagnoses'],
      ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
      ['route' => 'prescriptions.index', 'icon' => 'fa-solid fa-prescription-bottle-medical', 'label' => 'Prescription'],

      ['route' => 'products.index', 'icon' => 'fa-solid fa-boxes-stacked', 'label' => 'Products'],
      ['route' => 'messages.index', 'icon' => 'fa-solid fa-message', 'label' => 'Messages'],

      ['route' => 'doctor.list', 'icon' => 'fa-solid fa-users', 'label' => 'Doctor List'],
      ['route' => 'service.index', 'icon' => 'fa-solid fa-briefcase-medical', 'label' => 'Services'],
      ['route' => 'users.index', 'icon' => 'fa-solid fa-users', 'label' => 'Users'],
      ['route' => 'settings.index', 'icon' => 'fa-solid fa-gear', 'label' => 'Setting'],
    ];
  }

  // ================= DOCTOR (Appointment + Profile only ✅) =================
  if ($role === 'doctor') {
    $nav = [
      ['route' => 'appointment.show', 'icon' => 'fa-regular fa-calendar', 'label' => 'Appointment'],
      [
        'href' => (auth()->user()->doctorInfo
          ? route('doctor-info.show', auth()->user()->doctorInfo->id)
          : route('doctor-info.create')),
        'icon' => 'fa-solid fa-user-doctor',
        'label' => 'Profile',
        'is_active' => request()->routeIs('doctor-info.*'),
      ],
      ['route' => 'medical-orders.index', 'icon' => 'fa-solid fa-notes-medical', 'label' => 'Medical Orders'],
         ['route' => 'cash.index', 'icon' => 'fa-solid fa-cash-register', 'label' => 'Cash Management'],
      ];
  }

  // ================= PATIENT =================
  if ($role === 'patient') {
    $nav = [
      ['route' => 'appointment.show', 'icon' => 'fa-regular fa-calendar', 'label' => 'Appointment'],

      // ✅ Patient can view his records
      ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
      ['route' => 'prescriptions.index', 'icon' => 'fa-solid fa-prescription-bottle-medical', 'label' => 'Prescription'],
      ['route' => 'diagnoses.index', 'icon' => 'fa-solid fa-notes-medical', 'label' => 'Diagnoses'],
      ['route' => 'patient-transfers.index', 'icon' => 'fa-solid fa-right-left', 'label' => 'Transfers'],

      // ✅ Patient Profile (route has {id})
      [
        'href' => route('patient-info.my', auth()->id()),
        'icon' => 'fa-solid fa-user',
        'label' => 'Profile',
        'is_active' => request()->routeIs('patient-info.*'),
      ],

      ['route' => 'feedback.form', 'icon' => 'fa-regular fa-comment-dots', 'label' => 'Feedback'],
    ];
  }
  // ================= secretary =================
  if ($role === 'secretary') {
    $nav = [
      ['route' => 'dashboard', 'icon' => 'fa-solid fa-border-all', 'label' => 'Dashboard'],
      ['route' => 'appointment.show', 'icon' => 'fa-regular fa-calendar', 'label' => 'Appointment'],
      ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
      ['route' => 'patients.index', 'icon' => 'fa-solid fa-users', 'label' => 'Patient'],
      ['route' => 'cash.index', 'icon' => 'fa-solid fa-cash-register', 'label' => 'Cash Management'],
    ];
  }
@endphp

<div class="app">

  <!-- ================= Sidebar Desktop ================= -->
  <aside class="sidebar d-none d-lg-flex flex-column">

    <div class="brand d-flex align-items-center gap-2 px-3 py-3 border-bottom">
      <div class="brand-logo"><i class="fa-solid fa-hospital"></i></div>
      <a href="{{ route('home') }}" class="text-decoration-none">
        <span class="fw-bold fs-5">Helper Clinic</span>
      </a>
    </div>

    <nav class="side-nav flex-grow-1">
      @foreach($nav as $item)
        @php
          $href = $item['href'] ?? (isset($item['route']) ? route($item['route']) : '#');
          $active = $item['is_active'] ?? (isset($item['route']) ? request()->routeIs(($item['route']).'*') : false);
        @endphp
        <a href="{{ $href }}" class="side-link {{ $active ? 'active' : '' }}">
          <i class="{{ $item['icon'] }}"></i>
          {{ $item['label'] }}
        </a>
      @endforeach
    </nav>

    <div class="mt-auto">
      <div class="side-divider"></div>
      <form method="POST" action="{{ route('logout') }}" class="p-2">
        @csrf
        <button type="submit" class="side-link logout-link w-100 text-start border-0 bg-transparent">
          <i class="fa-solid fa-right-from-bracket"></i>
          Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- ================= Sidebar Mobile ================= -->
  <div class="offcanvas offcanvas-start sidebar-offcanvas d-lg-none" id="mobileSidebar">
    <div class="offcanvas-header">
      <div class="brand d-flex align-items-center gap-2">
        <div class="brand-logo"><i class="fa-solid fa-hospital"></i></div>
        <span class="fw-bold fs-5">Helper Clinic</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
      <nav class="side-nav flex-grow-1">
        @foreach($nav as $item)
          @php
            $href = $item['href'] ?? (isset($item['route']) ? route($item['route']) : '#');
            $active = $item['is_active'] ?? (isset($item['route']) ? request()->routeIs(($item['route']).'*') : false);
          @endphp
          <a href="{{ $href }}" class="side-link js-offcanvas-link {{ $active ? 'active' : '' }}">
            <i class="{{ $item['icon'] }}"></i>
            {{ $item['label'] }}
          </a>
        @endforeach
      </nav>

      <div class="mt-auto">
        <div class="side-divider"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="side-link logout-link w-100 text-start border-0 bg-transparent">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="main-wrap">
    @yield('dash-content')
  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('click', function (e) {
    const link = e.target.closest('.js-offcanvas-link');
    if (!link) return;
    const offcanvas = document.getElementById('mobileSidebar');
    const instance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (instance) instance.hide();
  });
</script>

</body>
</html>
