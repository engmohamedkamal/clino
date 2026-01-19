@extends('layouts.dash')
@section('dash-content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  .ds-wrap{ font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial; }
  .ds-head{
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:16px;
  }
  .ds-title{ margin:0; font-weight:800; letter-spacing:.2px; }
  .ds-sub{ color:#6b7280; margin-top:4px; }

  .ds-card{
    border:1px solid #e9eef6; border-radius:16px; background:#fff;
    box-shadow:0 10px 28px rgba(16,24,40,.06);
  }
  .ds-stat{
    padding:16px; display:flex; align-items:center; justify-content:space-between; gap:14px;
  }
  .ds-stat .k{ color:#6b7280; font-weight:600; font-size:12px; letter-spacing:.08em; text-transform:uppercase; }
  .ds-stat .v{ font-weight:800; font-size:22px; margin-top:2px; }
  .ds-ico{
    width:44px; height:44px; border-radius:14px; display:grid; place-items:center;
    background:#f5f9ff; color:#1C5197; font-size:20px; flex:0 0 auto;
    border:1px solid #e9eef6;
  }

  .ds-filter{ display:flex; gap:10px; flex-wrap:wrap; align-items:end; }
  .ds-filter .form-label{ font-weight:700; font-size:12px; color:#374151; }
  .ds-filter .form-control, .ds-filter .form-select{
    border-radius:12px; border:1px solid #e5e7eb; padding:.6rem .8rem;
  }
  .ds-btn{
    border-radius:12px; padding:.62rem 1rem; font-weight:800;
    background:#1C5197; border:1px solid #1C5197; color:#fff;
  }
  .ds-btn:hover{ filter:brightness(.96); }
  .ds-btn-soft{
    border-radius:12px; padding:.62rem 1rem; font-weight:800;
    background:#fff; border:1px solid #e5e7eb; color:#111827;
  }

  .ds-table thead th{ font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.08em; }
  .ds-pill{
    display:inline-flex; align-items:center; gap:6px;
    padding:.35rem .7rem; border-radius:999px; font-weight:800; font-size:12px;
    border:1px solid #e5e7eb; background:#fff;
  }
  .ds-pill-ok{ background:#ecfdf3; border-color:#bbf7d0; color:#166534; }
  .ds-pill-warn{ background:#fff7ed; border-color:#fed7aa; color:#9a3412; }

  @media print {
    .ds-no-print{ display:none !important; }
    body{ background:#fff !important; }
    .ds-card{ box-shadow:none !important; }
  }
</style>

<div class="ds-wrap container-fluid py-3 py-md-4">

  <div class="ds-head">
    <div>
      <h3 class="ds-title">Day Summary</h3>
      <div class="ds-sub">
        Status: <span class="ds-pill ds-pill-ok">completed</span>
        @if($doctorName) • Doctor: <strong>{{ $doctorName }}</strong> @endif
      </div>
    </div>

    <div class="ds-filter ds-no-print">
      <form method="GET" action="{{ route('day.summary') }}" class="d-flex gap-2 flex-wrap align-items-end">
        <div>
          <label class="form-label mb-1">Date</label>
          <input type="date" name="date" class="form-control" value="{{ request('date', $date) }}">
        </div>

        @if(auth()->user()->role === 'admin')
          <div>
            <label class="form-label mb-1">Doctor</label>
            <select name="doctor_name" class="form-select">
              <option value="">All doctors</option>
              @foreach($doctorNames as $dn)
                <option value="{{ $dn }}" {{ (string)request('doctor_name', $doctorName) === (string)$dn ? 'selected' : '' }}>
                  {{ $dn }}
                </option>
              @endforeach
            </select>
          </div>
        @endif

        <button class="ds-btn" type="submit">Apply</button>
        <button class="ds-btn-soft" type="button" onclick="window.print()">Print</button>
      </form>
    </div>
  </div>

  {{-- Stats --}}
  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <div class="ds-card ds-stat">
        <div>
          <div class="k">Completed Appointments</div>
          <div class="v">{{ $totalCompleted }}</div>
        </div>
        <div class="ds-ico"><i class="bi bi-check2-circle"></i></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="ds-card ds-stat">
        <div>
          <div class="k">Visit Types</div>
          <div class="v">{{ count($byVisitType) }}</div>
        </div>
        <div class="ds-ico"><i class="bi bi-grid-1x2"></i></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="ds-card ds-stat">
        <div>
          <div class="k">Total Revenue</div>
          <div class="v">{{ number_format($grandTotalPrice, 2) }}</div>
        </div>
        <div class="ds-ico"><i class="bi bi-cash-coin"></i></div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- By visit type --}}
    <div class="col-12 col-lg-5">
      <div class="ds-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="fw-bold" style="font-size:16px;">By Visit Type</div>
            <div class="text-muted" style="font-size:13px;">Counts & total price for the selected day</div>
          </div>
          <span class="ds-pill ds-pill-warn">{{ $date }}</span>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0 ds-table">
            <thead>
              <tr>
                <th>Type</th>
                <th class="text-end">Count</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($byVisitType as $row)
                <tr>
                  <td class="fw-semibold">{{ $row['visit_type'] }}</td>
                  <td class="text-end">{{ $row['items_count'] }}</td>
                  <td class="text-end fw-bold">{{ number_format((float)$row['total_price'], 2) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">
                    No completed appointments found for this day.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

    {{-- Appointments list --}}
    <div class="col-12 col-lg-7">
      <div class="ds-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="fw-bold" style="font-size:16px;">Completed Appointments</div>
            <div class="text-muted" style="font-size:13px;">Details (patient, time, visit types)</div>
          </div>
          <span class="ds-pill ds-pill-ok">completed</span>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0 ds-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Time</th>
                <th>Visit Types</th>
              </tr>
            </thead>
            <tbody>
              @forelse($appointments as $i => $ap)
                @php
                  $vts = $ap->visit_types;
                  if (is_string($vts)) $vts = json_decode($vts, true) ?: [];
                  if (!is_array($vts)) $vts = [];
                @endphp
                <tr>
                  <td class="text-muted">#{{ $ap->id }}</td>
                  <td class="fw-semibold">{{ $ap->patient_name }}</td>
                  <td>{{ \Carbon\Carbon::parse($ap->appointment_time)->format('h:i A') }}</td>
                  <td>
                    @forelse($vts as $it)
                      @php
                        $t = $it['type'] ?? '';
                        $p = $it['price'] ?? null;
                      @endphp
                      @if($t)
                        <span class="ds-pill">
                          {{ $t }}
                          @if($p !== null) • {{ number_format((float)$p, 0) }} @endif
                        </span>
                      @endif
                    @empty
                      <span class="text-muted">—</span>
                    @endforelse
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No completed appointments found for this day.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

</div>

@endsection
