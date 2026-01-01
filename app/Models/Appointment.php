<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_name',
        'doctor_name',
        'gender',
        'appointment_date',
        'appointment_time',
        'patient_number',
        'dob',
        'reason',
    ];

protected $casts = [
  'dob' => 'date',
];

}
