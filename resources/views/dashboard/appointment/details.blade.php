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
      <a href="{{ route('day.summary') }}" class="ad-btn ad-btn-primary">
        Day Summary
      </a>
@if(isset($nextAppointment) && $nextAppointment)
  <a href="{{ route('appointments.singleShow', $nextAppointment->id) }}" class="ad-btn ad-btn-soft" title="Next Pending">
    <span class="material-icons-round">arrow_forward</span>
    Next Pending
  </a>
@else
  <button class="ad-btn ad-btn-soft" type="button" disabled title="No next pending appointment">
    <span class="material-icons-round">block</span>
    No Next
  </button>
@endif

        @if(auth()->check() && auth()->user()->role === 'admin')
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

  @php
    
    session(['return_to' => url()->current()]);
  @endphp

  <div class="ad-row">
    <div class="ad-label"></div>
    <div class="ad-value">
      <div class="ad-box-actions d-flex gap-2 flex-wrap">

        {{-- Add Report --}}
        <a href="{{ route('reports.create', [
              'patient_id' => $appointment->patient_id,
              'appointment_id' => $appointment->id
            ]) }}" class="ad-btn ad-btn-soft" title="Add Report">
          <span class="material-icons-round">description</span>
          Report
        </a>

        {{-- Add Prescription --}}
        <a href="{{ route('prescriptions.create', [
              'patient_id' => $appointment->patient_id,
              'appointment_id' => $appointment->id
            ]) }}" class="ad-btn ad-btn-primary" title="Add Prescription">
          <span class="material-icons-round">medication</span>
          Rx
        </a>

        {{-- Add Diagnosis --}}
        <a href="{{ route('diagnoses.create', [
              'patient_id' => $appointment->patient_id,
              'appointment_id' => $appointment->id
            ]) }}" class="ad-btn ad-btn-soft" title="Add Diagnosis">
          <span class="material-icons-round">medical_information</span>
          Diagnosis
        </a>

        {{-- Add Patient Transfer --}}
        <a href="{{ route('patient-transfers.create', [
              'patient_id' => $appointment->patient_id,
              'appointment_id' => $appointment->id
            ]) }}" class="ad-btn ad-btn-warning" title="Patient Transfer">
          <span class="material-icons-round">sync_alt</span>
          Transfer
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
              @endif
            </div>
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
    {{-- ================= Previous Records (for this patient) ================= --}}
{{-- @if($canManage)
  @php
    $hasAny = (isset($reports) && $reports->count())
           || (isset($prescriptions) && $prescriptions->count())
           || (isset($diagnoses) && $diagnoses->count())
           || (isset($transfers) && $transfers->count());
  @endphp

  @if($hasAny)
    <div class="ad-row mt-3">
      <div class="ad-label">Previous</div>
      <div class="ad-value w-100">
        <div class="d-flex flex-wrap gap-2">

          @if(isset($reports) && $reports->count())
            @foreach($reports as $r)
              <a href="{{ route('reports.show', $r->id) }}"
                 class="ad-btn ad-btn-soft" title="Open report #{{ $r->id }}">
                <span class="material-icons-round">article</span>
                Report #{{ $r->id }}
              </a>
            @endforeach
          @endif

     
          @if(isset($prescriptions) && $prescriptions->count())
            
            @foreach($prescriptions as $rx)
              <a href="{{ route('prescriptions.show', $rx->id) }}"
                 class="ad-btn ad-btn-primary" title="Open prescription #{{ $rx->id }}">
                <span class="material-icons-round">receipt_long</span>
                Rx #{{ $rx->id }}
              </a>
            @endforeach
          @endif

          
          @if(isset($diagnoses) && $diagnoses->count())
     

            @foreach($diagnoses as $d)
              <a href="{{ route('diagnoses.show', $d->id) }}"
                 class="ad-btn ad-btn-soft" title="Open diagnosis #{{ $d->id }}">
                <span class="material-icons-round">info</span>
                Dx #{{ $d->id }}
              </a>
            @endforeach
          @endif

     
          @if(isset($transfers) && $transfers->count())
         

            @foreach($transfers as $t)
              <a href="{{ route('patient-transfers.show', $t->id) }}"
                 class="ad-btn ad-btn-warning" title="Open transfer {{ $t->transfer_code ?? ('#'.$t->id) }}">
                <span class="material-icons-round">move_up</span>
                {{ $t->transfer_code ?? ('Transfer #'.$t->id) }}
              </a>
            @endforeach
          @endif

        </div>
      </div>
    </div>
  @endif
@endif --}}
@if($canManage)
  @php
    $hasAny =
        (isset($reports) && $reports->count()) ||
        (isset($prescriptions) && $prescriptions->count()) ||
        (isset($diagnoses) && $diagnoses->count()) ||
        (isset($transfers) && $transfers->count());
  @endphp

  @if($hasAny)
    <div class="ad-row mt-4">
      <div class="ad-label">Previous Records</div>

      <div class="ad-value w-100">
        <div class="row g-3">

          {{-- ================= Reports ================= --}}
          @if(isset($reports) && $reports->count())
            <div class="col-12 col-md-6 col-lg-3">
              <div class="card ad-prev-card h-100">
                <div class="card-header ad-prev-head">
                  <span class="material-icons-round text-primary">article</span>
                  <span>Reports</span>
                </div>

                <div class="card-body p-2">
                  @foreach($reports as $r)
                    <a href="{{ route('reports.show', $r->id) }}"
                       class="ad-prev-item">
                      <span class="material-icons-round">description</span>
                      <span>Report #{{ $r->id }}</span>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          {{-- ================= Prescriptions ================= --}}
          @if(isset($prescriptions) && $prescriptions->count())
            <div class="col-12 col-md-6 col-lg-3">
              <div class="card ad-prev-card h-100">
                <div class="card-header ad-prev-head">
                  <span class="material-icons-round text-success">receipt_long</span>
                  <span>Prescriptions</span>
                </div>

                <div class="card-body p-2">
                  @foreach($prescriptions as $rx)
                    <a href="{{ route('prescriptions.show', $rx->id) }}"
                       class="ad-prev-item">
                      <span class="material-icons-round">medication</span>
                      <span>Rx #{{ $rx->id }}</span>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          {{-- ================= Diagnoses ================= --}}
          @if(isset($diagnoses) && $diagnoses->count())
            <div class="col-12 col-md-6 col-lg-3">
              <div class="card ad-prev-card h-100">
                <div class="card-header ad-prev-head">
                  <span class="material-icons-round text-info">info</span>
                  <span>Diagnoses</span>
                </div>

                <div class="card-body p-2">
                  @foreach($diagnoses as $d)
                    <a href="{{ route('diagnoses.show', $d->id) }}"
                       class="ad-prev-item">
                      <span class="material-icons-round">medical_information</span>
                      <span>Dx #{{ $d->id }}</span>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          {{-- ================= Transfers ================= --}}
          @if(isset($transfers) && $transfers->count())
            <div class="col-12 col-md-6 col-lg-3">
              <div class="card ad-prev-card h-100">
                <div class="card-header ad-prev-head">
                  <span class="material-icons-round text-warning">move_up</span>
                  <span>Transfers</span>
                </div>

                <div class="card-body p-2">
                  @foreach($transfers as $t)
                    <a href="{{ route('patient-transfers.show', $t->id) }}"
                       class="ad-prev-item">
                      <span class="material-icons-round">sync_alt</span>
                      <span>{{ $t->transfer_code ?? ('Transfer #'.$t->id) }}</span>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  @endif
@endif

  </section>
@endsection