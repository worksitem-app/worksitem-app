@php
$systemsJson = json_encode(
    $systems,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

if (!$systemsJson) {
    $systemsJson = '{}';
}
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sandviç Panel Çatı ve Cephe Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="Fabrika ve depolar için sandviç panel çatı ve cephe metrajı hesaplayın. PU, PIR, EPS ve taşyünü çatı panellerinde doğrulanmış 2026 YFK referans maliyetlerini görün.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/sandvic-panel-hesaplayici">

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
    --blue:#101318;
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
        radial-gradient(circle at 50% -8%,rgba(255,184,0,.09),transparent 30%),
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
    align-items:center;
    justify-content:space-between;
}

.brand{
    font-size:18px;
    font-weight:950;
}

.brand span{color:var(--yellow2)}

.back{
    font-size:13px;
    font-weight:850;
    color:var(--muted);
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
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:999px;
    color:#854d0e;
    font-size:12px;
    font-weight:900;
    margin-bottom:16px;
}

.hero h1{
    max-width:870px;
    margin:0 auto 14px;
    font-size:clamp(32px,5vw,54px);
    line-height:1.03;
    letter-spacing:-2px;
}

.hero p{
    max-width:770px;
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
    border:1px solid var(--line);
    border-radius:var(--radius);
    background:#fff;
    box-shadow:var(--shadow);
}

.card h2{
    margin:0 0 6px;
    font-size:22px;
}

.card-sub{
    margin-bottom:23px;
    color:var(--muted);
    font-size:13px;
    line-height:1.6;
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
    color:var(--text);
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
    line-height:1.55;
}

.system-note{
    margin-top:8px;
    padding:11px 12px;
    background:#f8fafc;
    border:1px solid var(--line);
    border-radius:11px;
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
}

.divider{
    margin:23px 0;
    border:0;
    border-top:1px solid var(--line);
}

.section-title{
    font-size:14px;
    font-weight:950;
    margin-bottom:14px;
}

.roof-fields,
.facade-fields{
    display:none;
}

.roof-fields.show,
.facade-fields.show{
    display:block;
}

.btn{
    width:100%;
    min-height:54px;
    border:0;
    border-radius:13px;
    background:var(--yellow);
    font-size:15px;
    font-weight:950;
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
    background:var(--dark);
    color:#fff;
    border-radius:16px;
    margin-bottom:17px;
}

.result-label{
    color:#b9bec7;
    font-size:11px;
    font-weight:850;
    text-transform:uppercase;
    letter-spacing:.7px;
}

.result-system{
    margin-top:7px;
    font-size:19px;
    font-weight:950;
    line-height:1.35;
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
    border:1px solid var(--line);
    border-radius:15px;
    overflow:hidden;
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

.source{
    margin-top:15px;
    padding:14px;
    background:#f8fafc;
    border:1px solid var(--line);
    border-radius:13px;
    color:var(--muted);
    font-size:11px;
    line-height:1.65;
}

.source strong{color:var(--text)}

.warning{
    display:none;
    margin-top:13px;
    padding:13px 14px;
    border:1px solid #fed7aa;
    border-radius:12px;
    background:#fff7ed;
    color:var(--orange);
    font-size:12px;
    line-height:1.6;
}

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
    border:1px solid var(--line);
    border-radius:11px;
    background:#fff;
    font-weight:850;
    cursor:pointer;
}

.actions .yes{
    background:var(--dark);
    border-color:var(--dark);
    color:#fff;
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
    line-height:1.55;
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
  "name":"Sandviç Panel Çatı ve Cephe Hesaplayıcı",
  "url":"https://worksitem.com/araclar/sandvic-panel-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"Sandviç panel çatı ve cephe sistemleri için metraj ve doğrulanmış YFK referans maliyet hesaplama aracı."
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
    <x-calc.head />
</head>

<body class="wc" data-tool-slug="sandvic-panel-calculator">

<x-site.contech-header />


<div class="wc-wrap" style="padding-bottom:0">
    <x-calc.hero title="Sandviç panel metrajı ve maliyeti hesaplama" crumb="Sandviç panel">
        Fabrika, depo ve sanayi yapılarında sandviç panel metrajınızı hesaplayın. Çatı sistemlerinde doğrulanmış 2026-08 YFK referans maliyetlerini görün; cephe tarafında fiyat uydurmadan gerçek metrajınızı çıkarın.
    </x-calc.hero>
</div>


<main class="container">

<div class="layout">


<section class="card">

    <h2>Panel Projesi</h2>

    <div class="card-sub">
        Çatı veya cephe uygulamasını seçin.
        Çatı fiyatları mevcut taşıyıcı aşıklar üzerine
        yapılan YFK panel imalatlarına dayanır.
    </div>


    <div class="field">

        <label>Uygulama</label>

        <select id="application">
            <option value="roof">Sandviç Panel Çatı</option>
            <option value="facade">Sandviç Panel Cephe</option>
        </select>

    </div>


    <div class="field">

        <label>Panel Sistemi</label>

        <select id="system"></select>

        <div class="system-note" id="systemNote">
            -
        </div>

    </div>


    <div class="field">

        <label>Proje Tipi</label>

        <select id="projectType">
            <option value="fabrika">Fabrika / Üretim Tesisi</option>
            <option value="depo">Depo / Lojistik Tesisi</option>
            <option value="atolye">Atölye</option>
            <option value="ticari">Ticari Yapı</option>
            <option value="tarim">Tarım / Hayvancılık Yapısı</option>
            <option value="diger">Diğer</option>
        </select>

    </div>


    <hr class="divider">


    <div class="roof-fields" id="roofFields">

        <div class="section-title">
            Çatı Metrajı
        </div>


        <div class="field">

            <label>Gerçek Çatı Alanı (m²)</label>

            <input
                type="number"
                id="roofArea"
                min="0"
                step="0.01"
                placeholder="Biliyorsanız doğrudan girin"
            >

            <div class="hint">
                Bu değer girilirse aşağıdaki geometrik hesap yerine
                doğrudan gerçek çatı alanı kullanılır.
            </div>

        </div>


        <div class="form-grid">

            <div class="field">
                <label>Bina Uzunluğu (m)</label>
                <input type="number" id="buildingLength" min="0" step="0.01">
            </div>

            <div class="field">
                <label>Bina Genişliği (m)</label>
                <input type="number" id="buildingWidth" min="0" step="0.01">
            </div>

            <div class="field">
                <label>Çatı Eğimi (%)</label>
                <input type="number" id="roofPitch" min="0" max="100" step="0.1" value="10">
            </div>

            <div class="field">
                <label>Saçak Payı (m)</label>
                <input type="number" id="eave" min="0" step="0.01" value="0">
            </div>

        </div>


        <div class="field">

            <label>Mevcut Taşıyıcı Aşık Sistemi</label>

            <select id="purlins">
                <option value="yes">Mevcut / Uygun</option>
                <option value="no">Yeni aşık / alt konstrüksiyon gerekli</option>
                <option value="unknown">Bilmiyorum</option>
            </select>

        </div>

    </div>


    <div class="facade-fields" id="facadeFields">

        <div class="section-title">
            Cephe Metrajı
        </div>


        <div class="field">

            <label>Net Cephe Alanı (m²)</label>

            <input
                type="number"
                id="facadeArea"
                min="0"
                step="0.01"
                placeholder="Biliyorsanız doğrudan girin"
            >

            <div class="hint">
                Girilirse bina çevresi hesabı yerine doğrudan
                bu cephe alanı kullanılır.
            </div>

        </div>


        <div class="form-grid">

            <div class="field">
                <label>Bina Uzunluğu (m)</label>
                <input type="number" id="facadeLength" min="0" step="0.01">
            </div>

            <div class="field">
                <label>Bina Genişliği (m)</label>
                <input type="number" id="facadeWidth" min="0" step="0.01">
            </div>

            <div class="field">
                <label>Cephe Yüksekliği (m)</label>
                <input type="number" id="facadeHeight" min="0" step="0.01">
            </div>

            <div class="field">
                <label>Kapı / Pencere Boşluğu (m²)</label>
                <input type="number" id="openings" min="0" step="0.01" value="0">
            </div>

        </div>

        <div class="hint">
            Geometrik hesap tüm bina çevresini:
            2 × (uzunluk + genişlik) × yükseklik olarak hesaplar
            ve açıklıkları düşer.
        </div>

    </div>


    <hr class="divider">


    <div class="form-grid">

        <div class="field">

            <label>Malzeme Fire / Kesim Payı (%)</label>

            <input
                type="number"
                id="waste"
                min="0"
                max="25"
                step="0.5"
                value="5"
            >

        </div>


        <div class="field">

            <label>Mevcut Panel Sökümü</label>

            <select id="demolition">
                <option value="no">Yok</option>
                <option value="yes">Var</option>
            </select>

        </div>

    </div>


    <button class="btn" id="calculateBtn">
        Panel Metrajını ve Maliyeti Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result" data-wc-result>

    <div class="result-top">

        <div class="result-label">
            Panel sistemi
        </div>

        <div class="result-system" id="rSystem">
            -
        </div>

        <div class="total" id="rTotal" data-wc-price>
            -
        </div>

        <div class="total-sub" id="rTotalSub">
            -
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Net uygulama alanı</span>
            <strong id="rNet">-</strong>
        </div>

        <div class="row">
            <span>Fire dahil sipariş alanı</span>
            <strong id="rOrder">-</strong>
        </div>

        <div class="row" id="slopeRow">
            <span>Çatı eğim katsayısı</span>
            <strong id="rSlope">-</strong>
        </div>

        <div class="row">
            <span>Referans poz</span>
            <strong id="rPoz">-</strong>
        </div>

        <div class="row">
            <span>YFK referans birim fiyatı</span>
            <strong id="rUnit">-</strong>
        </div>

        <div class="row">
            <span>Fiyat dönemi</span>
            <strong id="rPeriod">-</strong>
        </div>

    </div>


    <div class="source" id="sourceBox">
        -
    </div>


    <div class="warning" id="warningBox"></div>

</div>


<div class="budget" id="budgetBox">

    <div class="budget-title">
        Bu proje için Kainta İnşaat'tan net teklif ister misiniz?
    </div>

    <div class="budget-text">
        Referans bütçe veya metraj projenize uygunsa;
        çatı, cephe, yalıtım ve fabrika tadilat kapsamını
        birlikte değerlendirebiliriz.
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
            href="/maliyet-hesapla?service=sandvic-panel"
            id="quoteLink"
        >
            Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
        </a>

    </div>


    <div class="no-interest" id="noInterest">
        İletişim bilginizi almıyoruz.
        Projeniz netleştiğinde tekrar hesaplayabilirsiniz.
    </div>

</div>

</section>

</div>


<section class="info-grid">

<article class="info">

    <strong>Çatı fiyatı mevcut aşık sistemine göredir</strong>

    <p>
        YFK panel pozları mevcut ahşap, betonarme veya
        çelik aşıklar üzerine yapılan çatı örtüsü imalatını
        tarif eder. Yeni çelik konstrüksiyon gerektiğinde
        bunun bedeli ayrıca hesaplanmalıdır.
    </p>

</article>


<article class="info">

    <strong>Cephe fiyatı uydurulmuyor</strong>

    <p>
        Cephe için doğrulanmış uygun poz fiyatı sisteme
        bağlanana kadar yalnızca panel metrajı gösterilir.
        Çatı fiyatı cepheye taşınmaz.
    </p>

</article>


<article class="info">

    <strong>Fire ayrı gösterilir</strong>

    <p>
        Kesim ve sipariş fire payı panel satın alma alanına
        uygulanır. YFK tamamlanmış imalat referans hesabı
        net uygulama alanı üzerinden yapılır.
    </p>

</article>

</section>

</main>


<script>

const SYSTEMS = {!! $systemsJson !!};

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
        maximumFractionDigits:3
    }
);


function numeric(id) {

    const el =
        document.getElementById(id);

    if (!el) {
        return 0;
    }

    const n =
        parseFloat(el.value);

    return Number.isFinite(n)
        ? n
        : 0;
}


function refreshFields() {

    const application =
        document.getElementById('application').value;

    document.getElementById('roofFields')
        .classList.toggle(
            'show',
            application === 'roof'
        );

    document.getElementById('facadeFields')
        .classList.toggle(
            'show',
            application === 'facade'
        );

    refreshSystems();
}


function refreshSystems() {

    const application =
        document.getElementById('application').value;

    const select =
        document.getElementById('system');

    const old =
        select.value;

    select.innerHTML = '';


    Object.entries(SYSTEMS)
        .forEach(([key, system]) => {

            if (
                system.application !== application
            ) {
                return;
            }

            const option =
                document.createElement('option');

            option.value =
                key;

            option.textContent =
                system.label +
                (
                    system.price_found
                    ? ' · YFK'
                    : ' · Metraj'
                );

            select.appendChild(option);
        });


    if (
        old &&
        Array.from(select.options)
            .some(option => option.value === old)
    ) {

        select.value =
            old;
    }

    updateSystemNote();
}


function updateSystemNote() {

    const key =
        document.getElementById('system').value;

    const system =
        SYSTEMS[key];

    document.getElementById('systemNote')
        .innerText =
            system
            ? system.note
            : '-';
}


function calculateRoofArea() {

    const direct =
        numeric('roofArea');

    if (direct > 0) {

        return {
            area:direct,
            slope:null,
            calculated:false
        };
    }


    const length =
        numeric('buildingLength');

    const width =
        numeric('buildingWidth');

    const pitch =
        Math.max(
            0,
            numeric('roofPitch')
        );

    const eave =
        Math.max(
            0,
            numeric('eave')
        );


    if (
        length <= 0 ||
        width <= 0
    ) {

        return {
            area:0,
            slope:null,
            calculated:true
        };
    }


    /*
     * Basit ön hesap:
     * Gerçek eğimli alan =
     * yatay izdüşüm × sqrt(1 + eğim^2)
     *
     * Eğim yüzde olarak girilir.
     */

    const slopeMultiplier =
        Math.sqrt(
            1 +
            Math.pow(
                pitch / 100,
                2
            )
        );


    const projectedArea =
        (length + 2 * eave) *
        (width + 2 * eave);


    return {
        area:
            projectedArea *
            slopeMultiplier,

        slope:
            slopeMultiplier,

        calculated:true
    };
}


function calculateFacadeArea() {

    const direct =
        numeric('facadeArea');

    if (direct > 0) {

        return {
            area:direct,
            calculated:false
        };
    }


    const length =
        numeric('facadeLength');

    const width =
        numeric('facadeWidth');

    const height =
        numeric('facadeHeight');

    const openings =
        Math.max(
            0,
            numeric('openings')
        );


    const gross =
        2 *
        (length + width) *
        height;


    return {
        area:
            Math.max(
                0,
                gross - openings
            ),

        calculated:true
    };
}


function calculate() {

    const application =
        document.getElementById('application').value;

    const systemKey =
        document.getElementById('system').value;

    const system =
        SYSTEMS[systemKey];


    if (!system) {

        alert(
            'Lütfen panel sistemini seçin.'
        );

        return;
    }


    let resultData;

    if (application === 'roof') {

        resultData =
            calculateRoofArea();

    } else {

        resultData =
            calculateFacadeArea();
    }


    const netArea =
        resultData.area;


    if (
        !netArea ||
        netArea <= 0
    ) {

        alert(
            'Lütfen hesaplama için geçerli ölçü veya alan girin.'
        );

        return;
    }


    let waste =
        numeric('waste');

    waste =
        Math.max(
            0,
            Math.min(
                25,
                waste
            )
        );


    const orderArea =
        netArea *
        (1 + waste / 100);


    document.getElementById('rSystem')
        .innerText =
            system.label;


    document.getElementById('rNet')
        .innerText =
            number.format(netArea) +
            ' m²';


    document.getElementById('rOrder')
        .innerText =
            number.format(orderArea) +
            ' m²';


    const slopeRow =
        document.getElementById('slopeRow');


    if (
        application === 'roof' &&
        resultData.slope !== null
    ) {

        slopeRow.style.display =
            'flex';

        document.getElementById('rSlope')
            .innerText =
                number.format(
                    resultData.slope
                );

    } else {

        slopeRow.style.display =
            'none';
    }


    document.getElementById('rPoz')
        .innerText =
            system.poz ||
            'Doğrulanmış cephe pozu bekleniyor';


    if (
        system.price_found &&
        system.unit_price !== null
    ) {

        const unitPrice =
            parseFloat(
                system.unit_price
            );


        /*
         * YFK tamamlanmış imalat bedeli:
         * net uygulama alanı × birim fiyat
         *
         * Fire tekrar YFK parasal toplamına bindirilmez.
         */

        const referenceTotal =
            netArea *
            unitPrice;


        document.getElementById('rTotal')
            .innerText =
                money.format(
                    referenceTotal
                ) +
                ' TL';


        document.getElementById('rTotalSub')
            .innerText =
                '2026-08 YFK referans yaklaşık çatı imalat tutarı';


        document.getElementById('rUnit')
            .innerText =
                money.format(unitPrice) +
                ' TL/m²';


        document.getElementById('rPeriod')
            .innerText =
                system.price_period ||
                '2026-08';


        document.getElementById('sourceBox')
            .innerHTML =
                '<strong>Kaynak:</strong> ' +
                'YFK 2026-08 · Poz ' +
                system.poz +
                (
                    system.source_page
                    ? ' · Kaynak PDF sayfa ' +
                      system.source_page
                    : ''
                ) +
                '<br>' +
                system.note;

    } else {

        document.getElementById('rTotal')
            .innerText =
                'Metraj hazır';


        document.getElementById('rTotalSub')
            .innerText =
                'Cephe için doğrulanmış fiyat uydurulmadı';


        document.getElementById('rUnit')
            .innerText =
                'Doğrulanmış fiyat yok';


        document.getElementById('rPeriod')
            .innerText =
                '-';


        document.getElementById('sourceBox')
            .innerHTML =
                '<strong>Fiyat durumu:</strong> ' +
                system.note;
    }


    const warnings = [];


    if (
        application === 'roof'
    ) {

        const purlins =
            document.getElementById('purlins').value;

        if (purlins === 'no') {

            warnings.push(
                'Yeni aşık veya alt konstrüksiyon gerekli seçildi. ' +
                'YFK panel referans bedeli yeni taşıyıcı sistemi içermez.'
            );
        }

        if (purlins === 'unknown') {

            warnings.push(
                'Mevcut aşık sisteminin panel uygulamasına uygunluğu saha keşfinde doğrulanmalıdır.'
            );
        }

        if (
            resultData.calculated &&
            resultData.slope !== null
        ) {

            warnings.push(
                'Çatı alanı basit geometrik eğim hesabıyla çıkarıldı. ' +
                'Mahya, dere, fener, parapet ve özel çatı geometrileri ayrıca kontrol edilmelidir.'
            );
        }
    }


    if (
        application === 'facade'
    ) {

        warnings.push(
            'Cephe için doğrulanmış YFK birim fiyatı bağlanmadığından yalnızca metraj gösterilmektedir.'
        );
    }


    if (
        document.getElementById('demolition').value
        === 'yes'
    ) {

        warnings.push(
            'Mevcut panel sökümü seçildi. ' +
            'Doğrulanmış söküm fiyatı bulunmadığından referans toplama eklenmedi.'
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
        '?service=sandvic-panel' +
        '&area=' +
        encodeURIComponent(
            netArea.toFixed(2)
        ) +
        '&application=' +
        encodeURIComponent(application) +
        '&system=' +
        encodeURIComponent(systemKey) +
        '&project_type=' +
        encodeURIComponent(projectType);


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


document.getElementById('application')
    .addEventListener(
        'change',
        refreshFields
    );


document.getElementById('system')
    .addEventListener(
        'change',
        updateSystemNote
    );


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


refreshFields();

</script>

<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
