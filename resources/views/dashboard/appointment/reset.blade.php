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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('CSS/reset.css') }}" />
</head>

<body class="tk-body">

  <div class="tk-wrap container py-5">

    <!-- Print Button -->
    <div class="d-flex justify-content-center mb-4 tk-no-print">
      <button id="printBtn" class="btn tk-print-btn">
        <i class="bi bi-printer me-2"></i> Print Ticket
      </button>
    </div>

    <!-- Ticket -->
    <section class="tk-ticket mx-auto" id="ticket">
      <!-- Top Logo -->
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

      <!-- Big Number -->
      <div class="tk-number-wrap text-center">
        <div class="tk-number">5</div>
        <div class="tk-number-label">NUMBER</div>
      </div>

      <div class="tk-dots"></div>

      <!-- Message -->
      <div class="tk-msg text-center">
        Please wait for your turn. You will be<br>
        called shortly.
      </div>

      <!-- Key Values -->
      <div class="tk-kv mt-4">
        <div class="tk-row">
          <div class="tk-key">DEPARTMENT:</div>
          <div class="tk-val">Dental Clinic</div>
        </div>
        <div class="tk-row">
          <div class="tk-key">DATE:</div>
          <div class="tk-val">24 Oct 2023</div>
        </div>
        <div class="tk-row">
          <div class="tk-key">TIME:</div>
          <div class="tk-val">10:45 AM</div>
        </div>
      </div>

      <div class="tk-dots mt-4"></div>

      <!-- Footer -->
      <div class="tk-thanks text-center">
        THANK YOU FOR CHOOSING<br>
        HELPER CLINIC
      </div>

      <!-- Barcode (visual) -->
      <div class="tk-barcode" aria-hidden="true"></div>
      <div class="tk-code text-center">20231024-0005-DEN</div>
    </section>

  </div>

 
</body>
</html>