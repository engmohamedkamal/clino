<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_name',
        'public_diagnosis',
        'private_diagnosis',
        'created_by',
    ];

    /* ================= Relations ================= */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function doctorInfo()
{
    return $this->hasOne(DoctorInfo::class, 'user_id');
}

}
