<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />

  <link rel="shortcut icon"
    href="{{ $setting?->logo ? asset('storage/' . $setting->logo) : asset('images/favicon.ico') }}"
    type="image/x-icon" />

  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Helper Clinic - Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <link rel="stylesheet" href="{{ asset('CSS/dashBoard.css') }}" />
</head>

<body>
  <div class="app">

    <!-- ================= Sidebar (Desktop) ================= -->
    <aside class="sidebar d-none d-lg-flex flex-column">

      <!-- Brand -->
      <div class="brand d-flex align-items-center gap-2 px-3 py-3 border-bottom">
        <div class="brand-logo">
          <i class="fa-solid fa-hospital"></i>
        </div>
        <a href="{{ route('home') }}" class="text-decoration-none">
          <span class="fw-bold fs-5">Helper Clinic</span>
        </a>
      </div>

      <!-- Navigation -->
      <nav class="side-nav flex-grow-1">
        @if(auth()->user()->role === 'admin')
          <a href="{{ route('dashboard') }}"
             class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            Dashboard
          </a>
        @endif

        <a href="{{ route('appointment.show') }}"
           class="side-link {{ request()->routeIs('appointment.*') ? 'active' : '' }}">
          <i class="fa-regular fa-calendar"></i>
          Appointment
        </a>

        @if(auth()->user()->role !== 'patient')
          <a href="{{ route('patients.index') }}"
             class="side-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            Patient
          </a>
          <a href="{{ route('admin.doctors.services.bulkEdit') }}"
             class="side-link {{ request()->routeIs('admin.doctors.services.*') ? 'active' : '' }}">
            <i class="fa-solid fa-stethoscope"></i>
            Doctor Services
          </a>
        @endif


        @if(auth()->user()->role === 'patient')
          <a href="{{ route('patient-info.my') }}"
             class="side-link {{ request()->routeIs('patient-info.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            Profile
          </a>

          <a href="{{ route('feedback.form') }}"
             class="side-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
            <i class="fa-regular fa-comment-dots"></i>
            Feedback
          </a>
        @endif

        @if(auth()->user()->role === 'doctor')
          <a href="{{ auth()->user()->doctorInfo
                    ? route('doctor-info.show', auth()->user()->doctorInfo->id)
                    : route('doctor-info.create') }}"
             class="side-link {{ request()->routeIs('doctor-info.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-doctor"></i>
            Profile
          </a>
        @endif

        <a href="{{ route('doctor.list') }}"
           class="side-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
          <i class="fa-solid fa-users"></i>
          Doctor List
        </a>

        @if(auth()->user()->role === 'admin')
          <a href="{{ route('settings.index') }}"
             class="side-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>
            Setting
          </a>

          <a href="{{ route('service.index') }}"
             class="side-link {{ request()->routeIs('service.*') ? 'active' : '' }}">
            <i class="fa-solid fa-briefcase-medical"></i>
            Services
          </a>

          <a href="{{ route('users.index') }}"
             class="side-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            Users
          </a>
          <a href="{{ route('messages.index') }}"
             class="side-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <i class="fa-solid fa-message"></i>
            Messages
          </a>
       <a href="{{ route('products.index') }}"
   class="side-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
  <i class="fa-solid fa-boxes-stacked"></i>
  <span>Products</span>
</a>

@endif
<a href="{{ route('reports.index') }}"
   class="side-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
  <i class="fa-solid fa-boxes-stacked"></i>
  <span>Reports</span>
</a>

      </nav>

      <!-- Logout (ثابت تحت) -->
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

    <!-- ================= Sidebar (Mobile / Offcanvas) ================= -->
    <div class="offcanvas offcanvas-start sidebar-offcanvas d-lg-none"
         tabindex="-1"
         id="mobileSidebar"
         aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header">
        <div class="brand d-flex align-items-center gap-2" id="mobileSidebarLabel">
          <div class="brand-logo">
            <i class="fa-solid fa-hospital"></i>
          </div>
          <a href="{{ route('home') }}" class="text-decoration-none js-offcanvas-link">
            <span class="fw-bold fs-5">Helper Clinic</span>
          </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body pt-0 d-flex flex-column">
        <nav class="side-nav flex-grow-1">

          {{-- ✅ نفس لينكات الديسكتوب بالظبط + class js-offcanvas-link --}}
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('dashboard') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <i class="fa-solid fa-border-all"></i>
              Dashboard
            </a>
          @endif

          <a href="{{ route('appointment.show') }}"
             class="side-link js-offcanvas-link {{ request()->routeIs('appointment.*') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar"></i>
            Appointment
          </a>

          @if(auth()->user()->role !== 'patient')
            <a href="{{ route('patients.index') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
              <i class="fa-solid fa-users"></i>
              Patient
            </a>
          @endif

          <a href="{{ route('admin.doctors.services.bulkEdit') }}"
             class="side-link js-offcanvas-link {{ request()->routeIs('admin.doctors.services.*') ? 'active' : '' }}">
            <i class="fa-solid fa-stethoscope"></i>
            Doctor Services
          </a>

          @if(auth()->user()->role === 'patient')
            <a href="{{ route('patient-info.my') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('patient-info.*') ? 'active' : '' }}">
              <i class="fa-solid fa-user"></i>
              Profile
            </a>

            <a href="{{ route('feedback.form') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
              <i class="fa-regular fa-comment-dots"></i>
              Feedback
            </a>
          @endif

          @if(auth()->user()->role === 'doctor')
            <a href="{{ auth()->user()->doctorInfo
                      ? route('doctor-info.show', auth()->user()->doctorInfo->id)
                      : route('doctor-info.create') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('doctor-info.*') ? 'active' : '' }}">
              <i class="fa-solid fa-user-doctor"></i>
              Profile
            </a>
          @endif

          <a href="{{ route('doctor.list') }}"
             class="side-link js-offcanvas-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            Doctor List
          </a>

          @if(auth()->user()->role === 'admin')
            <a href="{{ route('settings.index') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
              <i class="fa-solid fa-gear"></i>
              Setting
            </a>

            <a href="{{ route('service.index') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('service.*') ? 'active' : '' }}">
              <i class="fa-solid fa-briefcase-medical"></i>
              Services
            </a>

            <a href="{{ route('users.index') }}"
               class="side-link js-offcanvas-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
              <i class="fa-solid fa-users"></i>
              Users
            </a>
          @endif

        </nav>

        <!-- Logout (ثابت تحت في الموبايل) -->
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

    <!-- ================= Page Content ================= -->
    <div class="main-wrap">
      @yield('dash-content')
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('JS/dashBord.js') }}"></script>

  <!-- ✅ Fix: close offcanvas AFTER click (بدون data-bs-dismiss على اللينكات) -->
  <script>
    document.addEventListener('click', function (e) {
      const link = e.target.closest('.js-offcanvas-link');
      if (!link) return;

      const offcanvasEl = document.getElementById('mobileSidebar');
      if (!offcanvasEl) return;

      const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
      if (instance) instance.hide();
    });
  </script>
</body>
</html>
