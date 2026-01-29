@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

{{-- Page CSS --}}
<link rel="stylesheet" href="{{ asset('CSS/patientLisst.css') }}" />
<link rel="stylesheet" href="{{ asset('CSS/patientList.css') }}">

@php
  $q = request('q', '');
@endphp

<section class="ap-body">

  {{-- Topbar --}}
  <header class="ap-topbar">
    <div class="container-fluid px-3 px-lg-4">
      <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
  <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
        {{-- Title --}}
        <div class="d-flex align-items-center gap-3">
          <h1 class="ap-title m-0">Patient List</h1>
        </div>

        {{-- Search (Desktop) --}}
        <div class="ap-search-wrap flex-grow-1 d-none d-md-block">
          <form class="input-group ap-search" method="GET" action="{{ route('patients.cards') }}">
            <input type="hidden" name="view" value="cards">
            <span class="input-group-text ap-search-ic">
              <i class="bi bi-search"></i>
            </span>
            <input
              type="text"
              name="q"
              value="{{ $q }}"
              class="form-control ap-search-input"
              placeholder="Search type of keywords"
            />
          </form>
        </div>

        {{-- Actions --}}
        <div class="d-flex align-items-center gap-2">
          {{-- Table View --}}
          <a class="btn ap-icon-btn" href="{{ route('patients.index', array_merge(request()->query(), ['view' => 'table'])) }}"
             aria-label="Table View" title="Table View">
            <i class="bi bi-table"></i>
          </a>

        </div>
      </div>

      {{-- Mobile search --}}
      <div class="ap-search-wrap d-md-none mt-3">
        <form class="input-group ap-search" method="GET" action="{{ route('patients.cards') }}">
          <input type="hidden" name="view" value="cards">
          <span class="input-group-text ap-search-ic">
            <i class="bi bi-search"></i>
          </span>
          <input
            type="text"
            name="q"
            value="{{ $q }}"
            class="form-control ap-search-input"
            placeholder="Search type of keywords"
          />
        </form>
      </div>

      @if(session('success'))
        <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
      @endif


  </header>

  {{-- Main --}}
  <main class="ap-main">
    <div class="container-fluid px-3 px-lg-4">

      <div class="row g-4 g-lg-5 justify-content-center">

        {{-- ================= Patients (table) ================= --}}
        @forelse($patients as $patient)
          <div class="col-12 col-md-6 col-xl-4">
            <article class="ap-card">

              <div class="ap-avatar">
                <i class="bi bi-person"></i>
              </div>

              <div class="ap-card-body">

                <div class="ap-row">
                  <div class="ap-left">
                    <i class="bi bi-person ap-ic"></i>
                    <span class="ap-k">Name :</span>
                    <span class="ap-v">{{ $patient->patient_name }}</span>
                  </div>

                 

                <div class="ap-row">
                  <div class="ap-left">
                    <i class="bi bi-telephone ap-ic"></i>
                    <span class="ap-k">Phone :</span>
                    <span class="ap-v">{{ $patient->patient_number }}</span>
                  </div>
                </div>

                <div class="ap-row">
                  <div class="ap-left">
                    <i class="bi bi-card-text ap-ic"></i>
                    <span class="ap-k">ID :</span>
                    <span class="ap-v">{{ $patient->id_number ?? '-' }}</span>
                  </div>
                </div>

                <div class="ap-row">
                  <div class="ap-left">
                    <i class="bi bi-envelope ap-ic"></i>
                    <span class="ap-k">Email :</span>
                    <span class="ap-v">{{ $patient->patient_email ?? '-' }}</span>
                  </div>
                </div>

                <div class="ap-row">
                  <div class="ap-left">
                    <i class="bi bi-geo-alt ap-ic"></i>
                    <span class="ap-k">Address :</span>
                    <span class="ap-v">{{ $patient->address ?? '-' }}</span>
                  </div>
                </div>

              </div>
            </article>
          </div>
        @empty
        @endforelse

        {{-- ================= Users (role=patient) ================= --}}
        @isset($users)
          @foreach($users as $user)
            <div class="col-12 col-md-6 col-xl-4">
              <article class="ap-card">

                <div class="ap-avatar">
                  <i class="bi bi-person-badge"></i>
                </div>

                <div class="ap-card-body">

                  <div class="ap-row">
                    <div class="ap-left">
                      <i class="bi bi-person ap-ic"></i>
                      <span class="ap-k">Name :</span>
                      <span class="ap-v">{{ $user->name }}</span>
                    </div>

             
                  </div>

                  <div class="ap-row">
                    <div class="ap-left">
                      <i class="bi bi-telephone ap-ic"></i>
                      <span class="ap-k">Phone :</span>
                      <span class="ap-v">{{ $user->phone ?? '-' }}</span>
                    </div>
                  </div>

                  <div class="ap-row">
                    <div class="ap-left">
                      <i class="bi bi-card-text ap-ic"></i>
                      <span class="ap-k">ID :</span>
                      <span class="ap-v">{{ $user->id_number ?? '-' }}</span>
                    </div>
                  </div>

                <div class="ap-row">
  <div class="ap-left">
    <a
       href="{{ route('patient-info.my', $user->id) }}"
      class="ap-view-btn"
    >
      View Profile
    </a>
  </div>
</div>


                </div>
              </article>
            </div>
          @endforeach
        @endisset

        {{-- لو مفيش نتائج --}}
        @if($patients->isEmpty() && (!isset($users) || $users->isEmpty()))
          <div class="col-12">
            <div class="text-center py-5">No patients found.</div>
          </div>
        @endif

      </div>

      {{-- Pagination --}}
      <div class="mt-4 custom-pagination">
        {{ $patients->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>

      @isset($users)
        <div class="mt-3 custom-pagination">
          {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @endisset

    </div>
  </main>

</section>

<script>
  const editBtn = document.getElementById('editBtn');
  const bulkForm = document.getElementById('bulkDeleteForm');

  function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
  }

  function getCheckedPatientsIds() {
    return getChecked()
      .filter(cb => cb.dataset.type === 'patient')
      .map(cb => cb.value);
  }

  function getCheckedRowsInfo() {
    return getChecked().map(cb => ({
      type: cb.dataset.type,
      id: cb.value
    }));
  }

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

    window.location.href = "{{ url('/users') }}/" + id + "/edit";
  });

  // Bulk delete: يطبق على patients فقط
  bulkForm?.addEventListener('submit', (e) => {
    const patientIds = getCheckedPatientsIds();

    if (patientIds.length === 0) {
      e.preventDefault();
      alert('اختار مريض/مرضى الأول (من Patients) عشان تعمل Delete');
      return;
    }

    if (!confirm('Delete selected patients?')) {
      e.preventDefault();
    }
  });
</script>

@endsection
