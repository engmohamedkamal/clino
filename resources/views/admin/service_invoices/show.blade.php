@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/paymentReceipt.css') }}">

@php
  /** @var \App\Models\ServiceInvoice $invoice */
  $invoice = $invoice ?? $service_invoice ?? null;

  $clinicName  = $setting->name ?? 'Helper Clinic';
  $clinicAddr  = $setting->address ?? '123 Health Way, Wellness City, ST 12345';

  $receiptNo = $invoice->receipt_no ?? $invoice->invoice_no ?? ('REC-' . str_pad((string)$invoice->id, 6, '0', STR_PAD_LEFT));
  $issuedAt  = optional($invoice->issued_at ?? $invoice->created_at)->format('M d, Y');

  $patientName  = $invoice->patient_name ?? optional($invoice->patient)->name ?? '—';
  $patientPhone = $invoice->patient_phone ?? optional($invoice->patient)->phone ?? '—';
  $patientCode  = $invoice->patient_code ?? (optional($invoice->patient)->code ?? null) ?? ('PID-' . ($invoice->patient_id ?? '—'));

  $invRef    = $invoice->invoice_no ?? ('INV-' . str_pad((string)$invoice->id, 6, '0', STR_PAD_LEFT));
  $billDate  = optional($invoice->issued_at ?? $invoice->created_at)->format('M d, Y');

  // department: لو بتبعت department في items
  $department = collect($invoice->items ?? [])->pluck('department')->filter()->first() ?? '—';

  $subtotal    = (float)($invoice->subtotal ?? 0);
  $discount    = (float)($invoice->discount ?? 0);
  $taxAmount   = (float)($invoice->tax_amount ?? 0);
  $total       = (float)($invoice->total ?? 0);

  $paymentMethod = (string)($invoice->payment_method ?? 'cash');
  $paymentStatus = (string)($invoice->payment_status ?? 'pending');

  $pmLabel = match($paymentMethod) {
    'card'      => 'Credit Card',
    'cash'      => 'Cash',
    'insurance' => 'Insurance',
    'wallet'    => 'Wallet',
    default     => ucfirst($paymentMethod),
  };

  $pmIcon = match($paymentMethod) {
    'card'      => 'bi-credit-card',
    'cash'      => 'bi-cash',
    'insurance' => 'bi-shield-check',
    'wallet'    => 'bi-wallet2',
    default     => 'bi-credit-card',
  };

  // transaction id placeholder (لو عندك عمود transaction_id استبدليه)
  $txnId = $invoice->transaction_id ?? ('txn_' . str_pad((string)$invoice->id, 10, '0', STR_PAD_LEFT));

  // insurance provider
  $insuranceProvider = $invoice->insurance_provider ?? null;

  // paid amount display (لو status paid => total، لو partial ممكن يكون عندك paid_amount)
  $paidAmount = $invoice->paid_amount ?? ($paymentStatus === 'paid' ? $total : 0);
@endphp

{{-- HEADER (برا الكونتينر لكن بنفس العرض) --}}
<header class="page-header">
  <div class="page-width px-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
  <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div>
        <h3 class="fw-bold mb-1">Payment Receipt</h3>
        <p class="text-secondary mb-0">Manage and view transaction details</p>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <button class="hdr-btn" type="button" onclick="window.print()">
          <i class="bi bi-printer"></i>
          <span class="d-none d-md-inline">Print</span>
        </button>

 
      </div>

    </div>
  </div>
</header>

<main class="pb-5">
  <div class="page-width px-3">

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success my-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger my-3">{{ session('error') }}</div>
    @endif

    <section class="receipt">

      {{-- Brand --}}
      <div class="sec">
        <div class="row g-3 align-items-start">
          <div class="col-md-7">
            <div class="brand">
              <div class="brand-ico">
                <i class="bi bi-hospital fs-4"></i>
              </div>
              <h5 class="mb-0 fw-bold">{{ $clinicName }}</h5>
            </div>
            <div class="text-secondary mt-2">
              {{ $clinicAddr }}
            </div>
          </div>

          <div class="col-md-5 meta">
            <small class="text-secondary fw-bold">RECEIPT NUMBER</small>
            <div class="fs-5 fw-bold">#{{ $receiptNo }}</div>
            <div class="text-secondary">Issued: {{ $issuedAt }}</div>
          </div>
        </div>
      </div>

      {{-- Patient / Invoice --}}
      <div class="sec">
        <div class="row g-4">

          {{-- Patient Info --}}
          <div class="col-md-6">
            <div class="sec-title">PATIENT INFORMATION</div>

            <div class="info-line">
              <span>Name</span>
              <strong>{{ $patientName }}</strong>
            </div>

            <div class="info-line">
              <span>Patient ID</span>
              <strong>#{{ $patientCode }}</strong>
            </div>

            <div class="info-line">
              <span>Mobile</span>
              <strong>{{ $patientPhone }}</strong>
            </div>
          </div>

          {{-- Invoice Info --}}
          <div class="col-md-6">
            <div class="sec-title">INVOICE REFERENCE</div>

            <div class="info-line">
              <span>Invoice Ref</span>
              <strong>#{{ $invRef }}</strong>
            </div>

            <div class="info-line">
              <span>Billing Date</span>
              <strong>{{ $billDate }}</strong>
            </div>

            <div class="info-line">
              <span>Department</span>
              <strong>{{ $department }}</strong>
            </div>
          </div>

        </div>

        {{-- Table --}}
        <div class="box mt-4">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Description</th>
                <th class="text-end">Amount</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td class="text-secondary fw-bold">
                  Total Billed Services
                  @if(($invoice->items ?? collect())->count())
                    <span class="text-secondary fw-normal">
                      ({{ ($invoice->items ?? collect())->count() }} items)
                    </span>
                  @endif
                </td>
                <td class="text-end">EGP  {{ number_format($subtotal, 2) }}</td>
              </tr>

              @if($insuranceProvider)
                <tr>
                  <td class="text-secondary fw-bold">Insurance Coverage ({{ $insuranceProvider }})</td>
                  <td class="text-end text-green">
                    -EGP  {{ number_format(max(0, $subtotal - $total), 2) }}
                  </td>
                </tr>
              @endif

              @if($discount > 0)
                <tr>
                  <td class="text-secondary fw-bold">Discount</td>
                  <td class="text-end text-green">-EGP  {{ number_format($discount, 2) }}</td>
                </tr>
              @endif

              @if($taxAmount > 0)
                <tr>
                  <td class="text-secondary fw-bold">Tax</td>
                  <td class="text-end">EGP  {{ number_format($taxAmount, 2) }}</td>
                </tr>
              @endif

              <tr class="fw-bold">
                <td>Total Paid</td>
                <td class="text-end text-primary">EGP  {{ number_format((float)$paidAmount, 2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- Optional: list items --}}
        @if(($invoice->items ?? collect())->count())
          <div class="box mt-3">
            <table class="table mb-0">
              <thead class="table-light">
                <tr>
                  <th>Service</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Unit</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoice->items as $it)
                  <tr>
                    <td class="fw-semibold">
                      {{ $it->service_name ?? '—' }}
                      @if($it->doctor_name || $it->department)
                        <div class="text-secondary small">
                          {{ $it->doctor_name ?? '' }}{{ $it->department ? ' • ' . $it->department : '' }}
                        </div>
                      @endif
                    </td>
                    <td class="text-center">{{ (int)($it->qty ?? 1) }}</td>
                    <td class="text-end">EGP  {{ number_format((float)($it->unit_price ?? 0), 2) }}</td>
                    <td class="text-end">EGP  {{ number_format((float)($it->subtotal ?? ((float)($it->qty ?? 1) * (float)($it->unit_price ?? 0))), 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Payment --}}
      <div class="sec">
        <div class="soft">
          <div class="row g-3">
            <div class="col-md-4">
              <small class="text-secondary">Payment Method</small>
              <div class="fw-bold payment-method">
                <i class="bi {{ $pmIcon }} me-1"></i>
                {{ $pmLabel }}
              </div>
            </div>

            <div class="col-md-4">
              <small class="text-secondary">Transaction ID</small>
              <div class="fw-bold text-black">{{ $txnId }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-secondary">Payment Date</small>
              <div class="fw-bold text-black">{{ $issuedAt }}</div>
            </div>
          </div>
        </div>
      </div>

     

      {{-- Footer --}}
      <div class="footer">
        <h5>Thank you for choosing {{ $clinicName }}</h5>
        <p class="text-secondary">
          This is a computer-generated receipt and does not require a physical <br>
          signature. For any billing questions, please contact our billing department at <br>
          {{ $setting->billing_email ?? 'billing@helperclinic.com' }}.
        </p>
      </div>

    </section>

  </div>
</main>

@endsection
