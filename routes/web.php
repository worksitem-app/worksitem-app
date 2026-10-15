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

// Sponsor (marka) alanları: tıklama takibi ve marka başvuru sayfası
Route::get('/sp/{sponsor}', [\App\Http\Controllers\SponsorController::class, 'go'])
    ->whereNumber('sponsor')->middleware('throttle:public-read')->name('sponsor.go');
// Telefonla giriş (SMS kodu) + AI kullanım hakkı (günde N ücretsiz, sonrası kredi)
Route::prefix('ai-hesap')->name('ai_account.')->controller(\App\Http\Controllers\AiAccountController::class)->group(function () {
    Route::get('/durum', 'status')->middleware('throttle:public-read')->name('status');
    Route::post('/kod', 'sendCode')->middleware('throttle:public-write')->name('code');
    Route::post('/giris', 'login')->middleware('throttle:public-write')->name('login');
    Route::post('/cikis', 'logout')->middleware('throttle:public-write')->name('logout');
});

Route::get('/planlayici/urun/{product}', [\App\Http\Controllers\Tools\BathPlannerController::class, 'product'])
    ->whereNumber('product')->middleware('throttle:public-read')->name('planner.product');
Route::get('/planlayici/seramik/{tile}', [\App\Http\Controllers\Tools\BathPlannerController::class, 'tile'])
    ->whereNumber('tile')->middleware('throttle:public-read')->name('planner.tile');
Route::get('/markalar-icin', [\App\Http\Controllers\SponsorController::class, 'page'])->name('sponsor.page');
Route::post('/markalar-icin', [\App\Http\Controllers\SponsorController::class, 'apply'])
    ->middleware('throttle:public-write')->name('sponsor.apply');

// Firma üyeliği (kayıt herkese açık; rehber FIRMS_DIRECTORY_ENABLED ile açılır)
Route::controller(\App\Http\Controllers\FirmController::class)->group(function () {
    Route::get('/firma-uyeligi', 'page')->name('firms.page');
    Route::post('/firma-uyeligi/kod', 'sendCode')->middleware('throttle:public-write')->name('firms.code');
    Route::post('/firma-uyeligi', 'register')->middleware('throttle:public-write')->name('firms.register');
    Route::get('/firma-uyeligi/{firm}/one-cikan', 'pro')->middleware('signed')->name('firms.pro');
    Route::get('/firmalar', 'index')->middleware('throttle:public-read')->name('firms.index');
    Route::get('/firmalar/{firm}', 'show')->middleware('throttle:public-read')->name('firms.show');
    Route::get('/firmalar/{firm}/iletisim/{via}', 'contact')->whereIn('via', ['ara', 'whatsapp'])
        ->middleware('throttle:public-write')->name('firms.contact');
});
Route::view('/legal/firma-uyelik-kosullari', 'legal.firma-uyelik-kosullari')->name('legal.firma_uyelik');
Route::view('/legal/teklif-alma-kosullari', 'legal.teklif-alma-kosullari')->name('legal.teklif_alma');
Route::post('/ab', [\App\Http\Controllers\AbController::class, 'store'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->middleware('throttle:public-write')->name('ab.track');

// Teklif pazaryeri: kullanıcı talebi ve teklifleri (bağlantıdaki gizli anahtarla), firma paneli (SMS ile giriş)
Route::controller(\App\Http\Controllers\Market\RequestController::class)->group(function () {
    Route::get('/teklif-al', 'create')->middleware('throttle:public-read')->name('market.request.create');
    Route::post('/teklif-al/kod', 'sendCode')->middleware('throttle:public-write')->name('market.request.code');
    Route::post('/teklif-al', 'store')->middleware('throttle:public-write')->name('market.request.store');
    Route::get('/teklif/{job}', 'show')->middleware('throttle:public-read')->name('market.request.show');
    Route::post('/teklif/{job}/kabul/{offer}', 'accept')->middleware('throttle:public-write')->name('market.request.accept');
    Route::post('/teklif/{job}/kapat', 'close')->middleware('throttle:public-write')->name('market.request.close');
});
Route::prefix('firma-paneli')->controller(\App\Http\Controllers\Market\FirmPanelController::class)->group(function () {
    Route::get('/giris', 'login')->name('market.panel.login');
    Route::post('/giris/kod', 'sendCode')->middleware('throttle:public-write')->name('market.panel.code');
    Route::post('/giris', 'verify')->middleware('throttle:public-write')->name('market.panel.verify');
    Route::post('/cikis', 'logout')->name('market.panel.logout');
    Route::get('/', 'dashboard')->name('market.panel');
    Route::post('/davetler', 'toggle')->name('market.panel.toggle');
    Route::get('/is/{job}', 'job')->name('market.panel.job');
    Route::post('/is/{job}/teklif', 'offer')->middleware('throttle:public-write')->name('market.panel.offer');
    Route::post('/is/{job}/ilgilenmiyorum', 'decline')->name('market.panel.decline');
    Route::post('/is/{job}/geri-cek', 'withdraw')->name('market.panel.withdraw');
    Route::post('/is/{job}/ode', 'pay')->middleware('throttle:public-write')->name('market.panel.pay');
    Route::post('/is/{job}/onayla', 'confirm')->middleware('throttle:public-write')->name('market.panel.confirm');
    Route::get('/hizmet-bilgileri', 'info')->name('market.panel.info');
    Route::post('/hizmet-bilgileri', 'saveInfo')->middleware('throttle:public-write')->name('market.panel.info.save');
});

// Yerinde keşif: teşekkür sayfasında kabul → ödeme/iletişim sayfası (oturuma bağlı, indekslenmez)
Route::post('/kesif/kabul', [\App\Http\Controllers\InspectionController::class, 'accept'])
    ->middleware('throttle:public-write')->name('kesif.accept');
Route::get('/kesif/odeme', [\App\Http\Controllers\InspectionController::class, 'payment'])->name('kesif.payment');
Route::post('/kesif/odeme/kart', [\App\Http\Controllers\InspectionController::class, 'iyzicoStart'])
    ->middleware('throttle:public-write')->name('kesif.iyzico.start');
// iyzico dönüşü: oturum/CSRF yok (çapraz site POST); sonuç sunucu tarafında iyzico'dan doğrulanır
Route::post('/kesif/odeme/iyzico/sonuc', [\App\Http\Controllers\InspectionController::class, 'iyzicoCallback'])
    ->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class])
    ->middleware('throttle:public-write')->name('kesif.iyzico.callback');
// Online satış: yapay zekâ kredisi ve ücretli Pro rapor (iyzico)
Route::post('/odeme/kredi', [\App\Http\Controllers\OrderController::class, 'buyCredits'])
    ->middleware('throttle:public-write')->name('odeme.kredi');
Route::post('/odeme/iyzico/sonuc', [\App\Http\Controllers\OrderController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class])
    ->middleware('throttle:public-write')->name('odeme.iyzico.callback');
Route::get('/odeme/sonuc/{order}', [\App\Http\Controllers\OrderController::class, 'result'])
    ->middleware(['signed', 'throttle:public-read'])->name('odeme.sonuc');
Route::get('/kesif/odeme/sonuc/{lead}', [\App\Http\Controllers\InspectionController::class, 'result'])
    ->middleware('signed')->name('kesif.result');
Route::get('/kesif/sozlesme.pdf', [\App\Http\Controllers\InspectionController::class, 'contractPdf'])
    ->middleware('throttle:public-read')->name('kesif.contract_pdf');
Route::get('/legal/kesif-on-bilgilendirme-formu', [\App\Http\Controllers\InspectionController::class, 'document'])
    ->defaults('doc', 'obf')->name('legal.kesif_obf');
Route::get('/legal/kesif-mesafeli-hizmet-sozlesmesi', [\App\Http\Controllers\InspectionController::class, 'document'])
    ->defaults('doc', 'mhs')->name('legal.kesif_mhs');

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
    Route::middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->group(function () {
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
    Route::post('/start', 'startGeneration')->middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->name('.start');
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

    // ── Hesap motoru (banyo tarzı): kalem dökümü, canlı hesap, Pro rapor ──
    // Araç listesi: config/worksitem_estimators.php (eski sayfalar: ToolController@paintCalculator / tileCalculator)
    $kit = \App\Http\Controllers\Kit\KitController::class;
    $kitPro = \App\Http\Controllers\Kit\KitProReportController::class;
    foreach (['boya-hesaplayici' => 'paint', 'seramik-maliyet-hesaplayici' => 'tile_calculator', 'mutfak-maliyet-hesaplayici' => 'mutfak_calculator',
        'wc-maliyet-hesabi' => 'wc_calculator', 'parke-maliyet-hesaplayici' => 'parquet_calculator', 'zemin-kaplama' => 'zemin_calculator',
        'mantolama-maliyeti' => 'yalitim_calculator', 'pvc-dograma-hesaplayici' => 'pvc',
        'sap-hesaplayici' => 'screed', 'kara-siva-hesaplama' => 'rough_plaster', 'alci-siva-hesaplama' => 'plaster', 'duvar-maliyet-hesaplayici' => 'wall', 'bahce-duvari-maliyeti' => 'garden_wall', 'kalip-plywood-maliyeti' => 'formwork', 'saha-betonu-hesaplayici' => 'concrete', 'duvar-orgu' => 'duvar_calculator', 'iskele-metraji-maliyeti' => 'iskele_calculator', 'hafriyat-kazi-hesabi' => 'excavation_calculator',
        'alcipan-hesaplayici' => 'alcipan_calculator', 'asma-tavan-hesaplayici' => 'ceiling_calculator', 'su-yalitimi-hesaplayici' => 'waterproofing_calculator', 'epoksi-zemin-hesaplayici' => 'epoxy_floor_calculator', 'sandvic-panel-hesaplayici' => 'sandwich_panel_calculator', 'radyator-petek-hesaplayici' => 'radiator', 'gunes-paneli-hesaplama' => 'ges_calculator', 'deprem-izolator-maliyeti' => 'isolator', 'tahmini-bina-maliyeti' => 'building_cost', 'statik-proje-bedeli' => 'statik_calculator',
        'beton-hesaplayici' => 'beton_calculator', 'su-tesisat-hesaplama' => 'sutesisat_calculator', 'cati-maliyet-hesaplayici' => 'cati_calculator'] as $kitSlug => $kitName) {
        Route::get('/'.$kitSlug, [$kit, 'show'])->defaults('kit', $kitSlug)->name($kitName);
    }
    Route::get('/{kit}/hesapla', [$kit, 'calc'])->whereIn('kit', \App\Estimators\KitRegistry::slugs())
        ->middleware('throttle:kit-calc')->name('kit.calc');
    Route::get('/{kit}/pro-rapor', [$kitPro, 'show'])->whereIn('kit', \App\Estimators\KitRegistry::slugs())
        ->middleware('throttle:public-read')->name('kit.pro');
    Route::post('/{kit}/pro-rapor/kod', [$kitPro, 'sendCode'])->whereIn('kit', \App\Estimators\KitRegistry::slugs())
        ->middleware('throttle:public-write')->name('kit.pro_code');
    Route::post('/{kit}/pro-rapor', [$kitPro, 'download'])->whereIn('kit', \App\Estimators\KitRegistry::slugs())
        ->middleware('throttle:public-write')->name('kit.pro_download');

    // ── Controller tabanlı (veri hazırlayan) araçlar ──
    Route::controller(ToolController::class)->group(function () {
        Route::get('/duvar-rengi-gorsellestirici', 'paintVisualizer')->name('paint_visualizer');
        Route::get('/demir-metraji-hesaplayici', 'rebarCalculator')->name('rebar');

        Route::get('/oda-planlayici', 'roomPlanner')->name('room');
        Route::get('/belge-olusturucu', 'documentBuilder')->name('document');
        Route::get('/donati-tahvil-hesaplayici', 'rebarEquivalentCalculator')->name('rebar.equivalent');
        Route::get('/insaat-muhendisi-maas-hesabi', 'salaryCalculator')->name('salary');
        Route::get('/yagmur-borusu-hesabi', 'rainpipeCalculator')->name('rainpipe');
        Route::get('/azami-konut-kredisi', 'mortgageCalculator')->name('mortgage');
        Route::get('/birim-fiyat-analiz', 'unitPriceCalculator')->name('unit_price');
        Route::get('/merdiven-basamak-hesabi', 'stairsCalculator')->name('stairs');
        Route::get('/jenerator-hesaplama', 'generatorCalculator')->name('generator');
        Route::get('/enerji-tuketim-hesaplama', 'energyCalculator')->name('energy');

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
            ->middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->name('room.generate');
    });

    // ── ConTech hesaplayıcıları (sunucu tarafı fiyat bağlama) ──
    Route::get('/yukseltilmis-doseme-hesaplayici', [ConTech\RaisedFloorCalculatorController::class, 'index'])->name('raised_floor_calculator');
    Route::get('/havalandirma-hesaplayici', [ConTech\VentilationCalculatorController::class, 'index'])->name('ventilation_calculator');

    Route::get('/otomatik-kesif-fiyatlandirma', [ConTech\AutoBoqController::class, 'index'])->name('auto_boq');
    Route::post('/otomatik-kesif-fiyatlandirma/hesapla', [ConTech\AutoBoqController::class, 'calculate'])
        ->middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->name('auto_boq.calc');

    // Seramik görselleştirici (MVP) — render çıktısı oturuma bağlı token ile indirilir
    Route::get('/seramik-gorsellestirici', [TileVisualizerController::class, 'index'])->name('tile_visualizer');


    // ── Statik (yalnızca view) araçlar ──
    Route::view('/banyo-maliyet-hesaplayici', 'tools.banyo-calculator')->name('banyo_calculator');
    Route::get('/banyo-planlayici', [\App\Http\Controllers\Tools\BathPlannerController::class, 'show'])->name('banyo_planner');
    Route::get('/wc-planlayici', [\App\Http\Controllers\Tools\BathPlannerController::class, 'wc'])->name('wc_planner');
    Route::get('/mutfak-planlayici', [\App\Http\Controllers\Tools\BathPlannerController::class, 'kitchen'])->name('kitchen_planner');
    Route::post('/banyo-planlayici/render', [\App\Http\Controllers\Tools\BathPlannerController::class, 'render'])
        ->middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->name('banyo_planner_render');
    Route::get('/banyo-maliyet-hesaplayici/rapor.pdf', [\App\Http\Controllers\Reports\BathroomReportController::class, 'pdf'])
        ->middleware('throttle:public-read')->name('banyo_report_pdf');
    Route::get('/banyo-maliyet-hesaplayici/pro-rapor', [\App\Http\Controllers\Reports\BathroomProReportController::class, 'show'])
        ->middleware('throttle:public-read')->name('banyo_pro_report');
    Route::post('/banyo-maliyet-hesaplayici/pro-rapor/kod', [\App\Http\Controllers\Reports\BathroomProReportController::class, 'sendCode'])
        ->middleware('throttle:public-write')->name('banyo_pro_report_code');
    Route::post('/banyo-maliyet-hesaplayici/pro-rapor', [\App\Http\Controllers\Reports\BathroomProReportController::class, 'download'])
        ->middleware('throttle:public-write')->name('banyo_pro_report_download');
    Route::view('/cimento-kum-hesaplama', 'tools.cement-sand-calculator')->name('cement_calculator');
    Route::view('/metrekup-hacim-hesaplama', 'tools.volume-calculator')->name('volume_calculator');
    Route::view('/iscilik-maliyeti', 'tools.iscilik-calculator')->name('iscilik_calculator');
    Route::view('/sgk-asgari-iscilik', 'tools.sgk-calculator')->name('sgk_calculator');
    Route::view('/klima-btu-hesaplayici', 'tools.klima-calculator')->name('klima_calculator');
    Route::view('/kablo-kesiti-hesabi', 'tools.kablo-calculator')->name('kablo_calculator');
    Route::view('/asansor-zorunlulugu', 'tools.asansor-calculator')->name('asansor_calculator');
    Route::view('/taks-kaks-hesaplayici', 'tools.taks-kaks-calculator')->name('taks_kaks_calculator');
    Route::view('/pafta-olcu-hesaplayici', 'tools.pafta-calculator')->name('pafta_calculator');
    Route::view('/kat-karsiligi-analiz', 'tools.kat-karsiligi')->name('kat_karsiligi');
    Route::view('/daire-kira-degeri', 'tools.kira-calculator')->name('kira_calculator');
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
Route::get('/llms.txt', \App\Http\Controllers\LlmsController::class)->middleware('throttle:public-read')->name('llms');

Route::post('/api/seramik-gorsellestirici/render', [TileVisualizerController::class, 'render'])
    ->middleware(['ai.spend', 'ai.quota', 'throttle:ai'])->name('api.tile_visualizer.render');

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
