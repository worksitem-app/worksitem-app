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

<title>Su Yalıtımı Metraj ve Maliyet Hesaplayıcı | WORKSİTEM</title>

<meta name="description"
      content="Teras, temel, bodrum, perde, balkon, banyo, otopark ve çatı su yalıtımı için metraj ve doğrulanmış 2026 YFK referans maliyetini hesaplayın.">

<meta name="robots" content="index,follow,max-image-preview:large">

<link rel="canonical"
      href="https://worksitem.com/araclar/su-yalitimi-hesaplayici">

<style>
:root{
    --bg:#f5f6f8;
    --surface:#fff;
    --surface2:#f8fafc;
    --text:#101318;
    --muted:#68707d;
    --line:#e2e5e9;
    --yellow:#ffb800;
    --yellow2:#e3a000;
    --dark:#111318;
    --blue:#2563eb;
    --orange:#c2410c;
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
        radial-gradient(circle at 50% -8%,rgba(37,99,235,.08),transparent 28%),
        var(--bg);
}

a{text-decoration:none;color:inherit}

.header{
    height:68px;
    display:flex;
    align-items:center;
    border-bottom:1px solid var(--line);
    background:rgba(255,255,255,.94);
    backdrop-filter:blur(15px);
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
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1d4ed8;
    font-size:12px;
    font-weight:900;
    margin-bottom:16px;
}

.hero h1{
    max-width:860px;
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
    font-size:16px;
}

.layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(360px,.86fr);
    gap:24px;
    align-items:start;
    padding-bottom:65px;
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
    line-height:1.6;
    margin-bottom:22px;
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
    border:1px solid var(--line);
    border-radius:12px;
    background:#fff;
    padding:0 13px;
    color:var(--text);
    font:inherit;
    outline:none;
}

input:focus,select:focus{
    border-color:#dba500;
    box-shadow:0 0 0 3px rgba(255,184,0,.12);
}

.hint{
    color:var(--muted);
    font-size:11px;
    line-height:1.5;
    margin-top:6px;
}

.system-note{
    margin-top:8px;
    padding:10px 12px;
    border-radius:10px;
    background:#f8fafc;
    border:1px solid var(--line);
    color:var(--muted);
    font-size:11px;
    line-height:1.55;
}

.divider{
    border:0;
    border-top:1px solid var(--line);
    margin:22px 0;
}

.subhead{
    font-size:14px;
    font-weight:900;
    margin-bottom:14px;
}

.btn{
    width:100%;
    min-height:54px;
    border:0;
    border-radius:13px;
    background:var(--yellow);
    color:#111;
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
    color:#aeb4bf;
    font-size:11px;
    font-weight:850;
    text-transform:uppercase;
    letter-spacing:.7px;
}

.result-system{
    margin-top:7px;
    font-size:19px;
    line-height:1.35;
    font-weight:900;
}

.total{
    margin-top:17px;
    font-size:34px;
    font-weight:950;
    letter-spacing:-1.3px;
}

.total-sub{
    color:#bac0c8;
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
    gap:18px;
    padding:13px 15px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.row:last-child{border-bottom:0}
.row span{color:var(--muted)}
.row strong{text-align:right}

.source{
    margin-top:16px;
    padding:14px;
    border:1px solid var(--line);
    border-radius:13px;
    background:#f7f8fa;
    color:var(--muted);
    font-size:12px;
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
    color:#9a3412;
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
    min-height:54px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:10px 15px;
    border-radius:12px;
    background:var(--yellow);
    color:#111;
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

.system-list{
    margin-top:24px;
    padding-top:20px;
    border-top:1px solid var(--line);
}

.system-list-title{
    font-size:13px;
    font-weight:900;
    margin-bottom:10px;
}

.system-tags{
    display:flex;
    flex-wrap:wrap;
    gap:7px;
}

.system-tag{
    padding:6px 9px;
    background:#f8fafc;
    border:1px solid var(--line);
    border-radius:999px;
    color:var(--muted);
    font-size:10px;
    font-weight:700;
}

@media(max-width:850px){
    .layout{grid-template-columns:1fr}
}

@media(max-width:620px){
    .form-grid,.actions,.info-grid{
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
  "name":"Su Yalıtımı Metraj ve Maliyet Hesaplayıcı",
  "url":"https://worksitem.com/araclar/su-yalitimi-hesaplayici",
  "applicationCategory":"BusinessApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"Su yalıtım sistemleri için metraj ve doğrulanmış YFK referans maliyet hesaplama aracı."
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

<body data-tool-slug="su-yalitimi-calculator">

<x-site.contech-header />


<section class="hero">

<div class="container">

    <div class="kicker">
        CONTECH · SU YALITIMI
    </div>

    <h1>
        Su Yalıtımı Metraj ve
        Maliyet Hesaplayıcı
    </h1>

    <p>
        Teras, temel, bodrum, perde, balkon,
        ıslak hacim, otopark ve çatı uygulamalarında
        gerçek yalıtım yüzeyinizi hesaplayın.
        Uygun sistemlerde doğrulanmış 2026-08 YFK
        referans maliyetini görün.
    </p>

</div>

</section>


<main class="container">

<div class="layout">

<section class="card">

    <h2>Yalıtım Projesi</h2>

    <div class="card-sub">
        Önce uygulama yerini seçin. WORKSİTEM yalnızca
        o kullanım için ilgili sistemleri listeler.
        Nihai sistem seçimi proje şartlarına göre teknik
        olarak doğrulanmalıdır.
    </div>


    <div class="field">

        <label>Uygulama Yeri</label>

        <select id="application">
            <option value="teras">Teras / Düz Çatı</option>
            <option value="egimli_cati">Eğimli Çatı Altı</option>
            <option value="cati">Membran Çatı</option>
            <option value="balkon">Balkon</option>
            <option value="banyo">Banyo / WC / Islak Hacim</option>
            <option value="temel">Temel / Bohçalama</option>
            <option value="bodrum">Bodrum</option>
            <option value="perde">Toprak Temaslı Perde</option>
            <option value="otopark">Otopark</option>
            <option value="fabrika">Fabrika / Endüstriyel Alan</option>
        </select>

    </div>


    <div class="field">

        <label>Yalıtım Sistemi</label>

        <select id="system"></select>

        <div class="system-note" id="systemNote">
            -
        </div>

    </div>


    <div class="field">

        <label>Proje Tipi</label>

        <select id="projectType">
            <option value="konut">Konut</option>
            <option value="fabrika">Fabrika / Üretim Tesisi</option>
            <option value="depo">Depo / Lojistik</option>
            <option value="ofis">Ofis / Ticari Alan</option>
            <option value="otel">Otel</option>
            <option value="saglik">Sağlık Yapısı</option>
            <option value="diger">Diğer</option>
        </select>

    </div>


    <hr class="divider">

    <div class="subhead">
        Uygulama Metrajı
    </div>


    <div class="field">

        <label>Yatay / Ana Yüzey Alanı (m²)</label>

        <input
            type="number"
            id="area"
            min="0"
            step="0.01"
            placeholder="Örn. 350"
        >

        <div class="hint">
            Eğimli çatıda izdüşüm değil, gerçek çatı
            yüzey alanını girin.
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

    </div>

    <div class="hint" style="margin-top:-8px;margin-bottom:18px">
        Alanı bilmiyorsanız uzunluk × genişlik kullanabilirsiniz.
        Alan kutusu doluysa doğrudan alan değeri esas alınır.
    </div>


    <div class="form-grid">

        <div class="field">
            <label>Dönüş Yapılacak Çevre (m)</label>
            <input type="number" id="perimeter" min="0" step="0.01" value="0">
        </div>

        <div class="field">
            <label>Düşey Dönüş Yüksekliği (cm)</label>
            <input type="number" id="upturnHeight" min="0" step="1" value="20">
        </div>

        <div class="field">
            <label>Ek Perde / Duvar Alanı (m²)</label>
            <input type="number" id="verticalArea" min="0" step="0.01" value="0">
        </div>

        <div class="field">
            <label>Malzeme Fire / Bindirme (%)</label>
            <input type="number" id="waste" min="0" max="30" step="0.5" value="5">
        </div>

    </div>


    <div class="field">

        <label>Mevcut Kaplama / Yalıtım Sökümü</label>

        <select id="demolition">
            <option value="no">Yok</option>
            <option value="yes">Var</option>
        </select>

    </div>


    <button class="btn" id="calculateBtn">
        Metraj ve Referans Maliyeti Hesapla →
    </button>


    <div class="system-list">

        <div class="system-list-title">
            Hesap motorundaki sistemler
        </div>

        <div class="system-tags">
            @foreach($systems as $system)
                <span class="system-tag">
                    {{ $system['label'] }}
                </span>
            @endforeach
        </div>

    </div>

</section>


<section>

<div class="card result" id="result">

    <div class="result-top">

        <div class="result-label">
            Seçilen su yalıtım sistemi
        </div>

        <div class="result-system" id="rSystem">
            -
        </div>

        <div class="total" id="rTotal">
            -
        </div>

        <div class="total-sub" id="rTotalSub">
            2026-08 YFK referans yaklaşık imalat tutarı
        </div>

    </div>


    <div class="rows">

        <div class="row">
            <span>Ana yüzey alanı</span>
            <strong id="rBase">-</strong>
        </div>

        <div class="row">
            <span>Düşey dönüş alanı</span>
            <strong id="rUpturn">-</strong>
        </div>

        <div class="row">
            <span>Ek perde / duvar alanı</span>
            <strong id="rVertical">-</strong>
        </div>

        <div class="row">
            <span>Net yalıtım uygulama alanı</span>
            <strong id="rNet">-</strong>
        </div>

        <div class="row">
            <span>Fire / bindirme dahil malzeme alanı</span>
            <strong id="rOrder">-</strong>
        </div>

        <div class="row">
            <span>Referans poz</span>
            <strong id="rPoz">-</strong>
        </div>

        <div class="row">
            <span>YFK referans birim fiyatı</span>
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


<div class="budget" id="budgetBox">

    <div class="budget-title">
        Bu bütçe aralığı projeniz için uygun mu?
    </div>

    <div class="budget-text">
        Referans bütçe uygunsa Kainta İnşaat'tan
        saha şartları ve uygulanacak sistem dikkate
        alınarak profesyonel net teklif isteyebilirsiniz.
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
            href="/maliyet-hesapla?service=su-yalitimi"
            id="quoteLink"
        >
            Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
        </a>

    </div>

    <div class="no-interest" id="noInterest">
        Bu aşamada iletişim bilginizi almıyoruz.
        Uygulama kararınız netleştiğinde tekrar
        hesaplayabilirsiniz.
    </div>

</div>

</section>

</div>


<section class="info-grid">

    <article class="info">

        <strong>YFK referansı satış fiyatı değildir</strong>

        <p>
            Sonuç doğrulanmış referans poz üzerinden
            hesaplanır. Nihai fiyat; yüzey hazırlığı,
            detay çözümleri, marka, iş programı,
            ulaşım, şantiye şartları ve uygulama
            kapsamına göre değişebilir.
        </p>

    </article>


    <article class="info">

        <strong>Düşey dönüşleri unutmayın</strong>

        <p>
            Teras ve ıslak hacimlerde yalnız zemin alanı
            değil, süpürgelik ve parapet dönüşleri de
            gerçek uygulama alanının parçasıdır.
        </p>

    </article>


    <article class="info">

        <strong>Fire ayrı gösterilir</strong>

        <p>
            Fire ve bindirme malzeme sipariş alanına
            uygulanır. YFK tamamlanmış imalat referans
            bedeli ise gerçek uygulama alanı üzerinden
            hesaplanır.
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


function val(id) {

    const n =
        parseFloat(
            document.getElementById(id).value
        );

    return Number.isFinite(n)
        ? n
        : 0;
}


function mainArea() {

    const direct =
        val('area');

    if (direct > 0) {
        return direct;
    }

    return (
        val('length') *
        val('width')
    );
}


function refreshSystems() {

    const application =
        document.getElementById('application').value;

    const select =
        document.getElementById('system');

    const current =
        select.value;

    select.innerHTML = '';

    Object.entries(SYSTEMS)
        .forEach(([key, system]) => {

            const applications =
                Array.isArray(system.applications)
                ? system.applications
                : [];

            if (
                !applications.includes(application)
            ) {
                return;
            }

            const option =
                document.createElement('option');

            option.value = key;

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
        current &&
        Array.from(select.options)
            .some(option => option.value === current)
    ) {

        select.value = current;
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
            : 'Bu kullanım için sistem tanımlanamadı.';
}


function calculate() {

    const systemKey =
        document.getElementById('system').value;

    const system =
        SYSTEMS[systemKey];

    if (!system) {
        alert('Lütfen geçerli bir su yalıtım sistemi seçin.');
        return;
    }


    const baseArea =
        mainArea();

    const perimeter =
        Math.max(
            0,
            val('perimeter')
        );

    const upturnHeightCm =
        Math.max(
            0,
            val('upturnHeight')
        );

    const verticalArea =
        Math.max(
            0,
            val('verticalArea')
        );

    const upturnArea =
        perimeter *
        (upturnHeightCm / 100);

    const netArea =
        Math.max(
            0,
            baseArea
        ) +
        upturnArea +
        verticalArea;


    if (netArea <= 0) {

        alert(
            'Lütfen yalıtım uygulanacak alanı girin.'
        );

        return;
    }


    let waste =
        val('waste');

    waste =
        Math.max(
            0,
            Math.min(30, waste)
        );

    const orderArea =
        netArea *
        (1 + waste / 100);


    document.getElementById('rSystem')
        .innerText =
            system.label;

    document.getElementById('rBase')
        .innerText =
            number.format(baseArea) +
            ' m²';

    document.getElementById('rUpturn')
        .innerText =
            number.format(upturnArea) +
            ' m²';

    document.getElementById('rVertical')
        .innerText =
            number.format(verticalArea) +
            ' m²';

    document.getElementById('rNet')
        .innerText =
            number.format(netArea) +
            ' m²';

    document.getElementById('rOrder')
        .innerText =
            number.format(orderArea) +
            ' m²';

    document.getElementById('rPoz')
        .innerText =
            system.poz || '-';


    if (
        system.price_found &&
        system.unit_price !== null
    ) {

        const unitPrice =
            parseFloat(
                system.unit_price
            );

        /*
         * YFK poz fiyatı tamamlanmış imalat m² bedelidir.
         * Fire/bindirme YFK toplamına ikinci kez eklenmez.
         */
        const referenceTotal =
            netArea *
            unitPrice;


        document.getElementById('rUnitPrice')
            .innerText =
                money.format(unitPrice) +
                ' TL/m²';

        document.getElementById('rPeriod')
            .innerText =
                system.price_period ||
                '2026-08';

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

        document.getElementById('budgetBox')
            .style.display =
                'block';

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
                'Doğrulanmış fiyat bulunamadı.';

        document.getElementById('budgetBox')
            .style.display =
                'none';
    }


    const warnings = [];

    if (
        document.getElementById('demolition').value
        === 'yes'
    ) {

        warnings.push(
            'Mevcut kaplama veya yalıtım sökümü seçildi. ' +
            'Doğrulanmış söküm fiyatı bulunmadığından ' +
            'söküm bedeli referans toplama eklenmedi.'
        );
    }

    if (waste > 0) {

        warnings.push(
            'Fire/bindirme yalnız malzeme sipariş alanında gösterildi; ' +
            'YFK imalat bedeline ikinci kez eklenmedi.'
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


    const application =
        document.getElementById('application').value;

    const projectType =
        document.getElementById('projectType').value;


    const quoteUrl =
        '/maliyet-hesapla' +
        '?service=su-yalitimi' +
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
        refreshSystems
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


refreshSystems();
</script>

    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
