<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',   // ✅
        'item_name',
        'sku',
        'unit_price',
        'qty',
        'line_total',
        'stock_before',
        'stock_after',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // ✅ relation with Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
