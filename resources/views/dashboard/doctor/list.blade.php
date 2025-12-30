@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/doctorProfile.css') }}">
<link rel="stylesheet" href="{{ asset('CSS/doctorList.css') }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

<main class="main">

  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="page-title">Doctors</div>
    </div>

    {{-- Search (GET) --}}
    <form class="search-wrap" method="GET" action="{{ route('doctor.list') }}">
      <input class="form-control search-input" type="text" name="q"
        value="{{ request('q') }}" placeholder="Search type of keywords" />
      <button type="submit" class="search-ico btn p-0 border-0 bg-transparent">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>

 
  </header>

  <div class="doctor-grid">
    @foreach ($doctors as $doctor)
      <div class="doctor-card">

        {{-- Avatar --}}
        <div class="doctor-avatar">
          <img
            src="{{ $doctor->image ? asset('storage/'.$doctor->image) : asset('images/default-doctor.png') }}"
            alt="Doctor {{ $doctor->user->name ?? '' }}">
        </div>

        {{-- Name --}}
        <h3 class="doctor-name">{{ $doctor->user->name ?? 'Doctor' }}</h3>

        {{-- Specialization --}}
        <p class="doctor-spec">
          {{ is_array($doctor->Specialization) ? implode(', ', $doctor->Specialization) : ($doctor->Specialization ?? '') }}
        </p>

        {{-- About --}}
        <p class="doctor-desc">
          {{ \Illuminate\Support\Str::limit($doctor->about ?? '', 100) }}
        </p>

        {{-- Social --}}
        <div class="doctor-social">
          @if($doctor->facebook)
            <a href="{{ $doctor->facebook }}" target="_blank" class="social-btn">
              <i class="fa-brands fa-facebook-f"></i>
            </a>
          @endif

          @if($doctor->twitter)
            <a href="{{ $doctor->twitter }}" target="_blank" class="social-btn">
              <i class="fa-brands fa-x-twitter"></i>
            </a>
          @endif

          @if($doctor->instagram)
            <a href="{{ $doctor->instagram }}" target="_blank" class="social-btn">
              <i class="fa-brands fa-instagram"></i>
            </a>
          @endif
        </div>

        {{-- View Profile --}}
       <a href="{{ route('doctor-info.show', $doctor->id) }}" class="doctor-view">
  View Profile
</a>


      </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  <div class="mt-4 d-flex justify-content-center">
    {{ $doctors->links() }}
  </div>

</main>
@endsection
