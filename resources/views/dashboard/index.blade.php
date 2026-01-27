@extends('layouts.dash')
@section('dash-content')

@php
  $chartLabels = $chartLabels ?? [];
  $chartData   = $chartData ?? [];

  $weeklyLabels = $weeklyLabels ?? [];
  $weeklyData   = $weeklyData ?? [];

  $monthlyLabels = $monthlyLabels ?? [];
  $monthlyData   = $monthlyData ?? [];

  $yearlyLabels = $yearlyLabels ?? [];
  $yearlyData   = $yearlyData ?? [];
@endphp

<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="page-title">Dashboard</div>
    </div>

    
  </header>

  <section class="content-area">
    <div class="row g-3 h-100">

      <div class="col-12">
        <div class="d-flex flex-column gap-3 h-100">

          <!-- Stats (4 cards) -->
          <div class="row g-3">
            <div class="col-12 col-md-6 col-xl-3">
              <div class="stat-card vertical">
                <div class="stat-ico stat-ico-primary">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-bottom">
                  <div class="stat-value">{{ number_format($totalPatients) }}</div>
                  <div class="stat-delta text-muted">All time</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="stat-card vertical">
                <div class="stat-ico stat-ico-green">
                  <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div class="stat-label">Total Doctors</div>
                <div class="stat-bottom">
                  <div class="stat-value">{{ number_format($totalDoctors) }}</div>
                  <div class="stat-delta text-muted">Registered</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="stat-card vertical">
                <div class="stat-ico stat-ico-orange">
                  <i class="fa-regular fa-calendar"></i>
                </div>
                <div class="stat-label">Today Appointments</div>
                <div class="stat-bottom">
                  <div class="stat-value">{{ number_format($todayAppointments) }}</div>
                  <div class="stat-delta text-muted">{{ now()->format('M d, Y') }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="stat-card vertical">
                <div class="stat-ico stat-ico-primary">
                  <i class="fa-solid fa-briefcase-medical"></i>
                </div>
                <div class="stat-label">Active Services</div>
                <div class="stat-bottom">
                  <div class="stat-value">{{ number_format($activeServices) }}</div>
                  <div class="stat-delta text-muted">Available</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chart + Upcoming -->
          <div class="row g-3">
            <div class="col-12 col-xl-8">
              <div class="cardx h-100">
                <div class="cardx-head">
                  <div class="cardx-title">Overview</div>

                  <div class="d-flex align-items-center gap-2">
                    <div class="small text-muted">Sort by</div>
                    <select id="chartRange" class="form-select form-select-sm select-compact" aria-label="Sort">
                      <option value="monthly" selected>Monthly</option>
                      <option value="weekly">Weekly</option>
                      <option value="yearly">Yearly</option>
                    </select>
                  </div>
                </div>

                <div class="p-3">
                  <div style="height: 320px;">
                    <canvas id="appointmentsChart"></canvas>
                  </div>
                </div>

              </div>
            </div>

        <div class="col-12 col-xl-4">
  <div class="cardx h-100">
    <div class="cardx-head">
      <div class="cardx-title">Upcoming Appointments</div>
    </div>

    <div class="p-3">
      @forelse($upcomingAppointments as $a)

        <div class="appt-item mb-2">
          {{-- Top row: time + date + arrow --}}
          <div class="appt-time d-flex align-items-center gap-2">
            <span class="dot"></span>

            <span>
              {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i a') }}
            </span>

            <span class="text-muted">•</span>

            <span class="text-muted">
              {{ \Carbon\Carbon::parse($a->appointment_date)->format('M d, Y') }}
            </span>

            <a href="{{ route('appointments.singleShow', $a->id) }}"
               class="ms-auto text-decoration-none text-muted"
               aria-label="View appointment">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          </div>

          <div class="appt-title">{{ $a->patient_name ?? '-' }}</div>
          <div class="appt-sub">{{ $a->doctor_name ?? '-' }}</div>
        </div>

      @empty
        <div class="text-muted">No upcoming appointments.</div>
      @endforelse
    </div>
  </div>
</div>


          <!-- Latest Patients Table -->
       <div class="cardx table-card">
  <div class="cardx-head">
    <div class="cardx-title">Latest Patients</div>
    <a href="{{ route('patients.index') }}" class="btn icon-btn icon-btn-sm" aria-label="more">
      <i class="fa-solid fa-ellipsis"></i>
    </a>
  </div>

  <div class="table-responsive table-wrap">
    <table class="table table-borderless align-middle mb-0 table-soft">
      <thead>
        <tr>
          <th>Patient Name</th>
          <th>ID_number</th>
          <th>Phone</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>

      <tbody>
        @forelse($latestPatients as $p)
          <tr>
            {{-- Name --}}
            <td>
              {{ $p->name }}
            </td>

            {{-- Email --}}
            <td class="text-muted">{{ $p->id_number ?? '-' }}</td>

            {{-- Phone --}}
            <td class="text-muted">{{ $p->phone ?? '-' }}</td>

       

            {{-- Actions --}}
            <td class="text-end">
              @if($p->source === 'patients')
                {{-- Edit Patient --}}
                <a class="btn action-ico" href="{{ url('/patients/'.$p->id.'/edit') }}">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>

                {{-- Delete Patient --}}
                <form action="{{ route('patients.destroy', $p->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn action-ico" type="submit"
                          onclick="return confirm('Delete this patient?')">
                    <i class="fa-regular fa-trash-can"></i>
                  </button>
                </form>
              @else
                {{-- Edit User --}}
                <a class="btn action-ico" href="{{ url('/users/'.$p->id.'/edit') }}">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              No patients found.
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
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const datasets = {
      weekly: {
        labels: @json($weeklyLabels),
        data: @json($weeklyData)
      },
      monthly: {
        labels: @json($monthlyLabels),
        data: @json($monthlyData)
      },
      yearly: {
        labels: @json($yearlyLabels),
        data: @json($yearlyData)
      }
    };

    const canvas = document.getElementById('appointmentsChart');
    if (!canvas) return;

    let chart;

    function render(rangeKey) {
      const payload = datasets[rangeKey] || datasets.monthly;

      if (chart) chart.destroy();

      chart = new Chart(canvas, {
        type: 'line',
        data: {
          labels: payload.labels,
          datasets: [{
            label: 'Appointments',
            data: payload.data,
            tension: 0.35,
            fill: true,
            pointRadius: 3,
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: true }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1, precision: 0 }
            }
          }
        }
      });
    }

    // default monthly
    render('monthly');

    const select = document.getElementById('chartRange');
    select?.addEventListener('change', (e) => {
      render(e.target.value);
    });
  });
</script>

@endsection
