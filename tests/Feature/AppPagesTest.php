<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Hesaplayıcı olmayan uygulama sayfaları: tek başlık, header/footer, bozuk betik yok */
class AppPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [
            ['/araclar/ihale-modulu'], ['/araclar/dijital-metraj'], ['/araclar/belge-olusturucu'],
            ['/araclar/oda-planlayici'], ['/araclar/duvar-rengi-gorsellestirici'], ['/araclar/seramik-gorsellestirici'],
        ];
    }

    #[DataProvider('pages')]
    public function test_uygulama_sayfasi_duzgun(string $url): void
    {
        $html = $this->followingRedirects()->get($url)->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, '<h1'), 'tek h1');
        $this->assertSame(1, substr_count($html, 'class="wks-footer'), 'footer');
        $this->assertSame(1, substr_count(strtolower($html), '</body>'), 'betik içinde </body> kalmamalı');
    }

    public function test_belge_olusturucuda_sinirsiz_sorumsuzluk_ifadesi_yok(): void
    {
        $html = $this->get('/araclar/belge-olusturucu')->getContent();
        $this->assertStringNotContainsString('herhangi bir hukuki sorumluluk kabul edilmez', $html);
    }
}
