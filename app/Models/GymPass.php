<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPass extends Model
{
    protected $fillable = ['user_id', 'remaining_uses', 'purchase_date'];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

