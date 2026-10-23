<?php
/*
|--------------------------------------------------------------------------
| WORKSITEM CONTECH
| 30-60 SECOND QUALIFIED COST ESTIMATOR + AI RENDER
|--------------------------------------------------------------------------
| Amaç:
| 1. Kullanıcıya hızlı ve gerçekçi ön bütçe vermek
| 2. AI Render ile duygusal satın alma kancasını atmak
| 3. Bütçesi ve zamanı uygun müşteriyi satışa taşımak
|--------------------------------------------------------------------------
*/

$unitPrices = [];

try {
    $unitPrices = \Illuminate\Support\Facades\DB::table('unit_prices')
        ->where('price_pool', 'market')
        ->where('is_active', true)
        ->pluck('price', 'key')
        ->map(function ($value) {
            return (float) $value;
        })
        ->toArray();
} catch (\Throwable $e) {
    $unitPrices = [];
}

try {
    $officialBuildingPrices = \Illuminate\Support\Facades\DB::table('building_classes as c')
        ->join('building_class_costs as p', 'p.building_class_id', '=', 'c.id')
        ->whereIn('c.code', ['3B', '4A'])
        ->where('c.is_active', true)
        ->where('p.is_active', true)
        ->orderByDesc('p.period')
        ->select('c.code', 'p.unit_price')
        ->get()
        ->unique('code');

    foreach ($officialBuildingPrices as $officialPrice) {
        $unitPrices[
            $officialPrice->code === '3B'
                ? 'bina_sinif_3b_m2'
                : 'bina_sinif_4a_m2'
        ] = (float) $officialPrice->unit_price;
    }
} catch (\Throwable $e) {
    // Resmî yapı fiyatı yoksa JavaScript güvenli biçimde hesaplamayı durdurur.
}
$priceJson = json_encode(
    $unitPrices,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

if (!$priceJson) {
    $priceJson = '{}';
}

$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tadilat Maliyeti Hesapla 2026: m² ve Kaliteye Göre | Worksitem</title>
<meta name="description" content="İnşaat veya tadilat projenizin yaklaşık bütçesini proje tipi, konum, alan ve kalite seviyesine göre hesaplayın. Yaklaşık 1 dakikada ön bütçenizi görün.">
<meta name="robots" content="index,follow,max-image-preview:large">
<link rel="canonical" href="https://worksitem.com/maliyet-hesapla">
<meta property="og:type" content="website">
<meta property="og:title" content="Tadilat Maliyeti Hesapla 2026: m², Şehir ve Kaliteye Göre | WORKSİTEM">
<meta property="og:description" content="İnşaat veya tadilat projenizin yaklaşık bütçesini yaklaşık 1 dakikada öğrenin.">
<meta property="og:url" content="https://worksitem.com/maliyet-hesapla">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
:root{
    --bg:#f5f6f8;
    --surface:#fff;
    --surface2:#f8f9fb;
    --text:#101318;
    --muted:#6b7280;
    --line:#e4e7eb;
    --accent:#ffb800;
    --accent-dark:#e5a500;
    --green:#16a34a;
    --red:#dc2626;
    --shadow:0 20px 60px rgba(15,23,42,.09);
    --radius:22px;
}

*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
    font-family:Inter,Arial,sans-serif;
    background:radial-gradient(circle at 50% -10%,rgba(255,184,0,.10),transparent 35%),var(--bg);
    color:var(--text);
    min-height:100vh;
    -webkit-font-smoothing:antialiased;
}
button,input,select,textarea{font:inherit;}
button{cursor:pointer;}
a{color:inherit;text-decoration:none;}

/* HEADER */
.header{height:68px;border-bottom:1px solid var(--line);background:rgba(255,255,255,.92);backdrop-filter:blur(18px);position:sticky;top:0;z-index:50;}
.header-inner{width:min(1080px,calc(100% - 32px));height:100%;margin:auto;display:flex;align-items:center;justify-content:space-between;}
.brand{display:flex;align-items:center;}
.brand img{height:38px;width:auto;object-fit:contain;}
.header-note{color:var(--muted);font-size:12px;font-weight:700;}

/* MAIN */
.main{width:min(820px,calc(100% - 28px));margin:0 auto;padding:34px 0 70px;}

/* HERO */
.hero{text-align:center;margin-bottom:24px;}
.badge{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:8px 13px;font-size:11px;font-weight:800;color:#555;margin-bottom:14px;box-shadow:0 5px 20px rgba(0,0,0,.04);}
.badge-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.12);}
.hero h1{font-size:clamp(28px,5vw,48px);line-height:1.03;letter-spacing:-1.8px;font-weight:900;margin-bottom:12px;}
.hero h1 strong{color:var(--ws-highlight,#9A6E00);}
.hero p{color:var(--muted);font-size:15px;line-height:1.65;max-width:620px;margin:auto;}

/* CALCULATOR */
.calculator{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.progress-wrap{height:5px;background:#eceef1;}
.progress{height:100%;width:14.28%;background:linear-gradient(90deg,#ffb800,#ffd66b);transition:width .35s ease;}
.calc-top{padding:19px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px;}
.step-text{font-size:12px;color:var(--muted);font-weight:700;}
.time-text{font-size:12px;color:#16a34a;font-weight:800;}
.step{padding:28px 25px 30px;display:none;}
.step.active{display:block;animation:fadeIn .25s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}

.question{font-size:25px;line-height:1.15;letter-spacing:-.8px;font-weight:900;margin-bottom:8px;}
.question-sub{font-size:13px;color:var(--muted);margin-bottom:22px;line-height:1.5;}
.options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px;}
.option{position:relative;min-height:76px;border:1.5px solid var(--line);border-radius:16px;background:#fff;padding:15px;text-align:left;transition:.18s ease;}
.option:hover{border-color:#c9cdd3;transform:translateY(-1px);}
.option.selected{border-color:var(--accent);background:#fffaf0;box-shadow:0 0 0 3px rgba(255,184,0,.12);}
.option-icon{font-size:21px;margin-bottom:7px;}
.option-title{font-size:14px;font-weight:800;line-height:1.25;}
.option-desc{font-size:11px;color:var(--muted);margin-top:4px;line-height:1.35;}

/* INPUT */
.field{margin-bottom:16px;}
.field label{display:block;font-size:12px;font-weight:800;margin-bottom:7px;}
.input{width:100%;height:52px;border:1.5px solid var(--line);border-radius:13px;padding:0 15px;outline:none;background:#fff;color:var(--text);font-size:16px;transition:.18s ease;}
.input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,184,0,.12);}
.input.error{border-color:#ef4444;}
.range-row{display:grid;grid-template-columns:1fr 110px;gap:12px;align-items:center;}
.range{width:100%;accent-color:#ffb800;}
.range-value{height:48px;border-radius:12px;background:var(--surface2);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:16px;}

/* BUTTONS */
.buttons{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:24px;}
.btn{min-height:52px;border-radius:14px;padding:0 19px;font-size:14px;font-weight:900;border:1px solid var(--line);background:#fff;color:#222;}
.btn:hover{transform:translateY(-1px);}
.btn-primary{background:#111;color:#fff;border-color:#111;flex:1;}
.btn-primary:hover{background:#222;}
.btn-primary.yellow{background:var(--accent);color:#111;border-color:var(--accent);}
.btn-back{min-width:100px;}

/* RESULT */
.result{display:none;}
.result.active{display:block;animation:fadeIn .3s ease;}
.result-head{text-align:center;padding:34px 24px 23px;}
.result-head .small{font-size:12px;color:var(--muted);font-weight:800;margin-bottom:7px;}
.result-head h2{font-size:30px;letter-spacing:-1px;font-weight:900;margin-bottom:7px;}
.result-head p{color:var(--muted);font-size:13px;line-height:1.55;}

/* PRICE */
.price-card{margin:0 22px;border-radius:20px;background:#111;color:#fff;padding:25px;text-align:center;position:relative;overflow:hidden;}
.price-card:before{content:"";position:absolute;width:240px;height:240px;border-radius:50%;background:rgba(255,184,0,.12);right:-90px;top:-110px;}
.price-label{position:relative;font-size:11px;color:#b5b8be;font-weight:700;text-transform:uppercase;letter-spacing:1px;}
.price{position:relative;font-size:39px;line-height:1.1;font-weight:900;color:var(--accent);margin:8px 0;letter-spacing:-1.5px;}
.price-note{position:relative;font-size:11px;color:#a9adb5;}

/* BREAKDOWN */
.breakdown{padding:22px;}
.breakdown-title{font-size:13px;font-weight:900;margin-bottom:11px;}
.breakdown-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:9px;}
.break-item{border:1px solid var(--line);border-radius:13px;padding:13px;background:#fff;}
.break-item span{display:block;font-size:10px;color:var(--muted);margin-bottom:4px;}
.break-item strong{display:block;font-size:14px;}
.result-info{margin:0 22px 18px;background:#fffaf0;border:1px solid #f6d878;border-radius:15px;padding:14px;font-size:12px;line-height:1.55;color:#63531d;}


.work-related{
    margin-top:16px;
    padding-top:15px;
    border-top:1px solid var(--line);
}
.work-related>strong{
    display:block;
    font-size:12px;
    margin-bottom:9px;
}
.work-related-links{
    display:flex;
    flex-wrap:wrap;
    gap:7px;
}
.work-related-links a{
    display:inline-flex;
    align-items:center;
    min-height:34px;
    padding:0 10px;
    border:1px solid var(--line);
    border-radius:9px;
    background:#fff;
    color:#111;
    text-decoration:none;
    font-size:10px;
    font-weight:800;
}
.work-related-links a:hover{
    border-color:var(--accent);
    background:#fffaf0;
}
/* AI RENDER BOX */
.ai-render-box {
    margin: 0 22px 25px;
    background: linear-gradient(145deg, #050505, #151515);
    border-radius: 18px;
    padding: 24px;
    color: #fff;
    text-align: left;
    border: 1px solid rgba(255,184,0,0.2);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}
.ai-badge {
    display: inline-block;
    background: rgba(255,184,0,0.15);
    color: var(--accent);
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 900;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.ai-render-box h3 { font-size: 18px; font-weight: 900; margin-bottom: 6px; letter-spacing:-0.5px;}
.ai-render-box p { color: #a1a1aa; font-size: 13px; line-height: 1.5; margin-bottom: 18px; }
.ai-submit-btn {
    width: 100%;
    background: var(--accent);
    color: #000;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-weight: 900;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}
.ai-submit-btn:hover { background: #fff; transform: translateY(-2px); }

/* QUALIFICATION */
.qualification{margin:0 22px 18px;padding:20px;border:1px solid var(--line);border-radius:18px;background:#fff;}
.qualification h3{font-size:17px;font-weight:900;margin-bottom:6px;}
.qualification p{color:var(--muted);font-size:12px;line-height:1.5;margin-bottom:14px;}
.qual-options{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.qual-option{min-height:58px;padding:12px;text-align:left;border-radius:13px;border:1.5px solid var(--line);background:#fff;font-size:12px;font-weight:800;}
.qual-option.selected{border-color:var(--accent);background:#fffaf0;box-shadow:0 0 0 3px rgba(255,184,0,.10);}

/* LEAD */
.lead-box{margin:0 22px 25px;border-radius:18px;background:#f7f7f8;border:1px solid var(--line);padding:22px;}
.lead-box h3{font-size:18px;font-weight:900;letter-spacing:-.4px;margin-bottom:6px;}
.lead-box p{color:var(--muted);font-size:12px;line-height:1.5;margin-bottom:16px;}
.lead-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.lead-submit{width:100%;min-height:52px;margin-top:10px;border-radius:13px;border:0;background:#111;color:#fff;font-weight:900;}
.lead-submit:hover{background:#222;}
.lead-note{margin-top:9px;color:#777;font-size:10px;line-height:1.45;text-align:center;}
.whatsapp{display:flex;align-items:center;justify-content:center;min-height:50px;border-radius:13px;background:#16a34a;color:#fff;font-size:13px;font-weight:900;margin-top:9px;}
.qualified-message{display:none;margin:0 22px 22px;padding:18px;border-radius:16px;background:#fff8e1;border:1px solid #fde68a;}
.qualified-message.active{display:block;}
.qualified-message strong{display:block;font-size:14px;margin-bottom:5px;}
.qualified-message span{display:block;color:#101318;font-size:11px;line-height:1.5;}

.result-actions{padding:0 22px 28px;display:flex;gap:9px;}
.secondary{flex:1;min-height:48px;border-radius:12px;border:1px solid var(--line);background:#fff;font-weight:800;font-size:12px;}

/* TRUST */
.trust{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:15px;}
.trust-item{background:#fff;border:1px solid var(--line);border-radius:14px;padding:13px;text-align:center;}
.trust-item strong{display:block;font-size:12px;margin-bottom:4px;}
.trust-item span{color:var(--muted);font-size:10px;}

@media(max-width:620px){
    .header-note{display:none;}
    .main{width:min(100% - 18px,820px);padding-top:23px;}
    .hero{margin-bottom:17px;}
    .hero h1{font-size:30px;}
    .step{padding:23px 16px 24px;}
    .question{font-size:22px;}
    .options{grid-template-columns:1fr;}
    .option{min-height:68px;}
    .calc-top{padding:16px;}
    .breakdown-grid{grid-template-columns:1fr 1fr;}
    .lead-grid{grid-template-columns:1fr;}
    .qual-options{grid-template-columns:1fr;}
    .trust{grid-template-columns:1fr;}
    .price{font-size:33px;}
    .price-card,.result-info,.lead-box,.qualification,.qualified-message,.ai-render-box{margin-left:14px;margin-right:14px;}
    .breakdown,.result-actions{padding-left:14px;padding-right:14px;}
}

/* WORK GROUPS V1 */
.work-groups{
    margin:18px 22px 24px;
    padding:20px;
    border:1px solid var(--line);
    border-radius:18px;
    background:var(--surface);
}
.work-head{
    margin-bottom:16px;
}
.work-head span{
    display:block;
    margin-bottom:5px;
    font-size:10px;
    font-weight:900;
    letter-spacing:.9px;
    color:var(--accent);
}
.work-head h3{
    margin:0 0 6px;
    font-size:19px;
    line-height:1.25;
    font-weight:900;
}
.work-head p{
    margin:0;
    color:var(--muted);
    font-size:12px;
    line-height:1.55;
}
.work-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
}
.work-card{
    padding:14px;
    border:1px solid var(--line);
    border-radius:14px;
    background:var(--bg);
}
.work-card h4{
    margin:0 0 9px;
    font-size:13px;
    font-weight:900;
}
.work-card ul{
    margin:0;
    padding:0;
    list-style:none;
}
.work-card li{
    position:relative;
    margin:6px 0;
    padding-left:14px;
    color:var(--muted);
    font-size:11px;
    line-height:1.45;
}
.work-card li::before{
    content:"";
    position:absolute;
    left:0;
    top:.55em;
    width:5px;
    height:5px;
    border-radius:50%;
    background:var(--accent);
}
.work-note{
    margin-top:13px;
    padding-top:12px;
    border-top:1px solid var(--line);
    color:var(--muted);
    font-size:10px;
    line-height:1.5;
}
@media (max-width:640px){
    .work-groups{
        margin-left:14px;
        margin-right:14px;
        padding:16px;
    }
    .work-grid{
        grid-template-columns:1fr;
    }
}
</style>
</head>

<body data-tool-slug="maliyet-hesapla">

<x-site.contech-header />

<main class="main">

<section class="hero">
    <div class="badge"><span class="badge-dot"></span>Yaklaşık 1 dakikada ön bütçe</div>
    <h1>İnşaat veya tadilat projenizin<br><strong>yaklaşık bütçesini hesaplayın.</strong></h1>
    <p>Proje tipi, konum, alan ve kalite seviyesine göre yaklaşık bütçenizi hesaplayın.</p>
</section>

<section class="calculator" id="calculator">
    <div class="progress-wrap"><div class="progress" id="progress"></div></div>
    <div class="calc-top">
        <span class="step-text" id="stepText">1 / 7</span>

    </div>

    {{-- STEPS 1-7 (Same HTML as previously established) --}}

    <div class="step active" data-step="1">
        <div class="question">Ne yapmak istiyorsunuz?</div>
        <div class="question-sub">Projenizin ana tipini seçin.</div>
        <div class="options">
            <button type="button" class="option" data-key="project_type" data-value="new_build"><div class="option-icon">🏗️</div><div class="option-title">Sıfırdan İnşaat</div><div class="option-desc">Arsa üzerinde yeni yapı</div></button>
            <button type="button" class="option" data-key="project_type" data-value="renovation"><div class="option-icon">🔨</div><div class="option-title">Komple Tadilat</div><div class="option-desc">Mevcut yapının yenilenmesi</div></button>
            <button type="button" class="option" data-key="project_type" data-value="partial_renovation"><div class="option-icon">🛠️</div><div class="option-title">Kısmi Tadilat</div><div class="option-desc">Belirli alanların yenilenmesi</div></button>
            <button type="button" class="option" data-key="project_type" data-value="commercial"><div class="option-icon">🏢</div><div class="option-title">Ticari Proje</div><div class="option-desc">Ofis, mağaza, restoran vb.</div></button>
        </div>
    </div>

    <div class="step" data-step="2">
        <div class="question">Projeniz nerede?</div>
        <div class="question-sub">Konum, işçilik ve lojistik maliyetlerini etkiler.</div>
        <div class="field">
            <label>İl</label>
            <select class="input" id="city">
                <option value="">İl seçin</option>
                <option value="istanbul">İstanbul</option>
                <option value="ankara">Ankara</option>
                <option value="izmir">İzmir</option>
                <option value="antalya">Antalya</option>
                <option value="mugla">Muğla</option>
                <option value="bursa">Bursa</option>
                <option value="kocaeli">Kocaeli</option>
                <option value="adana">Adana</option>
                <option value="mersin">Mersin</option>
                <option value="konya">Konya</option>
                <option value="gaziantep">Gaziantep</option>
                <option value="other">Diğer</option>
            </select>
        </div>
        <div class="field"><label>İlçe <span style="font-weight:500;color:#999">(opsiyonel)</span></label><input class="input" id="district" type="text" placeholder="Örn. Ümraniye"></div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary" data-next>Devam →</button></div>
    </div>

    <div class="step" data-step="3">
        <div class="question">Yaklaşık kaç m²?</div>
        <div class="question-sub">Tam ölçüyü bilmiyorsanız yaklaşık değer yeterli.</div>
        <div class="range-row">
            <input type="range" class="range" id="areaRange" min="20" max="5000" step="5" value="150">
            <div class="range-value"><span id="areaValue">150</span>&nbsp;m²</div>
        </div>
        <div style="height:14px"></div>
        <div class="field"><label>Doğrudan m² yazabilirsiniz</label><input class="input" id="areaInput" type="number" min="20" max="50000" value="150" placeholder="Örn. 180"></div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary" data-next>Devam →</button></div>
    </div>

    <div class="step" data-step="4">
        <div class="question">Tadilatın kapsamı ne kadar?</div>
        <div class="question-sub">Sadece gerçekten maliyeti değiştiren kapsamı seçin.</div>
        <div class="options">
            <button type="button" class="option" data-key="scope" data-value="light"><div class="option-icon">🎨</div><div class="option-title">Hafif Yenileme</div><div class="option-desc">Boya, zemin ve küçük yenilemeler</div></button>
            <button type="button" class="option" data-key="scope" data-value="medium"><div class="option-icon">🔧</div><div class="option-title">Orta Kapsam</div><div class="option-desc">İnce işler ve bazı tesisatlar</div></button>
            <button type="button" class="option" data-key="scope" data-value="complete"><div class="option-icon">🏠</div><div class="option-title">Komple Yenileme</div><div class="option-desc">Kırım, tesisat ve tüm ince işler</div></button>
            <button type="button" class="option" data-key="scope" data-value="turnkey"><div class="option-icon">🔑</div><div class="option-title">Anahtar Teslim</div><div class="option-desc">Baştan sona uygulama</div></button>
        </div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary" data-next>Devam →</button></div>
    </div>

    <div class="step" data-step="5">
        <div class="question">Nasıl bir kalite hedefliyorsunuz?</div>
        <div class="question-sub">Marka seçmenize gerek yok. Genel kalite seviyesini seçin.</div>
        <div class="options">
            <button type="button" class="option" data-key="quality" data-value="economic"><div class="option-icon">💰</div><div class="option-title">Ekonomik</div><div class="option-desc">Fonksiyonel ve bütçe odaklı</div></button>
            <button type="button" class="option" data-key="quality" data-value="standard"><div class="option-icon">⭐</div><div class="option-title">Standart</div><div class="option-desc">Piyasada en çok tercih edilen</div></button>
            <button type="button" class="option" data-key="quality" data-value="premium"><div class="option-icon">✨</div><div class="option-title">Premium</div><div class="option-desc">Üst kalite malzeme ve işçilik</div></button>
            <button type="button" class="option" data-key="quality" data-value="luxury"><div class="option-icon">💎</div><div class="option-title">Lüks</div><div class="option-desc">Özel tasarım ve üst segment</div></button>
        </div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary" data-next>Devam →</button></div>
    </div>

    <div class="step" data-step="6">
        <div class="question">Mevcut yapı ne durumda?</div>
        <div class="question-sub">Tadilatta kırım ve mevcut tesisat durumu önemlidir.</div>
        <div class="options">
            <button type="button" class="option" data-key="condition" data-value="good"><div class="option-icon">👍</div><div class="option-title">İyi Durumda</div><div class="option-desc">Büyük kırım gerekmiyor</div></button>
            <button type="button" class="option" data-key="condition" data-value="average"><div class="option-icon">🔨</div><div class="option-title">Orta Durumda</div><div class="option-desc">Bazı kırım ve yenilemeler var</div></button>
            <button type="button" class="option" data-key="condition" data-value="bad"><div class="option-icon">🚧</div><div class="option-title">Eski / Yıpranmış</div><div class="option-desc">Tesisat ve yüzeyler yenilenecek</div></button>
            <button type="button" class="option" data-key="condition" data-value="unknown"><div class="option-icon">❓</div><div class="option-title">Bilmiyorum</div><div class="option-desc">Keşifte netleşmesini istiyorum</div></button>
        </div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary" data-next>Devam →</button></div>
    </div>

    <div class="step" data-step="7">
        <div class="question">Ne zaman başlamayı düşünüyorsunuz?</div>
        <div class="question-sub">Bu bilgi Kainta İnşaat'ın doğru projeleri önceliklendirmesi için kullanılır.</div>
        <div class="options">
            <button type="button" class="option" data-key="timeline" data-value="now"><div class="option-icon">⚡</div><div class="option-title">Hemen</div><div class="option-desc">0–1 ay içinde</div></button>
            <button type="button" class="option" data-key="timeline" data-value="soon"><div class="option-icon">📅</div><div class="option-title">Yakında</div><div class="option-desc">1–3 ay içinde</div></button>
            <button type="button" class="option" data-key="timeline" data-value="later"><div class="option-icon">🗓️</div><div class="option-title">3–6 ay</div><div class="option-desc">Planlama aşamasındayım</div></button>
            <button type="button" class="option" data-key="timeline" data-value="research"><div class="option-icon">🔎</div><div class="option-title">Sadece araştırıyorum</div><div class="option-desc">Henüz karar vermedim</div></button>
        </div>
        <div class="buttons"><button type="button" class="btn btn-back" data-back>← Geri</button><button type="button" class="btn btn-primary yellow" id="calculateButton">Maliyeti Hesapla →</button></div>
    </div>


    {{-- ========================================================= --}}
    {{-- RESULT --}}
    {{-- ========================================================= --}}
    <div class="result" id="result">

        <div class="result-head">
            <div class="small">PROJENİZ İÇİN YAKLAŞIK BÜTÇE</div>
            <h2>İlk bütçe çerçeveniz hazır.</h2>
            <p>Bu sonuç teklif değildir. Keşif öncesi bütçe planlaması için seçtiğiniz bilgiler ve piyasa verileriyle oluşturulmuştur.</p>
        </div>

        {{-- PRICE --}}
        <div class="price-card">
            <div class="price-label">Tahmini toplam proje bütçesi</div>
            <div class="price" id="resultPrice">0 TL</div>
            <div class="price-note" id="resultRange">Yaklaşık fiyat aralığı</div>
        </div>

        {{-- BREAKDOWN --}}
        <div class="breakdown">
            <div class="breakdown-title">Bütçenin ana dağılımı</div>
            <div class="breakdown-grid">
                <div class="break-item"><span>Kaba / hazırlık</span><strong id="break1">0 TL</strong></div>
                <div class="break-item"><span>İnce işler</span><strong id="break2">0 TL</strong></div>
                <div class="break-item"><span>Mekanik</span><strong id="break3">0 TL</strong></div>
                <div class="break-item"><span>Elektrik</span><strong id="break4">0 TL</strong></div>
            </div>
        </div>

        <div class="result-info" id="resultInfo"></div>
<div class="work-groups" id="projectWorkGroups">
    <div class="work-head">
        <span>PROJE DETAYI</span>
        <h3>Projeniz için iş ve malzeme grupları</h3>
        <p id="workGroupsIntro"></p>
    </div>

    <div class="work-grid" id="workGroupsGrid"></div>
<div class="work-related">
    <strong>Bu proje için ilgili hesaplamalar</strong>
    <div class="work-related-links" id="workRelatedLinks"></div>
</div>

    <div class="work-note">
        Bu liste ön planlama içindir. Kesin miktarlar proje ve uygulama detaylarına göre değişir.
    </div>
</div>

{{-- WORKSITEM AI RENDER MODÜLÜ --}}
        <div class="ai-render-box" id="aiRenderBox">
            <div class="ai-badge">✨ Worksitem AI Vision</div>
            <h3>Projenizi görselleştirin</h3>
            <p>
                Seçtiğiniz <strong id="renderParamText">Kalite</strong> standartlarına uygun
                konsept bir 3D render oluşturmak için yapay zekâyı çalıştırın.
            </p>

            @if(config('services.ai_spend_enabled'))
            <button type="button" class="ai-submit-btn" id="btnGenerateRender">
                Konsept Render Üret →
            </button>
            @else
            <button type="button" class="ai-submit-btn" id="btnGenerateRender" disabled
                    style="opacity:.55;cursor:not-allowed;">
                AI Görselleştirme Geçici Olarak Kapalı
            </button>
            <div style="margin-top:10px;font-size:13px;color:#a1a1aa;">
                Maliyet hesaplama ve teklif talebi normal şekilde kullanılabilir.
            </div>
            @endif

            <div id="aiRenderContainer" style="display:none; margin-top: 20px;">
                <div id="aiLoader" style="color: #a1a1aa; font-size: 13px; font-weight: 600; text-align: center; margin-bottom: 10px;">
                    ⏳ Yapay zekâ tasarımınızı hazırlıyor... Lütfen bekleyin.
                </div>
                <img id="aiResultImage" src="" alt="Proje Render" style="display:none; width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            </div>
        </div>

        {{-- QUALIFICATION GATE --}}
        <div class="qualification" id="qualificationBox">
            <h3>Bu bütçe aralığı projeniz için gerçekçi mi?</h3>
            <p>Kainta İnşaat'a başvurmadan önce bütçenizin bu seviyede olduğunu onaylayın. Böylece sadece gerçekten uygulanabilir projeleri değerlendirmeye alabiliriz.</p>
            <div class="qual-options">
                <button type="button" class="qual-option" data-budget="yes">✓ Evet, bu bütçeyi karşılayabilirim.</button>
                <button type="button" class="qual-option" data-budget="no">Henüz bütçemi netleştirmedim.</button>
            </div>
        </div>

        <div class="qualified-message" id="qualifiedMessage">
            <strong>Projeniz Kainta İnşaat değerlendirmesine uygun görünüyor.</strong>
            <span>Bilgilerinizi bıraktığınızda projenizin kapsamı ve tahmini bütçesi Kainta İnşaat ekibine iletilir. Uygun projelerde sonraki adım için iletişime geçilir.</span>
        </div>

        {{-- LEAD --}}
        <div class="lead-box" id="leadBox" style="display:none;">
            <h3>Projenizi Kainta İnşaat'a değerlendirmeye gönderin.</h3>
            <p>Telefonunuzu bırakın. Projenizin bilgileri, yaklaşık bütçesi ve başlangıç zamanı Kainta İnşaat ekibine iletilsin. <strong>Uygun projelerde detaylı teklif süreci başlatılır.</strong></p>

            <form action="/maliyet-hesapla/lead" method="POST" id="leadForm">
                        @include('components.lead-tracking-fields')
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="source" value="worksitem_contech_60s">
                <input type="hidden" name="project_data" id="projectData">
                <input type="hidden" name="estimated_cost" id="estimatedCost">
                <input type="hidden" name="estimated_min" id="estimatedMin">
                <input type="hidden" name="estimated_max" id="estimatedMax">
                <input type="hidden" name="budget_confirmed" id="budgetConfirmed" value="0">
                <input type="hidden" name="lead_quality" id="leadQuality" value="unqualified">

                <div class="lead-grid">
                    <div><input class="input" name="name" type="text" placeholder="Ad Soyad" autocomplete="name" required></div>
                    <div><input class="input" name="phone" type="tel" placeholder="Telefon" autocomplete="tel" required></div>
                </div>
                <input class="input" name="email" type="email" placeholder="E-posta (opsiyonel)" autocomplete="email" style="margin-top:10px">

                                @include('components.lead-consent-fields')

<button type="submit" class="lead-submit">Kainta İnşaat Proje Değerlendirmesi İçin Başvur →</button>
                <div class="lead-note">Bilgileriniz yalnızca projenizle ilgili iletişim ve değerlendirme amacıyla kullanılır.</div>
            </form>

            <a href="#" id="whatsappLink" class="whatsapp" target="_blank" rel="noopener">WhatsApp'tan Projeyi Kainta İnşaat'a Gönder</a>
        </div>

        <div class="result-actions">
            <button type="button" class="secondary" id="restartButton">↻ Yeniden Hesapla</button>
            <button type="button" class="secondary" id="shareButton">↗ Sonucu Paylaş</button>
        </div>

    </div>
</section>

<div class="trust">
    <div class="trust-item"><strong>⚡ Yaklaşık 1 dakika</strong><span>Hızlı ön bütçe</span></div>
    <div class="trust-item"><strong>📐 Piyasa verisi</strong><span>Birim maliyet yaklaşımı</span></div>
    <div class="trust-item"><strong>🏗️ Kainta İnşaat</strong><span>Uygun projelere odaklanır</span></div>
</div>
</main>

<script>
(function(){
"use strict";

const DB_PRICES = <?php echo $priceJson; ?>;
const STEALTH_MULTIPLIER = 1.40;

const state = { project_type:"", city:"", district:"", area:150, scope:"", quality:"", condition:"", timeline:"", budget_confirmed:false, lead_quality:"unqualified", result:null };

const qs = new URLSearchParams(window.location.search);
const quickMode = qs.get("quick") === "1";
const presetTip = qs.get("tip");
if(presetTip === "insaat") state.project_type = "new_build";
if(presetTip === "tadilat") state.project_type = "renovation";
let currentStep = state.project_type ? 2 : 1;
const totalSteps = 7;
const money = new Intl.NumberFormat("tr-TR");

if(quickMode){
    setTimeout(() => {
        const btn = document.getElementById("calculateButton");
        if(btn) btn.click();
    }, 0);
}

const presetCity=qs.get("city");
const presetDistrict=qs.get("district");
const presetArea=parseFloat(qs.get("area")||0);
const presetQuality=qs.get("quality");


if(quickMode){
    state.scope = "complete";
    state.condition = "average";
    state.timeline = "research";
}


if(presetCity){
 state.city=cityKey(presetCity);
 const e=document.getElementById("city"); if(e) e.value=state.city;
}
if(presetDistrict){
 state.district=presetDistrict;
 const e=document.getElementById("district"); if(e) e.value=presetDistrict;
}
if(presetArea>=20){
 state.area=presetArea;
 ["areaRange","areaInput"].forEach(id=>{const e=document.getElementById(id);if(e)e.value=presetArea;});
 const e=document.getElementById("areaValue");if(e)e.textContent=presetArea;
}
if(["economic","standard","premium","luxury"].includes(presetQuality)){
 state.quality=presetQuality;
 document.querySelectorAll("[data-key=quality]").forEach(b=>b.classList.toggle("selected",b.dataset.value===presetQuality));
}

function tl(value){ return money.format(Math.round(value)) + " TL"; }
function clamp(value,min,max){ return Math.max(min,Math.min(max,value)); }


function cityKey(v){
 v=(v||"").toLocaleLowerCase("tr-TR");
 const m={"istanbul":"istanbul","ankara":"ankara","izmir":"izmir","antalya":"antalya","muğla":"mugla","mugla":"mugla","bursa":"bursa","kocaeli":"kocaeli","adana":"adana","mersin":"mersin","konya":"konya","gaziantep":"gaziantep"};
 return m[v]||"other";
}

const CITY_MULTIPLIERS = { istanbul:1.15, ankara:1.00, izmir:1.08, antalya:1.08, mugla:1.12, bursa:1.02, kocaeli:1.08, adana:.94, mersin:.96, konya:.92, gaziantep:.96, other:.95 };
const QUALITY_MULTIPLIERS = { economic:.85, standard:1.00, premium:1.28, luxury:1.60 };
const SCOPE_MULTIPLIERS = { light:.55, medium:.82, complete:1.00, turnkey:1.15 };
const CONDITION_MULTIPLIERS = { good:.92, average:1.00, bad:1.16, unknown:1.06 };

function getPrice(key){
    const value = Number(DB_PRICES[key]);
    if(Number.isFinite(value) && value > 0){ return value; }
    return null;
}

function getNewBuildBase(){
    const p3b = getPrice("bina_sinif_3b_m2");
    const p4a = getPrice("bina_sinif_4a_m2");

    if(p3b === null || p4a === null){ return null; }

    return (p3b + p4a) / 2;
}

function getRenovationBase(){
    return getPrice("tadilat_standart_m2");
}

function calculate(){
    const area = clamp(Number(state.area) || 150, 20, 50000);
    let base;

    if(state.project_type === "new_build"){ base = getNewBuildBase(); }
    else if(state.project_type === "commercial"){ base = getRenovationBase(); base = base === null ? null : base * 1.35; }
    else{ base = getRenovationBase(); }

    if(base === null || Number.isFinite(base) === false || base <= 0){
        throw new Error("Güncel fiyat verisi bulunamadı. Lütfen Kainta İnşaat proje ekibiyle iletişime geçin.");
    }

    if(state.project_type === "renovation" || state.project_type === "partial_renovation"){ base *= SCOPE_MULTIPLIERS[state.scope] || 1; }
    if(state.project_type === "partial_renovation"){ base *= .78; }

    base *= QUALITY_MULTIPLIERS[state.quality] || 1;
    if(state.project_type !== "new_build" && state.project_type !== "commercial"){ base *= CONDITION_MULTIPLIERS[state.condition] || 1; }
    base *= CITY_MULTIPLIERS[state.city] || 1;

    let scaleMultiplier = 1;
    if(area < 70){ scaleMultiplier = 1.18; }
    else if(area < 120){ scaleMultiplier = 1.10; }
    else if(area < 250){ scaleMultiplier = 1.00; }
    else if(area < 500){ scaleMultiplier = .94; }
    else{ scaleMultiplier = .88; }
    base *= scaleMultiplier;
    base *= STEALTH_MULTIPLIER;

    let total = area * base;
    total = Math.max(total, 35000);

    let variance = .14;
    if(state.condition === "unknown"){ variance += .04; }
    if(state.project_type === "commercial"){ variance += .04; }

    const min = total * (1 - variance);
    const max = total * (1 + variance);

    return { total:total, min:min, max:max, m2:base, area:area };
}

function getBreakdown(result){
    let ratios;
    if(state.project_type === "new_build"){ ratios = { kaba:.34, ince:.27, mekanik:.21, elektrik:.18 }; }
    else if(state.project_type === "commercial"){ ratios = { kaba:.12, ince:.40, mekanik:.28, elektrik:.20 }; }
    else{ ratios = { kaba:.08, ince:.43, mekanik:.29, elektrik:.20 }; }
    return { one:result.total * ratios.kaba, two:result.total * ratios.ince, three:result.total * ratios.mekanik, four:result.total * ratios.elektrik };
}

function buildResultInfo(){
    if(state.project_type === "new_build"){ return "Sıfırdan inşaatlarda arsa, zemin, mimari proje, statik sistem, ruhsat ve uygulama detayları nihai maliyeti değiştirebilir. Bu nedenle sonuç ön bütçe seviyesindedir."; }
    if(state.project_type === "commercial"){ return "Ticari projelerde kullanım amacı, mekanik sistemler, elektrik altyapısı, yangın sistemi ve özel imalatlar maliyeti önemli ölçüde değiştirebilir."; }
    if(state.timeline === "now"){ return "Başlangıç tarihiniz yakın olduğu için proje değerlendirmesi açısından daha yüksek öncelikli bir talep oluşturdunuz."; }
    return "Tadilatta mevcut yapının gerçek durumu fiyatı en fazla değiştiren unsurlardan biridir. Keşif ve metraj sonrası daha kesin teklif hazırlanabilir.";
}

function updateProgress(){
    const percent = ((currentStep - 1) / totalSteps) * 100;
    document.getElementById("progress").style.width = Math.max(14.28, percent) + "%";
    document.getElementById("stepText").textContent = currentStep + " / " + totalSteps;
    document.querySelectorAll(".step").forEach(function(el){ el.classList.toggle("active", Number(el.dataset.step) === currentStep); });
}

function selectOption(button){
    const key = button.dataset.key;
    const value = button.dataset.value;
    document.querySelectorAll('.option[data-key="' + key + '"]').forEach(function(el){ el.classList.remove("selected"); });
    button.classList.add("selected");
    state[key] = value;

    if(key === "project_type" || key === "scope" || key === "quality" || key === "condition"){
        setTimeout(function(){ nextStep(); }, 180);
    }
    if(key === "timeline"){
        setTimeout(function(){ calculateAndShow(); }, 220);
    }
}

function validateStep(){
    if(currentStep === 1){ return !!state.project_type; }
    if(currentStep === 2){ state.city = document.getElementById("city").value; state.district = document.getElementById("district").value.trim(); return !!state.city; }
    if(currentStep === 3){ const input = Number(document.getElementById("areaInput").value); state.area = clamp(Number.isFinite(input) ? input : 0, 20, 50000); return state.area >= 20; }
    if(currentStep === 4){ return !!state.scope; }
    if(currentStep === 5){ return !!state.quality; }
    if(currentStep === 6){ return !!state.condition; }
    return true;
}

function skipForward(){
    if(state.project_type === "new_build" || state.project_type === "commercial"){
        if(currentStep === 4 || currentStep === 6){ currentStep++; }
    }
}
function skipBackward(){
    if(state.project_type === "new_build" || state.project_type === "commercial"){
        if(currentStep === 6 || currentStep === 4){ currentStep--; }
    }
}

function nextStep(){
    if(!validateStep()){
        if(currentStep === 2){ document.getElementById("city").classList.add("error"); }
        if(currentStep === 3){ document.getElementById("areaInput").classList.add("error"); }
        return;
    }
    if(currentStep < totalSteps){
        currentStep++;
        skipForward();
        updateProgress();
        window.scrollTo({ top:0, behavior:"smooth" });
    }
}

function backStep(){
    if(currentStep > 1){
        currentStep--;
        skipBackward();
        updateProgress();
        window.scrollTo({ top:0, behavior:"smooth" });
    }
}

function updateLeadQuality(){
    let quality = "unqualified";
    if(state.budget_confirmed && state.timeline === "now"){ quality = "hot"; }
    else if(state.budget_confirmed && state.timeline === "soon"){ quality = "warm"; }
    else if(state.budget_confirmed && state.timeline === "later"){ quality = "nurture"; }
    else if(state.timeline === "research"){ quality = "research"; }

    state.lead_quality = quality;
    document.getElementById("leadQuality").value = quality;
}


/* WORK GROUPS DATA V1 */
const WORK_GROUPS = {
    new_build: [
        {
            title: "Proje ve hazırlık",
            items: [
                "Şantiye ve saha hazırlığı",
                "Proje ve uygulama koordinasyonu",
                "Kazı ve temel hazırlıkları"
            ]
        },
        {
            title: "Kaba yapı",
            items: [
                "Betonarme taşıyıcı sistem",
                "Duvar ve bölme imalatları",
                "Çatı ve temel yalıtımları"
            ]
        },
        {
            title: "İnce işler",
            items: [
                "Sıva, alçı ve boya",
                "Şap ve zemin kaplamaları",
                "Kapı, doğrama ve tamamlayıcı imalatlar"
            ]
        },
        {
            title: "Mekanik ve elektrik",
            items: [
                "Temiz ve atık su tesisatı",
                "Isıtma, soğutma ve havalandırma",
                "Elektrik ve aydınlatma altyapısı"
            ]
        }
    ],

    renovation: [
        {
            title: "Söküm ve hazırlık",
            items: [
                "Mevcut imalatların kontrollü sökümü",
                "Yüzey ve altyapı hazırlıkları",
                "Gerekli tamiratlar"
            ]
        },
        {
            title: "İnce işler",
            items: [
                "Duvar, tavan ve alçı uygulamaları",
                "Boya ve dekoratif uygulamalar",
                "Zemin ve kaplama işleri"
            ]
        },
        {
            title: "Mekanik ve elektrik",
            items: [
                "Tesisat revizyonları",
                "Elektrik ve aydınlatma işleri",
                "Havalandırma ve iklimlendirme düzenlemeleri"
            ]
        },
        {
            title: "Tamamlama",
            items: [
                "Kapı ve doğrama işleri",
                "Armatür ve aksesuar montajları",
                "Son kontroller ve teslim hazırlığı"
            ]
        }
    ],

    partial_renovation: [
        {
            title: "Lokal söküm",
            items: [
                "Değişecek imalatların sökümü",
                "Hasarlı alanların hazırlanması",
                "Lokal tamiratlar"
            ]
        },
        {
            title: "Seçili ince işler",
            items: [
                "Boya ve yüzey yenilemeleri",
                "Zemin veya duvar kaplamaları",
                "Tavan ve bölme düzenlemeleri"
            ]
        },
        {
            title: "Tesisat revizyonları",
            items: [
                "Gerekli mekanik müdahaleler",
                "Elektrik hattı düzenlemeleri",
                "Armatür değişimleri"
            ]
        },
        {
            title: "Tamamlama",
            items: [
                "Montaj ve rötuşlar",
                "Temizlik ve kontrol",
                "Teslim hazırlığı"
            ]
        }
    ],

    commercial: [
        {
            title: "Mahal ve uygulama hazırlığı",
            items: [
                "Mevcut alanın hazırlanması",
                "Bölme ve yerleşim düzenlemeleri",
                "Uygulama koordinasyonu"
            ]
        },
        {
            title: "İnce işler",
            items: [
                "Tavan ve duvar sistemleri",
                "Zemin kaplamaları",
                "Boya ve dekoratif uygulamalar"
            ]
        },
        {
            title: "Mekanik sistemler",
            items: [
                "Isıtma, soğutma ve havalandırma",
                "Temiz ve atık su altyapısı",
                "Kullanım amacına bağlı mekanik düzenlemeler"
            ]
        },
        {
            title: "Elektrik ve tamamlayıcı işler",
            items: [
                "Elektrik ve aydınlatma",
                "Zayıf akım altyapısı",
                "Son montaj ve teslim kontrolleri"
            ]
        }
    ]
};

const WORK_GROUP_INTROS = {
    new_build:
        "Sıfırdan inşaatta taşıyıcı sistem, duvarlar, kaplamalar, mekanik ve elektrik imalatları birlikte değerlendirilir.",

    renovation:
        "Komple tadilatta söküm, mevcut altyapının durumu, ıslak hacimler, yüzeyler ve tesisat yenilemeleri bütçeyi birlikte etkiler.",

    partial_renovation:
        "Kısmi tadilatta yalnız müdahale edilecek mahal ve imalatlara odaklanmak daha sağlıklı bir ön bütçe oluşturur.",

    commercial:
        "Ticari projelerde bölme-tavan sistemleri, yoğun kullanıma uygun zeminler, HVAC ve elektrik altyapısı öne çıkar."
};

const WORK_RELATED_TOOLS = {
    new_build: [
        ["Beton Hacmi ve Maliyeti","/araclar/beton-hesaplayici"],
        ["Demir Donatı Metrajı","/araclar/demir-metraji-hesaplayici"],
        ["Kalıp / Plywood Maliyeti","/araclar/kalip-plywood-maliyeti"],
        ["Hafriyat ve Kazı","/araclar/hafriyat-kazi-hesabi"]
    ],

    renovation: [
        ["Mutfak Tadilatı","/araclar/mutfak-maliyet-hesaplayici"],
        ["Banyo Tadilatı","/araclar/banyo-maliyet-hesaplayici"],
        ["Boya Hesabı","/araclar/boya-hesaplayici"],
        ["Parke Maliyeti","/araclar/parke-maliyet-hesaplayici"]
    ],

    partial_renovation: [
        ["Boya Hesabı","/araclar/boya-hesaplayici"],
        ["Seramik Maliyeti","/araclar/seramik-maliyet-hesaplayici"],
        ["Alçıpan Hesabı","/araclar/alcipan-hesaplayici"],
        ["Su Tesisatı","/araclar/su-tesisat-hesaplama"]
    ],

    commercial: [
        ["Havalandırma / HVAC","/araclar/havalandirma-hesaplayici"],
        ["Asma Tavan","/araclar/asma-tavan-hesaplayici"],
        ["Epoksi Zemin","/araclar/epoksi-zemin-hesaplayici"],
        ["Kablo Kesiti","/araclar/kablo-kesiti-hesabi"]
    ]
};
function renderWorkGroups(){
    const intro = document.getElementById("workGroupsIntro");
    const grid = document.getElementById("workGroupsGrid");

    if(!intro || !grid){
        return;
    }

    const groups = WORK_GROUPS[state.project_type] || [];

    intro.textContent =
        WORK_GROUP_INTROS[state.project_type] ||
        "Projeniz için öne çıkan iş grupları gösterilmektedir.";

    const related = document.getElementById("workRelatedLinks");

    if(related){
        const tools = WORK_RELATED_TOOLS[state.project_type] || [];

        related.innerHTML = tools.map(function(tool){
            return "<a href=\"" + tool[1] + "\">" + tool[0] + " →</a>";
        }).join("");
    }

    grid.innerHTML = "";

    groups.forEach(function(group){
        const card = document.createElement("div");
        card.className = "work-card";

        const title = document.createElement("h4");
        title.textContent = group.title;

        const list = document.createElement("ul");

        group.items.forEach(function(item){
            const li = document.createElement("li");
            li.textContent = item;
            list.appendChild(li);
        });

        card.appendChild(title);
        card.appendChild(list);
        grid.appendChild(card);
    });
}

function calculateAndShow(){
    try {
        state.result = calculate();
    } catch (error) {
        alert(error.message || "Fiyat hesaplanamadı.");
        return;
    }
    const result = state.result;
    const breakdown = getBreakdown(result);

    document.getElementById("resultPrice").textContent = tl(result.total);
    document.getElementById("resultRange").textContent = "Yaklaşık " + tl(result.min) + " – " + tl(result.max);
    document.getElementById("break1").textContent = tl(breakdown.one);
    document.getElementById("break2").textContent = tl(breakdown.two);
    document.getElementById("break3").textContent = tl(breakdown.three);
    document.getElementById("break4").textContent = tl(breakdown.four);
    document.getElementById("resultInfo").textContent = buildResultInfo();
    renderWorkGroups();

    document.getElementById("estimatedCost").value = Math.round(result.total);
    document.getElementById("estimatedMin").value = Math.round(result.min);
    document.getElementById("estimatedMax").value = Math.round(result.max);

    // AI Render Parametresi Güncelleme
    document.getElementById("renderParamText").textContent = qualityLabel(state.quality) + " " + projectLabel(state.project_type);

    // AI Kutu Resetleme
    document.getElementById('btnGenerateRender').style.display = 'block';
    document.getElementById('aiRenderContainer').style.display = 'none';
    document.getElementById('aiResultImage').src = '';
    document.getElementById('aiResultImage').style.display = 'none';

    state.budget_confirmed = false;
    state.lead_quality = state.timeline === "research" ? "research" : "unqualified";
    document.getElementById("budgetConfirmed").value = "0";
    document.getElementById("leadQuality").value = state.lead_quality;

    document.getElementById("projectData").value = JSON.stringify({ source: "worksitem_contech_60s", ...state });

    document.querySelectorAll(".step").forEach(function(el){ el.classList.remove("active"); });
    document.getElementById("result").classList.add("active");
    document.getElementById("progress").style.width = "100%";

    document.getElementById("leadBox").style.display = "none";
    document.getElementById("qualifiedMessage").classList.remove("active");
    document.querySelectorAll(".qual-option").forEach(function(el){ el.classList.remove("selected"); });

    createWhatsAppLink();
    window.scrollTo({ top:0, behavior:"smooth" });
}

function handleBudgetSelection(button){
    const value = button.dataset.budget;
    document.querySelectorAll(".qual-option").forEach(function(el){ el.classList.remove("selected"); });
    button.classList.add("selected");

    if(value === "yes"){
        state.budget_confirmed = true;
        document.getElementById("budgetConfirmed").value = "1";
        updateLeadQuality();
        document.getElementById("qualifiedMessage").classList.add("active");
        document.getElementById("leadBox").style.display = "block";
        createWhatsAppLink();
        setTimeout(function(){ document.getElementById("leadBox").scrollIntoView({ behavior:"smooth", block:"center" }); }, 100);
    } else {
        state.budget_confirmed = false;
        document.getElementById("budgetConfirmed").value = "0";
        updateLeadQuality();
        document.getElementById("qualifiedMessage").classList.remove("active");
        document.getElementById("leadBox").style.display = "none";
    }

    document.getElementById("projectData").value = JSON.stringify({ source: "worksitem_contech_60s", ...state });
}

function createWhatsAppLink(){
    if(!state.result){ return; }
    const message = "Merhaba Kainta İnşaat.\n\nWorksitem üzerinden proje bütçemi hesapladım.\n\nProje: " + projectLabel(state.project_type) + "\nKonum: " + (state.city || "-") + (state.district ? " / " + state.district : "") + "\nAlan: " + state.area + " m²\nKapsam: " + scopeLabel(state.scope) + "\nKalite: " + qualityLabel(state.quality) + "\nBaşlangıç: " + timelineLabel(state.timeline) + "\nYaklaşık bütçe: " + tl(state.result.total) + "\n\nBu bütçe aralığını karşılayabileceğimi onaylıyorum.\n\nProjemin Kainta tarafından değerlendirilmesini istiyorum.";
    document.getElementById("whatsappLink").href = "https://wa.me/908503464983?text=" + encodeURIComponent(message);
}

function projectLabel(value){ const map = { new_build: "Sıfırdan İnşaat", renovation: "Komple Tadilat", partial_renovation: "Kısmi Tadilat", commercial: "Ticari Proje" }; return map[value] || value || "-"; }
function scopeLabel(value){ const map = { light: "Hafif Yenileme", medium: "Orta Kapsam", complete: "Komple Yenileme", turnkey: "Anahtar Teslim" }; return map[value] || value || "-"; }
function qualityLabel(value){ const map = { economic: "Ekonomik", standard: "Standart", premium: "Premium", luxury: "Lüks" }; return map[value] || value || "-"; }
function timelineLabel(value){ const map = { now: "0–1 ay", soon: "1–3 ay", later: "3–6 ay", research: "Sadece araştırıyorum" }; return map[value] || value || "-"; }

function restart(){
    state.project_type = ""; state.city = ""; state.district = ""; state.area = 150; state.scope = ""; state.quality = ""; state.condition = ""; state.timeline = ""; state.budget_confirmed = false; state.lead_quality = "unqualified"; state.result = null;
    currentStep = 1;
    document.getElementById("city").value = "";
    document.getElementById("district").value = "";
    document.getElementById("areaInput").value = "150";
    document.getElementById("areaRange").value = "150";
    document.getElementById("areaValue").textContent = "150";
    document.querySelectorAll(".option").forEach(function(el){ el.classList.remove("selected"); });
    document.querySelectorAll(".qual-option").forEach(function(el){ el.classList.remove("selected"); });
    document.getElementById("result").classList.remove("active");
    document.getElementById("leadBox").style.display = "none";
    document.getElementById("qualifiedMessage").classList.remove("active");
    updateProgress();
    window.scrollTo({ top:0, behavior:"smooth" });
}

function shareResult(){
    if(!state.result){ return; }
    const text = "Worksitem maliyet tahmini: " + tl(state.result.total) + " | " + state.area + " m²";
    if(navigator.share){ navigator.share({ title: "Worksitem Maliyet Tahmini", text: text, url: window.location.href }).catch(function(){}); }
    else if(navigator.clipboard){ navigator.clipboard.writeText(text).then(function(){ alert("Sonuç panoya kopyalandı."); }); }
}

/* EVENTS */
document.querySelectorAll(".option").forEach(function(button){ button.addEventListener("click", function(){ selectOption(button); }); });
document.querySelectorAll("[data-next]").forEach(function(button){ button.addEventListener("click", nextStep); });
document.querySelectorAll("[data-back]").forEach(function(button){ button.addEventListener("click", backStep); });
document.getElementById("calculateButton").addEventListener("click", calculateAndShow);
document.getElementById("restartButton").addEventListener("click", restart);
document.getElementById("shareButton").addEventListener("click", shareResult);
document.querySelectorAll(".qual-option").forEach(function(button){ button.addEventListener("click", function(){ handleBudgetSelection(button); }); });
document.getElementById("city").addEventListener("change", function(){ this.classList.remove("error"); state.city = this.value; });
document.getElementById("areaRange").addEventListener("input", function(){ const value = Number(this.value); document.getElementById("areaValue").textContent = value; document.getElementById("areaInput").value = value; state.area = value; });
document.getElementById("areaInput").addEventListener("input", function(){ const value = clamp(Number(this.value) || 0, 20, 50000); document.getElementById("areaRange").value = Math.min(value,5000); document.getElementById("areaValue").textContent = value; state.area = value; this.classList.remove("error"); });

// AI Render Event Listener
document.getElementById('btnGenerateRender').addEventListener('click', function() {
    const btn = this;
    const container = document.getElementById('aiRenderContainer');
    const loader = document.getElementById('aiLoader');
    const img = document.getElementById('aiResultImage');

    btn.style.display = 'none';
    container.style.display = 'block';
    loader.style.display = 'block';
    img.style.display = 'none';

    fetch('/maliyet-hesapla/guest-render', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            type: state.project_type,
            quality: state.quality,
            scope: state.scope
        })
    })
    .then(response => {
        if (!response.ok) throw new Error("Render servisi şu an yanıt vermiyor.");
        return response.json();
    })
    .then(data => {
        if(data.image_url) {
            loader.style.display = 'none';
            img.src = data.image_url;
            img.style.display = 'block';
        } else {
            throw new Error("Görsel oluşturulamadı.");
        }
    })
    .catch(error => {
        loader.innerHTML = "⚠️ Render oluşturulurken bir hata oluştu.<br>Lütfen daha sonra tekrar deneyin veya teklif aşamasına geçin.";
        setTimeout(() => { btn.style.display = 'block'; container.style.display = 'none'; }, 4000);
    });
});

document.getElementById("leadForm").addEventListener("submit", function(e){
    if(!state.budget_confirmed){ alert("Kainta İnşaat'a başvurmadan önce bütçe aralığınızı onaylayın."); e.preventDefault(); return; }
    updateLeadQuality();
    document.getElementById("projectData").value = JSON.stringify({ source: "worksitem_contech_60s", ...state });
});

updateProgress();
})();
</script>

<script type="application/ld+json">
{
    "@@context":"https://schema.org",
    "@@type":"WebApplication",
    "name":"WORKSİTEM İnşaat & Tadilat Maliyet Hesaplama",
    "url":"https://worksitem.com/maliyet-hesapla",
    "applicationCategory":"BusinessApplication",
    "operatingSystem":"Web",
    "description":"İnşaat ve tadilat projeleri için yaklaşık maliyet hesaplama aracı.",
    "provider":{
        "@@type":"Organization",
        "name":"Kainta İnşaat",
        "url":"https://kainta.com"
    }
}
</script>

<x-site.kainta-linker />
<script src="/js/p1-product-events.js" defer></script>

{{-- P1_SERAMIK_INTERNAL_LINK --}}
<div style="max-width:1120px;margin:24px auto;padding:0 20px;text-align:center">
    <a
        href="/araclar/seramik-maliyet-hesaplayici"
        data-ws-internal-link
        data-ws-target-tool="seramik_v4"
        style="font-weight:800;text-decoration:underline;text-underline-offset:4px"
    >Seramik ve fayans metrajını ayrıca hesapla →</a>
</div>
    <x-site.contech-footer />
</body>
</html>

<style>
@media(max-width:760px){
    .header{
        position:relative!important;
        top:auto!important;
    }
}
</style>

<style>
@media(max-width:760px){
    .header{
        position:relative!important;
        top:auto!important;
    }
}
</style>
