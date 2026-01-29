@extends('layouts.dash')

@section('dash-content')
<div class="container-fluid py-3">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- ✅ Global Errors Summary --}}
  

  <div class="d-flex align-items-center justify-content-between mb-3">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    <h4 class="mb-0">Doctor ↔ Services</h4>
  </div>

  <form method="POST" action="{{ route('doctors.services.bulkUpdate') }}">
    @csrf
    @method('PUT')

    <div class="accordion" id="doctorsAccordion">
      @foreach($doctors as $i => $doctor)
        @php
          $doctorName = $doctor->user->name ?? ('Doctor #'.$doctor->id);
          $selected = $doctor->services->keyBy('id');

          // ✅ Open accordion if this doctor has any errors
          $hasDoctorErrors = collect($errors->keys())
            ->contains(fn($k) => str_starts_with($k, "doctors.$doctor->id."));

          $accordionShow = ($i == 0) || $hasDoctorErrors;
        @endphp

        <div class="accordion-item mb-2">
          <h2 class="accordion-header" id="heading-{{ $doctor->id }}">
            <button class="accordion-button {{ $accordionShow ? '' : 'collapsed' }}" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapse-{{ $doctor->id }}"
              aria-expanded="{{ $accordionShow ? 'true' : 'false' }}" aria-controls="collapse-{{ $doctor->id }}">
              <div class="d-flex flex-column">
                <span class="fw-bold">{{ $doctorName }}</span>
                <span class="text-muted small">
                  {{-- {{ $doctor->Specialization ?? '—' }} • License: {{ $doctor->license_number ?? '—' }} --}}
                </span>
              </div>
            </button>
          </h2>

          <div id="collapse-{{ $doctor->id }}" class="accordion-collapse collapse {{ $accordionShow ? 'show' : '' }}"
            aria-labelledby="heading-{{ $doctor->id }}" data-bs-parent="#doctorsAccordion">
            <div class="accordion-body">

              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="min-width: 220px;">Service</th>
                      <th style="width: 110px;">Select</th>
                      <th style="width: 180px;">Price</th>
                      <th style="width: 200px;">Duration (min)</th>
                      <th style="width: 190px;">Active</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach($services as $service)
                      @php
                        // current relation
                        $isChecked = $selected->has($service->id);
                        $pivot = $isChecked ? $selected[$service->id]->pivot : null;

                        // old checkbox override
                        $oldChecked = old("doctors.{$doctor->id}.services", null);
                        if (is_array($oldChecked)) {
                          $isChecked = in_array($service->id, $oldChecked);
                        }

                        // values
                        $priceVal  = old("doctors.{$doctor->id}.pivot.{$service->id}.price", $pivot->price ?? '');
                        $durVal    = old("doctors.{$doctor->id}.pivot.{$service->id}.duration", $pivot->duration ?? '');
                        $activeVal = old("doctors.{$doctor->id}.pivot.{$service->id}.active", isset($pivot) ? (int)$pivot->active : 1);

                        // ✅ error keys
                        $priceKey  = "doctors.{$doctor->id}.pivot.{$service->id}.price";
                        $durKey    = "doctors.{$doctor->id}.pivot.{$service->id}.duration";
                        $activeKey = "doctors.{$doctor->id}.pivot.{$service->id}.active";

                        // ✅ If there are pivot errors, force enable & checked so user can see/fix
                        $anyPivotErr = $errors->has($priceKey) || $errors->has($durKey) || $errors->has($activeKey);
                        if ($anyPivotErr) $isChecked = true;

                        $disablePivot = $isChecked ? '' : 'disabled';

                        $priceInvalid = $errors->has($priceKey) ? 'is-invalid' : '';
                        $durInvalid   = $errors->has($durKey) ? 'is-invalid' : '';
                        $actInvalid   = $errors->has($activeKey) ? 'is-invalid' : '';
                      @endphp

                      <tr>
                        <td>
                          <div class="fw-semibold">{{ $service->name }}</div>
                          <div class="text-muted small">{{ \Illuminate\Support\Str::limit($service->description, 70) }}</div>
                        </td>

                        <td>
                          <div class="form-check">
                            <input
                              class="form-check-input js-service-check"
                              type="checkbox"
                              id="doc{{ $doctor->id }}_srv{{ $service->id }}"
                              name="doctors[{{ $doctor->id }}][services][]"
                              value="{{ $service->id }}"
                              {{ $isChecked ? 'checked' : '' }}
                              data-doctor="{{ $doctor->id }}"
                              data-service="{{ $service->id }}"
                            >
                            <label class="form-check-label small" for="doc{{ $doctor->id }}_srv{{ $service->id }}">
                              Add
                            </label>
                          </div>
                        </td>

                        {{-- ✅ Price + error --}}
                        <td>
                          <input
                            type="number"
                            step="0.01"
                            class="form-control form-control-sm js-pivot-input {{ $priceInvalid }}"
                            name="doctors[{{ $doctor->id }}][pivot][{{ $service->id }}][price]"
                            value="{{ $priceVal }}"
                            placeholder="e.g. 350"
                            data-doctor="{{ $doctor->id }}"
                            data-service="{{ $service->id }}"
                            {{ $disablePivot }}
                          >
                       
                        </td>

                        {{-- ✅ Duration + error --}}
                        <td>
                          <input
                            type="number"
                            class="form-control form-control-sm js-pivot-input {{ $durInvalid }}"
                            name="doctors[{{ $doctor->id }}][pivot][{{ $service->id }}][duration]"
                            value="{{ $durVal }}"
                            placeholder="e.g. 30"
                            data-doctor="{{ $doctor->id }}"
                            data-service="{{ $service->id }}"
                            {{ $disablePivot }}
                          >
                       
                        </td>

                        {{-- ✅ Active + error --}}
                        <td>
                          <select
                            class="form-select form-select-sm js-pivot-input {{ $actInvalid }}"
                            name="doctors[{{ $doctor->id }}][pivot][{{ $service->id }}][active]"
                            data-doctor="{{ $doctor->id }}"
                            data-service="{{ $service->id }}"
                            {{ $disablePivot }}
                          >
                            <option value="1" {{ (string)$activeVal === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (string)$activeVal === '0' ? 'selected' : '' }}>Inactive</option>
                          </select>
                       
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-outline-secondary btn-sm js-select-all" data-doctor="{{ $doctor->id }}">
                  Select All
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm js-unselect-all" data-doctor="{{ $doctor->id }}">
                  Unselect All
                </button>
              </div>

            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-primary px-4" type="submit">Save All</button>
    </div>

  </form>
</div>

<script>
  document.addEventListener('change', function(e){
    if(!e.target.classList.contains('js-service-check')) return;

    const doctorId = e.target.dataset.doctor;
    const serviceId = e.target.dataset.service;
    const enabled = e.target.checked;

    document.querySelectorAll(`.js-pivot-input[data-doctor="${doctorId}"][data-service="${serviceId}"]`)
      .forEach(el => el.disabled = !enabled);
  });

  document.addEventListener('click', function(e){
    if(e.target.classList.contains('js-select-all') || e.target.classList.contains('js-unselect-all')){
      const doctorId = e.target.dataset.doctor;
      const check = e.target.classList.contains('js-select-all');

      document.querySelectorAll(`.js-service-check[data-doctor="${doctorId}"]`).forEach(cb => {
        cb.checked = check;
        cb.dispatchEvent(new Event('change', { bubbles:true }));
      });
    }
  });
</script>
@endsection
