<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Helper Clinic - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('CSS/dashBoard.css') }}" />
</head>

<body>
    <div class="app">
        <div class="app">
            <!-- ================= Sidebar (Desktop) ================= -->
            <aside class="sidebar d-none d-lg-flex">

                <!-- Brand -->
                <div class="brand d-flex align-items-center gap-2 px-3 py-3 border-bottom">
                    <div class="brand-logo">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <span class="fw-bold fs-5">Helper Clinic</span>
                </div>

                <!-- Navigation -->
                <nav class="side-nav">

                    <a href="{{ route('dashboard') }}"
                        class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-border-all"></i>
                        Dashboard
                    </a>

                    {{-- <a href="{{ route('users.index') }}"
                        class="side-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        Users
                    </a>

                    <a href="{{ route('services.index') }}"
                        class="side-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        Services
                    </a>--}}

                    <a href="{{ route('appointment') }}"
                        class="side-link {{ request()->routeIs('appointment.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar"></i>
                        Appointment
                    </a>

                    {{-- <a href="{{ route('doctors.index') }}"
                        class="side-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-doctor"></i>
                        Doctor
                    </a> --}}
                    @if(Auth()->user()->role !== 'patient')
                        <a href="{{ route('patients.index') }}"
                            class="side-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users"></i>
                            Patient
                        </a>
                    @endif
                    @if(Auth()->user()->role === 'patient')
                        <a href="{{ route('patient-info.my') }}"
                            class="side-link {{ request()->routeIs('patient-info.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users"></i>
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
                    
                    {{-- <a href="{{ route('reports.index') }}"
                        class="side-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-file-lines"></i>
                        Report
                    </a>

                    <a href="{{ route('settings.index') }}"
                        class="side-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gear"></i>
                        Setting
                    </a>--}}


                    <div class="side-divider"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="side-link logout-link w-100 text-start border-0 bg-transparent">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>

                </nav>
            </aside>


        </div>

        <!-- ================= Sidebar (Mobile / Offcanvas) ================= -->
        <div class="offcanvas offcanvas-start sidebar-offcanvas d-lg-none" tabindex="-1" id="mobileSidebar">

            <div class="offcanvas-header">
                <div class="brand d-flex align-items-center gap-2">
                    <div class="brand-logo">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <span class="fw-bold fs-5">Helper Clinic</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body pt-0">
                <nav class="side-nav">

                    <a href="dashBoard.html" class="side-link active">
                        <i class="fa-solid fa-border-all"></i>
                        Dashboard
                    </a>

                    <a href="#" class="side-link">
                        <i class="fa-solid fa-users"></i>
                        Users
                    </a>

                    <a href="#" class="side-link">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        Services
                    </a>

                    <a href="appointment.html" class="side-link">
                        <i class="fa-regular fa-calendar"></i>
                        Appointment
                    </a>

                    <a href="doctorProfile.html" class="side-link">
                        <i class="fa-solid fa-user-doctor"></i>
                        Doctor
                    </a>

                    <a href="{{ route('patients.create') }}" class="side-link">
                        <i class="fa-solid fa-users"></i>
                        Patient
                    </a>

                    <a href="#" class="side-link">
                        <i class="fa-regular fa-file-lines"></i>
                        Report
                    </a>

                    <a href="#" class="side-link">
                        <i class="fa-solid fa-gear"></i>
                        Setting
                    </a>

                    <a href="feedback.html" class="side-link">
                        <i class="fa-regular fa-comment-dots"></i>
                        Feedback
                    </a>

                </nav>
            </div>
        </div>
        @yield('dash-content')
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your JS -->
    <script src="{{ asset('JS/dashBord.js') }}"></script>
</body>

</html>