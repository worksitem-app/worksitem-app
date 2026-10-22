<?php

namespace Tests\Feature;

use Tests\TestCase;

/** Araç listesi ve arama menüsü: her aracın kısa açıklaması olmalı (yeni araç eklenince unutulmasın). */
class AracAciklamaTest extends TestCase
{
    public function test_her_aracin_aciklamasi_var(): void
    {
        foreach ((array) config('worksitem_tools.tools') as $slug => $t) {
            $d = trim((string) ($t['desc'] ?? ''));
            $this->assertNotSame('', $d, "$slug: açıklama yok");
            $this->assertLessThanOrEqual(160, mb_strlen($d), "$slug: açıklama 160 karakterden uzun");
            $this->assertGreaterThanOrEqual(40, mb_strlen($d), "$slug: açıklama çok kısa");
        }
    }

    public function test_arac_listesinde_aciklama_gorunur(): void
    {
        $this->get('/araclar')->assertOk()
            ->assertSee('Duvar ve tavan alanına göre alçı sıva torba sayısı', false)
            ->assertSee('Düzgün yayılı yük altında iki ucu mafsallı kirişin', false);
    }
}
