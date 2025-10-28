<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $fillable = [
        'gym_id',
        'scanner_id',
        'gym_pass_id',
        'user_id',
        'status',
        'revenue_amount',
        'scanned_at',
    ];

    protected $dates = ['scanned_at'];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function scanner()
    {
        return $this->belongsTo(Scanner::class);
    }

    public function gymPass()
    {
        return $this->belongsTo(GymPass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
