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
          <h3 class="appointment-title mt-2 mb-0">Edit Medical Order</h3>
          {{-- <p class="mb-0 small text-muted">Update medical order then click save</p> --}}
        </div>
      </div>
    </header>

    <!-- Content -->
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card" style="max-width:520px">

          {{-- Success --}}
          @if (session('success'))
            <div class="alert alert-success mb-3">
              {{ session('success') }}
            </div>
          @endif

          <form method="POST" action="{{ route('medical-orders.update', $medicalOrder) }}">
            @csrf
            @method('PUT')

            <!-- Medical Order Name -->
            <div class="mb-3">
              <label class="form-label appointment-label" for="orderName">Medical Order Name</label>
              <input
                type="text"
                id="orderName"
                name="name"
                value="{{ old('name', $medicalOrder->name) }}"
                class="form-control appointment-control @error('name') is-invalid @enderror"
                placeholder="e.g. Paracetamol / CBC / X-Ray Chest"
              >
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Order Type -->
            <div class="mb-4">
              <label class="form-label appointment-label" for="orderType">Order Type</label>
              <select
                id="orderType"
                name="type"
                class="form-select appointment-control @error('type') is-invalid @enderror"
              >
                <option value="" disabled {{ old('type', $medicalOrder->type) ? '' : 'selected' }}>
                  Select
                </option>

                <option value="medicine" @selected(old('type', $medicalOrder->type) === 'medicine')>
                  Medicine
                </option>

                <option value="rumor" @selected(old('type', $medicalOrder->type) === 'rumor')>
                  Radiology
                </option>

                <option value="analysis" @selected(old('type', $medicalOrder->type) === 'analysis')>
                  Analysis
                </option>
              </select>

              @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Actions -->
            <div class="mt-3 mt-md-4 d-flex gap-2">
              <button type="submit" class="btn btn-primary btn-save-full">
                Update
              </button>

              <a href="{{ route('medical-orders.index') }}" class="btn btn-outline-secondary btn-save-full">
                Cancel
              </a>
            </div>

          </form>

        </div>
      </div>
    </section>

  </main>
@endsection
