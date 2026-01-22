<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DoctorInfo;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medicine_name',
        'dosage',
        'duration',
        'diagnosis',
        'notes',
        'rumor',
        'analysis',
    ];

    protected $casts = [
        'medicine_name' => 'array',
        'dosage' => 'array',
        'duration' => 'array',
        'notes' => 'array',
        'rumor' => 'array',
        'analysis' => 'array',
    ];

    public function patientUser()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(DoctorInfo::class, 'doctor_id');
    }

    public function doctorInfo()
    {
        return $this->hasOne(DoctorInfo::class, 'user_id');
    }
}
