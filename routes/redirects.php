<?php

/*
|--------------------------------------------------------------------------
| SEO 301 Yönlendirmeleri
|--------------------------------------------------------------------------
| Eski / İngilizce / mükerrer slug'lar → kanonik Türkçe URL.
| Kural: zincir yok (A→B→C yerine A→C). Yeni yönlendirme eklerken
| hedefin gerçek bir 200 sayfası olduğunu doğrulayın (tests/Feature/RouteIntegrityTest).
*/

use Illuminate\Support\Facades\Route;

// ── /ai-tasarim/* eski URL'leri ────────────────────────────────────────
Route::redirect('/ai-tasarim/saha-betonu-hesaplayici', '/araclar/saha-betonu-hesaplayici', 301);
Route::redirect('/ai-tasarim/merdiven-hesaplayici', '/araclar/merdiven-basamak-hesabi', 301);

// ── İsimli yönlendirmeler (view'larda route() ile referans veriliyor) ──
Route::redirect('/araclar/karo-hesaplayici', '/araclar/seramik-maliyet-hesaplayici', 301)->name('tools.tile');
Route::redirect('/araclar/cati-alani-maliyeti', '/araclar/cati-maliyet-hesaplayici', 301)->name('tools.roof');
Route::redirect('/araclar/havalandirma-kanali', '/araclar/havalandirma-hesaplayici', 301)->name('tools.havalandirma_calculator');

// ── Menü alias'ları ve kaldırılan araçlar ─────────────────────────────
Route::redirect('/araclar/alcipan-asma-tavan-hesaplayici', '/araclar/alcipan-hesaplayici', 301);
Route::redirect('/araclar/yalitim-mantolama-hesaplayici', '/araclar/mantolama-maliyeti', 301);
Route::redirect('/araclar/taksit-pesin-kiyaslama', '/araclar', 301);
Route::redirect('/araclar/installment-vs-cash', '/araclar', 301);

// ── Eski view slug'ları → kanonik URL ─────────────────────────────────
$legacyToolRedirects = [
    // Klima
    'ac-calculator' => 'klima-btu-hesaplayici',
    'klima-calculator' => 'klima-btu-hesaplayici',
    // Alçıpan
    'alcipan-tavan-calculator' => 'alcipan-hesaplayici',
    'drywall-calculator' => 'alcipan-hesaplayici',
    // Asansör
    'asansor-calculator' => 'asansor-zorunlulugu',
    'elevator-calculator' => 'asansor-zorunlulugu',
    // Banyo
    'banyo-calculator' => 'banyo-maliyet-hesaplayici',
    'bathroom-calculator' => 'banyo-maliyet-hesaplayici',
    // Bina
    'building-cost-calculator' => 'tahmini-bina-maliyeti',
    'building-weight' => 'bina-agirlik-hesabi',
    // Çatı
    'cati-calculator' => 'cati-maliyet-hesaplayici',
    'roof-calculator' => 'cati-maliyet-hesaplayici',
    // Statik
    'column-capacity' => 'kolon-tasima-kapasitesi',
    'column-starter-bar' => 'kolon-filiz-boyu',
    'core-strength' => 'karot-dayanim',
    'simple-beam' => 'basit-kiris-statigi',
    'structural-dynamics' => 'yapi-dinamigi',
    // Diğer mühendislik
    'cutting-optimization' => 'kesim-optimizasyonu',
    'document-builder' => 'belge-olusturucu',
    'domed-tank' => 'bombeli-tank-hacmi',
    'energy-calculator' => 'enerji-tuketim-hesaplama',
    'excavation-calculator' => 'hafriyat-kazi-hesabi',
    'flat-for-land-calculator' => 'kat-karsiligi-analiz',
    'formwork-calculator' => 'kalip-plywood-maliyeti',
    'garden-wall-calculator' => 'bahce-duvari-maliyeti',
    'generator-calculator' => 'jenerator-hesaplama',
    // Enerji
    'ges-calculator' => 'gunes-paneli-hesaplama',
    'solar-calculator' => 'gunes-paneli-hesaplama',
    // Havalandırma
    'havalandirma-calculator' => 'havalandirma-hesaplayici',
    'ventilation-duct' => 'havalandirma-hesaplayici',
    // Yalıtım
    'insulation-calculator' => 'mantolama-maliyeti',
    'mantolama-calculator' => 'mantolama-maliyeti',
    'yalitim-calculator' => 'mantolama-maliyeti',
    // İşçilik
    'iscilik-calculator' => 'iscilik-maliyeti',
    'labor-calculator' => 'iscilik-maliyeti',
    // İskele
    'iskele-calculator' => 'iskele-metraji-maliyeti',
    'scaffolding-calculator' => 'iskele-metraji-maliyeti',
    // İzolatör
    'isolator-calculator' => 'deprem-izolator-maliyeti',
    // Kablo
    'cable-calculator' => 'kablo-kesiti-hesabi',
    'kablo-calculator' => 'kablo-kesiti-hesabi',
    // Kira
    'kira-calculator' => 'daire-kira-degeri',
    'rent-value' => 'daire-kira-degeri',
    // Mutfak
    'kitchen-calculator' => 'mutfak-maliyet-hesaplayici',
    'mutfak-calculator' => 'mutfak-maliyet-hesaplayici',
    // Lojistik
    'logistics-cost' => 'lojistik-maliyeti',
    'machine-cost' => 'is-makinesi-maliyeti',
    'material-weight' => 'malzeme-agirligi',
    // Kredi
    'mortgage-calculator' => 'azami-konut-kredisi',
    // Pafta
    'map-scale-calculator' => 'pafta-olcu-hesaplayici',
    'pafta-calculator' => 'pafta-olcu-hesaplayici',
    // Boya
    'paint-calculator' => 'boya-hesaplayici',
    'paint-visualizer' => 'duvar-rengi-gorsellestirici',
    // Parke
    'parquet-calculator' => 'parke-maliyet-hesaplayici',
    // Sıva
    'plaster-calculator' => 'alci-siva-hesaplama',
    'rough-plaster-calculator' => 'kara-siva-hesaplama',
    // Tesisat
    'plumbing-calculator' => 'su-tesisat-hesaplama',
    'su-tesisat-calculator' => 'su-tesisat-hesaplama',
    // Pompa
    'pump-power' => 'pompa-gucu-hesabi',
    // PVC
    'pvc-calculator' => 'pvc-dograma-hesaplayici',
    // Radyatör
    'radiator-calculator' => 'radyator-petek-hesaplayici',
    // Yağmur borusu
    'rainpipe-calculator' => 'yagmur-borusu-hesabi',
    // Donatı
    'rebar-calculator' => 'demir-metraji-hesaplayici',
    'rebar-equivalent-calculator' => 'donati-tahvil-hesaplayici',
    // Oda
    'room-planner' => 'oda-planlayici',
    // Maaş
    'salary-calculator' => 'insaat-muhendisi-maas-hesabi',
    // Şap
    'screed-calculator' => 'sap-hesaplayici',
    // SGK
    'sgk-calculator' => 'sgk-asgari-iscilik',
    'sgk-labor' => 'sgk-asgari-iscilik',
    // Sığınak
    'shelter-regulation' => 'siginak-yonetmeligi',
    // Merdiven
    'stair-calculator' => 'merdiven-basamak-hesabi',
    'stairs-calculator' => 'merdiven-basamak-hesabi',
    // Statik proje
    'static-project-fee' => 'statik-proje-bedeli',
    'statik-calculator' => 'statik-proje-bedeli',
    // TAKS KAKS
    'taks-kaks-calculator' => 'taks-kaks-hesaplayici',
    'zoning-calculator' => 'taks-kaks-hesaplayici',
    // İhale
    'tender-module' => 'ihale-modulu',
    // Birim fiyat
    'unit-price-calculator' => 'birim-fiyat-analiz',
    // Hacim
    'volume-calculator' => 'metrekup-hacim-hesaplama',
    // WC
    'wc-calculator' => 'wc-maliyet-hesabi',
    // Otomatik keşif
    'auto-boq' => 'otomatik-kesif-fiyatlandirma',
    // Beton
    'beton-calculator' => 'beton-hesaplayici',
    'concrete-calculator' => 'saha-betonu-hesaplayici',
    // Duvar
    'duvar-calculator' => 'duvar-orgu',
    'wall-calculator' => 'duvar-maliyet-hesaplayici',
    // Seramik / karo
    'karo-calculator' => 'seramik-maliyet-hesaplayici',
    'tile-calculator' => 'seramik-maliyet-hesaplayici',
    // Kat karşılığı
    'kat-karsiligi' => 'kat-karsiligi-analiz',
    // Zemin
    'zemin-calculator' => 'zemin-kaplama',
    // Dijital metraj
    'web-takeoff' => 'dijital-metraj',
    // Çimento
    'cement-calculator' => 'cimento-kum-hesaplama',
    'cement-sand-calculator' => 'cimento-kum-hesaplama',
    // Sözleşme → belge oluşturucu
    'pdf-contract' => 'belge-olusturucu',
];

foreach ($legacyToolRedirects as $old => $new) {
    Route::redirect('/araclar/'.$old, '/araclar/'.$new, 301);
}

Route::redirect('/araclar/maliyet-hesapla', '/maliyet-hesapla', 301);

// ── /ai-tasarim/{herhangi} → /araclar/{herhangi}
// NOT: Bu dosya web.php'de /ai-tasarim/ic-mekan grubundan SONRA yüklenir; o rotalar önce eşleşir.
Route::get('/ai-tasarim/{any}', fn (string $any) => redirect('/araclar/'.ltrim($any, '/'), 301))
    ->where('any', '.*');

/*
| Eski site (canlıda elle eklenmişti, 2026-10): eski SEO linkleri
*/
Route::any('/firmalar/{any?}', fn () => redirect('/maliyet-hesapla', 301))->where('any', '.*');
Route::any('/ic-dekorasyon-yuklenicisi/{any?}', fn () => redirect('/maliyet-hesapla', 301))->where('any', '.*');
Route::any('/detay.php', fn () => redirect('/', 301));
