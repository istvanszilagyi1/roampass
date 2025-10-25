<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'User',
            'email' => 'u@u.hu',
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'a@a.hu',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);
    }
}
