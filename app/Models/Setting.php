<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slogan','vision','mission',
        'facebook','instagram','twitter',
        'phone','email','address','logo',
        'map_url'
    ];
}
