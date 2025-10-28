<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scanner extends Model
{
    protected $fillable = [
        'gym_id',
        'name',
        'token',
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
