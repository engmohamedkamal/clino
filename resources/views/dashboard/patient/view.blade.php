@extends('layouts.dash')

@section('dash-content')
<link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<section class="pl-main container">
  <div class="pl-topbar">
    <h2 class="pl-title">Patient List</h2>

    <div class="pl-actions">

      {{-- Edit: يروح لصفحة edit لو مختار مريض واحد --}}
      <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
        <span class="material-icons-round">edit</span>
      </button>

      {{-- Delete Selected --}}
      <form id="bulkDeleteForm" method="POST" action="{{ route('patients.bulkDestroy') }}" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
          <span class="material-icons-round">delete</span>
        </button>
      </form>

      {{-- Add --}}
      <a href="{{ route('patients.create') }}" class="pl-icon-btn primary" aria-label="Add">
        <span class="material-icons-round">add</span>
      </a>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('patients.index') }}">
        <span class="material-icons-round">search</span>
        <input
          id="searchInput"
          name="q"
          type="text"
          value="{{ request('q') }}"
          placeholder="Search type of keywords"
        >
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
            <th class="check-col">
              <input class="form-check-input pl-check" type="checkbox" id="selectAll">
            </th>
            <th>Name</th>
            <th>Gender</th>
            <th>About</th>
            <th>Phone Number</th>
            <th>Address</th>
            <th>ID</th>
          </tr>
        </thead>

        <tbody id="patientsTbody">
          @forelse($patients as $patient)
            <tr data-id="{{ $patient->id }}">
              <td>
                <input class="form-check-input pl-check row-check"
                       type="checkbox"
                       name="ids[]"
                       value="{{ $patient->id }}"
                       form="bulkDeleteForm">
              </td>

              <td>{{ $patient->patient_name }}</td>
              <td>{{ $patient->gender ?? '-' }}</td>

              {{-- Diagnosis: لحد ما تضيف column diagnosis، هنحط about مختصر --}}
              <td>{{ \Illuminate\Support\Str::limit($patient->about ?? '-', 25) }}</td>

              <td>{{ $patient->patient_number }}</td>
              <td>{{ $patient->address ?? '-' }}</td>
              <td>{{ $patient->id_number ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">No patients found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Laravel Pagination --}}
    <div class="mt-4 custom-pagination">
    {{ $patients->links('pagination::bootstrap-5') }}
</div>
  </div>

</section>

{{-- ✅ JS صغير للـ select all + edit selected --}}
<script>
  const selectAll = document.getElementById('selectAll');
  const editBtn   = document.getElementById('editBtn');
  const bulkForm  = document.getElementById('bulkDeleteForm');

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
      alert('اختار مريض واحد بس عشان تعمل Edit');
      return;
    }
    // يروح ل edit route
    window.location.href = "{{ url('/patients') }}/" + ids[0] + "/edit";
  });

  bulkForm?.addEventListener('submit', (e) => {
    const ids = getCheckedIds();
    if (ids.length === 0) {
      e.preventDefault();
      alert('اختار مريض/مرضى الأول عشان تعمل Delete');
      return;
    }
    if (!confirm('Delete selected patients?')) {
      e.preventDefault();
    }
  });
</script>
@endsection
