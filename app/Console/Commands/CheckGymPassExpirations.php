<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GymPass;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentIdExpiringMail;
use App\Mail\PassExpiringMail;

class CheckGymPassExpirations extends Command
{
    protected $signature = 'gympass:check-expiration';
    protected $description = 'Lejárt bérletek törlése és diákigazolványok érvényességének ellenőrzése';

    public function handle()
    {
        $warningDate = Carbon::now()->addDays(5)->toDateString();
        $now = Carbon::now();

        ActionLog::log('SYSTEM_CLEANUP', "Automatikus karbantartás elindítva: $now");

        $notifiableStudents = User::whereDate('student_id_expiry', $warningDate)
                                    ->where('student_id_verified', true)
                                    ->get();
        foreach ($notifiableStudents as $user) {
            Mail::to($user->email)->send(new StudentIdExpiringMail($user));
            $this->info("Értesítés elküldve (diák lejárat): {$user->email}");
        }

        $notifiablePasses = GymPass::whereDate('expires_at', $warningDate)
                                    ->where('remaining_uses', '>', 0)
                                    ->get();
        foreach ($notifiablePasses as $pass) {
            Mail::to($pass->user->email)->send(new PassExpiringMail($pass));
            $this->info("Értesítés elküldve (bérlet lejárat): {$pass->user->email}");
        }

        $expiredPasses = GymPass::where('expires_at', '<', $now)
                                ->orWhere('remaining_uses', '<=', 0)
                                ->get();

        $passCount = 0;

        foreach ($expiredPasses as $pass) {
            $user = $pass->user;
            $email = $user ? $user->email : 'Ismeretlen';
            $reason = ($pass->remaining_uses <= 0) ? 'elfogyott alkalmak' : 'időbeli lejárat';

            ActionLog::log('SYSTEM_CLEANUP', "Bérlet törölve ($reason): $email");

            $pass->delete();
            $passCount++;

            $this->info("Bérlet törölve: $email ($reason)");
        }

        $expiredStudents = User::where('student_id_verified', true)
                               ->whereNotNull('student_id_expiry')
                               ->where('student_id_expiry', '<', $now)
                               ->get();

        $studentCount = 0;

        foreach ($expiredStudents as $user) {
            if ($user->student_card_front) {
                Storage::disk('public')->delete($user->student_card_front);
            }
            if ($user->student_card_back) {
                Storage::disk('public')->delete($user->student_card_back);
            }

            $user->update([
                'student_id_verified' => false,
                'student_card_front' => null,
                'student_card_back' => null,
                'student_id_expiry' => null
            ]);

            ActionLog::log('SYSTEM_CLEANUP', "Diákigazolvány lejárt, képek törölve, státusz inaktiválva: {$user->email}");
            
            $this->info("Igazolvány lejárt és képek törölve: {$user->email}");
            $studentCount++;
        }

        $this->info("--- Karbantartás vége ---");
        $this->info("Törölt bérletek: $passCount db");
        $this->info("Inaktivált diákigazolványok: $studentCount db");
    }
}