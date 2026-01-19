@extends('layouts.dash')
@section('dash-content')

<main class="main">
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button
        class="btn icon-btn d-lg-none"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar"
        aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <h3 class="appointment-title mt-2 mb-0">
        Add Medical Order
      </h3>
    </div>
  </header>

  <section class="content-area">
    <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
      <div class="appointment-card" style="max-width:520px">

        {{-- Success Message --}}
        @if (session('success'))
          <div class="alert alert-success mb-3">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('medical-orders.store') }}">
          @csrf

          {{-- ================= Order Name ================= --}}
          <div class="mb-3">
            <label class="form-label appointment-label" for="orderName">
              Medical Order Name
            </label>
            <input
              type="text"
              id="orderName"
              name="name"
              value="{{ old('name') }}"
              class="form-control appointment-control @error('name') is-invalid @enderror"
              placeholder="e.g. Paracetamol / CBC / X-Ray Chest">

            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- ================= Order Type ================= --}}
          <div class="mb-4">
            <label class="form-label appointment-label" for="orderType">
              Order Type
            </label>
            <select
              id="orderType"
              name="type"
              class="form-select appointment-control @error('type') is-invalid @enderror">
              <option value="" disabled {{ old('type') ? '' : 'selected' }}>
                Select type
              </option>
              <option value="medicine" @selected(old('type') === 'medicine')>
                Medicine
              </option>
              <option value="rumor" @selected(old('type') === 'rumor')>
                Radiology
              </option>
              <option value="analysis" @selected(old('type') === 'analysis')>
                Analysis
              </option>
            </select>

            @error('type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- ================= Medicine Fields ================= --}}
          <div id="medicineFields" style="display:none;">

            {{-- Dosage --}}
            <div class="mb-3">
              <label class="form-label appointment-label" for="dosage">
                Dosage
              </label>
              <input
                type="text"
                id="dosage"
                name="dosage"
                value="{{ old('dosage') }}"
                class="form-control appointment-control @error('dosage') is-invalid @enderror"
                placeholder="e.g. 1 tablet twice daily">

              @error('dosage')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Duration --}}
            <div class="mb-3">
              <label class="form-label appointment-label" for="duration">
                Duration
              </label>
              <input
                type="text"
                id="duration"
                name="duration"
                value="{{ old('duration') }}"
                class="form-control appointment-control @error('duration') is-invalid @enderror"
                placeholder="e.g. 5 days / 1 week">

              @error('duration')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-4">
              <label class="form-label appointment-label" for="notes">
                Notes
              </label>
              <textarea
                id="notes"
                name="notes"
                rows="3"
                class="form-control appointment-control @error('notes') is-invalid @enderror"
                placeholder="Additional instructions or notes">{{ old('notes') }}</textarea>

              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>

          {{-- ================= Save ================= --}}
          <button type="submit" class="btn btn-primary btn-save-full">
            Save Medical Order
          </button>

        </form>

      </div>
    </div>
  </section>
</main>

{{-- ================= Script ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const typeSelect = document.getElementById('orderType');
  const medicineFields = document.getElementById('medicineFields');

  function toggleMedicineFields() {
    if (typeSelect.value === 'medicine') {
      medicineFields.style.display = 'block';
    } else {
      medicineFields.style.display = 'none';

      // optional: clear values when not medicine
      medicineFields.querySelectorAll('input, textarea').forEach(el => {
        el.value = '';
      });
    }
  }

  typeSelect.addEventListener('change', toggleMedicineFields);

  // init (important for old values after validation)
  toggleMedicineFields();
});
</script>

@endsection
