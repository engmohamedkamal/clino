@extends('layouts.dash')
@section('dash-content')
<title>Prescription</title>
@php
  // ===== Clinic Info (ممكن تستبدلها بـ $setting من DB) =====
  $clinicName  = $setting->name   ?? 'Helper Clinic';
  $clinicAddr  = $setting->address ?? 'Nazer, Eltar3a Street';
  $clinicPhone = $setting->phone  ?? '01221604325';
  $clinicEmail = $setting->email  ?? 'memamo0338@helperclinic.com';

  // ===== Prescription Data =====
  $patientName = $rx->patientUser->name ?? '-';
  $patientId   = $rx->patientUser->id ?? '-';

  $doctorName  = $rx->doctor?->user?->name ?? 'Doctor';
  $date        = optional($rx->created_at)->format('M d, Y');
  $rxCode      = 'RX-' . str_pad($rx->id, 6, '0', STR_PAD_LEFT);

  $diagnosis   = $rx->diagnosis ?? '-';

  // ===== Arrays (Safe decode) =====
  $medicines = is_array($rx->medicine_name) ? $rx->medicine_name : (json_decode($rx->medicine_name, true) ?: []);
  $dosages   = is_array($rx->dosage)        ? $rx->dosage        : (json_decode($rx->dosage, true) ?: []);
  $durations = is_array($rx->duration)      ? $rx->duration      : (json_decode($rx->duration, true) ?: []);
  $notesArr  = is_array($rx->notes)         ? $rx->notes         : (json_decode($rx->notes, true) ?: []);

  $rumors    = is_array($rx->rumor)         ? $rx->rumor         : (json_decode($rx->rumor, true) ?: []);
  $analyses  = is_array($rx->analysis)      ? $rx->analysis      : (json_decode($rx->analysis, true) ?: []);

  // تنظيف أي قيم فاضية
  $medicines = array_values(array_filter($medicines, fn($v) => trim((string)$v) !== ''));
  $rumors    = array_values(array_filter($rumors, fn($v) => trim((string)$v) !== ''));
  $analyses  = array_values(array_filter($analyses, fn($v) => trim((string)$v) !== ''));
@endphp

<link rel="stylesheet" href="{{ asset('CSS/Prescription.css') }}">

<div class="rx-body">

  {{-- Top Actions --}}
  <div class="rx-actions container py-3">
    <div class="d-flex align-items-center justify-content-center">
      <button type="button"
              class="btn rx-btn rx-btn-primary"
              onclick="window.print()">
        <i class="bi bi-printer"></i>
        Print Prescription
      </button>
    </div>
  </div>

  {{-- Prescription Card --}}
  <div class="container pb-4">
    <div class="rx-card mx-auto">

      {{-- Header --}}
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h5 class="rx-title">
            <i class="bi bi-hospital me-1"></i> {{ $clinicName }}
          </h5>
          <div class="text-muted small">Medical Prescription</div>
        </div>
      </div>

      {{-- Contact + Meta --}}
      <div class="row align-items-start mt-4 gy-3">
        <div class="col-md-7">
          <div class="rx-contact">
            <div><i class="bi bi-geo-alt text-castom"></i> {{ $clinicAddr }}</div>
            <div><i class="bi bi-telephone text-castom"></i> {{ $clinicPhone }}</div>
            <div><i class="bi bi-envelope text-castom"></i> {{ $clinicEmail }}</div>
          </div>
        </div>

        <div class="col-md-5 text-md-end">
          <div class="rx-meta">
            <div><span class="text-muted">Date :</span> {{ $date }}</div>
            <div><span class="text-muted">Prescription ID :</span> {{ $rxCode }}</div>
          </div>
        </div>
      </div>

      <hr>

      {{-- Patient Info --}}
      <h6 class="rx-section-title">
        <i class="bi bi-person"></i> Patient Information
      </h6>

      <div class="rx-box">
        <div class="row g-2">
          <div class="col-md-6">
            <div>Patient Name : <span class="text-black">{{ $patientName }}</span></div>
            <div>Patient ID : <span class="text-black">#{{ $patientId }}</span></div>
          </div>

          <div class="col-md-6">
            <div>Diagnosis Complaint : <span class="text-black">{{ $diagnosis }}</span></div>
            <div>
              Doctor : <span class="text-black">Dr. {{ $doctorName }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Diagnosis --}}
      <h6 class="rx-section-title mt-3">
        <i class="bi bi-clipboard2-pulse"></i> Diagnosis
      </h6>

      <div class="rx-box">
        <div class="rx-item">{{ $diagnosis }}</div>
      </div>

      {{-- ================= Rx Medications (Hide if empty) ================= --}}
      @if(count($medicines))
        <h6 class="rx-section-title mt-4">
          <i class="bi bi-capsule"></i> Rx Medications
        </h6>

        <div class="table-responsive">
          <table class="table rx-table">
            <thead>
              <tr>
                <th>MEDICINE NAME</th>
                <th>DOSAGE</th>
                <th>DURATION</th>
                <th>NOTES</th>
              </tr>
            </thead>
            <tbody>
              @foreach($medicines as $index => $medicine)
                <tr>
                  <td class="td-castom">{{ $medicine }}</td>
                  <td>{{ $dosages[$index] ?? '-' }}</td>
                  <td>{{ $durations[$index] ?? '-' }}</td>
                  <td>{{ $notesArr[$index] ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <hr>
      @endif

      {{-- ================= Radiology (rumor) (Hide if empty) ================= --}}
      @if(count($rumors))
        <h6 class="rx-section-title mt-4">
          <i class="bi bi-x-ray"></i> Radiology
        </h6>

        <div class="rx-box">
          <ul class="mb-0 ps-3">
            @foreach($rumors as $item)
              <li class="rx-item">{{ $item }}</li>
            @endforeach
          </ul>
        </div>

        <hr>
      @endif

      {{-- ================= Analysis (Hide if empty) ================= --}}
      @if(count($analyses))
        <h6 class="rx-section-title mt-4">
          <i class="bi bi-droplet-half"></i> Lab Analysis
        </h6>

        <div class="rx-box">
          <ul class="mb-0 ps-3">
            @foreach($analyses as $item)
              <li class="rx-item">{{ $item }}</li>
            @endforeach
          </ul>
        </div>

        <hr>
      @endif

      {{-- Footer --}}
      <div class="text-center mt-4">
        <div class="rx-sign">
          <div class="rx-sign-text">
            <div class="rx-name">DR : {{ strtoupper($doctorName) }}</div>
            <div class="rx-label">Doctor’s Signature</div>
          </div>
        </div>

        <div class="text-muted small mt-3">
          <h6 class="text-black mb-1">Thank you for choosing {{ $clinicName }}</h6>
          This is a computer-generated prescription and does not require a physical <br>
          signature. For any questions, please contact our clinic at <br>
          {{ $clinicEmail }}.
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
