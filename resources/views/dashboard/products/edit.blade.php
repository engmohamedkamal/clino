{{-- resources/views/products/edit.blade.php --}}
@extends('layouts.dash')
@section('dash-content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/addProduct.css') }}" />

<section class="ap-body">
  <div class="d-flex min-vh-100">
    <main class="flex-grow-1">
      <div class="container-fluid ap-container py-3 py-md-4">

        <!-- Header -->
    <header class="topbar d-flex align-items-center justify-content-between">
  
  <!-- Left: menu + title -->
  <div class="d-flex align-items-center gap-3">
    <button class="btn icon-btn d-lg-none" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar">
      <i class="fa-solid fa-bars"></i>
    </button>

    <div>
      <h3 class="mb-0 ap-title">Edit Product</h3>
      <div class="small text-muted">Update product information</div>
    </div>
  </div>

  <!-- Right: Back button -->
  <a href="{{ route('products.index') }}"
     class="btn ap-btn-soft px-3 py-2 d-none d-md-inline-flex align-items-center gap-2">
    <i class="bi bi-arrow-left"></i>
    <span>Back</span>
  </a>

</header>
  
        {{-- ✅ CENTER the form card --}}
        <div class="row g-4 justify-content-center align-items-center" style="min-height: calc(100vh - 140px);">

          <!-- Form -->
          <div class="col-12 mx-auto">
            <section class="card ap-card">
              <div class="card-body p-0">

                <div class="d-flex align-items-center justify-content-between gap-2 p-3 p-md-4 border-bottom ap-soft-border">
                  <div class="d-flex align-items-center gap-2">
                    <div class="ap-sec-ico"><i class="bi bi-pencil-square"></i></div>
                    <div class="ap-sec-title">Product Information</div>
                  </div>

                  <div class="ap-mini-muted">
                    ID:
                    <span class="fw-semibold">#{{ $product->id }}</span>
                  </div>
                </div>

                {{-- ✅ Alerts (Success + Errors) --}}
                @if(session('success') || $errors->any())
                  <div class="p-3 p-md-4 border-bottom ap-soft-border">
                    @if(session('success'))
                      <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif

                    @if($errors->any())
                      <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <div class="fw-semibold mb-1">Please fix the following:</div>
                        <ul class="mb-0 ps-3">
                          @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                  </div>
                @endif

                <!-- ✅ FORM -->
                <form class="p-3 p-md-4" id="editProductForm"
                      method="POST"
                      action="{{ route('products.update', $product->id) }}"
                      enctype="multipart/form-data"
                      novalidate>
                  @csrf
                  @method('PUT')

                  <div class="row g-3">

                    <!-- Product Name -->
                    <div class="col-12 col-lg-8">
                      <label class="form-label ap-label" for="name">Product Name</label>
                      <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control ap-input @error('name') is-invalid @enderror"
                        value="{{ old('name', $product->name) }}"
                        required
                      />
                      @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- SKU -->
                    <div class="col-12 col-lg-4">
                      <label class="form-label ap-label" for="sku">SKU</label>
                      <div class="input-group">
                        <input
                          id="sku"
                          name="sku"
                          type="text"
                          class="form-control ap-input @error('sku') is-invalid @enderror"
                          value="{{ old('sku', $product->sku) }}"
                          required
                        />
                        <button class="btn ap-scan-btn" type="button" id="genSkuBtn" aria-label="generate sku">
                          <i class="bi bi-magic"></i>
                        </button>
                      </div>
                      <div class="form-text">Must be unique.</div>
                      @error('sku')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="category">Category</label>
                      <select
                        id="category"
                        name="category"
                        class="form-select ap-input @error('category') is-invalid @enderror"
                        required
                      >
                        @php $cat = old('category', $product->category); @endphp
                        <option value="" disabled {{ $cat ? '' : 'selected' }}>Select category</option>
                        <option value="consumables" {{ $cat=='consumables' ? 'selected' : '' }}>Consumables</option>
                        <option value="medications"  {{ $cat=='medications'  ? 'selected' : '' }}>Medications</option>
                        <option value="equipment"    {{ $cat=='equipment'    ? 'selected' : '' }}>Equipment</option>
                        <option value="others"       {{ $cat=='others'       ? 'selected' : '' }}>Others</option>
                      </select>
                      @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Unit -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="unit">Unit</label>
                      <select
                        id="unit"
                        name="unit"
                        class="form-select ap-input @error('unit') is-invalid @enderror"
                        required
                      >
                        @php $unit = old('unit', $product->unit); @endphp
                        <option value="" disabled {{ $unit ? '' : 'selected' }}>Select unit</option>
                        <option value="pcs"    {{ $unit=='pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="box"    {{ $unit=='box' ? 'selected' : '' }}>Box</option>
                        <option value="bottle" {{ $unit=='bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="pack"   {{ $unit=='pack' ? 'selected' : '' }}>Pack</option>
                      </select>
                      @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Purchase price -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="purchase_price">Purchase price</label>
                      <div class="input-group">
                        <span class="input-group-text ap-input-ico">EGP</span>
                        <input
                          id="purchase_price"
                          name="purchase_price"
                          type="number"
                          step="0.01"
                          min="0"
                          class="form-control ap-input @error('purchase_price') is-invalid @enderror"
                          value="{{ old('purchase_price', $product->purchase_price) }}"
                          required
                        />
                      </div>
                      @error('purchase_price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Selling price -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="selling_price">Selling price</label>
                      <div class="input-group">
                        <span class="input-group-text ap-input-ico">EGP</span>
                        <input
                          id="selling_price"
                          name="selling_price"
                          type="number"
                          step="0.01"
                          min="0"
                          class="form-control ap-input @error('selling_price') is-invalid @enderror"
                          value="{{ old('selling_price', $product->selling_price) }}"
                          required
                        />
                      </div>
                      @error('selling_price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Profit preview -->
                    <div class="col-12">
                      <div class="ap-stock-box p-3">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                          <div>
                            <div class="ap-stock-sub">Profit per unit</div>
                            <div class="ap-stock-num" id="profitText">0.00</div>
                          </div>
                          <div class="text-md-end">
                            <div class="ap-stock-sub">Margin</div>
                            <div class="ap-stock-tag" id="marginText">0%</div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="quantity">Quantity</label>
                      <div class="input-group">
                        <input
                          id="quantity"
                          name="quantity"
                          type="number"
                          class="form-control ap-input text-center @error('quantity') is-invalid @enderror"
                          value="{{ old('quantity', $product->quantity) }}"
                          min="0"
                          required
                        />
                        <span class="input-group-text ap-input-ico">Qty</span>
                      </div>
                      @error('quantity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Reorder Level -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="reorder_level">Reorder Level</label>
                      <div class="input-group">
                        <input
                          id="reorder_level"
                          name="reorder_level"
                          type="number"
                          class="form-control ap-input text-center @error('reorder_level') is-invalid @enderror"
                          value="{{ old('reorder_level', $product->reorder_level) }}"
                          min="0"
                        />
                        <span class="input-group-text ap-input-ico">Min</span>
                      </div>
                      <div class="form-text">Alert when stock reaches this value.</div>
                      @error('reorder_level')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Location -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="location">Storage Location</label>
                      <input
                        id="location"
                        name="location"
                        type="text"
                        class="form-control ap-input @error('location') is-invalid @enderror"
                        value="{{ old('location', $product->location) }}"
                        placeholder="e.g. Shelf A-12"
                      />
                      @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Expiry -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="expiry_date">Expiry Date</label>
                      <input
                        id="expiry_date"
                        name="expiry_date"
                        type="date"
                        class="form-control ap-input @error('expiry_date') is-invalid @enderror"
                        value="{{ old('expiry_date', optional($product->expiry_date)->format('Y-m-d')) }}"
                      />
                      <div class="form-text">Leave empty if not applicable.</div>
                      @error('expiry_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Supplier -->
                    <div class="col-12 col-lg-6">
                      <label class="form-label ap-label" for="supplier">Supplier</label>
                      <input
                        id="supplier"
                        name="supplier"
                        type="text"
                        class="form-control ap-input @error('supplier') is-invalid @enderror"
                        value="{{ old('supplier', $product->supplier) }}"
                        placeholder="Supplier name (optional)"
                      />
                      @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-lg-6 d-flex align-items-end">
                      <div class="w-100 ap-stock-box p-3">
                        <div class="d-flex align-items-center justify-content-between">
                          <div>
                            <div class="ap-stock-sub">Status</div>
                            <div class="fw-semibold">Active</div>
                          </div>
                          <div class="form-check form-switch m-0">
                            <input
                              class="form-check-input @error('status') is-invalid @enderror"
                              type="checkbox"
                              role="switch"
                              id="status"
                              name="status"
                              value="1"
                              {{ old('status', $product->status) ? 'checked' : '' }}
                            >
                          </div>
                        </div>
                        <div class="ap-stock-sub mt-1">Turn off to hide from booking/sales.</div>

                        @error('status')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <!-- Image -->
                    <div class="col-12">
                      <label class="form-label ap-label" for="image">Product Image</label>
                      <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-8">
                          <input
                            id="image"
                            name="image"
                            type="file"
                            class="form-control ap-input @error('image') is-invalid @enderror"
                            accept="image/*"
                          />
                          <div class="form-text">PNG/JPG recommended.</div>
                          @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="col-12 col-md-4">
                          <div class="ap-stock-box p-2 d-flex align-items-center justify-content-center" style="min-height: 92px;">
                            @php
                              $imgUrl = $product->image ? asset('storage/'.$product->image) : null;
                            @endphp
                            <img
                              id="imgPreview"
                              src="{{ $imgUrl ?? '' }}"
                              alt="preview"
                              style="max-width: 100%; max-height: 80px; {{ $imgUrl ? '' : 'display:none;' }} border-radius: 12px;"
                            >
                            <div id="imgPlaceholder" class="text-muted small" style="{{ $imgUrl ? 'display:none;' : '' }}">
                              No image selected
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                      <label class="form-label ap-label" for="notes">Notes</label>
                      <textarea
                        id="notes"
                        name="notes"
                        class="form-control ap-input @error('notes') is-invalid @enderror"
                        rows="3"
                        placeholder="Add any additional details..."
                      >{{ old('notes', $product->notes) }}</textarea>
                      @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                  </div>

                  <!-- Actions -->
                  <div class="pt-3 pt-md-4 mt-3 border-top ap-soft-border">
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                      <a href="{{ route('products.index') }}" class="btn ap-btn-soft px-4 py-2" role="button">Cancel</a>

                      <button
                        type="submit"
                        class="btn btn-primary ap-btn-primary px-4 py-2 d-inline-flex align-items-center justify-content-center gap-2"
                      >
                        <i class="bi bi-save2"></i>
                        <span>Update Product</span>
                      </button>
                    </div>
                  </div>

                </form>

              </div>
            </section>
          </div>

        </div>

      </div>
    </main>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  (function () {
    const form = document.getElementById('editProductForm');

    const purchase = document.getElementById('purchase_price');
    const selling  = document.getElementById('selling_price');

    const profitText = document.getElementById('profitText');
    const marginText = document.getElementById('marginText');

    const imgInput = document.getElementById('image');
    const imgPreview = document.getElementById('imgPreview');
    const imgPlaceholder = document.getElementById('imgPlaceholder');

    const skuInput = document.getElementById('sku');
    const genSkuBtn = document.getElementById('genSkuBtn');

    function num(v) {
      const n = parseFloat(v);
      return Number.isFinite(n) ? n : 0;
    }

    function updateProfit() {
      const p = num(purchase.value);
      const s = num(selling.value);

      const profit = s - p;
      const margin = p > 0 ? (profit / p) * 100 : 0;

      profitText.textContent = profit.toFixed(2);
      marginText.textContent = (p > 0 ? margin : 0).toFixed(0) + '%';

      if (selling.value !== '' && s < p) {
        selling.setCustomValidity('Selling price must be >= purchase price');
      } else {
        selling.setCustomValidity('');
      }
    }

    purchase?.addEventListener('input', updateProfit);
    selling?.addEventListener('input', updateProfit);
    updateProfit();

    imgInput?.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      imgPreview.src = url;
      imgPreview.style.display = 'block';
      if (imgPlaceholder) imgPlaceholder.style.display = 'none';
    });

    genSkuBtn?.addEventListener('click', function () {
      const rand = Math.random().toString(16).slice(2, 6).toUpperCase();
      const time = Date.now().toString().slice(-4);
      skuInput.value = skuInput.value?.trim() || ('PR-' + rand + '-' + time);
    });

    form?.addEventListener('submit', function (e) {
      updateProfit();
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  })();
</script>

@endsection
