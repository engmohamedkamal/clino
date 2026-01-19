@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/patientTransfer.css') }}" />

<main class="pt-main">

  {{-- ✅ Success / Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success mb-3">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error') || $errors->any())
    <div class="alert alert-danger mb-3">
      <strong>There were some problems:</strong>
      <ul class="mb-0 mt-2">
        @if(session('error'))
          <li>{{ session('error') }}</li>
        @endif
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ================= Header ================= --}}
  <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3 mb-md-4">
    <div>
      <h3 class="pt-title mb-1">Patient Transfers</h3>
      <div class="pt-subtitle">Manage patient transfer requests.</div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      {{-- Search --}}
      <form method="GET" action="{{ route('patient-transfers.index') }}" class="d-flex gap-2">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input
            type="text"
            name="q"
            value="{{ $q ?? request('q') }}"
            class="form-control"
            placeholder="Search by code, patient, hospital..."
          >
        </div>
        <button class="btn btn-primary" type="submit">
          Search
        </button>
        @if(!empty($q))
          <a href="{{ route('patient-transfers.index') }}" class="btn btn-light">Reset</a>
        @endif
      </form>

      {{-- ✅ Create (Admin/Doctor only) --}}
      @if(auth()->user()->role !== 'patient')
        <a href="{{ route('patient-transfers.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> New Transfer
        </a>
      @endif
    </div>
  </div>

  {{-- ================= Table / List ================= --}}
  <section class="pt-card">
    <div class="pt-card-head pt-between">
      <div class="d-flex align-items-center gap-2">
        <div class="pt-card-ico"><i class="bi bi-arrow-left-right"></i></div>
        <div class="pt-card-title">Transfers List</div>
      </div>
      <div class="pt-muted">
        {{ $transfers->total() }} Total
      </div>
    </div>

    <div class="pt-card-body">

      @if($transfers->count())

        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr class="text-uppercase small text-muted">
                <th>Patient</th>
                <th>Code</th>
                <th>Priority</th>
                <th>Destination</th>
                <th>Status</th>
                <th class="text-nowrap">Created</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach($transfers as $t)
                @php
                  $patientName = $t->patient_name ?? optional($t->patient)->name ?? '—';
                  $code = $t->transfer_code ?? '—';
                  $priority = $t->transfer_priority ?? 'normal';
                  $bed = $t->bed_status ?? 'pending';
                  $status = $t->status ?? 'submitted';
                  $dest = $t->destination_hospital ?? '—';

                  $prioClass = $priority === 'urgent' ? 'pt-tag-warn' : 'pt-tag-soft';

                  $bedClass = $bed === 'confirmed'
                    ? 'pt-tag-success'
                    : ($bed === 'denied' ? 'pt-tag-danger' : 'pt-tag-warn');

                  $created = optional($t->created_at)->format('M d, Y');
                @endphp

                <tr>
                  {{-- Patient --}}
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="min-w-0">
                        <div class="fw-semibold text-truncate" style="max-width:220px;">
                          {{ $patientName }}
                        </div>
                        <div class="small text-muted">
                          {{ $t->current_location ?? '—' }}
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Code --}}
                  <td class="text-nowrap">
                    <span class="pt-tag pt-tag-soft">#{{ $code }}</span>
                  </td>

                  {{-- Priority --}}
                  <td class="text-nowrap">
                    <span class="pt-tag {{ $prioClass }}">
                      {{ strtoupper($priority) }}
                    </span>
                  </td>

                  {{-- Destination --}}
                  <td class="min-w-0">
                    <div class="fw-semibold text-truncate" style="max-width:240px;">
                      {{ $dest }}
                    </div>
                    <div class="small text-muted">
                      {{ $t->destination_dept_unit ?? '—' }}
                      @if(!empty($t->destination_bed_no))
                        • {{ $t->destination_bed_no }}
                      @endif
                    </div>
                  </td>

                  {{-- Status --}}
                  <td class="text-nowrap">
                    <span class="pt-tag {{ $bedClass }}">
                      {{ strtoupper($bed) }}
                    </span>
                    <div class="small text-muted mt-1">
                      {{ str_replace('_',' ', ucfirst($status)) }}
                    </div>
                  </td>

                  {{-- Created --}}
                  <td class="text-nowrap">
                    {{ $created }}
                  </td>

                  {{-- Actions --}}
                  <td class="text-end text-nowrap">
                    {{-- View للجميع --}}
                    <a href="{{ route('patient-transfers.show', $t) }}" class="btn btn-sm btn-light">
                      <i class="bi bi-eye"></i>
                    </a>

                    {{-- Edit/Delete لغير المريض فقط --}}
                    @if(auth()->user()->role !== 'patient')
                      <a href="{{ route('patient-transfers.edit', $t) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-pencil"></i>
                      </a>

                      <form method="POST"
                            action="{{ route('patient-transfers.destroy', $t) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete this transfer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
          <div class="pt-muted">
            Showing {{ $transfers->firstItem() }} - {{ $transfers->lastItem() }} of {{ $transfers->total() }}
          </div>

          <div>
            {{ $transfers->links() }}
          </div>
        </div>

      @else
        <div class="text-center py-5">
          <div class="pt-muted mb-2">No transfers found.</div>

          {{-- ✅ Create button (Admin/Doctor only) --}}
          @if(auth()->user()->role !== 'patient')
            <a href="{{ route('patient-transfers.create') }}" class="btn btn-primary">
              <i class="bi bi-plus-lg me-1"></i> Create Transfer
            </a>
          @endif
        </div>
      @endif

    </div>
  </section>

</main>
@endsection
