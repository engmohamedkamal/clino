@extends('layouts.dash')

@section('dash-content')

<div class="container-fluid">

  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">

      <div class="card border-0 shadow-lg">
        <div class="card-body p-4 p-md-5">

          <div class="mb-4 text-center">
            <i class="fa-solid fa-prescription-bottle-medical fa-2x text-primary mb-2"></i>
            <h4 class="fw-bold mb-1">Create Prescription</h4>
            <p class="text-muted mb-0">Fill in the prescription details carefully</p>
          </div>

          {{-- Errors --}}
          @if ($errors->any())
            <div class="alert alert-danger">
              <h6 class="alert-heading mb-2">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                Please fix the following errors:
              </h6>

              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('prescriptions.store') }}" novalidate>
            @csrf

            <div class="row g-3">

              {{-- ================= Patient (Users role=patient) ================= --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Patient</label>

                <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                  <option value="">-- Select Patient --</option>
                  @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }}
                    </option>
                  @endforeach
                </select>

                @error('patient_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Doctor (Admin only) ================= --}}
              @if(auth()->user()->role === 'admin')
                <div class="col-12 col-md-6">
                  <label class="form-label fw-semibold">Doctor</label>

                  <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                    <option value="">-- Select Doctor --</option>
                    @foreach($doctors as $doctor)
                      <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->user->name ?? ('Doctor #' . $doctor->id) }}
                      </option>
                    @endforeach
                  </select>

                  @error('doctor_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              @endif

              {{-- ================= Medicine Name ================= --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Medicine Name</label>
                <input type="text"
                       name="medicine_name"
                       value="{{ old('medicine_name') }}"
                       class="form-control @error('medicine_name') is-invalid @enderror"
                       placeholder="e.g. Amoxicillin"
                       required>
                @error('medicine_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Dosage ================= --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Dosage</label>
                <input type="text"
                       name="dosage"
                       value="{{ old('dosage') }}"
                       class="form-control @error('dosage') is-invalid @enderror"
                       placeholder="e.g. 1 pill twice daily"
                       required>
                @error('dosage')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Duration ================= --}}
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Duration</label>
                <input type="text"
                       name="duration"
                       value="{{ old('duration') }}"
                       class="form-control @error('duration') is-invalid @enderror"
                       placeholder="e.g. 7 days"
                       required>
                @error('duration')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Diagnosis ================= --}}
              <div class="col-12 col-md-12">
                <label class="form-label fw-semibold">Diagnosis</label>
                <input type="text"
                       name="diagnosis"
                       value="{{ old('diagnosis') }}"
                       class="form-control @error('diagnosis') is-invalid @enderror"
                       placeholder="e.g. Acute pharyngitis"
                       required>
                @error('diagnosis')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Notes ================= --}}
              <div class="col-12">
                <label class="form-label fw-semibold">Notes (Optional)</label>
                <textarea name="notes"
                          rows="3"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Additional instructions...">{{ old('notes') }}</textarea>
                @error('notes')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Actions ================= --}}
              <div class="col-12 d-flex justify-content-between pt-2">
                <a href="{{ route('prescriptions.index') }}" class="btn btn-light">
                  <i class="fa-solid fa-arrow-left me-1"></i>
                  Back
                </a>

                <button type="submit" class="btn btn-primary px-4">
                  <i class="fa-solid fa-check me-1"></i>
                  Save Prescription
                </button>
              </div>

            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

</div>

@endsection
