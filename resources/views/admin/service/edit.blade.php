@extends('layouts.dash')

@section('dash-content')
<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button"
              data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div>
        <h3 class="appointment-title mt-2 mb-0">Edit Service</h3>
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

        {{-- Errors --}}
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST"
              action="{{ route('service.update', $service->id) }}"
              enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row g-3">

            <!-- Service Name -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label">Service Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $service->name) }}"
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
                          placeholder="Enter service description">{{ old('description', $service->description) }}</textarea>
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

                {{-- Current Image --}}
                @if($service->image)
                  <div class="mt-2">
                    <small class="text-muted">Current Image:</small><br>
                    <img src="{{ asset('storage/'.$service->image) }}"
                         alt="service"
                         style="height:80px;border-radius:8px">
                  </div>
                @endif
              </div>
            </div>

            <!-- Status -->
            <div class="col-12">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input"
                       type="checkbox"
                       id="status"
                       name="status"
                       value="1"
                       {{ old('status', $service->status) ? 'checked' : '' }}>
                <label class="form-check-label appointment-label" for="status">
                  Active Service
                </label>
              </div>
            </div>

          </div>

          <!-- Save -->
          <div class="mt-3 mt-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-save-full">
              Update Service
            </button>

            <a href="{{ route('service.index') }}" class="btn btn-primary btn-save-full">
              Cancel
            </a>
          </div>

        </form>

      </div>
    </div>
  </section>

</main>
@endsection
