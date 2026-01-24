@extends('layouts.dash')
@section('dash-content')

  <link rel="stylesheet" href="{{ asset('CSS/addReports.css') }}" />

  <main class="main">

    <!-- Topbar -->
    <header class="topbar d-flex align-items-center justify-content-between">

      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>

        <div class="page-title">Reports</div>


      </div>

      {{-- 🔍 Search --}}
      <form method="GET" action="{{ route('reports.index') }}" class="d-none d-md-flex align-items-center gap-2">

        <div class="position-relative">
          <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>

          <input type="text" name="q" value="{{ request('q') }}" class="form-control ps-5" placeholder="Search reports..."
            style="min-width:260px;">
        </div>
      </form>
      @if(auth()->user()->role !== 'patient')
        <a href="{{ route('reports.create') }}"
          class="btn btn-primary d-none d-md-inline-flex align-items-center gap-2 ms-2">
          <i class="bi bi-plus-circle"></i>
          <span>Add Report</span>
        </a>
      @endif
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

        @if($reports->isEmpty())
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
              <div class="display-6 mb-2">🗂️</div>
              <div class="fw-semibold fs-5">No reports yet</div>
              <div class="text-muted mt-1">There are no reports to display.</div>
            </div>
          </div>
        @else

          <div class="row g-3">
            @foreach($reports as $report)
                @php

                  $imgUrl = $report->exam_image
                    ? asset('storage/' . $report->exam_image)
                    : null;

                  $typeLabel = ucfirst(str_replace('_', ' ', $report->exam_type));
                @endphp

                <div class="col-12 col-lg-6">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 p-md-4">

                      {{-- Header --}}
                      <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                          <div class="text-muted small">Report #{{ $report->id }}</div>
                          <div class="fw-semibold">{{ $typeLabel }}</div>
                          <div class="text-muted small mt-1">
                            Date:
                            {{ $report->exam_date
              ? \Carbon\Carbon::parse($report->exam_date)->format('Y-m-d')
              : '-' }}
                          </div>
                        </div>


                      </div>

                      <hr class="my-3">

                      {{-- Patient / Doctor --}}
                      <div class="row g-3 align-items-center">

                        <div class="col-12 col-md-4">
                          {{-- Image --}}
                          @if($imgUrl)
                            <button type="button" class="btn p-0 border-0 bg-transparent w-100" data-bs-toggle="modal"
                              data-bs-target="#reportImageModal" data-img="{{ $imgUrl }}"
                              data-title="Report #{{ $report->id }} - {{ $typeLabel }}">
                              <div
                                class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center"
                                style="height:110px;">
                                <img src="{{ $imgUrl }}" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                              </div>
                              <div class="text-muted small mt-1 text-center">
                                Click to preview
                              </div>
                            </button>
                          @else
                            <div
                              class="border rounded-3 bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                              style="height:110px;">
                              <i class="bi bi-image fs-3"></i>
                              <div class="small mt-1">No image</div>
                            </div>
                          @endif
                        </div>
                        @php
                          $patientName = $report->patient?->patient_name
                            ?? $report->patientUser?->name
                            ?? '-';

                          $patientPhone = $report->patient?->patient_number
                            ?? $report->patientUser?->phone
                            ?? '-';
                        @endphp

                        <div class="col-12 col-md-8">
                          <div class="mb-2">
                            <div class="text-muted small">Patient</div>
                            <div class="fw-semibold">{{ $patientName }}</div>
                            <div class="text-muted small">{{ $patientPhone }}</div>
                          </div>

                          <div>
                            <div class="text-muted small">Doctor</div>
                            <div class="fw-semibold">{{ $report->doctor?->name ?? '-' }}</div>
                          </div>
                        </div>


                      </div>

                      {{-- Note --}}
                      @if($report->note)
                        <div class="mt-3">
                          <div class="text-muted small">Note</div>
                          <div>{{ \Illuminate\Support\Str::limit($report->note, 120) }}</div>
                        </div>
                      @endif

                      {{-- Actions --}}
                      <div class="d-flex justify-content-end gap-2 mt-3">

                        @if(auth()->user()->role === 'secretary')
                          <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                          </a>

                          <form action="{{ route('reports.destroy', $report->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this report?')">
                              <i class="bi bi-trash me-1"></i> Delete
                            </button>
                          </form>
                        @endif
                      </div>

                    </div>
                  </div>
                </div>
            @endforeach
          </div>

          <div class="mt-4">
            {{ $reports->links('pagination::bootstrap-5') }}
          </div>

        @endif

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
          <div class="bg-light d-flex align-items-center justify-content-center" style="min-height:60vh;">
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

  {{-- ================= Script ================= --}}
  <script>
    (function () {
      const modal = document.getElementById('reportImageModal');
      if (!modal) return;

      const imgEl = document.getElementById('reportImageModalImg');
      const titleEl = document.getElementById('reportImageTitle');
      const openBtn = document.getElementById('reportImageOpenNewTab');

      modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;

        const img = btn.getAttribute('data-img');
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