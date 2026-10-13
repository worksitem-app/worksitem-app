@php

$priceService = app(\App\Services\PriceService::class);

$coverings = [

    'clay_alaturka' => [
        'label' => 'Alaturka Kil Kiremit · 3 Latalı',
        'poz' => '15.305.1001',
        'ridge_poz' => '15.305.1005',
        'ridge_label' => 'Kil Mahya Kiremidi',
        'allow_underlay' => true,
    ],

    'clay_interlocking' => [
        'label' => 'Kenetlenebilen Kil Kiremit · 2 Latalı',
        'poz' => '15.305.1003',
        'ridge_poz' => '15.305.1005',
        'ridge_label' => 'Kil Mahya Kiremidi',
        'allow_underlay' => true,
    ],

    'concrete_tile' => [
        'label' => 'Beton Kiremit · Çift Latalı',
        'poz' => '15.305.1201',
        'ridge_poz' => '15.305.1204',
        'ridge_label' => 'Beton Mahya Kiremidi',
        'allow_underlay' => true,
    ],

    'trapezoid_timber' => [
        'label' => 'Galvaniz Trapez Sac · Ahşap Çatı',
        'poz' => '15.325.1009',
        'ridge_poz' => null,
        'ridge_label' => null,
        'allow_underlay' => false,
    ],

    'trapezoid_steel' => [
        'label' => 'Galvaniz Trapez Sac · Çelik / Betonarme Kiriş',
        'poz' => '15.325.1109',
        'ridge_poz' => null,
        'ridge_label' => null,
        'allow_underlay' => false,
    ],

    'bitumen_timber' => [
        'label' => 'Oluklu Bitümlü Levha · Ahşap Çatı',
        'poz' => '15.325.1102',
        'ridge_poz' => null,
        'ridge_label' => null,
        'allow_underlay' => false,
    ],

    'bitumen_rc' => [
        'label' => 'Oluklu Bitümlü Levha · Betonarme Çatı',
        'poz' => '15.325.1105',
        'ridge_poz' => null,
        'ridge_label' => null,
        'allow_underlay' => false,
    ],

    'bitumen_steel' => [
        'label' => 'Oluklu Bitümlü Levha · Çelik / Betonarme Kiriş',
        'poz' => '15.325.1106',
        'ridge_poz' => null,
        'ridge_label' => null,
        'allow_underlay' => false,
    ],
];

$underlays = [

    'none' => [
        'label' => 'Alt Kaplama Yok',
        'poz' => null,
    ],

    'timber' => [
        'label' => 'Tahta Alt Kaplama',
        'poz' => '15.300.1005',
    ],

    'osb' => [
        'label' => 'OSB/3 Alt Kaplama',
        'poz' => '15.300.1006',
    ],
];

$waterproofings = [

    'none' => [
        'label' => 'Su Yalıtımı Yok',
        'poz' => null,
    ],

    'breathable' => [
        'label' => 'Su Buharı Geçişine Açık Çatı Altı Örtüsü',
        'poz' => '15.330.1003',
    ],

    'bitumen3' => [
        'label' => '3 mm Plastomer Polyester Keçe Bitümlü Örtü',
        'poz' => '15.330.1005',
    ],
];


$resolve = function (&$item) use ($priceService) {

    if (empty($item['poz'])) {
        $item['found'] = false;
        $item['price'] = null;
        $item['unit'] = null;
        return;
    }

    $r = $priceService->byPoz(
        $item['poz'],
        '2026-08',
        'YFK'
    );

    $item['found'] =
        is_array($r) &&
        isset($r['price']) &&
        is_numeric($r['price']);

    $item['price'] =
        $item['found']
        ? (float) $r['price']
        : null;

    $item['unit'] =
        $r['unit'] ?? null;
};


foreach ($coverings as &$item) {

    $resolve($item);

    $item['ridge_found'] = false;
    $item['ridge_price'] = null;

    if (!empty($item['ridge_poz'])) {

        $r = $priceService->byPoz(
            $item['ridge_poz'],
            '2026-08',
            'YFK'
        );

        if (
            is_array($r) &&
            isset($r['price']) &&
            is_numeric($r['price'])
        ) {
            $item['ridge_found'] = true;
            $item['ridge_price'] = (float) $r['price'];
        }
    }
}
unset($item);


foreach ($underlays as &$item) {
    $resolve($item);
}
unset($item);


foreach ($waterproofings as &$item) {
    $resolve($item);
}
unset($item);


$coveringsJson = json_encode(
    $coverings,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

$underlaysJson = json_encode(
    $underlays,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

$waterproofingsJson = json_encode(
    $waterproofings,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

@endphp

<!doctype html>
<html lang="tr">

<head>

<meta charset="utf-8">

<meta
    name="viewport"
    content="width=device-width,initial-scale=1"
>

<title>Çatı Maliyet & Kaplama Motoru | WORKSİTEM</title>

<meta
    name="description"
    content="Kiremit, trapez sac, bitümlü levha, OSB, tahta alt kaplama, çatı altı su yalıtımı ve mahya için 2026 YFK referans maliyet hesabı."
>

<link
    rel="canonical"
    href="https://worksitem.com/araclar/cati-maliyet-hesaplayici"
>

<style>

*{
    box-sizing:border-box
}

body{
    margin:0;
    font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background:#f5f6f8;
    color:#111318
}

a{
    color:inherit;
    text-decoration:none
}

header{
    background:#fff;
    border-bottom:1px solid #e5e7eb
}

.head{
    width:min(1100px,calc(100% - 30px));
    margin:auto;
    min-height:66px;
    display:flex;
    align-items:center;
    justify-content:space-between
}

.logo{
    font-weight:900
}

.logo span{
    color:#d99c00
}

.wrap{
    width:min(1100px,calc(100% - 30px));
    margin:auto
}

.hero{
    padding:46px 0 30px;
    text-align:center
}

.hero h1{
    margin:0 0 12px;
    font-size:clamp(30px,5vw,50px)
}

.hero p{
    max-width:760px;
    margin:auto;
    color:#68707d;
    line-height:1.6
}

.layout{
    display:grid;
    grid-template-columns:1fr .9fr;
    gap:22px;
    align-items:start;
    padding-bottom:60px
}

.card{
    background:#fff;
    border:1px solid #e2e5e9;
    border-radius:18px;
    padding:24px
}

h2{
    margin-top:0
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px
}

.field{
    margin-bottom:15px
}

label{
    display:block;
    font-size:12px;
    font-weight:800;
    margin-bottom:7px
}

input,
select{
    width:100%;
    min-height:48px;
    border:1px solid #dfe3e8;
    border-radius:10px;
    padding:0 12px;
    background:#fff
}

hr{
    border:0;
    border-top:1px solid #e5e7eb;
    margin:22px 0
}

button{
    cursor:pointer
}

.calc{
    width:100%;
    min-height:52px;
    border:0;
    border-radius:11px;
    background:#ffb800;
    font-weight:900
}

.result{
    display:none
}

.result.show{
    display:block
}

.totalbox{
    background:#111318;
    color:#fff;
    border-radius:14px;
    padding:20px;
    margin-bottom:15px
}

.total{
    font-size:32px;
    font-weight:900;
    margin-top:10px
}

.rows{
    border:1px solid #e2e5e9;
    border-radius:12px;
    overflow:hidden
}

.row{
    display:flex;
    justify-content:space-between;
    padding:12px 13px;
    border-bottom:1px solid #e5e7eb;
    font-size:13px
}

.row:last-child{
    border:0
}

.row span{
    color:#68707d
}

.items{
    margin-top:14px
}

.item{
    background:#f8fafc;
    border:1px solid #e2e5e9;
    border-radius:11px;
    padding:12px;
    margin-bottom:8px
}

.itemtop{
    display:flex;
    justify-content:space-between;
    gap:10px;
    font-size:12px;
    font-weight:800
}

.meta{
    margin-top:5px;
    color:#68707d;
    font-size:10px;
    line-height:1.5
}

.warn{
    margin-top:14px;
    padding:12px;
    background:#fff7ed;
    border:1px solid #fed7aa;
    border-radius:10px;
    color:#9a3412;
    font-size:12px;
    line-height:1.5
}

.budget{
    display:none;
    margin-top:15px;
    padding:18px;
    background:#fff;
    border:1px solid #e2e5e9;
    border-radius:14px;
    text-align:center
}

.actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:9px;
    margin-top:13px
}

.actions button{
    min-height:44px;
    border:1px solid #ddd;
    border-radius:9px;
    background:#fff;
    font-weight:800
}

.actions .yes{
    background:#111318;
    color:#fff;
    border-color:#111318
}

.quote{
    display:none;
    margin-top:12px
}

.quote a{
    min-height:52px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#ffb800;
    border-radius:10px;
    font-weight:900
}

.no{
    display:none;
    margin-top:10px;
    color:#68707d;
    font-size:12px
}

.sandvic{
    margin:0 0 55px;
    background:#fff;
    border:1px solid #e2e5e9;
    border-radius:14px;
    padding:17px;
    font-size:12px;
    line-height:1.6
}

.sandvic a{
    font-weight:900
}

@media(max-width:800px){

    .layout{
        grid-template-columns:1fr
    }

}

@media(max-width:600px){

    .grid,
    .actions{
        grid-template-columns:1fr
    }

}

</style>

<script type="application/ld+json">
{
    "@@context":"https://schema.org",
    "@@type":"WebApplication",
    "name":"Çatı Maliyet ve Kaplama Motoru",
    "url":"https://worksitem.com/araclar/cati-maliyet-hesaplayici",
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

<body class="wc" data-tool-slug="cati-calculator">

<x-site.contech-header />


<div class="wrap">

<div class="wc-wrap" style="padding-bottom:0">
    <x-calc.hero title="Çatı maliyeti ve kiremit metrajı hesaplama" crumb="Çatı maliyeti" :facts="['Gerçek eğimli çatı alanı', 'Kiremit, trapez sac, bitümlü levha', 'YFK 2026 birim fiyatları']">
        12 × 10 m oturumlu, 30° eğimli beşik bir çatının gerçek alanı yaklaşık 125 m²'dir; %5 fireyle 131,5 m² kaplama ve yaklaşık 12 m mahya siparişi gerekir. Bina ölçülerini, eğimi ve kaplama tipini girin; metrajı ve Çevre, Şehircilik ve İklim Değişikliği Bakanlığı birim fiyatlarıyla yaklaşık bütçeyi görün.
    </x-calc.hero>
</div>


<div class="layout">


<section class="card">

<h2>Çatı Geometrisi</h2>


<div class="field">

<label>Gerçek çatı alanı biliyorsanız (m²)</label>

<input
    id="directArea"
    type="number"
    min="0"
    step="0.01"
>

</div>


<div class="grid">

<div class="field">

<label>Bina uzunluğu (m)</label>

<input
    id="length"
    type="number"
    min="0"
    step="0.01"
>

</div>


<div class="field">

<label>Bina genişliği (m)</label>

<input
    id="width"
    type="number"
    min="0"
    step="0.01"
>

</div>


<div class="field">

<label>Çatı eğimi (%)</label>

<input
    id="slope"
    type="number"
    min="0"
    step="0.1"
    value="30"
>

</div>


<div class="field">

<label>Saçak payı (m)</label>

<input
    id="eave"
    type="number"
    min="0"
    step="0.01"
    value="0"
>

</div>

</div>


<div class="grid">

<div class="field">

<label>Çatı formu</label>

<select id="roofForm">

<option value="gable">
Basit Beşik Çatı
</option>

<option value="single">
Tek Eğimli Çatı
</option>

<option value="hip">
Kırma Çatı
</option>

<option value="other">
Özel / Karma Çatı
</option>

</select>

</div>


<div class="field">

<label>Toplam mahya uzunluğu (m)</label>

<input
    id="ridge"
    type="number"
    min="0"
    step="0.01"
>

</div>

</div>


<hr>


<div class="field">

<label>Çatı örtüsü</label>

<select id="covering">

@foreach($coverings as $key => $item)

<option value="{{ $key }}">
{{ $item['label'] }}
</option>

@endforeach

</select>

</div>


<div class="field">

<label>Malzeme sipariş / fire payı (%)</label>

<input
    id="waste"
    type="number"
    min="0"
    max="25"
    step="0.5"
    value="5"
>

</div>


<hr>


<div class="field">

<label>Alt kaplama</label>

<select id="underlay">

@foreach($underlays as $key => $item)

<option value="{{ $key }}">
{{ $item['label'] }}
</option>

@endforeach

</select>

</div>


<div class="field">

<label>Çatı altı su yalıtımı</label>

<select id="waterproofing">

@foreach($waterproofings as $key => $item)

<option value="{{ $key }}">
{{ $item['label'] }}
</option>

@endforeach

</select>

</div>


<div class="field">

<label>Mevcut çatı sökümü</label>

<select id="demolition">

<option value="no">Yok</option>
<option value="yes">Var</option>

</select>

</div>


<button
    class="calc"
    id="calculate"
    type="button"
>
Çatı Metraj ve Maliyetini Hesapla →
</button>

</section>


<section>

<div class="card result" id="result" data-wc-result>

<div class="totalbox">

<div>
YFK 2026-08 Referans Bütçesi
</div>

<div class="total" id="total" data-wc-price>
-
</div>

</div>


<div class="rows">

<div class="row">

<span>Yatay izdüşüm</span>

<strong id="projected">
-
</strong>

</div>


<div class="row">

<span>Eğim katsayısı</span>

<strong id="multiplier">
-
</strong>

</div>


<div class="row">

<span>Gerçek çatı alanı</span>

<strong id="area">
-
</strong>

</div>


<div class="row">

<span>Fire dahil sipariş alanı</span>

<strong id="orderArea">
-
</strong>

</div>


<div class="row">

<span>Mahya</span>

<strong id="ridgeResult">
-
</strong>

</div>

</div>


<div
    class="items"
    id="items"
></div>


<div
    class="warn"
    id="warning"
></div>

</div>


<div
    class="budget"
    id="budget"
>

<strong>
Bu bütçe projeniz için uygun mu?
</strong>

<div class="actions">

<button
    type="button"
    class="yes"
    id="yes"
>
Evet, net teklif istiyorum
</button>

<button
    type="button"
    id="no"
>
Sadece maliyete baktım
</button>

</div>


<div
    class="quote"
    id="quote"
>

<a
    id="quoteLink"
    href="/maliyet-hesapla?service=cati"
>
Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
</a>

</div>


<div
    class="no"
    id="noText"
>
Bu aşamada iletişim bilginizi almıyoruz.
</div>

</div>

</section>

</div>


<div class="sandvic">

<strong>Sandviç panel ayrı araçtır.</strong>

Fabrika ve depo tipi sandviç panel çatı/cephe hesabı için

<a href="/araclar/sandvic-panel-hesaplayici">
Sandviç Panel Hesaplayıcı →
</a>

</div>

</div>


<script>

const COVERINGS =
{!! $coveringsJson !!};

const UNDERLAYS =
{!! $underlaysJson !!};

const WATERPROOFINGS =
{!! $waterproofingsJson !!};


const nf =
new Intl.NumberFormat(
    'tr-TR',
    {
        maximumFractionDigits:2
    }
);

const mf =
new Intl.NumberFormat(
    'tr-TR',
    {
        minimumFractionDigits:2,
        maximumFractionDigits:2
    }
);


function num(id){

    const v =
    parseFloat(
        document.getElementById(id).value
    );

    return Number.isFinite(v)
        ? v
        : 0;
}


function refreshCovering(){

    const covering =
    COVERINGS[
        document.getElementById('covering').value
    ];

    const underlay =
    document.getElementById('underlay');

    if(
        covering &&
        covering.allow_underlay === false
    ){
        underlay.value = 'none';
        underlay.disabled = true;
    }
    else{
        underlay.disabled = false;
    }
}


function geometry(){

    const direct =
    num('directArea');

    if(direct > 0){

        return {
            area:direct,
            projected:null,
            multiplier:null,
            automatic:false,
            length:0,
            eave:0
        };
    }


    const l =
    num('length');

    const w =
    num('width');

    const slope =
    Math.max(
        0,
        num('slope')
    );

    const eave =
    Math.max(
        0,
        num('eave')
    );


    if(
        l <= 0 ||
        w <= 0
    ){
        return null;
    }


    const projected =
    (l + 2 * eave) *
    (w + 2 * eave);


    const multiplier =
    Math.sqrt(
        1 +
        Math.pow(
            slope / 100,
            2
        )
    );


    return {
        area:
            projected *
            multiplier,

        projected,
        multiplier,
        automatic:true,
        length:l,
        eave:eave
    };
}


function ridgeLength(g){

    const entered =
    num('ridge');

    if(entered > 0){

        return {
            length:entered,
            estimated:false
        };
    }


    const form =
    document.getElementById('roofForm').value;


    if(
        form === 'gable' &&
        g.automatic
    ){

        return {
            length:
                g.length +
                2 * g.eave,

            estimated:true
        };
    }


    return {
        length:0,
        estimated:false
    };
}


function addItem(
    list,
    label,
    poz,
    found,
    price,
    quantity,
    unit
){

    if(
        !poz ||
        quantity <= 0
    ){
        return;
    }


    if(
        !found ||
        price === null
    ){

        list.push({
            label,
            poz,
            found:false
        });

        return;
    }


    const p =
    parseFloat(price);


    list.push({
        label,
        poz,
        found:true,
        unitPrice:p,
        quantity,
        unit,
        total:
            p *
            quantity
    });
}


function calculate(){

    const g =
    geometry();


    if(
        !g ||
        g.area <= 0
    ){

        alert(
            'Gerçek çatı alanını veya bina ölçülerini girin.'
        );

        return;
    }


    const coveringKey =
    document.getElementById('covering').value;

    const covering =
    COVERINGS[coveringKey];


    const ridge =
    ridgeLength(g);


    const waste =
    Math.max(
        0,
        Math.min(
            25,
            num('waste')
        )
    );


    const orderArea =
    g.area *
    (
        1 +
        waste / 100
    );


    const lines = [];


    addItem(
        lines,
        covering.label,
        covering.poz,
        covering.found,
        covering.price,
        g.area,
        'm²'
    );


    const underlay =
    UNDERLAYS[
        document.getElementById('underlay').value
    ];


    if(
        underlay &&
        underlay.poz
    ){

        addItem(
            lines,
            underlay.label,
            underlay.poz,
            underlay.found,
            underlay.price,
            g.area,
            'm²'
        );
    }


    const waterproof =
    WATERPROOFINGS[
        document.getElementById('waterproofing').value
    ];


    if(
        waterproof &&
        waterproof.poz
    ){

        addItem(
            lines,
            waterproof.label,
            waterproof.poz,
            waterproof.found,
            waterproof.price,
            g.area,
            'm²'
        );
    }


    if(
        covering.ridge_poz &&
        ridge.length > 0
    ){

        addItem(
            lines,
            covering.ridge_label,
            covering.ridge_poz,
            covering.ridge_found,
            covering.ridge_price,
            ridge.length,
            'm'
        );
    }


    let total = 0;
    let complete = true;


    lines.forEach(
        line => {

            if(!line.found){
                complete = false;
                return;
            }

            total += line.total;
        }
    );


    const form =
    document.getElementById('roofForm').value;


    /*
     * Kiremit sisteminde mahya gerekli olabilecek
     * çatı formunda mahya bulunamıyorsa bütçeyi
     * tamamlanmış kabul etmiyoruz.
     */

    if(
        covering.ridge_poz &&
        (
            form === 'gable' ||
            form === 'hip' ||
            form === 'other'
        ) &&
        ridge.length <= 0
    ){
        complete = false;
    }


    document.getElementById('total')
        .innerText =
        mf.format(total) +
        ' TL' +
        (
            complete
            ? ''
            : ' + eksik kalem'
        );


    document.getElementById('projected')
        .innerText =
        g.projected !== null
        ? nf.format(g.projected) + ' m²'
        : 'Doğrudan alan';


    document.getElementById('multiplier')
        .innerText =
        g.multiplier !== null
        ? nf.format(g.multiplier)
        : '-';


    document.getElementById('area')
        .innerText =
        nf.format(g.area) +
        ' m²';


    document.getElementById('orderArea')
        .innerText =
        nf.format(orderArea) +
        ' m²';


    document.getElementById('ridgeResult')
        .innerText =
        ridge.length > 0
        ? nf.format(ridge.length) +
          ' m' +
          (
            ridge.estimated
            ? ' · yaklaşık'
            : ''
          )
        : 'Yok / girilmedi';


    const holder =
    document.getElementById('items');


    holder.innerHTML = '';


    lines.forEach(
        line => {

            const div =
            document.createElement('div');

            div.className =
            'item';


            if(line.found){

                div.innerHTML =
                '<div class="itemtop">' +
                    '<span>' +
                        line.label +
                    '</span>' +
                    '<span>' +
                        mf.format(line.total) +
                        ' TL' +
                    '</span>' +
                '</div>' +

                '<div class="meta">' +
                    'Poz ' +
                    line.poz +
                    ' · ' +
                    mf.format(line.unitPrice) +
                    ' TL/' +
                    line.unit +
                    ' × ' +
                    nf.format(line.quantity) +
                    ' ' +
                    line.unit +
                    ' · YFK 2026-08' +
                '</div>';
            }
            else{

                div.innerHTML =
                '<strong>' +
                    line.label +
                '</strong>' +

                '<div class="meta">' +
                    'Doğrulanmış fiyat bulunamadı. Toplama eklenmedi.' +
                '</div>';
            }


            holder.appendChild(div);
        }
    );


    const warnings = [];


    if(g.automatic){

        warnings.push(
            'Alan basit homojen eğim geometrisiyle hesaplandı.'
        );
    }


    if(ridge.estimated){

        warnings.push(
            'Mahya basit beşik çatı varsayımıyla yaklaşık hesaplandı.'
        );
    }


    if(
        covering.ridge_poz &&
        (
            form === 'hip' ||
            form === 'other'
        ) &&
        ridge.length <= 0
    ){

        warnings.push(
            'Kırma veya özel çatıda toplam mahya/kırma hattı uzunluğunu girin.'
        );
    }


    if(
        document.getElementById('demolition').value
        === 'yes'
    ){

        warnings.push(
            'Söküm seçildi ancak doğrulanmış söküm pozu olmadığı için bedele eklenmedi.'
        );
    }


    warnings.push(
        'YFK tutarı referans bütçedir; saha şartları ve proje detayları net teklifte ayrıca değerlendirilir.'
    );


    document.getElementById('warning')
        .innerText =
        warnings.join(' ');


    const quoteUrl =
    '/maliyet-hesapla' +
    '?service=cati' +
    '&area=' +
    encodeURIComponent(
        g.area.toFixed(2)
    ) +
    '&covering=' +
    encodeURIComponent(
        coveringKey
    );


    document.getElementById('quoteLink')
        .href =
        quoteUrl;


    document.getElementById('budget')
        .style.display =
        complete
        ? 'block'
        : 'none';


    document.getElementById('quote')
        .style.display =
        'none';


    document.getElementById('noText')
        .style.display =
        'none';


    document.getElementById('result')
        .classList.add('show');
}


document.getElementById('covering')
.addEventListener(
    'change',
    refreshCovering
);


document.getElementById('calculate')
.addEventListener(
    'click',
    calculate
);


document.getElementById('yes')
.addEventListener(
    'click',
    () => {

        document.getElementById('quote')
            .style.display =
            'block';

        document.getElementById('noText')
            .style.display =
            'none';
    }
);


document.getElementById('no')
.addEventListener(
    'click',
    () => {

        document.getElementById('quote')
            .style.display =
            'none';

        document.getElementById('noText')
            .style.display =
            'block';
    }
);


refreshCovering();

</script>

    <x-calc.kit />
    <x-tools.tool-guide slug="cati-maliyet-hesaplayici" />

<x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
