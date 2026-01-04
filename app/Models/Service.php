<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'image', 'status'];
    public function doctors()
    {
        return $this->belongsToMany(
            DoctorInfo::class,
            'doctor_service'
        )
            ->withPivot(['price', 'duration', 'active'])
            ->withTimestamps();
    }

    
}
