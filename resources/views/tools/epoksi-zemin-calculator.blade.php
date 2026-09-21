@php
$layersJson = json_encode(
    $layers,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

if (!$layersJson) {
    $layersJson = '{}';
}
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Epoksi Zemin Maliyeti Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="Fabrika, depo, otopark ve ticari alanlar için 2,5 mm self-leveling epoksi zemin, tesviye ve UV son kat metraj ve 2026 YFK referans maliyetini hesaplayın.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/epoksi-zemin-hesaplayici">

<style>
:root{
    --bg:#f5f6f8;
    --surface:#fff;
    --text:#101318;
    --muted:#68707d;
    --line:#e2e5e9;
    --yellow:#ffb800;
    --yellow2:#df9f00;
    --dark:#111318;
    --green:#166534;
    --orange:#9a3412;
    --radius:20px;
    --shadow:0 18px 55px rgba(15,23,42,.08);
}

*{box-sizing:border-box}

html{scroll-behavior:smooth}

body{
    margin:0;
    font-family:Inter,Arial,sans-serif;
    color:var(--text);
    background:
        radial-gradient(circle at 50% -8%,rgba(255,184,0,.10),transparent 28%),
        var(--bg);
}

a{text-decoration:none;color:inherit}

.header{
    height:68px;
    display:flex;
    align-items:center;
    border-bottom:1px solid var(--line);
    background:rgba(255,255,255,.95);
    position:sticky;
    top:0;
    z-index:50;
}

.header-inner{
    width:min(1120px,calc(100% - 32px));
    margin:auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.brand{
    font-weight:950;
    font-size:18px;
}

.brand span{color:var(--yellow2)}

.back{
    color:var(--muted);
    font-size:13px;
    font-weight:850;
}

.container{
    width:min(1120px,calc(100% - 32px));
    margin:auto;
}

.hero{
    padding:58px 0 34px;
    text-align:center;
}

.kicker{
    display:inline-flex;
    padding:7px 12px;
    border:1px solid #fde68a;
    border-radius:999px;
    background:#fffbeb;
    color:#854d0e;
    font-size:12px;
    font-weight:900;
    margin-bottom:16px;
}

.hero h1{
    max-width:850px;
    margin:0 auto 14px;
    font-size:clamp(32px,5vw,54px);
    line-height:1.03;
    letter-spacing:-2px;
}

.hero p{
    max-width:760px;
    margin:auto;
    color:var(--muted);
    line-height:1.7;
}

.layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(360px,.88fr);
    gap:24px;
    align-items:start;
    padding-bottom:65px;
}

.card{
    padding:26px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
}

.card h2{
    margin:0 0 6px;
    font-size:22px;
}

.card-sub{
    color:var(--muted);
    font-size:13px;
    line-height:1.6;
    margin-bottom:23px;
}

.field{
    margin-bottom:16px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

label{
    display:block;
    margin-bottom:7px;
    font-size:12px;
    font-weight:850;
    color:#343a46;
}

input,select{
    width:100%;
    min-height:50px;
    padding:0 13px;
    border:1px solid var(--line);
    border-radius:12px;
    background:#fff;
    font:inherit;
    outline:none;
}

input:focus,select:focus{
    border-color:#dca400;
    box-shadow:0 0 0 3px rgba(255,184,0,.12);
}

.hint{
    margin-top:6px;
    color:var(--muted);
    font-size:11px;
    line-height:1.5;
}

.divider{
    border:0;
    border-top:1px solid var(--line);
    margin:23px 0;
}

.subhead{
    font-size:14px;
    font-weight:950;
    margin-bottom:14px;
}

.check{
    display:flex;
    align-items:flex-start;
    gap:11px;
    padding:14px;
    margin-bottom:11px;
    border:1px solid var(--line);
    border-radius:13px;
    cursor:pointer;
}

.check input{
    width:18px;
    min-height:18px;
    height:18px;
    margin-top:2px;
    flex:0 0 18px;
}

.check strong{
    display:block;
    font-size:13px;
    margin-bottom:3px;
}

.check span{
    display:block;
    color:var(--muted);
    font-size:11px;
    line-height:1.5;
}

.check.required{
    background:#f8fafc;
}

.btn{
    width:100%;
    min-height:54px;
    border:0;
    border-radius:13px;
    background:var(--yellow);
    color:#111;
    font-weight:950;
    font-size:15px;
    cursor:pointer;
}

.btn:hover{background:#ffc329}

.result{
    display:none;
}

.result.show{
    display:block;
}

.result-top{
    padding:22px;
    border-radius:16px;
    background:var(--dark);
    color:#fff;
    margin-bottom:17px;
}

.result-label{
    color:#b7bdc7;
    font-size:11px;
    font-weight:850;
    text-transform:uppercase;
    letter-spacing:.7px;
}

.result-system{
    margin-top:7px;
    font-size:20px;
    font-weight:950;
}

.total{
    margin-top:17px;
    font-size:34px;
    font-weight:950;
    letter-spacing:-1.3px;
}

.total-sub{
    margin-top:5px;
    color:#b8bec8;
    font-size:11px;
}

.rows{
    overflow:hidden;
    border:1px solid var(--line);
    border-radius:15px;
}

.row{
    display:flex;
    justify-content:space-between;
    gap:18px;
    padding:13px 15px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.row:last-child{border-bottom:0}

.row span{color:var(--muted)}
.row strong{text-align:right}

.layers{
    margin-top:16px;
}

.layer{
    padding:14px;
    margin-bottom:9px;
    border:1px solid var(--line);
    border-radius:13px;
    background:#f8fafc;
}

.layer-top{
    display:flex;
    justify-content:space-between;
    gap:15px;
    font-size:12px;
}

.layer-name{
    font-weight:900;
}

.layer-cost{
    font-weight:950;
    white-space:nowrap;
}

.layer-meta{
    margin-top:5px;
    color:var(--muted);
    font-size:10px;
    line-height:1.5;
}

.warning{
    display:none;
    margin-top:14px;
    padding:13px 14px;
    border:1px solid #fed7aa;
    background:#fff7ed;
    color:var(--orange);
    border-radius:12px;
    font-size:12px;
    line-height:1.6;
}

.source{
    margin-top:14px;
    padding:13px 14px;
    border:1px solid var(--line);
    background:#f8fafc;
    border-radius:12px;
    color:var(--muted);
    font-size:11px;
    line-height:1.6;
}

.source strong{color:var(--text)}

.budget{
    display:none;
    margin-top:18px;
    padding:20px;
    border:1px solid var(--line);
    border-radius:16px;
    background:#fff;
    text-align:center;
}

.budget-title{
    font-size:18px;
    font-weight:950;
}

.budget-text{
    margin:8px 0 15px;
    color:var(--muted);
    font-size:12px;
    line-height:1.6;
}

.actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.actions button{
    min-height:46px;
    border-radius:11px;
    border:1px solid var(--line);
    background:#fff;
    font-weight:850;
    cursor:pointer;
}

.actions .yes{
    background:var(--dark);
    color:#fff;
    border-color:var(--dark);
}

.quote{
    display:none;
    margin-top:14px;
}

.quote a{
    display:flex;
    min-height:54px;
    align-items:center;
    justify-content:center;
    padding:10px 15px;
    background:var(--yellow);
    border-radius:12px;
    font-size:14px;
    font-weight:950;
}

.no-interest{
    display:none;
    margin-top:12px;
    color:var(--muted);
    font-size:12px;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:16px;
    margin-bottom:70px;
}

.info{
    padding:20px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
}

.info strong{
    display:block;
    margin-bottom:7px;
}

.info p{
    margin:0;
    color:var(--muted);
    font-size:12px;
    line-height:1.65;
}

@media(max-width:850px){
    .layout{grid-template-columns:1fr}
}

@media(max-width:620px){
    .form-grid,
    .actions,
    .info-grid{
        grid-template-columns:1fr;
    }

    .hero{padding-top:38px}
    .card{padding:20px}
}
</style>

<script type="application/ld+json">
{
  "@@context":"https://schema.org",
  "@@type":"WebApplication",
  "name":"Epoksi Zemin Maliyeti Hesaplayıcı",
  "url":"https://worksitem.com/araclar/epoksi-zemin-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"Endüstriyel epoksi zemin kaplamaları için metraj ve doğrulanmış YFK referans maliyet hesaplama aracı."
}
</script>

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-tool-slug="epoksi-zemin-calculator">

<x-site.contech-header />


<section class="hero">

<div class="container">

    <div class="kicker">
        CONTECH · ENDÜSTRİYEL ZEMİN
    </div>

    <h1>
        Epoksi Zemin Metraj ve
        Maliyet Hesaplayıcı
    </h1>

    <p>
        Fabrika, depo, otopark ve ticari alanlar için
        2,5 mm self-leveling epoksi kaplama maliyetini;
        gerekli ise zemin tesviyesi ve UV dayanımlı
        son kat ile birlikte hesaplayın.
    </p>

</div>

</section>


<main class="container">

<div class="layout">


<section class="card">

    <h2>Epoksi Uygulaması</h2>

    <div class="card-sub">
        Uygulama alanınızı ve zeminin mevcut durumunu
        girin. Doğrulanmış poz bulunmayan hazırlık
        işlemleri maliyete eklenmez.
    </div>


    <div class="field">

        <label>Proje / Mekân Tipi</label>

        <select id="projectType">
            <option value="fabrika">Fabrika / Üretim Tesisi</option>
            <option value="depo">Depo / Lojistik Tesisi</option>
            <option value="otopark">Otopark</option>
            <option value="ticari">Mağaza / Ticari Alan</option>
            <option value="ofis">Ofis / Teknik Alan</option>
            <option value="diger">Diğer</option>
        </select>

    </div>


    <div class="field">

        <label>Epoksi Uygulama Alanı (m²)</label>

        <input
            type="number"
            id="area"
            min="0"
            step="0.01"
            placeholder="Örn. 1250"
        >

    </div>


    <div class="form-grid">

        <div class="field">
            <label>Uzunluk (m)</label>
            <input type="number" id="length" min="0" step="0.01">
        </div>

        <div class="field">
            <label>Genişlik (m)</label>
            <input type="number" id="width" min="0" step="0.01">
        </div>

    </div>

    <div class="hint">
        Alan kutusu doluysa doğrudan alan değeri kullanılır.
        Aksi halde uzunluk × genişlik hesaplanır.
    </div>


    <hr class="divider">


    <div class="subhead">
        Sistem Katmanları
    </div>


    <label class="check required">

        <input
            type="checkbox"
            id="epoxy"
            checked
            disabled
        >

        <span>
            <strong>2,5 mm Self-Leveling Epoksi</strong>
            Ana epoksi kaplama katmanı zorunludur.
        </span>

    </label>


    <label class="check">

        <input
            type="checkbox"
            id="leveling"
        >

        <span>
            <strong>Zemin Tesviyesi Ekle</strong>
            Ortalama 2 mm self-leveling tesviye katmanı.
        </span>

    </label>


    <label class="check">

        <input
            type="checkbox"
            id="uvTopcoat"
        >

        <span>
            <strong>UV Dayanımlı Poliüretan Son Kat Ekle</strong>
            Epoksi üzerine doğrulanmış YFK son kat kaplaması.
        </span>

    </label>


    <hr class="divider">


    <div class="subhead">
        Yüzey Hazırlığı
    </div>


    <div class="form-grid">

        <div class="field">

            <label>Zemin Taşlama Gerekli mi?</label>

            <select id="grinding">
                <option value="no">Hayır</option>
                <option value="yes">Evet</option>
            </select>

        </div>


        <div class="field">

            <label>Çatlak / Tamir Gerekiyor mu?</label>

            <select id="repair">
                <option value="no">Hayır</option>
                <option value="yes">Evet</option>
            </select>

        </div>

    </div>


    <button class="btn" id="calculateBtn">
        Epoksi Sistem Maliyetini Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result">

    <div class="result-top">

        <div class="result-label">
            YFK Referans Sistem Bütçesi
        </div>

        <div class="result-system">
            Endüstriyel Epoksi Zemin
        </div>

        <div class="total" id="rTotal">
            -
        </div>

        <div class="total-sub">
            Seçilen doğrulanmış imalat katmanlarının toplamı
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Uygulama alanı</span>
            <strong id="rArea">-</strong>
        </div>

        <div class="row">
            <span>Aktif katman sayısı</span>
            <strong id="rLayerCount">-</strong>
        </div>

        <div class="row">
            <span>Toplam sistem birim bedeli</span>
            <strong id="rSystemUnit">-</strong>
        </div>

        <div class="row">
            <span>Referans dönemi</span>
            <strong>2026-08</strong>
        </div>

    </div>


    <div class="layers" id="layerResults"></div>


    <div class="warning" id="warningBox"></div>


    <div class="source">

        <strong>Fiyat kaynağı:</strong>
        YFK 2026-08 doğrulanmış rayiç kayıtları.

        <br>

        Zemin taşlama, çatlak tamiri, lokal beton onarımı,
        nem bariyeri ve benzeri fiyatı doğrulanmamış
        hazırlık kalemleri referans toplamına eklenmez.

    </div>

</div>


<div class="budget" id="budgetBox">

    <div class="budget-title">
        Bu bütçe projeniz için uygun mu?
    </div>

    <div class="budget-text">
        Referans bütçe uygunsa Kainta İnşaat'tan
        saha koşulları, yüzey durumu ve uygulama
        detaylarına göre profesyonel net teklif alın.
    </div>

    <div class="actions">

        <button type="button" class="yes" id="budgetYes">
            ✓ Evet, teklif istiyorum
        </button>

        <button type="button" id="budgetNo">
            Sadece maliyete baktım
        </button>

    </div>


    <div class="quote" id="quoteBox">

        <a
            href="/maliyet-hesapla?service=epoksi-zemin"
            id="quoteLink"
        >
            Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
        </a>

    </div>


    <div class="no-interest" id="noInterest">
        Bu aşamada iletişim bilginizi almıyoruz.
        Projeniz netleştiğinde tekrar hesaplayabilirsiniz.
    </div>

</div>

</section>

</div>


<section class="info-grid">


<article class="info">

    <strong>Epoksi ana imalat ayrı pozdur</strong>

    <p>
        2,5 mm self-leveling epoksi kaplaması ana
        imalat olarak hesaplanır. İlave katmanlar
        seçilmediği sürece yalnızca bu poz kullanılır.
    </p>

</article>


<article class="info">

    <strong>Tesviye gerektiğinde ayrıca eklenir</strong>

    <p>
        Zeminin düzeltilmesi gerekiyorsa self-leveling
        tesviye katmanı ana epoksi imalatına ayrıca
        eklenir; aynı bedel içinde varsayılmaz.
    </p>

</article>


<article class="info">

    <strong>Saha keşfi kritik</strong>

    <p>
        Nem, beton dayanımı, çatlaklar, kot bozukluğu,
        eski kaplama ve yüzey hazırlığı gerçek epoksi
        teklifini ciddi biçimde etkileyebilir.
    </p>

</article>


</section>

</main>


<script>

const LAYERS = {!! $layersJson !!};

const money = new Intl.NumberFormat(
    'tr-TR',
    {
        minimumFractionDigits:2,
        maximumFractionDigits:2
    }
);

const number = new Intl.NumberFormat(
    'tr-TR',
    {
        maximumFractionDigits:2
    }
);


function numeric(id) {

    const n =
        parseFloat(
            document.getElementById(id).value
        );

    return Number.isFinite(n)
        ? n
        : 0;
}


function getArea() {

    const direct =
        numeric('area');

    if (direct > 0) {
        return direct;
    }

    return (
        numeric('length') *
        numeric('width')
    );
}


function addLayer(
    key,
    area,
    selected,
    results
) {

    if (!selected) {
        return;
    }

    const layer =
        LAYERS[key];

    if (!layer) {
        return;
    }

    if (
        !layer.price_found ||
        layer.unit_price === null
    ) {

        results.push({
            key:key,
            found:false,
            label:layer.label,
            poz:layer.poz,
            total:null,
            unitPrice:null
        });

        return;
    }

    const unitPrice =
        parseFloat(
            layer.unit_price
        );

    results.push({
        key:key,
        found:true,
        label:layer.label,
        poz:layer.poz,
        unitPrice:unitPrice,
        total:area * unitPrice
    });
}


function calculate() {

    const area =
        getArea();

    if (
        !area ||
        area <= 0
    ) {

        alert(
            'Lütfen epoksi uygulanacak alanı girin.'
        );

        return;
    }


    const selectedLayers = [];


    addLayer(
        'epoxy',
        area,
        true,
        selectedLayers
    );


    addLayer(
        'leveling',
        area,
        document.getElementById('leveling').checked,
        selectedLayers
    );


    addLayer(
        'uv_topcoat',
        area,
        document.getElementById('uvTopcoat').checked,
        selectedLayers
    );


    let grandTotal = 0;

    let systemUnit = 0;

    let allFound = true;


    selectedLayers.forEach(layer => {

        if (!layer.found) {

            allFound = false;

            return;
        }

        grandTotal +=
            layer.total;

        systemUnit +=
            layer.unitPrice;
    });


    const container =
        document.getElementById('layerResults');

    container.innerHTML = '';


    selectedLayers.forEach(layer => {

        const div =
            document.createElement('div');

        div.className =
            'layer';

        if (layer.found) {

            div.innerHTML =
                '<div class="layer-top">' +
                    '<div class="layer-name">' +
                        layer.label +
                    '</div>' +
                    '<div class="layer-cost">' +
                        money.format(layer.total) +
                        ' TL' +
                    '</div>' +
                '</div>' +

                '<div class="layer-meta">' +
                    'Poz ' +
                    layer.poz +
                    ' · ' +
                    money.format(layer.unitPrice) +
                    ' TL/m² × ' +
                    number.format(area) +
                    ' m²' +
                '</div>';

        } else {

            div.innerHTML =
                '<div class="layer-top">' +
                    '<div class="layer-name">' +
                        layer.label +
                    '</div>' +
                    '<div class="layer-cost">' +
                        'Fiyat yok' +
                    '</div>' +
                '</div>' +

                '<div class="layer-meta">' +
                    'Doğrulanmış fiyat bulunmadığı için toplama eklenmedi.' +
                '</div>';
        }

        container.appendChild(div);
    });


    document.getElementById('rArea')
        .innerText =
            number.format(area) +
            ' m²';


    document.getElementById('rLayerCount')
        .innerText =
            selectedLayers.length +
            ' katman';


    document.getElementById('rSystemUnit')
        .innerText =
            money.format(systemUnit) +
            ' TL/m²';


    document.getElementById('rTotal')
        .innerText =
            allFound
            ? money.format(grandTotal) + ' TL'
            : money.format(grandTotal) + ' TL + doğrulanmamış kalem';


    const warnings = [];


    if (
        document.getElementById('grinding').value
        === 'yes'
    ) {

        warnings.push(
            'Zemin taşlama gerekli seçildi. ' +
            'Doğrulanmış taşlama fiyatı bulunmadığından maliyete eklenmedi.'
        );
    }


    if (
        document.getElementById('repair').value
        === 'yes'
    ) {

        warnings.push(
            'Çatlak/tamir gerekli seçildi. ' +
            'Hasarın miktarı keşifle belirlenmesi gerektiğinden referans bütçeye eklenmedi.'
        );
    }


    if (!allFound) {

        warnings.push(
            'Seçilen katmanlardan en az biri için doğrulanmış birim fiyat bulunamadı.'
        );
    }


    const warningBox =
        document.getElementById('warningBox');


    if (warnings.length) {

        warningBox.style.display =
            'block';

        warningBox.innerText =
            warnings.join(' ');

    } else {

        warningBox.style.display =
            'none';

        warningBox.innerText =
            '';
    }


    const projectType =
        document.getElementById('projectType').value;


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=epoksi-zemin' +
        '&area=' +
        encodeURIComponent(
            area.toFixed(2)
        ) +
        '&project_type=' +
        encodeURIComponent(projectType) +
        '&leveling=' +
        (
            document.getElementById('leveling').checked
            ? '1'
            : '0'
        ) +
        '&uv=' +
        (
            document.getElementById('uvTopcoat').checked
            ? '1'
            : '0'
        );


    document.getElementById('quoteLink')
        .setAttribute(
            'href',
            quoteUrl
        );


    document.getElementById('quoteBox')
        .style.display =
            'none';


    document.getElementById('noInterest')
        .style.display =
            'none';


    document.getElementById('budgetBox')
        .style.display =
            'block';


    document.getElementById('result')
        .classList.add('show');


    document.getElementById('result')
        .scrollIntoView({
            behavior:'smooth',
            block:'start'
        });
}


document.getElementById('calculateBtn')
    .addEventListener(
        'click',
        calculate
    );


document.getElementById('budgetYes')
    .addEventListener(
        'click',
        function () {

            document.getElementById('quoteBox')
                .style.display =
                    'block';

            document.getElementById('noInterest')
                .style.display =
                    'none';
        }
    );


document.getElementById('budgetNo')
    .addEventListener(
        'click',
        function () {

            document.getElementById('quoteBox')
                .style.display =
                    'none';

            document.getElementById('noInterest')
                .style.display =
                    'block';
        }
    );

</script>

    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
