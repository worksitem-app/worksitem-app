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

<title>Asma Tavan Metraj ve Maliyet Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="60x60, 30x30, gizli taşıyıcılı, sarkmalı, lamel, PVC, alçıpan, baffle ve petek asma tavan sistemleri için metraj ve doğrulanmış YFK referans maliyetlerini hesaplayın.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/asma-tavan-hesaplayici">

<style>
:root{
    --bg:#f5f6f8;
    --surface:#ffffff;
    --surface2:#f9fafb;
    --text:#101318;
    --muted:#68707d;
    --line:#e2e5e9;
    --yellow:#ffb800;
    --yellow-dark:#d99c00;
    --green:#15803d;
    --red:#b91c1c;
    --blue:#1d4ed8;
    --radius:20px;
    --shadow:0 16px 50px rgba(15,23,42,.08);
}

*{box-sizing:border-box}

body{
    margin:0;
    font-family:Inter,Arial,sans-serif;
    background:
        radial-gradient(circle at 50% -10%,rgba(255,184,0,.10),transparent 32%),
        var(--bg);
    color:var(--text);
}

a{text-decoration:none;color:inherit}

.header{
    height:68px;
    background:rgba(255,255,255,.94);
    border-bottom:1px solid var(--line);
    display:flex;
    align-items:center;
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
    letter-spacing:-.5px;
}

.brand span{color:var(--yellow-dark)}

.back{
    font-size:13px;
    font-weight:800;
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
    background:#fff5cc;
    border:1px solid #f7d75e;
    color:#765700;
    border-radius:999px;
    padding:7px 12px;
    font-size:12px;
    font-weight:900;
    margin-bottom:16px;
}

.hero h1{
    margin:0 auto 14px;
    max-width:850px;
    font-size:clamp(32px,5vw,54px);
    line-height:1.03;
    letter-spacing:-2px;
}

.hero p{
    max-width:760px;
    margin:auto;
    color:var(--muted);
    line-height:1.7;
    font-size:16px;
}

.layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(360px,.85fr);
    gap:24px;
    align-items:start;
    padding-bottom:70px;
}

.card{
    background:var(--surface);
    border:1px solid var(--line);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:26px;
}

.card h2{
    margin:0 0 6px;
    font-size:22px;
    letter-spacing:-.6px;
}

.card-sub{
    color:var(--muted);
    font-size:13px;
    line-height:1.55;
    margin-bottom:24px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

.field{
    margin-bottom:16px;
}

.field.full{
    grid-column:1/-1;
}

label{
    display:block;
    font-size:12px;
    font-weight:850;
    margin-bottom:7px;
    color:#343a46;
}

input,select{
    width:100%;
    height:50px;
    border:1px solid var(--line);
    border-radius:12px;
    padding:0 13px;
    background:#fff;
    color:var(--text);
    font:inherit;
    outline:none;
}

input:focus,select:focus{
    border-color:#d9a400;
    box-shadow:0 0 0 3px rgba(255,184,0,.12);
}

.hint{
    font-size:11px;
    color:var(--muted);
    margin-top:6px;
    line-height:1.5;
}

.or{
    text-align:center;
    color:#9ca3af;
    font-size:11px;
    font-weight:900;
    margin:1px 0 14px;
    text-transform:uppercase;
    letter-spacing:.8px;
}

.btn{
    width:100%;
    border:0;
    border-radius:13px;
    min-height:54px;
    padding:0 18px;
    background:var(--yellow);
    color:#111;
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
    background:#111318;
    color:#fff;
    border-radius:16px;
    padding:22px;
    margin-bottom:17px;
}

.result-label{
    color:#aeb4bf;
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.7px;
    margin-bottom:7px;
}

.result-system{
    font-size:20px;
    font-weight:900;
    line-height:1.3;
}

.total{
    font-size:34px;
    font-weight:950;
    letter-spacing:-1.3px;
    margin-top:16px;
}

.total-sub{
    color:#b9bec7;
    font-size:11px;
    margin-top:5px;
}

.rows{
    border:1px solid var(--line);
    border-radius:15px;
    overflow:hidden;
}

.row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
    padding:13px 15px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.row:last-child{border-bottom:0}

.row span{color:var(--muted)}

.row strong{
    text-align:right;
    font-weight:850;
}

.source{
    margin-top:16px;
    padding:14px;
    border-radius:13px;
    background:#f7f8fa;
    border:1px solid var(--line);
    font-size:12px;
    line-height:1.6;
    color:var(--muted);
}

.source strong{color:var(--text)}

.warning{
    margin-top:14px;
    padding:13px 14px;
    border-radius:12px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    color:#9a3412;
    font-size:12px;
    line-height:1.6;
    display:none;
}

.missing{
    background:#fff7ed;
    border-color:#fed7aa;
}

.budget-confirm{
    margin-top:18px;
    display:none;
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
    padding:20px;
    text-align:center;
}

.budget-title{
    font-size:18px;
    font-weight:950;
    letter-spacing:-.4px;
}

.budget-text{
    color:var(--muted);
    font-size:12px;
    line-height:1.6;
    margin:8px 0 15px;
}

.budget-actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.budget-actions button{
    min-height:46px;
    border-radius:11px;
    border:1px solid var(--line);
    background:#fff;
    font-weight:850;
    cursor:pointer;
}

.budget-actions .yes{
    background:#111318;
    color:#fff;
    border-color:#111318;
}

.quote-box{
    margin-top:15px;
    display:none;
}

.quote-link{
    display:flex;
    min-height:54px;
    align-items:center;
    justify-content:center;
    border-radius:12px;
    background:var(--yellow);
    color:#111;
    font-size:14px;
    font-weight:950;
    padding:10px 15px;
}

.no-interest{
    display:none;
    margin-top:12px;
    color:var(--muted);
    font-size:12px;
    line-height:1.5;
}

.info-grid{
    margin:25px 0 70px;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:16px;
}

.info{
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
    padding:20px;
}

.info strong{
    display:block;
    margin-bottom:6px;
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

@media(max-width:600px){
    .form-grid,.budget-actions,.info-grid{
        grid-template-columns:1fr;
    }

    .hero{
        padding-top:38px;
    }

    .card{
        padding:20px;
    }
}
</style>

<script type="application/ld+json">
{
  "@@context":"https://schema.org",
  "@@type":"WebApplication",
  "name":"Asma Tavan Metraj ve Maliyet Hesaplayıcı",
  "url":"https://worksitem.com/araclar/asma-tavan-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"Asma tavan sistemleri için metraj ve doğrulanmış referans birim fiyat hesaplama aracı."
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
</head>

<body>

<x-site.contech-header />

<section class="hero">
    <div class="container">

        <div class="kicker">
            CONTECH · METRAJ + DOĞRULANMIŞ FİYAT
        </div>

        <h1>
            Asma Tavan Metraj ve
            Maliyet Hesaplayıcı
        </h1>

        <p>
            60x60, 30x30, sarkmalı, gizli taşıyıcılı,
            lamel, PVC, alçıpan, baffle ve petek tavan
            sistemleri için yaklaşık metrajınızı çıkarın.
            Doğrulanmış fiyat bulunan sistemlerde
            2026-08 YFK referans bedelini görün.
        </p>

    </div>
</section>

<main class="container">

<div class="layout">

<section class="card">

    <h2>Proje Bilgileri</h2>

    <div class="card-sub">
        Alanı doğrudan girebilir veya uzunluk × genişlik
        üzerinden hesaplatabilirsiniz.
    </div>

    <div class="field">

        <label>Asma Tavan Sistemi</label>

        <select id="system">

            @foreach($systems as $key => $system)

                <option value="{{ $key }}">
                    {{ $system['label'] }}
                    @if($system['price_found'])
                        · YFK
                    @else
                        · Metraj
                    @endif
                </option>

            @endforeach

        </select>

    </div>

    <div class="field">

        <label>Proje Tipi</label>

        <select id="projectType">
            <option value="ofis">Ofis / Fit-Out</option>
            <option value="fabrika">Fabrika / Üretim Tesisi</option>
            <option value="magaza">Mağaza / Restoran</option>
            <option value="otel">Otel / Konaklama</option>
            <option value="saglik">Sağlık Yapısı</option>
            <option value="konut">Konut</option>
            <option value="diger">Diğer</option>
        </select>

    </div>

    <div class="field">

        <label>Net Tavan Alanı (m²)</label>

        <input
            type="number"
            id="area"
            min="0"
            step="0.01"
            placeholder="Örn. 420"
        >

        <div class="hint">
            Alanı biliyorsanız yalnızca bu kutuyu doldurmanız yeterlidir.
        </div>

    </div>

    <div class="or">
        veya ölçülerden hesapla
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

        <div class="field">

            <label>Fire / Sipariş Payı (%)</label>

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

            <label>Mevcut Tavan Sökümü</label>

            <select id="demolition">
                <option value="no">Yok</option>
                <option value="yes">Var</option>
            </select>

        </div>

    </div>

    <button class="btn" id="calculateBtn">
        Metraj ve Referans Maliyeti Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result">

    <div class="result-top">

        <div class="result-label">
            Seçilen sistem
        </div>

        <div class="result-system" id="rSystem">
            -
        </div>

        <div class="total" id="rTotal">
            -
        </div>

        <div class="total-sub" id="rTotalSub">
            2026-08 YFK referans yaklaşık tutarı
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

        <div class="row" id="panelRow">
            <span>Yaklaşık panel ihtiyacı</span>
            <strong id="rPanels">-</strong>
        </div>

        <div class="row">
            <span>Referans poz</span>
            <strong id="rPoz">-</strong>
        </div>

        <div class="row">
            <span>Referans birim fiyat</span>
            <strong id="rUnitPrice">-</strong>
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


<div class="budget-confirm" id="budgetConfirm">

    <div class="budget-title">
        Bu bütçe projeniz için uygun mu?
    </div>

    <div class="budget-text">
        Referans hesap size uygunsa Kainta İnşaat'tan
        proje şartlarına göre profesyonel net teklif
        isteyebilirsiniz.
    </div>

    <div class="budget-actions">

        <button type="button" class="yes" id="budgetYes">
            ✓ Evet, teklif istiyorum
        </button>

        <button type="button" id="budgetNo">
            Sadece maliyete baktım
        </button>

    </div>

    <div class="quote-box" id="quoteBox">

        <a
            href="/maliyet-hesapla?service=asma-tavan"
            class="quote-link"
            id="quoteLink"
        >
            Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
        </a>

    </div>

    <div class="no-interest" id="noInterest">
        İletişim bilginizi almıyoruz. Projeniz
        netleştiğinde tekrar hesaplayabilirsiniz.
    </div>

</div>

</section>

</div>


<section class="info-grid">

    <article class="info">

        <strong>YFK referansı ≠ satış teklifi</strong>

        <p>
            Gösterilen tutar doğrulanmış referans birim
            fiyat üzerinden hesaplanır. Nihai Kainta İnşaat
            teklifi proje, marka, yükseklik, lojistik,
            saha koşulları ve uygulama detaylarına göre
            hazırlanır.
        </p>

    </article>

    <article class="info">

        <strong>Fire ayrı hesaplanır</strong>

        <p>
            Fire oranı satın alınacak yaklaşık malzeme
            miktarını gösterir. YFK imalat referans
            tutarı net uygulanmış alan üzerinden
            hesaplanır.
        </p>

    </article>

    <article class="info">

        <strong>Fiyat yoksa uydurulmaz</strong>

        <p>
            Baffle, petek/open-cell ve doğrulanmış
            fiyatı bulunmayan sistemlerde WORKSİTEM
            yalnızca metraj üretir; tahmini birim fiyat
            icat etmez.
        </p>

    </article>

</section>

</main>


<script>
const SYSTEMS = {!! $systemsJson !!};

const money = new Intl.NumberFormat(
    'tr-TR',
    {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }
);

const number = new Intl.NumberFormat(
    'tr-TR',
    {
        maximumFractionDigits: 2
    }
);

function getArea() {

    const direct =
        parseFloat(
            document.getElementById('area').value
        ) || 0;

    if (direct > 0) {
        return direct;
    }

    const length =
        parseFloat(
            document.getElementById('length').value
        ) || 0;

    const width =
        parseFloat(
            document.getElementById('width').value
        ) || 0;

    return length * width;
}


function calculate() {

    const systemKey =
        document.getElementById('system').value;

    const system =
        SYSTEMS[systemKey];

    const area =
        getArea();

    if (!system) {
        alert('Lütfen geçerli bir tavan sistemi seçin.');
        return;
    }

    if (!area || area <= 0) {
        alert('Lütfen tavan alanını veya uzunluk/genişlik ölçülerini girin.');
        return;
    }

    let waste =
        parseFloat(
            document.getElementById('waste').value
        );

    if (!Number.isFinite(waste)) {
        waste = 5;
    }

    waste =
        Math.max(
            0,
            Math.min(25, waste)
        );

    const orderArea =
        area * (1 + waste / 100);

    const demolition =
        document.getElementById('demolition').value;

    const projectType =
        document.getElementById('projectType').value;

    const result =
        document.getElementById('result');

    const warningBox =
        document.getElementById('warningBox');

    const panelRow =
        document.getElementById('panelRow');

    document.getElementById('rSystem')
        .innerText = system.label;

    document.getElementById('rNet')
        .innerText = number.format(area) + ' m²';

    document.getElementById('rOrder')
        .innerText = number.format(orderArea) + ' m²';


    if (
        system.panel_area &&
        parseFloat(system.panel_area) > 0
    ) {

        const panelCount =
            Math.ceil(
                orderArea /
                parseFloat(system.panel_area)
            );

        panelRow.style.display = 'flex';

        document.getElementById('rPanels')
            .innerText =
                number.format(panelCount) +
                ' adet';

    } else {

        panelRow.style.display = 'none';

    }


    document.getElementById('rPoz')
        .innerText =
            system.poz || 'Doğrulanmış poz bekleniyor';


    if (
        system.price_found &&
        system.unit_price !== null
    ) {

        const unitPrice =
            parseFloat(system.unit_price);

        /*
         * YFK pozu tamamlanmış imalat m² fiyatıdır.
         * Fireyi YFK toplamına tekrar eklemiyoruz.
         */
        const referenceTotal =
            area * unitPrice;

        document.getElementById('rUnitPrice')
            .innerText =
                money.format(unitPrice) +
                ' TL/m²';

        document.getElementById('rPeriod')
            .innerText =
                system.price_period || '2026-08';

        document.getElementById('rTotal')
            .innerText =
                money.format(referenceTotal) +
                ' TL';

        document.getElementById('rTotalSub')
            .innerText =
                '2026-08 YFK referans yaklaşık imalat tutarı';

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

        document.getElementById('rUnitPrice')
            .innerText =
                'Doğrulanmış fiyat yok';

        document.getElementById('rPeriod')
            .innerText =
                '-';

        document.getElementById('rTotal')
            .innerText =
                'Metraj hazır';

        document.getElementById('rTotalSub')
            .innerText =
                'Fiyat uydurulmadı';

        document.getElementById('sourceBox')
            .innerHTML =
                '<strong>Fiyat durumu:</strong> ' +
                'Bu sistem için doğrulanmış birim fiyat ' +
                'henüz fiyat motoruna eklenmedi.<br>' +
                system.note;
    }


    const warnings = [];

    if (demolition === 'yes') {

        warnings.push(
            'Mevcut tavan sökümü seçildi. ' +
            'Doğrulanmış söküm birim fiyatı bulunmadığından ' +
            'söküm bedeli referans toplama eklenmedi.'
        );
    }

    if (!system.price_found) {

        warnings.push(
            'Bu sistem için yalnızca metraj sonucu gösterilmektedir.'
        );
    }

    if (warnings.length) {

        warningBox.style.display = 'block';

        warningBox.innerText =
            warnings.join(' ');

    } else {

        warningBox.style.display = 'none';
        warningBox.innerText = '';

    }


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=asma-tavan' +
        '&area=' +
        encodeURIComponent(
            area.toFixed(2)
        ) +
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
        .style.display = 'none';

    document.getElementById('noInterest')
        .style.display = 'none';

    document.getElementById('budgetConfirm')
        .style.display = 'block';

    result.classList.add('show');

    result.scrollIntoView({
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
                .style.display = 'block';

            document.getElementById('noInterest')
                .style.display = 'none';

        }
    );


document.getElementById('budgetNo')
    .addEventListener(
        'click',
        function () {

            document.getElementById('quoteBox')
                .style.display = 'none';

            document.getElementById('noInterest')
                .style.display = 'block';

        }
    );
</script>

    <x-site.contech-footer />
</body>
</html>
