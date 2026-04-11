<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent($verified = true)
    {
        return User::create([
            'first_name' => 'Teszt',
            'last_name' => 'Diák',
            'email' => 'diak' . uniqid() . '@test.hu',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id_number' => '1111111111',
            'student_id_verified' => $verified,
            'ocr_status' => $verified ? 'high' : 'pending'
        ]);
    }

    public function test_profil_adatok_frissitese_diakszam_valtoztatassal()
    {
        Setting::updateOrCreate(['key' => 'student_id_upload_required'], ['value' => '1']);
        
        Http::fake();

        $user = $this->createStudent(true);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'student_id_number' => '2222222222'
        ]);

        $user->refresh();
        
        $this->assertEquals(0, (int)$user->student_id_verified);
        $this->assertEquals('pending', $user->ocr_status);
    }

    public function test_ocr_folyamat_szimulacio_sikeres_azonositasnal()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response(['is_valid' => true, 'confidence' => 99], 200)
        ]);

        Setting::updateOrCreate(['key' => 'student_id_upload_required'], ['value' => '1']);
        
        $user = $this->createStudent(false);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'student_id_number' => '1234567890',
            'student_card_front' => UploadedFile::fake()->image('front.jpg'),
            'student_card_back' => UploadedFile::fake()->image('back.jpg'),
        ]);

        $user->refresh();
        $this->assertEquals(1, (int)$user->student_id_verified);
    }

    public function test_ocr_folyamat_szimulacio_sikertelen_azonositasnal()
    {
        Http::fake([
            '*' => Http::response(['is_valid' => false], 200)
        ]);

        Setting::updateOrCreate(['key' => 'student_id_upload_required'], ['value' => '1']);
        
        $user = $this->createStudent(false);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'student_id_number' => '1234567890',
            'student_card_front' => UploadedFile::fake()->image('front.jpg'),
            'student_card_back' => UploadedFile::fake()->image('back.jpg'),
        ]);

        $user->refresh();
        $this->assertEquals(0, (int)$user->student_id_verified);
        $this->assertEquals('fail', $user->ocr_status);
    }
}