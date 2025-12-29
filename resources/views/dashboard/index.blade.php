@extends('layouts.dash')
@section('dash-content')
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

        <div class="search-wrap">
          <input class="form-control search-input" type="text" placeholder="Search type of keywords" />
          <i class="fa-solid fa-magnifying-glass search-ico"></i>
        </div>

        <button class="btn icon-btn" type="button" aria-label="notifications">
          <i class="fa-regular fa-bell"></i>
        </button>
      </header>


      <!-- Content (fits viewport, no page scroll) -->
      <section class="content-area">
        <div class="row g-3 h-100">

          <!-- Left column -->
          <div class="col-12 col-xl-8 h-100">
            <div class="d-flex flex-column gap-3 h-100">

              <!-- Stats -->
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <div class="stat-card vertical">
                    <div class="stat-ico stat-ico-primary">
                      <i class="fa-solid fa-sack-dollar"></i>
                    </div>

                    <div class="stat-label">Earnings</div>

                    <div class="stat-bottom">
                      <div class="stat-value">$23747</div>
                      <div class="stat-delta">
                        <i class="fa-solid fa-caret-up"></i> +502
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="stat-card vertical">
                    <div class="stat-ico stat-ico-orange">
                      <i class="fa-solid fa-sun"></i>
                    </div>

                    <div class="stat-label">New Patient</div>

                    <div class="stat-bottom">
                      <div class="stat-value">1,9</div>
                      <div class="stat-delta">
                        <i class="fa-solid fa-caret-up"></i> +100
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="stat-card vertical">
                    <div class="stat-ico stat-ico-green">
                      <i class="fa-solid fa-user-nurse"></i>
                    </div>

                    <div class="stat-label">New Appointment</div>

                    <div class="stat-bottom">
                      <div class="stat-value">153</div>
                      <div class="stat-delta">
                        <i class="fa-solid fa-caret-up"></i> +50
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chart -->
              <div class="cardx flex-grow-1">
                <div class="cardx-head">
                  <div class="cardx-title">Patient Visit</div>

                  <div class="d-flex align-items-center gap-2">
                    <div class="small text-muted">Sort by</div>
                    <select class="form-select form-select-sm select-compact" aria-label="Sort">
                      <option selected>Monthly</option>
                      <option>Weekly</option>
                      <option>Yearly</option>
                    </select>
                  </div>
                </div>

                <div class="chart-box">
                  <canvas id="visitChart"></canvas>
                </div>
              </div>

              <!-- Table -->
              <div class="cardx table-card">
                <div class="cardx-head">
                  <div class="cardx-title">Patient Data</div>
                  <button class="btn icon-btn icon-btn-sm" type="button" aria-label="more">
                    <i class="fa-solid fa-ellipsis"></i>
                  </button>
                </div>

                <div class="table-responsive table-wrap">
                  <table class="table table-borderless align-middle mb-0 table-soft">
                    <thead>
                      <tr>
                        <th>Patient name</th>
                        <th>Date In</th>
                        <th>Symptoms</th>
                        <th>Status</th>
                        <th class="text-end"></th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td><span class="avatar-sq"></span> Mostafa</td>
                        <td class="text-muted">Dec 18, 2024</td>
                        <td class="text-muted">Geriatrician</td>
                        <td><span class="status confirmed">Confirmed</span></td>
                        <td class="text-end">
                          <button class="btn action-ico" type="button"><i
                              class="fa-regular fa-pen-to-square"></i></button>
                          <button class="btn action-ico" type="button"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                      </tr>

                      <tr>
                        <td><span class="avatar-sq"></span> Zead</td>
                        <td class="text-muted">Dec 18, 2024</td>
                        <td class="text-muted">Internist</td>
                        <td><span class="status incoming">Incoming</span></td>
                        <td class="text-end">
                          <button class="btn action-ico" type="button"><i
                              class="fa-regular fa-pen-to-square"></i></button>
                          <button class="btn action-ico" type="button"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                      </tr>

                      <tr>
                        <td><span class="avatar-sq"></span> Ahmed</td>
                        <td class="text-muted">Dec 18, 2024</td>
                        <td class="text-muted">Neurologist</td>
                        <td><span class="status confirmed">Confirmed</span></td>
                        <td class="text-end">
                          <button class="btn action-ico" type="button"><i
                              class="fa-regular fa-pen-to-square"></i></button>
                          <button class="btn action-ico" type="button"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                      </tr>
                      <tr>
                        <td><span class="avatar-sq"></span> Ahmed</td>
                        <td class="text-muted">Dec 18, 2024</td>
                        <td class="text-muted">Neurologist</td>
                        <td><span class="status confirmed">Confirmed</span></td>
                        <td class="text-end">
                          <button class="btn action-ico" type="button"><i
                              class="fa-regular fa-pen-to-square"></i></button>
                          <button class="btn action-ico" type="button"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                      </tr>

                      <tr>
                        <td><span class="avatar-sq"></span> Mohamed</td>
                        <td class="text-muted">Dec 18, 2024</td>
                        <td class="text-muted">Cardiologist</td>
                        <td><span class="status cancelled">Cancelled</span></td>
                        <td class="text-end">
                          <button class="btn action-ico" type="button"><i
                              class="fa-regular fa-pen-to-square"></i></button>
                          <button class="btn action-ico" type="button"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                      </tr>

                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>

          <!-- Right column -->
          <div class="col-12 col-xl-4 h-100">
            <div class="d-flex flex-column gap-3 h-100">

              <!-- Profile -->
              <div class="cardx profile-card">
                <img class="profile-img" src="Images/Doctor.png" alt="Doctor" />
                <div class="profile-name">Dr. Mido Emam</div>

                <div class="profile-stats">
                  <div class="profile-stat">
                    <div class="k">Appointment</div>
                    <div class="v">4250</div>
                  </div>
                  <div class="profile-stat">
                    <div class="k">Total Patients</div>
                    <div class="v">32.1k</div>
                  </div>
                  <div class="profile-stat">
                    <div class="k">Rate</div>
                    <div class="v">4.8</div>
                  </div>
                </div>
              </div>

              <!-- Upcoming -->
              <div class="cardx flex-grow-1">
                <div class="cardx-head">
                  <div class="cardx-title">Upcoming Appointment</div>
                  <button class="btn icon-btn icon-btn-sm" type="button" aria-label="more">
                    <i class="fa-solid fa-ellipsis"></i>
                  </button>
                </div>

                <div class="date-accent">July 30, 2025</div>

                <div class="appt-item">
                  <div class="appt-time"><span class="dot"></span> 08:30 am - 10:30 am <i
                      class="fa-solid fa-chevron-right ms-auto"></i></div>
                  <div class="appt-title">Nurse Visit 20</div>
                  <div class="appt-sub">Dr. zead</div>
                </div>

                <div class="appt-item">
                  <div class="appt-time"><span class="dot"></span> 08:30 am - 10:30 am <i
                      class="fa-solid fa-chevron-right ms-auto"></i></div>
                  <div class="appt-title">Annual Visit 15</div>
                  <div class="appt-sub">Dr. Mostafa</div>
                </div>
              </div>

              <!-- Satisfaction -->
              <div class="cardx sat-card">
                <div class="cardx-title mb-2">Patient Satisfaction</div>

                <div class="donut-box">
                  <canvas id="satisfactionChart"></canvas>
                </div>

                <div class="sat-legend">
                  <div class="sat-row"><span class="dot dot-primary"></span> <span class="text-muted">Excellent</span>
                  </div>
                  <div class="sat-row"><span class="dot dot-green"></span> <span class="text-muted">Good</span></div>
                  <div class="sat-row"><span class="dot dot-yellow"></span> <span class="text-muted">poor</span></div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

    </main>
@endsection