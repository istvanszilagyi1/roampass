<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'opening_hours',
        'image_url'
    ];
}
