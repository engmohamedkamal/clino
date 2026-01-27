@extends('layouts.dash')
@section('dash-content')
  <main class="main">
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
          aria-controls="mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div>
          <h3 class="appointment-title mt-2 mb-0">Add New User</h3>
        </div>
      </div>
    </header>

    <!-- Content -->
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card">


          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" id="successAlert">
              {{ session('success') }}
            </div>
          @endif


          <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="row g-3">

              <!-- LEFT COLUMN -->
              <div class="col-md-6">

                <!-- Name -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Name</label>
                  <input type="text" name="name" value="{{ old('name') }}"
                    class="form-control appointment-control @error('name') is-invalid @enderror"
                    placeholder="Enter full name">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Phone -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Phone</label>
                  <input type="text" name="phone" value="{{ old('phone') }}"
                    class="form-control appointment-control @error('phone') is-invalid @enderror"
                    placeholder="Enter phone number">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- ID Number -->
                <div class="mb-3">
                  <label class="form-label appointment-label">ID Number</label>
                  <input type="text" name="id_number" value="{{ old('id_number') }}"
                    class="form-control appointment-control @error('id_number') is-invalid @enderror"
                    placeholder="Enter ID number">
                  @error('id_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

              </div>

              <!-- RIGHT COLUMN -->
              <div class="col-md-6">

                <!-- Role -->
           @if (auth()->user()->role === 'admin')

  {{-- Role --}}
  <div class="mb-3">
    <label class="form-label appointment-label">Role</label>
    <select
      name="role"
      id="roleSelect"
      class="form-select appointment-control @error('role') is-invalid @enderror"
    >
      <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role</option>
      <option value="admin" @selected(old('role') === 'admin')>Admin</option>
      <option value="doctor" @selected(old('role') === 'doctor')>Doctor</option>
      <option value="patient" @selected(old('role') === 'patient')>Patient</option>
      <option value="secretary" @selected(old('role') === 'secretary')>Secretary</option>
    </select>

    @error('role')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Doctor Select (hidden by default) --}}
  <div class="mb-3 d-none" id="doctorWrapper">
    <label class="form-label appointment-label">Assigned Doctor</label>
    <select
      name="doctor_id"
      class="form-select appointment-control @error('doctor_id') is-invalid @enderror"
    >
      <option value="" disabled selected>Select doctor</option>
      @foreach($doctors as $doctor)
        <option
          value="{{ $doctor->id }}"
          @selected(old('doctor_id') == $doctor->id)
        >
          {{ $doctor->name }}
        </option>
      @endforeach
    </select>

    @error('doctor_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

@endif


                <!-- Password -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Password</label>
                  <input type="password" name="password"
                    class="form-control appointment-control @error('password') is-invalid @enderror"
                    placeholder="Enter password">
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Confirm Password</label>
                  <input type="password" name="password_confirmation" class="form-control appointment-control"
                    placeholder="Confirm password">
                </div>

              </div>

            </div>

            <!-- Save button -->
            <div class="mt-3 mt-md-4">
              <button type="submit" class="btn btn-primary btn-save-full">
                Save User
              </button>
            </div>
          </form>

        </div>
      </div>
      <script>
        setTimeout(() => {
          const alert = document.getElementById('successAlert');
          if (alert) {
            alert.classList.add('fade');
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 500);
          }
        }, 3000); 
      
  document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('roleSelect');
    const doctorWrapper = document.getElementById('doctorWrapper');

    function toggleDoctorSelect() {
      if (roleSelect.value === 'secretary') {
        doctorWrapper.classList.remove('d-none');
      } else {
        doctorWrapper.classList.add('d-none');

        // optional: reset value
        const doctorSelect = doctorWrapper.querySelector('select');
        if (doctorSelect) doctorSelect.value = '';
      }
    }

    roleSelect.addEventListener('change', toggleDoctorSelect);

    // run on load (for validation errors / old values)
    toggleDoctorSelect();
  });
</script>


    </section>

  </main>
@endsection