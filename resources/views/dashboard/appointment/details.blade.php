@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/appointmentDetails.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'doctor']);

    $st = $appointment->status ?? 'pending';
    $stClass =
      $st === 'pending' ? 'bg-warning text-dark' :
      ($st === 'cancelled' ? 'bg-danger text-white' :
        ($st === 'completed' ? 'bg-success text-white' : 'bg-secondary text-white'));
  @endphp

  <section class="ad-main container">
    {{-- Topbar --}}
    <div class="ad-topbar">
      <div class="ad-left">
        <a href="{{ route('appointment.show')}}" class="ad-icon-btn" aria-label="Back" title="Back">
          <span class="material-icons-round">arrow_back</span>
        </a>

        <div>
          <h2 class="ad-title mb-0">Appointment Details</h2>
          <div class="ad-sub">Full information about this booking</div>
        </div>
      </div>

      <div class="ad-actions">
        <span class="badge ad-status {{ $stClass }}">
          {{ ucfirst($st) }}
        </span>

        @if($canManage)
          <a href="{{ url('/appointments') . '/' . $appointment->id . '/edit' }}" class="ad-icon-btn" title="Edit">
            <span class="material-icons-round">edit</span>
          </a>

          <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="ad-icon-btn ad-danger" type="submit" title="Delete"
              onclick="return confirm('Delete this appointment?')">
              <span class="material-icons-round">delete</span>
            </button>
          </form>
        @endif
      </div>
    </div>

    {{-- Card --}}
    <div class="ad-card">
      <div class="ad-grid">

        {{-- Patient --}}
        <div class="ad-box">
          <div class="ad-box-title">
            <span class="material-icons-round">person</span>
            <span>Patient</span>
          </div>

          <div class="ad-row">
            <div class="ad-label">Name</div>
            <div class="ad-value">{{ $appointment->patient_name ?? '-' }}</div>
          </div>

          <div class="ad-row">
            <div class="ad-label">Phone</div>
            <div class="ad-value">{{ $appointment->patient_number ?? '-' }}</div>
          </div>

          <div class="ad-row">
            <div class="ad-label">DOB</div>
            <div class="ad-value">
              {{ $appointment->dob ? \Carbon\Carbon::parse($appointment->dob)->format('d/m/Y') : '-' }}
            </div>
          </div>

          <div class="ad-row">
            <div class="ad-label">Gender</div>
            <div class="ad-value">{{ $appointment->gender ?? '-' }}</div>
          </div>
          <div class="ad-row">
            <div class="ad-label">Reason</div>
            <div class="ad-value">{{ $appointment->reason ?? '-' }}</div>
          </div>
        </div>

        {{-- Doctor --}}
        <div class="ad-box">

          {{-- Title --}}
          <div class="ad-box-title d-flex align-items-center gap-2">
            <span class="material-icons-round">medical_services</span>
            <span>Doctor</span>
          </div>

          {{-- Doctor name --}}
          <div class="ad-row">
            <div class="ad-label">Doctor</div>
            <div class="ad-value">
              {{ $appointment->doctor_name ?? '-' }}
            </div>
          </div>

          {{-- Actions under doctor name --}}
          @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
            <div class="ad-row">
              <div class="ad-label"></div>
              <div class="ad-value">
                <div class="ad-box-actions d-flex gap-2 flex-wrap">

                  {{-- Add Report --}}
                  <a href="{{ route('reports.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}"
                    class="ad-btn ad-btn-soft" title="Add Report">
                    <span class="material-icons-round">description</span>
                    Report
                  </a>

                  {{-- Add Prescription --}}
                  <a href="{{ route('prescriptions.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}"
                    class="ad-btn ad-btn-primary" title="Add Prescription">
                    <span class="material-icons-round">medication</span>
                    Rx
                  </a>

                </div>
              </div>
            </div>
          @endif

        </div>


        {{-- Date & Time --}}
        <div class="ad-box">
          <div class="ad-box-title">
            <span class="material-icons-round">event</span>
            <span>Date & Time</span>
          </div>

          <div class="ad-row">
            <div class="ad-label">Date</div>
            <div class="ad-value">{{ $appointment->appointment_date ?? '-' }}</div>
          </div>

          <div class="ad-row">
            <div class="ad-label">Time</div>
            <div class="ad-value">{{ $appointment->appointment_time ?? '-' }}</div>
          </div>

          {{-- ✅ Visit Type --}}
          <div class="ad-row">
            <div class="ad-label">Visit Type</div>
            <div class="ad-value"> @if(is_array($appointment->visit_types) && count($appointment->visit_types))
    {{ collect($appointment->visit_types)->pluck('type')->implode(' , ') }}
  @else
    -
  @endif</div>
          </div>



          <div class="ad-row">
            <div class="ad-label">Created</div>
            <div class="ad-value">{{ optional($appointment->created_at)->format('d/m/Y - h:i A') ?? '-' }}</div>
          </div>
        </div>

        {{-- Status --}}
        <div class="ad-box">
          <div class="ad-box-title">
            <span class="material-icons-round">verified</span>
            <span>Status</span>
          </div>

          <div class="ad-row">
            <div class="ad-label">Current</div>
            <div class="ad-value">
              <span class="badge {{ $stClass }}">{{ ucfirst($st) }}</span>
            </div>
          </div>

          @if($canManage)
            <div class="ad-status-actions">
              <div class="ad-mini">Quick change:</div>

              <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="pending">
                <button type="submit" class="ad-chip ad-chip-pending" onclick="return confirm('Change status to pending?')">
                  <span class="material-icons-round">hourglass_top</span>
                  Pending
                </button>
              </form>

              <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="ad-chip ad-chip-cancelled"
                  onclick="return confirm('Change status to cancelled?')">
                  <span class="material-icons-round">cancel</span>
                  Cancelled
                </button>
              </form>

              <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <button type="submit" class="ad-chip ad-chip-completed"
                  onclick="return confirm('Change status to completed?')">
                  <span class="material-icons-round">check_circle</span>
                  Completed
                </button>
              </form>
            </div>
          @endif
        </div>

      </div>
    </div>
  </section>
@endsection