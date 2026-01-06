<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'patient_number',
        'dob',
        'patient_email',
        'gender',
        'id_number',
        'address',
        'about',
    ];

    protected $casts = [
        'dob' => 'date',
    ];


// App\Models\Patient.php

public function reports()
{
    return $this->hasMany(Report::class);
}
// App\Models\Patient.php
public function user()
{
    return $this->belongsTo(User::class);
}


}
