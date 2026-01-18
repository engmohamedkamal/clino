@extends('layouts.dash')
@section('dash-content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('CSS/addDiagonis.css') }}" />
<main class="ad-main">
  <header class="ad-header">
    <h1 class="ad-title">Add Diagnosis</h1>
  </header>
  <div class="ad-card">
    <form method="POST" action="{{ route('diagnoses.store') }}">
      @csrf
      @php
        $oldPatientId = old('patient_id');
        $oldNewName   = old('patient_name_new');
        $useNew       = ($oldPatientId === '__new__');
      @endphp

      <div class="ad-group">
        <label>Patient</label>

        <select
          name="patient_id"
          id="patientSelect"
          class="ad-input @error('patient_id') is-invalid @enderror"
        >
          <option value="">Select patient...</option>

          @foreach(($patients ?? collect()) as $p)
            <option value="{{ $p->id }}" {{ (string)$oldPatientId === (string)$p->id ? 'selected' : '' }}>
              {{ $p->name }}
            </option>
          @endforeach

          <option value="__new__" {{ $useNew ? 'selected' : '' }}>+ Add new patient</option>
        </select>

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
  </div>
</main>
<script>
  (function () {
    const sel   = document.getElementById('patientSelect');
    const wrap  = document.getElementById('newPatientWrap');
    const input = document.getElementById('newPatientInput');
    if (!sel || !wrap || !input) return;

    function toggle() {
      const isNew = sel.value === '__new__';
      wrap.style.display = isNew ? '' : 'none';
      input.required = isNew;
      if (!isNew) input.value = '';
    }

    sel.addEventListener('change', toggle);
    toggle();
  })();
</script>
@endsection