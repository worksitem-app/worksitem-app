<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HowItWorksTest extends TestCase
{
    use RefreshDatabase;

    public function test_her_arac_turunde_nasil_calisir_bandi(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertOk()->assertSee('Boya Hesaplayıcı: 4 adımda sonuç')->assertSee('Güncel fiyatlarla hesaplayın')
            ->assertSee('Neden Worksitem?')->assertSee('Usta veya firma mısınız?');
        $this->get('/araclar/kolon-filiz-boyu')->assertOk()->assertSee('Mühendise danışın')->assertSee('statik proje yerine geçmez');
        $this->get('/araclar/banyo-planlayici')->assertOk()->assertSee('2D / 3D görün');
        $this->get('/araclar/beton-hesaplayici')->assertOk()->assertSee('4 adımda sonuç');
        $html = $this->get('/araclar/mantolama-maliyeti')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'class="ws-how"'));
        $this->assertLessThan(strpos($html, 'class="ws-bc"'), strpos($html, 'class="ws-how"'));   // markalar bandının üstünde
    }

    public function test_neden_worksitem_uc_kitle(): void
    {
        $this->get('/')->assertOk()->assertSee('Hesaplayan kazanır, iş yapan kazanır, ürün üreten kazanır.')
            ->assertSee('Ücretsiz firma kaydı')->assertSee('Sponsorluk teklifi iste');
        $this->get('/firma-uyeligi')->assertOk()->assertSee('Bütçesini bilen müşteri, doğru firmayı arıyor.')->assertSee('Komisyon yok');
        $this->get('/markalar-icin')->assertOk()->assertSee('Ürününüz, malzeme seçilmeden hemen önce görünsün.');
        $this->get('/firma-paneli/giris')->assertOk()->assertSee('Hazırlıklı müşteri');
    }
}
