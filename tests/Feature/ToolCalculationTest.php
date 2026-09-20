<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Araç Hesaplama Testleri
 *
 * 60+ hesaplayıcının en kritik olanları GET ile açılıyor mu?
 * Bu araçlar SEO ve lead magnet için kritik.
 */
class ToolCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_beton_hesaplayici_aciliyor(): void
    {
        $this->get('/araclar/beton-hesaplayici')->assertStatus(200);
    }

    public function test_boya_hesaplayici_aciliyor(): void
    {
        $this->get('/araclar/boya-hesaplayici')->assertStatus(200);
    }

    public function test_demir_hesaplayici_aciliyor(): void
    {
        $this->get('/araclar/demir-metraji-hesaplayici')->assertStatus(200);
    }

    public function test_insaat_maliyeti_hesaplama_aciliyor(): void
    {
        $this->get('/araclar/insaat-maliyeti-hesaplama')->assertStatus(200);
    }

    public function test_alci_siva_hesaplama_aciliyor(): void
    {
        $this->get('/araclar/alci-siva-hesaplama')->assertStatus(200);
    }

    public function test_havalandirma_hesaplayici_aciliyor(): void
    {
        $this->get('/araclar/havalandirma-hesaplayici')->assertStatus(200);
    }
}
