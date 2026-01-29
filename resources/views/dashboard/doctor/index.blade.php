@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/doctorInfo.css') }}">
@php
  $info = $info ?? null;

  $isEdit = !is_null($info);
  $action = $isEdit ? route('doctor-info.update', $info->id) : route('doctor-info.store');

  $existingImageName = $isEdit && !empty($info->image) ? basename($info->image) : '';

  $oldAvailability = old('availability_schedule', $info->availability_schedule ?? []);
  $availability = is_array($oldAvailability) ? $oldAvailability : [];

  $oldSpecs = old('Specialization', $info->Specialization ?? []);
  $specs = is_array($oldSpecs) ? $oldSpecs : [];

  $oldActivities = old('activities', $info->activities ?? []);
  $activitiesArr = is_array($oldActivities) ? $oldActivities : [];

  $oldSkills = old('skills', $info->skills ?? []);
  $skillsArr = is_array($oldSkills) ? $oldSkills : [];

  $oldVisitTypes = old('visit_types', $info->visit_types ?? []);
  $visitTypesArr = is_array($oldVisitTypes) ? $oldVisitTypes : [];

  if (count($availability) === 0) $availability = [['day' => '', 'from' => '', 'to' => '']];
  if (count($specs) === 0) $specs = [''];
  if (count($activitiesArr) === 0) $activitiesArr = [''];
  if (count($skillsArr) === 0) $skillsArr = [['name' => '', 'value' => 0]];
  if (count($visitTypesArr) === 0) $visitTypesArr = [['type' => '', 'price' => 0]];
@endphp

<main class="dp-main">

  <header class="dp-topbar">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    <h1 class="dp-title">Doctor Info</h1>
  </header>

  <section class="content-area">
    <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
      <div class="appointment-card di-wrap">

        @if (session('success'))
          <div id="successAlert" class="alert alert-success mb-3">
            {{ session('success') }}
          </div>
        @endif



        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate>
          @csrf
          @if($isEdit) @method('PUT') @endif

          <div class="row g-3">

            <!-- Gender -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="gender">Gender</label>
                <select id="gender" name="gender" class="form-select di-control @error('gender') is-invalid @enderror">
                  <option value="" disabled {{ old('gender', $info->gender ?? '') ? '' : 'selected' }}>Select</option>
                  <option value="male" {{ old('gender', $info->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('gender', $info->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- ✅ Specialization (Input + Add + X same line) -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label mb-2">Specialization</label>

                <div id="specWrap" class="di-repeat-wrap">
                  @foreach($specs as $i => $sp)
                    <div class="di-repeat-item d-flex align-items-center gap-2 flex-nowrap">
                      <input type="text" name="Specialization[]"
                             value="{{ $sp }}"
                             class="form-control di-control flex-grow-1 @error('Specialization.'.$i) is-invalid @enderror"
                             placeholder="Enter specialization">

                      @if($i === 0)
                        <button type="button" class="btn di-mini-btn flex-shrink-0 addSpecBtnRow">
                          <i class="fa-solid fa-plus"></i>
                        </button>
                      @endif

                      <button type="button" class="btn di-x-btn remove-item flex-shrink-0" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('Specialization.'.$i)
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- License -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number"
                       value="{{ old('license_number', $info->license_number ?? '') }}"
                       class="form-control di-control @error('license_number') is-invalid @enderror"
                       placeholder="Enter Number">
                @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Image -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="image">Profile picture</label>

                <div class="input-group di-file-group">
                  <input type="text" id="imageText" class="form-control di-control di-file-text"
                         placeholder="Add Picture" value="{{ old('image_text', $existingImageName) }}" readonly>
                  <button class="btn di-file-btn" type="button" id="imageBtn" aria-label="Upload image">
                    <i class="fa-regular fa-image"></i>
                  </button>
                </div>

                <input type="file" id="image" name="image" class="d-none @error('image') is-invalid @enderror" accept="image/*">
                @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- DOB -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob"
                       value="{{ old('dob', isset($info->dob) ? \Carbon\Carbon::parse($info->dob)->format('Y-m-d') : '') }}"
                       class="form-control di-control @error('dob') is-invalid @enderror">
                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Address -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="address">Address</label>
                <input type="text" id="address" name="address"
                       value="{{ old('address', $info->address ?? '') }}"
                       class="form-control di-control @error('address') is-invalid @enderror"
                       placeholder="Enter Address">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- ✅ Visit Types (Type + Price + Add + X same line) -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label mb-2">Visit Types</label>

                <div id="visitTypesWrap" class="di-repeat-wrap">
                  @foreach($visitTypesArr as $i => $vt)
                    @php
                      $vtType  = is_array($vt) ? ($vt['type'] ?? '') : '';
                      $vtPrice = is_array($vt) ? ($vt['price'] ?? 0) : 0;
                    @endphp

                    <div class="di-repeat-item d-flex align-items-center gap-2 flex-nowrap visit-type-row">
                      <input type="text"
                             name="visit_types[{{ $i }}][type]"
                             value="{{ $vtType }}"
                             class="form-control di-control vt-type flex-grow-1 @error('visit_types.'.$i.'.type') is-invalid @enderror"
                             placeholder="Type (e.g. Consultation, Follow-up)">

                      <input type="number"
                             name="visit_types[{{ $i }}][price]"
                             value="{{ $vtPrice }}"
                             min="0" step="0.01"
                             class="form-control di-control vt-price flex-shrink-0 @error('visit_types.'.$i.'.price') is-invalid @enderror"
                             style="max-width:160px"
                             placeholder="Price">

                      @if($i === 0)
                        <button type="button" class="btn di-mini-btn flex-shrink-0 addVisitBtnRow">
                          <i class="fa-solid fa-plus"></i>
                        </button>
                      @endif

                      <button type="button" class="btn di-x-btn remove-visit-type flex-shrink-0" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('visit_types.'.$i.'.type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('visit_types.'.$i.'.price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                  @endforeach
                </div>

                @error('visit_types') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- ✅ Availability (Day + From + To + Add + X same line) -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label mb-2">Availability schedule</label>

                <div id="availWrap" class="di-repeat-wrap">
                  @foreach($availability as $i => $item)
                    @php
                      $dayVal  = is_array($item) ? ($item['day']  ?? '') : '';
                      $fromVal = is_array($item) ? ($item['from'] ?? '') : '';
                      $toVal   = is_array($item) ? ($item['to']   ?? '') : '';
                    @endphp

                    <div class="di-repeat-item d-flex align-items-center gap-2 flex-nowrap avail-row">
                      <select name="availability_schedule[{{ $i }}][day]"
                              class="form-select di-control flex-shrink-0 @error('availability_schedule.'.$i.'.day') is-invalid @enderror"
                              style="max-width:180px">
                        <option value="">Day</option>
                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
                          <option value="{{ $d }}" @selected($dayVal === $d)>{{ $d }}</option>
                        @endforeach
                      </select>

                      <input type="time"
                             name="availability_schedule[{{ $i }}][from]"
                             value="{{ $fromVal }}"
                             class="form-control di-control flex-shrink-0 @error('availability_schedule.'.$i.'.from') is-invalid @enderror"
                             style="max-width:160px">

                      <input type="time"
                             name="availability_schedule[{{ $i }}][to]"
                             value="{{ $toVal }}"
                             class="form-control di-control flex-shrink-0 @error('availability_schedule.'.$i.'.to') is-invalid @enderror"
                             style="max-width:160px">

                      @if($i === 0)
                        <button type="button" class="btn di-mini-btn flex-shrink-0 addAvailBtnRow">
                          <i class="fa-solid fa-plus"></i>
                        </button>
                      @endif

                      <button type="button" class="btn di-x-btn remove-avail flex-shrink-0" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('availability_schedule.'.$i.'.day') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('availability_schedule.'.$i.'.from') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('availability_schedule.'.$i.'.to') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- Social URLs -->
            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label appointment-label" for="facebook">Facebook URL</label>
                <input type="url" id="facebook" name="facebook"
                       value="{{ old('facebook', $info->facebook ?? '') }}"
                       class="form-control di-control @error('facebook') is-invalid @enderror"
                       placeholder="Facebook">
                @error('facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label appointment-label" for="instagram">Instagram URL</label>
                <input type="url" id="instagram" name="instagram"
                       value="{{ old('instagram', $info->instagram ?? '') }}"
                       class="form-control di-control @error('instagram') is-invalid @enderror"
                       placeholder="Instagram">
                @error('instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label appointment-label" for="twitter">Twitter URL</label>
                <input type="url" id="twitter" name="twitter"
                       value="{{ old('twitter', $info->twitter ?? '') }}"
                       class="form-control di-control @error('twitter') is-invalid @enderror"
                       placeholder="Twitter">
                @error('twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- ✅ Skills (Name + % + Add + X same line) -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label mb-2">Skills</label>

                <div id="skillsWrap" class="di-repeat-wrap">
                  @foreach($skillsArr as $i => $sk)
                    @php
                      $skName = is_array($sk) ? ($sk['name'] ?? '') : '';
                      $skVal  = is_array($sk) ? ($sk['value'] ?? 0) : 0;
                    @endphp

                    <div class="di-skill-row d-flex align-items-center gap-2 flex-nowrap">
                      <input type="text"
                             name="skills[{{ $i }}][name]"
                             value="{{ $skName }}"
                             class="form-control di-control skill-name flex-grow-1 @error('skills.'.$i.'.name') is-invalid @enderror"
                             placeholder="Skill name">

                      <input type="number"
                             name="skills[{{ $i }}][value]"
                             value="{{ $skVal }}"
                             min="0" max="100"
                             class="form-control di-control di-skill-val skill-value flex-shrink-0 @error('skills.'.$i.'.value') is-invalid @enderror"
                             style="max-width:140px"
                             placeholder="%">

                      @if($i === 0)
                        <button type="button" class="btn di-mini-btn flex-shrink-0 addSkillBtnRow">
                          <i class="fa-solid fa-plus"></i>
                        </button>
                      @endif

                      <button type="button" class="btn di-x-btn remove-skill flex-shrink-0" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('skills.'.$i.'.name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('skills.'.$i.'.value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- ✅ Activities (Input + Add + X same line) -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label mb-2">Activities</label>

                <div id="actWrap" class="di-repeat-wrap">
                  @foreach($activitiesArr as $i => $a)
                    <div class="di-repeat-item d-flex align-items-center gap-2 flex-nowrap">
                      <input type="text"
                             name="activities[]"
                             value="{{ $a }}"
                             class="form-control di-control flex-grow-1 @error('activities.'.$i) is-invalid @enderror"
                             placeholder="Enter activity">

                      @if($i === 0)
                        <button type="button" class="btn di-mini-btn flex-shrink-0 addActBtnRow">
                          <i class="fa-solid fa-plus"></i>
                        </button>
                      @endif

                      <button type="button" class="btn di-x-btn remove-item flex-shrink-0" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('activities.'.$i) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- Social Link -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label" for="social_link">Social Link</label>
                <input type="text" id="social_link" name="social_link"
                       class="form-control di-control @error('social_link') is-invalid @enderror"
                       placeholder="Paste your social link (WhatsApp / Facebook / Instagram / Website)"
                       value="{{ old('social_link', $info->social_link ?? '') }}">
                <div class="form-text">
                  Note: This link will be automatically converted into a QR code inside the
                  <strong>Prescription</strong>, <strong>Medical Report</strong>,
                  <strong>Diagnosis</strong>, and <strong>Referral</strong>.
                </div>
                @error('social_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- About -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label" for="about">About</label>
                <textarea id="about" name="about" rows="7"
                  class="form-control di-control di-textarea @error('about') is-invalid @enderror"
                  placeholder="Describe about the doctor">{{ old('about', $info->about ?? '') }}</textarea>
                @error('about') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

          </div>

          <div class="mt-3 mt-md-4">
            <button type="submit" class="btn di-save-btn text-light w-100">Save</button>
          </div>

        </form>
      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const success = document.getElementById('successAlert');
  if (success) setTimeout(() => (success.style.display = 'none'), 3000);

  const fileInput = document.getElementById('image');
  const fileBtn = document.getElementById('imageBtn');
  const fileText = document.getElementById('imageText');
  if (fileBtn && fileInput && fileText) {
    fileBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => fileText.value = fileInput.files?.[0]?.name || '');
  }

  // ================== Specialization ==================
  const specWrap = document.getElementById('specWrap');
  const makeSpecRow = (val='') => {
    const row = document.createElement('div');
    row.className = 'di-repeat-item d-flex align-items-center gap-2 flex-nowrap';
    row.innerHTML = `
      <input type="text" name="Specialization[]" class="form-control di-control flex-grow-1" placeholder="Enter specialization" value="${String(val).replace(/"/g,'&quot;')}">
      <button type="button" class="btn di-x-btn remove-item flex-shrink-0" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button>
    `;
    return row;
  };
  document.addEventListener('click', (e) => {
    if (e.target.closest('.addSpecBtnRow')) {
      specWrap.appendChild(makeSpecRow(''));
      return;
    }
  });

  // ================== Activities ==================
  const actWrap = document.getElementById('actWrap');
  const makeActRow = (val='') => {
    const row = document.createElement('div');
    row.className = 'di-repeat-item d-flex align-items-center gap-2 flex-nowrap';
    row.innerHTML = `
      <input type="text" name="activities[]" class="form-control di-control flex-grow-1" placeholder="Enter activity" value="${String(val).replace(/"/g,'&quot;')}">
      <button type="button" class="btn di-x-btn remove-item flex-shrink-0" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button>
    `;
    return row;
  };
  document.addEventListener('click', (e) => {
    if (e.target.closest('.addActBtnRow')) {
      actWrap.appendChild(makeActRow(''));
      return;
    }
  });

  // remove for spec + activities (shared .remove-item)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    const item = btn.closest('.di-repeat-item');
    if (!item) return;
    const parent = item.parentElement;
    item.remove();
    // keep at least one
    if (parent && parent.children.length === 0) {
      if (parent.id === 'specWrap') parent.appendChild(makeSpecRow(''));
      if (parent.id === 'actWrap') parent.appendChild(makeActRow(''));
    }
  });

  // ================== Visit Types ==================
  const visitWrap = document.getElementById('visitTypesWrap');
  const makeVisitRow = (type='', price=0) => {
    const row = document.createElement('div');
    row.className = 'di-repeat-item d-flex align-items-center gap-2 flex-nowrap visit-type-row';
    row.innerHTML = `
      <input type="text" class="form-control di-control vt-type flex-grow-1" placeholder="Type (e.g. Consultation, Follow-up)" value="${String(type).replace(/"/g,'&quot;')}">
      <input type="number" class="form-control di-control vt-price flex-shrink-0" style="max-width:160px" min="0" step="0.01" placeholder="Price" value="${Number(price)||0}">
      <button type="button" class="btn di-x-btn remove-visit-type flex-shrink-0" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button>
    `;
    return row;
  };
  const renumberVisit = () => {
    const rows = visitWrap.querySelectorAll('.visit-type-row');
    rows.forEach((row, idx) => {
      row.querySelector('.vt-type').name  = `visit_types[${idx}][type]`;
      row.querySelector('.vt-price').name = `visit_types[${idx}][price]`;
    });
  };
  document.addEventListener('click', (e) => {
    if (e.target.closest('.addVisitBtnRow')) {
      visitWrap.appendChild(makeVisitRow('', 0));
      renumberVisit();
      return;
    }
    const rm = e.target.closest('.remove-visit-type');
    if (rm) {
      rm.closest('.visit-type-row')?.remove();
      if (visitWrap.querySelectorAll('.visit-type-row').length === 0) visitWrap.appendChild(makeVisitRow('',0));
      renumberVisit();
    }
  });
  renumberVisit();

  // ================== Skills ==================
  const skillsWrap = document.getElementById('skillsWrap');
  const makeSkillRow = (name='', value=0) => {
    const row = document.createElement('div');
    row.className = 'di-skill-row d-flex align-items-center gap-2 flex-nowrap';
    row.innerHTML = `
      <input type="text" class="form-control di-control skill-name flex-grow-1" placeholder="Skill name" value="${String(name).replace(/"/g,'&quot;')}">
      <input type="number" class="form-control di-control di-skill-val skill-value flex-shrink-0" style="max-width:140px" min="0" max="100" placeholder="%" value="${Number(value)||0}">
      <button type="button" class="btn di-x-btn remove-skill flex-shrink-0" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button>
    `;
    return row;
  };
  const renumberSkills = () => {
    const rows = skillsWrap.querySelectorAll('.di-skill-row');
    rows.forEach((row, idx) => {
      row.querySelector('.skill-name').name  = `skills[${idx}][name]`;
      row.querySelector('.skill-value').name = `skills[${idx}][value]`;
    });
  };
  document.addEventListener('click', (e) => {
    if (e.target.closest('.addSkillBtnRow')) {
      skillsWrap.appendChild(makeSkillRow('', 0));
      renumberSkills();
      return;
    }
    const rm = e.target.closest('.remove-skill');
    if (rm) {
      rm.closest('.di-skill-row')?.remove();
      if (skillsWrap.querySelectorAll('.di-skill-row').length === 0) skillsWrap.appendChild(makeSkillRow('',0));
      renumberSkills();
    }
  });
  renumberSkills();

  // ================== Availability ==================
  const availWrap = document.getElementById('availWrap');
  const makeAvailRow = (day='', from='', to='') => {
    const row = document.createElement('div');
    row.className = 'di-repeat-item d-flex align-items-center gap-2 flex-nowrap avail-row';
    row.innerHTML = `
      <select class="form-select di-control flex-shrink-0" style="max-width:180px">
        <option value="">Day</option>
        ${['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].map(d => `<option value="${d}" ${d===day?'selected':''}>${d}</option>`).join('')}
      </select>
      <input type="time" class="form-control di-control flex-shrink-0" style="max-width:160px" value="${from}">
      <input type="time" class="form-control di-control flex-shrink-0" style="max-width:160px" value="${to}">
      <button type="button" class="btn di-x-btn remove-avail flex-shrink-0" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button>
    `;
    return row;
  };
  const renumberAvail = () => {
    const rows = availWrap.querySelectorAll('.avail-row');
    rows.forEach((row, idx) => {
      const sel = row.querySelector('select');
      const times = row.querySelectorAll('input[type="time"]');
      sel.name = `availability_schedule[${idx}][day]`;
      times[0].name = `availability_schedule[${idx}][from]`;
      times[1].name = `availability_schedule[${idx}][to]`;
    });
  };
  document.addEventListener('click', (e) => {
    if (e.target.closest('.addAvailBtnRow')) {
      availWrap.appendChild(makeAvailRow('', '', ''));
      renumberAvail();
      return;
    }
    const rm = e.target.closest('.remove-avail');
    if (rm) {
      rm.closest('.avail-row')?.remove();
      if (availWrap.querySelectorAll('.avail-row').length === 0) availWrap.appendChild(makeAvailRow());
      renumberAvail();
    }
  });
  renumberAvail();
});
</script>

@endsection
