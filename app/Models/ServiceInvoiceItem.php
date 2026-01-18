<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInvoiceItem extends Model
{
    protected $fillable = [
        'service_invoice_id',
        'service_id',
        'service_name',
        'doctor_name',
        'department',
        'qty',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ServiceInvoice::class, 'service_invoice_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
