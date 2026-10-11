<?php

namespace Tests\Feature;

use App\Models\AiGeneration;
use App\Models\Project;
use App\Models\UnitPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CTO audit (2026-10) bulgularının geri gelmemesi için regresyon testleri.
 */
class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_hesaplayici_sayfasi_tum_fiyat_listesini_html_e_basmaz(): void
    {
        UnitPrice::query()->create([
            'key' => 'gizli_ic_fiyat_test_m2',
            'name' => 'Gizli test fiyatı',
            'category' => 'test',
            'unit' => 'm2',
            'price' => 12345,
            'price_pool' => 'market',
            'is_active' => true,
        ]);

        $this->get('/araclar/pompa-gucu-hesabi')
            ->assertOk()
            ->assertDontSee('gizli_ic_fiyat_test_m2');
    }

    public function test_ai_uretimi_sirali_id_ile_okunamaz(): void
    {
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => 'Modern',
            'input_image' => 'ai_inputs/test.jpg',
            'status' => 'completed',
            'output_image' => 'https://example.com/out.png',
        ]);

        $this->assertNotEmpty($generation->public_id);

        $this->getJson('/ai-tasarim/ic-mekan/status/'.$generation->id)->assertNotFound();

        $this->getJson('/ai-tasarim/ic-mekan/status/'.$generation->public_id)
            ->assertOk()
            ->assertJsonPath('generation_id', $generation->public_id);
    }

    public function test_proje_url_leri_tahmin_edilemez_token_kullanir(): void
    {
        $typeId = \DB::table('project_types')->insertGetId([
            'name' => 'Tadilat', 'slug' => 'tadilat-test', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $project = Project::create(['project_type_id' => $typeId, 'status' => 'draft']);

        $this->assertSame(26, strlen($project->public_token));
        $this->assertStringContainsString($project->public_token, route('projects.summary', $project));

        $this->get('/maliyet-hesapla/proje/'.$project->id.'/ozet')->assertNotFound();
        $this->get('/maliyet-hesapla/proje/'.$project->public_token.'/ozet')->assertOk();
    }

    public function test_proje_mass_assignment_ile_token_ezilemez(): void
    {
        $typeId = \DB::table('project_types')->insertGetId([
            'name' => 'X', 'slug' => 'x-test', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $project = Project::create([
            'project_type_id' => $typeId,
            'status' => 'draft',
            'public_token' => 'SALDIRGAN',
            'id' => 999999,
        ]);

        $this->assertNotSame('SALDIRGAN', $project->public_token);
        $this->assertNotSame(999999, $project->id);
    }

    public function test_ai_endpointleri_rate_limitli(): void
    {
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan', 'style' => 'Modern',
            'input_image' => 'x.jpg', 'status' => 'failed',
        ]);

        $codes = collect(range(1, 95))
            ->map(fn () => $this->getJson('/ai-tasarim/ic-mekan/status/'.$generation->public_id)->status());

        $this->assertTrue($codes->contains(429), 'ai-poll limiti devrede değil.');
    }
}
