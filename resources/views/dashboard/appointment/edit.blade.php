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
  </header>

  <!-- Content -->
  <section class="content-area">
    <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
      <div class="appointment-card">
        <h3 class="appointment-title mb-4">Update Appointment</h3>

        {{-- ERRORS --}}
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @php
          $oldDoctorName = old('doctor_name', $appointment->doctor_name);
          $oldDate = old('appointment_date', $appointment->appointment_date);
          $oldTime = old('appointment_time', $appointment->appointment_time);

          // ✅ new fields (edit)
          $oldVisitType  = old('visit_type', $appointment->visit_type ?? '');
          $oldVisitPrice = old('visit_price', $appointment->visit_price ?? '');

          $preDoctor = request('doctor'); // اختياري
          $isDoctorRole = auth()->check() && auth()->user()->role === 'doctor';
        @endphp

        <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
          @csrf
          @method('PUT')

          <div class="row g-3">

            {{-- LEFT COLUMN (Admin/Doctor فقط) --}}
            @if (auth()->user()->role !== 'patient')
              <div class="col-md-6">
<label class="form-label appointment-label" for="patient_name">
  Patient
</label>

<select id="patient_name" name="patient_name"
  class="form-select appointment-control @error('patient_name') is-invalid @enderror">

  <option value="">Select Patient</option>

  <option value="Visit"
    {{ old('patient_name', $appointment->patient_name) == 'Visit' ? 'selected' : '' }}>
    Visit
  </option>

  @foreach($patients as $p)
    <option value="{{ $p->name }}"
      {{ old('patient_name', $appointment->patient_name) == $p->name ? 'selected' : '' }}>
      {{ $p->name }}
    </option>
  @endforeach
</select>

@error('patient_name')
  <div class="invalid-feedback">{{ $message }}</div>
@enderror


                <div class="mb-3">
                  <label class="form-label appointment-label" for="patient_number">Patient Number</label>
                  <input
                    type="tel"
                    id="patient_number"
                    name="patient_number"
                    value="{{ old('patient_number', $appointment->patient_number) }}"
                    class="form-control appointment-control @error('patient_number') is-invalid @enderror"
                    placeholder="Enter Phone Number">
                  @error('patient_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label appointment-label" for="dob">Date of birth</label>
                  <input
                    type="date"
                    id="dob"
                    name="dob"
                    value="{{ old('dob', optional($appointment->dob)->format('Y-m-d')) }}"
                    class="form-control appointment-control @error('dob') is-invalid @enderror">
                  @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label appointment-label" for="gender">Gender</label>
                  <select
                    id="gender"
                    name="gender"
                    class="form-select appointment-control @error('gender') is-invalid @enderror">
                    <option value="" disabled {{ old('gender', $appointment->gender) ? '' : 'selected' }}>Select</option>
                    <option value="male" @selected(old('gender', $appointment->gender) === 'male')>Male</option>
                    <option value="female" @selected(old('gender', $appointment->gender) === 'female')>Female</option>
                    <option value="Other" @selected(old('gender', $appointment->gender) === 'Other')>Other</option>
                  </select>
                  @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

              </div>
            @endif

            {{-- RIGHT COLUMN --}}
            <div class="{{ auth()->user()->role !== 'patient' ? 'col-md-6' : 'col-md-12' }}">

              <!-- Doctor -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="doctor_name">Doctor Name</label>

                <select
                  id="doctor_name"
                  name="doctor_name"
                  class="form-select appointment-control @error('doctor_name') is-invalid @enderror"
                  {{ $isDoctorRole ? 'disabled' : '' }}
                >
                  <option value="" disabled {{ $oldDoctorName ? '' : 'selected' }}>Select Doctor</option>

                  @foreach ($doctors as $doctor)
                    @php
                      // ✅ visit types list (from doctorInfo)
                      $vts = $doctor->doctorInfo->visit_types ?? $doctor->visit_types ?? [];
                      $vts = is_array($vts) ? $vts : (json_decode($vts, true) ?: []);
                    @endphp
                    <option
                      value="{{ $doctor->name }}"
                      data-id="{{ $doctor->id }}"
                      data-visit-types='@json($vts)'
                      @selected($oldDoctorName === $doctor->name || (string)$preDoctor === (string)$doctor->id)
                    >
                      {{ $doctor->name }}
                    </option>
                  @endforeach
                </select>

                {{-- ✅ مهم: لو select disabled مش هيتبعت.. فنبعت hidden --}}
                @if($isDoctorRole)
                  <input type="hidden" name="doctor_name" value="{{ $oldDoctorName }}">
                @endif

                @error('doctor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <!-- ✅ Visit Type + Price -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="visit_type">Visit Type</label>

                <div class="row g-2 align-items-center">
                  <div class="col-7">
                    <select
                      id="visit_type"
                      name="visit_type"
                      class="form-select appointment-control @error('visit_type') is-invalid @enderror"
                      disabled
                    >
                      <option value="" selected>Select doctor first</option>
                    </select>
                    @error('visit_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-5">
                    <div class="input-group">
                      <input
                        id="visit_price"
                        name="visit_price"
                        type="text"
                        class="form-control appointment-control @error('visit_price') is-invalid @enderror"
                        value="{{ $oldVisitPrice }}"
                        placeholder="Price"
                        readonly
                      >
                      <span class="input-group-text">EGP</span>
                      @error('visit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                  </div>
                </div>

                {{-- ✅ لو select disabled (doctor role)، نضمن الإرسال --}}
                @if($isDoctorRole)
                  <input type="hidden" name="visit_type" value="{{ $oldVisitType }}">
                  <input type="hidden" name="visit_price" value="{{ $oldVisitPrice }}">
                @endif
              </div>

              <!-- Appointment Date (dynamic select) -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="appointment_date">Select date</label>
                <select
                  id="appointment_date"
                  name="appointment_date"
                  class="form-select appointment-control @error('appointment_date') is-invalid @enderror"
                  disabled>
                  <option value="" selected>Select doctor first</option>
                </select>
                @error('appointment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <!-- Appointment Time (dynamic select) -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="appointment_time">Select Time</label>
                <select
                  id="appointment_time"
                  name="appointment_time"
                  class="form-select appointment-control @error('appointment_time') is-invalid @enderror"
                  disabled>
                  <option value="" selected>Select date first</option>
                </select>
                @error('appointment_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <!-- Reason -->
              <div class="mb-3">
                <label class="form-label appointment-label" for="reason">Reason</label>
                <textarea
                  id="reason"
                  name="reason"
                  rows="4"
                  class="form-control appointment-control appointment-textarea @error('reason') is-invalid @enderror"
                  placeholder="Describe your reason">{{ old('reason', $appointment->reason) }}</textarea>
                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 gap-md-3 mt-3 mt-md-4">
              <a href="{{ route('appointment.show') }}" class="btn btn-light btn-cancel">Cancel</a>
              <button type="submit" class="btn btn-primary btn-book">Update Appointment</button>
            </div>

          </div>
        </form>

      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const doctorSel = document.getElementById('doctor_name');
  const dateSel   = document.getElementById('appointment_date');
  const timeSel   = document.getElementById('appointment_time');

  const visitSel  = document.getElementById('visit_type');
  const priceInp  = document.getElementById('visit_price');

  const oldDate = @json($oldDate);
  const oldTime = @json($oldTime);

  const oldVisitType  = @json($oldVisitType);
  const oldVisitPrice = @json($oldVisitPrice);

  let timesByDate = {};

  function escapeHtml(str){
    return String(str ?? '')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }

  function resetVisit() {
    if (!visitSel) return;
    visitSel.innerHTML = `<option value="" selected>Select doctor first</option>`;
    visitSel.disabled = true;
    if (priceInp) priceInp.value = '';
  }

  function resetDateTime() {
    dateSel.innerHTML = `<option value="" selected>Select doctor first</option>`;
    timeSel.innerHTML = `<option value="" selected>Select date first</option>`;
    dateSel.disabled = true;
    timeSel.disabled = true;
    timesByDate = {};
  }

  function resetAll() {
    resetDateTime();
    resetVisit();
  }

  function fillVisitTypesFromDoctorOption(opt) {
    resetVisit();

    // ✅ لو مفيش option (مثلاً select فاضي)
    if (!opt) return;

    let vts = [];
    try {
      vts = JSON.parse(opt.dataset.visitTypes || '[]');
    } catch (e) {
      vts = [];
    }

    // ✅ لو الدكتور ملوش أنواع كشف
    if (!Array.isArray(vts) || vts.length === 0) {
     
      if (oldVisitType) {
        visitSel.disabled = false;
        visitSel.innerHTML =
          `<option value="" selected>Select visit type</option>` +
          `<option value="${escapeHtml(oldVisitType)}" selected data-price="${escapeHtml(oldVisitPrice)}">${escapeHtml(oldVisitType)} (Current)</option>`;
        if (priceInp) priceInp.value = oldVisitPrice || '';
      } else {
        visitSel.innerHTML = `<option value="" selected>No visit types</option>`;
        visitSel.disabled = true;
        if (priceInp) priceInp.value = '';
      }
      return;
    }

    visitSel.innerHTML =
      `<option value="" selected>Select visit type</option>` +
      vts.map(v => {
        const type  = (v?.type ?? '').toString();
        const price = (v?.price ?? '').toString();
        return `<option value="${escapeHtml(type)}" data-price="${escapeHtml(price)}">${escapeHtml(type)}</option>`;
      }).join('');

    visitSel.disabled = false;

    // ✅ اختار old لو موجود وإلا أول عنصر
    const hasOld = oldVisitType && [...visitSel.options].some(o => o.value === oldVisitType);
    if (hasOld) {
      visitSel.value = oldVisitType;
    } else if (visitSel.options.length > 1) {
      visitSel.selectedIndex = 1;
    }

    // حدث السعر
    visitSel.dispatchEvent(new Event('change'));
  }

  visitSel?.addEventListener('change', () => {
    const opt = visitSel.options[visitSel.selectedIndex];
    const price = opt?.dataset?.price || '';
    if (priceInp) priceInp.value = price;
  });

  function formatTimeLabel(t) {
    try {
      const [h, m] = String(t).split(':').map(Number);
      const d = new Date();
      d.setHours(h, m, 0, 0);
      return d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    } catch (e) {
      return t;
    }
  }

  function ensureCurrentTimeOptionSelected() {
    if (!oldTime) return;

    const exists = [...timeSel.options].some(o => o.value === oldTime);
    if (!exists) {
      const opt = document.createElement('option');
      opt.value = oldTime;
      opt.textContent = `${formatTimeLabel(oldTime)} (Current)`;
      timeSel.appendChild(opt);
    }
    timeSel.value = oldTime;
  }

  function fillTimesForDate(dateValue) {
    const times = timesByDate[dateValue] || [];

    if (!times.length) {
      timeSel.innerHTML = `<option value="" selected>No available times</option>`;
      timeSel.disabled = true;

      if (oldDate && dateValue === oldDate && oldTime) {
        timeSel.disabled = false;
        timeSel.innerHTML = `<option value="" selected>Select time</option>`;
        ensureCurrentTimeOptionSelected();
      }
      return;
    }

    timeSel.innerHTML =
      `<option value="" selected>Select time</option>` +
      times.map(t => `<option value="${t}">${formatTimeLabel(t)}</option>`).join('');

    timeSel.disabled = false;

    if (oldDate && dateValue === oldDate && oldTime) {
      ensureCurrentTimeOptionSelected();
    }
  }

  async function loadAvailabilityByDoctorId(doctorId) {
    resetDateTime();
    if (!doctorId) return;

    try {
      const url = `{{ url('/doctors') }}/${doctorId}/availability`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) return resetDateTime();

      const data = await res.json();
      const dates = data.dates || [];

      if (!dates.length) {
        dateSel.innerHTML = `<option value="" selected>No available dates</option>`;
        dateSel.disabled = true;

        timeSel.innerHTML = `<option value="" selected>No available times</option>`;
        timeSel.disabled = true;

        if (oldDate) {
          dateSel.disabled = false;
          dateSel.innerHTML = `<option value="${oldDate}" selected>${oldDate} (Current)</option>`;
          timeSel.disabled = false;
          timeSel.innerHTML = `<option value="" selected>Select time</option>`;
          ensureCurrentTimeOptionSelected();
        }
        return;
      }

      timesByDate = {};
      dates.forEach(d => { timesByDate[d.value] = d.times || []; });

      dateSel.innerHTML =
        `<option value="" selected>Select date</option>` +
        dates.map(d => `<option value="${d.value}">${d.label}</option>`).join('');

      dateSel.disabled = false;

      const dateValues = dates.map(d => d.value);
      const chosenDate = (oldDate && dateValues.includes(oldDate)) ? oldDate : dates[0].value;

      dateSel.value = chosenDate;
      fillTimesForDate(chosenDate);

    } catch (e) {
      resetDateTime();
    }
  }

  doctorSel?.addEventListener('change', () => {
    const opt = doctorSel.options[doctorSel.selectedIndex];
    const doctorId = opt?.dataset?.id;

    // ✅ fill visit types
    fillVisitTypesFromDoctorOption(opt);

    // ✅ fill availability
    loadAvailabilityByDoctorId(doctorId);
  });

  dateSel?.addEventListener('change', () => {
    if (!dateSel.value) return;
    fillTimesForDate(dateSel.value);
  });

  // ✅ init
  resetAll();

  // ✅ في edit: شغّل fill من الدكتور المختار حتى لو select disabled
  if (doctorSel && doctorSel.selectedIndex >= 0) {
    const opt = doctorSel.options[doctorSel.selectedIndex];
    const doctorId = opt?.dataset?.id;

    fillVisitTypesFromDoctorOption(opt);

    if (doctorId) {
      loadAvailabilityByDoctorId(doctorId);
    } else {
      // fallback لو مفيش doctorId بس عندنا old visit
      if (oldVisitType && visitSel) {
        visitSel.disabled = false;
        visitSel.innerHTML =
          `<option value="" selected>Select visit type</option>` +
          `<option value="${escapeHtml(oldVisitType)}" selected data-price="${escapeHtml(oldVisitPrice)}">${escapeHtml(oldVisitType)} (Current)</option>`;
        if (priceInp) priceInp.value = oldVisitPrice || '';
      }
    }
  }
});
</script>
@endsection
