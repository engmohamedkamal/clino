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
    <h2 class="pl-title">Products</h2>

    <div class="pl-actions">

      {{-- Add --}}
      <a href="{{ route('products.create') }}" class="pl-icon-btn primary" aria-label="Add">
        <span class="material-icons-round">add</span>
      </a>

      {{-- Search --}}
      <form class="pl-search" method="GET" action="{{ route('products.index') }}">
        <span class="material-icons-round">search</span>
        <input
          name="q"
          type="text"
          value="{{ request('q') }}"
          placeholder="Search by name, SKU, category"
        >
      </form>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <!-- Table Card -->
  <div class="pl-table-card">
    <div class="table-responsive">
      <table class="table pl-table mb-0 align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Unit</th>
            <th class="text-center">Qty</th>
            <th>Selling</th>
            <th>Expiry Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($products as $product)
            <tr>
              <td class="fw-semibold">
                {{ $product->name }}
              </td>

              <td class="text-muted">{{ $product->sku }}</td>

              <td class="text-muted">{{ $product->category ?? '-' }}</td>

              <td class="text-muted">{{ $product->unit ?? '-' }}</td>

              <td class="text-center fw-semibold">
  <div class="d-inline-flex align-items-center gap-1">
    {{-- Reduce Qty --}}
    <form action="{{ route('products.decrease', $product->id) }}" method="POST">
      @csrf
      <button type="submit"
              class="btn btn-sm btn-outline-danger"
              {{ $product->quantity <= 0 ? 'disabled' : '' }}
              title="Decrease quantity">
        −
      </button>
    </form>

    {{-- Qty --}}
    <span class="mx-1">{{ $product->quantity }}</span>

    {{-- Low stock --}}
    @if($product->quantity <= $product->reorder_level)
      <span class="badge bg-danger">Low</span>
    @endif
  </div>
</td>




              <td class="text-muted">
                {{ number_format($product->selling_price, 2) }}
              </td>
@php
  $expiry = \Carbon\Carbon::parse($product->expiry_date);
  $daysLeft = now()->diffInDays($expiry, false);
@endphp

<td>
  @if($daysLeft < 0)
    {{-- ❌ منتهي --}}
    <span class="badge bg-danger">
      Expired ({{ $expiry->format('d-m-Y') }})
    </span>

  @elseif($daysLeft <= 10)
    {{-- ⚠️ قرب الانتهاء --}}
    <span class="badge bg-warning text-dark">
      Expires Soon ({{ $expiry->format('d-m-Y') }})
    </span>

  @else
    {{-- ✅ سليم --}}
    <span class="badge bg-success">
      {{ $expiry->format('d-m-Y') }}
    </span>
  @endif
</td>


              <td class="text-end">
                <a href="{{ route('products.edit', $product->id) }}"
                   class="btn action-ico">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>

                <form action="{{ route('products.destroy', $product->id) }}"
                      method="POST"
                      class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn action-ico"
                          type="submit"
                          onclick="return confirm('Delete this product?')">
                    <i class="fa-regular fa-trash-can"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">
                No products found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 custom-pagination">
      {{ $products->links('pagination::bootstrap-5') }}
    </div>
  </div>
</section>
@endsection
