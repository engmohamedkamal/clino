<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /* =========================
       Index
    ========================= */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $products = Product::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%")
                      ->orWhere('category', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.products.index', compact('products', 'q'));
    }

    /* =========================
       Create
    ========================= */
    public function create()
    {
        return view('dashboard.products.create');
    }

    /* =========================
       Store
    ========================= */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // checkbox status
        $data['status'] = $request->boolean('status');

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully');
    }

    /* =========================
       Show (اختياري)
    ========================= */
    public function show(Product $product)
    {
        return view('dashboard.products.show', compact('product'));
    }

    /* =========================
       Edit
    ========================= */
    public function edit(Product $product)
    {
        return view('dashboard.products.edit', compact('product'));
    }

    /* =========================
       Update
    ========================= */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // handle image update
        if ($request->hasFile('image')) {

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['status'] = $request->boolean('status');

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /* =========================
       Destroy
    ========================= */
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully');
    }

    /* =========================
       Generate SKU (optional)
    ========================= */
    public function generateSku()
    {
        $sku = 'PR-' . strtoupper(Str::random(4)) . '-' . now()->format('His');

        return response()->json([
            'sku' => $sku
        ]);
    }

    public function decreaseQty(Product $product)
{
    if ($product->quantity > 0) {
        $product->decrement('quantity');
    }

    return back()->with('success', 'Quantity updated');
}

}
