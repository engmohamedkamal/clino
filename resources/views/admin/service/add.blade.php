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
        <h3 class="appointment-title mt-2 mb-0">Add New Service</h3>
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

        <form method="POST"
              action="{{ route('service.store') }}"
              enctype="multipart/form-data">
          @csrf

          <div class="row g-3">

            <!-- Service Name -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label">Service Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control appointment-control @error('name') is-invalid @enderror"
                       placeholder="Enter service name">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Description -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label">Description</label>
                <textarea name="description"
                          rows="5"
                          class="form-control appointment-control appointment-textarea @error('description') is-invalid @enderror"
                          placeholder="Enter service description">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Image -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label">Service Image</label>
                <input type="file"
                       name="image"
                       class="form-control appointment-control @error('image') is-invalid @enderror">
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Status -->
            <div class="col-12">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       id="status"
                       name="status"
                       value="1"
                       {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label appointment-label" for="status">
                  Active Service
                </label>
              </div>
            </div>

          </div>

          <!-- Save button -->
          <div class="mt-3 mt-md-4">
            <button type="submit" class="btn btn-primary btn-save-full">
              Save Service
            </button>
          </div>

        </form>

      </div>
    </div>
  </section>

</main>
@endsection
