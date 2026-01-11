@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('css/invoiceEntry.css') }}" />

<main class="flex-grow-1">
  <div class="container-fluid ie-container py-3 py-md-4">

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
      <div class="d-flex align-items-start gap-2">
        <button class="btn btn-light d-lg-none ie-btn-soft" type="button"
          data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
          <i class="bi bi-list"></i>
        </button>

        <div>
          <h3 class="mb-1 ie-title">Invoices</h3>
          <div class="ie-subtitle">Manage sales and purchase invoices.</div>
        </div>
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <form method="GET" action="{{ route('invoices.index') }}" class="d-flex gap-2">
          <input type="text"
                 name="q"
                 value="{{ $q ?? '' }}"
                 class="form-control ie-input"
                 placeholder="Search invoice # or client...">
        </form>

        <a href="{{ route('invoices.create') }}"
           class="btn btn-primary ie-btn-primary d-inline-flex align-items-center gap-2">
          <i class="bi bi-plus-circle"></i>
          Create Invoice
        </a>
      </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    {{-- Desktop Table --}}
    <div class="card ie-card d-none d-md-block">
      <div class="table-responsive">
        <table class="table ie-table align-middle mb-0">
          <thead>
            <tr>
              <th class="ie-th">INVOICE</th>
              <th class="ie-th">CLIENT</th>
              <th class="ie-th text-center">TYPE</th>
              <th class="ie-th text-center">STATUS</th>
              <th class="ie-th text-end">TOTAL</th>
              <th class="ie-th text-end">DATE</th>
              <th class="ie-th text-end"></th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              @php
                $typeLabel = $inv->type === 'purchase' ? 'Purchase' : 'Sale';
                $typeBadge = $inv->type === 'purchase' ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary';

                $statusBadge = match($inv->status) {
                  'paid' => 'bg-success',
                  'pending' => 'bg-warning text-dark',
                  'partially_paid' => 'bg-secondary',
                  default => 'bg-light text-dark'
                };
              @endphp
              <tr>
                <td class="fw-semibold">
                  {{ $inv->invoice_no }}
                </td>

                <td>
                  {{ $inv->client?->name ?? $inv->client_name ?? '-' }}
                </td>

                <td class="text-center">
                  <span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span>
                </td>

                <td class="text-center">
                  <span class="badge {{ $statusBadge }}">
                    {{ ucfirst(str_replace('_',' ', $inv->status)) }}
                  </span>
                </td>

                <td class="text-end fw-semibold">
                  ${{ number_format($inv->grand_total, 2) }}
                </td>

                <td class="text-end text-muted">
                  {{ optional($inv->issued_at)->format('d M Y') }}
                </td>

                <td class="text-end">
                  <a href="{{ route('invoices.show', $inv->id) }}"
                     class="btn ie-btn-soft btn-sm">
                    View
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  No invoices found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="d-md-none">
      @forelse($invoices as $inv)
        @php
          $typeLabel = $inv->type === 'purchase' ? 'Purchase' : 'Sale';
        @endphp
        <article class="card ie-card mb-3">
          <div class="card-body p-3">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div>
                <div class="fw-semibold">{{ $inv->invoice_no }}</div>
                <div class="text-muted small">
                  {{ $inv->client?->name ?? $inv->client_name ?? '-' }}
                </div>
              </div>

              <span class="badge bg-light text-dark border">
                {{ ucfirst(str_replace('_',' ', $inv->status)) }}
              </span>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <div>
                <div class="small text-muted">{{ $typeLabel }}</div>
                <div class="fw-semibold">
                  ${{ number_format($inv->grand_total, 2) }}
                </div>
              </div>

              <a href="{{ route('invoices.show', $inv->id) }}"
                 class="btn ie-btn-soft btn-sm">
                View
              </a>
            </div>
          </div>
        </article>
      @empty
        <div class="text-center text-muted py-4">
          No invoices found.
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $invoices->links('pagination::bootstrap-5') }}
    </div>

  </div>
</main>
@endsection
