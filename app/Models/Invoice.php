<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'type',
        'issued_at',
        'client_id',
        'client_name',
        'payment_method',
        'status',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'paid_amount',
        'balance_due',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'issued_at'    => 'datetime',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'grand_total'  => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'balance_due'  => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
