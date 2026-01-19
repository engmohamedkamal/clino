@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/patientTransfer.css') }}" />

@php
  // قيمة الـ id المختارة (من query أو old)
  $pid = old('patient_id', $selectedPatientId ?? '');

  // اسم المريض المختار (لإظهار الاسم داخل الـ input)
  $selectedName = '';
  if(!empty($pid) && isset($patients)){
    $found = collect($patients)->firstWhere('id', (int)$pid) ?? collect($patients)->firstWhere('id', $pid);
    $selectedName = $found->name ?? '';
  }

  // اسم الانبوت (لو المستخدم كتب و رجع old)
  $patientNameOld = old('patient_name', $selectedName);
@endphp

<main class="pt-main">

  {{-- ✅ Success Message --}}
  @if(session('success'))
    <div class="alert alert-success mb-3">
      {{ session('success') }}
    </div>
  @endif

  {{-- ✅ Show ALL errors (session error + validation errors) --}}
  @if(session('error') || $errors->any())
    <div class="alert alert-danger mb-3">
      <strong>There were some problems with your submission:</strong>

      <ul class="mb-0 mt-2">
        @if(session('error'))
          <li>{{ session('error') }}</li>
        @endif

        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Top row: title -->
  <div class="d-flex align-items-center justify-content-between gap-2 mb-3 mb-md-4">
    <h3 class="pt-title mb-0">Patient Transfer</h3>
  </div>

  {{-- ===================== FORM ===================== --}}
  <form method="POST" action="{{ route('patient-transfers.store') }}">
    @csrf

    {{-- hidden controlled by UI --}}
    <input type="hidden" name="stability_status" id="stability_status" value="{{ old('stability_status', 'stable') }}">
    <input type="hidden" name="transport_mode" id="transport_mode" value="{{ old('transport_mode', 'als_ambulance') }}">

    <!-- Patient Header Card -->
    <section class="pt-patient-card mb-4">
      <div class="pt-left-accent"></div>

      <div class="pt-patient-body">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
          <div class="d-flex align-items-center gap-3">

            <div class="pt-patient-info w-100">

              {{-- Name + Code + Priority --}}
              <div class="d-flex align-items-end gap-3 flex-wrap">

                {{-- ================= Patient (Searchable) ================= --}}
                <div class="mb-3" style="min-width:260px">
                  <label class="form-label">Patient <span class="text-danger">*</span></label>

                  {{-- hidden id to submit --}}
                  <input type="hidden" name="patient_id" id="patient_id" value="{{ $pid }}">

                  {{-- searchable input --}}
                  <input
                    type="text"
                    name="patient_name"
                    id="patient_name"
                    class="form-control @error('patient_id') is-invalid @enderror"
                    list="patientsList"
                    placeholder="Search patient..."
                    value="{{ $patientNameOld }}"
                    autocomplete="off"
                    required
                  >

                  <datalist id="patientsList">
                    @foreach($patients as $p)
                      <option value="{{ $p->name }}" data-id="{{ $p->id }}"></option>
                    @endforeach
                  </datalist>

                  @error('patient_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- ================= Transfer Code ================= --}}
                <div class="pt-field">
                  <label class="pt-label mb-1">Transfer Code</label>
                  <input
                    type="text"
                    name="transfer_code"
                    class="form-control pt-control pt-control-sm w-auto @error('transfer_code') is-invalid @enderror"
                    value="{{ old('transfer_code', $transfer_code ?? '') }}"
                    placeholder="#CODE"
                    readonly
                  >
                  @error('transfer_code')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- ================= Transfer Priority ================= --}}
                <div class="pt-field">
                  <label class="pt-label mb-1">Transfer Priority <span class="text-danger">*</span></label>

                  <input type="hidden"
                        name="transfer_priority"
                        id="transfer_priority"
                        value="{{ old('transfer_priority', 'urgent') }}">

                  <div class="pt-priority-group" role="group" aria-label="Transfer Priority">
                    <button type="button"
                            class="pt-priority-btn urgent {{ old('transfer_priority','urgent') === 'urgent' ? 'active' : '' }}"
                            data-value="urgent"
                            aria-pressed="{{ old('transfer_priority','urgent') === 'urgent' ? 'true' : 'false' }}">
                      URGENT
                    </button>

                    <button type="button"
                            class="pt-priority-btn normal {{ old('transfer_priority') === 'normal' ? 'active' : '' }}"
                            data-value="normal"
                            aria-pressed="{{ old('transfer_priority') === 'normal' ? 'true' : 'false' }}">
                      NORMAL
                    </button>
                  </div>

                  @error('transfer_priority')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>

              </div>

              {{-- Meta Row --}}
              <div class="pt-meta-row mt-3">

                {{-- Age --}}
                <div class="pt-meta">
                  <div class="pt-meta-label">AGE</div>
                  <input
                    type="number"
                    name="age"
                    class="form-control pt-control pt-control-sm @error('age') is-invalid @enderror"
                    value="{{ old('age') }}"
                    min="0"
                    max="120"
                  >
                  @error('age')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Gender --}}
                <div class="pt-meta">
                  <div class="pt-meta-label">GENDER</div>
                  <select
                    name="gender"
                    class="form-select pt-control pt-control-sm @error('gender') is-invalid @enderror">
                    <option value="">—</option>
                    <option value="male" {{ old('gender')==='male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender')==='female' ? 'selected' : '' }}>Female</option>
                  </select>
                  @error('gender')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Blood Type --}}
                <div class="pt-meta">
                  <div class="pt-meta-label">BLOOD TYPE</div>
                  <input
                    type="text"
                    name="blood_type"
                    class="form-control pt-control pt-control-sm @error('blood_type') is-invalid @enderror"
                    value="{{ old('blood_type') }}"
                    placeholder="O+"
                  >
                  @error('blood_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Current Location --}}
                <div class="pt-meta">
                  <div class="pt-meta-label">CURRENT LOCATION</div>
                  <input
                    type="text"
                    name="current_location"
                    class="form-control pt-control pt-control-sm @error('current_location') is-invalid @enderror"
                    value="{{ old('current_location') }}"
                    placeholder="Ward / Unit"
                  >
                  @error('current_location')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Primary Physician --}}
                <div class="pt-meta">
                  <div class="pt-meta-label">PRIMARY PHYSICIAN</div>
                  <select
                    name="primary_physician_id"
                    class="form-select pt-control pt-control-sm @error('primary_physician_id') is-invalid @enderror">
                    <option value="">Select</option>
                    @foreach(($doctors ?? []) as $doc)
                      <option
                        value="{{ $doc->id }}"
                        {{ (string)old('primary_physician_id', $primaryPhysician->id ?? '') === (string)$doc->id ? 'selected' : '' }}>
                        Dr. {{ $doc->name }}
                      </option>
                    @endforeach
                  </select>

                  @error('primary_physician_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

              </div>

              {{-- Risk / Allergy --}}
              <div class="pt-pill-row mt-3">
                <label class="pt-pill pt-pill-danger d-flex align-items-center gap-2">
                  <input type="checkbox" name="penicillin_allergy" value="1"
                        {{ old('penicillin_allergy') ? 'checked' : '' }}>
                  <i class="bi bi-exclamation-triangle"></i>
                  Penicillin Allergy
                </label>

                <label class="pt-pill pt-pill-orange d-flex align-items-center gap-2">
                  <input type="checkbox" name="high_fall_risk" value="1"
                        {{ old('high_fall_risk') ? 'checked' : '' }}>
                  <i class="bi bi-lightning-charge"></i>
                  High Fall Risk
                </label>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Content grid -->
    <div class="row g-4">

      <!-- Left column -->
      <div class="col-12 col-xl-8">

        <!-- Clinical Assessment -->
        <section class="pt-card mb-4">
          <div class="pt-card-head">
            <div class="pt-card-ico"><i class="bi bi-clipboard2-pulse"></i></div>
            <div>
              <div class="pt-card-title">Clinical Assessment</div>
              <div class="pt-card-sub">Detailed medical summary and stability status</div>
            </div>
          </div>

          <div class="pt-card-body">
            <div class="row g-3 align-items-end">

              <div class="col-12 col-lg-6">
                <label class="pt-label">Reason for Transfer <span class="text-danger">*</span></label>

                <select name="reason_for_transfer"
                        class="form-select pt-control @error('reason_for_transfer') is-invalid @enderror">
                  @php $r = old('reason_for_transfer'); @endphp
                  <option value="Specialized Surgical Requirement" {{ $r==='Specialized Surgical Requirement' ? 'selected' : '' }}>
                    Specialized Surgical Requirement
                  </option>
                  <option value="ICU Bed Unavailable" {{ $r==='ICU Bed Unavailable' ? 'selected' : '' }}>ICU Bed Unavailable</option>
                  <option value="Advanced Imaging Needed" {{ $r==='Advanced Imaging Needed' ? 'selected' : '' }}>Advanced Imaging Needed</option>
                  <option value="Cardiac Cath Lab" {{ $r==='Cardiac Cath Lab' ? 'selected' : '' }}>Cardiac Cath Lab</option>
                </select>

                @error('reason_for_transfer')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-lg-6">
                <label class="pt-label">Stability Status</label>

                @php $st = old('stability_status','stable'); @endphp
                <div class="pt-chip-group" data-stability role="group" aria-label="Stability Status">
                  <button type="button" class="pt-chip {{ $st==='stable' ? 'pt-chip-active' : '' }}" data-value="stable" aria-pressed="{{ $st==='stable' ? 'true':'false' }}">Stable</button>
                  <button type="button" class="pt-chip {{ $st==='guarded' ? 'pt-chip-active' : '' }}" data-value="guarded" aria-pressed="{{ $st==='guarded' ? 'true':'false' }}">Guarded</button>
                  <button type="button" class="pt-chip {{ $st==='critical' ? 'pt-chip-active' : '' }}" data-value="critical" aria-pressed="{{ $st==='critical' ? 'true':'false' }}">Critical</button>
                </div>

                @error('stability_status')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label class="pt-label">Primary Diagnosis</label>
                <input name="primary_diagnosis"
                      class="form-control pt-control @error('primary_diagnosis') is-invalid @enderror"
                      value="{{ old('primary_diagnosis') }}" />
                @error('primary_diagnosis')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label class="pt-label">Medical Summary & Recent Vitals</label>
                <textarea name="medical_summary"
                          class="form-control pt-control pt-textarea @error('medical_summary') is-invalid @enderror"
                          rows="4">{{ old('medical_summary') }}</textarea>
                @error('medical_summary')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

            </div>
          </div>
        </section>

        <!-- Transport & Logistics -->
        <section class="pt-card mb-4">
          <div class="pt-card-head">
            <div class="pt-card-ico"><i class="bi bi-truck"></i></div>
            <div>
              <div class="pt-card-title">Transport & Logistics</div>
              <div class="pt-card-sub">Select transport mode and equipment needs</div>
            </div>
          </div>

          <div class="pt-card-body">
            @php $mode = old('transport_mode','als_ambulance'); @endphp

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <button type="button"
                        class="pt-mode-card w-100 {{ $mode==='als_ambulance' ? 'pt-mode-active' : '' }}"
                        data-mode="als_ambulance"
                        aria-pressed="{{ $mode==='als_ambulance' ? 'true':'false' }}">
                  <div class="pt-mode-icon pt-mode-muted"><i class="bi bi-truck"></i></div>
                  <div class="pt-mode-name">ALS Ambulance</div>
                  <div class="pt-mode-sub">ADVANCED LIFE SUPPORT</div>
                </button>
              </div>

              <div class="col-12 col-md-6">
                <button type="button"
                        class="pt-mode-card w-100 {{ $mode==='wheelchair_van' ? 'pt-mode-active' : '' }}"
                        data-mode="wheelchair_van"
                        aria-pressed="{{ $mode==='wheelchair_van' ? 'true':'false' }}">
                  <div class="pt-mode-icon pt-mode-muted"><i class="bi bi-car-front-fill"></i></div>
                  <div class="pt-mode-name">Wheelchair Van</div>
                  <div class="pt-mode-sub">NON-EMERGENCY</div>
                </button>
              </div>
            </div>

            @error('transport_mode')
              <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror

            <div class="pt-toggle-row mt-3">
              <div class="pt-toggle-item">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-wind"></i><span>Continuous Oxygen</span>
                </div>
                <div class="form-check form-switch m-0">
                  <input name="continuous_oxygen"
                        class="form-check-input pt-switch"
                        type="checkbox"
                        value="1"
                        {{ old('continuous_oxygen') ? 'checked' : '' }} />
                </div>
              </div>

              <div class="pt-toggle-item">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-heart-pulse"></i><span>Cardiac Monitoring</span>
                </div>
                <div class="form-check form-switch m-0">
                  <input name="cardiac_monitoring"
                        class="form-check-input pt-switch"
                        type="checkbox"
                        value="1"
                        {{ old('cardiac_monitoring') ? 'checked' : '' }} />
                </div>
              </div>
            </div>

          </div>
        </section>

      </div>

      <!-- Right column -->
      <div class="col-12 col-xl-4">

        <!-- Destination -->
        <section class="pt-card mb-4">
          <div class="pt-card-head pt-between">
            <div class="d-flex align-items-center gap-2">
              <div class="pt-card-ico"><i class="bi bi-geo-alt"></i></div>
              <div class="pt-card-title text-uppercase">Destination</div>
            </div>

            @php $bed = old('bed_status','confirmed'); @endphp
            <span class="pt-tag {{ $bed==='confirmed' ? 'pt-tag-success' : 'pt-tag-warn' }}">
              {{ $bed==='confirmed' ? 'BED CONFIRMED' : 'PENDING' }}
            </span>

            <input type="hidden" name="bed_status" value="{{ old('bed_status','confirmed') }}">
          </div>

          <div class="pt-card-body">
            <div class="mb-2">
              <label class="pt-label">Hospital <span class="text-danger">*</span></label>
              <input name="destination_hospital"
                    class="form-control pt-control @error('destination_hospital') is-invalid @enderror"
                    value="{{ old('destination_hospital') }}">
              @error('destination_hospital')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="row g-3 mt-1">
              <div class="col-6">
                <div class="pt-small-label">DEPT / UNIT</div>
                <input name="destination_dept_unit"
                      class="form-control pt-control @error('destination_dept_unit') is-invalid @enderror"
                      value="{{ old('destination_dept_unit') }}">
                @error('destination_dept_unit')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-6">
                <div class="pt-small-label">BED NO.</div>
                <input name="destination_bed_no"
                      class="form-control pt-control @error('destination_bed_no') is-invalid @enderror"
                      value="{{ old('destination_bed_no') }}">
                @error('destination_bed_no')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="pt-receiver mt-3">
              <div class="pt-receiver-ico"><i class="bi bi-person-badge"></i></div>

              <div class="flex-grow-1">
                <div class="pt-small-label mb-1">RECEIVING DOCTOR</div>

                <input
                  type="text"
                  name="receiving_doctor_name"
                  class="form-control pt-control @error('receiving_doctor_name') is-invalid @enderror"
                  value="{{ old('receiving_doctor_name') }}"
                  placeholder="Receiving Doctor Name"
                >
                @error('receiving_doctor_name')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <input name="receiving_phone"
                      class="form-control pt-control mt-2 @error('receiving_phone') is-invalid @enderror"
                      placeholder="Receiving phone"
                      value="{{ old('receiving_phone') }}">

                @error('receiving_phone')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </section>

        <div class="mb-2">
          <label class="pt-label">Attachments (text)</label>

          <div class="d-flex gap-2 align-items-start">
            <div id="attWrap" class="flex-grow-1 d-grid gap-2">
              <input
                type="text"
                name="attachments[]"
                class="form-control pt-control"
                placeholder="Attachment item..."
              >
            </div>

            <button
              type="button"
              class="btn btn-light pt-control"
              id="addAtt"
              title="Add another"
            >
              <i class="bi bi-plus"></i>
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="pt-footer mt-4">
          <button type="submit" class="btn pt-btn-primary mt-4">
            <i class="bi bi-send me-2"></i> Submit Final Transfer
          </button>
        </div>

      </div>
    </div>

  </form>
</main>

<script>
  // ================= Patient searchable -> set hidden patient_id =================
  (function () {
    const nameInput = document.getElementById('patient_name');
    const idInput   = document.getElementById('patient_id');
    const list      = document.getElementById('patientsList');

    if (!nameInput || !idInput || !list) return;

    function syncIdFromName() {
      const val = (nameInput.value || '').trim();
      if (!val) {
        idInput.value = '';
        return;
      }

      const opt = Array.from(list.options).find(o => (o.value || '').trim() === val);
      if (opt && opt.dataset && opt.dataset.id) {
        idInput.value = opt.dataset.id;
      } else {
        // لو كتب اسم مش موجود في الليست
        idInput.value = '';
      }
    }

    // أول تحميل
    syncIdFromName();

    nameInput.addEventListener('input', syncIdFromName);
    nameInput.addEventListener('change', syncIdFromName);
  })();

  // Stability chips -> hidden input
  (function () {
    const group = document.querySelector('[data-stability]');
    const input = document.getElementById('stability_status');
    if (!group || !input) return;

    group.addEventListener('click', function (e) {
      const btn = e.target.closest('.pt-chip');
      if (!btn) return;

      group.querySelectorAll('.pt-chip').forEach(b => {
        b.classList.remove('pt-chip-active');
        b.setAttribute('aria-pressed', 'false');
      });

      btn.classList.add('pt-chip-active');
      btn.setAttribute('aria-pressed', 'true');
      input.value = btn.getAttribute('data-value');
    });
  })();

  // Transport mode cards -> hidden input (single select)
  (function () {
    const input = document.getElementById('transport_mode');
    const cards = document.querySelectorAll('.pt-mode-card[data-mode]');
    if (!input || !cards.length) return;

    const init = input.value;
    cards.forEach(c => {
      const active = c.dataset.mode === init;
      c.classList.toggle('pt-mode-active', active);
      c.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    cards.forEach(card => {
      card.addEventListener('click', () => {
        cards.forEach(c => {
          c.classList.remove('pt-mode-active');
          c.setAttribute('aria-pressed', 'false');
        });
        card.classList.add('pt-mode-active');
        card.setAttribute('aria-pressed', 'true');
        input.value = card.dataset.mode;
      });
    });
  })();

  // Transfer priority -> hidden input
  (function () {
    const input = document.getElementById('transfer_priority');
    const buttons = document.querySelectorAll('.pt-priority-btn');
    if (!input || !buttons.length) return;

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => {
          b.classList.remove('active');
          b.setAttribute('aria-pressed', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');
        input.value = btn.dataset.value;
      });
    });
  })();

  // Add attachments inputs
  (function(){
    const btn = document.getElementById('addAtt');
    const wrap = document.getElementById('attWrap');
    if(!btn || !wrap) return;

    btn.addEventListener('click', () => {
      const inp = document.createElement('input');
      inp.type = 'text';
      inp.name = 'attachments[]';
      inp.className = 'form-control pt-control';
      inp.placeholder = 'Attachment item...';
      wrap.appendChild(inp);
    });
  })();
</script>

@endsection
