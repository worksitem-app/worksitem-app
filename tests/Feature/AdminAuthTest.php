<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_olmadan_admin_panel_login_e_redirect(): void
    {
        // Filament, /admin'e erişimi /admin/login'e yönlendirir
        $response = $this->get('/admin');

        // 302 (redirect) veya 301
        $this->assertTrue(
            in_array($response->status(), [301, 302]),
            "Expected redirect, got {$response->status()}"
        );
    }

    public function test_login_olmadan_leads_listesi_login_e_redirect(): void
    {
        // Filament'te leads resource'u /admin/leads (İngilizce)
        $response = $this->get('/admin/leads');

        $this->assertTrue(
            in_array($response->status(), [301, 302]),
            "Expected redirect, got {$response->status()}"
        );
    }

    public function test_login_sayfasi_aciliyor(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_login_sayfasi_form_iceriyor(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        // Filament login formu içeriyor mu? (input tag'i ara)
        $response->assertSee('password', false);
    }
}
