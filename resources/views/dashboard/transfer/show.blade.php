@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/transferReview.css') }}">

<main class="container-fluid py-2 py-md-3">

  {{-- =================== Top Actions =================== --}}
  <div class="pt-actions container py-3">
    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">

      <button type="button" class="pt-btn pt-btn-primary" onclick="window.print()">
        <i class="bi bi-printer"></i> Print
      </button>


      <a href="{{ route('patient-transfers.index') }}" class="pt-icon-circle" aria-label="Exit">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>

  {{-- =================== Page =================== --}}
  <div class="container pb-5">
    <div class="pt-page mx-auto">

      {{-- =================== Header =================== --}}
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
          <h1 class="pt-title">Patient Transfer</h1>
          <div class="pt-subline">
            <b>Hospital:</b> {{ $setting->name ?? 'Helper Clinic' }}
            &nbsp;&nbsp;&nbsp;
            <b>Phone:</b> {{ $setting->phone ?? '—' }}
          </div>
        </div>

        @php
          $priority = $transfer->transfer_priority ?? 'urgent';
          $isUrgent = $priority === 'urgent';

          $transferId = $transfer->transfer_code ?? ('TR-' . str_pad($transfer->id, 6, '0', STR_PAD_LEFT));
          $issued = optional($transfer->created_at)->format('M d, Y H:i');
        @endphp

        <div class="text-end">
          <div class="pt-badge">
            <i class="bi bi-exclamation-triangle"></i>
            {{ $isUrgent ? 'Urgent Transfer' : 'Normal Transfer' }}
          </div>

          <div class="pt-meta">
            <div><b>Transfer ID:</b> <a href="javascript:void(0)">{{ $transferId }}</a></div>
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

        @php
          $patient = $transfer->patient ?? null;

          $patientName = $transfer->patient_name ?? ($patient->name ?? '—');
          $patientId   = $patient->id ?? '—';

          $age    = $transfer->age ?? '—';
          $gender = $transfer->gender ? ucfirst($transfer->gender) : '—';

          $dept = $transfer->current_location ?: '—';

          $admission = $patient?->created_at ? $patient->created_at->format('M d, Y') : '—'; // عدلها لو عندك admission_date
          $blood = $transfer->blood_type ?: '—';
        @endphp

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
            <div class="pt-label">Age / Gender</div>
            <div class="pt-value strong">{{ $age }} Yrs / {{ $gender }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Department</div>
            <div class="pt-value strong">{{ $dept }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Admission Date</div>
            <div class="pt-value strong">{{ $admission }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Blood Type</div>
            <div class="pt-value strong">{{ $blood }}</div>
          </div>
        </div>

        <div class="pt-tags">
          @if($transfer->penicillin_allergy)
            <span class="pt-tag">
              <i class="bi bi-exclamation-circle"></i> Penicillin Allergy
            </span>
          @endif

          @if($transfer->high_fall_risk)
            <span class="pt-tag yellow">
              <i class="bi bi-shield-exclamation"></i> High Fall Risk
            </span>
          @endif

          @if(!$transfer->penicillin_allergy && !$transfer->high_fall_risk)
            <span class="pt-tag">
              <i class="bi bi-info-circle"></i> No risk flags
            </span>
          @endif
        </div>
      </div>

      {{-- =================== Clinical Summary =================== --}}
      <div class="pt-section">
        <div class="pt-section-head">
          <i class="bi bi-clipboard2-pulse"></i>
          Clinical Summary
        </div>

        <div class="pt-two">
          <div class="pt-field">
            <div class="pt-label">Reason for Transfer</div>
            <div class="pt-value strong">{{ $transfer->reason_for_transfer ?: '—' }}</div>
          </div>

          <div class="pt-field">
            <div class="pt-label">Primary Diagnosis</div>
            <div class="pt-value strong">{{ $transfer->primary_diagnosis ?: '—' }}</div>
          </div>
        </div>

        <div class="pt-note">
          <div class="pt-label">Clinical History &amp; Findings</div>
          {{ $transfer->medical_summary ?: '—' }}
        </div>
      </div>

      {{-- =================== Destination + Logistics =================== --}}
      <div class="pt-bottom-two">
        {{-- Destination --}}
        <div class="pt-box">
          <div class="pt-box-head">
            <i class="bi bi-geo-alt"></i>
            Destination
          </div>

          <div class="pt-box-body">
            <div class="pt-field">
              <div class="pt-label">Target Facility</div>
              <div class="pt-value strong">{{ $transfer->destination_hospital ?: '—' }}</div>
            </div>

            <div class="pt-field mt-3">
              <div class="pt-label">Department</div>
              <div class="pt-value strong">{{ $transfer->destination_dept_unit ?: '—' }}</div>
            </div>

            <div class="pt-field mt-3" style="border-bottom:none;padding-bottom:0;">
              <div class="pt-label">Receiving Physician</div>
              <div class="pt-value strong">{{ $transfer->receiving_doctor_name ?: '—' }}</div>
              @if($transfer->receiving_phone)
                <div class="pt-subline mt-1"><b>Phone:</b> {{ $transfer->receiving_phone }}</div>
              @endif
            </div>
          </div>
        </div>

        {{-- Transport --}}
        <div class="pt-box">
          <div class="pt-box-head">
            <i class="bi bi-truck"></i>
            Transport &amp; Logistics
          </div>

          <div class="pt-box-body">
            @php
              $mode = $transfer->transport_mode ?? 'als_ambulance';
              $isAls = $mode === 'als_ambulance';
              $isWheel = $mode === 'wheelchair_van';
            @endphp

            <div class="pt-checks">
              <label>
                <input type="checkbox" {{ $isAls ? 'checked' : '' }} disabled>
                ALS Ambulance
              </label>

              <label>
                <input type="checkbox" {{ $isWheel ? 'checked' : '' }} disabled>
                Wheelchair Van
              </label>
            </div>

            <div class="pt-note mt-3" style="margin:0;">
              <div class="pt-label">Equipment</div>
              @php
                $equip = [];
                if ($transfer->continuous_oxygen) $equip[] = 'Continuous Oxygen';
                if ($transfer->cardiac_monitoring) $equip[] = 'Cardiac Monitoring';
              @endphp
              {{ count($equip) ? implode(' • ', $equip) : '—' }}
            </div>
          </div>
        </div>
      </div>

      {{-- =================== Attachments Summary =================== --}}
      @php
        $atts = $transfer->attachments ?? [];

        // لو جاية json string
        if (is_string($atts)) {
          $atts = json_decode($atts, true) ?: [];
        }

        // لو جاية null
        $atts = is_array($atts) ? $atts : [];

        // تنظيف: شيل الفاضي والمسافات
        $atts = array_values(array_filter(array_map(fn($v) => trim((string)$v), $atts)));
      @endphp

      <div class="pt-section">
        <div class="pt-section-head">
          <i class="bi bi-paperclip"></i>
          Attachments Summary
        </div>

        <div class="pt-attach">
          @if(count($atts))
            <ul>
              @foreach($atts as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>
          @else
            <div class="pt-note" style="margin:0;">
              <div class="pt-label">No attachments</div>
              —
            </div>
          @endif
        </div>
      </div>

      {{-- =================== Signatures =================== --}}
      @php
        $physicianName = optional($transfer->primaryPhysician)->name ?? '—';
      @endphp

      <div class="pt-signs">
        <div class="pt-line">
          Physician's Signature
          <small>{{ $physicianName ? 'Dr. ' . $physicianName : '—' }}</small>
        </div>

        <div class="pt-line">
          Receiving Facility Approval
          <small>Representative Signature</small>
        </div>

       <div class="pt-stamp text-center">
  {{-- <div class="fw-semibold mb-2">Scan QR</div> --}}

  <img
    src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('/')) }}"
    alt="QR Code"
    style="width:120px;height:120px"
  >

  {{-- <div class="pt-muted mt-1 small">
    Transfer Details
  </div> --}}
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
