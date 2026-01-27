@extends('layouts.dash')
@section('dash-content')

  <link rel="stylesheet" href="{{ asset('CSS/Setting.css') }}" />

  @php
    $isEdit = !empty($settings);
    $action = $isEdit ? route('settings.update', $settings->id) : route('settings.store');
  @endphp

  <main class="main">

    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
          aria-controls="mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h3 class="appointment-title mt-2">Setting</h3>
      </div>
    </header>

    {{-- Alerts --}}
    <div class="mt-3">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          {{ session('info') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif


    </div>

    <section class="st-panel">
      <div class="st-card">

        <form class="st-form" action="{{ $action }}" method="POST" enctype="multipart/form-data">
          @csrf
          @if($isEdit) @method('PUT') @endif

          <div class="row g-4">

            <!-- Name / Slogan -->
            <div class="col-12 col-md-6">
              <label class="st-label">Name</label>
              <input type="text" name="name" class="form-control st-input @error('name') is-invalid @enderror"
                placeholder="Enter Name" value="{{ old('name', $settings->name ?? '') }}" />
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
              <label class="st-label">Slogan</label>
              <input type="text" name="slogan" class="form-control st-input @error('slogan') is-invalid @enderror"
                placeholder="Enter Slogan" value="{{ old('slogan', $settings->slogan ?? '') }}" />
              @error('slogan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Vision -->
            <div class="col-12">
              <label class="st-label">Vision</label>
              <textarea name="vision" class="form-control st-input st-textarea @error('vision') is-invalid @enderror"
                rows="3" placeholder="Enter Vision">{{ old('vision', $settings->vision ?? '') }}</textarea>
              @error('vision') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Mission -->
            <div class="col-12">
              <label class="st-label">Mission</label>
              <textarea name="mission" class="form-control st-input st-textarea @error('mission') is-invalid @enderror"
                rows="3" placeholder="Enter mission">{{ old('mission', $settings->mission ?? '') }}</textarea>
              @error('mission') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Socials -->
            <div class="col-12 col-lg-4">
              <label class="st-label">Facebook URL</label>
              <input type="url" name="facebook" class="form-control st-input @error('facebook') is-invalid @enderror"
                placeholder="Facebook" value="{{ old('facebook', $settings->facebook ?? '') }}" />
              @error('facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-lg-4">
              <label class="st-label">Instagram URL</label>
              <input type="url" name="instagram" class="form-control st-input @error('instagram') is-invalid @enderror"
                placeholder="instagram" value="{{ old('instagram', $settings->instagram ?? '') }}" />
              @error('instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-lg-4">
              <label class="st-label">Twitter URL</label>
              <input type="url" name="twitter" class="form-control st-input @error('twitter') is-invalid @enderror"
                placeholder="twitter" value="{{ old('twitter', $settings->twitter ?? '') }}" />
              @error('twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Phone / Email / Address -->
            <div class="col-12 col-lg-4">
              <label class="st-label">Phone</label>
              <input type="text" name="phone" class="form-control st-input @error('phone') is-invalid @enderror"
                placeholder="phone" value="{{ old('phone', $settings->phone ?? '') }}" />
              @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-lg-4">
              <label class="st-label">Email</label>
              <input type="email" name="email" class="form-control st-input @error('email') is-invalid @enderror"
                placeholder="email" value="{{ old('email', $settings->email ?? '') }}" />
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-lg-4">
              <label class="st-label">Address</label>
              <input type="text" name="address" class="form-control st-input @error('address') is-invalid @enderror"
                placeholder="address" value="{{ old('address', $settings->address ?? '') }}" />
              @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
<div class="row g-3 align-items-start">

  <!-- Map URL -->
  <div class="col-12 col-lg-4">
    <label class="st-label">Map URL</label>
    <input
      type="url"
      name="map_url"
      class="form-control st-input @error('map_url') is-invalid @enderror"
      placeholder="Google Maps link"
      value="{{ old('map_url', $settings->map_url ?? '') }}"
    />
    <small class="text-muted">
  Paste Google Maps <strong>Embed link</strong> not Share link
</small>

    @error('map_url')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror

  </div>

  <!-- Logo -->
  <div class="col-12 col-lg-8">
    <label class="st-label mb-2">Logo</label>

    <div class="st-file-wrapper">
      <input
        type="file"
        name="logo"
        id="logoInput"
        accept="image/*"
        class="st-file-input @error('logo') is-invalid @enderror"
      />

      <label for="logoInput" class="st-file-label">
        <i class="bi bi-upload"></i>
        <span id="logoFileName">Choose logo</span>
      </label>
    </div>

    @error('logo')
      <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if(!empty($settings->logo))
      <div class="mt-3 d-flex align-items-center gap-3">
        <img src="{{ asset('storage/' . $settings->logo) }}"
             alt="logo"
             class="st-logo-preview">
        <small class="text-muted">Current Logo</small>
      </div>
    @endif
  </div>

</div>



            <!-- Save -->
            <div class="col-12">
              <button type="submit" class="btn st-save-btn text-light w-100">
                {{ $isEdit ? 'Update' : 'Save' }}
              </button>
            </div>

          </div>
        </form>

      </div>
    </section>

  </main>
  <script>
    document.getElementById('logoInput')?.addEventListener('change', function () {
      const name = this.files[0]?.name || 'Choose logo';
      document.getElementById('logoFileName').textContent = name;
    });
  </script>

@endsection