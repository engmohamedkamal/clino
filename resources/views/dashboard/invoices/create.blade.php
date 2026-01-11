@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('css/invoiceEntry.css') }}" />

{{-- ✅ Bootstrap Icons (لو مش موجودة في layout) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

@php
  $productsJson = $products->map(fn($p) => [
    'id'   => $p->id,
    'name' => $p->name,
    'sku'  => $p->sku,
    'qty'  => (int) $p->quantity,
    'sell' => (float) $p->selling_price,
    'buy'  => (float) $p->purchase_price,
  ])->values();

  $oldProductIds = old('product_id', []);
  $oldQtys       = old('qty', []);
  $oldPrices     = old('unit_price', []);
@endphp

<main class="flex-grow-1">
  <div class="container-fluid ie-container py-3 py-md-4">

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
      <div class="d-flex align-items-start gap-2">
        <button class="btn btn-light d-lg-none ie-btn-soft" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="menu">
          <i class="bi bi-list"></i>
        </button>

        <div>
          <h3 class="mb-1 ie-title">Invoice Entry</h3>
          <div class="ie-subtitle">Create sales or purchase records and update inventory instantly.</div>
        </div>
      </div>

      <div class="ie-pill-online d-none d-sm-inline-flex align-items-center gap-2">
        <span class="ie-dot"></span>
        <span>System Online</span>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger mb-3">
        <div class="fw-semibold mb-1">Please fix the following:</div>
        <ul class="mb-0 ps-3">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
      @csrf

      {{-- hidden --}}
      <input type="hidden" name="tax_rate" id="taxRateInput" value="{{ old('tax_rate', '0.10') }}">
      <input type="hidden" name="type" id="typeInput" value="{{ old('type', 'sale') }}">

      <div class="row g-4">
        {{-- Left --}}
        <div class="col-12 col-xl-8">

          {{-- Form Card --}}
          <section class="card ie-card">
            <div class="card-body p-0">

              {{-- Tabs --}}
              <div class="p-3 p-md-4 border-bottom ie-soft-border">
                <div class="ie-tabs d-flex w-100 w-md-auto gap-2">
                  <button class="btn ie-tab-btn flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-2"
                    type="button" id="tabSale">
                    <i class="bi bi-cart3"></i>
                    <span>Sales (Out)</span>
                  </button>

                  <button class="btn ie-tab-btn flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-2"
                    type="button" id="tabPurchase">
                    <i class="bi bi-box-seam"></i>
                    <span>Purchases (In)</span>
                  </button>
                </div>
              </div>

              {{-- Fields --}}
              <div class="p-3 p-md-4">
                <div class="row g-3">

                  <div class="col-12 col-md-6">
                    <label class="form-label ie-label">INVOICE #</label>
                    <div class="input-group">
                      <span class="input-group-text ie-input-ico"><i class="bi bi-hash"></i></span>
                      <input class="form-control ie-input" name="invoice_no" value="{{ old('invoice_no', $invoiceNo) }}" readonly />
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label ie-label">DATE &amp; TIME</label>
                    <div class="input-group">
                      <span class="input-group-text ie-input-ico"><i class="bi bi-calendar3"></i></span>
                      <input class="form-control ie-input @error('issued_at') is-invalid @enderror"
                        type="datetime-local" name="issued_at"
                        value="{{ old('issued_at', now()->format('Y-m-d\TH:i')) }}" />
                      <span class="input-group-text ie-input-ico"><i class="bi bi-clock"></i></span>
                      @error('issued_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label ie-label">CLIENT (PATIENT / DEPT)</label>
                    <div class="input-group">
                      <span class="input-group-text ie-input-ico"><i class="bi bi-person"></i></span>
                      <select class="form-select ie-input @error('client_id') is-invalid @enderror" name="client_id">
                        <option value="">-- Select Patient (Optional) --</option>
                        @foreach($clients as $c)
                          <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                      </select>

                      <button class="btn ie-plus-btn" type="button" id="toggleClientName" aria-label="add client name">
                        <i class="bi bi-plus-circle"></i>
                      </button>
                      @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Optional free text client name --}}
                    <div class="mt-2 d-none" id="clientNameWrap">
                      <input class="form-control ie-input @error('client_name') is-invalid @enderror"
                        name="client_name" value="{{ old('client_name') }}"
                        placeholder="Or type client / department name...">
                      @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <label class="form-label ie-label">PAYMENT METHOD</label>
                    <select class="form-select ie-input @error('payment_method') is-invalid @enderror" name="payment_method">
                      @php $pm = old('payment_method', 'cash'); @endphp
                      <option value="cash" @selected($pm==='cash')>Cash</option>
                      <option value="card" @selected($pm==='card')>Card</option>
                      <option value="wallet" @selected($pm==='wallet')>Wallet</option>
                      <option value="insurance" @selected($pm==='insurance')>Insurance</option>
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-12 col-md-3">
                    <label class="form-label ie-label">STATUS</label>
                    <select class="form-select ie-input @error('status') is-invalid @enderror" name="status">
                      @php $st = old('status', 'paid'); @endphp
                      <option value="paid" @selected($st==='paid')>Paid</option>
                      <option value="pending" @selected($st==='pending')>Pending</option>
                      <option value="partially_paid" @selected($st==='partially_paid')>Partially Paid</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                </div>
              </div>

            </div>
          </section>

          {{-- Items Card --}}
          <section class="card ie-card mt-4">
            <div class="card-body p-0">

              {{-- Search --}}
              <div class="p-3 p-md-4 border-bottom ie-soft-border">
                <div class="d-flex flex-column flex-md-row gap-2 align-items-stretch">
                  <div class="flex-grow-1">
                    <div class="input-group">
                      <span class="input-group-text ie-input-ico"><i class="bi bi-upc-scan"></i></span>

                      {{-- ✅ datalist search --}}
                      <input class="form-control ie-input" id="productSearch" list="productsDatalist"
                        placeholder="Scan barcode or search item by name/SKU..." />

                      {{-- ✅ هنا بقى suggestions بالاسم وبالـ SKU وبالـ SKU - Name --}}
                      <datalist id="productsDatalist">
                        @foreach($products as $p)
                          <option value="{{ $p->sku }}"></option>
                          <option value="{{ $p->name }}"></option>
                          <option value="{{ $p->sku }} - {{ $p->name }}"></option>
                        @endforeach
                      </datalist>

                      <button class="btn ie-btn-soft d-inline-flex align-items-center justify-content-center gap-2"
                        type="button" id="addBySearch">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add</span>
                      </button>
                    </div>

                    <div class="small text-muted mt-1">
                      Tip: اكتب SKU أو الاسم (أو اختر من القائمة) ثم Add — أو اضغط Enter
                    </div>
                  </div>

                  <button class="btn ie-btn-soft d-inline-flex align-items-center justify-content-center gap-2"
                    type="button" id="browseProductsBtn">
                    <i class="bi bi-list"></i>
                    <span>Browse</span>
                  </button>
                </div>
              </div>

              {{-- Desktop Table --}}
              <div class="table-responsive d-none d-md-block">
                <table class="table ie-table align-middle mb-0">
                  <thead>
                    <tr>
                      <th class="ie-th">ITEM DETAILS</th>
                      <th class="ie-th text-center">UNIT PRICE</th>
                      <th class="ie-th text-center">QUANTITY</th>
                      <th class="ie-th text-end">LINE TOTAL</th>
                      <th class="ie-th text-end"></th>
                    </tr>
                  </thead>

                  <tbody id="itemsTbody">
                    {{-- old --}}
                    @foreach($oldProductIds as $i => $pid)
                      @php
                        $prod = $products->firstWhere('id', (int)$pid);
                        $qty  = (int)($oldQtys[$i] ?? 1);
                        $price= (float)($oldPrices[$i] ?? ($prod ? ($prod->selling_price) : 0));
                        $line = $qty * $price;
                      @endphp

                      @if($prod)
                      <tr class="ie-row" data-product-id="{{ $prod->id }}">
                        <td>
                          <div class="ie-item-title">{{ $prod->name }}</div>
                          <div class="ie-item-sub d-flex flex-wrap align-items-center gap-2">
                            <span class="ie-sku">SKU: {{ $prod->sku }}</span>
                            <span class="ie-stock"><span class="text-muted">Stock:</span>
                              <span class="js-stock">{{ $prod->quantity }}</span>
                            </span>
                          </div>
                          <input type="hidden" name="product_id[]" value="{{ $prod->id }}">
                        </td>

                        <td class="text-center">
                          <div class="ie-price-wrap mx-auto">
                            <span class="ie-dollar">$</span>
                            <input class="form-control ie-price-input js-price"
                                   name="unit_price[]"
                                   value="{{ number_format($price,2,'.','') }}"
                                   inputmode="decimal">
                          </div>
                        </td>

                        <td class="text-center">
                          <div class="ie-qty d-inline-flex align-items-center">
                            <button class="btn ie-qty-btn js-minus" type="button" aria-label="minus">
                              <i class="bi bi-dash"></i>
                            </button>
                            <input class="form-control js-qty text-center" name="qty[]" value="{{ $qty }}" style="width:64px;">
                            <button class="btn ie-qty-btn js-plus" type="button" aria-label="plus">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </td>

                        <td class="text-end ie-line-total js-line">$ {{ number_format($line,2,'.','') }}</td>

                        <td class="text-end">
                          <button class="btn ie-icon-danger js-remove" type="button" aria-label="delete">
                            <i class="bi bi-trash3"></i>
                          </button>
                        </td>
                      </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
              </div>

              {{-- Mobile List --}}
              <div class="d-md-none p-3" id="itemsMobile"></div>

              {{-- Footer --}}
              <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-top ie-soft-border">
                <div class="ie-footer-left">
                  <span id="itemsCount">0</span> Items in list
                </div>
                <a class="ie-clear" href="#" id="clearAll">Clear All</a>
              </div>

            </div>
          </section>
        </div>

        {{-- Right --}}
        <div class="col-12 col-xl-4">
          <section class="card ie-card">
            <div class="card-body p-4">

              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="ie-sec-ico"><i class="bi bi-receipt-cutoff"></i></div>
                <div class="ie-sec-title">Payment Summary</div>
              </div>

              <div class="ie-sum-row">
                <span class="ie-sum-label">Subtotal</span>
                <span class="ie-sum-val" id="sumSubtotal">$0.00</span>
              </div>

              <div class="ie-sum-row">
                <span class="ie-sum-label">Discount</span>
                <span class="ie-sum-val ie-muted" style="min-width:120px;">
                  <input class="form-control ie-input text-end" name="discount" id="discountInput"
                         value="{{ old('discount', '0') }}">
                </span>
              </div>

              <div class="ie-sum-row">
                <span class="ie-sum-label">Tax (VAT <span id="taxLabel">10%</span>)</span>
                <span class="ie-sum-val" id="sumTax">$0.00</span>
              </div>

              <hr class="ie-hr my-3" />

              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="ie-grand-label">Grand Total</div>
                <div class="ie-grand-val" id="sumGrand">$0.00</div>
              </div>

              <div class="ie-paid-box p-3 mb-3">
                <div class="ie-mini-title mb-2">PAID AMOUNT</div>
                <div class="input-group mb-2">
                  <span class="input-group-text ie-input-ico">$</span>
                  <input class="form-control ie-input text-end" name="paid_amount" id="paidInput"
                         value="{{ old('paid_amount', '0') }}" inputmode="decimal" />
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="ie-sum-label">Balance Due</span>
                  <span class="ie-balance" id="sumBalance">$0.00</span>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button class="btn btn-primary ie-btn-primary py-3 d-inline-flex align-items-center justify-content-center gap-2"
                  type="submit" id="submitBtn">
                  <i class="bi bi-check2"></i>
                  <span>Confirm &amp; Update Stock</span>
                </button>

                <a class="btn ie-btn-soft py-3 d-inline-flex align-items-center justify-content-center gap-2"
                  href="{{ route('invoices.index') }}">
                  <i class="bi bi-x-lg"></i>
                  <span>Cancel</span>
                </a>
              </div>

            </div>
          </section>
        </div>
      </div>
    </form>

  </div>
</main>

{{-- Browse Modal --}}
<div class="modal fade" id="productsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Browse Products</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" id="modalSearch" placeholder="Search by name or SKU...">
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-end">Stock</th>
                <th class="text-end"></th>
              </tr>
            </thead>
            <tbody id="modalTbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const products = @json($productsJson);

  const form = document.getElementById('invoiceForm');
  const typeInput = document.getElementById('typeInput');

  const tabSale = document.getElementById('tabSale');
  const tabPurchase = document.getElementById('tabPurchase');

  const itemsTbody = document.getElementById('itemsTbody');
  const itemsMobile = document.getElementById('itemsMobile');

  const productSearch = document.getElementById('productSearch');
  const addBySearch = document.getElementById('addBySearch');

  const itemsCount = document.getElementById('itemsCount');

  const discountInput = document.getElementById('discountInput');
  const paidInput = document.getElementById('paidInput');
  const taxRateInput = document.getElementById('taxRateInput');

  const sumSubtotal = document.getElementById('sumSubtotal');
  const sumTax = document.getElementById('sumTax');
  const sumGrand = document.getElementById('sumGrand');
  const sumBalance = document.getElementById('sumBalance');

  const clearAll = document.getElementById('clearAll');

  const toggleClientName = document.getElementById('toggleClientName');
  const clientNameWrap = document.getElementById('clientNameWrap');

  // modal
  const browseBtn = document.getElementById('browseProductsBtn');
  const modalEl = document.getElementById('productsModal');
  const modalSearch = document.getElementById('modalSearch');
  const modalTbody = document.getElementById('modalTbody');
  let bsModal = null;

  function money(n) {
    n = Number(n || 0);
    return '$' + n.toFixed(2);
  }

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  function normalize(str) {
    return String(str || '')
      .toLowerCase()
      .replace(/\s+/g, ' ')
      .trim();
  }

  function getTaxRate() {
    const r = Number(taxRateInput.value || 0.10);
    return isFinite(r) ? r : 0.10;
  }

  function currentType() {
    return (typeInput.value || 'sale');
  }

  function setType(type) {
    typeInput.value = type;
    tabSale.classList.toggle('active', type === 'sale');
    tabPurchase.classList.toggle('active', type === 'purchase');

    // update default prices if user didn't customize
    itemsTbody.querySelectorAll('tr.ie-row').forEach(tr => {
      const pid = Number(tr.dataset.productId);
      const p = products.find(x => x.id === pid);
      if (!p) return;

      const priceInput = tr.querySelector('.js-price');
      if (!priceInput) return;

      const val = Number(priceInput.value || 0);
      const newDefault = (type === 'purchase') ? p.buy : p.sell;
      const otherDefault = (type === 'purchase') ? p.sell : p.buy;

      if (val === 0 || Math.abs(val - otherDefault) < 0.001) {
        priceInput.value = Number(newDefault).toFixed(2);
      }
    });

    recalcAll();
    renderMobile();
  }

  tabSale.addEventListener('click', () => setType('sale'));
  tabPurchase.addEventListener('click', () => setType('purchase'));

  // init tabs based on old
  setType("{{ old('type', 'sale') }}");

  // Toggle client name input
  toggleClientName?.addEventListener('click', () => {
    clientNameWrap?.classList.toggle('d-none');
  });

  // ✅ NEW: supports SKU / Name / "SKU - Name"
  function findProductBySearchValue(v) {
    v = normalize(v);
    if (!v) return null;

    const skuCandidate = normalize(v.split('-')[0]);

    // SKU exact
    let p = products.find(x => normalize(x.sku) === skuCandidate);
    if (p) return p;

    // SKU exact by full text
    p = products.find(x => normalize(x.sku) === v);
    if (p) return p;

    // Name exact
    p = products.find(x => normalize(x.name) === v);
    if (p) return p;

    // contains
    p = products.find(x =>
      normalize(x.name).includes(v) || normalize(x.sku).includes(v)
    );

    return p || null;
  }

  function rowExists(productId) {
    return itemsTbody.querySelector(`tr.ie-row[data-product-id="${productId}"]`);
  }

  function buildRow(p, qty = 1) {
    const type = currentType();
    const unitPrice = (type === 'purchase') ? p.buy : p.sell;

    const tr = document.createElement('tr');
    tr.className = 'ie-row';
    tr.dataset.productId = p.id;

    tr.innerHTML = `
      <td>
        <div class="ie-item-title">${escapeHtml(p.name)}</div>
        <div class="ie-item-sub d-flex flex-wrap align-items-center gap-2">
          <span class="ie-sku">SKU: ${escapeHtml(p.sku || '-')}</span>
          <span class="ie-stock"><span class="text-muted">Stock:</span> <span class="js-stock">${p.qty}</span></span>
        </div>
        <input type="hidden" name="product_id[]" value="${p.id}">
      </td>

      <td class="text-center">
        <div class="ie-price-wrap mx-auto">
          <span class="ie-dollar">$</span>
          <input class="form-control ie-price-input js-price" name="unit_price[]"
                 value="${Number(unitPrice).toFixed(2)}" inputmode="decimal">
        </div>
      </td>

      <td class="text-center">
        <div class="ie-qty d-inline-flex align-items-center">
          <button class="btn ie-qty-btn js-minus" type="button" aria-label="minus"><i class="bi bi-dash"></i></button>
          <input class="form-control js-qty text-center" name="qty[]" value="${qty}" style="width:64px;">
          <button class="btn ie-qty-btn js-plus" type="button" aria-label="plus"><i class="bi bi-plus"></i></button>
        </div>
      </td>

      <td class="text-end ie-line-total js-line">$0.00</td>

      <td class="text-end">
        <button class="btn ie-icon-danger js-remove" type="button" aria-label="delete">
          <i class="bi bi-trash3"></i>
        </button>
      </td>
    `;

    itemsTbody.appendChild(tr);
    recalcRow(tr);
    return tr;
  }

  function recalcRow(tr) {
    const price = Number(tr.querySelector('.js-price')?.value || 0);
    const qtyInput = tr.querySelector('.js-qty');
    const qty = Math.max(1, Number(qtyInput?.value || 1));
    if (qtyInput) qtyInput.value = qty;

    const line = price * qty;
    tr.querySelector('.js-line').textContent = money(line);
    return line;
  }

  function recalcAll() {
    let subtotal = 0;

    itemsTbody.querySelectorAll('tr.ie-row').forEach(tr => {
      subtotal += recalcRow(tr);
    });

    const discount = Math.max(0, Number(discountInput.value || 0));
    discountInput.value = discount;

    const taxRate = getTaxRate();
    const tax = Math.max(0, (subtotal - discount)) * taxRate;

    const grand = (subtotal - discount + tax);

    const paid = Math.max(0, Number(paidInput.value || 0));
    paidInput.value = paid.toFixed(2);

    const balance = Math.max(0, grand - paid);

    sumSubtotal.textContent = money(subtotal);
    sumTax.textContent = money(tax);
    sumGrand.textContent = money(grand);
    sumBalance.textContent = money(balance);

    const count = itemsTbody.querySelectorAll('tr.ie-row').length;
    itemsCount.textContent = count;

    const pct = Math.round(taxRate * 100);
    const taxLabel = document.getElementById('taxLabel');
    if (taxLabel) taxLabel.textContent = pct + '%';
  }

  function renderMobile() {
    itemsMobile.innerHTML = '';

    itemsTbody.querySelectorAll('tr.ie-row').forEach(tr => {
      const pid = Number(tr.dataset.productId);
      const p = products.find(x => x.id === pid);
      if (!p) return;

      const price = tr.querySelector('.js-price')?.value || '0.00';
      const qty = tr.querySelector('.js-qty')?.value || '1';
      const line = tr.querySelector('.js-line')?.textContent || '$0.00';

      const card = document.createElement('article');
      card.className = 'card ie-card mb-3';
      card.dataset.productId = pid;

      card.innerHTML = `
        <div class="card-body p-3">
          <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
              <div class="ie-item-title">${escapeHtml(p.name)}</div>
              <div class="ie-item-sub d-flex flex-wrap align-items-center gap-2 mt-1">
                <span class="ie-sku">SKU: ${escapeHtml(p.sku || '-')}</span>
                <span class="ie-stock">${p.qty} in stock</span>
              </div>
            </div>

            <button class="btn ie-icon-danger js-remove-mobile" type="button" aria-label="delete">
              <i class="bi bi-trash3"></i>
            </button>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-6">
              <div class="ie-label">UNIT PRICE</div>
              <div class="input-group mt-1">
                <span class="input-group-text ie-input-ico">$</span>
                <input class="form-control ie-input text-center js-price-mobile" value="${escapeHtml(price)}" inputmode="decimal" />
              </div>
            </div>

            <div class="col-6">
              <div class="ie-label">QUANTITY</div>
              <div class="d-flex align-items-center gap-2 mt-1">
                <button class="btn ie-qty-btn flex-shrink-0 js-minus-mobile" type="button" aria-label="minus">
                  <i class="bi bi-dash"></i>
                </button>
                <input class="form-control ie-input text-center js-qty-mobile" value="${escapeHtml(qty)}" />
                <button class="btn ie-qty-btn flex-shrink-0 js-plus-mobile" type="button" aria-label="plus">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between mt-2">
                <div class="ie-label">LINE TOTAL</div>
                <div class="fw-semibold js-line-mobile">${escapeHtml(line)}</div>
              </div>
            </div>
          </div>
        </div>
      `;

      itemsMobile.appendChild(card);
    });
  }

  // ✅ Add by search button
  function addFromSearch() {
    const p = findProductBySearchValue(productSearch.value);
    if (!p) {
      alert('Product not found. Try SKU or name.');
      return;
    }

    const existing = rowExists(p.id);
    if (existing) {
      const q = existing.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1)) + 1;
    } else {
      buildRow(p, 1);
    }

    recalcAll();
    renderMobile();
    productSearch.value = '';
    productSearch.focus();
  }

  addBySearch.addEventListener('click', addFromSearch);

  // ✅ Enter = Add
  productSearch.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      addFromSearch();
    }
  });

  // events on table
  itemsTbody.addEventListener('click', (e) => {
    const tr = e.target.closest('tr.ie-row');
    if (!tr) return;

    if (e.target.closest('.js-remove')) {
      tr.remove();
      recalcAll(); renderMobile();
      return;
    }

    if (e.target.closest('.js-minus')) {
      const q = tr.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1) - 1);
      recalcAll(); renderMobile();
      return;
    }

    if (e.target.closest('.js-plus')) {
      const q = tr.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1) + 1);
      recalcAll(); renderMobile();
      return;
    }
  });

  itemsTbody.addEventListener('input', (e) => {
    if (e.target.closest('.js-price') || e.target.closest('.js-qty')) {
      recalcAll(); renderMobile();
    }
  });

  [discountInput, paidInput, taxRateInput].forEach(el => {
    el.addEventListener('input', () => { recalcAll(); renderMobile(); });
  });

  clearAll.addEventListener('click', (e) => {
    e.preventDefault();
    itemsTbody.innerHTML = '';
    recalcAll(); renderMobile();
  });

  // mobile actions
  itemsMobile.addEventListener('click', (e) => {
    const card = e.target.closest('article[data-product-id]');
    if (!card) return;

    const pid = Number(card.dataset.productId);
    const tr = rowExists(pid);
    if (!tr) return;

    if (e.target.closest('.js-remove-mobile')) {
      tr.remove();
      recalcAll(); renderMobile();
      return;
    }

    if (e.target.closest('.js-minus-mobile')) {
      const q = tr.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1) - 1);
      recalcAll(); renderMobile();
      return;
    }

    if (e.target.closest('.js-plus-mobile')) {
      const q = tr.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1) + 1);
      recalcAll(); renderMobile();
      return;
    }
  });

  itemsMobile.addEventListener('input', (e) => {
    const card = e.target.closest('article[data-product-id]');
    if (!card) return;

    const pid = Number(card.dataset.productId);
    const tr = rowExists(pid);
    if (!tr) return;

    if (e.target.closest('.js-price-mobile')) tr.querySelector('.js-price').value = e.target.value;
    if (e.target.closest('.js-qty-mobile')) tr.querySelector('.js-qty').value = e.target.value;

    recalcAll(); renderMobile();
  });

  // Browse modal
  function openModal() {
    if (!modalEl) return;
    bsModal = bsModal || new bootstrap.Modal(modalEl);
    bsModal.show();
    fillModal('');
    setTimeout(() => modalSearch?.focus(), 200);
  }

  function fillModal(q) {
    q = normalize(q);
    const filtered = products.filter(p => {
      if (!q) return true;
      return normalize(p.name).includes(q) || normalize(p.sku).includes(q);
    });

    modalTbody.innerHTML = filtered.map(p => `
      <tr>
        <td>${escapeHtml(p.name)}</td>
        <td class="text-muted">${escapeHtml(p.sku || '-')}</td>
        <td class="text-end">${p.qty}</td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-primary js-modal-add" data-id="${p.id}">Add</button>
        </td>
      </tr>
    `).join('');
  }

  browseBtn.addEventListener('click', openModal);
  modalSearch?.addEventListener('input', () => fillModal(modalSearch.value));

  modalTbody.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-modal-add');
    if (!btn) return;

    const pid = Number(btn.dataset.id);
    const p = products.find(x => x.id === pid);
    if (!p) return;

    const existing = rowExists(p.id);
    if (existing) {
      const q = existing.querySelector('.js-qty');
      q.value = Math.max(1, Number(q.value || 1)) + 1;
    } else {
      buildRow(p, 1);
    }

    recalcAll();
    renderMobile();
  });

  // initial
  recalcAll();
  renderMobile();

  // submit guard
  form.addEventListener('submit', (e) => {
    const count = itemsTbody.querySelectorAll('tr.ie-row').length;
    if (count === 0) {
      e.preventDefault();
      alert('Please add at least 1 item.');
    }
  });
})();
</script>
@endsection
