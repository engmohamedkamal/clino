@extends('layouts.dash')

@section('dash-content')
<link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<section class="pl-main container">
  <div class="pl-topbar">
    <h2 class="pl-title">Medical Orders List</h2>

    <div class="pl-actions">

      {{-- Edit: يروح لصفحة edit لو مختار Order واحد --}}
      <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
        <span class="material-icons-round">edit</span>
      </button>

      {{-- Delete Selected --}}
      <form id="bulkDeleteForm" method="POST" action="{{ route('medical-orders.bulkDestroy') }}" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
          <span class="material-icons-round">delete</span>
        </button>
      </form>

      {{-- Add --}}
      <a href="{{ route('medical-orders.create') }}" class="pl-icon-btn primary" aria-label="Add">
        <span class="material-icons-round">add</span>
      </a>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('medical-orders.index') }}">
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
            <th>Type</th>
            <th>Created At</th>
          </tr>
        </thead>

        <tbody id="ordersTbody">
          @forelse($medicalOrders as $order)
            <tr data-id="{{ $order->id }}">
              <td>
                <input class="form-check-input pl-check row-check"
                       type="checkbox"
                       name="ids[]"
                       value="{{ $order->id }}"
                       form="bulkDeleteForm">
              </td>

              <td>{{ $order->name }}</td>

              <td>
                @php
                  $type = $order->type;
                  $label = $type === 'medicine' ? 'Medicine' : ($type === 'analysis' ? 'Analysis' : 'Radiology');
                @endphp

                {{-- Badge لطيف (اختياري) --}}
                <span class="badge
                  {{ $type === 'medicine' ? 'bg-primary' : ($type === 'analysis' ? 'bg-success' : 'bg-warning text-dark') }}">
                  {{ $label }}
                </span>
              </td>

              <td>{{ optional($order->created_at)->format('Y-m-d') ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4">No medical orders found.</td>
            </tr>
          @endforelse
        </tbody>

      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 custom-pagination">
      {{ $medicalOrders->links('pagination::bootstrap-5') }}
    </div>
  </div>

</section>

<script>
  const selectAll = document.getElementById('selectAll');
  const editBtn   = document.getElementById('editBtn');
  const bulkForm  = document.getElementById('bulkDeleteForm');

  function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
  }

  function getCheckedIds() {
    return getChecked().map(cb => cb.value);
  }

  // Select all
  selectAll?.addEventListener('change', () => {
    const checked = selectAll.checked;
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
  });

  // Edit selected (لازم واحد بس)
  editBtn?.addEventListener('click', () => {
    const selected = getCheckedIds();

    if (selected.length !== 1) {
      alert('اختار صف واحد بس عشان تعمل Edit');
      return;
    }

    const id = selected[0];
    window.location.href = "{{ url('/medical-orders') }}/" + id + "/edit";
  });

  // Bulk delete
  bulkForm?.addEventListener('submit', (e) => {
    const ids = getCheckedIds();

    if (ids.length === 0) {
      e.preventDefault();
      alert('اختار عنصر/عناصر الأول عشان تعمل Delete');
      return;
    }

    if (!confirm('Delete selected medical orders?')) {
      e.preventDefault();
    }
  });
</script>
@endsection
