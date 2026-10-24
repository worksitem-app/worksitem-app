@php
$catalogJson = json_encode(
    $catalog,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

if (!$catalogJson) {
    $catalogJson = '{}';
}
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Havalandırma Kanalı ve HVAC Maliyet Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="Dikdörtgen ve spiral hava kanalı yüzeyi, kanal izolasyonu, menfez, lineer difüzör ve aspiratör için 2026 YFK referans metraj ve maliyet hesabı.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/havalandirma-hesaplayici">

<style>
:root{
    --bg:#f5f6f8;
    --surface:#fff;
    --text:#101318;
    --muted:#69717d;
    --line:#e2e5e9;
    --yellow:#ffb800;
    --dark:#111318;
    --orange:#9a3412;
    --radius:18px;
    --shadow:0 18px 50px rgba(15,23,42,.08);
}

*{box-sizing:border-box}

body{
    margin:0;
    font-family:Inter,Arial,sans-serif;
    color:var(--text);
    background:var(--bg);
}

a{text-decoration:none;color:inherit}

.header{
    height:68px;
    background:#fff;
    border-bottom:1px solid var(--line);
    display:flex;
    align-items:center;
}

.header-inner,
.container{
    width:min(1140px,calc(100% - 32px));
    margin:auto;
}

.header-inner{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.brand{
    font-size:18px;
    font-weight:950;
}

.brand span{color:#d99c00}

.back{
    color:var(--muted);
    font-size:13px;
    font-weight:800;
}

.hero{
    padding:55px 0 35px;
    text-align:center;
}

.kicker{
    display:inline-block;
    padding:7px 12px;
    margin-bottom:15px;
    background:#fff8e1;
    border:1px solid #fde68a;
    border-radius:999px;
    color:#101318;
    font-size:12px;
    font-weight:900;
}

.hero h1{
    max-width:900px;
    margin:0 auto 14px;
    font-size:clamp(32px,5vw,54px);
    line-height:1.04;
    letter-spacing:-2px;
}

.hero p{
    max-width:780px;
    margin:auto;
    color:var(--muted);
    line-height:1.7;
}

.layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(370px,.9fr);
    gap:24px;
    align-items:start;
    padding-bottom:70px;
}

.card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:25px;
}

.card h2{
    margin:0 0 6px;
    font-size:22px;
}

.sub{
    margin-bottom:22px;
    color:var(--muted);
    font-size:13px;
    line-height:1.6;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.field{margin-bottom:15px}

label{
    display:block;
    margin-bottom:7px;
    font-size:12px;
    font-weight:850;
}

input,select{
    width:100%;
    min-height:49px;
    padding:0 12px;
    border:1px solid var(--line);
    border-radius:11px;
    background:#fff;
    font:inherit;
}

.hint{
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
    margin-top:6px;
}

.divider{
    border:0;
    border-top:1px solid var(--line);
    margin:22px 0;
}

.section-title{
    font-size:14px;
    font-weight:950;
    margin-bottom:13px;
}

.btn{
    width:100%;
    min-height:54px;
    border:0;
    border-radius:12px;
    background:var(--yellow);
    font-size:15px;
    font-weight:950;
    cursor:pointer;
}

.result{display:none}
.result.show{display:block}

.result-head{
    padding:21px;
    background:var(--dark);
    color:#fff;
    border-radius:15px;
    margin-bottom:16px;
}

.result-label{
    color:#b9bec8;
    font-size:11px;
    font-weight:850;
    text-transform:uppercase;
}

.total{
    font-size:34px;
    font-weight:950;
    margin-top:12px;
}

.total-sub{
    color:#b9bec8;
    font-size:11px;
    margin-top:5px;
}

.rows{
    border:1px solid var(--line);
    border-radius:13px;
    overflow:hidden;
}

.row{
    display:flex;
    justify-content:space-between;
    gap:15px;
    padding:12px 14px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.row:last-child{border-bottom:0}
.row span{color:var(--muted)}
.row strong{text-align:right}

.items{
    margin-top:15px;
}

.item{
    padding:13px;
    margin-bottom:8px;
    background:#f8fafc;
    border:1px solid var(--line);
    border-radius:12px;
}

.item-top{
    display:flex;
    justify-content:space-between;
    gap:12px;
    font-size:12px;
    font-weight:900;
}

.item-meta{
    color:var(--muted);
    font-size:10px;
    margin-top:5px;
    line-height:1.5;
}

.warning{
    display:none;
    margin-top:13px;
    padding:13px;
    border:1px solid #fed7aa;
    background:#fff7ed;
    color:var(--orange);
    border-radius:11px;
    font-size:12px;
    line-height:1.55;
}

.budget{
    display:none;
    margin-top:18px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:15px;
    padding:19px;
    text-align:center;
}

.budget-title{
    font-size:18px;
    font-weight:950;
}

.budget-text{
    color:var(--muted);
    font-size:12px;
    line-height:1.6;
    margin:8px 0 14px;
}

.actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:9px;
}

.actions button{
    min-height:45px;
    border:1px solid var(--line);
    background:#fff;
    border-radius:10px;
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
    margin-top:13px;
}

.quote a{
    min-height:53px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:10px;
    border-radius:11px;
    background:var(--yellow);
    font-weight:950;
}

.no-interest{
    display:none;
    color:var(--muted);
    font-size:12px;
    margin-top:11px;
}

.info{
    margin-bottom:60px;
    padding:18px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#fff;
    color:var(--muted);
    font-size:12px;
    line-height:1.7;
}

.info strong{color:var(--text)}

.rect-fields,
.round-fields{
    display:none;
}

.show{display:block}

@media(max-width:850px){
    .layout{grid-template-columns:1fr}
}

@media(max-width:620px){
    .grid,.actions{grid-template-columns:1fr}
    .card{padding:19px}
}
</style>

<script type="application/ld+json">
{
  "@@context":"https://schema.org",
  "@@type":"WebApplication",
  "name":"Havalandırma Kanalı ve HVAC Maliyet Hesaplayıcı",
  "url":"https://worksitem.com/araclar/havalandirma-hesaplayici",
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

<body class="wc" data-tool-slug="havalandirma-calculator">

<x-site.contech-header />


<div class="wc-wrap" style="padding-bottom:0">
    <x-calc.hero title="Havalandırma kanalı ve HVAC maliyeti hesaplama" crumb="Havalandırma kanalı">
        Kanalın gerçek sac yüzeyini hesaplayın; izolasyon, menfez, lineer difüzör ve aspiratörleri ayrı YFK pozlarıyla bütçeye ekleyin.
    </x-calc.hero>
</div>


<main class="container">

<div class="layout">

<section class="card">

    <h2>Havalandırma Projesi</h2>

    <div class="sub">
        Sistem otomatik mühendislik projesi yapmaz.
        Kanal ve fan seçimleri proje mühendisi tarafından
        debi ve basınç hesabıyla doğrulanmalıdır.
    </div>


    <div class="grid">

        <div class="field">

            <label>Proje Tipi</label>

            <select id="projectType">
                <option value="fabrika">Fabrika / Üretim</option>
                <option value="depo">Depo / Lojistik</option>
                <option value="ofis">Ofis / Fit-Out</option>
                <option value="magaza">Mağaza / Restoran</option>
                <option value="otopark">Otopark</option>
                <option value="diger">Diğer</option>
            </select>

        </div>


        <div class="field">

            <label>Proje Debisi (m³/h)</label>

            <input
                type="number"
                id="airflow"
                min="0"
                step="1"
                placeholder="Örn. 12000"
            >

        </div>

    </div>


    <hr class="divider">


    <div class="section-title">
        Hava Kanalı
    </div>


    <div class="field">

        <label>Kanal Tipi</label>

        <select id="ductType">
            <option value="rectangular">Dikdörtgen Galvaniz Kanal</option>
            <option value="spiral">Spiral Galvaniz Kanal</option>
            <option value="ei60">Yangına Dayanıklı Duman Kanalı EI60</option>
            <option value="ei120">Yangına Dayanıklı Duman Kanalı EI120</option>
        </select>

    </div>


    <div class="rect-fields" id="rectFields">

        <div class="grid">

            <div class="field">

                <label>Kanal Genişliği (mm)</label>

                <input
                    type="number"
                    id="ductWidth"
                    min="1"
                    step="1"
                    placeholder="Örn. 400"
                >

            </div>

            <div class="field">

                <label>Kanal Yüksekliği (mm)</label>

                <input
                    type="number"
                    id="ductHeight"
                    min="1"
                    step="1"
                    placeholder="Örn. 250"
                >

            </div>

        </div>

    </div>


    <div class="round-fields" id="roundFields">

        <div class="field">

            <label>Kanal Çapı (mm)</label>

            <input
                type="number"
                id="diameter"
                min="1"
                step="1"
                placeholder="Örn. 315"
            >

        </div>

    </div>


    <div class="grid">

        <div class="field">

            <label>Düz Kanal Uzunluğu (m)</label>

            <input
                type="number"
                id="ductLength"
                min="0"
                step="0.01"
                placeholder="Örn. 30"
            >

        </div>


        <div class="field">

            <label>Ek Fitting / Parça Yüzeyi (m²)</label>

            <input
                type="number"
                id="extraSurface"
                min="0"
                step="0.01"
                value="0"
            >

        </div>

    </div>

    <div class="hint">
        Dirsek, redüksiyon, branşman vb. için otomatik keyfi yüzde eklenmez.
        Gerçek yüzey biliniyorsa ayrıca girilir.
    </div>


    <hr class="divider">


    <div class="section-title">
        Kanal İzolasyonu
    </div>


    <div class="field">

        <label>İzolasyon</label>

        <select id="insulation">

            <option value="">Yok</option>

            @foreach($catalog['insulation'] as $item)

                <option value="{{ $item['poz'] }}">
                    {{ $item['label'] }}
                </option>

            @endforeach

        </select>

    </div>


    <hr class="divider">


    <div class="section-title">
        Menfez / Difüzör
    </div>


    <div class="grid">

        <div class="field">

            <label>Menfez Tipi</label>

            <select id="grille">

                <option value="">Yok</option>

                @foreach($catalog['grilles'] as $item)

                    <option value="{{ $item['poz'] }}">
                        {{ $item['label'] }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="field">

            <label>Menfez Adedi</label>

            <input
                type="number"
                id="grilleQty"
                min="0"
                step="1"
                value="0"
            >

        </div>

    </div>


    <div class="grid">

        <div class="field">

            <label>Lineer Difüzör</label>

            <select id="slot">

                <option value="">Yok</option>

                @foreach($catalog['slots'] as $item)

                    <option value="{{ $item['poz'] }}">
                        {{ $item['label'] }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="field">

            <label>Toplam Lineer Difüzör Uzunluğu (m)</label>

            <input
                type="number"
                id="slotLength"
                min="0"
                step="0.01"
                value="0"
            >

        </div>

    </div>


    <hr class="divider">


    <div class="section-title">
        Aspiratör
    </div>


    <div class="grid">

        <div class="field">

            <label>Kanal Tipi Aspiratör</label>

            <select id="fan">

                <option value="">Yok</option>

                @foreach($catalog['fans'] as $item)

                    <option value="{{ $item['poz'] }}">
                        {{ $item['label'] }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="field">

            <label>Adet</label>

            <input
                type="number"
                id="fanQty"
                min="0"
                step="1"
                value="0"
            >

        </div>

    </div>


    <button class="btn" id="calculateBtn">
        Havalandırma Metraj ve Maliyetini Hesapla →
    </button>

</section>


<section>

<div class="card result" id="result" data-wc-result>

    <div class="result-head">

        <div class="result-label">
            YFK Referans Bütçesi
        </div>

        <div class="total" id="rTotal" data-wc-price>
            -
        </div>

        <div class="total-sub">
            Seçilen doğrulanmış mekanik imalatların toplamı
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Kanal yüzeyi</span>
            <strong id="rSurface">-</strong>
        </div>

        <div class="row">
            <span>Kanal uzunluğu</span>
            <strong id="rLength">-</strong>
        </div>

        <div class="row">
            <span>Proje debisi</span>
            <strong id="rAirflow">-</strong>
        </div>

        <div class="row">
            <span>Doğrulanmış kalem sayısı</span>
            <strong id="rItems">-</strong>
        </div>

    </div>


    <div class="items" id="itemResults"></div>

    <div class="warning" id="warningBox"></div>

</div>


<div class="budget" id="budgetBox">

    <div class="budget-title">
        Bu bütçe projeniz için uygun mu?
    </div>

    <div class="budget-text">
        Bütçe uygunsa Kainta İnşaat'tan havalandırma,
        elektrik, zemin, çatı ve diğer uygulamalarla birlikte
        profesyonel net teklif alabilirsiniz.
    </div>


    <div class="actions">

        <button class="yes" id="budgetYes" type="button">
            ✓ Evet, teklif istiyorum
        </button>

        <button id="budgetNo" type="button">
            Sadece maliyete baktım
        </button>

    </div>


    <div class="quote" id="quoteBox">

        <a
            href="/maliyet-hesapla?service=havalandirma"
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


<div class="info">

    <strong>Önemli:</strong>
    Bu araç ön keşif ve referans bütçe içindir.
    Kanal kesitinin ve fan kapasitesinin uygunluğu yalnız
    debiyle değil; hız, basınç kaybı, cihaz basıncı,
    ses kriteri, yangın senaryosu ve proje şartlarıyla
    birlikte değerlendirilmelidir.

</div>

</main>


<script>

const CATALOG = {!! $catalogJson !!};

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


function findByPoz(group, poz) {

    return (
        CATALOG[group] || []
    ).find(
        item => item.poz === poz
    ) || null;
}


function refreshDuctFields() {

    const type =
        document.getElementById('ductType').value;

    document.getElementById('rectFields')
        .classList.toggle(
            'show',
            type !== 'spiral'
        );

    document.getElementById('roundFields')
        .classList.toggle(
            'show',
            type === 'spiral'
        );
}


function getDuctData() {

    const type =
        document.getElementById('ductType').value;

    const length =
        Math.max(
            0,
            numeric('ductLength')
        );

    const extra =
        Math.max(
            0,
            numeric('extraSurface')
        );


    if (length <= 0) {
        return null;
    }


    if (type === 'spiral') {

        const diameter =
            numeric('diameter');

        if (diameter <= 0) {
            return null;
        }

        const surface =
            Math.PI *
            (diameter / 1000) *
            length +
            extra;


        const item =
            CATALOG.spiral.find(
                row =>
                    diameter <= row.max_mm
            ) || null;


        return {
            surface,
            item,
            type,
            dimension:
                'Ø' +
                number.format(diameter) +
                ' mm'
        };
    }


    const width =
        numeric('ductWidth');

    const height =
        numeric('ductHeight');


    if (
        width <= 0 ||
        height <= 0
    ) {
        return null;
    }


    const surface =
        2 *
        (
            (width / 1000) +
            (height / 1000)
        ) *
        length +
        extra;


    if (
        type === 'ei60' ||
        type === 'ei120'
    ) {

        const item =
            CATALOG.fire.find(
                row =>
                    row.key === type
            ) || null;


        return {
            surface,
            item,
            type,
            dimension:
                number.format(width) +
                '×' +
                number.format(height) +
                ' mm'
        };
    }


    const maxSide =
        Math.max(
            width,
            height
        );


    const item =
        CATALOG.rectangular.find(
            row =>
                maxSide <= row.max_mm
        ) || null;


    return {
        surface,
        item,
        type,
        dimension:
            number.format(width) +
            '×' +
            number.format(height) +
            ' mm'
    };
}


function addLine(
    lines,
    item,
    quantity,
    quantityUnit
) {

    if (
        !item ||
        quantity <= 0
    ) {
        return;
    }


    if (
        !item.price_found ||
        item.unit_price === null
    ) {

        lines.push({
            found:false,
            label:item.label,
            poz:item.poz,
            quantity,
            quantityUnit
        });

        return;
    }


    const unitPrice =
        parseFloat(
            item.unit_price
        );


    lines.push({
        found:true,
        label:item.label,
        poz:item.poz,
        quantity,
        quantityUnit,
        unitPrice,
        total:
            quantity *
            unitPrice
    });
}


function calculate() {

    const duct =
        getDuctData();


    if (!duct) {

        alert(
            'Lütfen geçerli kanal ölçüsü ve uzunluğu girin.'
        );

        return;
    }


    const lines = [];

    // v120: katalog dışı kanal ölçüsü (ör. Ø2000) eskiden satırı hiç eklemiyordu → "0,00 TL" tam sonuç
    // ve bütçe onayı açılıyordu. Artık eksik kalem olarak listelenir.
    if (duct.item) {
        addLine(lines, duct.item, duct.surface, 'm²');
    } else if (duct.surface > 0) {
        lines.push({
            found:false,
            label:'Kanal ' + duct.dimension + ' (katalog dışı ölçü)',
            poz:'—',
            quantity:duct.surface,
            quantityUnit:'m²'
        });
    }


    const insulationPoz =
        document.getElementById('insulation').value;


    if (insulationPoz) {

        addLine(
            lines,
            findByPoz(
                'insulation',
                insulationPoz
            ),
            duct.surface,
            'm²'
        );
    }


    const grillePoz =
        document.getElementById('grille').value;

    const grilleQty =
        Math.max(
            0,
            numeric('grilleQty')
        );


    if (
        grillePoz &&
        grilleQty > 0
    ) {

        addLine(
            lines,
            findByPoz(
                'grilles',
                grillePoz
            ),
            grilleQty,
            'Ad'
        );
    }


    const slotPoz =
        document.getElementById('slot').value;

    const slotLength =
        Math.max(
            0,
            numeric('slotLength')
        );


    if (
        slotPoz &&
        slotLength > 0
    ) {

        addLine(
            lines,
            findByPoz(
                'slots',
                slotPoz
            ),
            slotLength,
            'm'
        );
    }


    const fanPoz =
        document.getElementById('fan').value;

    const fanQty =
        Math.max(
            0,
            numeric('fanQty')
        );


    if (
        fanPoz &&
        fanQty > 0
    ) {

        addLine(
            lines,
            findByPoz(
                'fans',
                fanPoz
            ),
            fanQty,
            'Ad'
        );
    }


    let grandTotal = 0;
    let allFound = !!duct.item && lines.length > 0;
    let verifiedCount = 0;
    let missingCount = 0;


    lines.forEach(line => {

        if (!line.found) {
            allFound = false;
            missingCount++;
            return;
        }

        grandTotal +=
            line.total;

        verifiedCount++;
    });


    const itemResults =
        document.getElementById('itemResults');

    itemResults.innerHTML = '';


    lines.forEach(line => {

        const div =
            document.createElement('div');

        div.className =
            'item';


        if (line.found) {

            div.innerHTML =
                '<div class="item-top">' +
                    '<span>' +
                        line.label +
                    '</span>' +
                    '<span>' +
                        money.format(line.total) +
                        ' TL' +
                    '</span>' +
                '</div>' +

                '<div class="item-meta">' +
                    'Poz ' +
                    line.poz +
                    ' · ' +
                    money.format(line.unitPrice) +
                    ' TL/' +
                    line.quantityUnit +
                    ' × ' +
                    number.format(line.quantity) +
                    ' ' +
                    line.quantityUnit +
                '</div>';

        } else {

            div.innerHTML =
                '<div class="item-top">' +
                    '<span>' +
                        line.label +
                    '</span>' +
                    '<span>Fiyat yok</span>' +
                '</div>' +

                '<div class="item-meta">' +
                    'Doğrulanmış fiyat bulunmadığından toplama eklenmedi.' +
                '</div>';
        }


        itemResults.appendChild(div);
    });


    document.getElementById('rTotal')
        .innerText =
            allFound && grandTotal > 0
            ? money.format(grandTotal) + ' TL'
            : (verifiedCount > 0
                ? money.format(grandTotal) + ' TL + ' + missingCount + ' eksik kalem'
                : 'Hesaplanamadı (' + Math.max(missingCount, 1) + ' kalemde fiyat yok)');


    document.getElementById('rSurface')
        .innerText =
            number.format(
                duct.surface
            ) +
            ' m²';


    document.getElementById('rLength')
        .innerText =
            number.format(
                numeric('ductLength')
            ) +
            ' m';


    const airflow =
        numeric('airflow');


    document.getElementById('rAirflow')
        .innerText =
            airflow > 0
            ? number.format(airflow) + ' m³/h'
            : 'Girilemedi';


    document.getElementById('rItems')
        .innerText =
            verifiedCount +
            ' kalem';


    const warnings = [];


    if (!duct.item) {

        warnings.push(
            'Bu kanal ölçüsü için doğrulanmış uygun kanal pozu bulunamadı; kanal bedeli toplama dahil değil.'
        );
    }

    if (missingCount > 0) {
        warnings.push(
            missingCount + ' kalemin fiyatı bulunamadı; toplam eksiktir, bütçe onayı için kullanılamaz.'
        );
    }


    warnings.push(
        'Fan kapasitesi yalnız debiye bakılarak otomatik seçilmedi. ' +
        'Statik basınç ve sistem kayıpları proje mühendisi tarafından doğrulanmalıdır.'
    );


    if (
        numeric('extraSurface') <= 0
    ) {

        warnings.push(
            'Dirsek, redüksiyon, branşman ve özel parçalar için ek yüzey girilmediyse bu parçalar kanal metrajına dahil değildir.'
        );
    }


    const warningBox =
        document.getElementById('warningBox');


    warningBox.style.display =
        'block';

    warningBox.innerText =
        warnings.join(' ');


    const projectType =
        document.getElementById('projectType').value;


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=havalandirma' +
        '&duct_area=' +
        encodeURIComponent(
            duct.surface.toFixed(2)
        ) +
        '&airflow=' +
        encodeURIComponent(
            airflow
        ) +
        '&duct_type=' +
        encodeURIComponent(
            duct.type
        ) +
        '&project_type=' +
        encodeURIComponent(
            projectType
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
            allFound && grandTotal > 0
            ? 'block'
            : 'none';


    document.getElementById('result')
        .classList.add('show');


    document.getElementById('result')
        .scrollIntoView({
            behavior:'smooth',
            block:'start'
        });
}


document.getElementById('ductType')
    .addEventListener(
        'change',
        refreshDuctFields
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


refreshDuctFields();

</script>

<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
