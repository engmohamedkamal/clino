<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceInvoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'patient_id',
        'patient_name',
        'patient_phone',
        'patient_code',
        'insurance_provider',
        'notes',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount',
        'tax_percent',
        'tax_amount',
        'total',
        'created_by',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'subtotal'  => 'decimal:2',
        'discount'  => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount'  => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'service_invoice_id');
    }
}
