@extends('layouts.dash')
@section('dash-content')
  <main class="main">
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card">
          <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
       data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
       <i class="fa-solid fa-bars"></i>
     </button>
          <h3 class="appointment-title mb-4">Book Appointment</h3>

          {{-- SUCCESS --}}
          @if (session('success'))
            <div id="successAlert" class="alert alert-success mb-3">
              {{ session('success') }}
            </div>
          @endif

          <form method="POST" action="{{ route('appointment.store') }}">
            @csrf

            <div class="row g-3">
              @if (auth()->user()->role !== 'patient')
                <div class="col-md-6">

                  <div class="mb-3">
                    <label class="form-label appointment-label" for="patient_user_id">
                      Patient
                    </label>

                    <select id="patient_name" name="patient_name"
                      class="form-select appointment-control @error('patient_name') is-invalid @enderror">
                      <option value="">Select Patient</option>
 @if (auth()->user()->role !== 'patient')
                      <option value="Visit" {{ old('patient_name') =='Visit'? 'selected' : '' }}>
                        Visit
                      </option>
                      @endif
                      @foreach($patients as $p)
                        <option value="{{ $p->name }}" {{ old('patient_name') == $p->id ? 'selected' : '' }}>
                          {{ $p->name }}
                        </option>
                      @endforeach
                    </select>

                    @error('patient_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- ➕ Add new patient --}}
                    <div class="mt-2 text-end">
                      <a href="{{ route('users.create', ['role' => 'patient']) }}"
                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Add New Patient</span>
                      </a>
                    </div>
                  </div>




                  <div class="mb-3">
                    <label class="form-label appointment-label" for="patient_number">Patient Number</label>
                    <input type="tel" id="patient_number" name="patient_number" value="{{ old('patient_number') }}"
                      class="form-control appointment-control @error('patient_number') is-invalid @enderror"
                      placeholder="Enter Phone Number">
                    @error('patient_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="mb-3">
                    <label class="form-label appointment-label" for="dob">Date of birth</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}"
                      class="form-control appointment-control @error('dob') is-invalid @enderror">
                    @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="mb-3">
                    <label class="form-label appointment-label" for="gender">Gender</label>
                    <select id="gender" name="gender"
                      class="form-select appointment-control @error('gender') is-invalid @enderror">
                      <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select</option>
                      <option value="male" @selected(old('gender') === 'male')>Male</option>
                      <option value="female" @selected(old('gender') === 'female')>Female</option>
                      <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
{{-- ================= Emergency / VIP ================= --}}
<div class="row g-3">

  {{-- Emergency --}}
  <div class="col-6">
    <div class="form-check form-switch">
      <input type="hidden" name="emergency" value="0">

      <input
        class="form-check-input"
        type="checkbox"
        id="emergency"
        name="emergency"
        value="1"
        {{ old('emergency') == 1 ? 'checked' : '' }}
      >

      <label class="form-check-label fw-semibold" for="emergency">
        🚨 Emergency
      </label>
    </div>
  </div>

  {{-- VIP --}}
  <div class="col-6">
    <div class="form-check form-switch">
      <input type="hidden" name="vip" value="0">

      <input
        class="form-check-input"
        type="checkbox"
        id="vip"
        name="vip"
        value="1"
        {{ old('vip') == 1 ? 'checked' : '' }}
      >

      <label class="form-check-label fw-semibold" for="vip">
        ⭐ VIP
      </label>
    </div>
  </div>

</div>

                </div>
              @endif

              {{-- RIGHT COLUMN --}}
              <div class="{{ auth()->user()->role !== 'patient' ? 'col-md-6' : 'col-md-12' }}">

                <!-- Doctor -->
                <div class="mb-3">
                  <label class="form-label appointment-label" for="doctor_name">Doctor Name</label>
                  @php $preDoctor = request('doctor'); @endphp

                  <select id="doctor_name" name="doctor_name"
                    class="form-select appointment-control @error('doctor_name') is-invalid @enderror">
                    <option value="" disabled {{ old('doctor_name') ? '' : 'selected' }}>Select Doctor</option>

                    @foreach ($doctors as $doctor)
                      @php
                        $vts = $doctor->doctorInfo->visit_types ?? $doctor->visit_types ?? [];
                        $vts = is_array($vts) ? $vts : [];
                      @endphp

                      <option value="{{ $doctor->name }}" data-id="{{ $doctor->id }}" data-visit-types='@json($vts)'
                        @selected(old('doctor_name') === $doctor->name || (string) $preDoctor === (string) $doctor->id)>
                        {{ $doctor->name }}
                      </option>
                    @endforeach
                  </select>

                  @error('doctor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- ✅ Visit Type + Price -->
                <div class="mb-3">
                  <label class="form-label appointment-label" for="visit_type">Visit Type</label>

                  <div class="row g-2 align-items-center">
                    <div class="col-7">
                      <select id="visit_type" name="visit_type"
                        class="form-select appointment-control @error('visit_type') is-invalid @enderror" disabled>
                        <option value="" selected>Select doctor first</option>
                      </select>
                      @error('visit_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-5">
                      <div class="input-group">
                        <input id="visit_price" name="visit_price" type="text" class="form-control appointment-control"
                          value="{{ old('visit_price') }}" placeholder="Price" readonly>
                        <span class="input-group-text">EGP</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Appointment Date -->
                <div class="mb-3">
                  <label class="form-label appointment-label" for="appointment_date">Select date</label>
                  <select id="appointment_date" name="appointment_date"
                    class="form-select appointment-control @error('appointment_date') is-invalid @enderror" disabled>
                    <option value="" selected>Select doctor first</option>
                  </select>
                  @error('appointment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Appointment Time -->
                <div class="mb-3">
                  <label class="form-label appointment-label" for="appointment_time">Select Time</label>
                  <select id="appointment_time" name="appointment_time"
                    class="form-select appointment-control @error('appointment_time') is-invalid @enderror" disabled>
                    <option value="" selected>Select date first</option>
                  </select>
                  @error('appointment_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Reason -->
                <div class="mb-3">
                  <label class="form-label appointment-label" for="reason">Reason</label>
                  <textarea id="reason" name="reason" rows="4"
                    class="form-control appointment-control appointment-textarea @error('reason') is-invalid @enderror"
                    placeholder="Describe your reason">{{ old('reason') }}</textarea>
                  @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

              </div>

              <!-- Buttons -->
              <div class="d-flex justify-content-end gap-2 gap-md-3 mt-3 mt-md-4">
                <a href="{{ url()->previous() }}" class="btn btn-light btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-primary btn-book">Book Appointment</button>
              </div>

            </div>
          </form>

        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {

      // hide success alert
      const success = document.getElementById('successAlert');
      if (success) setTimeout(() => (success.style.display = 'none'), 3000);

      const doctorSel = document.getElementById('doctor_name');
      const visitSel = document.getElementById('visit_type');
      const priceInp = document.getElementById('visit_price');

      const dateSel = document.getElementById('appointment_date');
      const timeSel = document.getElementById('appointment_time');

      // ✅ map: date => times[]
      let timesByDate = {};

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
        resetVisit();
      }

      function formatTimeLabel(t) {
        // "13:00" => "1:00 PM"
        try {
          const [h, m] = t.split(':').map(Number);
          const d = new Date();
          d.setHours(h, m, 0, 0);
          return d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        } catch (e) {
          return t;
        }
      }

      function fillTimesForDate(dateValue) {
        const times = timesByDate[dateValue] || [];

        if (!times.length) {
          timeSel.innerHTML = `<option value="" selected>No available times</option>`;
          timeSel.disabled = true;
          return;
        }

        timeSel.innerHTML =
          `<option value="" selected>Select time</option>` +
          times.map(t => `<option value="${t}">${formatTimeLabel(t)}</option>`).join('');

        timeSel.disabled = false;
      }

      function fillVisitTypesFromDoctorOption(opt) {
        resetVisit();

        let vts = [];
        try {
          vts = JSON.parse(opt?.dataset?.visitTypes || "[]");
        } catch (e) { vts = []; }

        if (!Array.isArray(vts) || vts.length === 0) {
          visitSel.innerHTML = `<option value="" selected>No visit types</option>`;
          visitSel.disabled = true;
          return;
        }

        visitSel.innerHTML =
          `<option value="" selected>Select visit type</option>` +
          vts.map(v => {
            const type = (v?.type ?? '').toString();
            const price = (v?.price ?? '').toString();
            return `<option value="${type}" data-price="${price}">${type}</option>`;
          }).join('');

        visitSel.disabled = false;

        // ✅ old selected visit_type
        const oldType = @json(old('visit_type'));
        if (oldType) {
          visitSel.value = oldType;
        } else {
          // auto select first type
          if (visitSel.options.length > 1) visitSel.selectedIndex = 1;
        }

        visitSel.dispatchEvent(new Event('change'));
      }

      visitSel?.addEventListener('change', () => {
        const opt = visitSel.options[visitSel.selectedIndex];
        const price = opt?.dataset?.price || '';
        if (priceInp) priceInp.value = price ? price : '';
      });

      doctorSel?.addEventListener('change', async () => {
        const opt = doctorSel.options[doctorSel.selectedIndex];
        const doctorId = opt?.dataset?.id;

        resetDateTime();
        if (!doctorId) return;

        // ✅ fill visit types + price
        fillVisitTypesFromDoctorOption(opt);

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
            return;
          }

          // build timesByDate map
          timesByDate = {};
          dates.forEach(d => {
            timesByDate[d.value] = d.times || [];
          });

          // fill date dropdown
          dateSel.innerHTML =
            `<option value="" selected>Select date</option>` +
            dates.map(d => `<option value="${d.value}">${d.label}</option>`).join('');

          dateSel.disabled = false;

          // auto select first date
          dateSel.value = dates[0].value;
          fillTimesForDate(dates[0].value);

        } catch (e) {
          resetDateTime();
        }
      });

      dateSel?.addEventListener('change', () => {
        if (!dateSel.value) return;
        fillTimesForDate(dateSel.value);
      });

      // init
      resetDateTime();

      // ✅ لو جاي doctor متحدد من لينك خارجي (services->doctors)
      if (doctorSel && doctorSel.value) {
        doctorSel.dispatchEvent(new Event('change'));
      }
    });
  </script>

@endsection