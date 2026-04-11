<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'ip_address'];

    public static function log($action, $description)
    {
        // Csak akkor logolunk, ha az admin panelen be van kapcsolva!
        if (Setting::getValue('logging_enabled') === '1') {
            self::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip()
            ]);
        }
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}