@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/patientTransfer.css') }}" />

@php
  $role = auth()->user()->role ?? '';
  $canManage = in_array($role, ['admin', 'doctor']); // create/edit/delete
  $isPatient = ($role === 'patient');               // ✅ patient hides private
@endphp

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
      <h3 class="pt-title mb-1">Diagnoses</h3>
      <div class="pt-subtitle">Manage patient diagnoses records.</div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      {{-- Search --}}
      <form method="GET" action="{{ route('diagnoses.index') }}" class="d-flex gap-2">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input
            type="text"
            name="q"
            value="{{ $q ?? request('q') }}"
            class="form-control"
            placeholder="Search by patient, public..."
          >
        </div>

        <button class="btn btn-primary" type="submit">Search</button>

        @if(!empty($q))
          <a href="{{ route('diagnoses.index') }}" class="btn btn-light">Reset</a>
        @endif
      </form>

      {{-- ✅ Create (Admin/Doctor فقط) --}}
      @if($canManage)
        <a href="{{ route('diagnoses.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> New Diagnosis
        </a>
      @endif
    </div>
  </div>

  {{-- ================= Table / List ================= --}}
  <section class="pt-card">
    <div class="pt-card-head pt-between">
      <div class="d-flex align-items-center gap-2">
        <div class="pt-card-ico"><i class="bi bi-clipboard2-pulse"></i></div>
        <div class="pt-card-title">Diagnoses List</div>
      </div>
      <div class="pt-muted">
        {{ $diagnoses->total() }} Total
      </div>
    </div>

    <div class="pt-card-body">

      @if($diagnoses->count())

        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr class="text-uppercase small text-muted">
                <th>Patient</th>
                <th>Public Diagnosis</th>

                {{-- ✅ Private column يظهر فقط لغير patient --}}
                @if(!$isPatient)
                  <th>Private Diagnosis</th>
                @endif

                <th class="text-nowrap">Created</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach($diagnoses as $d)
                @php
                  $patientName = optional($d->patient)->name ?? ($d->patient_name ?? '—');
                  $public  = $d->public_diagnosis ?? '—';
                  $private = $d->private_diagnosis ?? '—';
                  $created = optional($d->created_at)->format('M d, Y');
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
                          Diagnosis #{{ $d->id }}
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Public --}}
                  <td class="min-w-0">
                    <div class="fw-semibold text-truncate" style="max-width:320px;">
                      {{ $public }}
                    </div>
                  </td>

                  {{-- ✅ Private (hidden for patient) --}}
                  @if(!$isPatient)
                    <td class="min-w-0">
                      <div class="text-truncate" style="max-width:320px;">
                        {{ $private }}
                      </div>
                    </td>
                  @endif

                  {{-- Created --}}
                  <td class="text-nowrap">
                    {{ $created }}
                  </td>

                  {{-- Actions --}}
                  <td class="text-end text-nowrap">
                    {{-- View: للجميع --}}
                    <a href="{{ route('diagnoses.show', $d) }}" class="btn btn-sm btn-light">
                      <i class="bi bi-eye"></i>
                    </a>

                    {{-- Edit/Delete: Admin/Doctor فقط --}}
                    @if($canManage)
                      <a href="{{ route('diagnoses.edit', $d) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-pencil"></i>
                      </a>

                      <form method="POST"
                            action="{{ route('diagnoses.destroy', $d) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete this diagnosis?');">
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
            Showing {{ $diagnoses->firstItem() }} - {{ $diagnoses->lastItem() }} of {{ $diagnoses->total() }}
          </div>

          <div>
            {{ $diagnoses->links() }}
          </div>
        </div>

      @else
        <div class="text-center py-5">
          <div class="pt-muted mb-2">No diagnoses found.</div>

          {{-- Create CTA: Admin/Doctor فقط --}}
          @if($canManage)
            <a href="{{ route('diagnoses.create') }}" class="btn btn-primary">
              <i class="bi bi-plus-lg me-1"></i> Create Diagnosis
            </a>
          @endif
        </div>
      @endif

    </div>
  </section>

</main>
@endsection
