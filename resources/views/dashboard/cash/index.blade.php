@extends('layouts.dash')
@section('dash-content')

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/cashManagement.css') }}" />

  @php
    $isEdit = isset($editMovement) && $editMovement;
    $isCashIn = $isEdit && (float) $editMovement->cash > 0; // ✅ cash in record
  @endphp

  <main class="cash-layout">
 <div class="cash-container">
  <!-- Header -->
  <div class="cash-head">
    <div class="cash-head-top">
      <button class="btn icon-btn d-lg-none cash-menu-btn" type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#mobileSidebar"
              aria-controls="mobileSidebar" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
      </button>

      <h1 class="cash-title mb-0">Services &amp; Cash Management</h1>
    </div>

    <p class="cash-subtitle mb-0">
      Manage clinic services and monitor real-time financial performance.
    </p>
  </div>
</div>

      {{-- ✅ Alerts --}}
      @if (session('success'))
        <div class="alert alert-success mb-3" id="successAlert">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger mb-3" id="errorAlert">
          {{ $errors->first() }}
        </div>
      @endif

      <!-- Top Section -->
      <div class="row g-4 align-items-stretch">

        <!-- Form -->
        <div class="col-12 col-lg-8">
          <div class="card cash-card h-100" id="cashFormCard">
            <div class="card-body p-4">

              <form method="POST"
                    action="{{ $isEdit ? route('cash.update', $editMovement->id) : route('cash.store') }}"
                    novalidate>
                @csrf
                @if($isEdit)
                  @method('PUT')
                @endif

                <div class="row g-3">

                  <!-- Service Name -->
                  <div class="col-12">
  <label class="cash-label" for="service">Service Name</label>
  <input
    id="service"
    name="service"
    type="text"
    class="form-control cash-input @error('service') is-invalid @enderror"
    placeholder="e.g. General Consultation"
    value="{{ old('service', $isEdit ? $editMovement->service : '') }}"
    required
  />
  @error('service')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>


              
                  <div class="col-12">
  <label class="cash-label" for="amount">
    {{ $isEdit ? ($isCashIn ? 'Cash In (EGP)' : 'Cash Out (EGP)') : 'Cost (EGP)' }}
  </label>

  @if($isEdit && $isCashIn)
    {{-- ✅ Editing Cash In --}}
    <input
      id="amount"
      name="cash"
      type="number"
      step="0.01"
      min="0"
      class="form-control cash-input @error('cash') is-invalid @enderror"
      placeholder="0.00"
      value="{{ old('cash', $isEdit ? $editMovement->cash : '') }}"
      required
    />
    @error('cash')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  @else
    {{-- ✅ New OR Editing Cash Out --}}
    <input
      id="amount"
      name="cash_out"
      type="number"
      step="0.01"
      min="0"
      class="form-control cash-input @error('cash_out') is-invalid @enderror"
      placeholder="0.00"
      value="{{ old('cash_out', $isEdit ? $editMovement->cash_out : '') }}"
      required
    />
    @error('cash_out')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  @endif
</div>


                  <!-- Buttons -->
                  <div class="col-12 pt-2 d-flex gap-2">
                    <button class="btn cash-btn w-100" type="submit">
                      <i class="fa-regular {{ $isEdit ? 'fa-pen-to-square' : 'fa-floppy-disk' }} me-2"></i>
                      {{ $isEdit ? 'Update' : 'Save' }}
                    </button>

                    @if($isEdit)
                      <a href="{{ route('cash.index') }}" class="btn btn-outline-secondary w-100">
                        Cancel
                      </a>
                    @endif
                  </div>

                </div>
              </form>

            </div>
          </div>
        </div>

        <!-- Balance -->
        <div class="col-12 col-lg-4">
          <div class="cash-balance h-100">

            <!-- Top -->
            <div>
              <div class="cash-balance-label">Clinic Cash Balance</div>
              <div class="cash-balance-value" id="cashValue">
                {{ number_format($net ?? 0, 0) }} <span>EGP</span>
              </div>
            </div>

            <!-- Add Cash (cash_in) -->
            @if (auth()->user()->role == 'doctor')
              <div class="cash-add mt-3">
                <form method="POST" action="{{ route('cash.store') }}" novalidate>
                  @csrf
                  <input type="hidden" name="service" value="Cash In">

                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    class="form-control cash-add-input @error('cash') is-invalid @enderror"
                    name="cash"
                    placeholder="Add cash amount"
                    value="{{ old('cash') }}"
                    required
                  />

                  @error('cash')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror

                  <button class="btn cash-add-btn mt-2" type="submit">
                    + Add Cash
                  </button>
                </form>
              </div>
            @endif

            <!-- Bottom -->
            <div class="cash-balance-row">
              <span class="cash-arrow-down"></span>
              <span class="cash-expense-text">Clinic Expenses</span>
              <span class="cash-expense-val text-danger">
                -{{ number_format($totalOut ?? 0, 0) }} EGP
              </span>
            </div>

          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card cash-card mt-4">
        <div class="cash-table-head">
          <h2 class="cash-card-title">Services</h2>

        <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">

  {{-- Filter by Day --}}
<form method="GET" action="{{ route('cash.index') }}"
      class="d-flex align-items-center gap-2">

  {{-- Date --}}
  <input
    type="date"
    name="day"
    value="{{ request('day') }}"
    class="form-control cash-date-input"
  />

  {{-- keep search value --}}
  @if(request('q'))
    <input type="hidden" name="q" value="{{ request('q') }}">
  @endif

  {{-- Apply --}}
  <button type="submit" class="btn btn-primary">
    <i class="fa-solid fa-filter me-1"></i>
    
  </button>



</form>


  {{-- Search --}}
  <form class="cash-search" method="GET" action="{{ route('cash.index') }}">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input
      class="form-control cash-search-input"
      name="q"
      value="{{ request('q') }}"
      placeholder="Search services..."
    />

    {{-- keep date value --}}
    @if(request('day'))
      <input type="hidden" name="day" value="{{ request('day') }}">
    @endif
  </form>

  {{-- Print --}}
 <a href="{{ route('printCash', ['q' => request('q'), 'day' => request('day')]) }}"
   class="btn btn-outline-secondary cash-print-btn">
  <i class="fa-solid fa-print me-1"></i>
  Print
</a>


</div>

        </div>

        <div class="table-responsive">
          <table class="table cash-table mb-0">
            <thead>
              <tr>
                <th>Service Name</th>
                <th>Cash In</th>
                <th>Cash Out</th>
                <th>Date Added</th>
                <th>Created By</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>

            <tbody>
              @forelse($movements as $m)
                <tr>
                  <td>{{ $m->service }}</td>
                  <td>{{ number_format((float) $m->cash, 2) }} EGP</td>
                  <td>{{ number_format((float) $m->cash_out, 2) }} EGP</td>
                  <td>{{ \Carbon\Carbon::parse($m->created_at)->format('d M Y') }}</td>
                  <td>{{ $m->creator?->name ?? '-' }}</td>
                  <td class="text-end">
                    <form action="{{ route('cash.destroy', $m->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this item?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn p-0 border-0 bg-transparent text-danger cash-icon" title="Delete">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4">No services found.</td>
                </tr>
              @endforelse
            </tbody>

          </table>
        </div>

        <div class="cash-table-foot">
          <span>Showing {{ $movements->count() }} of {{ $movements->total() }} items</span>
          <div class="cash-pagination">
            {{ $movements->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>

    </div>
  </main>

  <script>
    // ✅ success alert auto hide (3s)
    setTimeout(() => {
      const a = document.getElementById('successAlert');
      if (a) {
        a.classList.add('fade');
        a.classList.remove('show');
        setTimeout(() => a.remove(), 500);
      }
    }, 3000);

    // ✅ لو داخل Edit mode نعمل scroll للفورم
    @if($isEdit)
      document.getElementById('cashFormCard')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    @endif
  </script>

@endsection
