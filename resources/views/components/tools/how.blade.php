{{--
    "Nasıl çalışır?" + "Neden Worksitem?" bandı: her araç sayfasında (ToolPageEnhancer ekler).
    Araç türüne göre 4 adım: maliyet, metraj, mühendislik, planlayıcı, yapay zekâ, bilgi aracı.
    Yalnız doğru olan söylenir: fiyat tarihi gerçek veriden, firma teklifi seçeneği yalnız açıkken.
--}}
@props(['tool'])
@php
    $cfg = (array) config("worksitem_tools.tools.$tool", []);
    $title = $cfg['title'] ?? 'Bu araç';
    $eng = in_array($tool, (array) config('worksitem_tools.engineering_tools', []), true);
    // v118: oda-planlayici sürükle-bırak 2D/3D planlayıcı değil (AI görsel); kendi adımları var
    $room = $tool === 'oda-planlayici';
    $planner = ! $room && str_ends_with($tool, 'planlayici');
    $takeoff = $tool === 'dijital-metraj';
    $color = $tool === 'duvar-rengi-gorsellestirici';
    $kit = in_array($tool, \App\Estimators\KitRegistry::slugs(), true) || $tool === 'banyo-maliyet-hesaplayici';
    $ai = $tool === 'otomatik-kesif-fiyatlandirma';
    $info = in_array($tool, ['azami-konut-kredisi', 'daire-kira-degeri', 'taks-kaks-hesaplayici', 'insaat-muhendisi-maas-hesabi', 'sgk-asgari-iscilik', 'belge-olusturucu', 'kat-karsiligi-analiz', 'pafta-olcu-hesaplayici', 'kesim-optimizasyonu', 'ihale-modulu', 'birim-fiyat-analiz'], true);
    $cost = ! $eng && ! $planner && ! $room && ! $takeoff && ! $color && ! $ai && ! $info && ($kit
        || config("worksitem_market.tools.$tool") || str_contains(mb_strtolower($title), 'maliyet'));
    $kind = $eng ? 'eng' : ($room ? 'room' : ($takeoff ? 'takeoff' : ($planner ? 'plan' : ($color ? 'color' : ($ai ? 'ai' : ($info ? 'info' : ($cost ? 'cost' : 'qty')))))));
    try {
        $priceDate = \App\Support\PriceDate::for($tool)->locale('tr')->translatedFormat('j F Y');
    } catch (\Throwable) {
        $priceDate = null;
    }
    $firms = \App\Support\Pilot::marketOpen() && config("worksitem_market.tools.$tool");
    $offer = $firms ? 'Kainta İnşaat Uzmanlarından keşif isteyin ya da ilçenizdeki onaylı firmalardan teklif alın; telefonunuz yalnız seçtiğiniz firmayla paylaşılır.'
        : 'Sonucu beğendiyseniz tek formla uzmandan keşif ve yazılı teklif isteyin. İletişim izniniz olmadan sizi kimse aramaz.';
    $steps = match ($kind) {
        'cost' => [
            ['Ölçü ve seçimleri girin', 'Alan, kalite ve kapsam gibi birkaç soruyu yanıtlayın; her seçimde sonuç anında güncellenir.'],
            ['Güncel fiyatlarla hesaplayın', 'Malzeme ve işçilik resmî birim fiyatlar ve piyasa araştırmasıyla fiyatlanır'.($priceDate ? '; bu aracın fiyat tarihi '.$priceDate : '').'. Fiyat robotu her sabah piyasayı tarar; yeni fiyat uzman onayından sonra yayına girer.'],
            $kit ? ['Dökümü raporlayın', 'Kalem kalem döküm ve detaylı Pro rapor; PDF olarak indirin veya bağlantıyla paylaşın.'] : ['Dökümü inceleyin', 'Kalemleri ve varsayımları inceleyin; yazdırın, PDF alın veya ekibinizle paylaşın.'],
            ['Teklife dönüştürün', $offer],
        ],
        'qty' => [
            ['Ölçüleri girin', 'Boyut, adet ve malzeme sınıfı gibi değerleri yazın; birimler alanların yanında belirtilir.'],
            ['Miktarı anında görün', 'Hacim, ağırlık, torba ya da adet gibi metraj sonuçları standart formüllerle hesaplanır, fire payı ayrıca gösterilir.'],
            ['Kaydedin, paylaşın', 'Sonucu yazdırın, PDF alın veya ekibinizle paylaşın.'],
            ['Uygulamaya geçin', $offer],
        ],
        'eng' => [
            ['Proje değerlerini girin', 'Kesit, yük, malzeme sınıfı gibi değerleri girin; her alanın birimi ve açıklaması yanında.'],
            ['Ön kontrol sonucunu görün', 'Hesap, ilgili yönetmelik ve standart formüllerle anında yapılır.'],
            ['Raporlayın', 'Sonucu yazdırın ya da PDF alın; ekibinizle karşılaştırma için kullanın.'],
            ['Mühendise danışın', 'Bu sonuç bir ön kontroldür, statik proje yerine geçmez. Taşıyıcı sisteme ilişkin her karar için yetkili inşaat mühendisine danışın.'],
        ],
        'plan' => [
            ['Ölçüleri girin', 'Oda ölçüsünü ve kapı/pencere yerlerini girin ya da hazır şablonla başlayın.'],
            ['Yerleştirin', 'Ürünleri sürükleyip bırakın; renk ve model seçin.'],
            ['2D / 3D görün', 'Planı üstten ve üç boyutlu inceleyin; yazdırın veya bağlantıyla paylaşın.'],
            ['Maliyete geçin', 'Planınızdan maliyet hesaplayıcısına geçin; oradan keşif ve teklif isteyebilirsiniz.'],
        ],
        'room' => [
            ['Oda türünü ve stili seçin', 'Salon, yatak odası, mutfak gibi oda türünü ve beğendiğiniz iç mekân stilini seçin.'],
            ['Örnek görsel üretin', 'Yapay zekâ seçiminize göre örnek bir iç mekân görseli üretir (özellik açıkken). Görsel fikir vermek içindir, proje çizimi değildir.'],
            ['Stilleri karşılaştırın', 'Farklı stilleri deneyip hangisinin size uygun olduğuna karar verin.'],
            ['Maliyeti hesaplayın', 'Parke, zemin, seramik ve boya hesaplayıcılarıyla seçtiğiniz tarzın maliyetini görün.'],
        ],
        'takeoff' => [
            ['Planı yükleyin', 'Kat planınızı PDF (ilk sayfa), JPG veya PNG olarak yükleyin; dosya tarayıcınızda açılır.'],
            ['Ölçeği ayarlayın', 'Ölçüsünü bildiğiniz bir duvarın iki ucuna tıklayın ve gerçek uzunluğunu metre olarak yazın.'],
            ['Alanları ölçün', 'Odaların köşelerine sırayla tıklayın, başlangıç noktasına tıklayarak kapatın; alan m² olarak listeye eklenir.'],
            ['Teklif isteyin', 'Toplam alanı tek formla gönderip uygulama teklifi isteyebilirsiniz.'],
        ],
        'color' => [
            ['Rengi seçin', 'Doğrulanmış renk kataloglarındaki tonları inceleyin.'],
            ['Fotoğrafınızda görün', 'Seçtiğiniz tonu oda veya cephe fotoğrafınız üzerinde yaklaşık olarak önizleyin.'],
            ['Karşılaştırın', 'Birkaç tonu yan yana deneyip kararınızı verin.'],
            ['Maliyeti hesaplayın', 'Boya hesaplayıcısında bu iş için malzeme ve ustalık maliyetini görün.'],
        ],
        'ai' => [
            ['Dosyanızı yükleyin', 'PDF, görsel veya CSV proje dosyanızı ekleyin.'],
            ['Kalemleri çıkarın', 'İş kalemleri ve miktarlar okunur; okunamayan veya fiyatlanamayan kalemler ayrıca listelenir.'],
            ['Kontrol edin', 'Kalemleri ve miktarları kontrol edin. Sonuç bir ön analizdir, keşif yerine geçmez.'],
            ['Excel\'e aktarın', 'Sonucu Excel\'e aktarıp kendi keşfinizde düzenleyin.'],
        ],
        default => [
            ['Bilgileri girin', 'Gerekli birkaç değeri yazın ya da seçin.'],
            ['Sonucu anında görün', 'Hesap güncel mevzuat ve kabul görmüş formüllerle yapılır.'],
            ['Kaydedin, paylaşın', 'Sonucu yazdırın, PDF alın veya bağlantıyla paylaşın.'],
            ['Karar verin', 'Sonuç bilgilendirme amaçlıdır; önemli kararlarda uzman görüşü alın.'],
        ],
    };
    $why = [
        ['Ücretsiz, üyeliksiz', 'Hesaplamak için kayıt veya ödeme gerekmez.'],
        ['Güncel fiyat', 'Resmî poz fiyatları ve piyasa araştırması; fiyat tarihi raporda yazar.'],
        ['Telefonunuz sizde kalır', 'İzniniz olmadan kimseyle paylaşılmaz.'],
        ['Kararınız sizin', 'Sonuç yol gösterir; teklif almak zorunlu değildir.'],
    ];
@endphp
<section class="ws-how" aria-labelledby="wsHow-{{ $tool }}" data-kainta-auto-link="off">
    <div class="ws-how-head">
        <span class="ws-how-kicker">Nasıl çalışır?</span>
        <h2 id="wsHow-{{ $tool }}">{{ $title }}: 4 adımda sonuç</h2>
    </div>
    <ol class="ws-how-steps">
        @foreach ($steps as $i => [$h, $p])
            <li><span class="n">{{ $i + 1 }}</span><b>{{ $h }}</b><p>{{ $p }}</p></li>
        @endforeach
    </ol>
    @if ($kind !== 'eng')
    <div class="ws-how-why" aria-label="Neden Worksitem?">
        <span class="ws-how-whyt">Neden Worksitem?</span>
        @foreach ($why as [$h, $p])<span class="ws-how-chip"><b>{{ $h }}</b> · {{ $p }}</span>@endforeach
    </div>
    @endif
    <div class="ws-how-aud">
        @if (\App\Support\Pilot::firmSignupOpen())<a href="{{ route('firms.page', ['arac' => $tool]) }}">Usta veya firma mısınız? Bu işler size gelsin →</a>@endif
        <a href="{{ route('sponsor.page', ['arac' => $tool]) }}">Marka mısınız? Bu araçta görünün →</a>
    </div>
</section>
@once
<style>
.ws-how{margin:26px 0 0;padding:22px;border:1px solid #E3E6EA;border-radius:18px;background:#fff;text-align:left;font-family:Inter,-apple-system,'Segoe UI',sans-serif;color:#101318}
.ws-how-kicker{display:inline-block;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A6200}
.ws-how h2{margin:4px 0 14px;font-size:20px;line-height:1.3}
.ws-how-steps{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;counter-reset:none}
.ws-how-steps li{border:1px solid #EEF0F3;border-radius:14px;padding:14px;background:#FAFAFB}
.ws-how-steps .n{display:inline-flex;width:28px;height:28px;border-radius:9px;background:#FFB800;color:#101318;font-weight:900;align-items:center;justify-content:center;margin-bottom:8px}
.ws-how-steps b{display:block;font-size:15px;margin-bottom:4px}
.ws-how-steps p{margin:0;font-size:13.5px;line-height:1.55;color:#5B6472}
.ws-how-why{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:14px}
.ws-how-whyt{font-weight:800;font-size:13px;margin-right:4px}
.ws-how-chip{font-size:12.5px;color:#4A515C;background:#FFF8E1;border-radius:999px;padding:5px 11px}
.ws-how-chip b{color:#101318}
.ws-how-aud{display:flex;flex-wrap:wrap;gap:8px 18px;margin-top:14px;font-size:13.5px}
.ws-how-aud a{color:#101318;font-weight:700;text-decoration:none;border-bottom:1.5px solid #FFB800}
@media (max-width:900px){.ws-how-steps{grid-template-columns:1fr 1fr}}
@media (max-width:560px){.ws-how{padding:18px}.ws-how-steps{grid-template-columns:1fr}}
html[data-theme="dark"] .ws-how{background:#141A24;border-color:#232C3A;color:#E6E9EE}
html[data-theme="dark"] .ws-how-steps li{background:#0F141D;border-color:#232C3A}
html[data-theme="dark"] .ws-how-steps p,html[data-theme="dark"] .ws-how-chip{color:#C9CFD8}
html[data-theme="dark"] .ws-how-chip{background:#2A2410}
html[data-theme="dark"] .ws-how-chip b,html[data-theme="dark"] .ws-how-aud a{color:#fff}
@media print{.ws-how{display:none}}
</style>
@endonce
