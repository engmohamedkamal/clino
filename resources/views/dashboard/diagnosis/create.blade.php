@extends('layouts.dash')
@section('dash-content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('CSS/addDiagonis.css') }}" />

@php
  // حفظ الاختيارات بعد الفاليديشن
  $oldPatientId   = old('patient_id');
  $oldPatientName = old('patient_name'); // input search
  $oldNewName     = old('patient_name_new');

  // لو المستخدم كان مختار new
  $useNew = old('patient_mode') === '__new__' || $oldPatientId === '__new__';

  // لو جاي patient_id قديم ومحتاج نجيب اسمه من الليست (اختياري)
  $selectedName = '';
  if(!$useNew && !empty($oldPatientId) && isset($patients)){
    $found = collect($patients)->firstWhere('id', (int)$oldPatientId) ?? collect($patients)->firstWhere('id', $oldPatientId);
    $selectedName = $found->name ?? '';
  }

  $patientNameValue = $oldPatientName ?: $selectedName;
@endphp

<main class="ad-main">
  <header class="ad-header">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    <h1 class="ad-title">Add Diagnosis</h1>
  </header>

  <div class="ad-card">
    <form method="POST" action="{{ route('diagnoses.store') }}">
      @csrf

      {{-- ✅ ده اللي هيتبعت للباك --}}
      <input type="hidden" name="patient_id" id="patientId" value="{{ $useNew ? '__new__' : ($oldPatientId ?? '') }}">
      <input type="hidden" name="patient_mode" id="patientMode" value="{{ $useNew ? '__new__' : 'existing' }}">

      <div class="ad-group">
        <label>Patient</label>

        {{-- ✅ Searchable input --}}
        <input
          type="text"
          name="patient_name"
          id="patientName"
          class="ad-input @error('patient_id') is-invalid @enderror"
          placeholder="Search patient..."
          list="patientsList"
          autocomplete="off"
          value="{{ $useNew ? '__new__' : $patientNameValue }}"
        >

        {{-- ✅ Add new patient first option --}}
        <datalist id="patientsList">
          {{-- <option value="__new__">+ Add new patient</option> --}}

          @foreach(($patients ?? collect()) as $p)
            <option value="{{ $p->name }}" data-id="{{ $p->id }}"></option>
          @endforeach
        </datalist>

        @error('patient_id')
          <div class="ad-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="ad-group" id="newPatientWrap" style="{{ $useNew ? '' : 'display:none;' }}">
        <label>New Patient Name</label>
        <input
          type="text"
          name="patient_name_new"
          id="newPatientInput"
          value="{{ $oldNewName }}"
          class="ad-input @error('patient_name_new') is-invalid @enderror"
          placeholder="Type patient name..."
          {{ $useNew ? 'required' : '' }}
        >
        @error('patient_name_new')
          <div class="ad-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="ad-group">
        <label>Public Diagnosis</label>
        <input
          type="text"
          name="public_diagnosis"
          value="{{ old('public_diagnosis') }}"
          class="ad-input @error('public_diagnosis') is-invalid @enderror"
          placeholder="Public diagnosis"
        >
        @error('public_diagnosis')
          <div class="ad-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="ad-group">
        <label>Private Diagnosis</label>
        <input
          type="text"
          name="private_diagnosis"
          value="{{ old('private_diagnosis') }}"
          class="ad-input @error('private_diagnosis') is-invalid @enderror"
          placeholder="Private diagnosis"
        >
        @error('private_diagnosis')
          <div class="ad-error">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="ad-btn">Save Diagnosis</button>
    </form>
  </div>
</main>

<script>
(function () {
  const nameInput = document.getElementById('patientName');
  const idInput   = document.getElementById('patientId');
  const modeInput = document.getElementById('patientMode');
  const list      = document.getElementById('patientsList');

  const wrap  = document.getElementById('newPatientWrap');
  const input = document.getElementById('newPatientInput');

  if (!nameInput || !idInput || !modeInput || !list || !wrap || !input) return;

  function setNewMode(isNew) {
    wrap.style.display = isNew ? '' : 'none';
    input.required = isNew;

    if (isNew) {
      idInput.value = '__new__';
      modeInput.value = '__new__';
    } else {
      modeInput.value = 'existing';
      // لو رجعنا existing نمسح new name
      input.value = '';
    }
  }

  function sync() {
    const val = (nameInput.value || '').trim();

    // لو اختار add new
    if (val === '__new__' || val.toLowerCase() === '+ add new patient') {
      setNewMode(true);
      return;
    }

    // يبحث عن option مطابق للاسم
    const opt = Array.from(list.options).find(o => (o.value || '').trim() === val);

    // لو الاسم مطابق لواحد من المرضى (عنده data-id)
    if (opt && opt.dataset && opt.dataset.id) {
      setNewMode(false);
      idInput.value = opt.dataset.id;
      return;
    }

    // لو كتب اسم مش موجود: نخليه invalid (patient_id فاضي) عشان الفاليديشن يمسكه
    setNewMode(false);
    idInput.value = '';
  }

  nameInput.addEventListener('input', sync);
  nameInput.addEventListener('change', sync);

  // init
  sync();
})();
</script>

@endsection
