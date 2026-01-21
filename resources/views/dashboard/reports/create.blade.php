@extends('layouts.dash')
@section('dash-content')

  <link rel="stylesheet" href="{{ asset('CSS/addReports.css') }}" />

  <main class="main">
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
          aria-controls="mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">Add Reports Results</div>
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

          {{-- ✅ Errors --}}
          @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
              <div class="fw-semibold mb-1">Please fix the following:</div>
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          {{-- ✅ FORM --}}
          <form class="rp-form" method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data"
            novalidate>
            @csrf

            <div class="row g-4">

              {{-- ✅ Patient --}}
              <div class="col-12 col-md-6">
                <label class="rp-label">Patient</label>
                @php
                  $selectedPatient = old('patient_ref')
                    ?? (isset($patient_id) ? 'patient:' . $patient_id : null);
                @endphp

                <div class="rp-field">
                  <select name="patient_ref" class="rp-control @error('patient_ref') is-invalid @enderror" required>
                    <option value="" disabled {{ $selectedPatient ? '' : 'selected' }}>
                      Select Patient
                    </option>

                    @foreach($patients as $p)
                      @php
                        $value = $p->source . ':' . $p->id;
                      @endphp

                      <option value="{{ $value }}" {{ $selectedPatient === $value ? 'selected' : '' }}>
                        {{ $p->name }} {{ $p->phone ? ' - ' . $p->phone : '' }}
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
                  <input name="exam_date" type="date" class="rp-control @error('exam_date') is-invalid @enderror"
                    value="{{ old('exam_date') }}" required />
                  <i class="bi bi-calendar3 rp-sfx"></i>
                </div>

                @error('exam_date')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              {{-- ✅ Exam Type + Image --}}
              <div class="col-12 col-md-6">

                <label class="rp-label">Type of examination</label>
                <div class="rp-field">
                  <select name="exam_type" id="exam_type" class="rp-control @error('exam_type') is-invalid @enderror"
                    required>
                    <option value="" disabled {{ old('exam_type') ? '' : 'selected' }}>Select</option>

                    <optgroup label="Lab Tests">
                      <option value="lab_result" {{ old('exam_type') == 'lab_result' ? 'selected' : '' }}>Lab Result
                      </option>
                      <option value="blood_test" {{ old('exam_type') == 'blood_test' ? 'selected' : '' }}>Blood Test
                      </option>
                      <option value="urine_test" {{ old('exam_type') == 'urine_test' ? 'selected' : '' }}>Urine Test
                      </option>
                    </optgroup>

                    <optgroup label="Imaging">
                      <option value="x_ray" {{ old('exam_type') == 'x_ray' ? 'selected' : '' }}>X-Ray</option>
                      <option value="ct_scan" {{ old('exam_type') == 'ct_scan' ? 'selected' : '' }}>CT Scan</option>
                      <option value="mri" {{ old('exam_type') == 'mri' ? 'selected' : '' }}>MRI</option>
                      <option value="ultrasound" {{ old('exam_type') == 'ultrasound' ? 'selected' : '' }}>Ultrasound
                      </option>
                    </optgroup>

                    <optgroup label="Medical">
                      <option value="prescription" {{ old('exam_type') == 'prescription' ? 'selected' : '' }}>Prescription
                      </option>
                      <option value="medical_report" {{ old('exam_type') == 'medical_report' ? 'selected' : '' }}>Medical
                        Report</option>
                    </optgroup>
                  </select>
                  <i class="bi bi-chevron-down rp-sfx"></i>
                </div>

                @error('exam_type')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <label class="rp-label mt-4" for="exam_image">Examination Image</label>

                <div class="rp-file-wrap @error('exam_image') is-invalid @enderror">

                  <input type="file" name="exam_image" id="exam_image" accept="image/*" class="rp-file-input" />

                  <div class="rp-file-ui">
                    <span class="rp-file-btn">
                      <i class="bi bi-upload me-1"></i>
                      Choose Image
                    </span>

                    <span class="rp-file-name" id="examImageName">
                      No file chosen
                    </span>

                    <i class="bi bi-image rp-file-icon"></i>
                  </div>

                </div>

                @error('exam_image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror


                <div class="mt-2">
                  <img id="examImagePreview" src="" class="img-fluid rounded d-none" style="max-height: 180px;"
                    alt="Preview">
                </div>

              </div>

              {{-- ✅ Notes --}}
              <div class="col-12">
                <label class="rp-label">Notes</label>
                <textarea name="note" class="rp-control rp-textarea @error('note') is-invalid @enderror"
                  placeholder="Write notes...">{{ old('note') }}</textarea>

                @error('note')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              {{-- ✅ Save --}}
              <div class="col-12">
                <button class="rp-save" type="submit">Save</button>
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
    const examInput = document.getElementById('exam_image');
    const examName = document.getElementById('examImageName');

    if (examInput) {
      examInput.addEventListener('change', function () {
        examName.textContent = this.files.length
          ? this.files[0].name
          : 'No file chosen';
      });
    }
  </script>


@endsection