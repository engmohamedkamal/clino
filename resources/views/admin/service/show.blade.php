@extends('layouts.dash')

@section('dash-content')
<link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<section class="pl-main container">
  <div class="pl-topbar">
    <h2 class="pl-title">Services</h2>

    <div class="pl-actions">

      {{-- Edit: يروح لصفحة edit لو مختار خدمة واحدة --}}
      <button class="pl-icon-btn" type="button" aria-label="Edit" id="editBtn">
        <span class="material-icons-round">edit</span>
      </button>

      {{-- Delete Selected --}}
      <form id="bulkDeleteForm" method="POST" action="{{ route('service.bulkDestroy') }}" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="pl-icon-btn" type="submit" aria-label="Delete" id="deleteBtn">
          <span class="material-icons-round">delete</span>
        </button>
      </form>

      {{-- Add --}}
      <a href="{{ route('service.create') }}" class="pl-icon-btn primary" aria-label="Add">
        <span class="material-icons-round">add</span>
      </a>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('service.index') }}">
        <span class="material-icons-round">search</span>
        <input
          id="searchInput"
          name="q"
          type="text"
          value="{{ request('q') }}"
          placeholder="Search services..."
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
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
          </tr>
        </thead>

        <tbody id="servicesTbody">
          @forelse($services as $service)
            <tr data-id="{{ $service->id }}">
              <td>
                <input class="form-check-input pl-check row-check"
                       type="checkbox"
                       name="ids[]"
                       value="{{ $service->id }}"
                       form="bulkDeleteForm">
              </td>

              <td>
                <img
                  src="{{ $service->image ? asset('storage/'.$service->image) : asset('images/default-service.png') }}"
                  alt="service"
                  style="width:44px;height:44px;object-fit:cover;border-radius:10px">
              </td>

              <td>{{ $service->name }}</td>

              <td>{{ \Illuminate\Support\Str::limit($service->description ?? '-', 40) }}</td>

              <td>
                @if($service->status)
                  <span class="badge text-bg-success">Active</span>
                @else
                  <span class="badge text-bg-secondary">Inactive</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No services found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 custom-pagination">
      {{ $services->links('pagination::bootstrap-5') }}
    </div>
  </div>

</section>

{{-- ✅ JS صغير للـ select all + edit selected + bulk delete --}}
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
      alert('اختار Service واحدة بس عشان تعمل Edit');
      return;
    }
    // يروح ل edit route
    window.location.href = "{{ url('/service') }}/" + ids[0] + "/edit";
    // لو route عندك /services بدل /service غيرها هنا:
    // window.location.href = "{{ url('/services') }}/" + ids[0] + "/edit";
  });

  bulkForm?.addEventListener('submit', (e) => {
    const ids = getCheckedIds();
    if (ids.length === 0) {
      e.preventDefault();
      alert('اختار Service/Services الأول عشان تعمل Delete');
      return;
    }
    if (!confirm('Delete selected services?')) {
      e.preventDefault();
    }
  });
</script>
@endsection
