<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
  'patient_id',
  'patient_user_id',
  'doctor_id',
  'exam_type',
  'exam_date',
  'exam_image',
  'note',
];

   // App\Models\Report.php

public function patient()
{
    return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
}

public function patientUser()
{
    return $this->belongsTo(\App\Models\User::class, 'patient_user_id');
}

public function doctor()
{
    return $this->belongsTo(\App\Models\User::class, 'doctor_id');
}


}
