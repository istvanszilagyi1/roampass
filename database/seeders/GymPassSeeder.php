<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GymPass;
use Carbon\Carbon;

class GymPassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            GymPass::create([
                'user_id' => $user->id,
                'remaining_uses' => 12,
                'purchase_date' => Carbon::now()->subDays(rand(1, 30)),
                'qr_code_url' => '/images/qrcodes/sample-qr.png', // Példa QR kód
            ]);
        }
    }
}
