<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymPass extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'remaining_uses', 'purchase_date', 'expires_at', 'qr_code_url', 'qr_token'];

    protected $casts = [
        'purchase_date' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}

