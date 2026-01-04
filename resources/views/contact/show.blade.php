@extends('layouts.dash')

@section('dash-content')
  <link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

  <section class="pl-main container">
    <div class="pl-topbar">
      <h2 class="pl-title">Messages List</h2>

      <div class="pl-actions">

        {{-- Delete Selected --}}
        <form id="bulkDeleteForm" method="POST" action="{{ route('messages.bulkDestroy') }}" class="d-inline">
          @csrf
          @method('DELETE')
          <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
            <span class="material-icons-round">delete</span>
          </button>
        </form>

        {{-- Search --}}
        <form class="pl-search" method="GET" action="{{ route('messages.index') }}">
          <span class="material-icons-round">search</span>
          <input id="searchInput" name="q" type="text" value="{{ request('q') }}" placeholder="Search messages">
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
              <th>Service</th>
              <th>Message</th>
              <th>Sent From</th>
              {{-- <th>Created At</th> --}}
            </tr>
          </thead>

          <tbody id="messagesTbody">
            @forelse($messages as $msg)
              <tr data-id="{{ $msg->id }}">
                <td>
                  <input class="form-check-input pl-check row-check"
                         type="checkbox"
                         name="ids[]"
                         value="{{ $msg->id }}"
                         form="bulkDeleteForm">
                </td>

                <td>{{ $msg->user->name }}</td>
                <td>{{ $msg->service ?? '-' }}</td>

                {{-- لو الرسالة طويلة --}}
                <td style="max-width: 420px;">
                  <div class="text-truncate" style="max-width: 420px;">
                    {{ $msg->message }}
                  </div>
                </td>

                <td>{{ $msg->created_at ? $msg->created_at->diffForHumans() : '-' }}</td>

                {{-- <td>{{ optional($msg->created_at)->format('Y-m-d') }}</td> --}}
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">No messages found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="mt-4 custom-pagination">
        {{ $messages->links('pagination::bootstrap-5') }}
      </div>
    </div>

  </section>

  {{-- ✅ JS: select all + delete selected --}}
  <script>
    const selectAll = document.getElementById('selectAll');
    const bulkForm = document.getElementById('bulkDeleteForm');

    function getCheckedIds() {
      return [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
    }

    selectAll?.addEventListener('change', () => {
      const checked = selectAll.checked;
      document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
    });

    bulkForm?.addEventListener('submit', (e) => {
      const ids = getCheckedIds();
      if (ids.length === 0) {
        e.preventDefault();
        alert('اختار رسالة/رسائل الأول عشان تعمل Delete');
        return;
      }
      if (!confirm('Delete selected messages?')) {
        e.preventDefault();
      }
    });
  </script>
@endsection
