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
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
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
          <a href="{{ route('appointments.singleShow', $nextAppointment->id) }}" class="ad-btn ad-btn-soft"
            title="Next Pending">
            <span class="material-icons-round">arrow_forward</span>
            Next Pending
          </a>
        @else
          <button class="ad-btn ad-btn-soft" type="button" disabled title="No next pending appointment">
            <span class="material-icons-round">block</span>
            No Next Today
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
  <div class="row g-3">

    <!-- Name -->
    <div class="col-md-6">
      <div class="card ad-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="material-icons-round text-primary fs-3">person</span>
          <div class="flex-grow-1 min-w-0">
            <div class="ad-card-label">Name</div>
            <div class="ad-card-value ad-wrap">{{ $appointment->patient_name ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phone -->
    <div class="col-md-6">
      <div class="card ad-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="material-icons-round text-success fs-3">call</span>
          <div class="flex-grow-1 min-w-0">
            <div class="ad-card-label">Phone</div>
            <div class="ad-card-value ad-wrap">{{ $appointment->patient_number ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- DOB -->
    <div class="col-md-6">
      <div class="card ad-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="material-icons-round text-warning fs-3">calendar_month</span>
          <div class="flex-grow-1 min-w-0">
            <div class="ad-card-label">DOB</div>
            <div class="ad-card-value ad-wrap">
              {{ $appointment->dob ? \Carbon\Carbon::parse($appointment->dob)->format('d/m/Y') : '-' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gender -->
    <div class="col-md-6">
      <div class="card ad-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="material-icons-round text-info fs-3">wc</span>
          <div class="flex-grow-1 min-w-0">
            <div class="ad-card-label">Gender</div>
            <div class="ad-card-value ad-wrap">{{ $appointment->gender ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reason -->
    <div class="col-12">
      <div class="card ad-card h-100">
        <div class="card-body d-flex align-items-start gap-3">
          <span class="material-icons-round text-danger fs-3">help_outline</span>
          <div class="flex-grow-1 min-w-0">
            <div class="ad-card-label">Reason</div>
            <div class="ad-card-value ad-wrap">{{ $appointment->reason ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

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

            <div class="ad-row mt-3">
              <div class="ad-label"></div>

              <div class="ad-value w-100">

                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="fw-semibold">Quick Actions</div>
                </div>

                {{-- ✅ 2 cards per row on ALL screens --}}
                <div class="row g-3 row-cols-2">

                  {{-- Report --}}
                  <div class="col">
                    <a href="{{ route('reports.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}"
                      class="ad-action-card text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center text-center">

                      <div class="ad-card-icon soft mb-3">
                        <span class="material-icons-round">description</span>
                      </div>

                      <div class="ad-card-title">Report</div>
                      <div class="ad-card-sub">Add medical report</div>
                    </a>
                  </div>

                  {{-- Prescription --}}
                  <div class="col">
                    <a href="{{ route('prescriptions.create', ['patient_name' => $appointment->patient_name, 'appointment_id' => $appointment->id]) }}"
                      class="ad-action-card primary text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center text-center">

                      <div class="ad-card-icon primary mb-3">
                        <span class="material-icons-round">medication</span>
                      </div>

                      <div class="ad-card-title">Prescription</div>
                      <div class="ad-card-sub">Write patient Rx</div>
                    </a>
                  </div>

                  {{-- Diagnosis --}}
                  <div class="col">
                    <a href="{{ route('diagnoses.create', ['patient_name' => $appointment->patient_name, 'appointment_id' => $appointment->id]) }}"
                      class="ad-action-card text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center text-center">

                      <div class="ad-card-icon soft mb-3">
                        <span class="material-icons-round">medical_information</span>
                      </div>

                      <div class="ad-card-title">Diagnosis</div>
                      <div class="ad-card-sub">Add diagnosis</div>
                    </a>
                  </div>

                  {{-- Transfer --}}
                  <div class="col">
                    <a href="{{ route('patient-transfers.create', ['patient_name' => $appointment->patient_name, 'appointment_id' => $appointment->id]) }}"
                      class="ad-action-card warning text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center text-center">

                      <div class="ad-card-icon warning mb-3">
                        <span class="material-icons-round">sync_alt</span>
                      </div>

                      <div class="ad-card-title">Transfer</div>
                      <div class="ad-card-sub">Patient transfer</div>
                    </a>
                  </div>

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
                <div class="col-12 col-md-6 col-lg-3" id="reportsWrap">
                  <div class="card ad-prev-card h-100">

                    <div class="card-header ad-prev-head">
                      <span class="material-icons-round text-primary">article</span>
                      <span>Reports</span>
                    </div>

                    <div class="card-body p-2">
                      @foreach($reports as $r)
                        <a href="{{ route('reports.show', $r->id) }}" class="ad-prev-item">
                          <span class="material-icons-round">description</span>
                          <span>Report #{{ $r->exam_type }}</span>
                        </a>
                      @endforeach
                    </div>

                    @if($reports->hasPages())
                      <div class="card-footer py-2">
                        <div class="d-flex justify-content-between">

                          {{-- Prev --}}
                          @if($reports->onFirstPage())
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_left</span>
                            </span>
                          @else
                            <a class="js-ajax-page" href="{{ $reports->appends(['section' => 'reports'])->previousPageUrl() }}">
                              <span class="material-icons-round">chevron_left</span>
                            </a>
                          @endif

                          {{-- Next --}}
                          @if($reports->hasMorePages())
                            <a class="js-ajax-page" href="{{ $reports->appends(['section' => 'reports'])->nextPageUrl() }}">
                              <span class="material-icons-round">chevron_right</span>
                            </a>
                          @else
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_right</span>
                            </span>
                          @endif

                        </div>
                      </div>
                    @endif

                  </div>
                </div>
              @endif


              @if(isset($prescriptions) && $prescriptions->count())
                <div class="col-12 col-md-6 col-lg-3" id="rxWrap">
                  <div class="card ad-prev-card h-100">

                    <div class="card-header ad-prev-head">
                      <span class="material-icons-round text-success">receipt_long</span>
                      <span>Prescriptions</span>
                    </div>

                    <div class="card-body p-2">
                      @foreach($prescriptions as $rx)
                        <a href="{{ route('prescriptions.show', $rx->id) }}" class="ad-prev-item">
                          <span class="material-icons-round">medication</span>
                          <span>Rx #{{ $rx->id }}</span>
                        </a>
                      @endforeach
                    </div>

                    @if($prescriptions->hasPages())
                      <div class="card-footer py-2">
                        <div class="d-flex justify-content-between">

                          {{-- Prev --}}
                          @if($prescriptions->onFirstPage())
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_left</span>
                            </span>
                          @else
                            <a class="js-ajax-page" href="{{ $prescriptions->appends(['section' => 'rx'])->previousPageUrl() }}">
                              <span class="material-icons-round">chevron_left</span>
                            </a>
                          @endif

                          {{-- Next --}}
                          @if($prescriptions->hasMorePages())
                            <a class="js-ajax-page" href="{{ $prescriptions->appends(['section' => 'rx'])->nextPageUrl() }}">
                              <span class="material-icons-round">chevron_right</span>
                            </a>
                          @else
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_right</span>
                            </span>
                          @endif

                        </div>
                      </div>
                    @endif

                  </div>
                </div>
              @endif


              {{-- ================= Diagnoses ================= --}}
              @if(isset($diagnoses) && $diagnoses->count())
                <div class="col-12 col-md-6 col-lg-3" id="dxWrap">
                  <div class="card ad-prev-card h-100">

                    <div class="card-header ad-prev-head">
                      <span class="material-icons-round text-info">info</span>
                      <span>Diagnoses</span>
                    </div>

                    <div class="card-body p-2">
                      @foreach($diagnoses as $d)
                        <a href="{{ route('diagnoses.show', $d->id) }}" class="ad-prev-item">
                          <span class="material-icons-round">medical_information</span>
                          <span>Dx #{{ $d->id }}</span>
                        </a>
                      @endforeach
                    </div>

                    @if($diagnoses->hasPages())
                      <div class="card-footer py-2">
                        <div class="d-flex justify-content-between">

                          {{-- Prev --}}
                          @if($diagnoses->onFirstPage())
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_left</span>
                            </span>
                          @else
                            <a class="js-ajax-page" href="{{ $diagnoses->appends(['section' => 'dx'])->previousPageUrl() }}">
                              <span class="material-icons-round">chevron_left</span>
                            </a>
                          @endif

                          {{-- Next --}}
                          @if($diagnoses->hasMorePages())
                            <a class="js-ajax-page" href="{{ $diagnoses->appends(['section' => 'dx'])->nextPageUrl() }}">
                              <span class="material-icons-round">chevron_right</span>
                            </a>
                          @else
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_right</span>
                            </span>
                          @endif

                        </div>
                      </div>
                    @endif

                  </div>
                </div>
              @endif

              {{-- ================= Transfers ================= --}}
              @if(isset($transfers) && $transfers->count())
                <div class="col-12 col-md-6 col-lg-3" id="trWrap">
                  <div class="card ad-prev-card h-100">

                    <div class="card-header ad-prev-head">
                      <span class="material-icons-round text-warning">move_up</span>
                      <span>Transfers</span>
                    </div>

                    <div class="card-body p-2">
                      @foreach($transfers as $t)
                        <a href="{{ route('patient-transfers.show', $t->id) }}" class="ad-prev-item">
                          <span class="material-icons-round">sync_alt</span>
                          <span>{{ $t->transfer_code ?? ('Transfer #' . $t->id) }}</span>
                        </a>
                      @endforeach
                    </div>

                    @if($transfers->hasPages())
                      <div class="card-footer py-2">
                        <div class="d-flex justify-content-between">

                          {{-- Prev --}}
                          @if($transfers->onFirstPage())
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_left</span>
                            </span>
                          @else
                            <a class="js-ajax-page" href="{{ $transfers->appends(['section' => 'tr'])->previousPageUrl() }}">
                              <span class="material-icons-round">chevron_left</span>
                            </a>
                          @endif

                          {{-- Next --}}
                          @if($transfers->hasMorePages())
                            <a class="js-ajax-page" href="{{ $transfers->appends(['section' => 'tr'])->nextPageUrl() }}">
                              <span class="material-icons-round">chevron_right</span>
                            </a>
                          @else
                            <span class="text-muted">
                              <span class="material-icons-round">chevron_right</span>
                            </span>
                          @endif

                        </div>
                      </div>
                    @endif

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