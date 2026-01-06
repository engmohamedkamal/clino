<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id ?? null;

        return [
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:255|unique:products,sku,' . $productId,
            'category'       => 'nullable|string|max:255',
            'unit'           => 'nullable|string|max:50',

            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0|gte:purchase_price',

            'quantity'       => 'required|integer|min:0',
            'reorder_level'  => 'nullable|integer|min:0',

            'location'       => 'nullable|string|max:255',
            'expiry_date'    => 'nullable|date',
            'supplier'       => 'nullable|string|max:255',

             'status' => "nullable|in:0,1",

            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'notes'          => 'nullable|string',
        ];
    }
}
