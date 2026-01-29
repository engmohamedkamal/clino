@extends('layouts.dash')
@section('dash-content')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('CSS/app.css') }}" />

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['doctor', 'secretary']);
    $isAdmin = auth()->check() && auth()->user()->role === 'secretary';

    $day = request('day');
    $q = request('q');
    $currentStatus = request('status', 'pending');

    $dayCounters = [];

    $groups = $appointments->getCollection()
      ->groupBy(fn($a) => $a->appointment_date ?? 'unknown');

    $colspan = $canManage ? 10 : 8;
  @endphp

  <section class="ap-main">
    <div class="container-fluid ap-container">

      <!-- ===== Header ===== -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <button class="btn icon-btn d-lg-none cash-menu-btn" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar" aria-label="Open menu">
      <i class="fa-solid fa-bars"></i>
    </button>
        <h1 class="ap-title m-0">Appointments</h1>

        <div class="d-flex align-items-center gap-2 flex-wrap">

          @if($isAdmin)
            <button class="ap-ic-btn" type="button" id="editBtn" title="Edit">
              <i class="fa-solid fa-pen"></i>
            </button>

            <form id="bulkDeleteForm" method="POST" action="{{ route('appointments.bulkDestroy') }}" class="d-inline">
              @csrf
              @method('DELETE')
              <button class="ap-ic-btn" type="submit" id="deleteBtn" title="Delete">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
            @endif
            {{-- @if(auth()->user()->role = 'patient' || auth()->user()->role = 'secretary' ) --}}
            <a class="ap-ic-btn ap-ic-primary" href="{{ route('appointment.index') }}" title="Add">
              <i class="fa-solid fa-plus"></i>
            </a>
            {{-- @endif --}}

          <a class="ap-ic-btn"
            href="{{ route('appointments.cards', array_merge(request()->query(), ['view' => 'cards'])) }}"
            title="Card View">
            <i class="fa-solid fa-grip"></i>
          </a>

          {{-- Search --}}
          <form class="ap-search" method="GET" action="{{ route('appointment.show') }}">
            <input type="hidden" name="view" value="table">
            @if($day) <input type="hidden" name="day" value="{{ $day }}"> @endif
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

            <i class="fa-solid fa-magnifying-glass ap-search-ic"></i>
            <input class="form-control ap-search-input" type="search" name="q" value="{{ $q }}"
              placeholder="Search appointments" />
          </form>

          {{-- Date filter --}}
          <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('appointment.show') }}">
            <input type="hidden" name="view" value="table">
            @if($q) <input type="hidden" name="q" value="{{ $q }}"> @endif
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

            <input type="date" name="day" class="form-control" value="{{ $day }}" style="max-width:170px">

            <button class="ap-ic-btn" type="submit" title="Filter">
              <i class="fa-solid fa-filter"></i>
            </button>

            <a class="ap-ic-btn" title="Today" href="{{ route('appointment.show', array_filter([
    'q' => $q,
    'status' => request('status'),
    'day' => now()->toDateString(),
    'view' => 'table'
  ])) }}">
              <i class="fa-regular fa-calendar-days"></i>
            </a>

            <a class="ap-ic-btn" title="Clear" href="{{ route('appointment.show', array_filter([
    'q' => $q,
    'status' => request('status'),
    'view' => 'table'
  ])) }}">
              <i class="fa-solid fa-xmark"></i>
            </a>
          </form>

        </div>
      </div>

      {{-- Alerts --}}
      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
      @endif

      <!-- ===== Status + Pills ===== -->
      <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-3">

        <div class="ap-status-block">
          <div class="ap-status-label">Status</div>

          <form method="GET" action="{{ route('appointment.show') }}">
            <input type="hidden" name="view" value="table">
            @if($q) <input type="hidden" name="q" value="{{ $q }}"> @endif
            @if($day) <input type="hidden" name="day" value="{{ $day }}"> @endif

            <select class="form-select ap-select" name="status" onchange="this.form.submit()">
              <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All</option>
            </select>
          </form>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
          <span class="ap-pill ap-pill-total">
            <i class="fa-solid fa-chart-pie"></i>
            Total
            <span class="ap-pill-count">
              {{ ($pendingCount ?? 0) + ($completedCount ?? 0) + ($cancelledCount ?? 0) }}
            </span>
          </span>
          <span class="ap-pill ap-pill-pending">
            <i class="fa-regular fa-clock"></i>
            Pending
            <span class="ap-pill-count">{{ $pendingCount ?? 0 }}</span>
          </span>

          <span class="ap-pill ap-pill-completed">
            <i class="fa-regular fa-circle-check"></i>
            Completed
            <span class="ap-pill-count">{{ $completedCount ?? 0 }}</span>
          </span>

          <span class="ap-pill ap-pill-cancelled">
            <i class="fa-regular fa-circle-xmark"></i>
            Cancelled
            <span class="ap-pill-count">{{ $cancelledCount ?? 0 }}</span>
          </span>
        </div>

      </div>

      <!-- ===== Table ===== -->
      <section class="ap-table-card">
        <div class="table-responsive">
          <table class="table ap-table align-middle mb-0">

            <thead>
              <tr>
                @if($role == 'secretary')
                  <th style="width:44px;">
                    <input class="form-check-input ap-check" type="checkbox" id="selectAll">
                  </th>
                @endif

                <th >No.</th>
                <th>Patient</th>
                <th class="d-none d-md-table-cell">Phone</th>
                <th class="d-none d-lg-table-cell">Doctor</th>
                <th class="d-none d-lg-table-cell">Visit</th>
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
                @endphp

                <!-- Date Group -->
                <tr class="ap-group">
                  <td colspan="{{ $colspan }}">{{ $dayHeader }}</td>
                </tr>

                @foreach($sorted as $appt)
                  @php
                    $st = $appt->status ?? 'pending';
                    $badgeClass =
                      $st === 'pending' ? 'ap-badge-pending' :
                      ($st === 'completed' ? 'ap-badge-completed' :
                        ($st === 'cancelled' ? 'ap-badge-cancelled' : 'ap-badge-pending'));

                    $dayCounters[$dateKey] = ($dayCounters[$dateKey] ?? 0) + 1;
                    $dayNo = $dayCounters[$dateKey];

                    $visitText = '-';
                    if (is_array($appt->visit_types) && count($appt->visit_types)) {
                      $visitText = collect($appt->visit_types)->pluck('type')->implode(', ');
                    }
                  @endphp

                  <tr class="ap-row">

                    @if($role == 'secretary')
                      <td>
                        <input class="form-check-input ap-check row-check" type="checkbox" value="{{ $appt->id }}" name="ids[]"
                          form="bulkDeleteForm">
                      </td>
                    @endif

                    <td class="ap-td-no">{{ $dayNo }}</td>

                    <!-- Patient + mobile meta -->
                    <td>
                      <div class="ap-td-strong">{{ $appt->patient_name ?? '-' }}</div>

                      <div class="ap-mob-meta d-lg-none mt-1">
                        <div class="ap-mob-item"><span>Phone:</span> {{ $appt->patient_number ?? '-' }}</div>
                        <div class="ap-mob-item"><span>Doctor:</span> {{ $appt->doctor_name ?? '-' }}</div>
                        <div class="ap-mob-item"><span>Visit:</span> {{ $visitText }}</div>
                      </div>
                    </td>

                    <!-- Desktop columns -->
                    <td class="d-none d-md-table-cell ap-td-strong">{{ $appt->patient_number ?? '-' }}</td>
                    <td class="d-none d-lg-table-cell ap-td-strong">{{ $appt->doctor_name ?? '-' }}</td>
                    <td class="d-none d-lg-table-cell ap-td-strong">{{ $visitText }}</td>

                    <td class="ap-td-strong">{{ $appt->appointment_date ?? '-' }}</td>

                    <td>
                      <span class="ap-badge {{ $badgeClass }}">{{ ucfirst($st) }}</span>
                    </td>


              <td class="text-end">
  <div class="d-inline-flex gap-2">

    {{-- Doctor --}}
    @if($role === 'doctor')
      <a class="ap-action"
         href="{{ route('appointments.singleShow', $appt->id) }}"
         title="View">
        <i class="fa-regular fa-eye"></i>
      </a>
    @endif

    {{-- Secretary --}}
    @if($role === 'secretary')
      <a class="ap-action"
         href="{{ route('appointment.reset', $appt->id) }}?no={{ $dayNo }}"
         target="_blank"
         title="Print">
        <i class="fa-solid fa-print"></i>
      </a>

      <a class="ap-action"
         href="{{ route('appointment.vipPrint', $appt->id) }}?no={{ $dayNo }}"
         target="_blank"
         title="Print VIP">
        <i class="fa-solid fa-award"></i>
      </a>
    @endif

  </div>
</td>



                  </tr>
                @endforeach

              @empty
                <tr>
                  <td colspan="{{ $colspan }}" class="text-center py-4">
                    No appointments found.
                  </td>
                </tr>
              @endforelse
            </tbody>

          </table>
        </div>
      </section>

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