@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'doctor']);
    $day = request('day');
    $dayCounters = [];

    $groups = $appointments->getCollection()
  ->groupBy(fn($a) => $a->appointment_date ?? 'unknown');

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

            @forelse($groups as $dateKey => $items)
              @php
                // ✅ Sort inside each day by time
                $sorted = $items->sortBy(function($a){
                  try {
                    return \Carbon\Carbon::parse($a->appointment_time)->format('H:i');
                  } catch (\Exception $e) {
                    // fallback: لو الوقت فاضي/مش قابل للبارس
                    return $a->appointment_time ?? '99:99';
                  }
                });

                // ✅ header label for the day row
                $dayHeader = $dateKey !== 'unknown'
                  ? \Carbon\Carbon::parse($dateKey)->format('D, d M Y')
                  : 'Unknown Date';

                $colspan = $canManage ? 10 : 9;
              @endphp

              {{-- ✅ Day separator row --}}
              <tr class="table-light">
                <td colspan="{{ $colspan }}" class="fw-semibold">
                  {{ $dayHeader }}
                </td>
              </tr>

              @foreach($sorted as $appt)
                @php
                  $st = $appt->status ?? 'pending';
                  $badgeClass =
                    $st === 'pending' ? 'bg-warning text-dark' :
                    ($st === 'cancelled' ? 'bg-danger text-white' :
                    ($st === 'completed' ? 'bg-success text-white' : 'bg-secondary text-white'));

                  // ✅ Per-day counter
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

                  <td class="text-end view-col">
                    <div class="d-inline-flex align-items-center gap-2">

                      <a class="pl-icon-btn"
                         href="{{ route('appointments.singleShow', $appt->id) }}"
                         aria-label="View details"
                         title="View details">
                        <span class="material-icons-round">visibility</span>
                      </a>

                      <a
                    href="{{ route('appointment.reset', $appt->id) }}?no={{ $dayNo }}"

                        class="pl-icon-btn"
                        title="Print receipt"
                        aria-label="Print receipt"
                        target="_blank"
                        onclick="event.stopPropagation();"
                      >
                        <span class="material-icons-round">print</span>
                      </a>

                    </div>
                  </td>
                </tr>
              @endforeach

            @empty
              @php $colspan = $canManage ? 10 : 9; @endphp
              <tr>
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

@endsection
