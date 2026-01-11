@extends('layouts.dash')

@section('dash-content')

<div class="container-fluid">

  {{-- ================= Doctor / Admin ================= --}}
  @if(auth()->user()->role === 'doctor' || auth()->user()->role === 'admin')

    <div class="container py-4">

      {{-- Top Card --}}
      <div class="d-flex justify-content-center mb-4">
        <div class="card border-0 shadow-lg text-center w-100" style="max-width:620px;">
          <div class="card-body p-4 p-md-5">

            <div class="mb-3">
              <i class="fa-solid fa-prescription-bottle-medical fa-3x text-primary"></i>
            </div>

            <h4 class="fw-bold mb-2">
              Welcome Dr. {{ auth()->user()->name }}
            </h4>

            <p class="text-muted mb-4">
              You can create and manage your patient prescriptions.
            </p>

            <a href="{{ route('prescriptions.create') }}" class="btn btn-primary btn-lg">
              <i class="fa-solid fa-plus me-1"></i>
              Create Prescription
            </a>

          </div>
        </div>
      </div>

      {{-- Prescriptions List --}}
      @if($prescriptions->count())
        <div class="row g-3">

          @foreach($prescriptions as $rx)
            @php
              $medicines = is_array($rx->medicine_name) ? $rx->medicine_name : (json_decode($rx->medicine_name, true) ?: []);
              $dosages   = is_array($rx->dosage) ? $rx->dosage : (json_decode($rx->dosage, true) ?: []);
              $durations = is_array($rx->duration) ? $rx->duration : (json_decode($rx->duration, true) ?: []);
              $notesArr  = is_array($rx->notes) ? $rx->notes : (json_decode($rx->notes, true) ?: []);

              // ✅ NEW: Radiology + Analysis
              $rumors    = is_array($rx->rumor) ? $rx->rumor : (json_decode($rx->rumor, true) ?: []);
              $analyses  = is_array($rx->analysis) ? $rx->analysis : (json_decode($rx->analysis, true) ?: []);

              $firstMed  = $medicines[0] ?? '-';
              $firstDos  = $dosages[0]   ?? '-';
              $firstDur  = $durations[0] ?? '-';
              $moreCount = max(count($medicines) - 1, 0);

              $firstRumor = $rumors[0] ?? null;
              $moreRumor  = max(count($rumors) - 1, 0);

              $firstAna   = $analyses[0] ?? null;
              $moreAna    = max(count($analyses) - 1, 0);
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
              <div class="card h-100 border-0 shadow-sm">

                <div class="card-body">

                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold">
                      RX-{{ str_pad($rx->id, 6, '0', STR_PAD_LEFT) }}
                    </div>

                    <span class="badge bg-light text-dark">
                      {{ optional($rx->created_at)->format('d M Y') }}
                    </span>
                  </div>

                  <div class="mb-2 text-muted small">
                    <i class="fa-solid fa-user me-1"></i>
                    {{ $rx->patientUser->name ?? 'Patient' }}
                  </div>

                  {{-- Medicines Preview --}}
                  <div class="mb-3">
                    <div class="fw-semibold">
                      {{ $firstMed }}
                      @if($moreCount > 0)
                        <span class="badge bg-primary-subtle text-primary ms-2">
                          +{{ $moreCount }} more
                        </span>
                      @endif
                    </div>

                    <div class="text-muted small">
                      {{ $firstDos }} • {{ $firstDur }}
                    </div>
                  </div>

                  {{-- ✅ Radiology --}}
                  @if($firstRumor)
                    <div class="mb-2">
                      <div class="small text-muted">
                        <i class="fa-solid fa-x-ray me-1"></i> Radiology:
                      </div>
                      <div class="fw-semibold">
                        {{ $firstRumor }}
                        @if($moreRumor > 0)
                          <span class="badge bg-warning-subtle text-warning ms-2">
                            +{{ $moreRumor }} more
                          </span>
                        @endif
                      </div>
                    </div>
                  @endif

                  {{-- ✅ Analysis --}}
                  @if($firstAna)
                    <div class="mb-3">
                      <div class="small text-muted">
                        <i class="fa-solid fa-vials me-1"></i> Analysis:
                      </div>
                      <div class="fw-semibold">
                        {{ $firstAna }}
                        @if($moreAna > 0)
                          <span class="badge bg-success-subtle text-success ms-2">
                            +{{ $moreAna }} more
                          </span>
                        @endif
                      </div>
                    </div>
                  @endif

                  <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('prescriptions.show', $rx->id) }}"
                       class="btn btn-sm btn-outline-primary">
                      View
                    </a>

                    <span class="text-muted small text-truncate" style="max-width: 55%;">
                      {{ $rx->diagnosis }}
                    </span>
                  </div>

                </div>

              </div>
            </div>
          @endforeach

        </div>

      @else
        {{-- Empty State --}}
        <div class="text-center text-muted py-5">
          <i class="fa-solid fa-file-circle-xmark fa-3x mb-3"></i>
          <h5>No prescriptions yet</h5>
          <p class="mb-0">Start by creating your first prescription.</p>
        </div>
      @endif

    </div>

  @endif

  {{-- ================= Patient ================= --}}
  @if(auth()->user()->role === 'patient')

    <div class="container py-4">
      <h4 class="mb-3">My Prescriptions</h4>

      @forelse($prescriptions as $rx)
        @php
          $medicines = is_array($rx->medicine_name) ? $rx->medicine_name : (json_decode($rx->medicine_name, true) ?: []);
          $dosages   = is_array($rx->dosage) ? $rx->dosage : (json_decode($rx->dosage, true) ?: []);
          $durations = is_array($rx->duration) ? $rx->duration : (json_decode($rx->duration, true) ?: []);
          $notesArr  = is_array($rx->notes) ? $rx->notes : (json_decode($rx->notes, true) ?: []);

          // ✅ NEW
          $rumors    = is_array($rx->rumor) ? $rx->rumor : (json_decode($rx->rumor, true) ?: []);
          $analyses  = is_array($rx->analysis) ? $rx->analysis : (json_decode($rx->analysis, true) ?: []);

          $firstMed  = $medicines[0] ?? '-';
          $firstDos  = $dosages[0]   ?? '-';
          $firstDur  = $durations[0] ?? '-';
          $moreCount = max(count($medicines) - 1, 0);

          $firstNote = $notesArr[0] ?? null;

          $firstRumor = $rumors[0] ?? null;
          $moreRumor  = max(count($rumors) - 1, 0);

          $firstAna   = $analyses[0] ?? null;
          $moreAna    = max(count($analyses) - 1, 0);
        @endphp

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3">
              <div class="flex-grow-1">

                <h5 class="mb-1">
                  {{ $firstMed }}
                  @if($moreCount > 0)
                    <span class="badge bg-primary-subtle text-primary ms-2">
                      +{{ $moreCount }} more
                    </span>
                  @endif
                </h5>

                <div class="text-muted small mb-2">
                  Dosage: {{ $firstDos }} · Duration: {{ $firstDur }}
                </div>

                {{-- ✅ Radiology --}}
                @if($firstRumor)
                  <div class="small text-muted mb-1">
                    <i class="fa-solid fa-x-ray me-1"></i>
                    {{ $firstRumor }}
                    @if($moreRumor > 0)
                      <span class="badge bg-warning-subtle text-warning ms-2">
                        +{{ $moreRumor }} more
                      </span>
                    @endif
                  </div>
                @endif

                {{-- ✅ Analysis --}}
                @if($firstAna)
                  <div class="small text-muted mb-1">
                    <i class="fa-solid fa-vials me-1"></i>
                    {{ $firstAna }}
                    @if($moreAna > 0)
                      <span class="badge bg-success-subtle text-success ms-2">
                        +{{ $moreAna }} more
                      </span>
                    @endif
                  </div>
                @endif

                @if($firstNote)
                  <p class="mb-0 text-muted">{{ $firstNote }}</p>
                @endif

              </div>

              <a href="{{ route('prescriptions.show', $rx->id) }}"
                 class="btn btn-sm btn-outline-primary">
                View
              </a>
            </div>
          </div>
        </div>

      @empty
        <div class="alert alert-info">
          <i class="fa-solid fa-circle-info me-1"></i>
          No prescriptions available for you yet.
        </div>
      @endforelse

    </div>

  @endif

</div>

@endsection
