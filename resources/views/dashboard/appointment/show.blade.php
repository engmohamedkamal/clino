@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'doctor']);
    $day = request('day');

    // ✅ Counters per day (appointment_date)
    $dayCounters = [];
      $vts = $appointments->visit_types;
  $vts = is_array($vts) ? $vts : (json_decode($vts, true) ?: []);
  @endphp

  <section class="pl-main container">
    <div class="pl-topbar">
      <h2 class="pl-title">Appointments List</h2>

      <div class="pl-actions">

        @if($canManage)
          <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
            <span class="material-icons-round">edit</span>
          </button>

          <form id="bulkDeleteForm" method="POST" action="{{ route('appointments.bulkDestroy') }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
              <span class="material-icons-round">delete</span>
            </button>
          </form>
        @endif

        <a href="{{ route('appointment.index') }}" class="pl-icon-btn primary" aria-label="Add">
          <span class="material-icons-round">add</span>
        </a>

        <form class="pl-filter d-flex align-items-center gap-2" method="GET" action="{{ route('appointment.show') }}">
          @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
          @endif

          <input type="date" name="day" class="form-control form-control-sm" value="{{ $day }}" aria-label="Filter by day">

          <button class="pl-icon-btn" type="submit" aria-label="Apply day filter" title="Filter">
            <span class="material-icons-round">filter_alt</span>
          </button>

          <a class="pl-icon-btn" aria-label="Today" title="Today"
             href="{{ route('appointment.show', array_filter(['q' => request('q'), 'day' => now()->toDateString()])) }}">
            <span class="material-icons-round">today</span>
          </a>

          <a class="pl-icon-btn" aria-label="Clear filter" title="Clear"
             href="{{ route('appointment.show', array_filter(['q' => request('q')])) }}">
            <span class="material-icons-round">close</span>
          </a>
        </form>

        <form class="pl-search" method="GET" action="{{ route('appointment.show') }}">
          <span class="material-icons-round">search</span>
          @if($day)
            <input type="hidden" name="day" value="{{ $day }}">
          @endif
          <input id="searchInput" name="q" type="text" value="{{ request('q') }}" placeholder="Search appointments">
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
    @endif

    <div class="pl-table-card">
      <div class="table-responsive">
        <table class="table pl-table mb-0 align-middle">
          <thead>
            <tr>
              @if($canManage)
                <th class="check-col">
                  <input class="form-check-input pl-check" type="checkbox" id="selectAll">
                </th>
              @endif

              {{-- ✅ New: Number column --}}
              <th style="width:72px;">No.</th>

              <th>Patient</th>
              <th>Phone</th>
              <th>Doctor</th>
              <th>Visit Type</th>
              <th>Date</th>
              <th>DOB</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>

          <tbody>
            @forelse($appointments as $appt)
              @php
                $st = $appt->status ?? 'pending';
                $badgeClass =
                  $st === 'pending' ? 'bg-warning text-dark' :
                  ($st === 'cancelled' ? 'bg-danger text-white' :
                  ($st === 'completed' ? 'bg-success text-white' : 'bg-secondary text-white'));

                // ✅ Per-day counter
                $dateKey = $appt->appointment_date ?? 'unknown';
                $dayCounters[$dateKey] = ($dayCounters[$dateKey] ?? 0) + 1;
                $dayNo = $dayCounters[$dateKey];
              @endphp

              <tr data-id="{{ $appt->id }}">
                @if($canManage)
                  <td>
                    <input class="form-check-input pl-check row-check"
                           type="checkbox"
                           name="ids[]"
                           value="{{ $appt->id }}"
                           form="bulkDeleteForm">
                  </td>
                @endif

                {{-- ✅ Number --}}
                <td class="fw-semibold">{{ $dayNo }}</td>

                <td>{{ $appt->patient_name ?? '-' }}</td>
                <td>{{ $appt->patient_number ?? '-' }}</td>
                <td>{{ $appt->doctor_name ?? '-' }}</td>

                <td>
                  @if(is_array($appt->visit_types) && count($appt->visit_types))
                    {{ collect($appt->visit_types)->pluck('type')->implode(' , ') }}
                  @else
                    -
                  @endif
                </td>

                <td>{{ $appt->appointment_date ?? '-' }}</td>
                <td>{{ $appt->dob ? \Carbon\Carbon::parse($appt->dob)->format('d/m/Y') : '-' }}</td>

                <td class="status-col">
                  <span class="badge {{ $badgeClass }}">{{ ucfirst($st) }}</span>
                </td>

                {{-- ✅ Actions: View + Print Receipt --}}
                <td class="text-end view-col">
                  <div class="d-inline-flex align-items-center gap-2">

                    <a class="pl-icon-btn"
                       href="{{ route('appointments.singleShow', $appt->id) }}"
                       aria-label="View details"
                       title="View details">
                      <span class="material-icons-round">visibility</span>
                    </a>

                  <button
  type="button"
  class="pl-icon-btn"
  title="Print receipt"
  aria-label="Print receipt"
  onclick="event.preventDefault(); event.stopPropagation(); printReceipt(
    {{ $dayNo }},
    @json($appt->appointment_date),
    @json($appt->patient_name),
    @json($appt->doctor_name),
    @json($appt->visit_types)
  )"
>
  <span class="material-icons-round">print</span>
</button>

                  </div>
                </td>
              </tr>

            @empty
              <tr>
                @php
                  // columns count:
                  // canManage => checkbox + No + Patient + Phone + Doctor + VisitType + Date + DOB + Status + Actions = 10
                  // else      => No + Patient + Phone + Doctor + VisitType + Date + DOB + Status + Actions = 9
                  $colspan = $canManage ? 10 : 9;
                @endphp
                <td colspan="{{ $colspan }}" class="text-center py-4">
                  No appointments found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4 custom-pagination">
        {{ $appointments->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </section>

  @if($canManage)
    <script>
      const selectAll = document.getElementById('selectAll');
      const editBtn = document.getElementById('editBtn');
      const bulkForm = document.getElementById('bulkDeleteForm');

      function getCheckedIds() {
        return [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
      }

      selectAll?.addEventListener('change', () => {
        const checked = selectAll.checked;
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
      });

      editBtn?.addEventListener('click', () => {
        const ids = getCheckedIds();
        if (ids.length !== 1) {
          alert('اختار ميعاد واحد بس عشان تعمل Edit');
          return;
        }
        window.location.href = "{{ url('/appointments') }}/" + ids[0] + "/edit";
      });

      bulkForm?.addEventListener('submit', (e) => {
        const ids = getCheckedIds();
        if (ids.length === 0) {
          e.preventDefault();
          alert('اختار ميعاد/مواعيد الأول عشان تعمل Delete');
          return;
        }
        if (!confirm('Delete selected appointments?')) {
          e.preventDefault();
        }
      });
    </script>
  @endif

 
  <script>
  window.printReceipt = function(no, date, patient, doctor, visitTypes) {
    console.log('printReceipt fired', { no, date, patient, doctor, visitTypes });

    const visitTypeText = (Array.isArray(visitTypes) && visitTypes.length)
      ? visitTypes.map(v => v?.type).filter(Boolean).join(' , ')
      : '-';

    const html = `
      <!doctype html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>Receipt</title>
        <style>
          body{ font-family: Arial, sans-serif; padding:16px; }
          .card{ border:1px solid #e5e7eb; border-radius:12px; padding:14px; max-width:360px; margin:0 auto; }
          .title{ font-size:16px; font-weight:700; margin-bottom:8px; text-align:center; }
          .no{ font-size:44px; font-weight:800; text-align:center; margin:10px 0; }
          .row{ display:flex; justify-content:space-between; gap:10px; margin:6px 0; font-size:13px; }
          .muted{ color:#6b7280; }
          .hr{ height:1px; background:#e5e7eb; margin:10px 0; }
        </style>
      </head>
      <body onload="window.print()">
        <div class="card">
          <div class="title">Helper Clinic - Appointment Receipt</div>
          <div class="hr"></div>
          <div class="muted" style="text-align:center;">Queue No.</div>
          <div class="no">${no ?? '-'}</div>
          <div class="hr"></div>
          <div class="row"><div class="muted">Date</div><div>${date || '-'}</div></div>
          <div class="row"><div class="muted">Patient</div><div>${patient || '-'}</div></div>
          <div class="row"><div class="muted">Doctor</div><div>${doctor || '-'}</div></div>
          <div class="row"><div class="muted">Visit Type</div><div>${visitTypeText}</div></div>
        </div>
      </body>
      </html>
    `;

    const w = window.open('', '_blank', 'width=450,height=650');
    if (!w) {
      alert('Popup blocked! Allow popups for this site to print.');
      return;
    }
    w.document.open();
    w.document.write(html);
    w.document.close();
  }
</script>

@endsection
