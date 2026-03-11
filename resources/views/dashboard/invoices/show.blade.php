@extends('layouts.dash')
@section('dash-content')

@php
  // ===================== Clinic Info =====================
  $clinicName  = $setting->name    ?? 'Helper Clinic';
  $clinicAddr  = $setting->address ?? 'Nazer, Eltar3a Street';
  $clinicPhone = $setting->phone   ?? '01221604325';
  $clinicEmail = $setting->email   ?? 'memamo0338@helperclinic.com';

  // ===================== Invoice Core =====================
  $invoiceNo = $invoice->invoice_no ?? ('INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT));

  $issuedAt = optional($invoice->issued_at ?? $invoice->created_at)->format('M d, Y');

  $type   = $invoice->type ?? 'sale'; // sale | purchase
  $status = $invoice->status ?? 'paid'; // paid | pending | partially_paid

  $paymentMethod = $invoice->payment_method ?? '-';

  // ===================== Client =====================
  $clientName = $invoice->client?->name ?? ($invoice->client_name ?? '-');
  $clientId   = $invoice->client?->id ?? '-';
  $clientAddr = $invoice->client?->address ?? '-';

  // ===================== Items =====================
  $items = $invoice->items ?? collect();

  // ===================== Totals (fallback محسوب لو مش مخزن) =====================
  $calcSubtotal = $items->sum(fn($it) => (float)($it->unit_price ?? 0) * (int)($it->qty ?? 1));

  $subtotal   = (float) ($invoice->subtotal ?? $calcSubtotal);
  $discount   = (float) ($invoice->discount ?? 0);

  // ✅ VAT / TAX: لو Purchase يبقى 0
  $taxRate    = $type === 'purchase' ? 0 : 0.10;

  // tax amount (force 0 for purchase)
  $taxAmount  = $type === 'purchase'
    ? 0
    : (float) ($invoice->tax_amount ?? max(0, ($subtotal - $discount) * $taxRate));

  $grandTotal = $type === 'purchase'
    ? (float) ($invoice->grand_total ?? max(0, ($subtotal - $discount))) // بدون ضريبة
    : (float) ($invoice->grand_total ?? max(0, ($subtotal - $discount) + $taxAmount));

  $paidAmount = (float) ($invoice->paid_amount ?? 0);
  $balance    = (float) ($invoice->balance_due ?? max(0, $grandTotal - $paidAmount));

  // Label helpers
  $typeLabel = $type === 'purchase' ? 'Purchase (In)' : 'Sale (Out)';

  $statusLabel = match($status) {
    'paid' => 'Paid',
    'pending' => 'Pending',
    'partially_paid' => 'Partially Paid',
    default => ucfirst(str_replace('_',' ', $status)),
  };

  $taxPct = (int) round($taxRate * 100);
@endphp

<link rel="stylesheet" href="{{ asset('css/paymentReceipt.css') }}">

<div class="container-fluid p-0">

  {{-- HEADER --}}
  <header class="page-header">
    <div class="page-width px-3">
      <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">

        <div>
          <h3 class="fw-bold mb-1">Invoice Receipt</h3>
          <p class="text-secondary mb-0">Invoice details and payment summary</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <button class="hdr-btn" type="button" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span class="d-none d-md-inline">Print</span>
          </button>

          <a class="hdr-btn" href="{{ route('invoices.index') }}">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-md-inline">Back</span>
          </a>
        </div>

      </div>
    </div>
  </header>

  {{-- CONTENT --}}
  <main class="pb-5">
    <div class="page-width px-3">

      <section class="receipt">

        {{-- Brand --}}
        <div class="sec">
          <div class="row">
            <div class="col-md-7">
              <div class="brand">
                <h5 class="mb-0 fw-bold">{{ $clinicName }}</h5>
              </div>
              <div class="text-secondary mt-2">{{ $clinicAddr }}</div>
              <div class="text-secondary mt-1">{{ $clinicPhone }} · {{ $clinicEmail }}</div>

              <div class="mt-3 d-flex flex-wrap gap-2">
                <span class="badge bg-light text-dark">
                  Type: {{ $typeLabel }}
                </span>
                <span class="badge bg-light text-dark">
                  Status: {{ $statusLabel }}
                </span>
              </div>
            </div>

            <div class="col-md-5 meta">
              <small class="text-secondary fw-bold">INVOICE NUMBER</small>
              <div class="fs-5 fw-bold">#{{ $invoiceNo }}</div>
              <div class="text-secondary">Issued: {{ $issuedAt }}</div>
            </div>
          </div>
        </div>

        {{-- Client / Invoice --}}
        <div class="sec">
          <div class="row g-4">

            {{-- Client Info --}}
            <div class="col-md-6">
              <div class="sec-title">CLIENT INFORMATION</div>

              <div class="info-line">
                <span>Name</span>
                <strong>{{ $clientName }}</strong>
              </div>

              <div class="info-line">
                <span>Client ID</span>
                <strong>#{{ $clientId }}</strong>
              </div>

              <div class="info-line">
                <span>Address</span>
                <strong>{{ $clientAddr }}</strong>
              </div>
            </div>

            {{-- Invoice Info --}}
            <div class="col-md-6">
              <div class="sec-title">INVOICE DATA</div>

              <div class="info-line">
                <span>Payment Method</span>
                <strong>{{ ucfirst(str_replace('_',' ', $paymentMethod)) }}</strong>
              </div>

              <div class="info-line">
                <span>VAT</span>
                <strong>{{ $taxPct }}%</strong>
              </div>

              <div class="info-line">
                <span>Paid Amount</span>
                <strong>EGP {{ number_format($paidAmount, 2) }}</strong>
              </div>
            </div>

          </div>

          {{-- Items Table --}}
          @if($items && $items->count())
            <div class="box mt-4">
              <table class="table mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Item</th>
                    <th class="text-center">Unit Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Line Total</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach($items as $it)
                    @php
                      $name = $it->product?->name ?? ($it->name ?? 'Item');
                      $sku  = $it->product?->sku ?? null;
                      $price = (float)($it->unit_price ?? 0);
                      $qty   = max(1, (int)($it->qty ?? 1));
                      $line  = $price * $qty;
                    @endphp
                    <tr>
                      <td class="text-secondary fw-bold">
                        {{ $name }}
                        @if($sku)
                          <div class="small text-muted">SKU: {{ $sku }}</div>
                        @endif
                      </td>

                      <td class="text-center">EGP {{ number_format($price, 2) }}</td>
                      <td class="text-center">{{ $qty }}</td>
                      <td class="text-end">EGP {{ number_format($line, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        {{-- Totals --}}
        <div class="sec">
          <div class="soft">
            <div class="row g-3">

              <div class="col-md-4">
                <small class="text-secondary">Subtotal</small>
                <div class="fw-bold text-black">EGP {{ number_format($subtotal, 2) }}</div>
              </div>

              <div class="col-md-4">
                <small class="text-secondary">Discount</small>
                <div class="fw-bold text-black">
                  EGP {{ number_format($discount, 2) }}
                </div>
              </div>

              <div class="col-md-4">
                <small class="text-secondary">Tax</small>
                <div class="fw-bold text-black">EGP {{ number_format($taxAmount, 2) }}</div>
              </div>

              <div class="col-12"><hr class="my-2"></div>

              <div class="col-md-4">
                <small class="text-secondary">Grand Total</small>
                <div class="fw-bold text-primary">EGP {{ number_format($grandTotal, 2) }}</div>
              </div>

              <div class="col-md-4">
                <small class="text-secondary">Paid</small>
                <div class="fw-bold text-primary">EGP {{ number_format($paidAmount, 2) }}</div>
              </div>

              <div class="col-md-4">
                <small class="text-secondary">Balance Due</small>
                <div class="fw-bold text-black">EGP {{ number_format($balance, 2) }}</div>
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
            {{ $clinicEmail }}.
          </p>
        </div>

      </section>

    </div>
  </main>

</div>
@endsection
