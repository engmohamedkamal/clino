@extends('layouts.dash')
@section('dash-content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/addDiagonis.css') }}" />

@php
  // هل التشخيص مرتبط بمريض موجود ولا اسم حر
  $isNewPatient = empty($diagnosis->patient_id);

  $oldPatientId = old('patient_id', $isNewPatient ? '__new__' : $diagnosis->patient_id);
  $oldNewName   = old('patient_name_new', $isNewPatient ? $diagnosis->patient_name : '');
@endphp

<main class="ad-main">
  <div class="ad-wrap">

    {{-- ================= Header ================= --}}
    <header class="ad-header">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <h1 class="ad-title">Edit Diagnosis</h1>
    </header>

    {{-- ================= Card ================= --}}
    <div class="ad-card">
      <form method="POST" action="{{ route('diagnoses.update', $diagnosis) }}">
        @csrf
        @method('PUT')

        {{-- ================= Patient Select ================= --}}
        <div class="ad-group">
          <label for="patientSelect">Patient</label>

          <select
            name="patient_id"
            id="patientSelect"
            class="ad-input @error('patient_id') is-invalid @enderror"
          >
            <option value="">Select patient...</option>

            @foreach(($patients ?? collect()) as $p)
              <option value="{{ $p->id }}"
                {{ (string)$oldPatientId === (string)$p->id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach

            <option value="__new__" {{ (string)$oldPatientId === '__new__' ? 'selected' : '' }}>
              + Add new patient
            </option>
          </select>

          @error('patient_id')
            <div class="ad-error">{{ $message }}</div>
          @enderror
        </div>

        {{-- ================= New Patient Name ================= --}}
        <div class="ad-group" id="newPatientWrap" style="{{ (string)$oldPatientId === '__new__' ? '' : 'display:none;' }}">
          <label for="newPatientInput">New Patient Name</label>

          <input
            type="text"
            name="patient_name_new"
            id="newPatientInput"
            value="{{ $oldNewName }}"
            class="ad-input @error('patient_name_new') is-invalid @enderror"
            placeholder="Type patient name..."
            {{ (string)$oldPatientId === '__new__' ? 'required' : '' }}
          >

          @error('patient_name_new')
            <div class="ad-error">{{ $message }}</div>
          @enderror
        </div>

        {{-- ================= Public Diagnosis ================= --}}
        <div class="ad-group">
          <label>Public Diagnosis</label>

          <input
            type="text"
            name="public_diagnosis"
            value="{{ old('public_diagnosis', $diagnosis->public_diagnosis) }}"
            class="ad-input @error('public_diagnosis') is-invalid @enderror"
            placeholder="Public diagnosis"
          >

          @error('public_diagnosis')
            <div class="ad-error">{{ $message }}</div>
          @enderror
        </div>

        {{-- ================= Private Diagnosis ================= --}}
        <div class="ad-group">
          <label>Private Diagnosis</label>

          <input
            type="text"
            name="private_diagnosis"
            value="{{ old('private_diagnosis', $diagnosis->private_diagnosis) }}"
            class="ad-input @error('private_diagnosis') is-invalid @enderror"
            placeholder="Private diagnosis"
          >

          @error('private_diagnosis')
            <div class="ad-error">{{ $message }}</div>
          @enderror
        </div>

        {{-- ================= Actions ================= --}}
        <div class="ad-actions">
          <button type="submit" class="ad-btn">
            Update Diagnosis
          </button>

        
        </div>

      </form>
    </div>

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
