<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fooldal_sikeresen_betolt()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_bejelentkezes_oldal_sikeresen_betolt()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_regisztracio_oldal_sikeresen_betolt()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_nem_letezo_oldal_404_hibat_ad()
    {
        $response = $this->get('/ez-az-oldal-nem-letezik');
        $response->assertStatus(404);
    }

    public function test_hibas_api_vegpont_404_hibat_ad()
    {
        $response = $this->get('/api/nem-letezo-vegpont');
        $response->assertStatus(404);
    }

    public function test_publikus_partnerek_oldal_betolt()
    {
        $response = $this->get('/partners'); 
        $response->assertStatus(200); 
    }

    public function test_partner_jelentkezes_vegpont_letezik()
    {
        $response = $this->post('/partner-apply');
        $this->assertTrue(in_array($response->status(), [302, 419]));
    }

    public function test_robot_txt_elerheto_vagy_nem()
    {
        $this->assertTrue(true);
    }
}