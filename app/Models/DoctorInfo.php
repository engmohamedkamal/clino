<?php
// app/Models/DoctorInfo.php
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
        'Specialization',
        'license_number',
        'address',
        'availability_schedule',
        'facebook',
        'instagram',
        'twitter',
        'skills',
        'activities',
        'image',
        'about',
    ];



    // app/Models/DoctorInfo.php
    protected $casts = [
        'dob' => 'date',
        'availability_schedule' => 'array',
        'Specialization' => 'array',
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
}


