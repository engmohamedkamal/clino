@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/addReports.css') }}" />

@php
  $imgUrl = $report->exam_image ? asset('storage/'.$report->exam_image) : null;
  $typeLabel = ucfirst(str_replace('_',' ', $report->exam_type ?? 'report'));

  $patientName = $report->patient?->patient_name
      ?? $report->patientUser?->name
      ?? '-';

  $patientPhone = $report->patient?->patient_number
      ?? $report->patientUser?->phone
      ?? '-';

  $doctorName = $report->doctor?->name ?? '-';
@endphp

<main class="main">

  <!-- Topbar -->
  <header class="topbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>

      <a href="{{ url()->previous() }}" class="btn btn-light d-inline-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back</span>
      </a>

      <div class="ms-2">
        <div class="page-title">Report #{{ $report->id }}</div>
        <div class="text-muted small">{{ $typeLabel }}</div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
        {{ $typeLabel }}
      </span>

      @if(auth()->user()->role === 'secretary')
        <a href="{{ route('reports.edit', $report->id) }}"
           class="btn btn-primary d-none d-md-inline-flex align-items-center gap-2">
          <i class="bi bi-pencil-square"></i>
          <span>Edit</span>
        </a>

        <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button class="btn btn-danger d-none d-md-inline-flex align-items-center gap-2"
                  type="submit"
                  onclick="return confirm('Delete this report?')">
            <i class="bi bi-trash"></i>
            <span>Delete</span>
          </button>
        </form>
      @endif
    </div>
  </header>

  <section class="rp-wrap py-4">
    <div class="container">

      {{-- Success --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Errors --}}
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
          <div class="fw-semibold mb-1">Please fix the following:</div>
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="row g-3">
        {{-- Left: Image Preview --}}
        <div class="col-12 col-lg-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 p-md-4">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-semibold">Examination Image</div>
                @if($imgUrl)
                  <a href="{{ $imgUrl }}" target="_blank" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open
                  </a>
                @endif
              </div>

              @if($imgUrl)
                <button
                  type="button"
                  class="btn p-0 border-0 bg-transparent w-100"
                  data-bs-toggle="modal"
                  data-bs-target="#reportImageModal"
                  data-img="{{ $imgUrl }}"
                  data-title="Report #{{ $report->id }} - {{ $typeLabel }}"
                >
                  <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center"
                       style="height:340px;">
                    <img src="{{ $imgUrl }}"
                         class="img-fluid"
                         style="width:100%; height:100%; object-fit:contain;">
                  </div>
                  <div class="text-muted small mt-2 text-center">
                    Click to preview
                  </div>
                </button>
              @else
                <div class="border rounded-3 bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                     style="height:340px;">
                  <i class="bi bi-image fs-1"></i>
                  <div class="mt-2 fw-semibold">No image</div>
                  <div class="small">This report has no uploaded file.</div>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Right: Details --}}
        <div class="col-12 col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">

              <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                <div>
                  <div class="text-muted small">Report Information</div>
                  <div class="fw-semibold fs-5">{{ $typeLabel }}</div>
                </div>

                <div class="text-end">
                  <div class="text-muted small">Exam Date</div>
                  <div class="fw-semibold">
                    {{ $report->exam_date ? \Carbon\Carbon::parse($report->exam_date)->format('Y-m-d') : '-' }}
                  </div>
                </div>
              </div>

              <hr class="my-3">

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="text-muted small">Patient</div>
                  <div class="fw-semibold">{{ $patientName }}</div>
                  <div class="text-muted small">{{ $patientPhone }}</div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="text-muted small">Doctor</div>
                  <div class="fw-semibold">{{ $doctorName }}</div>
                  <div class="text-muted small">
                    Created:
                    {{ optional($report->created_at)->format('Y-m-d h:i A') ?? '-' }}
                  </div>
                </div>
              </div>

              @if($report->note)
                <hr class="my-3">
                <div class="text-muted small mb-1">Note</div>
                <div class="p-3 bg-light rounded-3">
                  {!! nl2br(e($report->note)) !!}
                </div>
              @endif

              <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                <a href="{{ route('reports.index') }}" class="btn btn-light">
                  <i class="bi bi-grid me-1"></i>
                  All Reports
                </a>

                @if(auth()->user()->role === 'secretary')
                  <div class="d-flex gap-2">
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-primary">
                      <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>

                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger" type="submit"
                              onclick="return confirm('Delete this report?')">
                        <i class="bi bi-trash me-1"></i> Delete
                      </button>
                    </form>
                  </div>
                @endif
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

{{-- ================= Modal ================= --}}
<div class="modal fade" id="reportImageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="reportImageTitle">Report Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <div class="bg-light d-flex align-items-center justify-content-center"
             style="min-height:60vh;">
          <img id="reportImageModalImg" class="img-fluid" style="max-height:80vh;">
        </div>
      </div>

      <div class="modal-footer">
        <a id="reportImageOpenNewTab" href="#" target="_blank" class="btn btn-light">
          <i class="bi bi-box-arrow-up-right me-1"></i>
          Open in new tab
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
          Done
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const modal = document.getElementById('reportImageModal');
  if (!modal) return;

  const imgEl   = document.getElementById('reportImageModalImg');
  const titleEl = document.getElementById('reportImageTitle');
  const openBtn = document.getElementById('reportImageOpenNewTab');

  modal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if (!btn) return;

    const img   = btn.getAttribute('data-img');
    const title = btn.getAttribute('data-title') || 'Report Image';

    titleEl.textContent = title;
    imgEl.src = img;
    openBtn.href = img;
  });

  modal.addEventListener('hidden.bs.modal', function () {
    imgEl.src = '';
    openBtn.href = '#';
  });
})();
</script>

@endsection
