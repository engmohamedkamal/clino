@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('CSS/patientTransfer.css') }}" />

@php
  $role = auth()->user()->role ?? '';
  $canManage = in_array($role, ['admin', 'doctor']);  // create/edit/delete
  $isPatient = ($role === 'patient');
@endphp

<main class="pt-main">

  {{-- ✅ Success / Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success mb-3">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error') || $errors->any())
    <div class="alert alert-danger mb-3">
      <strong>There were some problems:</strong>
      <ul class="mb-0 mt-2">
        @if(session('error'))
          <li>{{ session('error') }}</li>
        @endif
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ================= Header ================= --}}
  <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3 mb-md-4">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    <div>
      <h3 class="pt-title mb-1">Service Invoices</h3>
      <div class="pt-subtitle">Manage service invoices records.</div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      {{-- Search --}}
      <form method="GET" action="{{ route('service-invoices.index') }}" class="d-flex gap-2">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input
            type="text"
            name="q"
            value="{{ $q ?? request('q') }}"
            class="form-control"
            placeholder="Search by invoice, patient, phone..."
          >
        </div>

        <button class="btn btn-primary" type="submit">Search</button>

        @if(!empty($q))
          <a href="{{ route('service-invoices.index') }}" class="btn btn-light">Reset</a>
        @endif
      </form>

      {{-- ✅ Create (Admin/Doctor فقط) --}}
      @if($canManage)
        <a href="{{ route('service-invoices.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> New Invoice
        </a>
      @endif
    </div>
  </div>

  {{-- ================= Table / List ================= --}}
  <section class="pt-card">
    <div class="pt-card-head pt-between">
      <div class="d-flex align-items-center gap-2">
        <div class="pt-card-ico"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="pt-card-title">Invoices List</div>
      </div>
      <div class="pt-muted">
        {{ $invoices->total() }} Total
      </div>
    </div>

    <div class="pt-card-body">

      @if($invoices->count())

        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr class="text-uppercase small text-muted">
                <th>Invoice</th>
                <th>Patient</th>
                <th class="text-nowrap">Payment</th>
                <th class="text-nowrap">Total</th>
                <th class="text-nowrap">Date</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>

            <tbody>
              @foreach($invoices as $inv)
                @php
                  $patientName = optional($inv->patient)->name ?? ($inv->patient_name ?? '—');
                  $patientPhone = optional($inv->patient)->phone ?? ($inv->patient_phone ?? null);

                  $invoiceNo = $inv->invoice_no ?? ('INV-' . $inv->id);

                  $total    = (float)($inv->total ?? 0);
                  $subtotal = (float)($inv->subtotal ?? 0);

                  $method = $inv->payment_method ?? '-';
                  $status = $inv->payment_status ?? '-';

                  $created = optional($inv->issued_at ?? $inv->created_at)->format('M d, Y');

                  $statusClass =
                    $status === 'paid' ? 'bg-success text-white' :
                    ($status === 'partial' ? 'bg-warning text-dark' :
                    ($status === 'pending' ? 'bg-secondary text-white' : 'bg-light text-dark'));

                  $methodText =
                    $method === 'card' ? 'Card' :
                    ($method === 'cash' ? 'Cash' :
                    ($method === 'insurance' ? 'Insurance' :
                    ($method === 'wallet' ? 'Wallet' : ucfirst((string)$method))));
                @endphp

                <tr>
                  {{-- Invoice --}}
                  <td class="text-nowrap">
                    <div class="fw-semibold">{{ $invoiceNo }}</div>
                    <div class="small text-muted">Subtotal: ${{ number_format($subtotal, 2) }}</div>
                  </td>

                  {{-- Patient --}}
                  <td>
                    <div class="fw-semibold text-truncate" style="max-width:220px;">
                      {{ $patientName }}
                    </div>
                    <div class="small text-muted">
                      {{ $patientPhone ? $patientPhone : '—' }}
                    </div>
                  </td>

                  {{-- Payment --}}
                  <td class="text-nowrap">
                    <div class="fw-semibold">{{ $methodText }}</div>
                    <span class="badge {{ $statusClass }}">{{ strtoupper($status) }}</span>
                  </td>

                  {{-- Total --}}
                  <td class="text-nowrap fw-semibold">
                    ${{ number_format($total, 2) }}
                  </td>

                  {{-- Date --}}
                  <td class="text-nowrap">
                    {{ $created }}
                  </td>

                  {{-- Actions --}}
                  <td class="text-end text-nowrap">
                    {{-- View: للجميع --}}
                    <a href="{{ route('service-invoices.show', $inv) }}" class="btn btn-sm btn-light">
                      <i class="bi bi-eye"></i>
                    </a>

                    {{-- Edit/Delete: Admin/Doctor فقط --}}
                    @if($canManage)
                      <a href="{{ route('service-invoices.edit', $inv) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-pencil"></i>
                      </a>

                      <form method="POST"
                            action="{{ route('service-invoices.destroy', $inv) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete this invoice?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
          <div class="pt-muted">
            Showing {{ $invoices->firstItem() }} - {{ $invoices->lastItem() }} of {{ $invoices->total() }}
          </div>

          <div>
            {{ $invoices->links() }}
          </div>
        </div>

      @else
        <div class="text-center py-5">
          <div class="pt-muted mb-2">No invoices found.</div>

          {{-- Create CTA: Admin/Doctor فقط --}}
          @if($canManage)
            <a href="{{ route('service-invoices.create') }}" class="btn btn-primary">
              <i class="bi bi-plus-lg me-1"></i> Create Invoice
            </a>
          @endif
        </div>
      @endif

    </div>
  </section>

</main>
@endsection
