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
        'image_url',
        'owner_id',
    ];

    public function users() {
        return $this->hasMany(User::class); // dolgozók, ha kell
    }

    public function passes() {
        return $this->hasManyThrough(GymPass::class, User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scanners()
    {
        return $this->hasMany(Scanner::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
