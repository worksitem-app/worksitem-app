@php
$insulationsJson = json_encode(
    $insulations,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

if (!$insulationsJson) {
    $insulationsJson = '{}';
}
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Yükseltilmiş Döşeme Metraj Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="60x60 yükseltilmiş döşeme paneli, ayak ve stringer metrajını hesaplayın. Alt yalıtım seçildiğinde doğrulanmış 2026 YFK referans maliyetini görün.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/yukseltilmis-doseme-hesaplayici">

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
    --blue:#101318;
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
        radial-gradient(circle at 50% -8%,rgba(16,19,24,.07),transparent 30%),
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
    border-radius:999px;
    background:#fff8e1;
    border:1px solid #fde68a;
    color:#101318;
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
    line-height:1.55;
}

.note{
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
    border:0;
    border-top:1px solid var(--line);
    margin:23px 0;
}

.section-title{
    font-size:14px;
    font-weight:950;
    margin-bottom:14px;
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
    font-size:20px;
    font-weight:950;
}

.total{
    margin-top:17px;
    font-size:31px;
    font-weight:950;
    letter-spacing:-1.1px;
}

.total-sub{
    margin-top:5px;
    color:#b8bec8;
    font-size:11px;
    line-height:1.5;
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

.insulation-box{
    margin-top:16px;
    padding:16px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#f8fafc;
}

.insulation-title{
    font-size:12px;
    font-weight:950;
    margin-bottom:9px;
}

.insulation-cost{
    font-size:24px;
    font-weight:950;
}

.insulation-meta{
    margin-top:6px;
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
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
    line-height:1.65;
}

.source strong{color:var(--text)}

.cta{
    margin-top:18px;
    padding:20px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
    text-align:center;
}

.cta-title{
    font-size:18px;
    font-weight:950;
}

.cta-text{
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
  "name":"Yükseltilmiş Döşeme Metraj Hesaplayıcı",
  "url":"https://worksitem.com/araclar/yukseltilmis-doseme-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"60x60 yükseltilmiş döşeme sistemi için panel, ayak, stringer ve alt yalıtım metraj hesaplama aracı."
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

<body data-tool-slug="yukseltilmis-doseme-calculator">

<x-site.contech-header />


<section class="hero">
<div class="container">

    <div class="kicker">
        CONTECH · OFİS & TEKNİK ALAN
    </div>

    <h1>
        Yükseltilmiş Döşeme
        Metraj Hesaplayıcı
    </h1>

    <p>
        60×60 cm modüler yükseltilmiş döşeme için
        panel, ayak ve stringer metrajınızı çıkarın.
        Alt yalıtım seçerseniz doğrulanmış 2026-08 YFK
        referans maliyetini ayrıca görün.
    </p>

</div>
</section>


<main class="container">

<div class="layout">


<section class="card">

    <h2>Döşeme Projesi</h2>

    <div class="card-sub">
        Ana yükseltilmiş döşeme sistemi için doğrulanmış
        birim fiyat henüz bulunmadığından panel, ayak ve
        stringer fiyatı üretilmez.
    </div>


    <div class="field">

        <label>Proje Tipi</label>

        <select id="projectType">
            <option value="ofis">Ofis / Fit-Out</option>
            <option value="veri-merkezi">Veri Merkezi / Sistem Odası</option>
            <option value="banka">Banka / Finans</option>
            <option value="kontrol-odasi">Kontrol Odası</option>
            <option value="ticari">Ticari Alan</option>
            <option value="diger">Diğer</option>
        </select>

    </div>


    <div class="field">

        <label>Panel Tipi</label>

        <select id="panelType">
            <option value="belirsiz">Henüz Belirlenmedi</option>
            <option value="kalsiyum-sulfat">Kalsiyum Sülfat Özlü</option>
            <option value="celik-kapsullu">Çelik Kapsüllü</option>
            <option value="sunta-ozlu">Sunta Özlü</option>
        </select>

        <div class="hint">
            Panel tipi metrajı değiştirmez; teklif aşamasında teknik seçim için kullanılır.
        </div>

    </div>


    <div class="field">

        <label>Net Döşeme Alanı (m²)</label>

        <input
            type="number"
            id="area"
            min="0"
            step="0.01"
            placeholder="Örn. 600"
        >

        <div class="hint">
            Alanı doğrudan girerseniz panel adedi yaklaşık
            hesaplanır. Ayak ve stringer için oda ölçülerini
            girmeniz gerekir.
        </div>

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
            <label>Döşeme Yüksekliği (cm)</label>
            <input type="number" id="height" min="5" step="1" value="30">
        </div>

        <div class="field">
            <label>Panel Fire Payı (%)</label>
            <input type="number" id="waste" min="0" max="20" step="0.5" value="5">
        </div>

    </div>


    <hr class="divider">


    <div class="section-title">
        Alt Isı / Ses Yalıtımı
    </div>


    <div class="field">

        <label>Yalıtım Sistemi</label>

        <select id="insulation">

            @foreach($insulations as $key => $insulation)

                <option value="{{ $key }}">
                    {{ $insulation['label'] }}
                    @if($insulation['price_found'])
                        · YFK
                    @endif
                </option>

            @endforeach

        </select>

        <div class="note" id="insulationNote">
            -
        </div>

    </div>


    <button class="btn" id="calculateBtn">
        Yükseltilmiş Döşeme Metrajını Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result">

    <div class="result-top">

        <div class="result-label">
            Yükseltilmiş Döşeme Ön Metrajı
        </div>

        <div class="result-system">
            60×60 cm Modüler Sistem
        </div>

        <div class="total" id="rMainStatus">
            Metraj hazır
        </div>

        <div class="total-sub">
            Ana döşeme sistemi için doğrulanmış fiyat bulunmadığından toplam bedel üretilmiyor.
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Net döşeme alanı</span>
            <strong id="rNet">-</strong>
        </div>

        <div class="row">
            <span>Fire dahil panel sipariş alanı</span>
            <strong id="rOrder">-</strong>
        </div>

        <div class="row">
            <span>60×60 panel adedi</span>
            <strong id="rPanels">-</strong>
        </div>

        <div class="row">
            <span>Yaklaşık ayak adedi</span>
            <strong id="rPedestals">-</strong>
        </div>

        <div class="row">
            <span>Yaklaşık stringer metrajı</span>
            <strong id="rStringer">-</strong>
        </div>

        <div class="row">
            <span>Yükseltme yüksekliği</span>
            <strong id="rHeight">-</strong>
        </div>

    </div>


    <div class="insulation-box" id="insulationResult">

        <div class="insulation-title">
            Alt Yalıtım Referansı
        </div>

        <div class="insulation-cost" id="rInsulationCost">
            Yalıtım seçilmedi
        </div>

        <div class="insulation-meta" id="rInsulationMeta">
            -
        </div>

    </div>


    <div class="warning" id="warningBox"></div>


    <div class="source">

        <strong>Ana sistem fiyat durumu:</strong>
        Panel, pedestal/ayak, stringer, kaplama ve aksesuarların
        doğrulanmış komple sistem fiyatı henüz fiyat motoruna
        bağlanmadı. Bu nedenle ana sistem için rakam üretilmez.

    </div>

</div>


<div class="cta" id="ctaBox">

    <div class="cta-title">
        Projeniz uygulama aşamasında mı?
    </div>

    <div class="cta-text">
        Ana sistem fiyatı için panel tipi, yük sınıfı,
        yükseltme yüksekliği, yüzey kaplaması ve saha
        şartlarının netleştirilmesi gerekir.
    </div>

    <div class="actions">

        <button type="button" class="yes" id="quoteYes">
            ✓ Evet, net teklif istiyorum
        </button>

        <button type="button" id="quoteNo">
            Şimdilik metraja baktım
        </button>

    </div>


    <div class="quote" id="quoteBox">

        <a
            href="/maliyet-hesapla?service=yukseltilmis-doseme"
            id="quoteLink"
        >
            Kainta İnşaat'tan profesyonel net teklif alın →
        </a>

    </div>


    <div class="no-interest" id="noInterest">
        İletişim bilginizi almıyoruz.
        Projeniz netleştiğinde tekrar kullanabilirsiniz.
    </div>

</div>

</section>

</div>


<section class="info-grid">

<article class="info">

    <strong>60×60 modül hesabı</strong>

    <p>
        V1 hesap motoru 600×600 mm standart modüler grid
        üzerinden panel adedi çıkarır. Oda ölçüleri girildiğinde
        ayak ve stringer metrajı da grid üzerinden hesaplanır.
    </p>

</article>


<article class="info">

    <strong>Ayak hesabı ölçü ister</strong>

    <p>
        Yalnızca toplam m² girildiğinde ayak ve stringer
        adedinde sahte hassasiyet üretmiyoruz. Bu kalemleri
        görmek için uzunluk ve genişlik girilmelidir.
    </p>

</article>


<article class="info">

    <strong>Yalıtım ayrı fiyatlandırılır</strong>

    <p>
        Taşyünü ve camyünü alt yalıtımı seçildiğinde yalnızca
        ilgili doğrulanmış YFK yalıtım bedeli hesaplanır.
        Bu tutar ana yükseltilmiş döşeme fiyatı değildir.
    </p>

</article>

</section>

</main>


<script>

const INSULATIONS = {!! $insulationsJson !!};

const PANEL_SIDE = 0.60;
const PANEL_AREA = PANEL_SIDE * PANEL_SIDE;

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


function updateInsulationNote() {

    const key =
        document.getElementById('insulation').value;

    const insulation =
        INSULATIONS[key];

    document.getElementById('insulationNote')
        .innerText =
            insulation
            ? insulation.note
            : '-';
}


function calculate() {

    const directArea =
        numeric('area');

    const length =
        numeric('length');

    const width =
        numeric('width');


    let netArea = 0;

    let dimensionsUsed = false;


    if (directArea > 0) {

        netArea =
            directArea;

    } else if (
        length > 0 &&
        width > 0
    ) {

        netArea =
            length *
            width;

        dimensionsUsed =
            true;
    }


    if (netArea <= 0) {

        alert(
            'Lütfen döşeme alanını veya uzunluk/genişlik ölçülerini girin.'
        );

        return;
    }


    /*
     * Doğrudan alan girilse bile uzunluk/genişlik de mevcutsa
     * grid hesabı için ölçüleri kullanabiliriz; fakat alan ile
     * ölçüler arasında büyük fark varsa sahte hassasiyet üretmeyiz.
     */

    let canCalculateGrid =
        length > 0 &&
        width > 0;


    if (
        directArea > 0 &&
        canCalculateGrid
    ) {

        const geometryArea =
            length *
            width;

        const difference =
            Math.abs(
                geometryArea -
                directArea
            );

        const tolerance =
            Math.max(
                directArea,
                geometryArea
            ) * 0.05;


        if (difference > tolerance) {

            canCalculateGrid =
                false;
        }
    }


    let waste =
        numeric('waste');

    waste =
        Math.max(
            0,
            Math.min(
                20,
                waste
            )
        );


    const orderArea =
        netArea *
        (1 + waste / 100);


    let panelCount =
        Math.ceil(
            orderArea /
            PANEL_AREA
        );


    let pedestalText =
        'Ölçüler gerekli';

    let stringerText =
        'Ölçüler gerekli';


    if (canCalculateGrid) {

        const panelsLength =
            Math.ceil(
                length /
                PANEL_SIDE
            );

        const panelsWidth =
            Math.ceil(
                width /
                PANEL_SIDE
            );


        /*
         * Grid panel adedi fire hariç geometrik modüldür.
         * Sipariş panel adedi için ayrıca fire uygulanır.
         */

        const geometricPanels =
            panelsLength *
            panelsWidth;


        const gridOrderPanels =
            Math.ceil(
                geometricPanels *
                (1 + waste / 100)
            );


        panelCount =
            Math.max(
                panelCount,
                gridOrderPanels
            );


        const pedestalCount =
            (panelsLength + 1) *
            (panelsWidth + 1);


        /*
         * Ortogonal 600x600 gridde tüm panel kenarlarında
         * stringer kullanıldığı varsayılan ön metraj.
         */

        const horizontalSegments =
            panelsLength *
            (panelsWidth + 1);


        const verticalSegments =
            panelsWidth *
            (panelsLength + 1);


        const stringerLength =
            (
                horizontalSegments +
                verticalSegments
            ) *
            PANEL_SIDE;


        pedestalText =
            number.format(
                pedestalCount
            ) +
            ' adet';


        stringerText =
            number.format(
                stringerLength
            ) +
            ' m';
    }


    document.getElementById('rNet')
        .innerText =
            number.format(netArea) +
            ' m²';


    document.getElementById('rOrder')
        .innerText =
            number.format(orderArea) +
            ' m²';


    document.getElementById('rPanels')
        .innerText =
            number.format(panelCount) +
            ' adet';


    document.getElementById('rPedestals')
        .innerText =
            pedestalText;


    document.getElementById('rStringer')
        .innerText =
            stringerText;


    document.getElementById('rHeight')
        .innerText =
            number.format(
                numeric('height')
            ) +
            ' cm';


    /*
     * ALT YALITIM
     */

    const insulationKey =
        document.getElementById('insulation').value;

    const insulation =
        INSULATIONS[insulationKey];


    if (
        insulation &&
        insulation.price_found &&
        insulation.unit_price !== null
    ) {

        const unitPrice =
            parseFloat(
                insulation.unit_price
            );


        /*
         * YFK tamamlanmış yalıtım imalatı:
         * net alan × doğrulanmış birim fiyat.
         */

        const insulationTotal =
            netArea *
            unitPrice;


        document.getElementById('rInsulationCost')
            .innerText =
                money.format(
                    insulationTotal
                ) +
                ' TL';


        document.getElementById('rInsulationMeta')
            .innerText =
                insulation.label +
                ' · Poz ' +
                insulation.poz +
                ' · ' +
                money.format(unitPrice) +
                ' TL/m² × ' +
                number.format(netArea) +
                ' m² · YFK 2026-08';

    } else {

        document.getElementById('rInsulationCost')
            .innerText =
                'Yalıtım seçilmedi';


        document.getElementById('rInsulationMeta')
            .innerText =
                'Ana yükseltilmiş döşeme sistemi için fiyat üretilmez.';
    }


    const warnings = [];


    if (!canCalculateGrid) {

        warnings.push(
            'Ayak ve stringer metrajının hesaplanabilmesi için tutarlı uzunluk ve genişlik ölçüleri gerekir.'
        );
    }


    if (
        numeric('height') > 60
    ) {

        warnings.push(
            'Yüksek döşeme kotlarında taşıyıcı sistem, çapraz kuşaklama ve stabilite ayrıca teknik olarak kontrol edilmelidir.'
        );
    }


    warnings.push(
        'Panel, ayak, stringer, aksesuar ve üst yüzey kaplaması için doğrulanmış komple sistem fiyatı bulunmadığından ana döşeme maliyeti gösterilmemektedir.'
    );


    const warningBox =
        document.getElementById('warningBox');


    warningBox.style.display =
        'block';

    warningBox.innerText =
        warnings.join(' ');


    const projectType =
        document.getElementById('projectType').value;

    const panelType =
        document.getElementById('panelType').value;


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=yukseltilmis-doseme' +
        '&area=' +
        encodeURIComponent(
            netArea.toFixed(2)
        ) +
        '&height=' +
        encodeURIComponent(
            numeric('height')
        ) +
        '&panel_type=' +
        encodeURIComponent(panelType) +
        '&project_type=' +
        encodeURIComponent(projectType) +
        '&insulation=' +
        encodeURIComponent(insulationKey);


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


    document.getElementById('ctaBox')
        .style.display =
            'block';


    document.getElementById('result')
        .scrollIntoView({
            behavior:'smooth',
            block:'start'
        });
}


document.getElementById('insulation')
    .addEventListener(
        'change',
        updateInsulationNote
    );


document.getElementById('calculateBtn')
    .addEventListener(
        'click',
        calculate
    );


document.getElementById('quoteYes')
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


document.getElementById('quoteNo')
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


updateInsulationNote();

</script>

    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
