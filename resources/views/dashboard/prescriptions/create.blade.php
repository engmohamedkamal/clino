@extends('layouts.dash')

@section('dash-content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">

@php
  // ===== OLD ARRAYS (علشان نرسم نفس عدد الصفوف بعد validation error) =====
  $oldMeds      = old('medicine_name', ['']);
  $oldDosages   = old('dosage', ['']);
  $oldDurations = old('duration', ['']);
  $oldNotes     = old('notes', ['']);

  $oldRumors    = old('rumor_name', ['']);
  $oldAnalyses  = old('analysis_name', ['']);

  // ✅ patient selected from old() OR query param (if passed from appointment)
  $selectedPatientId = old('patient_id', $selectedPatientId ?? request('patient_id'));
@endphp

<div class="container-fluid">

  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-9">

      <div class="card border-0 shadow-lg">
        <div class="card-body p-4 p-md-5">

          {{-- Header --}}
          <div class="mb-4 text-center">
              <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
            <i class="fa-solid fa-prescription-bottle-medical fa-2x text-primary mb-2"></i>
            <h4 class="fw-bold mb-1">Create Prescription</h4>
            <p class="text-muted mb-0">Fill in the prescription details carefully</p>
          </div>

          <form method="POST" action="{{ route('prescriptions.store') }}" novalidate>
            @csrf

            <div class="row g-3">

              {{-- Patient --}}
              <div class="col-md-6">
                <label class="form-label fw-semibold">Patient</label>
                <select name="patient_id"
                        class="form-select ts-select ts-patient @error('patient_id') is-invalid @enderror"
                        required>
                  <option value="">-- Select Patient --</option>
                  @foreach($patients as $p)
                  <option value="{{ $p->id }}"
  @selected($patient === $p->name)>
  {{ $p->name }}
</option>

                  @endforeach
                </select>
                @error('patient_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Doctor (Admin only) --}}
              @if(auth()->user()->role === 'admin')
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Doctor</label>
                  <select name="doctor_id"
                          class="form-select ts-select ts-doctor @error('doctor_id') is-invalid @enderror"
                          required>
                    <option value="">-- Select Doctor --</option>
                    @foreach($doctors as $doctor)
                      <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
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
                  @foreach($oldMeds as $i => $val)
                    <div class="row g-2 align-items-end medicine-row mb-2">

                   <div class="col-md-3">
  <select name="medicine_name[]" class="form-select ts-select ts-medicine" required>
    <option value="">Select medicine...</option>
    @foreach($medicinesList as $m)
      <option
        value="{{ $m->name }}"
        data-dosage="{{ $m->dosage }}"
        data-duration="{{ $m->duration }}"
        data-notes="{{ $m->notes }}"
        @selected(($oldMeds[$i] ?? '') == $m->name)
      >
        {{ $m->name }}
      </option>
    @endforeach
  </select>
</div>


                      <div class="col-md-3">
                        <input type="text" name="dosage[]" class="form-control" placeholder="Dosage"
                               value="{{ $oldDosages[$i] ?? '' }}" required>
                      </div>

                      <div class="col-md-3">
                        <input type="text" name="duration[]" class="form-control" placeholder="Duration"
                               value="{{ $oldDurations[$i] ?? '' }}" required>
                      </div>

                      <div class="col-md-2">
                        <input type="text" name="notes[]" class="form-control" placeholder="Notes"
                               value="{{ $oldNotes[$i] ?? '' }}">
                      </div>

                      <div class="col-md-1 text-center">
                        @if($i == 0)
                          <button type="button" class="btn btn-outline-primary add-row" data-target="medicinesWrapper" data-template="medicineTemplate">
                            <i class="fa-solid fa-plus"></i>
                          </button>
                        @else
                          <button type="button" class="btn btn-outline-danger remove-row">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        @endif
                      </div>

                    </div>
                  @endforeach
                </div>

                @error('medicine_name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Radiology ================= --}}
              <div class="col-12">
                <label class="form-label fw-semibold mb-2">Radiology (X-Ray / Scan)</label>

                <div id="rumorWrapper">
                  @foreach($oldRumors as $i => $val)
                    <div class="row g-2 align-items-end rumor-row mb-2">

                      <div class="col-md-11">
                        <select name="rumor_name[]" class="form-select ts-select ts-rumor">
                          <option value="">Select radiology...</option>
                          @foreach($rumorsList as $r)
                            <option value="{{ $r->name }}" @selected(($oldRumors[$i] ?? '') == $r->name)>
                              {{ $r->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-1 text-center">
                        @if($i == 0)
                          <button type="button" class="btn btn-outline-primary add-row" data-target="rumorWrapper" data-template="rumorTemplate">
                            <i class="fa-solid fa-plus"></i>
                          </button>
                        @else
                          <button type="button" class="btn btn-outline-danger remove-row">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        @endif
                      </div>

                    </div>
                  @endforeach
                </div>

                @error('rumor_name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- ================= Analysis ================= --}}
              <div class="col-12">
                <label class="form-label fw-semibold mb-2">Analysis (Lab Tests)</label>

                <div id="analysisWrapper">
                  @foreach($oldAnalyses as $i => $val)
                    <div class="row g-2 align-items-end analysis-row mb-2">

                      <div class="col-md-11">
                        <select name="analysis_name[]" class="form-select ts-select ts-analysis">
                          <option value="">Select analysis...</option>
                          @foreach($analysesList as $a)
                            <option value="{{ $a->name }}" @selected(($oldAnalyses[$i] ?? '') == $a->name)>
                              {{ $a->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-1 text-center">
                        @if($i == 0)
                          <button type="button" class="btn btn-outline-primary add-row" data-target="analysisWrapper" data-template="analysisTemplate">
                            <i class="fa-solid fa-plus"></i>
                          </button>
                        @else
                          <button type="button" class="btn btn-outline-danger remove-row">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        @endif
                      </div>

                    </div>
                  @endforeach
                </div>

                @error('analysis_name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Diagnosis --}}
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

              {{-- Actions --}}
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

{{-- ✅ Templates (نظيفة بدون TomSelect wrappers) --}}
<template id="medicineTemplate">
  <div class="row g-2 align-items-end medicine-row mb-2">
    <div class="col-md-3">
      <select name="medicine_name[]" class="form-select ts-select ts-medicine" required>
        <option value="">Select medicine...</option>
        @foreach($medicinesList as $m)
          <option value="{{ $m->name }}">{{ $m->name }}</option>
        @endforeach
      </select>
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
      <button type="button" class="btn btn-outline-danger remove-row">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
</template>

<template id="rumorTemplate">
  <div class="row g-2 align-items-end rumor-row mb-2">
    <div class="col-md-11">
      <select name="rumor_name[]" class="form-select ts-select ts-rumor">
        <option value="">Select radiology...</option>
        @foreach($rumorsList as $r)
          <option value="{{ $r->name }}">{{ $r->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-1 text-center">
      <button type="button" class="btn btn-outline-danger remove-row">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
</template>

<template id="analysisTemplate">
  <div class="row g-2 align-items-end analysis-row mb-2">
    <div class="col-md-11">
      <select name="analysis_name[]" class="form-select ts-select ts-analysis">
        <option value="">Select analysis...</option>
        @foreach($analysesList as $a)
          <option value="{{ $a->name }}">{{ $a->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-1 text-center">
      <button type="button" class="btn btn-outline-danger remove-row">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
</template>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

  function initTomSelect(container) {
    container.querySelectorAll('select.ts-select').forEach((sel) => {
      if (sel.tomselect) return;

      const isPatient = sel.classList.contains('ts-patient');
      const isDoctor  = sel.classList.contains('ts-doctor');

      new TomSelect(sel, {
        create: false,
        allowEmptyOption: true,
        maxItems: 1,
        placeholder: sel.querySelector('option')?.textContent || 'Select...',

        // ✅ تحسين البحث خصوصاً للمرضى/الدكاترة
        searchField: ['text'],
        closeAfterSelect: true,
        openOnFocus: true,
      });

      // ✅ لو patient عليه required و user مسح القيمة
      if (isPatient) {
        sel.addEventListener('change', () => {});
      }
    });
  }

  // init for initial rows
  initTomSelect(document);

  document.addEventListener('click', function (e) {
    const addBtn = e.target.closest('.add-row');
    if (addBtn) {
      const wrapperId = addBtn.getAttribute('data-target');
      const templateId = addBtn.getAttribute('data-template');

      const wrapper = document.getElementById(wrapperId);
      const template = document.getElementById(templateId);

      if (!wrapper || !template) return;

      const node = template.content.cloneNode(true);
      wrapper.appendChild(node);

      // init tomselect for newly added row
      initTomSelect(wrapper);
      return;
    }

    const removeBtn = e.target.closest('.remove-row');
    if (removeBtn) {
      const row = removeBtn.closest('.row');

      // destroy tomselect instances before removing
      row.querySelectorAll('select.ts-select').forEach(sel => {
        if (sel.tomselect) sel.tomselect.destroy();
      });

      row.remove();
      return;
    }
  });

});

document.addEventListener('change', function (e) {
  const select = e.target.closest('.ts-medicine');
  if (!select) return;

  const option = select.options[select.selectedIndex];
  if (!option) return;

  const row = select.closest('.medicine-row');
  if (!row) return;

  const dosageInput   = row.querySelector('input[name="dosage[]"]');
  const durationInput = row.querySelector('input[name="duration[]"]');
  const notesInput    = row.querySelector('input[name="notes[]"]');

  // لو المستخدم اختار placeholder
  if (!option.value) {
    if (dosageInput) dosageInput.value = '';
    if (durationInput) durationInput.value = '';
    if (notesInput) notesInput.value = '';
    return;
  }

  // Auto fill
  if (dosageInput && !dosageInput.value) {
    dosageInput.value = option.dataset.dosage || '';
  }

  if (durationInput && !durationInput.value) {
    durationInput.value = option.dataset.duration || '';
  }

  if (notesInput && !notesInput.value) {
    notesInput.value = option.dataset.notes || '';
  }
});
</script>

@endsection
