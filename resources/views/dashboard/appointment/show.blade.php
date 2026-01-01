@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'doctor']); // doctor + admin فقط
  @endphp

  <section class="pl-main container">
    <div class="pl-topbar">
      <h2 class="pl-title">Appointments List</h2>

      <div class="pl-actions">

        {{-- Edit (admin/doctor فقط) --}}
        @if($canManage)
          <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
            <span class="material-icons-round">edit</span>
          </button>

          {{-- Delete Selected --}}
          
          <form id="bulkDeleteForm" method="POST" action="{{ route('appointments.bulkDestroy') }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
              <span class="material-icons-round">delete</span>
            </button>
          </form>
          @endif
          
        {{-- Add (أي حد يقدر يحجز) --}}
        <a href="{{ route('appointment.index') }}" class="pl-icon-btn primary" aria-label="Add">
          <span class="material-icons-round">add</span>
        </a>

        {{-- Search --}}
        <form class="pl-search" method="GET" action="{{ route('appointment.show') }}">
          <span class="material-icons-round">search</span>
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

    <!-- Table Card -->
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

              <th>Patient</th>
              <th>Phone</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Gender</th>
              <th>DOB</th>
              <th>Created At</th>
            </tr>
          </thead>

          <tbody>
            @forelse($appointments as $appt)
              <tr data-id="{{ $appt->id }}">
                @if($canManage)
                  <td>
                    <input class="form-check-input pl-check row-check" type="checkbox" name="ids[]" value="{{ $appt->id }}"
                      form="bulkDeleteForm">
                  </td>
                @endif

                <td>{{ $appt->patient_name ?? '-' }}</td>
                <td>{{ $appt->patient_number ?? '-' }}</td>
                <td>{{ $appt->doctor_name ?? '-' }}</td>
                <td>{{ $appt->appointment_date ?? '-' }}</td>
                <td>{{ $appt->appointment_time ?? '-' }}</td>
                <td>{{ $appt->gender ?? '-' }}</td>
                <td>{{ $appt->dob ?? '-' }}</td>
                <td>{{ optional($appt->created_at)->format('Y-m-d') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="{{ $canManage ? 9 : 8 }}" class="text-center py-4">
                  No appointments found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
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