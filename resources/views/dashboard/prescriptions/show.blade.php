@extends('layouts.dash')
@section('dash-content')

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
  $medicine    = $rx->medicine_name ?? '-';
  $dosage      = $rx->dosage ?? '-';
  $duration    = $rx->duration ?? '-';
  $notes       = $rx->notes ?? '-';

  $printUrl = route('prescriptions.show', $rx->id) . '?print=1';
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
        {{-- لو diagnosis نص واحد --}}
        <div class="rx-item">1- {{ $diagnosis }}</div>
      </div>

      {{-- Medications --}}
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
            <tr>
              <td class="td-castom">{{ $medicine }}</td>
              <td>{{ $dosage }}</td>
              <td>{{ $duration }}</td>
              <td>{{ $notes }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <hr>

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
