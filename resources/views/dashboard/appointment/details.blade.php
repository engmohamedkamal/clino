@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('css/appointmentDetails.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'doctor']);

    $st = $appointment->status ?? 'pending';
    $stClass =
      $st === 'pending' ? 'bg-warning text-dark' :
      ($st === 'cancelled' ? 'bg-danger text-white' :
        ($st === 'completed' ? 'bg-success text-white' : 'bg-secondary text-white'));

    // حفظ return_to للرجوع بعد create/edit
    if ($canManage) {
      session(['return_to' => url()->current()]);
    }
  @endphp

  <section class="ad-main">
    <div class="container">

      <!-- ================= Topbar ================= -->
      <div class="ad-topbar">

        <div class="d-flex align-items-center gap-2 d-lg-none">
          <button class="btn icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
        </div>

        <div class="ad-left">
          <a href="{{ route('appointment.show') }}" class="ad-icon-btn" title="Back" aria-label="Back">
            <span class="material-icons-round">arrow_back</span>
          </a>

          <div>
            <h2 class="ad-title">Appointment Details</h2>
            <div class="ad-sub">Full information about this booking</div>
          </div>
        </div>

        <div class="ad-actions">
          <span class="ad-status-btn {{ $stClass }}">{{ ucfirst($st) }}</span>

          <a href="{{ route('day.summary') }}" class="ad-btn ad-btn-primary">Day Summary</a>

          @if(isset($nextAppointment) && $nextAppointment)
            <a href="{{ route('appointments.singleShow', $nextAppointment->id) }}" class="ad-btn ad-btn-soft"
              title="Next Pending">
              Next Pending
            </a>
          @else
            <a href="#" class="ad-btn ad-btn-soft disabled" aria-disabled="true" tabindex="-1"
              title="No next pending appointment">
              No Next Today
            </a>
          @endif

          @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ url('/appointments') . '/' . $appointment->id . '/edit' }}" class="ad-icon-btn" title="Edit"
              aria-label="Edit">
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

      <!-- ================= Panel ================= -->
      <div class="ad-panel">
        <div class="row g-2 align-items-start">

          <!-- ================= LEFT COLUMN ================= -->
          <div class="col-12 col-lg-7 d-flex flex-column gap-2">

            <!-- ===== Patient ===== -->
            <div class="ad-box">
              <div class="row g-2">

                <div class="col-md-6">
                  <div class="ad-info-card">
                    <div class="ad-info-body">
                      <span class="material-icons-round text-primary">person</span>
                      <div class="ad-flex-grow">
                        <div class="ad-card-label">Name</div>
                        <div class="ad-card-value">{{ $appointment->patient_name ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="ad-info-card">
                    <div class="ad-info-body">
                      <span class="material-icons-round text-success">call</span>
                      <div class="ad-flex-grow">
                        <div class="ad-card-label">Phone</div>
                        <div class="ad-card-value">{{ $appointment->patient_number ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="ad-info-card">
                    <div class="ad-info-body">
                      <span class="material-icons-round text-warning">calendar_month</span>
                      <div class="ad-flex-grow">
                        <div class="ad-card-label">DOB</div>
                        <div class="ad-card-value">
                          {{ $appointment->dob ? \Carbon\Carbon::parse($appointment->dob)->format('d/m/Y') : '-' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="ad-info-card">
                    <div class="ad-info-body">
                      <span class="material-icons-round text-info">wc</span>
                      <div class="ad-flex-grow">
                        <div class="ad-card-label">Gender</div>
                        <div class="ad-card-value">{{ $appointment->gender ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="ad-info-card">
                    <div class="ad-info-body top">
                      <span class="material-icons-round text-danger">help_outline</span>
                      <div class="ad-flex-grow">
                        <div class="ad-card-label">Reason</div>
                        <div class="ad-card-value">{{ $appointment->reason ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex gap-3 flex-wrap mt-3 justify-content-evenly ">

                  {{-- Emergency Card --}}
                  @if($appointment->emergency)
                    <div class="flag-card flag-card-emergency">
                      <div class="flag-card-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                      </div>

                      <div class="flag-card-body">
                        <div class="flag-card-title">Emergency</div>
                        <div class="flag-card-sub">High priority case</div>
                      </div>
                    </div>
                  @endif

                  {{-- VIP Card --}}
                  @if($appointment->vip)
                    <div class="flag-card flag-card-vip">
                      <div class="flag-card-icon">
                        <i class="fa-solid fa-crown"></i>
                      </div>

                      <div class="flag-card-body">
                        <div class="flag-card-title">VIP</div>
                        <div class="flag-card-sub">Special care patient</div>
                      </div>
                    </div>
                  @endif

                </div>



              </div>
            </div>

            <!-- ===== Quick Actions ===== -->
            @if($canManage)
              <div class="ad-box">
                <div class="ad-box-title">
                  <span class="material-icons-round">bolt</span>
                  <span>Quick Actions</span>
                </div>

                <div class="row g-2 row-cols-2">

                  <div class="col">
                    <a href="{{ route('reports.create', ['patient_name' => $appointment->patient_name, 'patient_phone' => $appointment->patient_number]) }}"
                      class="ad-action-card text-decoration-none">
                      <div class="ad-card-icon soft">
                        <span class="material-icons-round">description</span>
                      </div>
                      <div class="ad-card-title">Report</div>
                      <div class="ad-card-sub">Add medical report</div>
                    </a>
                  </div>

                  <div class="col">
                    <a href="{{ route('prescriptions.create', ['patient_name' => $appointment->patient_name, 'appointment_id' => $appointment->id]) }}"
                      class="ad-action-card primary text-decoration-none">
                      <div class="ad-card-icon primary">
                        <span class="material-icons-round">medication</span>
                      </div>
                      <div class="ad-card-title">Prescription</div>
                      <div class="ad-card-sub">Write patient Rx</div>
                    </a>
                  </div>

                  <div class="col">
                    <a href="{{ route('diagnoses.create', ['patient_name' => $appointment->patient_name, 'patient_phone' => $appointment->patient_number]) }}"
                      class="ad-action-card text-decoration-none">
                      <div class="ad-card-icon soft">
                        <span class="material-icons-round">medical_information</span>
                      </div>
                      <div class="ad-card-title">Diagnosis</div>
                      <div class="ad-card-sub">Add diagnosis</div>
                    </a>
                  </div>

                  <div class="col">
                    <a href="{{ route('patient-transfers.create', ['patient_name' => $appointment->patient_name, 'patient_phone' => $appointment->patient_number]) }}"
                      class="ad-action-card warning text-decoration-none">
                      <div class="ad-card-icon warning">
                        <span class="material-icons-round">sync_alt</span>
                      </div>
                      <div class="ad-card-title">Transfer</div>
                      <div class="ad-card-sub">Patient transfer</div>
                    </a>
                  </div>

                </div>
              </div>
            @endif

          </div>

          <!-- ================= RIGHT COLUMN ================= -->
          <div class="col-12 col-lg-5 d-flex flex-column gap-2">

            <!-- Date & Time -->
            <div class="ad-box">
              <div class="ad-box-title">
                <span class="material-icons-round">event</span>
                <span>Date & Time</span>
              </div>

              <div class="ad-row"><span>Date</span><span>{{ $appointment->appointment_date ?? '-' }}</span></div>
              <div class="ad-row"><span>Time</span><span>{{ $appointment->appointment_time ?? '-' }}</span></div>

              <div class="ad-row">
                <span>Visit Type</span>
                <span>
                  @if(is_array($appointment->visit_types) && count($appointment->visit_types))
                    {{ collect($appointment->visit_types)->pluck('type')->implode(' , ') }}
                  @else
                    -
                  @endif
                </span>
              </div>

              <div class="ad-row">
                <span>Created</span><span>{{ optional($appointment->created_at)->format('d/m/Y - h:i A') ?? '-' }}</span>
              </div>
            </div>

            <!-- Previous Records -->
            @if($canManage)
              @php
                $hasAny =
                  (isset($reports) && $reports->count()) ||
                  (isset($prescriptions) && $prescriptions->count()) ||
                  (isset($diagnoses) && $diagnoses->count()) ||
                  (isset($transfers) && $transfers->count());
              @endphp

              @if($hasAny)
                <div class="ad-box">
                  <div class="ad-box-title">
                    <span class="material-icons-round">history</span>
                    <span>Previous Records</span>
                  </div>

                  {{-- Reports --}}
                  @if(isset($reports) && $reports->count())
                    <div class="ad-accordion" id="reportsAcc">
                      <button class="ad-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#reportsBody"
                        aria-expanded="true" aria-controls="reportsBody">
                        <span>📄 Reports</span>
                        <span class="material-icons-round">expand_more</span>
                      </button>

                      <div id="reportsBody" class="collapse show ad-accordion-body">
                        @foreach($reports as $r)
                          <a href="{{ route('reports.show', $r->id) }}" class="ad-prev-link">
                            Report #{{ $r->exam_type }}
                          </a>
                        @endforeach

                        @if($reports->hasPages())
                          <div class="d-flex justify-content-between mt-2">
                            @if($reports->onFirstPage())
                              <span class="text-muted"><span class="material-icons-round">chevron_left</span></span>
                            @else
                              <a class="js-ajax-page" href="{{ $reports->appends(['section' => 'reports'])->previousPageUrl() }}">
                                <span class="material-icons-round">chevron_left</span>
                              </a>
                            @endif

                            @if($reports->hasMorePages())
                              <a class="js-ajax-page" href="{{ $reports->appends(['section' => 'reports'])->nextPageUrl() }}">
                                <span class="material-icons-round">chevron_right</span>
                              </a>
                            @else
                              <span class="text-muted"><span class="material-icons-round">chevron_right</span></span>
                            @endif
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif

                  {{-- Prescriptions --}}
                  @if(isset($prescriptions) && $prescriptions->count())
                    <div class="ad-accordion" id="rxAcc">
                      <button class="ad-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#rxBody"
                        aria-expanded="false" aria-controls="rxBody">
                        <span>💊 Prescriptions</span>
                        <span class="material-icons-round">expand_more</span>
                      </button>

                      <div id="rxBody" class="collapse ad-accordion-body">
                        @foreach($prescriptions as $rx)
                          <a href="{{ route('prescriptions.show', $rx->id) }}" class="ad-prev-link">
                            Rx #{{ $rx->id }}
                          </a>
                        @endforeach

                        @if($prescriptions->hasPages())
                          <div class="d-flex justify-content-between mt-2">
                            @if($prescriptions->onFirstPage())
                              <span class="text-muted"><span class="material-icons-round">chevron_left</span></span>
                            @else
                              <a class="js-ajax-page" href="{{ $prescriptions->appends(['section' => 'rx'])->previousPageUrl() }}">
                                <span class="material-icons-round">chevron_left</span>
                              </a>
                            @endif

                            @if($prescriptions->hasMorePages())
                              <a class="js-ajax-page" href="{{ $prescriptions->appends(['section' => 'rx'])->nextPageUrl() }}">
                                <span class="material-icons-round">chevron_right</span>
                              </a>
                            @else
                              <span class="text-muted"><span class="material-icons-round">chevron_right</span></span>
                            @endif
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif

                  {{-- Diagnoses --}}
                  @if(isset($diagnoses) && $diagnoses->count())
                    <div class="ad-accordion" id="dxAcc">
                      <button class="ad-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#dxBody"
                        aria-expanded="false" aria-controls="dxBody">
                        <span>🩺 Diagnoses</span>
                        <span class="material-icons-round">expand_more</span>
                      </button>

                      <div id="dxBody" class="collapse ad-accordion-body">
                        @foreach($diagnoses as $d)
                          <a href="{{ route('diagnoses.show', $d->id) }}" class="ad-prev-link">
                            Dx #{{ $d->id }}
                          </a>
                        @endforeach

                        @if($diagnoses->hasPages())
                          <div class="d-flex justify-content-between mt-2">
                            @if($diagnoses->onFirstPage())
                              <span class="text-muted"><span class="material-icons-round">chevron_left</span></span>
                            @else
                              <a class="js-ajax-page" href="{{ $diagnoses->appends(['section' => 'dx'])->previousPageUrl() }}">
                                <span class="material-icons-round">chevron_left</span>
                              </a>
                            @endif

                            @if($diagnoses->hasMorePages())
                              <a class="js-ajax-page" href="{{ $diagnoses->appends(['section' => 'dx'])->nextPageUrl() }}">
                                <span class="material-icons-round">chevron_right</span>
                              </a>
                            @else
                              <span class="text-muted"><span class="material-icons-round">chevron_right</span></span>
                            @endif
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif

                  {{-- Transfers --}}
                  @if(isset($transfers) && $transfers->count())
                    <div class="ad-accordion" id="trAcc">
                      <button class="ad-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#trBody"
                        aria-expanded="false" aria-controls="trBody">
                        <span>🔁 Transfers</span>
                        <span class="material-icons-round">expand_more</span>
                      </button>

                      <div id="trBody" class="collapse ad-accordion-body">
                        @foreach($transfers as $t)
                          <a href="{{ route('patient-transfers.show', $t->id) }}" class="ad-prev-link">
                            {{ $t->transfer_code ?? ('Transfer #' . $t->id) }}
                          </a>
                        @endforeach

                        @if($transfers->hasPages())
                          <div class="d-flex justify-content-between mt-2">
                            @if($transfers->onFirstPage())
                              <span class="text-muted"><span class="material-icons-round">chevron_left</span></span>
                            @else
                              <a class="js-ajax-page" href="{{ $transfers->appends(['section' => 'tr'])->previousPageUrl() }}">
                                <span class="material-icons-round">chevron_left</span>
                              </a>
                            @endif

                            @if($transfers->hasMorePages())
                              <a class="js-ajax-page" href="{{ $transfers->appends(['section' => 'tr'])->nextPageUrl() }}">
                                <span class="material-icons-round">chevron_right</span>
                              </a>
                            @else
                              <span class="text-muted"><span class="material-icons-round">chevron_right</span></span>
                            @endif
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif

                </div>
              @endif
            @endif

            <!-- Status -->
            <div class="ad-box">
              <div class="ad-box-title">
                <span class="material-icons-round">verified</span>
                <span>Status</span>
              </div>

              <div class="ad-row ad-status-row">
                <span class="ad-label">Current</span>

                <div class="ad-status-buttons">
                  @if($canManage)
                    <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}"
                      class="d-inline">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="status" value="pending">
                      <button type="submit" class="ad-status-btn pending"
                        onclick="return confirm('Change status to pending?')">
                        Pending
                      </button>
                    </form>

                    <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}"
                      class="d-inline">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="status" value="cancelled">
                      <button type="submit" class="ad-status-btn cancelled"
                        onclick="return confirm('Change status to cancelled?')">
                        Cancelled
                      </button>
                    </form>

                    <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}"
                      class="d-inline">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="status" value="completed">
                      <button type="submit" class="ad-status-btn completed"
                        onclick="return confirm('Change status to completed?')">
                        Completed
                      </button>
                    </form>
                  @else

                    <span class="badge {{ $stClass }}">{{ ucfirst($st) }}</span>
                  @endif
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- نفس فكرة js-ajax-page لو عندك ajax pagination --}}
  <script>
    // لو انت عندك AJAX pagination قديم، سيبه زي ما هو في ملفك
    // هنا مجرد placeholder بسيط (اختياري)
    document.querySelectorAll('.js-ajax-page').forEach(a => {
      a.addEventListener('click', function (e) {
        // لو شغال full page reload، سيبها بدون منع
        // e.preventDefault();
      });
    });
  </script>
@endsection