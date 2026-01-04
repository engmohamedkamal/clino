@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  <section class="pl-main container">
    <div class="pl-topbar">
      <h2 class="pl-title">Users List</h2>

      <div class="pl-actions">

        {{-- Edit: يروح لصفحة edit لو مختار يوزر واحد --}}
        <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
          <span class="material-icons-round">edit</span>
        </button>

        {{-- Delete Selected --}}
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

        {{-- Search --}}
        <form class="pl-search" method="GET" action="{{ route('users.index') }}">
          <span class="material-icons-round">search</span>
          <input id="searchInput" name="q" type="text" value="{{ request('q') }}" placeholder="Search users">
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
              <th>Role</th>
              <th>Phone</th>
              <th>ID Number</th>
              <th>Created At</th>
            </tr>
          </thead>

          <tbody id="usersTbody">
            @forelse($users as $user)
              <tr data-id="{{ $user->id }}">
                <td>
                  <input class="form-check-input pl-check row-check" type="checkbox" name="ids[]" value="{{ $user->id }}"
                    form="bulkDeleteForm">
                </td>

                <td>{{ $user->name }}</td>
                <td>
                  @php
                    $roleClasses = [
                      'admin' => 'bg-danger',
                      'doctor' => 'bg-primary',
                      'patient' => 'bg-success',
                    ];

                    $roleClass = $roleClasses[$user->role] ?? 'bg-secondary';
                  @endphp

                  <span class="badge {{ $roleClass }} px-3 py-2 rounded-pill">
                    {{ ucfirst($user->role) }}
                  </span>
                </td>

                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->id_number ?? '-' }}</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">No users found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="mt-4 custom-pagination">
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    </div>

  </section>

  {{-- ✅ JS: select all + edit selected --}}
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
        alert('اختار يوزر واحد بس عشان تعمل Edit');
        return;
      }
      window.location.href = "{{ url('/users') }}/" + ids[0] + "/edit";
    });

    bulkForm?.addEventListener('submit', (e) => {
      const ids = getCheckedIds();
      if (ids.length === 0) {
        e.preventDefault();
        alert('اختار يوزر/يوزرز الأول عشان تعمل Delete');
        return;
      }
      if (!confirm('Delete selected users?')) {
        e.preventDefault();
      }
    });
  </script>
@endsection