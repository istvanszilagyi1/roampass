<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $fillable = [
        'gym_id',
        'invoice_number',
        'amount',
        'pdf_url',
        'issue_date',
        'status',
    ];
    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
