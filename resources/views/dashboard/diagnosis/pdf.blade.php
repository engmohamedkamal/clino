<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Diagnosis PDF</title>

  <style>
    /* DomPDF-safe styling */
    *{ box-sizing:border-box; }

    body{
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #111827;
      margin: 18px;
      line-height: 1.5;
    }

    .wrap{
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 18px;
    }

    .top{
      width: 100%;
      margin-bottom: 12px;
    }

    .left{
      display: inline-block;
      width: 66%;
      vertical-align: top;
    }

    .right{
      display: inline-block;
      width: 33%;
      vertical-align: top;
      text-align: right;
    }

    .title{
      margin: 0 0 4px 0;
      font-size: 18px;
      font-weight: 800;
      letter-spacing: .2px;
    }

    .subtitle{
      margin: 0;
      color: #6b7280;
      font-size: 12px;
    }

    .contact{
      margin-top: 10px;
      color: #374151;
    }
    .contact div{ margin: 2px 0; }

    .chip{
      display: inline-block;
      padding: 6px 10px;
      border-radius: 999px;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      font-weight: 800;
      font-size: 11px;
      margin-bottom: 10px;
    }

    .meta-table{
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      font-size: 12px;
    }
    .meta-table td{
      padding: 6px 0;
      border-bottom: 1px dashed #e5e7eb;
    }
    .meta-table tr:last-child td{ border-bottom: 0; }

    .meta-key{
      color: #6b7280;
      font-weight: 700;
    }
    .meta-val{
      font-weight: 900;
      color: #111827;
      text-align: right;
    }

    hr{
      border: 0;
      border-top: 1px solid #e5e7eb;
      margin: 14px 0;
    }

    .section-title{
      margin: 14px 0 8px 0;
      font-size: 13px;
      font-weight: 900;
      color: #111827;
      padding-bottom: 6px;
      border-bottom: 1px dashed #e5e7eb;
    }

    .box{
      border: 1px solid #eef2f7;
      border-radius: 12px;
      padding: 12px;
      background: #f9fafb;
    }

    .grid{
      width: 100%;
      display: block;
    }

    .col{
      display: inline-block;
      width: 49%;
      vertical-align: top;
    }

    .label{
      color: #6b7280;
      font-weight: 700;
    }

    .val{
      font-weight: 900;
      color: #111827;
    }

    .note{
      background: #fff;
      border: 1px solid #eef2f7;
      border-radius: 12px;
      padding: 12px;
      margin-top: 10px;
    }

    .note .note-label{
      font-weight: 900;
      color: #111827;
      margin-bottom: 6px;
    }

    .sign-row{
      width: 100%;
      margin-top: 14px;
    }

    .sign{
      display: inline-block;
      width: 49%;
      border: 1px dashed #c7d2fe;
      background: #eef2ff;
      padding: 10px 14px;
      border-radius: 12px;
      vertical-align: top;
    }

    .sign .line{
      font-weight: 900;
      letter-spacing: .3px;
      margin-bottom: 4px;
    }

    .sign .small{
      color: #6b7280;
      font-size: 11px;
      font-weight: 700;
    }

    .qr{
      display: inline-block;
      width: 100%;
      text-align: right;
      margin-top: 12px;
    }

    .footer{
      text-align: center;
      margin-top: 14px;
      color: #6b7280;
      font-size: 11px;
    }
  </style>
</head>

<body>
@php
  $clinicName  = $setting->name ?? 'Helper Clinic';
  $clinicPhone = $setting->phone ?? '—';
  $clinicAddr  = $setting->address ?? '—';
  $clinicEmail = $setting->email ?? '—';

  // passed from controller (recommended), but fallback:
  $patientName = $patientName ?? ($diagnosis->patient?->name ?? ($diagnosis->patient_name ?? '—'));
  $patientId   = $patientId ?? ($diagnosis->patient?->id ?? ($diagnosis->patient_id ?? '—'));
  $doctorName  = $doctorName ?? ($diagnosis->creator?->name ?? '—');

  $diagId = $diagId ?? ('DX-' . str_pad($diagnosis->id, 6, '0', STR_PAD_LEFT));
  $issued = $issued ?? optional($diagnosis->created_at)->format('M d, Y H:i');

  $publicDiagnosis  = $diagnosis->public_diagnosis ?: '—';
  $privateDiagnosis = $diagnosis->private_diagnosis ?: '—';

  $canSeePrivate = $canSeePrivate ?? false;
@endphp

  <div class="wrap">

    <div class="top">
      <div class="left">
        <div class="title">Diagnosis Report</div>
        <p class="subtitle">Confidential medical document</p>

        <div class="contact">
          <div><span class="label">Hospital:</span> <span class="val" style="font-weight:800">{{ $clinicName }}</span></div>
          <div><span class="label">Phone:</span> <span class="val" style="font-weight:800">{{ $clinicPhone }}</span></div>
          <div><span class="label">Address:</span> <span class="val" style="font-weight:800">{{ $clinicAddr }}</span></div>
          <div><span class="label">Email:</span> <span class="val" style="font-weight:800">{{ $clinicEmail }}</span></div>
        </div>
      </div>

      <div class="right">
        <div class="chip">Diagnosis Record</div>

        <table class="meta-table">
          <tr>
            <td class="meta-key">Diagnosis ID</td>
            <td class="meta-val">{{ $diagId }}</td>
          </tr>
          <tr>
            <td class="meta-key">Issued</td>
            <td class="meta-val">{{ $issued }}</td>
          </tr>
        </table>
      </div>
    </div>

    <hr>

    <div class="section-title">Patient Information</div>
    <div class="box">
      <div class="grid">
        <div class="col">
          <div><span class="label">Full Name:</span> <span class="val">{{ $patientName }}</span></div>
          <div><span class="label">Patient ID:</span> <span class="val">#{{ $patientId }}</span></div>
        </div>
        <div class="col" style="text-align:right;">
          <div><span class="label">Created By:</span> <span class="val">Dr / {{ $doctorName }}</span></div>
          <div><span class="label">Record Date:</span> <span class="val">{{ optional($diagnosis->created_at)->format('M d, Y') }}</span></div>
        </div>
      </div>
    </div>

    <div class="section-title">Diagnosis Summary</div>

    <div class="note">
      <div class="note-label">Public Diagnosis</div>
      <div>{{ $publicDiagnosis }}</div>
    </div>

    @if($canSeePrivate)
      <div class="note">
        <div class="note-label">Private Diagnosis</div>
        <div>{{ $privateDiagnosis }}</div>
      </div>
    @endif

    <div class="section-title">Signatures</div>
    <div class="sign-row">
      <div class="sign">
        <div class="line">Physician's Signature</div>
        <div class="small">Dr / {{ $doctorName }}</div>
      </div>

    </div>

    <hr>

    <div class="footer">
      Confidential Medical Record — Generated by Helper Clinic CMS
    </div>

  </div>
</body>
</html>
