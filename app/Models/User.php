<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Models\GymPass;
use App\Models\Setting;

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
        'student_id_number',
        'student_card_path',
        'student_id_verified',
        'student_id_expiry',
        'student_card_front',
        'student_card_back',
        'role',
        'ocr_status',
        'ocr_confidence',
        'wants_newsletter',
        'privacy_policy_accepted_at',
        'is_admin'
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
            'student_id_expiry' => 'datetime',
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
        // Ellenőrizzük, hogy szükséges-e egyáltalán a diákigazolvány
        $isUploadRequired = Setting::getValue('student_id_upload_required', '0') === '1';
        
        // Ha nem szükséges, akkor minden valid
        if (!$isUploadRequired) {
            return true;
        }
        
        if (empty($this->student_id_number)) {
            return false;
        }
        
        if (!$this->student_id_verified) {
            return false;
        }

        if ($this->student_id_expiry && $this->student_id_expiry < now()) {
            return false;
        }
        
        return true;
    }

    public function getStudentCardStatus(): array
    {
        $isUploadRequired = Setting::getValue('student_id_upload_required', '0') === '1';
        
        if (!$isUploadRequired) {
            return [
                'valid' => true,
                'status' => 'not_required',
                'message' => 'Diákigazolvány nem szükséges'
            ];
        }
        
        if (empty($this->student_id_number)) {
            return [
                'valid' => false,
                'status' => 'missing_number',
                'message' => 'Diákigazolvány szám megadása kötelező'
            ];
        }
        
        if (!$this->student_id_verified) {
            $statusMessage = match($this->ocr_status) {
                'pending' => 'A diákigazolványod ellenőrzése folyamatban van',
                'fail' => 'A diákigazolványod ellenőrzése sikertelen volt',
                default => 'A diákigazolványod nincs hitelesítve'
            };
            
            return [
                'valid' => false,
                'status' => $this->ocr_status ?? 'unverified',
                'message' => $statusMessage
            ];
        }
        
        if ($this->student_id_expiry && $this->student_id_expiry < now()) {
            return [
                'valid' => false,
                'status' => 'expired',
                'message' => 'A diákigazolványod lejárt: ' . $this->student_id_expiry->format('Y.m.d')
            ];
        }
        
        return [
            'valid' => true,
            'status' => 'verified',
            'message' => 'Diákigazolvány hitelesítve'
        ];
    }
    public function scannerProfile() {
        return $this->hasOne(Scanner::class);
    }
    public function hasActiveStudentId()
    {
        if (!$this->student_id_verified) {
            return false;
        }

        if ($this->student_id_expiry && now()->greaterThan($this->student_id_expiry)) {
            return false;
        }

        return true;
    }

}
