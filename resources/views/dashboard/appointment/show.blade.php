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

      {{-- Left/Top actions group --}}
      <div class="pl-action-group d-flex align-items-center gap-2 flex-wrap">

        @if(auth()->check() && auth()->user()->role === 'admin')
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

        {{-- Card View (يحافظ على نفس الفلاتر + يضيف view=cards) --}}
        <a href="{{ route('appointments.cards', array_merge(request()->query(), ['view' => 'cards'])) }}"
           class="pl-icon-btn"
           aria-label="Card View"
           title="Card View">
          <span class="material-icons-round">view_module</span>
        </a>

        {{-- Filters --}}
        <form class="pl-filter d-flex align-items-center gap-2" method="GET" action="{{ route('appointment.show') }}">
          {{-- ✅ يخلي أي submit من هنا يرجع Table View --}}
          <input type="hidden" name="view" value="table">

          @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
          @endif
          @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
          @endif

          <input type="date" name="day" class="form-control form-control-sm" value="{{ $day }}">

          <button class="pl-icon-btn" type="submit" aria-label="Filter">
            <span class="material-icons-round">filter_alt</span>
          </button>

          <a class="pl-icon-btn" aria-label="Today"
             href="{{ route('appointment.show', array_filter([
               'q' => request('q'),
               'status' => request('status'),
               'day' => now()->toDateString(),
               'view' => 'table'
             ])) }}">
            <span class="material-icons-round">today</span>
          </a>

          <a class="pl-icon-btn" aria-label="Clear"
             href="{{ route('appointment.show', array_filter([
               'q' => request('q'),
               'status' => request('status'),
               'view' => 'table'
             ])) }}">
            <span class="material-icons-round">close</span>
          </a>
        </form>

      </div>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('appointment.show') }}">
        {{-- ✅ يخلي أي submit من هنا يرجع Table View --}}
        <input type="hidden" name="view" value="table">

        <span class="material-icons-round">search</span>
        @if($day)
          <input type="hidden" name="day" value="{{ $day }}">
        @endif
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <input name="q" type="text" value="{{ request('q') }}" placeholder="Search appointments">
      </form>

    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
  @endif

  @php
    $currentStatus = request('status', 'pending');
  @endphp

  <form method="GET" action="{{ route('appointment.show') }}" class="d-inline-flex align-items-center gap-2 mb-3">
    {{-- ✅ يخلي أي submit من هنا يرجع Table View --}}
    <input type="hidden" name="view" value="table">

    {{-- حافظي على باقي الفلاتر --}}
    @if(request('q'))
      <input type="hidden" name="q" value="{{ request('q') }}">
    @endif
    @if(request('day'))
      <input type="hidden" name="day" value="{{ request('day') }}">
    @endif

    <label class="form-label mb-0 fw-semibold">Status</label>

    <select name="status" class="form-select form-select-sm" style="min-width:160px" onchange="this.form.submit()">
      <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
      <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Completed</option>
      <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All</option>
    </select>
  </form>

  {{-- ================= Table ================= --}}
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
            <th>Status</th>

            @if($canManage)
              <th class="text-end">Actions</th>
            @endif
          </tr>
        </thead>

        <tbody>
          @forelse($groups as $dateKey => $items)

            @php
              $sorted = $items->sortBy(fn($a) => $a->appointment_time ?? '99:99');
              $dayHeader = $dateKey !== 'unknown'
                ? \Carbon\Carbon::parse($dateKey)->format('D, d M Y')
                : 'Unknown Date';

              $colspan = $canManage ? 10 : 9;
            @endphp

            {{-- Day Header --}}
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

                $dayCounters[$dateKey] = ($dayCounters[$dateKey] ?? 0) + 1;
                $dayNo = $dayCounters[$dateKey];
              @endphp

              <tr>
                @if($canManage)
                  <td>
                    <input class="form-check-input pl-check row-check" type="checkbox" value="{{ $appt->id }}" name="ids[]"
                      form="bulkDeleteForm">
                  </td>
                @endif

                <td class="fw-semibold">{{ $dayNo }}</td>
                <td>{{ $appt->patient_name ?? '-' }}</td>
                <td>{{ $appt->patient_number ?? '-' }}</td>
                <td>{{ $appt->doctor_name ?? '-' }}</td>
                <td>
                  @if(is_array($appt->visit_types) && count($appt->visit_types))
                    {{ collect($appt->visit_types)->pluck('type')->implode(', ') }}
                  @else
                    -
                  @endif
                </td>
                <td>{{ $appt->appointment_date ?? '-' }}</td>
                <td>
                  <span class="badge {{ $badgeClass }}">{{ ucfirst($st) }}</span>
                </td>

                @if($canManage)
                  <td class="text-end">
                    <div class="d-inline-flex gap-2">

                      {{-- View --}}
                      <a class="pl-icon-btn" href="{{ route('appointments.singleShow', $appt->id) }}">
                        <span class="material-icons-round">visibility</span>
                      </a>

                      @if(auth()->check() && auth()->user()->role === 'admin')
                        <a class="pl-icon-btn"
                           href="{{ route('appointment.reset', $appt->id) }}?no={{ $dayNo }}"
                           target="_blank">
                          <span class="material-icons-round">print</span>
                        </a>

                        <a class="pl-icon-btn vip"
                           href="{{ route('appointment.vipPrint', $appt->id) }}?no={{ $dayNo }}"
                           target="_blank"
                           title="Print VIP Ticket">
                          <span class="material-icons-round">workspace_premium</span>
                        </a>
                      @endif

                    </div>
                  </td>
                @endif

              </tr>
            @endforeach

          @empty
            <tr>
              <td colspan="{{ $canManage ? 10 : 9 }}" class="text-center py-4">
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
      document.querySelectorAll('.row-check').forEach(cb => cb.checked = selectAll.checked);
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
      if (getCheckedIds().length === 0) {
        e.preventDefault();
        alert('اختار ميعاد/مواعيد الأول');
      }
    });
  </script>
@endif

@endsection
