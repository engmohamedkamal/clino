@extends('layouts.dash')

@section('dash-content')
<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
        aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div>
        <h3 class="appointment-title mt-2 mb-0">Edit Your Info</h3>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('patient-info.my') }}" class="dp-btn">View</a>
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

        {{-- Info Message --}}
        @if (session('info'))
          <div class="alert alert-info mb-3">
            {{ session('info') }}
          </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- ✅ هنا لازم id (resource update محتاج parameter) --}}
        <form method="POST" action="{{ route('patient-info.update', $info->id) }}">
          @csrf
          @method('PUT')

          <div class="row g-3">

            {{-- ================== BASIC INFO ================== --}}
            <div class="col-12">
              <h6 class="fw-bold mb-2">Basic Information</h6>
            </div>

         

            <div class="col-md-6">
              <label class="form-label appointment-label" for="phone">Phone</label>
              <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone', $info->phone) }}"
                class="form-control appointment-control @error('phone') is-invalid @enderror"
                placeholder="Enter Phone Number">
              @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label appointment-label" for="gender">Gender</label>
              @php($genderVal = old('gender', $info->gender))
              <select
                id="gender"
                name="gender"
                class="form-select appointment-control @error('gender') is-invalid @enderror">
                <option value="" disabled {{ $genderVal ? '' : 'selected' }}>Select</option>
                <option value="male"   @selected($genderVal === 'male')>Male</option>
                <option value="female" @selected($genderVal === 'female')>Female</option>
                <option value="Other"  @selected($genderVal === 'Other')>Other</option>
              </select>
              @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label appointment-label" for="dob">Date of Birth</label>
              <input
                type="date"
                id="dob"
                name="dob"
                value="{{ old('dob', optional($info->dob)->format('Y-m-d')) }}"
                class="form-control appointment-control @error('dob') is-invalid @enderror">
              @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-6">
              <label class="form-label appointment-label" for="address">Address</label>
              <input
                type="text"
                id="address"
                name="address"
                value="{{ old('address', $info->address) }}"
                class="form-control appointment-control @error('address') is-invalid @enderror"
                placeholder="Enter Address">
              @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ================== HEALTH INFO ================== --}}
            <div class="col-12 mt-2">
              <h6 class="fw-bold mb-2">Health Information</h6>
            </div>

            <div class="col-md-4">
              <label class="form-label appointment-label" for="blood_type">Blood Type</label>
              @php($bloodVal = old('blood_type', $info->blood_type))
              <select
                id="blood_type"
                name="blood_type"
                class="form-select appointment-control @error('blood_type') is-invalid @enderror">
                <option value="" {{ $bloodVal ? '' : 'selected' }}>Select</option>
                <option value="A+"  @selected($bloodVal === 'A+')>A+</option>
                <option value="A-"  @selected($bloodVal === 'A-')>A-</option>
                <option value="B+"  @selected($bloodVal === 'B+')>B+</option>
                <option value="B-"  @selected($bloodVal === 'B-')>B-</option>
                <option value="AB+" @selected($bloodVal === 'AB+')>AB+</option>
                <option value="AB-" @selected($bloodVal === 'AB-')>AB-</option>
                <option value="O+"  @selected($bloodVal === 'O+')>O+</option>
                <option value="O-"  @selected($bloodVal === 'O-')>O-</option>
              </select>
              @error('blood_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label appointment-label" for="weight">Weight (kg)</label>
              <input
                type="number"
                step="0.1"
                id="weight"
                name="weight"
                value="{{ old('weight', $info->weight) }}"
                class="form-control appointment-control @error('weight') is-invalid @enderror"
                placeholder="e.g. 72.5">
              @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label appointment-label" for="height">Height (cm)</label>
              <input
                type="number"
                step="0.1"
                id="height"
                name="height"
                value="{{ old('height', $info->height) }}"
                class="form-control appointment-control @error('height') is-invalid @enderror"
                placeholder="e.g. 175">
              @error('height') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ================== EMERGENCY CONTACT ================== --}}
            <div class="col-12 mt-2">
              <h6 class="fw-bold mb-2">Emergency Contact</h6>
            </div>

            <div class="col-md-6">
              <label class="form-label appointment-label" for="emergency_contact_name">Contact Name</label>
              <input
                type="text"
                id="emergency_contact_name"
                name="emergency_contact_name"
                value="{{ old('emergency_contact_name', $info->emergency_contact_name) }}"
                class="form-control appointment-control @error('emergency_contact_name') is-invalid @enderror"
                placeholder="Enter contact name">
              @error('emergency_contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label appointment-label" for="emergency_contact_phone">Contact Phone</label>
              <input
                type="text"
                id="emergency_contact_phone"
                name="emergency_contact_phone"
                value="{{ old('emergency_contact_phone', $info->emergency_contact_phone) }}"
                class="form-control appointment-control @error('emergency_contact_phone') is-invalid @enderror"
                placeholder="Enter contact phone">
              @error('emergency_contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ================== MEDICAL DETAILS ================== --}}
            <div class="col-12 mt-2">
              <h6 class="fw-bold mb-2">Medical Details</h6>
            </div>

            <div class="col-3">
              <label class="form-label appointment-label" for="medical_history">Medical History</label>
              <textarea
                id="medical_history"
                name="medical_history"
                rows="3"
                class="form-control appointment-control appointment-textarea @error('medical_history') is-invalid @enderror"
                placeholder="Previous medical history...">{{ old('medical_history', $info->medical_history) }}</textarea>
              @error('medical_history') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-3">
              <label class="form-label appointment-label" for="allergies">Allergies</label>
              <textarea
                id="allergies"
                name="allergies"
                rows="3"
                class="form-control appointment-control appointment-textarea @error('allergies') is-invalid @enderror"
                placeholder="Allergies...">{{ old('allergies', $info->allergies) }}</textarea>
              @error('allergies') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-3">
              <label class="form-label appointment-label" for="current_medications">Current Medications</label>
              <textarea
                id="current_medications"
                name="current_medications"
                rows="3"
                class="form-control appointment-control appointment-textarea @error('current_medications') is-invalid @enderror"
                placeholder="Current medications...">{{ old('current_medications', $info->current_medications) }}</textarea>
              @error('current_medications') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-3">
              <label class="form-label appointment-label" for="notes">Notes</label>
              <textarea
                id="notes"
                name="notes"
                rows="4"
                class="form-control appointment-control appointment-textarea @error('notes') is-invalid @enderror"
                placeholder="Additional notes...">{{ old('notes', $info->notes) }}</textarea>
              @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

          </div>

          <!-- Save button -->
          <div class="mt-3 mt-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-save-full">
              Update
            </button>

            {{-- ✅ Cancel يرجع لصفحة my --}}
            <a href="{{ route('patient-info.my') }}" class="btn btn-outline-secondary btn-save-full">
              Cancel
            </a>
          </div>

        </form>

      </div>
    </div>
  </section>

</main>
@endsection
