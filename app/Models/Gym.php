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
        'billing_name', 
        'billing_address', 
        'tax_number',
        'payout_per_scan',
    ];

    public function users() {
        return $this->hasMany(User::class);
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
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest(); // Legfrissebb elől
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getOccupancyStatus()
    {
        $recentScans = $this->scans()
                            ->where('scanned_at', '>=', now()->subHours(2))
                            ->where('status', 'success')
                            ->count();

        if ($recentScans < 1) {
            return [
                'level' => 'Szellős',
                'color' => 'text-emerald-400',
                'bg' => 'bg-emerald-500/20',
                'icon' => 'user'
            ];
        } elseif ($recentScans < 2) {
            return [
                'level' => 'Közepes',
                'color' => 'text-amber-400',
                'bg' => 'bg-amber-500/20',
                'icon' => 'users'
            ];
        } else {
            return [
                'level' => 'Tömött',
                'color' => 'text-red-400',
                'bg' => 'bg-red-500/20',
                'icon' => 'users-round'
            ];
        }
    }
}
