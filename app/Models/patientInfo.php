<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patientInfo extends Model
{
    public function user()
{
    return $this->belongsTo(User::class);
}
protected $fillable = [
    'user_id',
    'gender',
    'dob',
    'phone',
    'address',
    'blood_type',
    'weight',
    'height',
    'emergency_contact_name',
    'emergency_contact_phone',
    'medical_history',
    'allergies',
    'current_medications',
    'notes',
];

protected $casts = [
  'dob' => 'date',
];

}
