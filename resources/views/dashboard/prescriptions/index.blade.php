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
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-bold">
                  RX-{{ str_pad($rx->id, 6, '0', STR_PAD_LEFT) }}
                </div>
                <span class="badge bg-light text-dark">
                  {{ $rx->created_at->format('d M Y') }}
                </span>
              </div>

              <div class="mb-2 text-muted small">
                <i class="fa-solid fa-user me-1"></i>
                {{ $rx->patientUser->name ?? 'Patient' }}
              </div>

              <div class="mb-3">
                <div class="fw-semibold">{{ $rx->medicine_name }}</div>
                <div class="text-muted small">
                  {{ $rx->dosage }} • {{ $rx->duration }}
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('prescriptions.show', $rx->id) }}"
                   class="btn btn-sm btn-outline-primary">
                  View
                </a>

                <span class="text-muted small">
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

    <h4 class="mb-3">My Prescriptions</h4>

    @forelse($prescriptions as $rx)
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h5 class="mb-1">{{ $rx->medicine_name }}</h5>
              <div class="text-muted small mb-2">
                Dosage: {{ $rx->dosage }} · Duration: {{ $rx->duration }}
              </div>
              @if($rx->notes)
                <p class="mb-0">{{ $rx->notes }}</p>
              @endif
            </div>

            <a href="{{ route('prescriptions.show', $rx->id) }}" class="btn btn-sm btn-outline-primary">
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

  @endif

</div>

@endsection
