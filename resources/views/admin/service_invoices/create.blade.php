@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/createInvoice.css') }}" />

@php
  $patients = $patients ?? collect();
  $services = $services ?? collect();

  $oldPatientId = old('patient_id', '');
  $useNewPatient = ($oldPatientId === '__new__');

  // ✅ old items: فاضي أول مرة
  $oldItems = old('items');
  if (!is_array($oldItems)) {
    $oldItems = [];
  }

  $oldTaxPercent = old('tax_percent', 10);
  $oldDiscount   = old('discount', 0);
@endphp

<main class="flex-grow-1">
  <div class="container-fluid ci-container py-4">

    {{-- ================= Alerts ================= --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error') || $errors->any())
      <div class="alert alert-danger mb-3">
        <strong>There were some problems:</strong>
        <ul class="mb-0 mt-2">
          @if(session('error')) <li>{{ session('error') }}</li> @endif
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('service-invoices.store') }}" id="invoiceForm">
      @csrf

      {{-- ================= Top Header ================= --}}
      <div class="d-flex align-items-start justify-content-between gap-3 mb-4 flex-wrap">
        <div>
          <h3 class="mb-1 ci-title">Create New Invoice</h3>
          <div class="ci-subtitle">
            Invoice will be generated automatically | Date: {{ now()->format('M d, Y') }}
          </div>
        </div>

        <button type="submit" class="btn btn-primary ci-btn-primary d-inline-flex align-items-center gap-2">
          <i class="bi bi-floppy"></i>
          <span>Save Invoice</span>
        </button>
      </div>

      <div class="row g-4">

        {{-- ================= Left Column ================= --}}
        <div class="col-12 col-xl-8">

          {{-- ================= Patient Information ================= --}}
          <section class="card ci-card mb-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                  <div class="ci-sec-ico"><i class="bi bi-person"></i></div>
                  <div class="ci-sec-title">Patient Information</div>
                </div>

             
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label ci-label">Patient</label>
                  <select
                    name="patient_id"
                    id="patientSelect"
                    class="form-select ci-input @error('patient_id') is-invalid @enderror"
                  >
                    <option value="">Select patient...</option>

                    @foreach($patients as $p)
                      <option value="{{ $p->id }}" {{ (string)$oldPatientId === (string)$p->id ? 'selected' : '' }}>
                        {{ $p->name }} {{ $p->phone ? ' - ' . $p->phone : '' }}
                      </option>
                    @endforeach

      
                  </select>
                  @error('patient_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6" id="newPatientWrap" style="{{ $useNewPatient ? '' : 'display:none;' }}">
                  <label class="form-label ci-label">New Patient Name</label>
                  <input
                    type="text"
                    name="patient_name_new"
                    id="newPatientName"
                    class="form-control ci-input @error('patient_name_new') is-invalid @enderror"
                    value="{{ old('patient_name_new') }}"
                    placeholder="Type patient name..."
                  >
                  @error('patient_name_new') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6" id="newPatientPhoneWrap" style="{{ $useNewPatient ? '' : 'display:none;' }}">
                  <label class="form-label ci-label">New Patient Phone</label>
                  <input
                    type="text"
                    name="patient_phone_new"
                    class="form-control ci-input @error('patient_phone_new') is-invalid @enderror"
                    value="{{ old('patient_phone_new') }}"
                    placeholder="01xxxxxxxxx"
                  >
                  @error('patient_phone_new') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6" id="newPatientCodeWrap" style="{{ $useNewPatient ? '' : 'display:none;' }}">
                  <label class="form-label ci-label">Patient Code (Optional)</label>
                  <input
                    type="text"
                    name="patient_code_new"
                    class="form-control ci-input @error('patient_code_new') is-invalid @enderror"
                    value="{{ old('patient_code_new') }}"
                    placeholder="PID-88392"
                  >
                  @error('patient_code_new') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6" id="newPatientIdNumberWrap" style="{{ $useNewPatient ? '' : 'display:none;' }}">
                  <label class="form-label ci-label">ID Number (Optional)</label>
                  <input
                    type="text"
                    name="id_number"
                    class="form-control ci-input @error('id_number') is-invalid @enderror"
                    value="{{ old('id_number') }}"
                    placeholder="National ID / Passport..."
                  >
                  @error('id_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label ci-label">
                    Insurance Provider <span class="ci-muted">(Optional)</span>
                  </label>
                  <select name="insurance_provider" class="form-select ci-input @error('insurance_provider') is-invalid @enderror">
                    <option value="">Select Provider</option>
                    @foreach(['AXA','MetLife','Other'] as $prov)
                      <option value="{{ $prov }}" {{ old('insurance_provider') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                  </select>
                  @error('insurance_provider') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
          </section>

          {{-- ================= Add Medical Service ================= --}}
          <section class="card ci-card mb-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="ci-sec-ico"><i class="bi bi-plus-square"></i></div>
                <div class="ci-sec-title">Add Medical Service</div>
              </div>

              <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-7">
                  <label class="form-label ci-label">Service</label>

                  @if($services->count())
                    <select class="form-select ci-input" id="servicePick">
                      <option value="">Select service...</option>

                      @foreach($services as $s)
                        <option
                          value="{{ $s->id }}"
                          data-name="{{ $s->name }}"
                          data-price="{{ $s->pivot->price ?? $s->price ?? 0 }}"
                        >
                          {{ $s->name }}
                        </option>
                      @endforeach
                    </select>
                  @else
                    <input class="form-control ci-input" id="serviceNamePick" placeholder="Service name..." />
                  @endif
                </div>

           @php $isDoctor = (auth()->user()->role ?? '') === 'doctor'; @endphp

@if($isDoctor)
  <div class="col-12 col-lg-5">
    <label class="form-label ci-label">Doctor</label>
    <input
      type="text"
      class="form-control ci-input"
      value="{{ auth()->user()->name }}"
      readonly
    />
  </div>
@else
  <div class="col-12 col-lg-5">
    <label class="form-label ci-label">Doctor</label>
    <select name="doctor_id" class="form-select ci-input">
      @foreach($doctors as $d)
        <option value="{{ $d->id }}" {{ (string)$selectedDoctorId === (string)$d->id ? 'selected' : '' }}>
          {{ $d->user->name ?? ('Doctor #' . $d->id) }}
        </option>
      @endforeach
    </select>
  </div>
@endif


                <div class="col-12 col-md-4">
                  <label class="form-label ci-label">Department</label>
                  <input class="form-control ci-input" id="deptPick" placeholder="Department..." />
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label ci-label">Quantity</label>
                  <input type="number" class="form-control ci-input" id="qtyPick" value="1" min="1" />
                </div>

                <div class="col-12 col-md-5">
                  <label class="form-label ci-label">Price</label>
                  <div class="input-group">
                    <span class="input-group-text ci-input-ico">$</span>
                    <input type="number" class="form-control ci-input" id="pricePick" value="0" step="0.01" min="0" />
                    <button class="btn btn-primary ci-btn-primary px-4 d-inline-flex align-items-center gap-2"
                      type="button" id="addServiceBtn">
                      <i class="bi bi-plus-circle"></i>
                      <span>Add Service</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>

          {{-- ================= Invoice Items ================= --}}
          <section class="card ci-card mb-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="ci-sec-ico"><i class="bi bi-table"></i></div>
                <div class="ci-sec-title">Invoice Items</div>
              </div>

              <div class="table-responsive">
                <table class="table ci-table align-middle mb-0">
                  <thead>
                    <tr>
                      <th class="ci-th">SERVICE DESCRIPTION</th>
                      <th class="ci-th text-center">QTY</th>
                      <th class="ci-th text-end">UNIT PRICE</th>
                      <th class="ci-th text-end">SUBTOTAL</th>
                      <th class="ci-th text-end">ACTION</th>
                    </tr>
                  </thead>

                  <tbody id="itemsBody">
                    @if(count($oldItems))
                      @foreach($oldItems as $i => $it)
                        @php
                          $qty = (int)($it['qty'] ?? 1);
                          $unit = (float)($it['unit_price'] ?? 0);
                          $line = $qty * $unit;
                        @endphp
                        <tr class="item-row">
                          <td>
                            <div class="ci-item-title">{{ $it['service_name'] ?? '' }}</div>
                            <div class="ci-item-sub">
                              {{ ($it['doctor_name'] ?? '') }}{{ ($it['department'] ?? '') ? ' - ' . ($it['department'] ?? '') : '' }}
                            </div>

                            <input type="hidden" name="items[{{ $i }}][service_id]" value="{{ $it['service_id'] ?? '' }}">
                            <input type="hidden" name="items[{{ $i }}][service_name]" value="{{ $it['service_name'] ?? '' }}">
                            <input type="hidden" name="items[{{ $i }}][doctor_name]" value="{{ $it['doctor_name'] ?? '' }}">
                            <input type="hidden" name="items[{{ $i }}][department]" value="{{ $it['department'] ?? '' }}">
                          </td>

                          <td class="text-center">
                            <input type="number" class="form-control ci-input text-center qty-input"
                                   name="items[{{ $i }}][qty]" value="{{ $qty }}" min="1">
                          </td>

                          <td class="text-end">
                            <input type="number" class="form-control ci-input text-end unit-input"
                                   name="items[{{ $i }}][unit_price]" value="{{ number_format($unit, 2, '.', '') }}" step="0.01" min="0">
                          </td>

                          <td class="text-end ci-strong line-subtotal">
                            ${{ number_format($line, 2) }}
                          </td>

                          <td class="text-end">
                            <button type="button" class="btn btn-sm ci-icon-danger remove-btn" aria-label="delete">
                              <i class="bi bi-trash3"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    @else
                      {{-- ✅ فاضي أول مرة --}}
                      <tr id="emptyItemsRow">
                        <td colspan="5" class="text-center text-muted py-4">
                          No items yet. Add a service to start.
                        </td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <div class="text-center pt-3">
                <button type="button" class="btn btn-link p-0 ci-link-primary d-inline-flex align-items-center gap-2" id="addAnotherBtn">
                  <i class="bi bi-plus"></i>
                  <span>Add Another Service</span>
                </button>
              </div>
            </div>
          </section>

          {{-- ================= Notes ================= --}}
          <section class="card ci-card">
            <div class="card-body p-4">
              <div class="ci-sec-title mb-2">Notes / Payment Terms</div>
              <textarea
                name="notes"
                class="form-control ci-input @error('notes') is-invalid @enderror"
                rows="4"
                placeholder="Enter any additional notes..."
              >{{ old('notes') }}</textarea>
              @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </section>
        </div>

        {{-- ================= Right Column ================= --}}
        <div class="col-12 col-xl-4">

          {{-- ================= Payment Details ================= --}}
          <section class="card ci-card mb-4">
            <div class="card-body p-4">
              <div class="ci-sec-title mb-3">Payment Details</div>

              <div class="ci-mini-title mb-2">PAYMENT METHOD</div>
              <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method', 'cash') }}">

              <div class="row g-2 mb-3">
                @php $pm = old('payment_method', 'cash'); @endphp
                <div class="col-6">
                  <button type="button" class="btn ci-pay-btn w-100 d-inline-flex align-items-center justify-content-center gap-2 pay-btn {{ $pm==='card'?'active':'' }}" data-method="card">
                    <i class="bi bi-credit-card"></i><span>Card</span>
                  </button>
                </div>
                <div class="col-6">
                  <button type="button" class="btn ci-pay-btn w-100 d-inline-flex align-items-center justify-content-center gap-2 pay-btn {{ $pm==='cash'?'active':'' }}" data-method="cash">
                    <i class="bi bi-cash"></i><span>Cash</span>
                  </button>
                </div>
                <div class="col-6">
                  <button type="button" class="btn ci-pay-btn w-100 d-inline-flex align-items-center justify-content-center gap-2 pay-btn {{ $pm==='insurance'?'active':'' }}" data-method="insurance">
                    <i class="bi bi-shield-check"></i><span>Insur.</span>
                  </button>
                </div>
                <div class="col-6">
                  <button type="button" class="btn ci-pay-btn w-100 d-inline-flex align-items-center justify-content-center gap-2 pay-btn {{ $pm==='wallet'?'active':'' }}" data-method="wallet">
                    <i class="bi bi-wallet2"></i><span>Wallet</span>
                  </button>
                </div>
              </div>
              @error('payment_method') <div class="text-danger small">{{ $message }}</div> @enderror

              <div class="ci-mini-title mb-2">PAYMENT STATUS</div>
              <select name="payment_status" class="form-select ci-input @error('payment_status') is-invalid @enderror">
                @php $ps = old('payment_status', 'pending'); @endphp
                <option value="pending" {{ $ps==='pending'?'selected':'' }}>Pending Payment</option>
                <option value="paid" {{ $ps==='paid'?'selected':'' }}>Paid</option>
                <option value="partial" {{ $ps==='partial'?'selected':'' }}>Partially Paid</option>
              </select>
              @error('payment_status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </section>

          {{-- ================= Invoice Summary ================= --}}
          <section class="card ci-card">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="ci-sec-ico"><i class="bi bi-receipt-cutoff"></i></div>
                <div class="ci-sec-title">Invoice Summary</div>
              </div>

              <div class="ci-sum-row">
                <span class="ci-sum-label">Subtotal</span>
                <span class="ci-sum-val" id="sumSubtotal">$0.00</span>
              </div>

              <div class="ci-sum-row">
                <span class="ci-sum-label">Discount</span>
                <div class="input-group ci-sum-input">
                  <span class="input-group-text ci-input-ico">$</span>
                  <input
                    class="form-control ci-input text-end"
                    type="number"
                    name="discount"
                    id="discountInput"
                    value="{{ $oldDiscount }}"
                    step="0.01"
                    min="0"
                  />
                </div>
              </div>
              @error('discount') <div class="text-danger small">{{ $message }}</div> @enderror

              <div class="ci-sum-row align-items-center">
                <span class="ci-sum-label">Tax (%)</span>
                <input
                  class="form-control ci-input text-end"
                  style="max-width:110px"
                  type="number"
                  name="tax_percent"
                  id="taxPercentInput"
                  value="{{ $oldTaxPercent }}"
                  step="0.01"
                  min="0"
                  max="100"
                />
              </div>
              @error('tax_percent') <div class="text-danger small">{{ $message }}</div> @enderror

              <div class="ci-sum-row">
                <span class="ci-sum-label">Tax Amount</span>
                <span class="ci-sum-val" id="sumTax">$0.00</span>
              </div>

              <hr class="ci-hr" />

              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="ci-total-label">Total</div>
                <div class="ci-total-val" id="sumTotal">$0.00</div>
              </div>

              <button type="submit" class="btn btn-primary w-100 ci-btn-primary py-3 d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-printer"></i>
                <span>Save Invoice</span>
              </button>

              <a href="{{ route('service-invoices.index') }}" class="btn btn-link w-100 text-decoration-none mt-2 ci-cancel">
                Cancel
              </a>
            </div>
          </section>

        </div>
      </div>
    </form>
  </div>
</main>

<script>
(function () {
  const patientSelect = document.getElementById('patientSelect');
  const toggleNewPatientBtn = document.getElementById('toggleNewPatientBtn');

  const newWraps = [
    document.getElementById('newPatientWrap'),
    document.getElementById('newPatientPhoneWrap'),
    document.getElementById('newPatientCodeWrap'),
    document.getElementById('newPatientIdNumberWrap'),
  ].filter(Boolean);

  function showNewPatient(show) {
    newWraps.forEach(w => w.style.display = show ? '' : 'none');
    if (patientSelect) patientSelect.value = show ? '__new__' : (patientSelect.value === '__new__' ? '' : patientSelect.value);
  }

  toggleNewPatientBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    const isNew = patientSelect?.value === '__new__';
    showNewPatient(!isNew);
  });

  patientSelect?.addEventListener('change', () => {
    const isNew = patientSelect.value === '__new__';
    newWraps.forEach(w => w.style.display = isNew ? '' : 'none');
  });

  // ===== Payment method buttons
  const pmInput = document.getElementById('payment_method');
  document.querySelectorAll('.pay-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (pmInput) pmInput.value = btn.dataset.method || 'cash';
    });
  });

  // ===== Items dynamic
  const itemsBody = document.getElementById('itemsBody');
  const addServiceBtn = document.getElementById('addServiceBtn');
  const addAnotherBtn = document.getElementById('addAnotherBtn');

  const discountInput = document.getElementById('discountInput');
  const taxPercentInput = document.getElementById('taxPercentInput');

  const sumSubtotal = document.getElementById('sumSubtotal');
  const sumTax = document.getElementById('sumTax');
  const sumTotal = document.getElementById('sumTotal');

  const servicePick = document.getElementById('servicePick');
  const serviceNamePick = document.getElementById('serviceNamePick');
  const doctorPick = document.getElementById('doctorPick');
  const deptPick = document.getElementById('deptPick');
  const qtyPick = document.getElementById('qtyPick');
  const pricePick = document.getElementById('pricePick');

  function money(n) {
    n = Number(n || 0);
    return '$' + n.toFixed(2);
  }

  function recalc() {
    let subtotal = 0;

    document.querySelectorAll('#itemsBody tr.item-row').forEach(row => {
      const qty = Number(row.querySelector('.qty-input')?.value || 1);
      const unit = Number(row.querySelector('.unit-input')?.value || 0);
      const line = Math.max(1, qty) * Math.max(0, unit);

      subtotal += line;
      const cell = row.querySelector('.line-subtotal');
      if (cell) cell.textContent = money(line);
    });

    const discount = Math.max(0, Number(discountInput?.value || 0));
    const taxPercent = Math.max(0, Math.min(100, Number(taxPercentInput?.value || 0)));

    const base = Math.max(0, subtotal - discount);
    const tax = base * (taxPercent / 100);
    const total = base + tax;

    if (sumSubtotal) sumSubtotal.textContent = money(subtotal);
    if (sumTax) sumTax.textContent = money(tax);
    if (sumTotal) sumTotal.textContent = money(total);
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#itemsBody tr.item-row');
    rows.forEach((row, idx) => {
      row.querySelectorAll('input[name^="items["]').forEach(input => {
        input.name = input.name.replace(/items\[\d+\]/, `items[${idx}]`);
      });
    });
  }

  function addRow({service_id='', service_name='', doctor_name='', department='', qty=1, unit_price=0} = {}) {
    const idx = document.querySelectorAll('#itemsBody tr.item-row').length;

    const line = Math.max(1, Number(qty)) * Math.max(0, Number(unit_price));

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
      <td>
        <div class="ci-item-title">${escapeHtml(service_name)}</div>
        <div class="ci-item-sub">${escapeHtml(doctor_name)}${department ? ' - ' + escapeHtml(department) : ''}</div>

        <input type="hidden" name="items[${idx}][service_id]" value="${escapeAttr(service_id)}">
        <input type="hidden" name="items[${idx}][service_name]" value="${escapeAttr(service_name)}">
        <input type="hidden" name="items[${idx}][doctor_name]" value="${escapeAttr(doctor_name)}">
        <input type="hidden" name="items[${idx}][department]" value="${escapeAttr(department)}">
      </td>

      <td class="text-center">
        <input type="number" class="form-control ci-input text-center qty-input"
               name="items[${idx}][qty]" value="${Number(qty) || 1}" min="1">
      </td>

      <td class="text-end">
        <input type="number" class="form-control ci-input text-end unit-input"
               name="items[${idx}][unit_price]" value="${Number(unit_price || 0).toFixed(2)}" step="0.01" min="0">
      </td>

      <td class="text-end ci-strong line-subtotal">${money(line)}</td>

      <td class="text-end">
        <button type="button" class="btn btn-sm ci-icon-danger remove-btn" aria-label="delete">
          <i class="bi bi-trash3"></i>
        </button>
      </td>
    `;

    // ✅ remove empty message row
    const emptyRow = document.getElementById('emptyItemsRow');
    if (emptyRow) emptyRow.remove();

    itemsBody?.appendChild(tr);

    tr.querySelector('.remove-btn')?.addEventListener('click', () => {
      tr.remove();
      reindexRows();
      recalc();

      // ✅ لو مفيش ولا row، رجّع empty row
      if (!document.querySelector('#itemsBody tr.item-row')) {
        itemsBody.innerHTML = `
          <tr id="emptyItemsRow">
            <td colspan="5" class="text-center text-muted py-4">
              No items yet. Add a service to start.
            </td>
          </tr>
        `;
      }
    });

    tr.querySelector('.qty-input')?.addEventListener('input', recalc);
    tr.querySelector('.unit-input')?.addEventListener('input', recalc);

    recalc();
  }

  function escapeHtml(str){ return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  function escapeAttr(str){ return escapeHtml(str).replace(/"/g,'&quot;'); }

  // ✅ auto-fill price from select option
  servicePick?.addEventListener('change', () => {
    const opt = servicePick.options[servicePick.selectedIndex];
    const price = opt?.dataset?.price ?? 0;
    if (pricePick) pricePick.value = Number(price || 0);
  });

  function getPickedService() {
    let sid = '';
    let sname = '';

    if (servicePick) {
      const opt = servicePick.options[servicePick.selectedIndex];
      sid = opt?.value || '';
      sname = opt?.dataset?.name || '';
      if (!sid) sname = '';
    } else if (serviceNamePick) {
      sname = serviceNamePick.value.trim();
    }

    return { sid, sname };
  }

  function handleAddPicked() {
    const { sid, sname } = getPickedService();

    const doctor = (doctorPick?.value || '').trim();
    const dept = (deptPick?.value || '').trim();
    const qty = Number(qtyPick?.value || 1);
    const price = Number(pricePick?.value || 0);

    if (!sname) {
      alert('Please select / type a service name.');
      return;
    }

    addRow({
      service_id: sid,
      service_name: sname,
      doctor_name: doctor,
      department: dept,
      qty: qty,
      unit_price: price
    });

    // reset pickers
    if (servicePick) servicePick.value = '';
    if (serviceNamePick) serviceNamePick.value = '';
    if (doctorPick) doctorPick.value = '';
    if (deptPick) deptPick.value = '';
    if (qtyPick) qtyPick.value = 1;
    if (pricePick) pricePick.value = 0;
  }

  addServiceBtn?.addEventListener('click', handleAddPicked);
  addAnotherBtn?.addEventListener('click', (e) => { e.preventDefault(); handleAddPicked(); });

  // existing rows listeners (old items)
  document.querySelectorAll('#itemsBody .remove-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('tr')?.remove();
      reindexRows();
      recalc();

      if (!document.querySelector('#itemsBody tr.item-row')) {
        itemsBody.innerHTML = `
          <tr id="emptyItemsRow">
            <td colspan="5" class="text-center text-muted py-4">
              No items yet. Add a service to start.
            </td>
          </tr>
        `;
      }
    });
  });
  document.querySelectorAll('#itemsBody .qty-input').forEach(inp => inp.addEventListener('input', recalc));
  document.querySelectorAll('#itemsBody .unit-input').forEach(inp => inp.addEventListener('input', recalc));

  discountInput?.addEventListener('input', recalc);
  taxPercentInput?.addEventListener('input', recalc);

  // init recalc
  recalc();

  // init new patient wrap based on selection
  const initIsNew = patientSelect?.value === '__new__';
  newWraps.forEach(w => w.style.display = initIsNew ? '' : 'none');

})();
</script>

@endsection
