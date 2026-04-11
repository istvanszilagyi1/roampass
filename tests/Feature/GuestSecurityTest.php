<?php

namespace Tests\Feature;

use Tests\TestCase;

class GuestSecurityTest extends TestCase
{
    public function test_vendeg_nem_ferhet_hozza_a_profilhoz()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_profilt_modositani()
    {
        $response = $this->post('/profile', []);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_jelszot_modositani()
    {
        $response = $this->post('/profile/update-password', []);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_ferhet_hozza_a_sajat_berletekhez()
    {
        $response = $this->get('/my-passes'); 
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_berletet_vasarolni()
    {
        $response = $this->post('/buy-pass', []);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_ferhet_hozza_a_szkenner_vezerlopulthoz()
    {
        $response = $this->get('/scanner/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_qr_kodot_beolvasni()
    {
        $response = $this->post('/scanner/scan', ['token' => '123']);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_beolvasast_visszavonni()
    {
        $response = $this->post('/scanner/cancel', ['scan_id' => '1']);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_ferhet_hozza_az_admin_panelhez()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_ferhet_hozza_az_admin_felhasznalokhoz()
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_szamlat_generalni()
    {
        $response = $this->post('/admin/gyms/1/generate-invoice', []);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_felhasznalot_torolni()
    {
        $response = $this->delete('/admin/user/1');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_manualis_diakigazolvanyt_jovahagyni()
    {
        $response = $this->post('/admin/user/1/verify-student');
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_tud_hirlevel_beallitast_modositani()
    {
        $response = $this->post('/profile/newsletter', []);
        $response->assertRedirect('/login');
    }

    public function test_vendeg_nem_nyithat_meg_api_vegpontot_kozvetlenul()
    {
        $response = $this->getJson('/scanner/dashboard');
        $response->assertStatus(401);
    }
}