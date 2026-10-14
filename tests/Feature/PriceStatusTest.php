<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_fiyat_durumu_raporu_calisir_ve_arac_kaynaklarini_listeler(): void
    {
        $this->artisan('prices:status')
            ->expectsOutputToContain('prices:research-paint-web')
            ->expectsOutputToContain('Araç başına fiyat kaynağı')
            ->assertSuccessful();
    }
}
