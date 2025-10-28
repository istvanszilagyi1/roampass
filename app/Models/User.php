<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'student_card_path',
        'student_id_verified',
        'student_id_expiry',
        'student_card_front',
        'student_card_back',
        'student_type',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function gymPasses()
    {
        return $this->hasMany(GymPass::class);
    }

    public function gyms()
    {
        return $this->hasOne(Gym::class, 'owner_id');
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function ownedGym()
    {
        return $this->hasOne(Gym::class, 'owner_id');
    }

    public function hasValidStudentCard(): bool
    {
        return $this->student_id_verified
            && $this->student_id_expiry
            && Carbon::parse($this->student_id_expiry)->isFuture();
    }
    public function scannerProfile() {
        return $this->hasOne(Scanner::class);
    }

}
