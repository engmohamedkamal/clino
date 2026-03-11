@extends('layouts.dash')

@section('dash-content')
<link rel="stylesheet" href="{{ asset('css/patientList.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<section class="pl-main container">
  <div class="pl-topbar">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    <h2 class="pl-title">Invoices</h2>

    <div class="pl-actions">

      {{-- Create Invoice --}}
      <a href="{{ route('invoices.create') }}" class="pl-icon-btn primary" aria-label="Add">
        <span class="material-icons-round">add</span>
      </a>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('invoices.index') }}">
        <span class="material-icons-round">search</span>
        <input
          id="searchInput"
          name="q"
          type="text"
          value="{{ $q ?? request('q') }}"
          placeholder="Search invoice # or client..."
        >
      </form>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
  @endif

  <!-- Table Card -->
  <div class="pl-table-card">
    <div class="table-responsive">
      <table class="table pl-table mb-0 align-middle">
        <thead>
          <tr>
            <th>INVOICE</th>
            <th>CLIENT</th>
            <th class="text-center">TYPE</th>
            <th class="text-center">STATUS</th>
            <th class="text-end">TOTAL</th>
            <th class="text-end">DATE</th>
            <th class="text-end"></th>
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

              $client = $inv->client?->name ?? $inv->client_name ?? '-';
              $date = optional($inv->issued_at)->format('d M Y');
            @endphp

            <tr>
              <td class="fw-semibold">{{ $inv->invoice_no }}</td>

              <td>{{ $client }}</td>

              <td class="text-center">
                <span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span>
              </td>

              <td class="text-center">
                <span class="badge {{ $statusBadge }}">
                  {{ ucfirst(str_replace('_',' ', $inv->status)) }}
                </span>
              </td>

              <td class="text-end fw-semibold">
                EGP {{ number_format($inv->grand_total, 2) }}
              </td>

              <td class="text-end text-muted">{{ $date }}</td>

              <td class="text-end">
                <a href="{{ route('invoices.show', $inv->id) }}" class="pl-icon-btn" aria-label="View">
                  <span class="material-icons-round">visibility</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">No invoices found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ✅ Pagination --}}
    <div class="mt-4 custom-pagination">
      {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
  </div>

</section>

@endsection
