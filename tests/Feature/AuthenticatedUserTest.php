<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_bejelentkezett_diak_meg_tudja_nyitni_a_profiljat()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get(route('profile.edit'));
        $response->assertStatus(200);
    }

    public function test_bejelentkezett_diak_el_tudja_erni_a_berleteit()
    {
        // 1. Felhasználó létrehozása manuálisan, minden szükséges adattal
        $student = \App\Models\User::create([
            'first_name' => 'Teszt',
            'last_name' => 'Diák',
            'email' => 'diak' . uniqid() . '@test.hu', // Egyedi email
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id_number' => '7123456789', // Ez kell a validáláshoz
            'student_id_verified' => true,      // Ez akadályozza meg az átirányítást
        ]);

        // 2. Bejelentkezés és az oldal lekérése
        $response = $this->actingAs($student)->get(route('passes.index'));

        $response->assertStatus(200);
    }

    public function test_bejelentkezett_diak_el_tudja_erni_a_vasarlas_oldalt()
    {
        // Itt is szükség van a verifikált diákra a 200-as kódhoz
        $student = \App\Models\User::create([
            'first_name' => 'Vásárló',
            'last_name' => 'Diák',
            'email' => 'vasarlo' . uniqid() . '@test.hu',
            'password' => bcrypt('password'),
            'role' => 'student',
            'student_id_number' => '7987654321',
            'student_id_verified' => true,
        ]);

        $response = $this->actingAs($student)->get(route('passes.create'));
        $response->assertStatus(200);
    }

    public function test_kijelentkezes_utan_mar_nem_elerheto_a_profil()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        // Bejelentkezik
        $this->actingAs($student);
        
        // Kijelentkezik (Post kérés a logout route-ra, ahogy az auth.php definiálja)
        $this->post('/logout');

        // Megpróbálja megnyitni a profilt
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect('/login');
    }
}