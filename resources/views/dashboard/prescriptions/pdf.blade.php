<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Prescription PDF</title>

  <style>
    /* DomPDF safe styles */
    * { box-sizing: border-box; }

    body{
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #111827;
      margin: 18px;
    }

    .wrap{
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 18px;
    }

    .top{
      display: block;
      width: 100%;
      margin-bottom: 12px;
    }

    .clinic{
      display: inline-block;
      width: 68%;
      vertical-align: top;
    }

    .meta{
      display: inline-block;
      width: 31%;
      vertical-align: top;
      text-align: right;
    }

    .title{
      font-size: 18px;
      font-weight: 800;
      margin: 0 0 3px 0;
    }

    .subtitle{
      color: #6b7280;
      font-size: 12px;
      margin: 0;
    }

    .contact{
      margin-top: 8px;
      color: #374151;
      line-height: 1.6;
    }

    .chip{
      display: inline-block;
      padding: 6px 10px;
      border-radius: 999px;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      font-weight: 700;
      font-size: 11px;
      margin-bottom: 10px;
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
      line-height: 1.6;
    }

    .row{
      display: block;
      width: 100%;
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
      font-weight: 800;
      color: #111827;
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
      font-weight: 800;
      color: #111827;
      text-align: right;
    }

    table.rx{
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      overflow: hidden;
    }

    table.rx thead th{
      background: #f3f4f6;
      padding: 10px 8px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .3px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }

    table.rx tbody td{
      padding: 9px 8px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: top;
    }

    table.rx tbody tr:last-child td{
      border-bottom: 0;
    }

    ul.clean{
      margin: 6px 0 0 0;
      padding-left: 16px;
    }

    .terms{
      color: #374151;
      background: #fff;
    }

    .footer{
      text-align: center;
      margin-top: 16px;
    }

    .sign{
      display: inline-block;
      border: 1px dashed #c7d2fe;
      background: #eef2ff;
      padding: 10px 14px;
      border-radius: 12px;
      text-align: center;
    }

    .sign .dr{
      font-weight: 900;
      letter-spacing: .6px;
      font-size: 12px;
    }

    .sign .lbl{
      color: #6b7280;
      font-size: 11px;
      font-weight: 700;
      margin-top: 3px;
    }

    .thanks{
      color: #6b7280;
      margin-top: 10px;
      font-size: 11px;
    }
  </style>
</head>

<body>
@php
  // ====== Settings / Clinic ======
  $clinicName  = $setting->name   ?? 'Helper Clinic';
  $clinicAddr  =$setting->address ?? 'Nazer, Eltar3a Street';
  $clinicPhone = $setting->phone  ?? '01221604325';
  $clinicEmail = $setting->email  ?? 'memamo0338@helperclinic.com';

  // ====== RX / Patient / Doctor ======
  $patientName  = $rx->patientUser->name ?? '-';
  $patientId    = $rx->patientUser->id ?? null;
  $doctorName   = $rx->doctor?->user?->name ?? 'Doctor';

  $date         = optional($rx->created_at)->format('M d, Y');
  $rxCode       = 'RX-' . str_pad($rx->id, 6, '0', STR_PAD_LEFT);

  $diagnosis    = $rx->diagnosis ?? '-';

  // ====== Arrays safe decode ======
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
@endphp

  <div class="wrap">

    <div class="top">
      <div class="clinic">
        <div class="title">{{ $clinicName }}</div>
        <p class="subtitle">Medical Prescription (PDF)</p>

        <div class="contact">
          <div><span class="label">Address:</span> <span class="val" style="font-weight:700">{{ $clinicAddr ?? '-' }}</span></div>
          <div><span class="label">Phone:</span> <span class="val" style="font-weight:700">{{ $clinicPhone }}</span></div>
          <div><span class="label">Email:</span> <span class="val" style="font-weight:700">{{ $clinicEmail }}</span></div>
        </div>
      </div>

      <div class="meta">
        <div class="chip">Verified Document</div>

        <table class="meta-table">
          <tr>
            <td class="meta-key">Date</td>
            <td class="meta-val">{{ $date }}</td>
          </tr>
          <tr>
            <td class="meta-key">Prescription ID</td>
            <td class="meta-val">{{ $rxCode }}</td>
          </tr>
        </table>
      </div>
    </div>

    <hr>

    <div class="section-title">Patient Information</div>
    <div class="box">
      <div class="row">
        <div class="col">
          <div><span class="label">Patient Name:</span> <span class="val">{{ $patientName }}</span></div>
          <div><span class="label">Patient ID:</span> <span class="val">#{{ $patientId ?? '-' }}</span></div>
        </div>
        <div class="col" style="text-align:right;">
          <div><span class="label">Doctor:</span> <span class="val">Dr. {{ $doctorName }}</span></div>
        </div>
      </div>
    </div>

    <div class="section-title">Diagnosis</div>
    <div class="box">
      {{ $diagnosis }}
    </div>

    @if(count($medicines))
      <div class="section-title">Rx Medications</div>

      <table class="rx">
        <thead>
          <tr>
            <th style="width:32%;">Medicine Name</th>
            <th style="width:18%;">Dosage</th>
            <th style="width:18%;">Duration</th>
            <th style="width:32%;">Notes</th>
          </tr>
        </thead>
        <tbody>
          @foreach($medicines as $index => $medicine)
            <tr>
              <td><b>{{ $medicine }}</b></td>
              <td>{{ $dosages[$index] ?? '-' }}</td>
              <td>{{ $durations[$index] ?? '-' }}</td>
              <td>{{ $notesArr[$index] ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    @if(count($rumors))
      <div class="section-title">Radiology</div>
      <div class="box">
        <ul class="clean">
          @foreach($rumors as $item)
            <li>{{ $item }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(count($analyses))
      <div class="section-title">Lab Analysis</div>
      <div class="box">
        <ul class="clean">
          @foreach($analyses as $item)
            <li>{{ $item }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="section-title">Terms & Conditions</div>
    <div class="box terms">
      Please follow the prescribed treatment exactly as directed by your physician.<br>
      Do not stop or modify the medication without medical consultation.<br>
      In case of any side effects, contact the clinic immediately.
    </div>

    <div class="footer">
      <div class="sign">
        <div class="dr">DR : {{ strtoupper($doctorName) }}</div>
        <div class="lbl">Doctor’s Signature</div>
      </div>

      <div class="thanks">
        Thank you for choosing <b>{{ $clinicName }}</b>
      </div>
    </div>

  </div>
</body>
</html>
