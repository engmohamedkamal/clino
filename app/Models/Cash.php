<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    protected $fillable = [
        'cash',
        'cash_out',
        'service',
        'created_by',
    ];

    protected $casts = [
        'cash'     => 'decimal:2',
        'cash_out' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

