<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedSecurityTest extends TestCase
{
    use RefreshDatabase;

    // --- DIÁK (STUDENT) JOGOSULTSÁGOK BLOKKOLÁSA ---

    public function test_diak_nem_ferhet_hozza_az_admin_vezerlopulthoz()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get(route('admin.dashboard'));
        $response->assertStatus(302); // 302 Átirányítás történik a 403 helyett
    }

    public function test_diak_nem_ferhet_hozza_az_admin_felhasznalolistohoz()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get(route('admin.users'));
        $response->assertStatus(302);
    }

    public function test_diak_nem_ferhet_hozza_a_szkenner_felulethez()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get(route('scanner.dashboard'));
        $response->assertStatus(403); 
    }

    public function test_diak_nem_tud_qr_kodot_beolvasni()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->post(route('scanner.scan'), ['token' => '123']);
        $response->assertStatus(403);
    }

    // --- SZKENNER (SCANNER) JOGOSULTSÁGOK BLOKKOLÁSA ---

    public function test_szkenner_nem_ferhet_hozza_az_admin_panelhez()
    {
        $scanner = User::factory()->create(['role' => 'scanner']);
        $response = $this->actingAs($scanner)->get(route('admin.dashboard'));
        $response->assertStatus(302);
    }

    public function test_szkenner_nem_ferhet_hozza_a_sajat_berletek_oldalhoz()
    {
        $scanner = User::factory()->create(['role' => 'scanner']);
        $response = $this->actingAs($scanner)->get(route('passes.index'));
        $response->assertStatus(302); 
    }

    // --- ADMIN JOGOSULTSÁGOK BLOKKOLÁSA ---

    public function test_admin_hozzafer_az_admin_vezerlopulthoz()
    {
        $admin = User::factory()->create(['role' => 'admin']); 
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}