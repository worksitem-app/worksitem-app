<?php

namespace Tests\Feature;

use App\Models\PlannerProduct;
use App\Models\PlannerTile;
use App\Models\User;
use App\Services\Planner\CatalogSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PlannerCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_tools.tool_status.overrides.banyo-planlayici' => 'active']);
    }

    public function test_excel_sablonu_doldurulup_ice_aktarilir(): void
    {
        $book = app(CatalogSheet::class)->template();
        $p = $book->getSheetByName('Ürünler');
        $p->fromArray(['Duşakabin', 'Kare kabin 90', 'Marka A', 'DK-90', 900, 900, 2000, 800, 1200, 800, 1200, '#ffffff', '', 'Hayır', 9000, 12000, 'https://a.com/dk', 'dk.jpg', 'dk.glb'], null, 'A3');
        $p->fromArray(['Kapı', 'Geçersiz tür', 'Marka A', '', 1, 1, 1], null, 'A4');
        $t = $book->getSheetByName('Seramikler');
        $t->fromArray(['Beton Gri', 'Marka B', 'Urban', 'BG-6060', 600, 600, 'Mat', 'Zemin', 700, 900, 'https://b.com/bg', 'bg.jpg'], null, 'A3');
        $path = tempnam(sys_get_temp_dir(), 'tpl').'.xlsx';
        (new Xlsx($book))->save($path);

        $r = app(CatalogSheet::class)->import($path);
        $this->assertSame(1, $r['products']);   // örnek satır ve geçersiz satır alınmaz
        $this->assertSame(1, $r['tiles']);
        $this->assertSame(1, $r['skipped']);
        $k = PlannerProduct::query()->where('sku', 'DK-90')->firstOrFail();
        $this->assertSame(['kabin', 90, 90, 200, 80, 120], [$k->type, $k->width, $k->depth, $k->height, $k->width_min, $k->width_max]);
        $this->assertSame(9000, $k->price_min);
        $tile = PlannerTile::query()->firstOrFail();
        $this->assertSame([60, 60, 'floor'], [$tile->width_cm, $tile->height_cm, $tile->usage]);

        app(CatalogSheet::class)->import($path);   // aynı stok kodu güncellenir, çoğalmaz
        $this->assertSame(1, PlannerProduct::query()->count());
    }

    public function test_sponsor_seramigi_ve_3d_modeli_planlayiciya_gelir(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('planner/tiles/x.png', 'png');
        Storage::disk('public')->put('planner/models/m.glb', 'glb');
        $tile = PlannerTile::query()->create(['name' => 'Desen', 'brand' => 'KaroMarka', 'width_cm' => 20, 'height_cm' => 20, 'image_path' => 'planner/tiles/x.png', 'product_url' => 'https://k.com/d', 'price_m2_min' => 900]);
        PlannerTile::query()->create(['name' => 'Görselsiz', 'brand' => 'X', 'width_cm' => 30, 'height_cm' => 30]);   // dokusu yoksa gösterilmez
        PlannerProduct::query()->create(['type' => 'lavabo', 'name' => 'L', 'brand' => 'M', 'width' => 80, 'depth' => 46, 'height' => 85, 'model_path' => 'planner/models/m.glb']);

        $html = $this->get('/araclar/banyo-planlayici')->assertOk()->getContent();
        preg_match('#id="plCfg">(.*?)</script>#s', $html, $m);
        $c = json_decode($m[1], true);
        $this->assertSame('KaroMarka Desen', $c['tiles']['db-'.$tile->id]['name']);
        $this->assertSame([900, 900], $c['tiles']['db-'.$tile->id]['price']);
        $this->assertCount(1, array_filter(array_keys($c['tiles']), fn ($k) => str_starts_with($k, 'db-')));
        $this->assertStringEndsWith('planner/models/m.glb', $c['products']['lavabo'][0]['model']);
        $this->assertStringContainsString('/araclar/banyo-planlayici/render', $c['renderUrl']);

        $this->get('/planlayici/seramik/'.$tile->id)->assertRedirectContains('https://k.com/d?utm_source=worksitem');
        $this->assertSame(1, $tile->fresh()->clicks);
    }

    public function test_gercekci_gorsel_girissiz_kullaniciya_kapali_admin_tam_gorsel_alir(): void
    {
        config(['services.ai_spend_enabled' => true, 'services.gemini.api_key' => 'test-key']);
        $png = base64_encode((string) file_get_contents(public_path('images/tile-visualizer/carrara-beyaz.png')));
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['inlineData' => ['mimeType' => 'image/png', 'data' => $png]]]]]]])]);
        $scene = 'data:image/png;base64,'.$png;
        $body = ['mode' => 'plan', 'scene' => $scene, 'floor' => 'traverten-gri', 'wall' => 'carrara-beyaz', 'paint' => '#F7F7F5', 'tile_height' => 120, 'items' => 'Lavabo 80x46'];

        $this->postJson('/araclar/banyo-planlayici/render', $body)->assertStatus(401)->assertJsonPath('ai_gate', 'login');

        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->actingAs($admin)->postJson('/araclar/banyo-planlayici/render', $body)->assertOk()->assertJsonPath('preview_only', false);
        $this->assertStringStartsWith('data:image/png;base64,', $r->json('image'));
        Http::assertSent(fn ($req) => str_contains($req->body(), '120 cm from the floor') && str_contains($req->body(), 'Lavabo 80x46'));

        $this->actingAs($admin)->postJson('/araclar/banyo-planlayici/render', ['mode' => 'photo'] + $body)->assertStatus(422);   // fotoğraf modu fotoğrafsız olmaz
        $this->actingAs($admin)->post('/araclar/banyo-planlayici/render', ['mode' => 'photo', 'photo' => UploadedFile::fake()->image('b.jpg', 800, 600)] + $body, ['Accept' => 'application/json'])->assertOk();
    }

    public function test_admin_katalog_ekranlari_acilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/planner-tiles')->assertOk()->assertSee('Planlayıcı seramikleri');
        $this->actingAs($admin)->get('/admin/planner-tiles/create')->assertOk();
        $this->actingAs($admin)->get('/admin/planner-products/create')->assertOk()->assertSee('3D model');
    }
}
