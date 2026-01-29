@extends('layouts.dash')

@section('dash-content')
<link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('CSS/medicine.css') }}"> --}}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<section class="pl-main container">

  {{-- Topbar --}}
  <div class="pl-topbar">
  <div class="d-flex align-items-start gap-3">
    <button class="btn icon-btn d-lg-none" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar">
      <i class="fa-solid fa-bars"></i>
    </button>

    <div>
      <h2 class="pl-title mb-0">Medical Orders List</h2>
      <div class="pl-sub text-muted">Manage medicines, radiology and lab analysis orders.</div>
    </div>
  </div>

  <div class="pl-actions">
    <form id="bulkDeleteForm" method="POST" action="{{ route('medical-orders.bulkDestroy') }}" class="d-inline">
      @csrf
      @method('DELETE')
      <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn" title="Delete selected">
        <span class="material-icons-round">delete</span>
      </button>
    </form>

    <a href="{{ route('medical-orders.create') }}" class="pl-icon-btn primary" aria-label="Add" title="Add new order">
      <span class="material-icons-round">add</span>
    </a>

    <form class="pl-search" method="GET" action="{{ route('medical-orders.index') }}">
      <span class="material-icons-round">search</span>
      <input
        id="searchInput"
        name="q"
        type="text"
        value="{{ request('q') }}"
        placeholder="Search by name or type..."
      >
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

  {{-- Table Card --}}
  <div class="pl-table-card">
    <div class="table-responsive">
      <table class="table pl-table mb-0 align-middle">
        <thead>
          <tr>
            <th class="check-col">
              <input class="form-check-input pl-check" type="checkbox" id="selectAll">
            </th>
            <th style="min-width:240px;">Name</th>
            <th style="min-width:140px;">Type</th>
            <th style="min-width:140px;">Dosage</th>
            <th style="min-width:140px;">Duration</th>
            <th style="min-width:140px;">Notes</th>
            {{-- <th class="text-nowrap" style="min-width:150px;">Created</th> --}}
            <th class="text-end text-nowrap" style="width:1%;">Actions</th>
          </tr>
        </thead>

        <tbody id="ordersTbody">
          @forelse($medicalOrders as $order)
            @php
              $type = $order->type;

              $label = $type === 'medicine'
                ? 'Medicine'
                : ($type === 'analysis' ? 'Analysis' : 'Radiology');

              $badgeClass = $type === 'medicine'
                ? 'bg-primary'
                : ($type === 'analysis' ? 'bg-success' : 'bg-warning text-dark');

              $typeIcon = $type === 'medicine'
                ? 'medication'
                : ($type === 'analysis' ? 'science' : 'image');
            @endphp

            <tr data-id="{{ $order->id }}" class="order-row">

              <td>
                <input class="form-check-input pl-check row-check"
                       type="checkbox"
                       name="ids[]"
                       value="{{ $order->id }}"
                       form="bulkDeleteForm">
              </td>

              {{-- Name --}}
              <td class="min-w-0">
                <div class="fw-semibold text-truncate" style="max-width:420px;">
                  {{ $order->name }}
                </div>
              
              </td>

              {{-- Type --}}
              <td class="text-nowrap">
                <span class="badge {{ $badgeClass }} d-inline-flex align-items-center gap-1 px-2 py-2">
                  <span class="material-icons-round" style="font-size:18px; line-height:1;">
                    {{ $typeIcon }}
                  </span>
                  {{ $label }}
                </span>
              </td>
<td>  {{ $order->dosage ?? '' }}</td>
<td>  {{ $order->duration ?? '' }}</td>
<td>  {{ $order->notes ?? '' }}</td>
              {{-- Created --}}
             
              {{-- Actions --}}
              <td class="text-end text-nowrap">


                <a href="{{ route('medical-orders.edit', $order->id) }}" class="btn btn-sm btn-light" title="Edit">
                  <span class="material-icons-round" style="font-size:18px;">edit</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center py-4">No medical orders found.</td>
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

  function getChecks() {
    return [...document.querySelectorAll('.row-check')];
  }

  function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
  }

  function getCheckedIds() {
    return getChecked().map(cb => cb.value);
  }

  function syncSelectAllState() {
    const all = getChecks();
    const checked = getChecked();
    if (!selectAll) return;

    if (all.length === 0) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
      return;
    }

    selectAll.checked = checked.length === all.length;
    selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
  }

  // Select all
  selectAll?.addEventListener('change', () => {
    const checked = selectAll.checked;
    getChecks().forEach(cb => cb.checked = checked);
    syncSelectAllState();
  });

  // per checkbox change
  document.addEventListener('change', (e) => {
    if (!e.target.classList.contains('row-check')) return;
    syncSelectAllState();
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

  // Optional: click row to open show (if exists), avoid clicking checkbox/buttons
  document.querySelectorAll('tr.order-row').forEach(row => {
    row.addEventListener('click', (e) => {
      const avoid = e.target.closest('input,button,a,label');
      if (avoid) return;

      @if(\Illuminate\Support\Facades\Route::has('medical-orders.show'))
        const id = row.getAttribute('data-id');
        if (id) window.location.href = "{{ url('/medical-orders') }}/" + id;
      @endif
    });
  });

  // init
  syncSelectAllState();
</script>
@endsection
