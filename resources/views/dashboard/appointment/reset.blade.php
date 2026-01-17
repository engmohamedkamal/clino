<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Helper Clinic - Queue Ticket</title>

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

  {{-- Print Fixes --}}
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
  </style>
</head>

<body class="tk-body">

  @php
    $queueNo = request('no')
      ?? $appointment->day_no
      ?? $appointment->queue_no
      ?? $appointment->id;

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

    $ticketCode =
      ($date ? \Carbon\Carbon::parse($date)->format('Ymd') : now()->format('Ymd'))
      . '-' . str_pad((string) $appointment->id, 4, '0', STR_PAD_LEFT);
  @endphp

  <div class="tk-wrap container py-5">

    {{-- Print Button --}}
    <div class="d-flex justify-content-center mb-4 tk-no-print">
      <button id="printBtn" class="btn tk-print-btn" type="button">
        <i class="bi bi-printer me-2"></i> Print Ticket
      </button>
    </div>

    {{-- Ticket --}}
    <section class="tk-ticket mx-auto" id="ticket">

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

      <div class="tk-divider"></div>

      {{-- Queue Number --}}
      <div class="tk-number-wrap text-center">
        <div class="tk-number">{{ $queueNo }}</div>
        <div class="tk-number-label">NUMBER</div>
      </div>

      <div class="tk-dots"></div>

      {{-- Message --}}
      <div class="tk-msg text-center">
        Please wait for your turn. You will be<br>
        called shortly.
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

      // ✅ Generate QR (login page + ticket code)
      const qrEl = document.getElementById('tkQr');
      if (qrEl && window.QRCode) {
        const qrData = @json(url('/') . '?ticket=' . $ticketCode);

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
