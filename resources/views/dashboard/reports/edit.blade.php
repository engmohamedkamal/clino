@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/addReports.css') }}" />

<main class="main">

  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#mobileSidebar"
              aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div class="page-title">Edit Report</div>
    </div>
  </header>

  <section class="rp-wrap py-4">
    <div class="container">

      <div class="rp-panel mx-auto">

        {{-- ✅ Success --}}
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif



        {{-- ✅ FORM --}}
        <form class="rp-form"
              method="POST"
              action="{{ route('reports.update', $report->id) }}"
              novalidate
              enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row g-4">

            {{-- ✅ Patient + Date --}}
            <div class="col-12 col-md-6">
              <label class="rp-label">Patient</label>

@php
  // ✅ لو رجع validation error
  $selectedRef = old('patient_ref');

  // ✅ لو مفيش old خالص، هنعمل ref من بيانات التقرير
  // 1) لو التقرير مربوط بجدول patients
  if (!$selectedRef && !empty($report->patient_id)) {
      $selectedRef = 'patients:' . $report->patient_id;
  }

  // 2) لو التقرير مربوط بيوزر patient (لو عندك patient_user_id في reports)
  if (!$selectedRef && !empty($report->patient_user_id)) {
      $selectedRef = 'users:' . $report->patient_user_id;
  }
@endphp

<div class="rp-field">
  <select name="patient_ref" class="rp-control @error('patient_ref') is-invalid @enderror" required>
    <option value="" disabled {{ $selectedRef ? '' : 'selected' }}>Select Patient</option>

    @foreach($patients as $p)
      @php $ref = $p->source.':'.$p->id; @endphp

      <option value="{{ $ref }}" {{ $selectedRef === $ref ? 'selected' : '' }}>
        {{ $p->name }} {{ $p->phone ? ' - '.$p->phone : '' }}
      </option>
    @endforeach
  </select>

  <i class="bi bi-chevron-down rp-sfx"></i>
</div>

@error('patient_ref')
  <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror


              <label class="rp-label mt-4">Examination Date</label>
              <div class="rp-field">
                <input
                  name="exam_date"
                  class="rp-control @error('exam_date') is-invalid @enderror"
                  type="date"
                  value="{{ old('exam_date', optional($report->exam_date)->format('Y-m-d') ?? $report->exam_date) }}"
                  required
                />
                <i class="bi bi-calendar3 rp-sfx"></i>
              </div>
              @error('exam_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- ✅ Exam Type + Image --}}
            <div class="col-12 col-md-6">

              {{-- Exam Type --}}
              <label class="rp-label">Type of examination</label>
              <div class="rp-field">
                <select
                  name="exam_type"
                  id="exam_type"
                  class="rp-control @error('exam_type') is-invalid @enderror"
                  required
                >
                  <option value="" disabled {{ old('exam_type', $report->exam_type) ? '' : 'selected' }}>Select</option>

                  <optgroup label="Lab Tests">
                    <option value="lab_result" {{ old('exam_type', $report->exam_type)=='lab_result' ? 'selected' : '' }}>Lab Result</option>
                    <option value="blood_test" {{ old('exam_type', $report->exam_type)=='blood_test' ? 'selected' : '' }}>Blood Test</option>
                    <option value="urine_test" {{ old('exam_type', $report->exam_type)=='urine_test' ? 'selected' : '' }}>Urine Test</option>
                  </optgroup>

                  <optgroup label="Imaging">
                    <option value="x_ray" {{ old('exam_type', $report->exam_type)=='x_ray' ? 'selected' : '' }}>X-Ray</option>
                    <option value="ct_scan" {{ old('exam_type', $report->exam_type)=='ct_scan' ? 'selected' : '' }}>CT Scan</option>
                    <option value="mri" {{ old('exam_type', $report->exam_type)=='mri' ? 'selected' : '' }}>MRI</option>
                    <option value="ultrasound" {{ old('exam_type', $report->exam_type)=='ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                  </optgroup>

                  <optgroup label="Medical">
                    <option value="prescription" {{ old('exam_type', $report->exam_type)=='prescription' ? 'selected' : '' }}>Prescription</option>
                    <option value="medical_report" {{ old('exam_type', $report->exam_type)=='medical_report' ? 'selected' : '' }}>Medical Report</option>
                  </optgroup>
                </select>
                <i class="bi bi-chevron-down rp-sfx"></i>
              </div>
              @error('exam_type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror

              {{-- Image Upload --}}
              <label class="rp-label mt-4">Examination Image</label>

              {{-- Current image --}}
              @if(!empty($report->exam_image))
                <div class="mb-2">
                  <div class="text-muted small mb-1">Current image</div>
                  <a href="{{ asset('storage/'.$report->exam_image) }}" target="_blank" class="d-inline-block">
                    <img
                      src="{{ asset('storage/'.$report->exam_image) }}"
                      class="img-fluid rounded"
                      style="max-height: 140px;"
                      alt="Current image"
                    >
                  </a>
                </div>
              @else
                <div class="text-muted small mb-2">No image uploaded yet.</div>
              @endif

              <div class="rp-field">
                <input
                  type="file"
                  name="exam_image"
                  id="exam_image"
                  accept="image/*"
                  class="form-control @error('exam_image') is-invalid @enderror"
                >
                <i class="bi bi-image rp-sfx"></i>
              </div>

              @error('exam_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror

              {{-- New Image Preview --}}
              <div class="mt-2">
                <div class="text-muted small mb-1">New image preview</div>
                <img
                  id="examImagePreview"
                  src=""
                  class="img-fluid rounded d-none"
                  style="max-height: 180px;"
                  alt="Preview"
                >
              </div>

            </div>

            {{-- Notes --}}
            <div class="col-12">
              <label class="rp-label">Notes</label>
              <textarea
                name="note"
                class="rp-control rp-textarea @error('note') is-invalid @enderror"
                placeholder="note"
              >{{ old('note', $report->note) }}</textarea>
              @error('note')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Save --}}
            <div class="col-12 d-flex gap-2">
             

              <button class="rp-save" type="submit">
                Update
              </button>
            </div>

          </div>
        </form>

      </div>

    </div>
  </section>

</main>

<script>
  document.getElementById('exam_image')?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    const preview = document.getElementById('examImagePreview');

    if (!file) {
      preview.classList.add('d-none');
      preview.src = '';
      return;
    }

    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
  });
</script>

@endsection
