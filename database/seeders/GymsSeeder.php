<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gym;

class GymsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gym::create([
            'name' => 'Downtown Gym',
            'city' => 'Budapest',
            'address' => 'Kossuth Lajos utca 12',
            'opening_hours' => '06:00 - 22:00',
            'image_url' => '/images/gyms/downtown.jpg'
        ]);

        Gym::create([
            'name' => 'Elite Fitness',
            'city' => 'Budapest',
            'address' => 'Andrássy út 50',
            'opening_hours' => '05:30 - 23:00',
            'image_url' => '/images/gyms/elite.jpg'
        ]);

        Gym::create([
            'name' => 'PowerZone',
            'city' => 'Debrecen',
            'address' => 'Piac utca 3',
            'opening_hours' => '06:00 - 22:00',
            'image_url' => '/images/gyms/powerzone.jpg'
        ]);

        Gym::create([
            'name' => 'Flex Arena',
            'city' => 'Szeged',
            'address' => 'Petőfi S. sgt. 7',
            'opening_hours' => '06:30 - 21:30',
            'image_url' => '/images/gyms/flexarena.jpg'
        ]);
    }
}
