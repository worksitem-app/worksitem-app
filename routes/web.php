<?php

/*
|--------------------------------------------------------------------------
| WORKSITEM — Web Rotaları
|--------------------------------------------------------------------------
| Bölümler:
|   1. Ana sayfa & kurumsal / yasal sayfalar
|   2. Maliyet hesaplama sihirbazı (projects)
|   3. Fiyat motoru endpoint'leri (Zero-Trust hesaplama)
|   4. Lead toplama
|   5. AI tasarım
|   6. Araçlar (/araclar/*)
|   7. Poz / SEO / Public JSON API
|   8. Admin (auth + admin)
|   9. 301 yönlendirmeleri (routes/redirects.php) + fallback
|
| Throttle profilleri AppServiceProvider::configureRateLimiters() içinde:
|   ai (5/dk + 30/gün), ai-poll (90/dk), public-write (20/dk), public-read (60/dk)
|
| KURAL: Aynı URI iki kez tanımlanmaz. Sonradan tanımlanan öncekini sessizce
| ezer — eski dosyada 20+ ölü rota bu yüzden oluşmuştu.
*/

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRayicController;
use App\Http\Controllers\AiDesignController;
use App\Http\Controllers\ConTech;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PozPublicController;
use App\Http\Controllers\Pricing;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TileVisualizerController;
use App\Http\Controllers\ToolController;
use App\Services\Pricing\LiveMarketTickerService;
use App\Services\ToolPriceService;
use Illuminate\Support\Facades\Route;

/* ─────────────────────────────────────────────────────────────────────
 | 1. ANA SAYFA & KURUMSAL / YASAL
 * ───────────────────────────────────────────────────────────────────── */

Route::get('/', HomeController::class)->name('home');

Route::view('/hakkimizda', 'hakkimizda');
Route::view('/vizyon', 'vizyon');
Route::view('/ekip', 'ekip');
// Eski (v1) aydınlatma metni → tek güncel metne kalıcı yönlendirme
Route::permanentRedirect('/kvkk-aydinlatma-metni', '/legal/kvkk-aydinlatma')->name('kvkk.aydinlatma');

// KVKK 2026/347 uyumlu yasal sayfalar
Route::prefix('legal')->name('legal.')->group(function () {
    Route::view('/kvkk-aydinlatma', 'legal.kvkk-aydinlatma')->name('kvkk_aydinlatma');
    Route::view('/acik-riza', 'legal.acik-riza')->name('acik_riza');
    Route::view('/cerez-politikasi', 'legal.cerez-politikasi')->name('cerez_politikasi');
    Route::view('/gizlilik-politikasi', 'legal.gizlilik-politikasi')->name('gizlilik_politikasi');
    Route::view('/kvkk-basvuru', 'legal.kvkk-basvuru')->name('kvkk_basvuru');
    Route::view('/kullanim-kosullari', 'legal.kullanim-kosullari')->name('kullanim_kosullari');
});

/* ─────────────────────────────────────────────────────────────────────
 | 2. MALİYET HESAPLAMA SİHİRBAZI
 |    {project} artık sıralı ID değil, tahmin edilemez ULID (public_token).
 * ───────────────────────────────────────────────────────────────────── */

Route::view('/maliyet-hesapla', 'maliyet-hesapla')->name('maliyet_hesapla');
Route::view('/maliyet-hesapla/basarili', 'thanks')->name('lead.thanks');

Route::controller(ProjectController::class)->prefix('maliyet-hesapla')->group(function () {
    Route::middleware('throttle:public-write')->group(function () {
        Route::post('/', 'store')->name('maliyet_hesapla.store');
        Route::post('/kaydet', 'store')->name('projects.store');
        Route::post('/baslat/{projectType:slug}', 'start')->name('projects.start');
        Route::post('/proje/{project}/cevaplar', 'saveAnswers')->name('projects.answers.save');
        Route::post('/proje/{project}/iletisim', 'saveLead')->name('projects.lead.save');
        Route::get('/proje/{project}/pdf', 'downloadPdf')->name('projects.pdf');
    });

    Route::middleware('throttle:public-read')->group(function () {
        Route::get('/proje/{project}', 'questions')->name('projects.questions');
        Route::get('/proje/{project}/ozet', 'summary')->name('projects.summary');
        Route::get('/proje/{project}/sonuc', 'calculate')->name('projects.calculate');
        Route::get('/proje/{project}/iletisim', 'lead')->name('projects.lead');
    });

    // AI harcama kilidi (AI_SPEND_ENABLED) kapalıyken 503 döner; throttle hakkı tüketilmez
    Route::middleware(['ai.spend', 'throttle:ai'])->group(function () {
        Route::post('/guest-render', 'guestRender')->name('projects.guest.render');
        Route::post('/proje/{project}/render', 'generateRender')->name('projects.render');
    });
});

/* ─────────────────────────────────────────────────────────────────────
 | 3. FİYAT MOTORU (Zero-Trust: fiyat sunucuda hesaplanır, snapshot imzalanır)
 * ───────────────────────────────────────────────────────────────────── */

Route::middleware('throttle:public-write')->prefix('hesaplama')->group(function () {
    Route::post('/yapi-maliyeti', [Pricing\BuildingEstimateController::class, 'calculate'])->name('pricing.building.calculate');
    Route::post('/tadilat-maliyeti', [Pricing\RenovationEstimateController::class, 'calculate'])->name('pricing.renovation.calculate');
    Route::post('/seramik-maliyeti', [Pricing\TileEstimateController::class, 'calculate'])->name('pricing.tile.calculate');
});

/* ─────────────────────────────────────────────────────────────────────
 | 4. LEAD TOPLAMA (LeadController ayrıca IP başına kendi limitini uygular)
 * ───────────────────────────────────────────────────────────────────── */

Route::middleware('throttle:public-write')->group(function () {
    Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');
    Route::post('/lead-kaydet', [LeadController::class, 'store'])->name('lead.store');
    Route::post('/maliyet-hesapla/lead', [LeadController::class, 'store'])->name('maliyet_hesapla.lead');
});

/* ─────────────────────────────────────────────────────────────────────
 | 5. AI TASARIM
 * ───────────────────────────────────────────────────────────────────── */

Route::controller(AiDesignController::class)->prefix('ai-tasarim/ic-mekan')->name('ai.interior')->group(function () {
    Route::get('/', 'interiorIndex');
    Route::post('/start', 'startGeneration')->middleware(['ai.spend', 'throttle:ai'])->name('.start');
    Route::get('/status/{id}', 'checkStatus')->middleware('throttle:ai-poll')->name('.status');
    Route::post('/estimate/{id}', 'estimate')->middleware('throttle:public-write')->name('.estimate');
    Route::get('/quota', 'getQuota')->middleware('throttle:public-read')->name('.quota');
});

/* ─────────────────────────────────────────────────────────────────────
 | 6. ARAÇLAR (/araclar/*)
 |    Durum (active/review/soon) config/worksitem_tools.php → ToolQaGate middleware
 * ───────────────────────────────────────────────────────────────────── */

Route::prefix('araclar')->name('tools.')->group(function () {
    Route::view('/', 'tools.index')->name('index');

    // ── Controller tabanlı (veri hazırlayan) araçlar ──
    Route::controller(ToolController::class)->group(function () {
        Route::get('/boya-hesaplayici', 'paintCalculator')->name('paint');
        Route::get('/duvar-rengi-gorsellestirici', 'paintVisualizer')->name('paint_visualizer');
        Route::get('/seramik-maliyet-hesaplayici', 'tileCalculator')->name('tile_calculator');
        Route::get('/demir-metraji-hesaplayici', 'rebarCalculator')->name('rebar');
        Route::get('/saha-betonu-hesaplayici', 'concreteCalculator')->name('concrete');

        Route::get('/sap-hesaplayici', 'screedCalculator')->name('screed');
        Route::get('/oda-planlayici', 'roomPlanner')->name('room');
        Route::get('/belge-olusturucu', 'documentBuilder')->name('document');
        Route::get('/pvc-dograma-hesaplayici', 'pvcCalculator')->name('pvc');
        Route::get('/duvar-maliyet-hesaplayici', 'wallCalculator')->name('wall');
        Route::get('/radyator-petek-hesaplayici', 'radiatorCalculator')->name('radiator');
        Route::get('/donati-tahvil-hesaplayici', 'rebarEquivalentCalculator')->name('rebar.equivalent');
        Route::get('/tahmini-bina-maliyeti', 'buildingCostCalculator')->name('building_cost');
        Route::get('/bahce-duvari-maliyeti', 'gardenWallCalculator')->name('garden_wall');
        Route::get('/deprem-izolator-maliyeti', 'isolatorCalculator')->name('isolator');
        Route::get('/insaat-muhendisi-maas-hesabi', 'salaryCalculator')->name('salary');
        Route::get('/yagmur-borusu-hesabi', 'rainpipeCalculator')->name('rainpipe');
        Route::get('/azami-konut-kredisi', 'mortgageCalculator')->name('mortgage');
        Route::get('/birim-fiyat-analiz', 'unitPriceCalculator')->name('unit_price');
        Route::get('/merdiven-basamak-hesabi', 'stairsCalculator')->name('stairs');
        Route::get('/kalip-plywood-maliyeti', 'formworkCalculator')->name('formwork');
        Route::get('/jenerator-hesaplama', 'generatorCalculator')->name('generator');
        Route::get('/alci-siva-hesaplama', 'plasterCalculator')->name('plaster');
        Route::get('/enerji-tuketim-hesaplama', 'energyCalculator')->name('energy');
        Route::get('/kara-siva-hesaplama', 'roughPlasterCalculator')->name('rough_plaster');

        // İleri mühendislik ve niş araçlar
        Route::get('/yapi-dinamigi', 'structuralDynamics')->name('structural_dynamics');
        Route::get('/kolon-tasima-kapasitesi', 'columnCapacity')->name('column_capacity');
        Route::get('/basit-kiris-statigi', 'simpleBeam')->name('simple_beam');
        Route::get('/bina-agirlik-hesabi', 'buildingWeight')->name('building_weight');
        Route::get('/karot-dayanim', 'coreStrength')->name('core_strength');
        Route::get('/kolon-filiz-boyu', 'columnStarterBar')->name('column_starter_bar');
        Route::get('/bombeli-tank-hacmi', 'domedTank')->name('domed_tank');
        Route::get('/pompa-gucu-hesabi', 'pumpPower')->name('pump_power');
        Route::get('/siginak-yonetmeligi', 'shelterRegulation')->name('shelter_regulation');
        Route::get('/kesim-optimizasyonu', 'cuttingOptimization')->name('cutting_optimization');
        Route::get('/malzeme-agirligi', 'materialWeight')->name('material_weight');
        Route::get('/lojistik-maliyeti', 'logisticsCost')->name('logistics_cost');
        Route::get('/is-makinesi-maliyeti', 'machineCost')->name('machine_cost');
        Route::get('/ihale-modulu', 'tenderModule')->name('tender');

        Route::post('/belge-olusturucu/indir', 'generateDocument')
            ->middleware('throttle:public-write')->name('document.generate');
        Route::post('/oda-planlayici/generate', 'generateRoomDesign')
            ->middleware(['ai.spend', 'throttle:ai'])->name('room.generate');
    });

    // ── ConTech hesaplayıcıları (sunucu tarafı fiyat bağlama) ──
    Route::get('/alcipan-hesaplayici', [ConTech\DrywallCalculatorController::class, 'index'])->name('alcipan_calculator');
    Route::get('/asma-tavan-hesaplayici', [ConTech\CeilingCalculatorController::class, 'index'])->name('ceiling_calculator');
    Route::get('/su-yalitimi-hesaplayici', [ConTech\WaterproofingCalculatorController::class, 'index'])->name('waterproofing_calculator');
    Route::get('/epoksi-zemin-hesaplayici', [ConTech\EpoxyFloorCalculatorController::class, 'index'])->name('epoxy_floor_calculator');
    Route::get('/sandvic-panel-hesaplayici', [ConTech\SandwichPanelCalculatorController::class, 'index'])->name('sandwich_panel_calculator');
    Route::get('/yukseltilmis-doseme-hesaplayici', [ConTech\RaisedFloorCalculatorController::class, 'index'])->name('raised_floor_calculator');
    Route::get('/havalandirma-hesaplayici', [ConTech\VentilationCalculatorController::class, 'index'])->name('ventilation_calculator');

    Route::get('/otomatik-kesif-fiyatlandirma', [ConTech\AutoBoqController::class, 'index'])->name('auto_boq');
    Route::post('/otomatik-kesif-fiyatlandirma/hesapla', [ConTech\AutoBoqController::class, 'calculate'])
        ->middleware(['ai.spend', 'throttle:ai'])->name('auto_boq.calc');

    // Seramik görselleştirici (MVP) — render çıktısı oturuma bağlı token ile indirilir
    Route::get('/seramik-gorsellestirici', [TileVisualizerController::class, 'index'])->name('tile_visualizer');

    Route::get('/beton-hesaplayici', function (ToolPriceService $prices) {
        return view('tools.beton-calculator', ['toolPrices' => $prices->forTool('beton-hesaplayici')]);
    })->name('beton_calculator');

    // ── Statik (yalnızca view) araçlar ──
    Route::view('/parke-maliyet-hesaplayici', 'tools.parquet-calculator')->name('parquet_calculator');
    Route::view('/mutfak-maliyet-hesaplayici', 'tools.mutfak-calculator')->name('mutfak_calculator');
    Route::view('/banyo-maliyet-hesaplayici', 'tools.banyo-calculator')->name('banyo_calculator');
    Route::view('/wc-maliyet-hesabi', 'tools.wc-calculator')->name('wc_calculator');
    Route::view('/cimento-kum-hesaplama', 'tools.cement-sand-calculator')->name('cement_calculator');
    Route::view('/metrekup-hacim-hesaplama', 'tools.volume-calculator')->name('volume_calculator');
    Route::view('/hafriyat-kazi-hesabi', 'tools.excavation-calculator')->name('excavation_calculator');
    Route::view('/iskele-metraji-maliyeti', 'tools.iskele-calculator')->name('iskele_calculator');
    Route::view('/iscilik-maliyeti', 'tools.iscilik-calculator')->name('iscilik_calculator');
    Route::view('/sgk-asgari-iscilik', 'tools.sgk-calculator')->name('sgk_calculator');
    Route::view('/klima-btu-hesaplayici', 'tools.klima-calculator')->name('klima_calculator');
    Route::view('/su-tesisat-hesaplama', 'tools.su-tesisat-calculator')->name('sutesisat_calculator');
    Route::view('/kablo-kesiti-hesabi', 'tools.kablo-calculator')->name('kablo_calculator');
    Route::view('/asansor-zorunlulugu', 'tools.asansor-calculator')->name('asansor_calculator');
    Route::view('/taks-kaks-hesaplayici', 'tools.taks-kaks-calculator')->name('taks_kaks_calculator');
    Route::view('/pafta-olcu-hesaplayici', 'tools.pafta-calculator')->name('pafta_calculator');
    Route::view('/statik-proje-bedeli', 'tools.statik-calculator')->name('statik_calculator');
    Route::view('/kat-karsiligi-analiz', 'tools.kat-karsiligi')->name('kat_karsiligi');
    Route::view('/gunes-paneli-hesaplama', 'tools.ges-calculator')->name('ges_calculator');
    Route::view('/daire-kira-degeri', 'tools.kira-calculator')->name('kira_calculator');
    Route::view('/cati-maliyet-hesaplayici', 'tools.cati-calculator')->name('cati_calculator');
    Route::view('/mantolama-maliyeti', 'tools.yalitim-calculator')->name('yalitim_calculator');
    Route::view('/zemin-kaplama', 'tools.zemin-calculator')->name('zemin_calculator');
    Route::view('/duvar-orgu', 'tools.duvar-calculator')->name('duvar_calculator');
    Route::view('/dijital-metraj', 'tools.web-takeoff')->name('digital_takeoff');
    Route::view('/insaat-maliyeti-hesaplama', 'tools.insaat-maliyeti')->name('insaat_maliyeti');
});

// Kök seviyede eski görselleştirici URL'si (kanonik: /araclar/duvar-rengi-gorsellestirici)
Route::get('/duvar-rengi-gorsellestirici', [ToolController::class, 'paintVisualizer']);

/* ─────────────────────────────────────────────────────────────────────
 | 7. POZ / SEO / PUBLIC JSON
 * ───────────────────────────────────────────────────────────────────── */

Route::get('/pozlar', [PozPublicController::class, 'index'])->name('poz.index');
Route::redirect('/poz', '/pozlar', 301);
Route::get('/poz/{pozNo}', [PozPublicController::class, 'show'])->name('poz.show')->where('pozNo', '.+');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::post('/api/seramik-gorsellestirici/render', [TileVisualizerController::class, 'render'])
    ->middleware(['ai.spend', 'throttle:ai'])->name('api.tile_visualizer.render');

Route::middleware('throttle:public-read')->group(function () {
    Route::get('/api/pozlar/search', [PozPublicController::class, 'search'])->name('api.poz.search');
    Route::get('/api/seramik-gorsellestirici/download/{token}', [TileVisualizerController::class, 'download'])
        ->where('token', '[a-f0-9]{32}')->name('api.tile_visualizer.download');
    Route::get('/api/live-market', fn (LiveMarketTickerService $service) => response()->json($service->data()))
        ->name('api.live-market');
});

/* ─────────────────────────────────────────────────────────────────────
 | 8. ADMIN (Filament dışı eski admin ekranları)
 * ───────────────────────────────────────────────────────────────────── */

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Birim fiyat & CRM
    Route::get('/fiyatlar', [AdminController::class, 'index'])->name('prices.index');
    Route::post('/fiyatlar/guncelle/{id}', [AdminController::class, 'update'])->name('prices.update');
    Route::get('/talepler', [AdminController::class, 'leads'])->name('leads.index');
    Route::get('/talepler/{id}/teklif', [AdminController::class, 'generateProposal'])->name('leads.proposal');

    // Poz CSV import
    Route::get('/poz-import', [AdminController::class, 'pozImportForm'])->name('poz.import.form');
    Route::post('/poz-import', [AdminController::class, 'pozImportUpload'])->name('poz.import.upload');

    // AI PDF fiyat güncelleme
    Route::get('/ai-fiyat-guncelle', [ConTech\AdminPriceController::class, 'index'])->name('ai_prices.index');
    Route::post('/ai-fiyat-guncelle', [ConTech\AdminPriceController::class, 'upload'])->name('prices.upload');

    // Rayiç PDF → AI analiz → önizleme → onay
    // (Controller + view'lar vardı ama rotalar hiç kaydedilmemişti → modül erişilemezdi.)
    Route::prefix('rayic')->name('rayic.')->controller(AdminRayicController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/yeni', 'create')->name('create');
        Route::post('/analiz', 'analyze')->name('analyze');
        Route::get('/{id}', 'preview')->whereNumber('id')->name('preview');
        Route::post('/{id}/onayla', 'approve')->whereNumber('id')->name('approve');
    });
});

/* ─────────────────────────────────────────────────────────────────────
 | 9. YÖNLENDİRMELER + FALLBACK
 * ───────────────────────────────────────────────────────────────────── */

require __DIR__.'/redirects.php';

if (class_exists(\App\Http\Controllers\Auth\PublicAuthController::class) && file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

/*
| Bilinmeyen URL → ana sayfaya 301 (eski insaatvtadilat.com linkleri için).
| UYARI: Google toplu ana sayfa yönlendirmelerini "soft 404" sayar. Uzun vadede
| bilinen eski URL'leri redirects.php'ye tek tek ekleyip burada 404 dönülmeli.
*/
Route::fallback(fn () => redirect('/', 301));
