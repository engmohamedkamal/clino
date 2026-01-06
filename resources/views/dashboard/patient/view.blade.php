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

      {{-- Delete Selected (patients فقط) --}}
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

          {{-- ✅ Patients table results --}}
          @forelse($patients as $patient)
            <tr data-id="patient-{{ $patient->id }}">
              <td>
                <input class="form-check-input pl-check row-check"
                       type="checkbox"
                       name="ids[]"
                       value="{{ $patient->id }}"
                       form="bulkDeleteForm"
                       data-type="patient">
              </td>

              <td>{{ $patient->patient_name }}</td>
              <td>{{ $patient->gender ?? '-' }}</td>
              <td>{{ \Illuminate\Support\Str::limit($patient->about ?? '-', 25) }}</td>
              <td>{{ $patient->patient_number }}</td>
              <td>{{ $patient->address ?? '-' }}</td>
              <td>{{ $patient->id_number ?? '-' }}</td>
            </tr>
          @empty
            {{-- هنسيبها فاضية هنا، ونطبع "No patients" تحت لو الاتنين فاضيين --}}
          @endforelse

          {{-- ✅ Users table results (role = patient) --}}
          @isset($users)
            @foreach($users as $user)
              <tr data-id="user-{{ $user->id }}" class="table-warning">
                <td>
                  {{-- NOTE: ده مش هيتحذف من bulkDeleteForm لأن الفورم بتاعك patients --}}
                  <input class="form-check-input pl-check row-check"
                         type="checkbox"
                         name="user_ids[]"
                         value="{{ $user->id }}"
                         form="bulkDeleteForm"
                         data-type="user">
                </td>

                <td>
                  {{ $user->name }}
                  <span class="badge bg-secondary ms-2">User</span>
                </td>
                <td>-</td>
                <td>User Only</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>-</td>
                <td>{{ $user->id_number }}</td>
              </tr>
            @endforeach
          @endisset

          {{-- ✅ لو مفيش نتائج خالص --}}
          @if($patients->isEmpty() && (!isset($users) || $users->isEmpty()))
            <tr>
              <td colspan="7" class="text-center py-4">No patients found.</td>
            </tr>
          @endif

        </tbody>
      </table>
    </div>

    {{-- ✅ Pagination --}}
    <div class="mt-4 custom-pagination">
      {{ $patients->links('pagination::bootstrap-5') }}
    </div>

    @isset($users)
      <div class="mt-3 custom-pagination">
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    @endisset
  </div>

</section>

<script>
  const selectAll = document.getElementById('selectAll');
  const editBtn   = document.getElementById('editBtn');
  const bulkForm  = document.getElementById('bulkDeleteForm');

  function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
  }

  function getCheckedPatientsIds() {
    return getChecked()
      .filter(cb => cb.dataset.type === 'patient')
      .map(cb => cb.value);
  }

  function getCheckedRowsInfo() {
    // [{type:'patient'|'user', id:'...'}]
    return getChecked().map(cb => ({
      type: cb.dataset.type,
      id: cb.value
    }));
  }

  // Select all
  selectAll?.addEventListener('change', () => {
    const checked = selectAll.checked;
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
  });

  // Edit selected (يدعم patient أو user)
  editBtn?.addEventListener('click', () => {
    const selected = getCheckedRowsInfo();

    if (selected.length !== 1) {
      alert('اختار صف واحد بس عشان تعمل Edit');
      return;
    }

    const { type, id } = selected[0];

    if (type === 'patient') {
      window.location.href = "{{ url('/patients') }}/" + id + "/edit";
      return;
    }

    // لو عندك صفحة تعديل لليوزر:
    // عدّل المسار حسب الروت عندك
    window.location.href = "{{ url('/users') }}/" + id + "/edit";
  });

  // Bulk delete: يطبق على patients فقط (لأن route بتاعك patients.bulkDestroy)
  bulkForm?.addEventListener('submit', (e) => {
    const patientIds = getCheckedPatientsIds();

    if (patientIds.length === 0) {
      e.preventDefault();
      alert('اختار مريض/مرضى الأول (من جدول Patients) عشان تعمل Delete');
      return;
    }

    if (!confirm('Delete selected patients?')) {
      e.preventDefault();
    }
  });
</script>
@endsection
