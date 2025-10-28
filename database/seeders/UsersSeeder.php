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
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'u@u.hu',
            'password' => Hash::make('password123'),
            'is_admin' => false,
            'student_type' => 'Egyetem',
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'a@a.hu',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'student_type' => 'Egyetem',
        ]);
    }
}
