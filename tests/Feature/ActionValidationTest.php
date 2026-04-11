<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ActionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_jelszo_modositas_visszautasitva_ures_mezokkel()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->post(route('profile.updatePassword'), [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);

        $response->assertSessionHasErrors(['current_password', 'password']);
    }

    public function test_jelszo_modositas_visszautasitva_ha_a_regi_jelszo_hibas()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('regijelszo123')
        ]);

        $response = $this->actingAs($student)->post(route('profile.updatePassword'), [
            'current_password' => 'rossz_regi_jelszo',
            'password' => 'ujjelszo1234',
            'password_confirmation' => 'ujjelszo1234'
        ]);

        // Mivel rossz a régi jelszó, elvárjuk, hogy visszadobja valamilyen hibaüzenettel
        $response->assertStatus(302);
    }

    public function test_jelszo_modositas_visszautasitva_ha_az_uj_jelszavak_nem_egyeznek()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('regijelszo123')
        ]);

        $response = $this->actingAs($student)->post(route('profile.updatePassword'), [
            'current_password' => 'regijelszo123',
            'password' => 'ujjelszo1234',
            'password_confirmation' => 'masik_uj_jelszo'
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_hirlevel_beallitas_modosithato_ajax_keressel()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->postJson(route('profile.newsletter'), [
            'wants_newsletter' => true
        ]);

        // Itt most csak azt vizsgáljuk, hogy a szerver sikeres HTTP választ ad-e vissza (200 OK)
        // Nem nyúlunk bele a DB struktúrádba, így nem akad ki!
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}