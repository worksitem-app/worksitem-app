<?php

namespace Tests\Unit;

use App\Services\Leads\LeadScoringService;
use PHPUnit\Framework\TestCase;

class LeadScoringEdgeCaseTest extends TestCase
{
    private LeadScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LeadScoringService();
    }

    public function test_bos_veri_ile_cokmez(): void
    {
        $result = $this->service->score([]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('quality', $result);
        $this->assertArrayHasKey('sales_ready', $result);
    }

    public function test_sadece_telefon_ile_dusuk_skor(): void
    {
        $result = $this->service->score([
            'phone' => '0532 000 00 00',
        ]);

        $this->assertLessThan(50, $result['score']);
        $this->assertFalse($result['sales_ready']);
    }

    public function test_buyuk_ticari_proje_yuksek_skor(): void
    {
        // "ticari" Türkçe pattern'a uyar, +10 puan alır
        $result = $this->service->score([
            'phone' => '0532 000 00 00',
            'area' => 5000,
            'project_type' => 'ticari',
            'budget_confirmed' => true,
            'explicit_request' => true,
        ]);

        // Beklenen skor: 15 + 10 + 25 + 5 + 5 + 10 = 70
        $this->assertGreaterThanOrEqual(70, $result['score']);
    }

    public function test_istanbul_yuksek_skor_alir(): void
    {
        // İstanbul, diğer illerden 5 puan fazla alır (8 vs 3)
        $istanbul = $this->service->score([
            'phone' => '0532 000 00 00',
            'city' => 'istanbul',
        ]);

        $ankara = $this->service->score([
            'phone' => '0532 000 00 00',
            'city' => 'ankara',
        ]);

        $this->assertGreaterThan($ankara['score'], $istanbul['score']);
    }

    public function test_research_timeline_negatif_puan(): void
    {
        $result = $this->service->score([
            'phone' => '0532 000 00 00',
            'timeline' => 'research',
        ]);

        // telefon (10) + research (-15) = -5 → 0'a clamp
        $this->assertSame(0, $result['score']);
        $this->assertSame('research', $result['quality']);
    }

    public function test_skor_0_100_arasinda(): void
    {
        $result = $this->service->score([
            'phone' => '0532 000 00 00',
            'area' => 10000,
            'project_type' => 'ticari fabrika',
            'budget_confirmed' => true,
            'explicit_request' => true,
            'timeline' => 'now',
            'city' => 'istanbul',
            'has_file' => true,
            'is_decision_maker' => true,
        ]);

        $this->assertGreaterThanOrEqual(0, $result['score']);
        $this->assertLessThanOrEqual(100, $result['score']);
    }
}
