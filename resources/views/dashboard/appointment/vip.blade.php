<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Helper Clinic - VIP Queue Ticket</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800;900&display=swap"
    rel="stylesheet">

  <!-- Reset / Ticket CSS -->
  <link rel="stylesheet" href="{{ asset('CSS/reset.css') }}" />

  {{-- Print Fixes + VIP add-ons --}}
  <style>
    @media print {
      .tk-no-print { display: none !important; }
      body.tk-body { background: #fff !important; }
      .tk-wrap { padding: 0 !important; }
      .tk-ticket { margin: 0 auto !important; box-shadow: none !important; }
    }

    .tk-qr{
      width: 140px;
      height: 140px;
      margin: 10px auto 0;
      display: grid;
      place-items: center;
    }
    .tk-qr img,
    .tk-qr canvas{
      width: 140px !important;
      height: 140px !important;
    }

    /* ================= VIP Styling ================= */
    .tk-ticket.tk-vip{
      position: relative;
      border: 1px solid rgba(245, 158, 11, .35) !important;
      box-shadow: 0 18px 45px rgba(245, 158, 11, .12);
    }

    /* VIP ribbon (top right) */
    .tk-vip-ribbon{
      position: absolute;
      top: 14px;
      right: 14px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      font-weight: 800;
      font-size: 12px;
      letter-spacing: .3px;
      color: #92400e;
      background: rgba(245, 158, 11, .18);
      border: 1px solid rgba(245, 158, 11, .30);
    }
    .tk-vip-ribbon i{ font-size: 14px; }

    /* VIP badge under section title */
    .tk-vip-badge{
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 12px;
      border-radius: 999px;
      margin: 10px auto 0;
      font-weight: 800;
      font-size: 12px;
      color: #fff;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      box-shadow: 0 10px 25px rgba(245, 158, 11, .25);
    }

    /* Number glow */
    .tk-number-wrap.tk-vip-number .tk-number{
      color: #b45309;
      text-shadow: 0 6px 22px rgba(245, 158, 11, .35);
    }

    .tk-vip-priority{
      margin-top: 6px;
      font-weight: 800;
      font-size: 12px;
      letter-spacing: .6px;
      color: #b45309;
    }

    .tk-vip-note{
      margin-top: 10px;
      font-size: 12px;
      font-weight: 600;
      color: #6b7280;
      text-align: center;
    }

    .tk-vip-note strong{ color:#111827; }

    /* Print: keep VIP ribbon visible */
    @media print{
      .tk-vip-ribbon, .tk-vip-badge{ -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
  </style>
</head>

<body class="tk-body">

@php
  $date = $appointment->appointment_date ?? null;
  $time = $appointment->appointment_time ?? null;

  $dateLabel = $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '-';

  try {
    $timeLabel = $time ? \Carbon\Carbon::parse($time)->format('h:i A') : '-';
  } catch (\Exception $e) {
    $timeLabel = $time ?: '-';
  }

  // 🔗 Home / Login (Route::view('/', 'login'))
  $siteUrl = url('/');

  // ================= VIP Iterative No (per day + appointment time) =================
  // key uses doctor_id if available (preferred), else fallback to doctor_name
  $doctorKey = $appointment->doctor_id
    ?? (isset($appointment->doctor) ? $appointment->doctor->id : null)
    ?? ($appointment->doctor_name ?? 'doctor');

  $dateKey = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');

  // normalize time to HH:MM to avoid "09:00" vs "9:00 AM" differences
  try {
    $timeKey = $time ? \Carbon\Carbon::parse($time)->format('H:i') : '00:00';
  } catch (\Exception $e) {
    $timeKey = preg_replace('/\s+/', '', (string)$time) ?: '00:00';
  }

  // cache key: vip counter for THIS doctor + THIS day + THIS time
  $vipCounterKey = "vip:{$doctorKey}:{$dateKey}:{$timeKey}";

  // increment VIP number
  $queueNo = \Illuminate\Support\Facades\Cache::increment($vipCounterKey);

  // first time Cache::increment may return 1 or null depending on driver, so ensure starts at 1
  if (!$queueNo || $queueNo < 1) {
    \Illuminate\Support\Facades\Cache::put($vipCounterKey, 1, now()->addDays(2));
    $queueNo = 1;
  } else {
    // extend TTL so it doesn't expire during the day
    \Illuminate\Support\Facades\Cache::put($vipCounterKey, (int)$queueNo, now()->addDays(2));
  }

  // ✅ VIP ticket code (include vip queue number)
  $ticketCode =
    'VIP-' .
    ($date ? \Carbon\Carbon::parse($date)->format('Ymd') : now()->format('Ymd'))
    . '-' . $timeKey
    . '-' . str_pad((string) $queueNo, 3, '0', STR_PAD_LEFT);
@endphp

  <div class="tk-wrap container py-5">

    {{-- Print Button --}}
    <div class="d-flex justify-content-center mb-4 tk-no-print">
      <button id="printBtn" class="btn tk-print-btn" type="button">
        <i class="bi bi-printer me-2"></i> Print VIP Ticket
      </button>
    </div>

    {{-- Ticket --}}
    <section class="tk-ticket tk-vip mx-auto" id="ticket">

      {{-- VIP ribbon --}}
      <div class="tk-vip-ribbon" aria-label="VIP ticket">
        <i class="bi bi-stars"></i> VIP
      </div>

      {{-- Header --}}
      <div class="tk-top text-center">
        <div class="tk-logo">
          <i class="bi bi-bag-plus"></i>
        </div>
        <div class="tk-brand">HELPER CLINIC</div>
        <div class="tk-slogan">PRIMARY CARE EXCELLENCE</div>
      </div>

      <div class="tk-divider"></div>

      <div class="tk-section-title text-center">QUEUE TICKET</div>

      {{-- VIP badge --}}
      <div class="tk-vip-badge">
        <i class="bi bi-lightning-charge-fill"></i>
        VIP PRIORITY ACCESS
      </div>

      <div class="tk-divider"></div>

      {{-- Queue Number --}}
      <div class="tk-number-wrap tk-vip-number text-center">
        <div class="tk-number">{{ $queueNo }}</div>
        <div class="tk-number-label">NUMBER</div>
        <div class="tk-vip-priority">PRIORITY</div>
      </div>

      <div class="tk-dots"></div>

      {{-- Message --}}
      <div class="tk-msg text-center">
        Please wait for your turn. You will be<br>
        called shortly.
      </div>

      <div class="tk-vip-note">
        <strong>VIP:</strong> You will be served before standard queue.
      </div>

      {{-- Info --}}
      <div class="tk-kv mt-4">
        <div class="tk-row">
          <div class="tk-key">PATIENT:</div>
          <div class="tk-val">{{ $appointment->patient_name ?? '-' }}</div>
        </div>

        <div class="tk-row">
          <div class="tk-key">DOCTOR:</div>
          <div class="tk-val">{{ $appointment->doctor_name ?? '-' }}</div>
        </div>

        <div class="tk-row">
          <div class="tk-key">DATE:</div>
          <div class="tk-val">{{ $dateLabel }}</div>
        </div>

        <div class="tk-row">
          <div class="tk-key">TIME:</div>
          <div class="tk-val">{{ $timeLabel }}</div>
        </div>
      </div>

      <div class="tk-dots mt-4"></div>

      {{-- Thanks --}}
      <div class="tk-thanks text-center">
        THANK YOU FOR CHOOSING<br>
        HELPER CLINIC
      </div>

      {{-- QR Code --}}
      <a href="{{ $siteUrl }}" target="_blank" class="d-block text-decoration-none">
        <div id="tkQr" class="tk-qr" aria-hidden="true"></div>
        <div class="tk-code text-center">{{ $ticketCode }}</div>
      </a>

    </section>
  </div>

  <!-- QR Library -->
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('printBtn');
      if (btn) btn.addEventListener('click', () => window.print());

      // ✅ Generate QR (login page + ticket code + vip=1)
      const qrEl = document.getElementById('tkQr');
      if (qrEl && window.QRCode) {
        const qrData = @json(url('/') . '?ticket=' . $ticketCode . '&vip=1');

        new QRCode(qrEl, {
          text: qrData,
          width: 140,
          height: 140,
          correctLevel: QRCode.CorrectLevel.M
        });
      }

      // Auto Print
      const params = new URLSearchParams(window.location.search);
      if (params.get('autoprint') === '1') {
        setTimeout(() => window.print(), 200);
      }
    });
  </script>

</body>
</html>
