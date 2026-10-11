<?php

namespace Tests\Feature;

use App\Services\KaintaCostEngine;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KaintaCostEngineTest extends TestCase
{
    public function test_istanbul_carpani_her_yazimda_uygulanir(): void
    {
        $engine = new KaintaCostEngine();
        $base = $engine->calculate('komple', 100, 'standart', 'Ankara')['hesaplanan_maliyet_tl'];

        foreach (['İstanbul', 'istanbul', 'ISTANBUL', 'Istanbul'] as $city) {
            $this->assertEqualsWithDelta(
                $base * 1.15,
                $engine->calculate('komple', 100, 'standart', $city)['hesaplanan_maliyet_tl'],
                0.01,
                "Şehir yazımı: {$city}"
            );
        }
    }

    public function test_varsayilan_olarak_ai_api_cagrisi_yapilmaz(): void
    {
        config(['services.gemini.api_key' => 'test-key']);
        Http::fake();

        $result = (new KaintaCostEngine())->calculate('komple', 100);

        Http::assertNothingSent();
        $this->assertNull($result['ai_tavsiye']);
    }
}
