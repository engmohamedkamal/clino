@extends('layouts.dash')

@section('dash-content')

<div class="container-fluid">

  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-9">

      <div class="card border-0 shadow-lg">
        <div class="card-body p-4 p-md-5">

          {{-- Header --}}
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

              {{-- ================= Patient ================= --}}
              <div class="col-md-6">
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
                <div class="col-md-6">
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

              {{-- ================= Medicines ================= --}}
              <div class="col-12">
                <label class="form-label fw-semibold mb-2">Medicines</label>

                <div id="medicinesWrapper">

                  {{-- Medicine Row --}}
                  <div class="row g-2 align-items-end medicine-row mb-2">

                    <div class="col-md-3">
                      <input type="text" name="medicine_name[]" class="form-control" placeholder="Medicine name" required>
                    </div>

                    <div class="col-md-3">
                      <input type="text" name="dosage[]" class="form-control" placeholder="Dosage" required>
                    </div>

                    <div class="col-md-3">
                      <input type="text" name="duration[]" class="form-control" placeholder="Duration" required>
                    </div>

                    <div class="col-md-2">
                      <input type="text" name="notes[]" class="form-control" placeholder="Notes">
                    </div>

                    <div class="col-md-1 text-center">
                      <button type="button" class="btn btn-outline-primary add-row">
                        <i class="fa-solid fa-plus"></i>
                      </button>
                    </div>

                  </div>
                </div>

                @error('medicine_name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Diagnosis ================= --}}
              <div class="col-12">
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

              {{-- ================= Actions ================= --}}
              <div class="col-12 d-flex justify-content-between pt-3">
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
<script>
document.addEventListener('DOMContentLoaded', function () {

  const wrapper = document.getElementById('medicinesWrapper');

  wrapper.addEventListener('click', function (e) {

    // Add Row
    if (e.target.closest('.add-row')) {
      const row = e.target.closest('.medicine-row');
      const clone = row.cloneNode(true);

      clone.querySelectorAll('input').forEach(input => input.value = '');

      const btn = clone.querySelector('button');
      btn.classList.remove('btn-outline-primary', 'add-row');
      btn.classList.add('btn-outline-danger', 'remove-row');
      btn.innerHTML = '<i class="fa-solid fa-trash"></i>';

      wrapper.appendChild(clone);
    }

    // Remove Row
    if (e.target.closest('.remove-row')) {
      e.target.closest('.medicine-row').remove();
    }
  });

});
</script>
@endsection




