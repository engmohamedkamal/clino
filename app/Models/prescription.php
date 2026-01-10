<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',     // users.id (role = patient)
        'doctor_id',      // doctor_infos.id
        'medicine_name',
        'dosage',
        'duration',
        'diagnosis',
        'notes',
    ];

    /* ================= Relations ================= */

    // المريض = User (role patient)
    public function patientUser()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(DoctorInfo::class, 'doctor_id');
    }
}
