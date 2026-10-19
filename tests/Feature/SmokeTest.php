<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke Test — Kritik sayfalar 200 dönüyor mu?
 *
 * Deployment sonrası "her şey ayakta mı?" kontrolü.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_ana_sayfa_200_donuyor(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_araclar_sayfasi_200_donuyor(): void
    {
        $this->get('/araclar')->assertStatus(200);
    }

    public function test_kvkk_sayfasi_200_donuyor(): void
    {
        $this->get('/kvkk-aydinlatma-metni')->assertRedirect('/legal/kvkk-aydinlatma');
    }

    public function test_admin_login_200_donuyor(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_olmayan_sayfa_404_donuyor(): void
    {
        // Ana sayfaya toplu 301 = Google'da soft 404. Bilinen eski adresler routes/redirects.php'de.
        $this->get('/bu-sayfa-yok-12345')->assertStatus(404);
    }
}
