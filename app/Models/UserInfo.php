<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
     protected $fillable = [
        'user_id',
        'availability_schedule',
        'gender',
        'dob',
        'specialization',
        'license_number',
        'address',
        'image',
        'about',
    ];
}
