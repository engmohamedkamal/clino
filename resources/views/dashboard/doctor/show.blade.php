@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/doctorProfile.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

@php
  $info = $info ?? (auth()->user()->doctorInfo ?? null);
  $user = auth()->user();

  // greeting
  $hour = now()->format('H');
  $greeting = $hour < 12 ? 'Good Morning,' : ($hour < 18 ? 'Good Afternoon,' : 'Good Evening,');

  // Image
  $img = ($info && $info->image) ? asset('storage/'.$info->image) : asset('Images/doctor-placeholder.png');

  // Name
  $doctorName = 'Dr. ' . ($user->name ?? 'Doctor');

  // ✅ Multi arrays (JSON)
  $specializations = $info?->Specialization ?? [];
  $availability = $info?->availability_schedule ?? [];
  $activities = $info?->activities ?? [];
  $skills = $info?->skills ?? [];

  // Hero subtitle: first specialization if exists
  $specTitle = (is_array($specializations) && count($specializations)) ? $specializations[0] : null;

  // About
  $about = $info?->about ?? 'No description yet.';

  // Contact
  $phone = $info?->phone ?? ($user->phone ?? '-');
  $address = $info?->address ?? '-';

  // Age
  $age = ($info && $info->dob) ? \Carbon\Carbon::parse($info->dob)->age : null;

  // rating placeholder
  $rating = 4;
@endphp

<main class="dp-main">

  <!-- Topbar -->
  <header class="dp-topbar">
    <h1 class="dp-title">Doctor Profile</h1>

    <div class="dp-top-actions">
    
      {{-- لو عندك route لقائمة الأطباء حطه هنا --}}
      <a class="dp-btn" href="#">Doctor List</a>
      <a class="dp-btn" href="{{ route('doctor-info.edit', $info->id) }}">Edit</a>
    </div>
  </header>

  <div class="dp-content">
    <div class="dp-container">

      @if(!$info)
        <div class="alert alert-warning">
          No doctor info found for your account.
          <a href="{{ route('doctor-info.create') }}" class="ms-2">Create now</a>
        </div>
      @else

        <!-- Hero -->
        <section class="dp-hero">
          <div class="dp-hero-inner">
            <div class="dp-hero-photo">
              <img src="{{ $img }}" alt="Doctor" />
            </div>

            <div class="dp-hero-text">
              <div class="dp-hero-small">{{ $greeting }}</div>
              <div class="dp-hero-name">{{ $doctorName }}</div>

              <div class="dp-hero-sub">
                {{ $specTitle ? $specTitle : 'Specialization not set' }}
              </div>

              <div class="dp-stars">
                @for($i=1; $i<=5; $i++)
                  <span class="material-icons-round {{ $i <= $rating ? 'on' : 'off' }}">star</span>
                @endfor
              </div>

              <div class="dp-hero-rev">
                {{ $info->license_number ? 'License: '.$info->license_number : 'Profile Completed' }}
              </div>
            </div>
          </div>
        </section>

        <!-- Stats -->
        <section class="row g-4 dp-section">

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="dp-card dp-stat">
              <div class="dp-stat-icon icon-blue">
                <span class="material-icons-round">badge</span>
              </div>
              <div class="dp-stat-label">License</div>
              <div class="dp-stat-bottom">
                <div class="dp-stat-value">{{ $info->license_number ?? '-' }}</div>
                <div class="dp-stat-delta">
                  <span class="material-icons-round up">verified</span> Verified
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="dp-card dp-stat">
              <div class="dp-stat-icon icon-red">
                <span class="material-icons-round">person</span>
              </div>
              <div class="dp-stat-label">Gender</div>
              <div class="dp-stat-bottom">
                <div class="dp-stat-value">{{ $info->gender ? ucfirst($info->gender) : '-' }}</div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="dp-card dp-stat">
              <div class="dp-stat-icon icon-green">
                <span class="material-icons-round">location_on</span>
              </div>
              <div class="dp-stat-label">Address</div>
              <div class="dp-stat-bottom">
                <div class="dp-stat-value">{{ $address }}</div>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-3">
            <div class="dp-card dp-stat">
              <div class="dp-stat-icon icon-yellow">
                <span class="material-icons-round">calendar_today</span>
              </div>

              <div class="dp-stat-label">Age</div>

              <div class="dp-stat-bottom">
                @if($age !== null)
                  <span class="dp-age-badge">{{ $age }} Years</span>
                @else
                  <span class="dp-age-badge dp-age-empty">-</span>
                @endif
              </div>
            </div>
          </div>

        </section>

        <!-- About + Availability -->
        <section class="row g-4 dp-section">

          <div class="col-12 col-lg-8">
            <div class="dp-card dp-about">
              <div class="dp-about-grid">

                <div>
                  <h2 class="dp-h2">About me</h2>
                  <p class="dp-para">{{ $about }}</p>
                </div>

                <div class="dp-about-mid">
                  <div class="dp-meta">
                    <div class="dp-meta-title">Mobile</div>
                    <div class="dp-meta-row">
                      <span class="material-icons-round dp-meta-ic">call</span>
                      {{ $phone }}
                    </div>
                  </div>

                  <div class="dp-meta">
                    <div class="dp-meta-title">Location</div>
                    <div class="dp-meta-row">
                      <span class="material-icons-round dp-meta-ic">location_on</span>
                      {{ $address }}
                    </div>
                  </div>

                  {{-- Social links --}}
                  <div class="mt-3 d-flex gap-2 flex-wrap">
                    @if($info->facebook)
                      <a class="btn btn-sm btn-light" href="{{ $info->facebook }}" target="_blank">Facebook</a>
                    @endif
                    @if($info->instagram)
                      <a class="btn btn-sm btn-light" href="{{ $info->instagram }}" target="_blank">Instagram</a>
                    @endif
                    @if($info->twitter)
                      <a class="btn btn-sm btn-light" href="{{ $info->twitter }}" target="_blank">Twitter</a>
                    @endif
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="dp-card dp-avail">
              <h2 class="dp-h2">Availability</h2>

              <div class="row g-3 dp-chips">
                @forelse($availability as $item)
                  <div class="col-6">
                    <div class="dp-chip">{{ $item }}</div>
                  </div>
                @empty
                  <div class="col-12">
                    <div class="dp-chip">No schedule added</div>
                  </div>
                @endforelse
              </div>
            </div>
          </div>

        </section>

        <!-- Bottom: Speciality / Activities / Skills -->
        <section class="row g-4 dp-section">

          <!-- Speciality -->
          <div class="col-12 col-lg-4">
            <div class="dp-card dp-box">
              <h2 class="dp-h2">Speciality</h2>

              <div class="dp-list">
                @forelse($specializations as $sp)
                  <div class="dp-li">
                    <div class="dp-badge b-blue">
                      <span class="material-icons-round">verified</span>
                    </div>
                    <div>
                      <div class="dp-li-title">Specialization</div>
                      <div class="dp-li-sub">{{ $sp }}</div>
                    </div>
                  </div>
                @empty
                  <div class="dp-li">
                    <div class="dp-badge b-blue">
                      <span class="material-icons-round">info</span>
                    </div>
                    <div>
                      <div class="dp-li-title">Not Set</div>
                      <div class="dp-li-sub">No specialization added</div>
                    </div>
                  </div>
                @endforelse
              </div>

            </div>
          </div>

          <!-- Activities -->
          <div class="col-12 col-lg-4">
            <div class="dp-card dp-box">
              <h2 class="dp-h2">Activities</h2>

              <div class="dp-acts">
                @forelse($activities as $a)
                  <div class="dp-act">
                    <span class="dp-dot"></span>
                    <div>
                      <div class="dp-li-title">Activity</div>
                      <div class="dp-li-sub">{{ $a }}</div>
                    </div>
                  </div>
                @empty
                  <div class="dp-act">
                    <span class="dp-dot"></span>
                    <div>
                      <div class="dp-li-title">Not Set</div>
                      <div class="dp-li-sub">No activities added</div>
                    </div>
                  </div>
                @endforelse
              </div>

            </div>
          </div>

          <!-- Skills -->
          <div class="col-12 col-lg-4">
            <div class="dp-card dp-box">
              <h2 class="dp-h2">Skills</h2>

              @php
                $fills = ['f-blue','f-orange','f-green','f-yellow'];
              @endphp

              @forelse($skills as $idx => $sk)
                @php
                  $fillClass = $fills[$idx % count($fills)];
                  $name = $sk['name'] ?? '-';
                  $w = max(0, min(100, (int)($sk['value'] ?? 0)));
                @endphp

                <div class="dp-skill">
                  <div class="dp-skill-top">
                    <span>{{ $name }}</span>
                    <span>{{ $w }}%</span>
                  </div>
                  <div class="dp-line">
                    <span class="dp-fill {{ $fillClass }}" style="width: {{ $w }}%"></span>
                  </div>
                </div>
              @empty
                <div class="dp-skill">
                  <div class="dp-skill-top">
                    <span>No skills added</span>
                    <span>0%</span>
                  </div>
                  <div class="dp-line">
                    <span class="dp-fill f-blue" style="width:0%"></span>
                  </div>
                </div>
              @endforelse

            </div>
          </div>

        </section>

      @endif
    </div>
  </div>

</main>
@endsection
