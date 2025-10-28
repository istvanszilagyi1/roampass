<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ExpireStudentCards extends Command
{
    protected $signature = 'students:expire';
    protected $description = 'Expire student cards on fixed dates';

    public function handle()
    {
        $today = Carbon::today();
        $monthDay = $today->format('m-d');

        // Október 31 minden típusnál
        if ($monthDay === '10-31') {
            $users = User::where('student_id_verified', true)->get();
            foreach ($users as $user) {
                $user->update([
                    'student_id_verified' => false,
                    'student_card_front' => null,
                    'student_card_back' => null,
                    'student_id_expiry' => null,
                ]);
            }
            $this->info('Október 31-i diákigazolvány törlés lefutott.');
        }

        // Március 31 csak "egyetemi" típusnál
        if ($monthDay === '03-31') {
            $users = User::where('student_id_verified', true)
                         ->where('student_type', 'Egyetem')
                         ->get();
            foreach ($users as $user) {
                $user->update([
                    'student_id_verified' => false,
                    'student_card_front' => null,
                    'student_card_back' => null,
                    'student_id_expiry' => null,
                ]);
            }
            $this->info('Március 31-i egyetemi diákigazolvány törlés lefutott.');
        }
    }
}
