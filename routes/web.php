<?php

// B2B Seramik Aracı Rotası
Route::get('/araclar/seramik-maliyet-hesaplayici', [\App\Http\Controllers\ToolController::class, 'tileCalculator'])->name('tools.tile_calculator');


// ADMIN: AI PDF FİYAT GÜNCELLEME
Route::get('/admin/ai-fiyat-guncelle', [\App\Http\Controllers\ConTech\AdminPriceController::class, 'index'])->name('admin.ai_prices.index')->middleware('auth');
Route::post('/admin/ai-fiyat-guncelle', [\App\Http\Controllers\ConTech\AdminPriceController::class, 'upload'])->name('admin.prices.upload')->middleware('auth');


use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $homePrices = \Illuminate\Support\Facades\DB::table('unit_prices')
            ->where('price_pool', 'market')
            ->where('is_active', true)
            ->pluck('price', 'key')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    } catch (\Throwable $e) {
        $homePrices = [];
    }

    try {
        $officialBuildingPrices = \Illuminate\Support\Facades\DB::table('building_classes as c')
            ->join('building_class_costs as p', 'p.building_class_id', '=', 'c.id')
            ->whereIn('c.code', ['3B', '4A'])
            ->where('c.is_active', true)
            ->where('p.is_active', true)
            ->orderByDesc('p.period')
            ->select('c.code', 'p.unit_price')
            ->get()
            ->unique('code');

        foreach ($officialBuildingPrices as $officialPrice) {
            $homePrices[
                $officialPrice->code === '3B'
                    ? 'bina_sinif_3b_m2'
                    : 'bina_sinif_4a_m2'
            ] = (float) $officialPrice->unit_price;
        }
    } catch (\Throwable $e) {
        // Resmî fiyat bulunamazsa ana sayfa uydurma bütçe üretmez.
    }

    return view('index', compact('homePrices'));
})->name('home');

    // YENİ EKLENEN İLERİ MÜHENDİSLİK VE NİŞ ARAÇLAR
    Route::get('/araclar/yapi-dinamigi', [\App\Http\Controllers\ToolController::class, 'structuralDynamics'])->name('tools.structural_dynamics');
    Route::get('/araclar/kolon-tasima-kapasitesi', [\App\Http\Controllers\ToolController::class, 'columnCapacity'])->name('tools.column_capacity');
    Route::get('/araclar/basit-kiris-statigi', [\App\Http\Controllers\ToolController::class, 'simpleBeam'])->name('tools.simple_beam');
    Route::get('/araclar/bina-agirlik-hesabi', [\App\Http\Controllers\ToolController::class, 'buildingWeight'])->name('tools.building_weight');
    Route::get('/araclar/karot-dayanim', [\App\Http\Controllers\ToolController::class, 'coreStrength'])->name('tools.core_strength');
    Route::get('/araclar/kolon-filiz-boyu', [\App\Http\Controllers\ToolController::class, 'columnStarterBar'])->name('tools.column_starter_bar');
    Route::get('/araclar/bombeli-tank-hacmi', [\App\Http\Controllers\ToolController::class, 'domedTank'])->name('tools.domed_tank');
    Route::get('/araclar/pompa-gucu-hesabi', [\App\Http\Controllers\ToolController::class, 'pumpPower'])->name('tools.pump_power');
        Route::get('/araclar/siginak-yonetmeligi', [\App\Http\Controllers\ToolController::class, 'shelterRegulation'])->name('tools.shelter_regulation');
    Route::get('/araclar/kesim-optimizasyonu', [\App\Http\Controllers\ToolController::class, 'cuttingOptimization'])->name('tools.cutting_optimization');
        Route::get('/araclar/malzeme-agirligi', [\App\Http\Controllers\ToolController::class, 'materialWeight'])->name('tools.material_weight');
    Route::get('/araclar/lojistik-maliyeti', [\App\Http\Controllers\ToolController::class, 'logisticsCost'])->name('tools.logistics_cost');
    Route::get('/araclar/is-makinesi-maliyeti', [\App\Http\Controllers\ToolController::class, 'machineCost'])->name('tools.machine_cost');
                // İHALE VE YAKLAŞIK MALİYET MODÜLÜ
    Route::get('/araclar/ihale-modulu', [\App\Http\Controllers\ToolController::class, 'tenderModule'])->name('tools.tender');
    // KULLANICI OTOMATİK METRAJ FİYATLANDIRICI
    
    

Route::post('/maliyet-hesapla/baslat/{projectType:slug}', [ProjectController::class, 'start'])
    ->name('projects.start');

Route::get('/maliyet-hesapla/proje/{project}', [ProjectController::class, 'questions'])
    ->name('projects.questions');

Route::post('/maliyet-hesapla/proje/{project}/cevaplar', [ProjectController::class, 'saveAnswers'])
    ->name('projects.answers.save');

Route::get('/maliyet-hesapla/proje/{project}/ozet', [ProjectController::class, 'summary'])
    ->name('projects.summary');

Route::get('/maliyet-hesapla/proje/{project}/sonuc', [ProjectController::class, 'calculate'])
    ->name('projects.calculate');

Route::get('/maliyet-hesapla/proje/{project}/iletisim', [ProjectController::class, 'lead'])
    ->name('projects.lead');

Route::post('/maliyet-hesapla/proje/{project}/iletisim', [ProjectController::class, 'saveLead'])
    ->name('projects.lead.save');

Route::get('/maliyet-hesapla/proje/{project}/pdf', [\App\Http\Controllers\ProjectController::class, 'downloadPdf'])->name('projects.pdf');

Route::post('/maliyet-hesapla/proje/{project}/render', [\App\Http\Controllers\ProjectController::class, 'generateRender'])->name('projects.render');

Route::post('/maliyet-hesapla/kaydet', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');

// Kainta Profesyonel Keşif Talebi Rotası
use App\Http\Controllers\LeadController;
Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');


// WORKSITEM AI GÖRSELLEŞTİRME MODÜLLERİ
Route::prefix('ai-tasarim')->group(function () {
    Route::get('/ic-mekan', [\App\Http\Controllers\AiDesignController::class, 'interiorIndex'])->name('ai.interior');
    Route::post('/ic-mekan/start', [\App\Http\Controllers\AiDesignController::class, 'startGeneration'])->name('ai.interior.start');
    Route::get('/ic-mekan/status/{id}', [\App\Http\Controllers\AiDesignController::class, 'checkStatus'])->name('ai.interior.status');    Route::get('/merdiven-hesaplayici', [\App\Http\Controllers\ToolController::class, 'stairCalculator'])->name('tools.stair');

});


// SEO: eski saha betonu URL'si kanonik araç URL'sine yönlenir
Route::redirect(
    '/ai-tasarim/saha-betonu-hesaplayici',
    '/araclar/saha-betonu-hesaplayici',
    301
);

// KANONİK SAHA BETONU HESAPLAYICI
Route::get(
    '/araclar/saha-betonu-hesaplayici',
    [\App\Http\Controllers\ToolController::class, 'concreteCalculator']
)->name('tools.concrete');
Route::redirect(
    '/ai-tasarim/merdiven-hesaplayici',
    '/araclar/merdiven-basamak-hesabi',
    301
);
Route::get('/ai-tasarim/{any}', function ($any) {
    return redirect('/araclar/' . $any, 301);
})->where('any', '.*');
// WORKSITEM AI ARAÇLARI (SEO & LEAD GEN)
Route::prefix('araclar')->group(function () {
    Route::get('/', [\App\Http\Controllers\ToolController::class, 'index'])->name('tools.index');
    Route::get('/boya-hesaplayici', [\App\Http\Controllers\ToolController::class, 'paintCalculator'])->name('tools.paint');
    Route::get('/karo-hesaplayici', [\App\Http\Controllers\ToolController::class, 'tileCalculator'])->name('tools.tile');
    Route::get('/alcipan-hesaplayici', [\App\Http\Controllers\ToolController::class, 'drywallCalculator'])->name('tools.drywall');
    Route::get('/sap-hesaplayici', [\App\Http\Controllers\ToolController::class, 'screedCalculator'])->name('tools.screed');
    Route::get('/oda-planlayici', [\App\Http\Controllers\ToolController::class, 'roomPlanner'])->name('tools.room');

    Route::get('/belge-olusturucu', [\App\Http\Controllers\ToolController::class, 'documentBuilder'])->name('tools.document');
    Route::post('/belge-olusturucu/indir', [\App\Http\Controllers\ToolController::class, 'generateDocument'])->name('tools.document.generate');
    Route::get('/banyo-maliyet-hesaplayici', [\App\Http\Controllers\ToolController::class, 'bathroomCalculator'])->name('tools.bathroom');
    Route::get('/demir-metraji-hesaplayici', [\App\Http\Controllers\ToolController::class, 'rebarCalculator'])->name('tools.rebar');
    Route::get('/pvc-dograma-hesaplayici', [\App\Http\Controllers\ToolController::class, 'pvcCalculator'])->name('tools.pvc');
    Route::get('/duvar-maliyet-hesaplayici', [\App\Http\Controllers\ToolController::class, 'wallCalculator'])->name('tools.wall');
    Route::get('/parke-maliyet-hesaplayici', [\App\Http\Controllers\ToolController::class, 'parquetCalculator'])->name('tools.parquet');
    Route::get('/klima-btu-hesaplayici', [\App\Http\Controllers\ToolController::class, 'acCalculator'])->name('tools.ac');
    Route::get('/mutfak-maliyet-hesaplayici', [\App\Http\Controllers\ToolController::class, 'kitchenCalculator'])->name('tools.kitchen');
    Route::get('/radyator-petek-hesaplayici', [\App\Http\Controllers\ToolController::class, 'radiatorCalculator'])->name('tools.radiator');
    Route::get('/donati-tahvil-hesaplayici', [\App\Http\Controllers\ToolController::class, 'rebarEquivalentCalculator'])->name('tools.rebar.equivalent');
    Route::get('/taks-kaks-hesaplayici', [\App\Http\Controllers\ToolController::class, 'zoningCalculator'])->name('tools.zoning');
    Route::get('/pafta-olcu-hesaplayici', [\App\Http\Controllers\ToolController::class, 'mapScaleCalculator'])->name('tools.map_scale');
    Route::get('/tahmini-bina-maliyeti', [\App\Http\Controllers\ToolController::class, 'buildingCostCalculator'])->name('tools.building_cost');
    Route::get('/kat-karsiligi-analiz', [\App\Http\Controllers\ToolController::class, 'flatForLandCalculator'])->name('tools.flat_for_land');
    Route::get('/bahce-duvari-maliyeti', [\App\Http\Controllers\ToolController::class, 'gardenWallCalculator'])->name('tools.garden_wall');
    Route::get('/deprem-izolator-maliyeti', [\App\Http\Controllers\ToolController::class, 'isolatorCalculator'])->name('tools.isolator');
    Route::get('/insaat-muhendisi-maas-hesabi', [\App\Http\Controllers\ToolController::class, 'salaryCalculator'])->name('tools.salary');
    Route::get('/wc-maliyet-hesabi', [\App\Http\Controllers\ToolController::class, 'wcCalculator'])->name('tools.wc');
    Route::get('/yagmur-borusu-hesabi', [\App\Http\Controllers\ToolController::class, 'rainpipeCalculator'])->name('tools.rainpipe');
    Route::get('/azami-konut-kredisi', [\App\Http\Controllers\ToolController::class, 'mortgageCalculator'])->name('tools.mortgage');
    Route::get('/iscilik-maliyeti', [\App\Http\Controllers\ToolController::class, 'laborCalculator'])->name('tools.labor');
    Route::get('/birim-fiyat-analiz', [\App\Http\Controllers\ToolController::class, 'unitPriceCalculator'])->name('tools.unit_price');
    Route::get('/hafriyat-kazi-hesabi', [\App\Http\Controllers\ToolController::class, 'excavationCalculator'])->name('tools.excavation');
    Route::redirect('/cati-alani-maliyeti', '/araclar/cati-maliyet-hesaplayici', 301)->name('tools.roof');
    Route::get('/merdiven-basamak-hesabi', [\App\Http\Controllers\ToolController::class, 'stairsCalculator'])->name('tools.stairs');
    Route::get('/iskele-metraji-maliyeti', [\App\Http\Controllers\ToolController::class, 'scaffoldingCalculator'])->name('tools.scaffolding');
    Route::get('/kalip-plywood-maliyeti', [\App\Http\Controllers\ToolController::class, 'formworkCalculator'])->name('tools.formwork');
    Route::get('/kablo-kesiti-hesabi', [\App\Http\Controllers\ToolController::class, 'cableCalculator'])->name('tools.cable');
    Route::get('/asansor-zorunlulugu', [\App\Http\Controllers\ToolController::class, 'elevatorCalculator'])->name('tools.elevator');
    Route::get('/gunes-paneli-hesaplama', [\App\Http\Controllers\ToolController::class, 'solarCalculator'])->name('tools.solar');
    Route::get('/jenerator-hesaplama', [\App\Http\Controllers\ToolController::class, 'generatorCalculator'])->name('tools.generator');
    Route::get('/alci-siva-hesaplama', [\App\Http\Controllers\ToolController::class, 'plasterCalculator'])->name('tools.plaster');
    Route::get('/cimento-kum-hesaplama', [\App\Http\Controllers\ToolController::class, 'cementCalculator'])->name('tools.cement');
    Route::get('/metrekup-hacim-hesaplama', [\App\Http\Controllers\ToolController::class, 'volumeCalculator'])->name('tools.volume');
    Route::get('/enerji-tuketim-hesaplama', [\App\Http\Controllers\ToolController::class, 'energyCalculator'])->name('tools.energy');
    Route::get('/su-tesisat-hesaplama', [\App\Http\Controllers\ToolController::class, 'plumbingCalculator'])->name('tools.plumbing');
    Route::get('/kara-siva-hesaplama', [\App\Http\Controllers\ToolController::class, 'roughPlasterCalculator'])->name('tools.rough_plaster');
    Route::post('/oda-planlayici/generate', [\App\Http\Controllers\ToolController::class, 'generateRoomDesign'])->name('tools.room.generate');
});
// SEO Dinamik Sitemap
Route::get('/sitemap.xml', function() {
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Ana Sayfa ve Araçlar Merkezi
    $xml .= '<url><loc>' . url('/') . '</loc><priority>1.0</priority><changefreq>daily</changefreq></url>';
    $xml .= '<url><loc>' . url('/araclar') . '</loc><priority>0.9</priority><changefreq>daily</changefreq></url>';
    $xml .= '<url><loc>' . url('/maliyet-hesapla') . '</loc><priority>0.9</priority><changefreq>daily</changefreq></url>';
    
    // Tüm Araçlar
    $toolUrls = [];
    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();
        if (!in_array("GET", $route->methods(), true)) continue;
        if (!str_starts_with($uri, "araclar/") || str_contains($uri, "{")) continue;
        if (str_contains($route->getActionName(), "RedirectController")) continue;
        $toolUrls[$uri] = true;
    }
    ksort($toolUrls);
    foreach (array_keys($toolUrls) as $uri) {
        $xml .= "<url><loc>" . url("/".$uri) . "</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>";
    }
    
    $xml .= '</urlset>';
    
    return response($xml, 200)->header('Content-Type', 'text/xml');
    
});

// AI Duvar Rengi Görselleştirici
Route::get('/araclar/duvar-rengi-gorsellestirici', [\App\Http\Controllers\ToolController::class, 'paintVisualizer'])->name('tools.paint_visualizer');
Route::get('/duvar-rengi-gorsellestirici', [\App\Http\Controllers\ToolController::class, 'paintVisualizer']);

// Evrensel Keşif Modal Lead Kayıt Endpoint'i
Route::post('/lead-kaydet', [\App\Http\Controllers\LeadController::class, 'store'])->name('lead.store');


Route::get('/maliyet-hesapla', function() { return view('maliyet-hesapla');
    
     })->name('maliyet_hesapla');

// Maliyet Hesaplama Form Gönderimi (POST)
Route::post('/maliyet-hesapla', [\App\Http\Controllers\ProjectController::class, 'store'])->name('maliyet_hesapla.store');

// ADMIN PANELİ ROTALARI
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/fiyatlar', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.prices.index');
    

// SEO 301 YÖNLENDİRMELERİ (Eski URL'leri Yeni ConTech Hub'a Bağlama)

Route::post('/fiyatlar/guncelle/{id}', [\App\Http\Controllers\AdminController::class, 'update'])->name('admin.prices.update');
    Route::get('/talepler', [\App\Http\Controllers\AdminController::class, 'leads'])->name('admin.leads.index');
    Route::get('/talepler/{id}/teklif', [\App\Http\Controllers\AdminController::class, 'generateProposal'])->name('admin.leads.proposal');
});

// CANLI POZ ARAMA API'Sİ
Route::get('/api/pozlar/search', function(\Illuminate\Http\Request $request) {
    $q = trim($request->get('q', ''));
    $inst = $request->get('institution', 'all');

    $query = \App\Models\TenderPoz::query();

    if ($inst !== 'all' && !empty($inst)) {
        $query->where('institution', $inst);
    }

    if (!empty($q)) {
        $query->where(function($sub) use ($q) {
            $sub->where('poz_no', 'LIKE', "%{$q}%")
                ->orWhere('description', 'LIKE', "%{$q}%");
        });
    }

    $pricing = app(\App\Services\TenderPricingService::class);

    $rows = $query
        ->where('is_active', true)
        ->orderBy('poz_no')
        ->limit(40)
        ->get()
        ->map(function ($poz) use ($pricing) {
            $calc = $pricing->calculate($poz, 1);

            return [
                'id' => $poz->id,
                'year' => $poz->year,
                'period' => $poz->period,
                'institution' => $poz->institution,
                'poz_no' => $poz->poz_no,
                'description' => $poz->description,
                'unit' => $poz->unit,
                'official_unit_price' => $calc['official_unit_price'],
                'offer_unit_price' => $calc['offer_unit_price'],
                'quote_status' => $calc['status'],
                'quote_message' => $calc['reason'],
            ];
        });

    return response()->json($rows);
});

// ADMIN EXCEL POZ IMPORT ROTALARI
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/poz-import', [\App\Http\Controllers\AdminController::class, 'pozImportForm'])->name('admin.poz.import.form');
    

// SEO 301 YÖNLENDİRMELERİ (Eski URL'leri Yeni ConTech Hub'a Bağlama)

Route::post('/poz-import', [\App\Http\Controllers\AdminController::class, 'pozImportUpload'])->name('admin.poz.import.upload');
});
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;





Route::post('/maliyet-hesapla/lead', [\App\Http\Controllers\LeadController::class, 'store'])
    ->name('maliyet_hesapla.lead');
Route::post('/maliyet-hesapla/guest-render', [\App\Http\Controllers\ProjectController::class, 'guestRender'])->name('projects.guest.render');


// B2B TEŞEKKÜR EKRANI ROTALARI
Route::get('/maliyet-hesapla/basarili', function () {
    return view('thanks');
})->name('lead.thanks');

// B2B OTOMATİK KEŞİF (CLEAN ARCHITECTURE)
Route::get('/araclar/otomatik-kesif-fiyatlandirma', [\App\Http\Controllers\ConTech\AutoBoqController::class, 'index'])->name('tools.auto_boq');
Route::post('/araclar/otomatik-kesif-fiyatlandirma/hesapla', [\App\Http\Controllers\ConTech\AutoBoqController::class, 'calculate'])->name('tools.auto_boq.calc');

// 🤖 AI KARO OPTİMİZASYON (B2B Kesin Override)
Route::get('/araclar/karo-hesaplayici', function() { return view('tools.karo-calculator'); })->name('tools.karo_calculator_ai');

// 🪵 B2B PARKE HESAPLAYICI (Kesin Override)
Route::get('/araclar/parke-maliyet-hesaplayici', function() { return view('tools.parquet-calculator'); })->name('tools.parquet_calculator');

// 🌡️ B2B MANTOLAMA HESAPLAYICI (Kesin Override)

// 🏠 B2B ÇATI HESAPLAYICI (Kesin Override)


// 🍳 B2B MUTFAK HESAPLAYICI (Kesin Override)
Route::get('/araclar/mutfak-maliyet-hesaplayici', function() { return view('tools.mutfak-calculator'); })->name('tools.mutfak_calculator');

// 🚿 B2B BANYO HESAPLAYICI (Kesin Override)
Route::get('/araclar/banyo-maliyet-hesaplayici', function() { return view('tools.banyo-calculator'); })->name('tools.banyo_calculator');

// 🚽 B2B WC HESAPLAYICI (Kesin Override)
Route::get('/araclar/wc-maliyet-hesabi', function() { return view('tools.wc-calculator'); })->name('tools.wc_calculator');

// 🏗️ B2B ÇİMENTO & KUM HESAPLAYICI (Kesin Override)
Route::get('/araclar/cimento-kum-hesaplama', function() { return view('tools.cement-sand-calculator'); })->name('tools.cement_calculator');

// 📐 B2B METREKÜP HESAPLAYICI (Kesin Override)
Route::get('/araclar/metrekup-hacim-hesaplama', function() { return view('tools.volume-calculator'); })->name('tools.volume_calculator');

// 🚜 B2B HAFRİYAT HESAPLAYICI (Kesin Override)
Route::get('/araclar/hafriyat-kazi-hesabi', function() { return view('tools.excavation-calculator'); })->name('tools.excavation_calculator');

// 🏗️ B2B İSKELE HESAPLAYICI (Kesin Override)
Route::get('/araclar/iskele-metraji-maliyeti', function() { return view('tools.iskele-calculator'); })->name('tools.iskele_calculator');

// 👷 B2B İŞÇİLİK HESAPLAYICI (Kesin Override)
Route::get('/araclar/iscilik-maliyeti', function() { return view('tools.iscilik-calculator'); })->name('tools.iscilik_calculator');

// 🏛️ B2B SGK İSKAN HESAPLAYICI (Kesin Override)
Route::get('/araclar/sgk-asgari-iscilik', function() { return view('tools.sgk-calculator'); })->name('tools.sgk_calculator');

// ❄️ B2B KLİMA İKLİMLENDİRME HESAPLAYICI (Kesin Override)
Route::get('/araclar/klima-btu-hesaplayici', function() { return view('tools.klima-calculator'); })->name('tools.klima_calculator');

// 💨 B2B HAVALANDIRMA KANALI HESAPLAYICI (Kesin Override)
Route::redirect('/araclar/havalandirma-kanali', '/araclar/havalandirma-hesaplayici', 301)->name('tools.havalandirma_calculator');

// 🚰 B2B SU TESİSATI HESAPLAYICI (Kesin Override)
Route::get('/araclar/su-tesisat-hesaplama', function() { return view('tools.su-tesisat-calculator'); })->name('tools.sutesisat_calculator');

// ⚡ B2B ELEKTRİK / KABLO KESİTİ HESAPLAYICI (Kesin Override)
Route::get('/araclar/kablo-kesiti-hesabi', function() { return view('tools.kablo-calculator'); })->name('tools.kablo_calculator');

// 🛗 B2B ASANSÖR HESAPLAYICI (Kesin Override)
Route::get('/araclar/asansor-zorunlulugu', function() { return view('tools.asansor-calculator'); })->name('tools.asansor_calculator');

// 🏢 B2B İMAR TAKS KAKS HESAPLAYICI (Kesin Override)
Route::get('/araclar/taks-kaks-hesaplayici', function() { return view('tools.taks-kaks-calculator'); })->name('tools.taks_kaks_calculator');

// 🗺️ B2B HARİTA PAFTA HESAPLAYICI (Kesin Override)
Route::get('/araclar/pafta-olcu-hesaplayici', function() { return view('tools.pafta-calculator'); })->name('tools.pafta_calculator');

// 📐 B2B STATİK PROJE HESAPLAYICI (Kesin Override)
Route::get('/araclar/statik-proje-bedeli', function() { return view('tools.statik-calculator'); })->name('tools.statik_calculator');

// 🤝 B2B KAT KARŞILIĞI HESAPLAYICI (Kesin Override)
Route::get('/araclar/kat-karsiligi-analiz', function() { return view('tools.kat-karsiligi'); })->name('tools.kat_karsiligi');

// 🧹 TEMİZLENEN B2C ARAÇLARI İÇİN YÖNLENDİRME

Route::redirect('/araclar/taksit-pesin-kiyaslama', '/araclar', 301);


 

// ☀️ B2B GÜNEŞ PANELİ (GES) HESAPLAYICI (Kesin Override)
Route::get('/araclar/gunes-paneli-hesaplama', function() { return view('tools.ges-calculator'); })->name('tools.ges_calculator');

// 🏢 B2B KİRA AMORTİSMAN HESAPLAYICI (Kesin Override)
Route::get('/araclar/daire-kira-degeri', function() { return view('tools.kira-calculator'); })->name('tools.kira_calculator');


// 🛠️ DÜZELTİLMİŞ İNCE İŞLER & ÇATI ROTALARI 
Route::get(
    '/araclar/alcipan-hesaplayici',
    [
        \App\Http\Controllers\ConTech\DrywallCalculatorController::class,
        'index'
    ]
)->name('tools.alcipan_calculator');
Route::get('/araclar/cati-maliyet-hesaplayici', function() { return view('tools.cati-calculator'); })->name('tools.cati_calculator');
Route::get('/araclar/mantolama-maliyeti', function() { return view('tools.yalitim-calculator'); })->name('tools.yalitim_calculator');

// 🔄 MENÜ İÇİN YEDEK (ALIAS) ROTALAR 
Route::redirect('/araclar/alcipan-asma-tavan-hesaplayici', '/araclar/alcipan-hesaplayici', 301);
Route::redirect('/araclar/yalitim-mantolama-hesaplayici', '/araclar/mantolama-maliyeti', 301);

// 🏗️ YENİ EKSİK B2B MOTORLARI (Beton, Fayans, Duvar) 
Route::get('/araclar/beton-hesaplayici', function () {
    $toolPrices = app(\App\Services\ToolPriceService::class)
        ->forTool('beton-hesaplayici');

    return view('tools.beton-calculator', compact('toolPrices'));
})->name('tools.beton_calculator');
Route::get('/araclar/zemin-kaplama', function() { return view('tools.zemin-calculator'); })->name('tools.zemin_calculator');
Route::get('/araclar/duvar-orgu', function() { return view('tools.duvar-calculator'); })->name('tools.duvar_calculator');


// ============================================================
// WORKSITEM LEGACY TOOL SLUGS -> CANONICAL URLS
// ============================================================

$legacyToolRedirects = [

    // Klima
    'ac-calculator'
        => 'klima-btu-hesaplayici',
    'klima-calculator'
        => 'klima-btu-hesaplayici',

    // Alçıpan
    'alcipan-tavan-calculator'
        => 'alcipan-hesaplayici',
    'drywall-calculator'
        => 'alcipan-hesaplayici',

    // Asansör
    'asansor-calculator'
        => 'asansor-zorunlulugu',
    'elevator-calculator'
        => 'asansor-zorunlulugu',

    // Banyo
    'banyo-calculator'
        => 'banyo-maliyet-hesaplayici',
    'bathroom-calculator'
        => 'banyo-maliyet-hesaplayici',

    // Bina
    'building-cost-calculator'
        => 'tahmini-bina-maliyeti',
    'building-weight'
        => 'bina-agirlik-hesabi',

    // Çatı
    'cati-calculator'
        => 'cati-maliyet-hesaplayici',
    'roof-calculator'
        => 'cati-maliyet-hesaplayici',

    // Statik
    'column-capacity'
        => 'kolon-tasima-kapasitesi',
    'column-starter-bar'
        => 'kolon-filiz-boyu',
    'core-strength'
        => 'karot-dayanim',
    'simple-beam'
        => 'basit-kiris-statigi',
    'structural-dynamics'
        => 'yapi-dinamigi',

    // Diğer mühendislik araçları
    'cutting-optimization'
        => 'kesim-optimizasyonu',
    'document-builder'
        => 'belge-olusturucu',
    'domed-tank'
        => 'bombeli-tank-hacmi',
    'energy-calculator'
        => 'enerji-tuketim-hesaplama',
    'excavation-calculator'
        => 'hafriyat-kazi-hesabi',
    'flat-for-land-calculator'
        => 'kat-karsiligi-analiz',
    'formwork-calculator'
        => 'kalip-plywood-maliyeti',
    'garden-wall-calculator'
        => 'bahce-duvari-maliyeti',
    'generator-calculator'
        => 'jenerator-hesaplama',

    // Enerji
    'ges-calculator'
        => 'gunes-paneli-hesaplama',
    'solar-calculator'
        => 'gunes-paneli-hesaplama',

    // Havalandırma
    'havalandirma-calculator'
        => 'havalandirma-hesaplayici',
    'ventilation-duct'
        => 'havalandirma-hesaplayici',

    // Yalıtım
    'insulation-calculator'
        => 'mantolama-maliyeti',
    'mantolama-calculator'
        => 'mantolama-maliyeti',
    'yalitim-calculator'
        => 'mantolama-maliyeti',

    // İşçilik
    'iscilik-calculator'
        => 'iscilik-maliyeti',
    'labor-calculator'
        => 'iscilik-maliyeti',

    // İskele
    'iskele-calculator'
        => 'iskele-metraji-maliyeti',
    'scaffolding-calculator'
        => 'iskele-metraji-maliyeti',

    // İzolatör
    'isolator-calculator'
        => 'deprem-izolator-maliyeti',

    // Kablo
    'cable-calculator'
        => 'kablo-kesiti-hesabi',
    'kablo-calculator'
        => 'kablo-kesiti-hesabi',

    // Kira
    'kira-calculator'
        => 'daire-kira-degeri',
    'rent-value'
        => 'daire-kira-degeri',

    // Mutfak
    'kitchen-calculator'
        => 'mutfak-maliyet-hesaplayici',
    'mutfak-calculator'
        => 'mutfak-maliyet-hesaplayici',

    // Lojistik
    'logistics-cost'
        => 'lojistik-maliyeti',
    'machine-cost'
        => 'is-makinesi-maliyeti',
    'material-weight'
        => 'malzeme-agirligi',

    // Kredi
    'mortgage-calculator'
        => 'azami-konut-kredisi',

    // Pafta
    'map-scale-calculator'
        => 'pafta-olcu-hesaplayici',
    'pafta-calculator'
        => 'pafta-olcu-hesaplayici',

    // Boya
    'paint-calculator'
        => 'boya-hesaplayici',
    'paint-visualizer'
        => 'duvar-rengi-gorsellestirici',

    // Parke
    'parquet-calculator'
        => 'parke-maliyet-hesaplayici',

    // Sıva
    'plaster-calculator'
        => 'alci-siva-hesaplama',
    'rough-plaster-calculator'
        => 'kara-siva-hesaplama',

    // Tesisat
    'plumbing-calculator'
        => 'su-tesisat-hesaplama',
    'su-tesisat-calculator'
        => 'su-tesisat-hesaplama',

    // Pompa
    'pump-power'
        => 'pompa-gucu-hesabi',

    // PVC
    'pvc-calculator'
        => 'pvc-dograma-hesaplayici',

    // Radyatör
    'radiator-calculator'
        => 'radyator-petek-hesaplayici',

    // Yağmur borusu
    'rainpipe-calculator'
        => 'yagmur-borusu-hesabi',

    // Donatı
    'rebar-calculator'
        => 'demir-metraji-hesaplayici',
    'rebar-equivalent-calculator'
        => 'donati-tahvil-hesaplayici',

    // Oda
    'room-planner'
        => 'oda-planlayici',

    // Maaş
    'salary-calculator'
        => 'insaat-muhendisi-maas-hesabi',

    // Şap
    'screed-calculator'
        => 'sap-hesaplayici',

    // SGK
    'sgk-calculator'
        => 'sgk-asgari-iscilik',
    'sgk-labor'
        => 'sgk-asgari-iscilik',

    // Sığınak
    'shelter-regulation'
        => 'siginak-yonetmeligi',

    // Merdiven
    'stair-calculator'
        => 'merdiven-basamak-hesabi',
    'stairs-calculator'
        => 'merdiven-basamak-hesabi',

    // Statik proje
    'static-project-fee'
        => 'statik-proje-bedeli',
    'statik-calculator'
        => 'statik-proje-bedeli',

    // TAKS KAKS
    'taks-kaks-calculator'
        => 'taks-kaks-hesaplayici',

    // İhale
    'tender-module'
        => 'ihale-modulu',

    // Birim fiyat
    'unit-price-calculator'
        => 'birim-fiyat-analiz',

    // Hacim
    'volume-calculator'
        => 'metrekup-hacim-hesaplama',

    // WC
    'wc-calculator'
        => 'wc-maliyet-hesabi',
];

foreach ($legacyToolRedirects as $old => $new) {

    Route::redirect(
        '/araclar/' . $old,
        '/araclar/' . $new,
        301
    );
}


// ============================================================
// WORKSITEM ORPHAN LEGACY REDIRECTS
// ============================================================

// Eski Auto BOQ view slug
Route::redirect(
    '/araclar/auto-boq',
    '/araclar/otomatik-kesif-fiyatlandirma',
    301
);

// Beton araçları
Route::redirect(
    '/araclar/beton-calculator',
    '/araclar/beton-hesaplayici',
    301
);

Route::redirect(
    '/araclar/concrete-calculator',
    '/araclar/saha-betonu-hesaplayici',
    301
);

// Duvar
Route::redirect(
    '/araclar/duvar-calculator',
    '/araclar/duvar-orgu',
    301
);

Route::redirect(
    '/araclar/wall-calculator',
    '/araclar/duvar-maliyet-hesaplayici',
    301
);

// Seramik / karo
Route::redirect(
    '/araclar/karo-calculator',
    '/araclar/karo-hesaplayici',
    301
);

Route::redirect(
    '/araclar/tile-calculator',
    '/araclar/seramik-maliyet-hesaplayici',
    301
);

// Kat karşılığı
Route::redirect(
    '/araclar/kat-karsiligi',
    '/araclar/kat-karsiligi-analiz',
    301
);

// Eski maliyet hesaplama view slug
Route::redirect(
    '/araclar/maliyet-hesapla',
    '/maliyet-hesapla',
    301
);

// Zemin
Route::redirect(
    '/araclar/zemin-calculator',
    '/araclar/zemin-kaplama',
    301
);

// B2C'den kaldırılmış taksit/peşin aracı
// Redirect zinciri oluşturmamak için direkt /araclar'a.
Route::redirect(
    '/araclar/installment-vs-cash',
    '/araclar',
    301
);


// ============================================================
// WORKSITEM FINAL CANONICAL TOOL ROUTES
// ============================================================

// Dijital Metraj
Route::get('/araclar/dijital-metraj', function () {
    return view('tools.web-takeoff');
})->name('tools.digital_takeoff');

// Eski Web Takeoff
Route::redirect(
    '/araclar/web-takeoff',
    '/araclar/dijital-metraj',
    301
);

// Eski Çimento URL'leri
Route::redirect(
    '/araclar/cement-calculator',
    '/araclar/cimento-kum-hesaplama',
    301
);

Route::redirect(
    '/araclar/cement-sand-calculator',
    '/araclar/cimento-kum-hesaplama',
    301
);

// Eski TAKS / KAKS URL
Route::redirect(
    '/araclar/zoning-calculator',
    '/araclar/taks-kaks-hesaplayici',
    301
);

// pdf-contract doğrudan kullanıcı aracı değildir
Route::redirect(
    '/araclar/pdf-contract',
    '/araclar/belge-olusturucu',
    301
);



// ============================================================
// WORKSITEM CONTECH - ASMA TAVAN SISTEMLERI
// ============================================================

Route::get(
    '/araclar/asma-tavan-hesaplayici',
    [
        \App\Http\Controllers\ConTech\CeilingCalculatorController::class,
        'index'
    ]
)->name('tools.ceiling_calculator');


// ============================================================
// WORKSITEM CONTECH - SU YALITIMI
// ============================================================

Route::get(
    '/araclar/su-yalitimi-hesaplayici',
    [
        \App\Http\Controllers\ConTech\WaterproofingCalculatorController::class,
        'index'
    ]
)->name('tools.waterproofing_calculator');


// ============================================================
// WORKSITEM CONTECH - EPOKSI ZEMIN
// ============================================================

Route::get(
    '/araclar/epoksi-zemin-hesaplayici',
    [
        \App\Http\Controllers\ConTech\EpoxyFloorCalculatorController::class,
        'index'
    ]
)->name('tools.epoxy_floor_calculator');


// ============================================================
// WORKSITEM CONTECH - SANDVIC PANEL
// ============================================================

Route::get(
    '/araclar/sandvic-panel-hesaplayici',
    [
        \App\Http\Controllers\ConTech\SandwichPanelCalculatorController::class,
        'index'
    ]
)->name('tools.sandwich_panel_calculator');


// ============================================================
// WORKSITEM CONTECH - YUKSELTILMIS DOSEME
// ============================================================

Route::get(
    '/araclar/yukseltilmis-doseme-hesaplayici',
    [
        \App\Http\Controllers\ConTech\RaisedFloorCalculatorController::class,
        'index'
    ]
)->name('tools.raised_floor_calculator');


// ============================================================
// WORKSITEM CONTECH - HAVALANDIRMA V2
// ============================================================

Route::get(
    '/araclar/havalandirma-hesaplayici',
    [
        \App\Http\Controllers\ConTech\VentilationCalculatorController::class,
        'index'
    ]
)->name('tools.ventilation_calculator');

// 🌐 ANA ARAÇLAR (DASHBOARD) SAYFASI
Route::get('/araclar', function() { return view('tools.index'); })->name('tools.index');

/*
|--------------------------------------------------------------------------
| KVKK Aydınlatma Metni
|--------------------------------------------------------------------------
*/
Route::view(
    '/kvkk-aydinlatma-metni',
    'kvkk-aydinlatma-metni'
)->name('kvkk.aydinlatma');

// Beta Hesaplayıcı Aracı Rotası
Route::get('/araclar/beta-hesaplayici', function () {
    return view('tools.beta-hesaplayici');
});

// SEO Uyumlu Gelişmiş İnşaat Maliyeti Hesaplama Rotası
Route::get('/araclar/insaat-maliyeti-hesaplama', function () {
    return view('tools.insaat-maliyeti');
});

/*
|--------------------------------------------------------------------------
| SEO Fallback — Bulunamayan URL'leri ana sayfaya 301 ile yönlendir
|--------------------------------------------------------------------------
| insaatvtadilat.com'dan gelen eski URL'ler ve ölü linkler için.
| 404 yerine 301 ile ana sayfaya göndererek SEO değerini korur.
*/
Route::fallback(function () {
    return redirect('/', 301);
});
