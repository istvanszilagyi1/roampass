<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bejelentkezes_sikertelen_ha_nincs_email_megadva()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'jelszo123'
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_bejelentkezes_sikertelen_ha_nincs_jelszo_megadva()
    {
        $response = $this->post('/login', [
            'email' => 'teszt@roampass.hu',
            'password' => ''
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_regisztracio_sikertelen_ha_nincs_keresztnev()
    {
        $response = $this->post('/register', [
            'first_name' => '',
            'last_name' => 'Kovács',
            'email' => 'teszt@roampass.hu',
            'password' => 'jelszo123',
            'password_confirmation' => 'jelszo123'
        ]);
        $response->assertSessionHasErrors('first_name');
    }

    public function test_regisztracio_sikertelen_ha_nincs_email()
    {
        $response = $this->post('/register', [
            'first_name' => 'Teszt',
            'last_name' => 'Kovács',
            'email' => '',
            'password' => 'jelszo123',
            'password_confirmation' => 'jelszo123'
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_regisztracio_sikertelen_rovid_jelszo_eseten()
    {
        $response = $this->post('/register', [
            'first_name' => 'Teszt',
            'last_name' => 'Kovács',
            'email' => 'teszt@roampass.hu',
            'password' => '123',
            'password_confirmation' => '123'
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_regisztracio_sikertelen_ha_a_jelszavak_nem_egyeznek()
    {
        $response = $this->post('/register', [
            'first_name' => 'Teszt',
            'last_name' => 'Kovács',
            'email' => 'teszt@roampass.hu',
            'password' => 'jelszo123',
            'password_confirmation' => 'masikjelszo'
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_jelszo_emlekezteto_sikertelen_ures_email_eseten()
    {
        $response = $this->post('/forgot-password', [
            'email' => ''
        ]);
        $response->assertSessionHasErrors('email');
    }
}