<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Helper Clinic - Cash Report</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="{{ asset('CSS/printServices.css') }}" />

  <style>
    @media print { .no-print { display:none !important; } }
  </style>
</head>

<body class="rpt-body">

  <main class="rpt-page">

    <!-- Top actions (no print) -->
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
      </a>
      <button onclick="window.print()" class="btn btn-dark">
        <i class="fa-solid fa-print me-1"></i> Print
      </button>
    </div>

    <!-- Brand -->
    <div class="rpt-brand pb-4 m-5">
      <div class="rpt-logo">
        <i class="fa-solid fa-hospital"></i>
      </div>
      <div class="rpt-brand-name">{{$setting->name}}</div>
    </div>

    <!-- Title row -->
    <div class="rpt-title-row m-5">
      <div class="rpt-title-left">
        <div class="rpt-title">CLINIC SERVICES &amp; CASH REPORT</div>
        <div class="rpt-subtitle">
          Financial Operations Summary
          @if($day || $q)
            <span class="rpt-muted">
              —
              @if($day) Date: {{ $day }} @endif
              @if($day && $q) | @endif
              @if($q) Search: "{{ $q }}" @endif
            </span>
          @endif
        </div>
      </div>

      <div class="rpt-title-right">
        <div class="rpt-meta"><span>REPORT ID:</span> {{ $reportId }}</div>
        <div class="rpt-meta">
          <span>Generated on:</span> {{ $generatedAt->format('M d, Y - H:i A') }}
        </div>
      </div>
    </div>

    <!-- Table card -->
    <section class="rpt-card m-5">
      <div class="table-responsive">
        <table class="table rpt-table">
          <thead>
            <tr>
              <th>SERVICE NAME</th>
              <th>Cash In (EGP)</th>
              <th>Cash Out (EGP)</th>
              <th>DATE</th>
              <th>ADDED BY</th>
            </tr>
          </thead>

          <tbody>
            @forelse($movements as $m)
              <tr>
                <td>{{ $m->service }}</td>
                <td class="rpt-num">{{ number_format((float)$m->cash, 2) }}</td>
                <td class="rpt-num">{{ number_format((float)$m->cash_out, 2) }}</td>
                <td class="rpt-muted">{{ optional($m->created_at)->format('Y-m-d') }}</td>
                <td class="rpt-muted">{{ $m->creator->name ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 rpt-muted">
                  No results for this filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <!-- Summary cards -->
  <section class="rpt-summaries m-5">
  <div class="row g-4 justify-content-center">

    {{-- TOTAL CASH IN --}}
    <div class="col-12 col-md-4">
      <div class="rpt-sum-card">
        <div class="rpt-sum-label">TOTAL CASH IN</div>
        <div class="rpt-sum-value">
          {{ number_format($totalCashIn, 2) }} <span>EGP</span>
        </div>
      </div>
    </div>

    {{-- TOTAL CASH OUT --}}
    <div class="col-12 col-md-4">
      <div class="rpt-sum-card">
        <div class="rpt-sum-label">TOTAL CASH OUT</div>
        <div class="rpt-sum-value text-danger">
          {{ number_format($totalCashOut, 2) }} <span>EGP</span>
        </div>
      </div>
    </div>

    {{-- NET CASH --}}
    <div class="col-12 col-md-4">
      <div class="rpt-sum-card rpt-sum-highlight">
        <div class="rpt-sum-label">NET CASH</div>
        <div class="rpt-sum-value rpt-sum-blue">
          {{ number_format($netCash, 2) }} <span>EGP</span>
        </div>
      </div>
    </div>

  </div>
</section>


  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Optional: Auto print when opened --}}
  {{-- <script>window.onload = () => window.print();</script> --}}
</body>
</html>
