<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'dob',

        // JSON
        'Specialization',
        'availability_schedule',
        'activities',
        'skills',

        // other fields
        'license_number',
        'address',
        'facebook',
        'instagram',
        'twitter',
        'image',
        'about',
    ];

    protected $casts = [
        'dob' => 'date',

        // JSON casts
        'Specialization' => 'array',
        'availability_schedule' => 'array',
        'activities' => 'array',
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'doctor_service'
        )
        ->withPivot(['price', 'duration', 'active'])
        ->withTimestamps();
    }

    // App\Models\User.php

public function reports()
{
    return $this->hasMany(Report::class, 'doctor_id');
}

public function prescriptions()
{
    return $this->hasMany(Prescription::class);
}

}
