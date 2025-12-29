@extends('layouts.dash')

@section('dash-content')
<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div>
        <h3 class="appointment-title mt-2 mb-0">Edit Patient</h3>
        {{-- <p class="mb-0 small text-muted">Update patient details then click save</p> --}}
      </div>
    </div>

   
  </header>

  <!-- Content -->
  <section class="content-area">
    <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
      <div class="appointment-card">

        {{-- Success --}}
        @if (session('success'))
          <div class="alert alert-success mb-3">
            {{ session('success') }}
          </div>
        @endif

      

        <form method="POST" action="{{ route('patients.update', $patient) }}">
          @csrf
          @method('PUT')

          <div class="row g-3">

            <!-- LEFT COLUMN -->
            <div class="col-md-6">

              <!-- Patient Name -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="patientName">Patient Name</label>
                <input
                  type="text"
                  id="patientName"
                  name="patient_name"
                  value="{{ old('patient_name', $patient->patient_name) }}"
                  class="form-control appointment-control @error('patient_name') is-invalid @enderror"
                  placeholder="Enter Name"
                >
                @error('patient_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Patient Number -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="patientNumber">Patient Number</label>
                <input
                  type="text"
                  id="patientNumber"
                  name="patient_number"
                  value="{{ old('patient_number', $patient->patient_number) }}"
                  class="form-control appointment-control @error('patient_number') is-invalid @enderror"
                  placeholder="Enter Phone Number"
                >
                @error('patient_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Date of birth -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="dob">Date of birth</label>
                <input
                  type="date"
                  id="dob"
                  name="dob"
                  value="{{ old('dob', optional($patient->dob)->format('Y-m-d')) }}"
                  class="form-control appointment-control @error('dob') is-invalid @enderror"
                >
                @error('dob')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-md-6">

              <!-- Patient Email -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="patientEmail">Patient Email</label>
                <input
                  type="email"
                  id="patientEmail"
                  name="patient_email"
                  value="{{ old('patient_email', $patient->patient_email) }}"
                  class="form-control appointment-control @error('patient_email') is-invalid @enderror"
                  placeholder="Enter email address"
                >
                @error('patient_email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Gender -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="gender">Gender</label>
                <select
                  id="gender"
                  name="gender"
                  class="form-select appointment-control @error('gender') is-invalid @enderror"
                >
                  <option value="" disabled {{ old('gender', $patient->gender) ? '' : 'selected' }}>Select</option>
                  <option value="Male"   @selected(old('gender', $patient->gender) === 'Male')>Male</option>
                  <option value="Female" @selected(old('gender', $patient->gender) === 'Female')>Female</option>
                  <option value="Other"  @selected(old('gender', $patient->gender) === 'Other')>Other</option>
                </select>
                @error('gender')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- ID Number -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="idNumber">ID Number</label>
                <input
                  type="text"
                  id="idNumber"
                  name="id_number"
                  value="{{ old('id_number', $patient->id_number) }}"
                  class="form-control appointment-control @error('id_number') is-invalid @enderror"
                  placeholder="Enter id number"
                >
                @error('id_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

            </div>

            <!-- Address (full width) -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label" for="address">Address</label>
                <input
                  type="text"
                  id="address"
                  name="address"
                  value="{{ old('address', $patient->address) }}"
                  class="form-control appointment-control @error('address') is-invalid @enderror"
                  placeholder="Enter Address"
                >
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- About (full width) -->
            <div class="col-12">
              <div class="mb-2 mb-md-3">
                <label class="form-label appointment-label" for="about">About</label>
                <textarea
                  id="about"
                  name="about"
                  rows="5"
                  class="form-control appointment-control appointment-textarea @error('about') is-invalid @enderror"
                  placeholder="Describe your About"
                >{{ old('about', $patient->about) }}</textarea>
                @error('about')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

          </div>

          <!-- Save button -->
          <div class="mt-3 mt-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-save-full">
              Update
            </button>

            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-save-full">
              Cancel
            </a>
          </div>
        </form>

      </div>
    </div>
  </section>

</main>
@endsection
