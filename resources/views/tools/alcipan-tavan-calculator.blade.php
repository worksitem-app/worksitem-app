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

<title>Alçıpan Bölme & Giydirme Duvar Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="Alçıpan bölme ve giydirme duvar metrajını; C50, C75, C100 profiller ve farklı levha tipleriyle 2026 YFK referans maliyetine göre hesaplayın.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/alcipan-hesaplayici">

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
    --orange:#9a3412;
    --radius:20px;
    --shadow:0 18px 55px rgba(15,23,42,.08);
}

*{box-sizing:border-box}

html{scroll-behavior:smooth}

body{
    margin:0;
    font-family:Inter,Arial,sans-serif;
    background:
        radial-gradient(circle at 50% -8%,rgba(255,184,0,.09),transparent 30%),
        var(--bg);
    color:var(--text);
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
    font-size:18px;
    font-weight:950;
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
    margin-bottom:23px;
    color:var(--muted);
    font-size:13px;
    line-height:1.6;
}

.field{margin-bottom:16px}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

label{
    display:block;
    margin-bottom:7px;
    color:#343a46;
    font-size:12px;
    font-weight:850;
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

.note{
    margin-top:8px;
    padding:11px 12px;
    border:1px solid var(--line);
    border-radius:11px;
    background:#f8fafc;
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
}

.hint{
    margin-top:6px;
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
}

.divider{
    margin:23px 0;
    border:0;
    border-top:1px solid var(--line);
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

.result{display:none}
.result.show{display:block}

.result-top{
    padding:22px;
    background:var(--dark);
    color:#fff;
    border-radius:16px;
    margin-bottom:17px;
}

.result-label{
    color:#b8bec8;
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
    margin-top:14px;
    padding:13px 14px;
    border:1px solid #fed7aa;
    background:#fff7ed;
    color:var(--orange);
    border-radius:12px;
    font-size:12px;
    line-height:1.6;
}

.budget{
    display:none;
    margin-top:18px;
    padding:20px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
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
    border-radius:12px;
    background:var(--yellow);
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
  "name":"Alçıpan Bölme ve Giydirme Duvar Hesaplayıcı",
  "url":"https://worksitem.com/araclar/alcipan-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR"
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

<body class="wc" data-tool-slug="alcipan-tavan-calculator">

<x-site.contech-header />


<div class="wc-wrap" style="padding-bottom:0">
    <x-calc.hero title="Alçıpan bölme ve giydirme duvar hesaplama" crumb="Alçıpan duvar" :facts="['Bölme ve giydirme duvar', 'C50 / C75 / C100 profil', 'YFK 2026 birim fiyatları']">
        5 m uzunluğunda ve 2,8 m yüksekliğinde bir alçıpan bölme duvarın net imalat alanı 14 m²'dir; kapı ve pencere boşlukları düşülerek hesaplanır. Uygulama tipini, profili, levha türünü ve ölçüleri girin; net metrajı ve seçilen sisteme ait Bakanlık birim fiyatıyla yaklaşık tutarı görün.
    </x-calc.hero>
</div>


<main class="container">

<div class="layout">


<section class="card">

    <h2>Duvar Sistemi</h2>

    <div class="card-sub">
        Asma tavan hesaplaması artık ayrı Asma Tavan
        aracında yapılmaktadır. Bu araç bölme ve giydirme
        duvar sistemlerine odaklanır.
    </div>


    <div class="field">

        <label>Uygulama Tipi</label>

        <select id="application">
            <option value="partition">Bölme Duvar</option>
            <option value="lining_glue">Giydirme Duvar · Yapıştırma</option>
            <option value="lining_frame">Giydirme Duvar · Metal İskelet</option>
        </select>

    </div>


    <div class="field" id="profileField">

        <label>Profil Genişliği</label>

        <select id="profile">
            <option value="C50">C50</option>
            <option value="C75">C75</option>
            <option value="C100">C100</option>
        </select>

    </div>


    <div class="field">

        <label>Levha Tipi</label>

        <select id="board">
            <option value="standard">Standart</option>
            <option value="water">Suya Dayanıklı</option>
            <option value="fire">Yangına Dayanıklı</option>
            <option value="water_fire">Su + Yangın Dayanımlı</option>
        </select>

    </div>


    <div class="note" id="systemNote">
        -
    </div>


    <hr class="divider">


    <div class="form-grid">

        <div class="field">

            <label>Duvar Uzunluğu (m)</label>

            <input
                type="number"
                id="length"
                min="0"
                step="0.01"
                placeholder="Örn. 24"
            >

        </div>


        <div class="field">

            <label>Duvar Yüksekliği (m)</label>

            <input
                type="number"
                id="height"
                min="0"
                step="0.01"
                placeholder="Örn. 3.20"
            >

        </div>

    </div>


    <div class="form-grid">

        <div class="field">

            <label>Kapı Boşlukları Toplamı (m²)</label>

            <input
                type="number"
                id="doors"
                min="0"
                step="0.01"
                value="0"
            >

        </div>


        <div class="field">

            <label>Diğer Boşluklar (m²)</label>

            <input
                type="number"
                id="openings"
                min="0"
                step="0.01"
                value="0"
            >

        </div>

    </div>


    <div class="field">

        <label>Alternatif: Net Duvar Alanı (m²)</label>

        <input
            type="number"
            id="directArea"
            min="0"
            step="0.01"
            placeholder="Biliyorsanız doğrudan girin"
        >

        <div class="hint">
            Bu alan doldurulursa uzunluk × yükseklik hesabının
            yerine doğrudan net alan kullanılır.
        </div>

    </div>


    <div class="field">

        <label>Mevcut Duvar / Kaplama Sökümü</label>

        <select id="demolition">
            <option value="no">Yok</option>
            <option value="yes">Var</option>
        </select>

    </div>


    <button class="btn" id="calculateBtn">
        Metraj ve YFK Maliyetini Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result" data-wc-result>

    <div class="result-top">

        <div class="result-label">
            Seçilen sistem
        </div>

        <div class="result-system" id="rSystem">
            -
        </div>

        <div class="total" id="rTotal" data-wc-price>
            -
        </div>

        <div class="total-sub">
            2026-08 YFK referans yaklaşık imalat tutarı
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Brüt duvar alanı</span>
            <strong id="rGross">-</strong>
        </div>

        <div class="row">
            <span>Düşülen boşluklar</span>
            <strong id="rOpenings">-</strong>
        </div>

        <div class="row">
            <span>Net imalat alanı</span>
            <strong id="rNet">-</strong>
        </div>

        <div class="row">
            <span>YFK pozu</span>
            <strong id="rPoz">-</strong>
        </div>

        <div class="row">
            <span>YFK birim fiyatı</span>
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
        Bu bütçe projeniz için uygun mu?
    </div>

    <div class="budget-text">
        Bütçe uygunsa Kainta İnşaat'tan proje kapsamına,
        saha şartlarına ve detaylara göre profesyonel
        net teklif alabilirsiniz.
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
            href="/maliyet-hesapla?service=alcipan"
            id="quoteLink"
        >
            Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
        </a>

    </div>


    <div class="no-interest" id="noInterest">
        Bu aşamada iletişim bilginizi almıyoruz.
    </div>

</div>

</section>

</div>


<section class="info-grid">

<article class="info">

    <strong>Fiyat sistem seçimine göre değişir</strong>

    <p>
        Profil genişliği ve levhanın standart, suya veya
        yangına dayanıklı olması farklı YFK pozlarına bağlıdır.
    </p>

</article>


<article class="info">

    <strong>Boşluklar net alandan düşülür</strong>

    <p>
        Kapı ve diğer açıklıklar duvarın brüt geometrik
        alanından düşülerek net uygulama alanı oluşturulur.
    </p>

</article>


<article class="info">

    <strong>Asma tavan ayrı araçtır</strong>

    <p>
        Alçıpan ve metal asma tavan sistemleri için
        WORKSİTEM Asma Tavan Hesaplayıcı kullanılmalıdır.
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


function findSystem() {

    const application =
        document.getElementById('application').value;

    const board =
        document.getElementById('board').value;

    const profile =
        document.getElementById('profile').value;


    for (const [key, system] of Object.entries(SYSTEMS)) {

        if (
            system.application !== application ||
            system.board !== board
        ) {
            continue;
        }


        if (
            application === 'partition' &&
            system.profile !== profile
        ) {
            continue;
        }


        return {
            key:key,
            system:system
        };
    }


    return null;
}


function refreshUi() {

    const application =
        document.getElementById('application').value;

    document.getElementById('profileField')
        .style.display =
            application === 'partition'
            ? 'block'
            : 'none';


    const found =
        findSystem();


    document.getElementById('systemNote')
        .innerText =
            found
            ? found.system.note
            : 'Bu kombinasyon için doğrulanmış sistem bulunamadı.';
}


function calculate() {

    const found =
        findSystem();


    if (!found) {

        alert(
            'Seçilen kombinasyon için doğrulanmış sistem bulunamadı.'
        );

        return;
    }


    const system =
        found.system;


    const directArea =
        numeric('directArea');

    const length =
        numeric('length');

    const height =
        numeric('height');

    const doors =
        Math.max(
            0,
            numeric('doors')
        );

    const openings =
        Math.max(
            0,
            numeric('openings')
        );


    let grossArea;
    let deductions;
    let netArea;


    if (directArea > 0) {

        grossArea =
            directArea;

        deductions =
            0;

        netArea =
            directArea;

    } else {

        grossArea =
            length *
            height;

        deductions =
            doors +
            openings;

        netArea =
            Math.max(
                0,
                grossArea -
                deductions
            );
    }


    if (netArea <= 0) {

        alert(
            'Lütfen geçerli duvar ölçülerini veya net alanı girin.'
        );

        return;
    }


    document.getElementById('rSystem')
        .innerText =
            system.label;


    document.getElementById('rGross')
        .innerText =
            number.format(grossArea) +
            ' m²';


    document.getElementById('rOpenings')
        .innerText =
            number.format(deductions) +
            ' m²';


    document.getElementById('rNet')
        .innerText =
            number.format(netArea) +
            ' m²';


    document.getElementById('rPoz')
        .innerHTML =
            '<a href="/poz/' + system.poz + '" target="_blank" style="color:var(--yellow2); text-decoration:underline;" title="' + system.poz + ' detaylarını incele">' + system.poz + ' &rarr;</a>';


    if (
        system.price_found &&
        system.unit_price !== null
    ) {

        const unitPrice =
            parseFloat(
                system.unit_price
            );


        const total =
            netArea *
            unitPrice;


        document.getElementById('rTotal')
            .innerText =
                money.format(total) +
                ' TL';


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
                ' · PDF sayfa ' +
                system.source_page +
                '<br>' +
                system.note;


        document.getElementById('budgetBox')
            .style.display =
                'block';

    } else {

        document.getElementById('rTotal')
            .innerText =
                'Metraj hazır';


        document.getElementById('rUnit')
            .innerText =
                'Doğrulanmış fiyat yok';


        document.getElementById('rPeriod')
            .innerText =
                '-';


        document.getElementById('sourceBox')
            .innerHTML =
                '<strong>Fiyat durumu:</strong> Doğrulanmış birim fiyat bulunamadı.';


        document.getElementById('budgetBox')
            .style.display =
                'none';
    }


    const warnings = [];


    if (
        directArea <= 0 &&
        deductions >= grossArea
    ) {

        warnings.push(
            'Girilen boşluk alanları brüt duvar alanına eşit veya daha büyük görünüyor.'
        );
    }


    if (
        document.getElementById('demolition').value
        === 'yes'
    ) {

        warnings.push(
            'Mevcut duvar veya kaplama sökümü seçildi. ' +
            'Doğrulanmış söküm fiyatı bulunmadığından YFK referans toplamına eklenmedi.'
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


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=alcipan' +
        '&area=' +
        encodeURIComponent(
            netArea.toFixed(2)
        ) +
        '&application=' +
        encodeURIComponent(
            system.application
        ) +
        '&system=' +
        encodeURIComponent(
            found.key
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
        refreshUi
    );


document.getElementById('profile')
    .addEventListener(
        'change',
        refreshUi
    );


document.getElementById('board')
    .addEventListener(
        'change',
        refreshUi
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


refreshUi();

</script>

    <x-calc.kit />
    <x-tools.tool-guide slug="alcipan-hesaplayici" />

<x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
