<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transfer PDF</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
    .row { display:flex; justify-content:space-between; gap:10px; }
    .box { border:1px solid #e5e7eb; padding:12px; border-radius:10px; margin:10px 0; }
    .title { font-size:18px; font-weight:700; margin:0 0 6px; }
    .muted { color:#6b7280; }
    .label { color:#6b7280; font-size:11px; margin-bottom:2px; }
    .value { font-weight:700; }
    hr { border:none; border-top:1px solid #e5e7eb; margin:14px 0; }
    ul { margin:8px 0 0 18px; }
  </style>
</head>
<body>

  <div class="row">
    <div>
      <div class="title">Patient Transfer</div>
      <div class="muted">
        <b>Hospital:</b> {{ $setting->name ?? 'Helper Clinic' }}
        &nbsp; | &nbsp;
        <b>Phone:</b> {{ $setting->phone ?? '—' }}
      </div>
    </div>

    @php
      $priority = $transfer->transfer_priority ?? 'urgent';
      $transferId = $transfer->transfer_code ?? ('TR-' . str_pad($transfer->id, 6, '0', STR_PAD_LEFT));
      $issued = optional($transfer->created_at)->format('M d, Y H:i');
    @endphp

    <div style="text-align:right;">
      <div><b>Transfer ID:</b> {{ $transferId }}</div>
      <div class="muted">Issued: {{ $issued }}</div>
      <div style="margin-top:6px;"><b>{{ $priority === 'urgent' ? 'Urgent' : 'Normal' }}</b></div>
    </div>
  </div>

  <div class="box">
    <div class="title" style="font-size:14px;">Patient Information</div>

    @php
      $patient = $transfer->patient ?? null;
      $patientName = $transfer->patient_name ?? ($patient->name ?? '—');
      $patientId   = $patient->id ?? '—';
      $age    = $transfer->age ?? '—';
      $gender = $transfer->gender ? ucfirst($transfer->gender) : '—';
      $dept   = $transfer->current_location ?: '—';
      $admission = $patient?->created_at ? $patient->created_at->format('M d, Y') : '—';
      $blood  = $transfer->blood_type ?: '—';
    @endphp

    <div class="row">
      <div style="flex:1">
        <div class="label">Full Name</div>
        <div class="value">{{ $patientName }}</div>
      </div>
      <div style="flex:1">
        <div class="label">Patient ID</div>
        <div class="value">{{ $patientId }}</div>
      </div>
      <div style="flex:1">
        <div class="label">Age / Gender</div>
        <div class="value">{{ $age }} Yrs / {{ $gender }}</div>
      </div>
    </div>

    <div class="row" style="margin-top:10px;">
      <div style="flex:1">
        <div class="label">Department</div>
        <div class="value">{{ $dept }}</div>
      </div>
      <div style="flex:1">
        <div class="label">Admission Date</div>
        <div class="value">{{ $admission }}</div>
      </div>
      <div style="flex:1">
        <div class="label">Blood Type</div>
        <div class="value">{{ $blood }}</div>
      </div>
    </div>

    <div style="margin-top:10px;">
      <b>Flags:</b>
      {{ $transfer->penicillin_allergy ? 'Penicillin Allergy' : '' }}
      {{ ($transfer->penicillin_allergy && $transfer->high_fall_risk) ? ' • ' : '' }}
      {{ $transfer->high_fall_risk ? 'High Fall Risk' : '' }}
      @if(!$transfer->penicillin_allergy && !$transfer->high_fall_risk)
        <span class="muted">No risk flags</span>
      @endif
    </div>
  </div>

  <div class="box">
    <div class="title" style="font-size:14px;">Clinical Summary</div>
    <div>
      <div class="label">Reason for Transfer</div>
      <div class="value">{{ $transfer->reason_for_transfer ?: '—' }}</div>
    </div>
    <div style="margin-top:10px;">
      <div class="label">Primary Diagnosis</div>
      <div class="value">{{ $transfer->primary_diagnosis ?: '—' }}</div>
    </div>
    <div style="margin-top:10px;">
      <div class="label">Clinical History & Findings</div>
      <div>{{ $transfer->medical_summary ?: '—' }}</div>
    </div>
  </div>

  <div class="box">
    <div class="title" style="font-size:14px;">Destination & Transport</div>

    <div class="row">
      <div style="flex:1">
        <div class="label">Target Facility</div>
        <div class="value">{{ $transfer->destination_hospital ?: '—' }}</div>

        <div style="margin-top:10px;" class="label">Destination Dept/Unit</div>
        <div class="value">{{ $transfer->destination_dept_unit ?: '—' }}</div>

        <div style="margin-top:10px;" class="label">Receiving Physician</div>
        <div class="value">{{ $transfer->receiving_doctor_name ?: '—' }}</div>
        @if($transfer->receiving_phone)
          <div class="muted"><b>Phone:</b> {{ $transfer->receiving_phone }}</div>
        @endif
      </div>

      @php
        $mode = $transfer->transport_mode ?? 'als_ambulance';
        $equip = [];
        if ($transfer->continuous_oxygen) $equip[] = 'Continuous Oxygen';
        if ($transfer->cardiac_monitoring) $equip[] = 'Cardiac Monitoring';
      @endphp

      <div style="flex:1">
        <div class="label">Transport Mode</div>
        <div class="value">{{ $mode }}</div>

        <div style="margin-top:10px;" class="label">Equipment</div>
        <div class="value">{{ count($equip) ? implode(' • ', $equip) : '—' }}</div>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="title" style="font-size:14px;">Attachments Summary</div>
    @if(!empty($atts) && count($atts))
      <ul>
        @foreach($atts as $item)
          <li>{{ $item }}</li>
        @endforeach
      </ul>
    @else
      <div class="muted">No attachments</div>
    @endif
  </div>

  @php
    $physicianName = optional($transfer->primaryPhysician)->name ?? '—';
  @endphp

  <hr>

  <div class="row">
    <div style="flex:1">
      <b>Physician's Signature</b><br>
      <span class="muted">{{ $physicianName !== '—' ? 'Dr. ' . $physicianName : '—' }}</span>
    </div>
    <div style="flex:1; text-align:right;">
      <b>Receiving Facility Approval</b><br>
      <span class="muted">Representative Signature</span>
    </div>
  </div>

  <div style="margin-top:14px; text-align:center;" class="muted">
    Confidential Medical Record - Form Generated by Helper Clinic CMS
  </div>

</body>
</html>
