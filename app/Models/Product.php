<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit',
        'purchase_price',
        'selling_price',
        'quantity',
        'reorder_level',
        'location',
        'expiry_date',
        'supplier',
        'status',
        'image',
        'notes',
    ];

    protected $casts = [
        'status' => 'boolean',
        'expiry_date' => 'date',
    ];


    public function getProfitAttribute()
    {
        return $this->selling_price - $this->purchase_price;
    }

    // low stock checker
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->reorder_level;
    }
}
