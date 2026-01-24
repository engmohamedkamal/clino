@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  @php
    // هنا عندك إدارة الجدول (الـ bulk delete و edit)
  @endphp

  <section class="pl-main container">
    <div class="pl-topbar">
      <h2 class="pl-title">Patient List</h2>

      <div class="pl-actions">

        {{-- Edit --}}
        <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
          <span class="material-icons-round">edit</span>
        </button>

        {{-- Delete Selected (patients فقط) --}}
        <form id="bulkDeleteForm" method="POST" action="{{ route('users.bulkDestroy') }}" class="d-inline">
          @csrf
          @method('DELETE')
          <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
            <span class="material-icons-round">delete</span>
          </button>
        </form>

        {{-- Add --}}
        <a href="{{ route('users.create') }}" class="pl-icon-btn primary" aria-label="Add">
          <span class="material-icons-round">add</span>
        </a>

        {{-- ✅ Card View --}}
        <a href="{{ route('patients.cards', array_merge(request()->query(), ['view' => 'cards'])) }}" class="pl-icon-btn"
          aria-label="Card View" title="Card View">
          <span class="material-icons-round">view_module</span>
        </a>

        {{-- Search --}}
        <form class="pl-search" method="GET" action="{{ route('patients.index') }}">
          {{-- ✅ يخلي أي submit من هنا يرجع Table View --}}
          <input type="hidden" name="view" value="table">

          <span class="material-icons-round">search</span>
          <input id="searchInput" name="q" type="text" value="{{ request('q') }}" placeholder="Search type of keywords">
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
              <th>Phone Number</th>
              <th>ID</th>
            </tr>
          </thead>

          <tbody id="patientsTbody">
            @isset($users)
              @foreach($users as $user)
                <tr data-id="user-{{ $user->id }}" class="table-warning">
                  <td>
                    <input class="form-check-input pl-check row-check" type="checkbox" name="user_ids[]"
                      value="{{ $user->id }}" form="bulkDeleteForm" data-type="user">
                  </td>

                  <td>{{ $user->name }}</td>
                  <td>{{ $user->phone ?? '-' }}</td>
                  <td>{{ $user->id_number }}</td>
                </tr>
              @endforeach
            @endisset

            {{-- ✅ لو مفيش نتائج خالص --}}
            @if($patients->isEmpty() && (!isset($users) || $users->isEmpty()))
              <tr>
                <td colspan="4" class="text-center py-4">No patients found.</td>
              </tr>
            @endif

          </tbody>
        </table>
      </div>

      {{-- ✅ Pagination --}}
      <div class="mt-4 custom-pagination">
        {{ $patients->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>

      @isset($users)
        <div class="mt-3 custom-pagination">
          {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @endisset
    </div>

  </section>

  <script>
    const selectAll = document.getElementById('selectAll');
    const editBtn = document.getElementById('editBtn');
    const bulkForm = document.getElementById('bulkDeleteForm');

    function getChecked() {
      return [...document.querySelectorAll('.row-check:checked')];
    }

    function getCheckedUsersIds() {
      return getChecked()
        .filter(cb => cb.dataset.type === 'user')
        .map(cb => cb.value);
    }


    function getCheckedRowsInfo() {
      return getChecked().map(cb => ({
        type: cb.dataset.type,
        id: cb.value
      }));
    }

    selectAll?.addEventListener('change', () => {
      const checked = selectAll.checked;
      document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
    });

    editBtn?.addEventListener('click', () => {
      const selected = getCheckedRowsInfo();

      if (selected.length !== 1) {
        alert('Select Patient To Edit');
        return;
      }

      const { type, id } = selected[0];

      if (type === 'patient') {
        window.location.href = "{{ url('/users') }}/" + id + "/edit";
        return;
      }

      window.location.href = "{{ url('/users') }}/" + id + "/edit";
    });

    bulkForm?.addEventListener('submit', (e) => {
      const patientIds = getCheckedPatientsIds();

      if (patientIds.length === 0) {
        e.preventDefault();
        alert('Select Patient First To delete');
        return;
      }

      if (!confirm('Delete selected patients?')) {
        e.preventDefault();
      }
    });

  </script>
@endsection