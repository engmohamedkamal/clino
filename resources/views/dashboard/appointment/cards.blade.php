@extends('layouts.dash')
@section('dash-content')

<section class="container pl-main">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="pl-title mb-0">Appointments – Card View</h2>

    <a href="{{ route('appointment.show', request()->query()) }}"
       class="pl-icon-btn"
       title="Table View">
      <span class="material-icons-round">table_rows</span>
    </a>
  </div>

  <div class="row g-3">

    @forelse($appointments as $appt)
      @php
        $st = $appt->status ?? 'pending';
        $badgeClass =
          $st === 'pending' ? 'bg-warning text-dark' :
          ($st === 'cancelled' ? 'bg-danger text-white' :
          ($st === 'completed' ? 'bg-success text-white' : 'bg-secondary text-white'));
      @endphp

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">

          <div class="card-body">

            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="fw-bold mb-0">
                {{ $appt->patient_name ?? '-' }}
              </h6>
              <span class="badge {{ $badgeClass }}">{{ ucfirst($st) }}</span>
            </div>

            <div class="small text-muted mb-1">
              <strong>Doctor:</strong> {{ $appt->doctor_name ?? '-' }}
            </div>

            <div class="small text-muted mb-1">
              <strong>Date:</strong> {{ $appt->appointment_date }}
              @if($appt->appointment_time)
                – {{ $appt->appointment_time }}
              @endif
            </div>

            <div class="small text-muted mb-2">
              <strong>Phone:</strong> {{ $appt->patient_number ?? '-' }}
            </div>

            @if(is_array($appt->visit_types) && count($appt->visit_types))
              <div class="small mb-3">
                <strong>Visit:</strong>
                {{ collect($appt->visit_types)->pluck('type')->implode(', ') }}
              </div>
            @endif

            <div class="d-flex justify-content-end gap-2">

              <a href="{{ route('appointments.singleShow', $appt->id) }}"
                 class="btn btn-sm btn-outline-secondary">
                <span class="material-icons-round fs-6">visibility</span>
              </a>

              @if(auth()->user()->role === 'admin')
                <a href="{{ route('appointment.reset', $appt->id) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary">
                  <span class="material-icons-round fs-6">print</span>
                </a>
              @endif

            </div>

          </div>
        </div>
      </div>

    @empty
      <div class="col-12 text-center py-5">
        No appointments found.
      </div>
    @endforelse

  </div>

  <div class="mt-4">
    {{ $appointments->links('pagination::bootstrap-5') }}
  </div>

</section>

@endsection
