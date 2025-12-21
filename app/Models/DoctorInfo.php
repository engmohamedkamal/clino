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
        'specialization',
        'license_number',
        'dob',
        'availability_schedule',
        'address',
        'image',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'about',
    ];

    /**
     * Relation → each doctor belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
