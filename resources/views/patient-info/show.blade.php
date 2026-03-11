@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('css/patientInfo.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $info = $info ?? (auth()->user()->patientInfo ?? null);
  @endphp

  <main class="main">

    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>

        <div>
          <h3 class="appointment-title mt-2 mb-0">My Info</h3>
          <p class="mb-0 small text-muted">Your saved personal & medical information</p>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        @if($info && auth()->user()->role == 'patient')
        <a href="{{ route('patient-info.edit', $info->id) }}" class="dp-btn">
  Edit
</a>

        @elseif(auth()->user()->role == 'patient')
          <a href="{{ route('patient-info.create') }}" class="dp-btn">Create</a>
        @endif
      </div>
    </header>

    <!-- Content -->
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card w-100" >

          {{-- Alerts --}}
          @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
          @endif

          @if(session('info'))
            <div class="alert alert-info mb-3">{{ session('info') }}</div>
          @endif

          @if(!$info)
            <div class="alert alert-warning">
              No information found.
              <a href="{{ route('patient-info.create') }}" class="ms-2">Create now</a>
            </div>
          @else

            @php
              $name = $info->user->name ?? '-';

              // initials
              $parts = preg_split('/\s+/', trim($name));
              $initials = '';
              if (!empty($parts)) {
                $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
              }
              $initials = $initials ?: 'U';

              // age
              $age = $info->dob ? \Carbon\Carbon::parse($info->dob)->age : null;

              // greeting
              $hour = now()->format('H');
              $greeting = $hour < 12 ? 'Good Morning,' : ($hour < 18 ? 'Good Afternoon,' : 'Good Evening,');

              // registered
              $registeredDate = $info->created_at
                ? \Carbon\Carbon::parse($info->created_at)->format('d M, Y')
                : '-';

              $gender = $info->gender ?? null;
              $blood = $info->blood_type ?? null;
            @endphp

            <div class="dp-main dp-skeleton-on" id="dpWrap">

              <!-- ================= PROFILE CARD ================= -->
              <div class="dp-profile-card">
                <!-- Left -->
                <div class="dp-profile-left">
                  <div class="dp-greeting dp-skel">{{ $greeting }}</div>

                  <div class="dp-name-row">
                    <div class="dp-avatar dp-skel" aria-hidden="true">{{ $initials }}</div>

                    <div class="dp-name-wrap">
                      <div class="dp-name dp-skel" style="color: yellow;">{{ $name }}</div>

                      <div class="dp-badges">
                        <span class="dp-badge dp-badge-blue dp-skel">
                          <span class="material-icons-round">person</span>
                          {{ $gender ? ucfirst($gender) : 'Gender -' }}
                        </span>

                        <span class="dp-badge dp-badge-red dp-skel">
                          <span class="material-icons-round">favorite</span>
                          {{ $blood ? 'Blood ' . $blood : 'Blood -' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="dp-contact">
                    <div class="dp-contact-item dp-skel">
                      <span class="material-icons-round">call</span>
                      <span>{{ $info->phone ?? '-' }}</span>
                    </div>

                    <div class="dp-contact-item dp-skel">
                      <span class="material-icons-round">location_on</span>
                      <span>{{ $info->address ?? '-' }}</span>
                    </div>
                  </div>
                </div>

                <div class="dp-profile-divider"></div>

                <!-- Right -->
                <div class="dp-profile-right">
                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Gender</div>
                    <div class="dp-stat-value">{{ $info->gender ?? '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Age</div>
                    <div class="dp-stat-value">{{ $age ?? '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Registered</div>
                    <div class="dp-stat-value">{{ $registeredDate }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Blood</div>
                    <div class="dp-stat-value">{{ $blood ?? '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Weight</div>
                    <div class="dp-stat-value">{{ $info->weight ? $info->weight . ' kg' : '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Height</div>
                    <div class="dp-stat-value">{{ $info->height ? $info->height . ' cm' : '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Emergency Name</div>
                    <div class="dp-stat-value">{{ $info->emergency_contact_name ?? '-' }}</div>
                  </div>

                  <div class="dp-stat dp-skel">
                    <div class="dp-stat-title">Emergency Phone</div>
                    <div class="dp-stat-value">{{ $info->emergency_contact_phone ?? '-' }}</div>
                  </div>
                </div>
              </div>

              <!-- ================= MEDICAL DETAILS ================= -->
              <div class="dp-card-lg">
                <div class="dp-card-head">
                  <h6 class="dp-card-title">Medical Details</h6>
                  <span class="dp-card-sub">History & notes</span>
                </div>

                <div class="dp-card-body">
                  <div class="row g-3">
                    <div class="col-12 col-sm-6 col-xl-3">
                      <div class="dp-text-item">
                        <div class="dp-info-label">Medical History</div>
                        <div class="dp-text-box">{{ $info->medical_history ?? '-' }}</div>
                      </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                      <div class="dp-text-item">
                        <div class="dp-info-label">Allergies</div>
                        <div class="dp-text-box">{{ $info->allergies ?? '-' }}</div>
                      </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                      <div class="dp-text-item">
                        <div class="dp-info-label">Current Medications</div>
                        <div class="dp-text-box">{{ $info->current_medications ?? '-' }}</div>
                      </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                      <div class="dp-text-item">
                        <div class="dp-info-label">Notes</div>
                        <div class="dp-text-box">{{ $info->notes ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


            
            </div>

            <script>
              // Skeleton shimmer then reveal content quickly
              document.addEventListener('DOMContentLoaded', () => {
                const wrap = document.getElementById('dpWrap');
                if (!wrap) return;
                setTimeout(() => wrap.classList.remove('dp-skeleton-on'), 450);
              });
            </script>

          @endif

        </div>
      </div>
    </section>
  </main>
@endsection