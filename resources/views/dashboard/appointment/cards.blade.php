@extends('layouts.dash')
@section('dash-content')

  <link rel="stylesheet" href="{{ asset('css/appointments.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/patientList.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['secretary', 'doctor']);
    $day = request('day');
    $dayCounters = [];

    $groups = $appointments->getCollection()
      ->groupBy(fn($a) => $a->appointment_date ?? 'unknown');
  @endphp

  <section class="pl-main container">

    {{-- ================= Topbar ================= --}}
    <div class="pl-topbar">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <h2 class="pl-title">App List</h2>

      <div class="pl-actions">
        <div class="pl-action-group d-flex align-items-center gap-2 flex-wrap">

          @if(auth()->check() && auth()->user()->role === 'secretary')
            <button class="ap-ic-btn" type="button" id="editBtn" title="Edit">
              <i class="fa-solid fa-pen"></i>
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

          {{-- ✅ Table View --}}
          <a href="{{ route('appointment.show', array_merge(request()->query(), ['view' => 'table'])) }}"
            class="ap-icon-btn" aria-label="Table View" title="Table View">
            <i class="fa-solid fa-table-list"></i>
          </a>

          {{-- Filters --}}
          <form class="pl-filter d-flex align-items-center gap-2 flex-wrap" method="GET"
            action="{{ route('appointments.cards') }}">

            <input type="hidden" name="view" value="cards">

            @if(request('q'))
              <input type="hidden" name="q" value="{{ request('q') }}">
            @endif
            @if(request('status'))
              <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <input type="date" name="day" value="{{ $day }}">

            <button class="pl-icon-btn" type="submit" aria-label="Filter">
              <span class="material-icons-round">filter_alt</span>
            </button>

            <a class="pl-icon-btn" aria-label="Today" href="{{ route('appointments.cards', array_filter([
    'q' => request('q'),
    'status' => request('status'),
    'day' => now()->toDateString(),
    'view' => 'cards'
  ])) }}">
              <span class="material-icons-round">today</span>
            </a>

            <a class="pl-icon-btn" aria-label="Clear" href="{{ route('appointments.cards', array_filter([
    'q' => request('q'),
    'status' => request('status'),
    'view' => 'cards'
  ])) }}">
              <span class="material-icons-round">close</span>
            </a>
          </form>

        </div>

        {{-- Search --}}
        <form class="pl-search" method="GET" action="{{ route('appointments.cards') }}">
          <input type="hidden" name="view" value="cards">

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

    {{-- ================= Alerts ================= --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif


    {{-- ================= Status Filter + Counters ================= --}}
    @php $currentStatus = request('status', 'pending'); @endphp

    <form method="GET" action="{{ route('appointments.cards') }}"
      class="d-flex align-items-end gap-3 flex-wrap w-100">

      <input type="hidden" name="view" value="cards">

      @if(request('q'))
        <input type="hidden" name="q" value="{{ request('q') }}">
      @endif
      @if(request('day'))
        <input type="hidden" name="day" value="{{ request('day') }}">
      @endif

      <div class="d-flex flex-column">
        <label class="form-label mb-0 fw-semibold">Status</label>
        <select name="status" class="form-select form-select-sm" style="min-width:160px" onchange="this.form.submit()">
          <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All</option>
        </select>
      </div>

      {{-- ✅ Counters جنب الـ select و Responsive --}}
      <div class="d-flex gap-2 ms-auto flex-wrap">
        <span class="ap-pill ap-pill-total">
          <i class="fa-solid fa-chart-pie"></i>
          Total
          <span class="ap-pill-count">
            {{ ($pendingCount ?? 0) + ($completedCount ?? 0) + ($cancelledCount ?? 0) }}
          </span>
        </span>
        <div class="ad-status-pill ad-pending">
          <span class="material-icons-round">schedule</span>
          Pending <strong>{{ $pendingCount ?? 0 }}</strong>
        </div>

        <div class="ad-status-pill ad-completed">
          <span class="material-icons-round">check_circle</span>
          Completed <strong>{{ $completedCount ?? 0 }}</strong>
        </div>

        <div class="ad-status-pill ad-cancelled">
          <span class="material-icons-round">cancel</span>
          Cancelled <strong>{{ $cancelledCount ?? 0 }}</strong>
        </div>
      </div>

    </form>

{{-- ================= Cards ================= --}}
<section class="ap-main">
  <div class="container-fluid ap-container">
    <div class="row g-4">

      @forelse($groups as $dateKey => $items)

        @php
          $sorted = $items->sortBy(fn($a) => $a->appointment_time ?? '99:99');
          $dayHeader = $dateKey !== 'unknown'
            ? \Carbon\Carbon::parse($dateKey)->format('D, d M Y')
            : 'Unknown Date';
        @endphp

        {{-- Day Header --}}
        <div class="col-12">
          <div class="ap-day-head">
            <span class="ap-day-title">{{ $dayHeader }}</span>
          </div>
        </div>

        @foreach($sorted as $appt)
          @php
            $st = $appt->status ?? 'pending';

            $dayCounters[$dateKey] = ($dayCounters[$dateKey] ?? 0) + 1;
            $dayNo = $dayCounters[$dateKey];

            $visitText = '-';
            if (is_array($appt->visit_types) && count($appt->visit_types)) {
              $visitText = collect($appt->visit_types)->pluck('type')->implode(', ');
            }

            $dtText = $appt->appointment_date ?? '-';
            if (!empty($appt->appointment_time)) {
              $dtText .= ' - ' . $appt->appointment_time;
            }

            // ✅ Flags
            $isVip = (int)($appt->vip ?? 0) === 1;
            $isEmergency = (int)($appt->emergency ?? 0) === 1;

            // ✅ Card highlight class
            $flagCardClass = $isEmergency ? 'ap-card-emergency' : ($isVip ? 'ap-card-vip' : '');
          @endphp

          <div class="col-12 col-md-6 col-lg-4">
            <div class="ap-card {{ $flagCardClass }}">

              <div class="ap-card-head d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <div class="ap-name">{{ $appt->patient_name ?? '-' }}</div>               
                </div>

                <span class="ap-status ap-status-{{ $st }}">{{ ucfirst($st) }}</span>
              </div>

              {{-- ✅ small flag line (optional) --}}
              @if($isEmergency || $isVip)
                <div class="ap-flag-row">
                  @if($isEmergency)
                    <span class="ap-flag ap-flag-emergency">
                      <i class="fa-solid fa-triangle-exclamation"></i> High Priority
                    </span>
                  @endif
                  @if($isVip)
                    <span class="ap-flag ap-flag-vip">
                      <i class="fa-solid fa-crown"></i> Special Care
                    </span>
                  @endif
                </div>
              @endif

              <div class="ap-info">
                <div class="ap-line">
                  <i class="fa-regular fa-user ap-line-ic"></i>
                  <span class="ap-line-label">Doctor :</span>
                  <span class="ap-line-val">{{ $appt->doctor_name ?? '-' }}</span>
                </div>

                <div class="ap-line">
                  <i class="fa-regular fa-calendar ap-line-ic"></i>
                  <span class="ap-line-label">Date:</span>
                  <span class="ap-line-val">{{ $dtText }}</span>
                </div>

                <div class="ap-line">
                  <i class="fa-solid fa-phone ap-line-ic"></i>
                  <span class="ap-line-label">Phone :</span>
                  <span class="ap-line-val">{{ $appt->patient_number ?? '-' }}</span>
                </div>

                <div class="ap-line">
                  <i class="fa-solid fa-house-chimney-medical ap-line-ic"></i>
                  <span class="ap-line-label">Visit :</span>
                  <span class="ap-line-val">{{ $visitText }}</span>
                </div>
              </div>

              <div class="ap-actions">

                @if(auth()->check() && auth()->user()->role === 'secretary')
                  <label class="ap-check-wrap" title="Select">
                    <input class="form-check-input row-check" type="checkbox" value="{{ $appt->id }}" name="ids[]"
                      form="bulkDeleteForm">
                    <span class="ap-check-ui"><i class="fa-solid fa-check"></i></span>
                  </label>
                @endif

                @if(auth()->check() && auth()->user()->role === 'doctor')
                  <a class="ap-action-btn" href="{{ route('appointments.singleShow', $appt->id) }}" aria-label="View"
                    title="View">
                    <i class="fa-regular fa-eye"></i>
                  </a>
                @endif

                @if(auth()->check() && auth()->user()->role === 'secretary')
                  <a class="ap-action-btn" href="{{ route('appointment.reset', $appt->id) }}?no={{ $dayNo }}"
                    target="_blank" aria-label="Print" title="Print">
                    <i class="fa-solid fa-print"></i>
                  </a>

                  {{-- ✅ VIP Print only إذا VIP = 1 (اختياري) --}}
                  <a class="ap-action-btn ap-action-btn-vip {{ $isVip ? '' : 'disabled' }}"
                    href="{{ $isVip ? (route('appointment.vipPrint', $appt->id) . '?no=' . $dayNo) : '#' }}"
                    target="{{ $isVip ? '_blank' : '' }}"
                    aria-label="VIP Ticket"
                    title="{{ $isVip ? 'Print VIP Ticket' : 'Not VIP' }}">
                    <i class="fa-solid fa-award"></i>
                  </a>
                @endif

              </div>

            </div>
          </div>
        @endforeach

      @empty
        <div class="col-12">
          <div class="text-center py-5">
            No appointments found.
          </div>
        </div>
      @endforelse

    </div>

    <div class="mt-4 custom-pagination">
      {{ $appointments->links('pagination::bootstrap-5') }}
    </div>

  </div>
</section>


  </section>

  @if($canManage)
    <script>
      const editBtn = document.getElementById('editBtn');
      const bulkForm = document.getElementById('bulkDeleteForm');

      function getCheckedIds() {
        return [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
      }

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