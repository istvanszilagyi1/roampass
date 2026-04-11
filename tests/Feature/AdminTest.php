<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\ActionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            DB::connection()->getPdo()->sqliteCreateFunction('MONTH', function($date) {
                return date('m', strtotime($date));
            });
        }
    }

    private function createAdmin()
    {
        return User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@roampass.hu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function test_admin_dashboard_betolti_a_statisztikakat()
    {
        $admin = $this->createAdmin();

        Setting::updateOrCreate(['key' => 'active_sticker_color'], ['value' => '#FF0000']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas(['totalPasses', 'totalGyms', 'users']);
    }

    public function test_hirlevel_kikuldes_mukodik_es_logol()
    {
        Mail::fake();
        $admin = $this->createAdmin();
        
        User::create([
            'first_name' => 'Teszt',
            'last_name' => 'Elek',
            'email' => 'sub@test.hu',
            'password' => bcrypt('password'),
            'wants_newsletter' => true
        ]);

        $response = $this->actingAs($admin)->post(route('admin.newsletter.send'), [
            'subject' => 'Teszt Hirlevel',
            'message' => 'Ez egy teszt uzenet.'
        ]);

        $response->assertRedirect();
        Mail::assertSent(NewsletterMail::class);
        $this->assertDatabaseHas('action_logs', ['action' => 'NEWSLETTER_SENT']);
    }

    public function test_hirlevel_nem_kuldheto_feliratkozo_nelkul()
    {
        $admin = $this->createAdmin();
        User::where('wants_newsletter', true)->update(['wants_newsletter' => false]);

        $response = $this->actingAs($admin)->post(route('admin.newsletter.send'), [
            'subject' => 'Hiba Teszt',
            'message' => 'Uzenet'
        ]);

        $response->assertSessionHas('error');
    }

    public function test_rendszer_beallitasok_frissitese()
    {
        $admin = $this->createAdmin();
        
        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'student_id_upload_required' => '1'
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'student_id_upload_required',
            'value' => '1'
        ]);
    }
}