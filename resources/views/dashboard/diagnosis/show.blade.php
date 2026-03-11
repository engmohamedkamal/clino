@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/transferReview.css') }}">

@php
  $role = auth()->user()->role ?? '';
  $canSeePrivate = in_array($role, ['admin', 'doctor']);

  $patient = $diagnosis->patient ?? null;
  $patientName = $patient->name ?? ($diagnosis->patient_name ?? '—');
  $patientId   = $patient->id ?? ($diagnosis->patient_id ?? '—');

  $diagId = 'DX-' . str_pad($diagnosis->id, 6, '0', STR_PAD_LEFT);
  $issued = optional($diagnosis->created_at)->format('M d, Y H:i');

  // QR يفتح صفحة التشخيص نفسها
  $qrUrl = route('diagnoses.show', $diagnosis);
  $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qrUrl);
@endphp

<main class="container-fluid py-2 py-md-3">

  {{-- =================== Top Actions =================== --}}
 <div class="pt-actions container py-3">
  <div class="pt-actions-bar">

    <!-- Print -->
    <button
      type="button"
      class="pt-btn pt-btn-print"
      onclick="window.print()"
    >
      <span class="pt-btn-icon">
        <i class="bi bi-printer"></i>
      </span>
      <span class="pt-btn-text">Print</span>
    </button>

    {{-- WhatsApp --}}
    @php
      $pdfUrl = route('diagnoses.pdf', $diagnosis->id);
      $msg = "🧾 Diagnosis Report PDF is ready:\n{$pdfUrl}";
      $patient = $diagnosis->patient ?? null;
    @endphp

    @if(session('patient_phone'))
      <a
        href="https://wa.me/{{ preg_replace('/\D+/', '',  session('patient_phone') ) }}?text={{ urlencode($msg) }}"
        target="_blank"
        class="pt-btn pt-btn-wa text-decoration-none"
      >
        <span class="pt-btn-icon">
          <i class="bi bi-whatsapp"></i>
        </span>
        <span class="pt-btn-text">Send Diagnosis PDF</span>
      </a>
    @else
      <button class="pt-btn pt-btn-wa disabled" disabled>
        <span class="pt-btn-icon">
          <i class="bi bi-whatsapp"></i>
        </span>
        <span class="pt-btn-text">No Patient Phone</span>
      </button>
    @endif

  </div>
</div>

  <div class="container pb-5">
    <div class="pt-page mx-auto">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
          <h1 class="pt-title">Diagnosis Report</h1>
          <div class="pt-subline">
            <b>Hospital:</b> {{ $setting->name ?? 'Helper Clinic' }}
            &nbsp;&nbsp;&nbsp;
            <b>Phone:</b> {{ $setting->phone ?? '—' }}
          </div>
        </div>

        <div class="text-end">
          <div class="pt-badge">
            <i class="bi bi-clipboard2-check"></i>
            Diagnosis Record
          </div>

          <div class="pt-meta">
            <div><b>Diagnosis ID:</b> <a href="javascript:void(0)">{{ $diagId }}</a></div>
            <div>Issued: {{ $issued }}</div>
          </div>
        </div>
      </div>

      {{-- =================== Patient Info =================== --}}
      <div class="pt-section">
        <div class="pt-section-head">
          <i class="bi bi-person"></i>
          Patient Information
        </div>

        <div class="pt-grid">
          <div class="pt-field">
            <div class="pt-label">Full Name</div>
            <div class="pt-value strong">{{ $patientName }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Patient ID</div>
            <div class="pt-value strong">{{ $patientId }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Created By</div>
            <div class="pt-value strong">
              {{ 'Dr / ' . $diagnosis->creator->name ?? '_' }}
            </div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Record Date</div>
            <div class="pt-value strong">{{ optional($diagnosis->created_at)->format('M d, Y') }}</div>
          </div>
        </div>
      </div>

      {{-- =================== Diagnosis Summary =================== --}}
      <div class="pt-section">
        <div class="pt-section-head">
          <i class="bi bi-clipboard2-pulse"></i>
          Diagnosis Summary
        </div>

        {{-- Public Diagnosis (always visible) --}}
        <div class="pt-note">
          <div class="pt-label">Public Diagnosis</div>
          {{ $diagnosis->public_diagnosis ?: '—' }}
        </div>

        {{-- Private Diagnosis (only admin/doctor) --}}
        @if($canSeePrivate)
          <div class="pt-note">
            <div class="pt-label">Private Diagnosis</div>
            {{ $diagnosis->private_diagnosis ?: '—' }}
          </div>
        @endif
      </div>

      {{-- =================== Signatures =================== --}}
      @php
        $doctorName = optional($diagnosis->doctor ?? $diagnosis->user ?? null)->name ?? '—';
      @endphp

      <div class="pt-signs">
        <div class="pt-line">
          Physician's Signature
          <small>{{ 'Dr / ' . $diagnosis->creator->name ?? '_' }}</small>
        </div>

        <div class="pt-line">
          Patient Acknowledgement
          <small>Signature</small>
        </div>

        {{-- QR بدل Stamp --}}
     <div class="pt-stamp text-center">
  @if(!empty($diagnosis->creator?->doctorInfo?->social_link))
    {!! QrCode::size(120)->generate($diagnosis->creator->doctorInfo->social_link) !!}
  @endif
</div>


      </div>

      <hr class="text-muted mt-5">

      <div class="pt-footer p-3">
        Confidential Medical Record - Form Generated by Helper Clinic CMS
      </div>

    </div>
  </div>

</main>

@endsection
