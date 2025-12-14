<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
      protected $fillable = [
        'name',
        'phone',
        'id_number',
        'register_for',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
