<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    private function userRole(): string
    {
        return (string) (auth()->user()->role ?? '');
    }

    private function canManage(): bool
    {
        // عدّلها حسب صلاحياتك
        return in_array($this->userRole(), ['admin', 'doctor'], true);
    }

    private function normalizeType(string $type): string
    {
        $type = strtolower(trim($type));
        return in_array($type, ['sale', 'purchase'], true) ? $type : 'sale';
    }

    private function normalizePayment(string $payment): string
    {
        $payment = strtolower(trim($payment));
        return in_array($payment, ['cash', 'card', 'wallet', 'insurance'], true) ? $payment : 'cash';
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return in_array($status, ['paid', 'pending', 'partially_paid'], true) ? $status : 'paid';
    }

    /**
     * INV-2026-000001
     */
    private function generateInvoiceNo(): string
    {
        $year = now()->format('Y');

        // آخر فاتورة في نفس السنة
        $last = Invoice::where('invoice_no', 'like', "INV-{$year}-%")
            ->orderByDesc('id')
            ->value('invoice_no');

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq = (int) end($parts);
            $next = $seq + 1;
        }

        return "INV-{$year}-" . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }

    /* ================= Index ================= */

    public function index(Request $request)
    {
        if (!$this->canManage()) abort(403);

        $q = trim((string) $request->get('q', ''));

        $invoices = Invoice::with(['client', 'creator'])
            ->latest()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('invoice_no', 'like', "%{$q}%")
                      ->orWhere('client_name', 'like', "%{$q}%")
                      ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$q}%"));
                });
            })
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.invoices.index', compact('invoices', 'q'));
    }

    /* ================= Create ================= */

    public function create()
    {
        if (!$this->canManage()) abort(403);

        // لو هتعمل بحث/اختيار عميل من users (patients)
        $clients = User::where('role', 'patient')->orderBy('name')->get(['id', 'name']);

        // Products لاختيارها في الواجهة
        $products = Product::orderBy('name')->get([
            'id', 'name', 'sku', 'selling_price', 'purchase_price', 'quantity'
        ]);

        $invoiceNo = $this->generateInvoiceNo();

        return view('dashboard.invoices.create', compact('clients', 'products', 'invoiceNo'));
    }

    /* ================= Store ================= */

    public function store(Request $request)
    {
        if (!$this->canManage()) abort(403);

        $rules = [
            'type'           => ['required', 'in:sale,purchase'],
            'issued_at'      => ['nullable', 'date'],
            'client_id'      => ['nullable', 'integer', 'exists:users,id'],
            'client_name'    => ['nullable', 'string', 'max:255'],

            'payment_method' => ['required', 'in:cash,card,wallet,insurance'],
            'status'         => ['required', 'in:paid,pending,partially_paid'],

            // totals inputs (اختياري/أغلبه بيتحسب)
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'tax_rate'       => ['nullable', 'numeric', 'min:0', 'max:1'], // مثال 0.10

            'paid_amount'    => ['nullable', 'numeric', 'min:0'],

            // items arrays
            'product_id'     => ['required', 'array', 'min:1'],
            'product_id.*'   => ['required', 'integer', 'exists:products,id'],

            'qty'            => ['required', 'array', 'min:1'],
            'qty.*'          => ['required', 'integer', 'min:1'],

            // unit_price: لو هتبعتها من الواجهة (ممكن تتساب ونحسب من DB)
            'unit_price'     => ['nullable', 'array'],
            'unit_price.*'   => ['nullable', 'numeric', 'min:0'],
        ];

        $data = $request->validate($rules);

        $type = $this->normalizeType($data['type']);
        $payment = $this->normalizePayment($data['payment_method']);
        $status = $this->normalizeStatus($data['status']);

        $discount = (float) ($data['discount'] ?? 0);
        $taxRate  = (float) ($data['tax_rate'] ?? 0.10); // افتراضي 10%
        $paidAmount = (float) ($data['paid_amount'] ?? 0);

        // تنظيف ids و qty و prices
        $productIds = collect($data['product_id'])->values()->all();
        $qtys       = collect($data['qty'])->values()->all();

        $pricesInput = collect($data['unit_price'] ?? [])->values()->all();

        if (count($productIds) !== count($qtys)) {
            return back()->withErrors(['product_id' => 'Items arrays mismatch.'])->withInput();
        }

        return DB::transaction(function () use (
            $data, $type, $payment, $status, $discount, $taxRate, $paidAmount,
            $productIds, $qtys, $pricesInput
        ) {
            // رقم فاتورة
            $invoiceNo = $this->generateInvoiceNo();

            // Create invoice first (totals later)
            $invoice = Invoice::create([
                'invoice_no'     => $invoiceNo,
                'type'           => $type,
                'issued_at'      => $data['issued_at'] ?? now(),

                'client_id'      => $data['client_id'] ?? null,
                'client_name'    => $data['client_name'] ?? null,

                'payment_method' => $payment,
                'status'         => $status,

                'subtotal'       => 0,
                'discount'       => $discount,
                'tax'            => 0,
                'grand_total'    => 0,

                'paid_amount'    => $paidAmount,
                'balance_due'    => 0,

                'created_by'     => auth()->id(),
            ]);

            $subtotal = 0.0;

            // loop items
            foreach ($productIds as $i => $pid) {
                $qty = (int) ($qtys[$i] ?? 0);
                if ($qty <= 0) continue;

                // lock row to prevent race conditions
                $product = Product::whereKey($pid)->lockForUpdate()->firstOrFail();

                // unit price:
                // - لو purchase: غالبًا purchase_price
                // - لو sale: selling_price
                $defaultPrice = $type === 'purchase'
                    ? (float) $product->purchase_price
                    : (float) $product->selling_price;

                $unitPrice = isset($pricesInput[$i]) && $pricesInput[$i] !== null && $pricesInput[$i] !== ''
                    ? (float) $pricesInput[$i]
                    : $defaultPrice;

                $stockBefore = (int) $product->quantity;

                // update stock
                if ($type === 'sale') {
                    if ($product->quantity < $qty) {
                        // rollback transaction
                        throw new \RuntimeException("Not enough stock for: {$product->name}");
                    }
                    $product->quantity = $product->quantity - $qty;
                } else {
                    $product->quantity = $product->quantity + $qty;
                }

                $product->save();

                $stockAfter = (int) $product->quantity;

                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'product_id'   => $product->id,

                    // snapshot
                    'item_name'    => $product->name,
                    'sku'          => $product->sku,

                    'unit_price'   => $unitPrice,
                    'qty'          => $qty,
                    'line_total'   => $lineTotal,

                    'stock_before' => $stockBefore,
                    'stock_after'  => $stockAfter,
                ]);
            }

            // totals
            $subtotal = round($subtotal, 2);
            $discount = max(0, round($discount, 2));
            $tax      = round(max(0, ($subtotal - $discount)) * $taxRate, 2);
            $grand    = round(($subtotal - $discount + $tax), 2);

            $paidAmount = round(max(0, $paidAmount), 2);
            $balance    = round(max(0, $grand - $paidAmount), 2);

            // status auto adjust (اختياري)
            if ($paidAmount <= 0) {
                $status = 'pending';
            } elseif ($paidAmount >= $grand) {
                $status = 'paid';
            } else {
                $status = 'partially_paid';
            }

            $invoice->update([
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'tax'          => $tax,
                'grand_total'  => $grand,
                'paid_amount'  => $paidAmount,
                'balance_due'  => $balance,
                'status'       => $status,
            ]);

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');
        }, 3); // retries
    }

    /* ================= Show ================= */

    public function show($invoice)
    {
        if (!$this->canManage()) abort(403);

        $invoice = Invoice::with(['items.product', 'client', 'creator'])->findOrFail((int) $invoice);

        return view('dashboard.invoices.show', compact('invoice'));
    }

    /* ================= Destroy ================= */

    public function destroy($invoice)
    {
        if (!$this->canManage()) abort(403);

        $invoice = Invoice::with('items')->findOrFail((int) $invoice);

        // لو عايز تعمل reverse stock عند الحذف (اختياري)
        // - sale: تزود
        // - purchase: تنقص
        DB::transaction(function () use ($invoice) {
            foreach ($invoice->items as $item) {
                if (!$item->product_id) continue;

                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if (!$product) continue;

                if ($invoice->type === 'sale') {
                    $product->quantity += (int) $item->qty;
                } else {
                    $product->quantity -= (int) $item->qty;
                    if ($product->quantity < 0) $product->quantity = 0;
                }
                $product->save();
            }

            $invoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
