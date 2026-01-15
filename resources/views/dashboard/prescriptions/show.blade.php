@extends('layouts.dash')
@section('dash-content')

@php
  $clinicName  = $setting->name   ?? 'Helper Clinic';
  $clinicAddr  = $setting->address ?? 'Nazer, Eltar3a Street';
  $clinicPhone = $setting->phone  ?? '01221604325';
  $clinicEmail = $setting->email  ?? 'memamo0338@helperclinic.com';

  $patientName = $rx->patientUser->name ?? '-';
  $patientId   = $rx->patientUser->id ?? null;

  $doctorName  = $rx->doctor?->user?->name ?? 'Doctor';

  $date        = optional($rx->created_at)->format('M d, Y');
  $rxCode      = 'RX-' . str_pad($rx->id, 6, '0', STR_PAD_LEFT);

  $diagnosis   = $rx->diagnosis ?? '-';


  $patientUrl = '#';
  if ($patientId) {
    $patientInfo = \App\Models\PatientInfo::where('user_id', $patientId)->first();
    $patientUrl = $patientInfo ? route('patient-info.show', $patientInfo->id) : '#';
  }

  $doctorUrl = $rx->doctor_id ? route('doctor-info.show', $rx->doctor_id) : '#';

  $logoUrl = !empty($setting?->logo) ? asset('storage/'.$setting->logo) : null;

  // ===== Arrays (Safe decode) =====
  $medicines = is_array($rx->medicine_name) ? $rx->medicine_name : (json_decode($rx->medicine_name, true) ?: []);
  $dosages   = is_array($rx->dosage)        ? $rx->dosage        : (json_decode($rx->dosage, true) ?: []);
  $durations = is_array($rx->duration)      ? $rx->duration      : (json_decode($rx->duration, true) ?: []);
  $notesArr  = is_array($rx->notes)         ? $rx->notes         : (json_decode($rx->notes, true) ?: []);

  $rumors    = is_array($rx->rumor)         ? $rx->rumor         : (json_decode($rx->rumor, true) ?: []);
  $analyses  = is_array($rx->analysis)      ? $rx->analysis      : (json_decode($rx->analysis, true) ?: []);

  // Clean empties
  $medicines = array_values(array_filter($medicines, fn($v) => trim((string)$v) !== ''));
  $rumors    = array_values(array_filter($rumors, fn($v) => trim((string)$v) !== ''));
  $analyses  = array_values(array_filter($analyses, fn($v) => trim((string)$v) !== ''));

  // WhatsApp share (share current RX page)
  $shareUrl = url()->current();
  $waLink = 'https://wa.me/?text=' . urlencode("Prescription: {$rxCode}\n{$shareUrl}");
@endphp

<link rel="stylesheet" href="{{ asset('CSS/Prescription.css') }}">
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
  rel="stylesheet">


<style>
  /* QR blocks to match UI placeholders */
  .rx-qr-top,
  .rx-qr-bottom{ display:flex; align-items:center; justify-content:center; }

  .qr-wrap{ position:relative; display:inline-block; }
  /* doctor in header (small) */
  .qr-wrap .qr-img.qr-doctor{ width:95px; height:95px; }
  /* patient bottom (bigger but still clean) */
  .qr-wrap .qr-img.qr-patient{ width:120px; height:120px; }

  .qr-wrap .qr-logo{
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
    width:26px; height:26px;
    background:#fff; padding:3px;
    border-radius:8px;
    object-fit:contain;
    box-shadow:0 6px 14px rgba(0,0,0,.12);
  }

  .qr-caption{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
    text-align:center;
  }

  @media (max-width: 576px){
    .qr-wrap .qr-img.qr-doctor{ width:85px; height:85px; }
    .qr-wrap .qr-img.qr-patient{ width:110px; height:110px; }
    .qr-wrap .qr-logo{ width:24px; height:24px; }
  }
</style>

<div class="rx-body">
  <div class="rx-actions container py-3">
  <div class="d-flex align-items-center justify-content-center">
    <button
      type="button"
      class="rx-print-btn"
      onclick="window.print()"
    >
      <span class="rx-print-icon">
        <i class="bi bi-printer"></i>
      </span>
      <span class="rx-print-text">Print Prescription</span>
    </button>
  </div>
</div>

  <div class="container pb-4">
    <div class="rx-card mx-auto">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h5 class="rx-title">
            <i class="bi bi-hospital me-1"></i> {{ $clinicName }}
          </h5>
          <div class="text-muted small">Medical Prescription</div>
        </div>

        <!-- ✅ Doctor QR Top -->
        <div class="rx-qr-top">
          <div class="text-center">
            <div class="qr-wrap">
              <img
                class="qr-img qr-doctor"
                style="width:70px;height:70px"
                src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($doctorUrl) }}"
                alt="Doctor QR">
             
            </div>
            
          </div>
        </div>
      </div>

      <!-- Contact + Meta -->
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

      <!-- Patient Info -->
      <h6 class="rx-section-title">
        <i class="bi bi-person"></i> Patient Information
      </h6>

      <div class="rx-box">
        <div class="row g-2">
          <div class="col-md-6">
            <div>Patient Name : <span class="text-black">{{ $patientName }}</span></div>
            <div>Patient ID : <span class="text-black">#{{ $patientId ?? '-' }}</span></div>
          </div>
          <div class="col-md-6">
            <div>Diagnosis Complaint : <span class="text-black">{{ $diagnosis }}</span></div>
            <div>
              Doctor : <span class="text-black">Dr. {{ $doctorName }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Diagnosis -->
      <h6 class="rx-section-title mt-3">
        <i class="bi bi-clipboard2-pulse"></i> Diagnosis
      </h6>

      <div class="rx-box">
        <div class="rx-item">{{ $diagnosis }}</div>
      </div>

      <!-- Medications -->
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
      @endif

      {{-- ✅ Radiology (rumor) --}}
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
      @endif

      {{-- ✅ Lab Analysis --}}
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
      @endif

      <!-- Terms & Patient QR Section -->
      <div class="row align-items-start mt-4">
        <div class="col-md-8">
          <div class="rx-terms-title">TERMS & CONDITIONS</div>
          <div class="rx-terms text-muted">
            Please follow the prescribed treatment exactly as directed by your physician.<br>
            Do not stop or modify the medication without medical consultation.<br>
            In case of any side effects, contact the clinic immediately.
          </div>
        </div>

        <div class="col-md-4 d-flex justify-content-end mt-3 mt-md-0 pe-md-4">
  <div class="rx-qr-bottom text-center">
    <div class="qr-wrap">
     <img
  class="qr-img qr-patient"
  style="width:70px;height:70px"
  src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($patientUrl) }}"
  alt="Patient QR">

    </div>
  </div>
</div>

      </div>

      <hr>

      <!-- Footer -->
      <div class="text-center mt-4">
        <div class="rx-sign">
          <div class="rx-sign-text">
            <div class="rx-name">DR : {{ strtoupper($doctorName) }}</div>
            <div class="rx-label">Doctor’s Signature</div>
          </div>
        </div>

        <div class="text-muted small mt-3">
          <h6 class="text-black mb-1">Thank you for choosing {{ $clinicName }}</h6>
          
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
