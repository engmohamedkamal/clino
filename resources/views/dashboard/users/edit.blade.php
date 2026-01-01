@extends('layouts.dash')
@section('dash-content')
  <main class="main">
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div>
          <h3 class="appointment-title mt-2 mb-0">Edit User</h3>
        </div>
      </div>
    </header>

    <!-- Content -->
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card">

          {{-- Success Message --}}
          @if (session('success'))
            <div class="alert alert-success mb-3">
              {{ session('success') }}
            </div>
          @endif

          <form method="POST" action="{{ route('users.update', $user->id) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">

              <!-- LEFT COLUMN -->
              <div class="col-md-6">

                <!-- Name -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Name</label>
                  <input type="text" name="name"
                    value="{{ old('name', $user->name) }}"
                    class="form-control appointment-control @error('name') is-invalid @enderror"
                    placeholder="Enter full name">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Phone -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Phone</label>
                  <input type="text" name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    class="form-control appointment-control @error('phone') is-invalid @enderror"
                    placeholder="Enter phone number">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- ID Number -->
                <div class="mb-3">
                  <label class="form-label appointment-label">ID Number</label>
                  <input type="text" name="id_number"
                    value="{{ old('id_number', $user->id_number) }}"
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
                <div class="mb-3">
                  <label class="form-label appointment-label">Role</label>
                  @php
                    $currentRole = old('role', $user->role);
                  @endphp
                  <select name="role" class="form-select appointment-control @error('role') is-invalid @enderror">
                    <option value="" disabled {{ $currentRole ? '' : 'selected' }}>Select role</option>
                    <option value="admin"   {{ $currentRole === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="doctor"  {{ $currentRole === 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="patient" {{ $currentRole === 'patient' ? 'selected' : '' }}>Patient</option>
                  </select>
                  @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Password (Optional) -->
                <div class="mb-3">
                  <label class="form-label appointment-label">New Password (optional)</label>
                  <input type="password" name="password"
                    class="form-control appointment-control @error('password') is-invalid @enderror"
                    placeholder="Leave empty to keep current password">
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="mb-3">
                  <label class="form-label appointment-label">Confirm New Password</label>
                  <input type="password" name="password_confirmation"
                    class="form-control appointment-control"
                    placeholder="Confirm new password">
                </div>

              </div>

            </div>

            <!-- Save button -->
            <div class="mt-3 mt-md-4">
              <button type="submit" class="btn btn-primary btn-save-full">
                Update User
              </button>
            </div>
          </form>

        </div>
      </div>
    </section>

  </main>
@endsection
