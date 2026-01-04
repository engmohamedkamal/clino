@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('CSS/doctorInfo.css') }}">
<link rel="stylesheet" href="{{ asset('CSS/doctorProfile.css') }}">

@php
  $info = $info ?? null;

  $isEdit = !is_null($info);
  $action = $isEdit ? route('doctor-info.update', $info->id) : route('doctor-info.store');

  $existingImageName = $isEdit && !empty($info->image) ? basename($info->image) : '';

  // ✅ Prepare multi arrays with old() fallback
  $oldAvailability = old('availability_schedule', $info->availability_schedule ?? []);
  $availability = is_array($oldAvailability) ? $oldAvailability : [];

  $oldSpecs = old('Specialization', $info->Specialization ?? []);
  $specs = is_array($oldSpecs) ? $oldSpecs : [];

  $oldActivities = old('activities', $info->activities ?? []);
  $activitiesArr = is_array($oldActivities) ? $oldActivities : [];

  // skills: array of objects
  $oldSkills = old('skills', $info->skills ?? []);
  $skillsArr = is_array($oldSkills) ? $oldSkills : [];

  // ensure at least 1 row for better UX
  if (count($availability) === 0) $availability = [['day' => '', 'from' => '', 'to' => '']];
  if (count($specs) === 0) $specs = [''];
  if (count($activitiesArr) === 0) $activitiesArr = [''];
  if (count($skillsArr) === 0) $skillsArr = [['name' => '', 'value' => 0]];
@endphp

<main class="dp-main">

  <!-- Topbar -->
  <header class="dp-topbar" style="height: 175px;">
    <h1 class="dp-title">Doctor Info</h1>
  </header>

  <!-- Content -->
  <section class="content-area">
    <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
      <div class="appointment-card di-wrap">

        {{-- SUCCESS --}}
        @if (session('success'))
          <div id="successAlert" class="alert alert-success mb-3">
            {{ session('success') }}
          </div>
        @endif

        {{-- Optional: show errors summary --}}
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <strong>There are some errors:</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate>
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif

          <div class="row g-3">

            <!-- Row 1: Gender / Specialization (Multi) -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="gender">Gender</label>
                <select id="gender" name="gender" class="form-select di-control @error('gender') is-invalid @enderror">
                  <option value="" disabled {{ old('gender', $info->gender ?? '') ? '' : 'selected' }}>Select</option>
                  <option value="male" {{ old('gender', $info->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('gender', $info->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- ✅ Specialization Multi -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <div class="di-label-row">
                  <label class="form-label appointment-label mb-0">Specialization</label>
                  <button type="button" class="btn di-mini-btn" id="addSpecBtn">
                    <i class="fa-solid fa-plus"></i> Add
                  </button>
                </div>

                <div id="specWrap" class="di-repeat-wrap">
                  @foreach($specs as $i => $sp)
                    <div class="di-repeat-item">
                      <input
                        type="text"
                        name="Specialization[]"
                        value="{{ $sp }}"
                        class="form-control di-control @error('Specialization.'.$i) is-invalid @enderror"
                        placeholder="Enter specialization"
                      >
                      <button type="button" class="btn di-x-btn remove-item" aria-label="Remove">
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

            <!-- Row 2: License / Profile picture -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number"
                  value="{{ old('license_number', $info->license_number ?? '') }}"
                  class="form-control di-control @error('license_number') is-invalid @enderror"
                  placeholder="Enter Number">
                @error('license_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

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

                <input type="file" id="image" name="image" class="d-none @error('image') is-invalid @enderror"
                  accept="image/*">

                @error('image')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Row 3: DOB / Address -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob"
                  value="{{ old('dob', isset($info->dob) ? \Carbon\Carbon::parse($info->dob)->format('Y-m-d') : '') }}"
                  class="form-control di-control @error('dob') is-invalid @enderror">
                @error('dob')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label appointment-label" for="address">Address</label>
                <input type="text" id="address" name="address"
                  value="{{ old('address', $info->address ?? '') }}"
                  class="form-control di-control @error('address') is-invalid @enderror"
                  placeholder="Enter Address">
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- ✅ Availability Multi (3 inputs) -->
            <div class="col-12">
              <div class="mb-3">
                <div class="di-label-row">
                  <label class="form-label appointment-label mb-0">Availability schedule</label>
                  <button type="button" class="btn di-mini-btn" id="addAvailBtn">
                    <i class="fa-solid fa-plus"></i> Add
                  </button>
                </div>

                <div id="availWrap" class="di-repeat-wrap">
                  @foreach($availability as $i => $item)
                    @php
                      $dayVal  = is_array($item) ? ($item['day']  ?? '') : '';
                      $fromVal = is_array($item) ? ($item['from'] ?? '') : '';
                      $toVal   = is_array($item) ? ($item['to']   ?? '') : '';
                    @endphp

                    <div class="di-repeat-item row g-2 align-items-center">
                      <!-- Day -->
                      <div class="col-md-4">
                        <select
                          name="availability_schedule[{{ $i }}][day]"
                          class="form-select di-control @error('availability_schedule.'.$i.'.day') is-invalid @enderror">
                          <option value="">Day</option>
                          @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                            <option value="{{ $day }}" @selected($dayVal === $day)>{{ $day }}</option>
                          @endforeach
                        </select>
                        @error('availability_schedule.'.$i.'.day')
                          <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- From -->
                      <div class="col-md-3">
                        <input
                          type="time"
                          name="availability_schedule[{{ $i }}][from]"
                          value="{{ $fromVal }}"
                          class="form-control di-control @error('availability_schedule.'.$i.'.from') is-invalid @enderror">
                        @error('availability_schedule.'.$i.'.from')
                          <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- To -->
                      <div class="col-md-3">
                        <input
                          type="time"
                          name="availability_schedule[{{ $i }}][to]"
                          value="{{ $toVal }}"
                          class="form-control di-control @error('availability_schedule.'.$i.'.to') is-invalid @enderror">
                        @error('availability_schedule.'.$i.'.to')
                          <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Remove -->
                      <div class="col-md-2 text-end">
                        <button type="button" class="btn di-x-btn remove-avail" aria-label="Remove">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>
                    </div>
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
                @error('facebook')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label appointment-label" for="instagram">Instagram URL</label>
                <input type="url" id="instagram" name="instagram"
                  value="{{ old('instagram', $info->instagram ?? '') }}"
                  class="form-control di-control @error('instagram') is-invalid @enderror"
                  placeholder="Instagram">
                @error('instagram')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label appointment-label" for="twitter">Twitter URL</label>
                <input type="url" id="twitter" name="twitter"
                  value="{{ old('twitter', $info->twitter ?? '') }}"
                  class="form-control di-control @error('twitter') is-invalid @enderror"
                  placeholder="Twitter">
                @error('twitter')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- ✅ Skills Multi (name + % value) -->
            <div class="col-12">
              <div class="mb-3">
                <div class="di-label-row">
                  <label class="form-label appointment-label mb-0">Skills</label>
                  <button type="button" class="btn di-mini-btn" id="addSkillBtn">
                    <i class="fa-solid fa-plus"></i> Add
                  </button>
                </div>

                <div id="skillsWrap" class="di-repeat-wrap">
                  @foreach($skillsArr as $i => $sk)
                    @php
                      $skName = is_array($sk) ? ($sk['name'] ?? '') : '';
                      $skVal  = is_array($sk) ? ($sk['value'] ?? 0) : 0;
                    @endphp

                    <div class="di-skill-row">
                      <input
                        type="text"
                        name="skills[{{ $i }}][name]"
                        value="{{ $skName }}"
                        class="form-control di-control skill-name @error('skills.'.$i.'.name') is-invalid @enderror"
                        placeholder="Skill name"
                      >

                      <input
                        type="number"
                        name="skills[{{ $i }}][value]"
                        value="{{ $skVal }}"
                        min="0" max="100"
                        class="form-control di-control di-skill-val skill-value @error('skills.'.$i.'.value') is-invalid @enderror"
                        placeholder="%"
                      >

                      <button type="button" class="btn di-x-btn remove-skill" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('skills.'.$i.'.name')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    @error('skills.'.$i.'.value')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- ✅ Activities Multi -->
            <div class="col-12">
              <div class="mb-3">
                <div class="di-label-row">
                  <label class="form-label appointment-label mb-0">Activities</label>
                  <button type="button" class="btn di-mini-btn" id="addActBtn">
                    <i class="fa-solid fa-plus"></i> Add
                  </button>
                </div>

                <div id="actWrap" class="di-repeat-wrap">
                  @foreach($activitiesArr as $i => $a)
                    <div class="di-repeat-item">
                      <input
                        type="text"
                        name="activities[]"
                        value="{{ $a }}"
                        class="form-control di-control @error('activities.'.$i) is-invalid @enderror"
                        placeholder="Enter activity"
                      >
                      <button type="button" class="btn di-x-btn remove-item" aria-label="Remove">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>

                    @error('activities.'.$i)
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  @endforeach
                </div>
              </div>
            </div>

            <!-- About -->
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label appointment-label" for="about">About</label>
                <textarea id="about" name="about" rows="7"
                  class="form-control di-control di-textarea @error('about') is-invalid @enderror"
                  placeholder="Describe about the doctor">{{ old('about', $info->about ?? '') }}</textarea>
                @error('about')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

          </div>

          <!-- Save button -->
          <div class="mt-3 mt-md-4">
            <button type="submit" class="btn di-save-btn w-100">
              Save
            </button>
          </div>
        </form>

      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* =========================
     1) Auto hide success alert
  ========================== */
  const success = document.getElementById('successAlert');
  if (success) setTimeout(() => (success.style.display = 'none'), 3000);

  /* =========================
     2) File picker (image)
  ========================== */
  const fileInput = document.getElementById('image');
  const fileBtn = document.getElementById('imageBtn');
  const fileText = document.getElementById('imageText');

  if (fileBtn && fileInput && fileText) {
    fileBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      fileText.value = fileInput.files?.[0]?.name || '';
    });
  }

  /* =========================
     Helpers for repeatable text rows (spec + activities)
  ========================== */
  const makeRepeatItem = (name, placeholder = '', value = '') => {
    const wrap = document.createElement('div');
    wrap.className = 'di-repeat-item';
    wrap.innerHTML = `
      <input type="text" name="${name}" class="form-control di-control" placeholder="${placeholder}" value="${value}">
      <button type="button" class="btn di-x-btn remove-item" aria-label="Remove">
        <i class="fa-solid fa-xmark"></i>
      </button>
    `;
    return wrap;
  };

  const ensureOneRow = (parent, createFn) => {
    if (!parent) return;
    if (parent.children.length === 0) parent.appendChild(createFn());
  };

  /* =========================
     3) Specialization add/remove
  ========================== */
  const specWrap = document.getElementById('specWrap');
  const addSpecBtn = document.getElementById('addSpecBtn');

  addSpecBtn?.addEventListener('click', () => {
    specWrap.appendChild(makeRepeatItem('Specialization[]', 'Enter specialization'));
  });

  /* =========================
     4) Activities add/remove
  ========================== */
  const actWrap = document.getElementById('actWrap');
  const addActBtn = document.getElementById('addActBtn');

  addActBtn?.addEventListener('click', () => {
    actWrap.appendChild(makeRepeatItem('activities[]', 'Enter activity'));
  });

  /* =========================
     5) Delegated remove for repeat items (spec + activities فقط)
  ========================== */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;

    const item = btn.closest('.di-repeat-item');
    if (!item) return;

    const parent = item.parentElement;
    item.remove();

    if (parent?.id === 'specWrap') {
      ensureOneRow(parent, () => makeRepeatItem('Specialization[]', 'Enter specialization'));
    }
    if (parent?.id === 'actWrap') {
      ensureOneRow(parent, () => makeRepeatItem('activities[]', 'Enter activity'));
    }
  });

  /* =========================
     6) Skills add/remove + renumber
  ========================== */
  const skillsWrap = document.getElementById('skillsWrap');
  const addSkillBtn = document.getElementById('addSkillBtn');

  const renumberSkills = () => {
    const rows = skillsWrap?.querySelectorAll('.di-skill-row') || [];
    rows.forEach((row, idx) => {
      const nameInput = row.querySelector('.skill-name');
      const valInput  = row.querySelector('.skill-value');
      if (nameInput) nameInput.name = `skills[${idx}][name]`;
      if (valInput)  valInput.name  = `skills[${idx}][value]`;
    });
  };

  const makeSkillRow = (name = '', value = 0) => {
    const row = document.createElement('div');
    row.className = 'di-skill-row';

    row.innerHTML = `
      <input
        type="text"
        class="form-control di-control skill-name"
        value="${String(name).replace(/"/g, '&quot;')}"
        placeholder="Skill name"
      >

      <input
        type="number"
        class="form-control di-control di-skill-val skill-value"
        value="${Number(value) || 0}"
        min="0" max="100"
        placeholder="%"
      >

      <button type="button" class="btn di-x-btn remove-skill" aria-label="Remove">
        <i class="fa-solid fa-xmark"></i>
      </button>
    `;

    return row;
  };

  addSkillBtn?.addEventListener('click', () => {
    if (!skillsWrap) return;
    skillsWrap.appendChild(makeSkillRow('', 0));
    renumberSkills();
  });

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-skill');
    if (!btn) return;

    const row = btn.closest('.di-skill-row');
    if (!row) return;

    row.remove();

    if (skillsWrap && skillsWrap.querySelectorAll('.di-skill-row').length === 0) {
      skillsWrap.appendChild(makeSkillRow('', 0));
    }

    renumberSkills();
  });

  renumberSkills();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* =========================
     Availability (3 inputs) add/remove + renumber
  ========================== */
  const wrap = document.getElementById('availWrap');
  const addBtn = document.getElementById('addAvailBtn');
  if (!wrap || !addBtn) return;

  const makeAvailRow = (i, day = '', from = '', to = '') => {
    const row = document.createElement('div');
    row.className = 'di-repeat-item row g-2 align-items-center';
    row.innerHTML = `
      <div class="col-md-4">
        <select name="availability_schedule[${i}][day]" class="form-select di-control">
          <option value="">Day</option>
          ${['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].map(d => `
            <option value="${d}" ${d === day ? 'selected' : ''}>${d}</option>
          `).join('')}
        </select>
      </div>

      <div class="col-md-3">
        <input type="time" name="availability_schedule[${i}][from]" class="form-control di-control" value="${from}">
      </div>

      <div class="col-md-3">
        <input type="time" name="availability_schedule[${i}][to]" class="form-control di-control" value="${to}">
      </div>

      <div class="col-md-2 text-end">
        <button type="button" class="btn di-x-btn remove-avail" aria-label="Remove">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    `;
    return row;
  };

  const renumberAvail = () => {
    const rows = wrap.querySelectorAll('.di-repeat-item');
    rows.forEach((row, idx) => {
      const day  = row.querySelector('select');
      const times = row.querySelectorAll('input[type="time"]');
      const from = times[0];
      const to   = times[1];

      if (day)  day.name  = `availability_schedule[${idx}][day]`;
      if (from) from.name = `availability_schedule[${idx}][from]`;
      if (to)   to.name   = `availability_schedule[${idx}][to]`;
    });
  };

  const ensureOneAvailRow = () => {
    if (wrap.querySelectorAll('.di-repeat-item').length === 0) {
      wrap.appendChild(makeAvailRow(0));
      renumberAvail();
    }
  };

  addBtn.addEventListener('click', () => {
    const i = wrap.querySelectorAll('.di-repeat-item').length;
    wrap.appendChild(makeAvailRow(i));
    renumberAvail();
  });

  wrap.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-avail');
    if (!btn) return;

    const row = btn.closest('.di-repeat-item');
    if (!row) return;

    row.remove();
    ensureOneAvailRow();
    renumberAvail();
  });

  ensureOneAvailRow();
  renumberAvail();
});
</script>

@endsection
