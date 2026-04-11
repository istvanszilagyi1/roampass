<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Gym;
use App\Models\GymPass;
use App\Models\Scanner;
use App\Models\Scan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScannerTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function createTestData($remainingUses = 10, $token = 'SECRET_TOKEN')
    {
        // 1. Terem létrehozása
        $gym = Gym::create([
            'name' => 'Teszt Konditerem',
            'city' => 'Budapest',
            'address' => 'Váci út 1.',
            'payout_per_scan' => 500,
            'billing_name' => 'Teszt Kft.',
            'billing_address' => 'Budapest, Váci út 1.',
            'tax_number' => '12345678-1-12'
        ]);

        // 2. Szkenner felhasználó
        $scannerUser = User::create([
            'first_name' => 'Szkenner',
            'last_name' => 'Elek',
            'email' => 'scanner' . Str::random(5) . '@test.hu',
            'password' => Hash::make('password'),
            'role' => 'scanner'
        ]);

        // 3. Szkenner profil összekötése a teremmel
        $scannerProfile = Scanner::create([
            'user_id' => $scannerUser->id,
            'gym_id' => $gym->id,
            'name' => 'Főbejárat Szkenner'
        ]);

        // 4. Diák felhasználó
        $student = User::create([
            'first_name' => 'Teszt',
            'last_name' => 'Diák',
            'email' => 'diak' . Str::random(5) . '@test.hu',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        // 5. Bérlet létrehozása
        $pass = GymPass::create([
            'user_id' => $student->id,
            'remaining_uses' => $remainingUses,
            'purchase_date' => now(),
            'expires_at' => now()->addMonth(),
            'qr_token' => $token,
            'qr_code_url' => 'http://test.hu/qr.png'
        ]);

        return [$gym, $scannerUser, $scannerProfile, $student, $pass];
    }

    public function test_sikeres_beolvasas_es_alkalom_levonas()
    {
        [$gym, $scannerUser, $scannerProfile, $student, $pass] = $this->createTestData();

        $response = $this->actingAs($scannerUser)->postJson(route('scanner.scan'), [
            'qr_code' => 'SECRET_TOKEN',
            'deduct' => true
        ]);

        $response->assertJson(['status' => 'deducted']);
        $this->assertEquals(9, $pass->fresh()->remaining_uses);
        $this->assertDatabaseHas('scans', [
            'gym_id' => $gym->id, 
            'revenue_amount' => 500
        ]);
    }

    public function test_beolvasas_visszautasitva_ha_elfogyott_az_alkalom()
    {
        // 0 alkalommal hozzuk létre
        [$gym, $scannerUser, $scannerProfile, $student, $pass] = $this->createTestData(0, 'EMPTY_TOKEN');

        $response = $this->actingAs($scannerUser)->postJson(route('scanner.scan'), [
            'qr_code' => 'EMPTY_TOKEN',
            'deduct' => true
        ]);

        $response->assertJson(['status' => 'no_uses']);
    }

    public function test_beleptetes_visszavonasa_rollback_mukodik()
    {
        [$gym, $scannerUser, $scannerProfile, $student, $pass] = $this->createTestData(5, 'TOKEN123');

        // Manuális beléptetés rekord létrehozása a visszavonáshoz
        $scan = Scan::create([
            'user_id' => $student->id,
            'scanner_id' => $scannerProfile->id,
            'gym_id' => $gym->id,
            'gym_pass_id' => $pass->id,
            'scanned_at' => now(),
            'revenue_amount' => 500
        ]);

        $response = $this->actingAs($scannerUser)->postJson(route('scanner.cancel'), [
            'qr_code' => 'TOKEN123'
        ]);

        $response->assertJson(['status' => 'canceled']);
        
        // 5-ről 6-ra kell nőnie, mert visszavontuk
        $this->assertEquals(6, $pass->fresh()->remaining_uses);
        $this->assertDatabaseMissing('scans', ['id' => $scan->id]);
    }
}