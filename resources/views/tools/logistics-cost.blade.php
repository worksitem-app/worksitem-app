<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Lojistik & Nakliye Maliyeti | WORKSİTEM</title>
    <meta name="description" content="Hafriyat ve dökme malzeme taşımalarında tonaj, mesafe, yakıt ve sefer verilerine göre toplam lojistik ve nakliye maliyetini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/lojistik-maliyeti"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .wc-warn{color:#b91c1c;font-size:14px;font-weight:700} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} @media(max-width:600px){.grid-2{grid-template-columns:1fr}} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="logistics-cost">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Nakliye ve kamyon sefer maliyeti hesaplama" crumb="Nakliye maliyeti">Hafriyat veya dökme malzemelerin tonaj, mesafe ve yakıt verilerine göre toplam lojistik bütçesini planlayın.</x-calc.hero>
@php($araclar = config('worksitem_mevzuat_2026.lojistik_arac'))
<div class="grid-2"><div class="form-group"><label>Toplam Taşınacak Tonaj</label><input type="number" id="ton" value="500" min="0" oninput="calc()"></div><div class="form-group"><label>Araç Tipi</label><select id="vtype" onchange="pick()">@foreach($araclar as $k => $a)<option value="{{ $k }}" data-ton="{{ $a['ton'] }}" data-km="{{ $a['km'] }}" @selected($k === 'damper')>{{ $a['ad'] }}</option>@endforeach</select></div><div class="form-group"><label>Araç Kapasitesi (Ton)</label><input type="number" id="cap" value="25" min="0" step="0.5" oninput="calc()"></div><div class="form-group"><label>Gidiş-Dönüş Mesafe (km)</label><input type="number" id="dist" value="40" min="0" oninput="calc()"></div><div class="form-group"><label>Km Başı Araç Gideri (TL/km)</label><input type="number" id="km" value="35" min="0" oninput="calc()"><small style="color:#6b7280">Yakıt, lastik ve aşınma; piyasa tahmini, kendi değerinizi girin.</small></div><div class="form-group"><label>Sefer Başı Sabit Ücret (TL)</label><input type="number" id="fee" value="{{ (float) data_get($pricing ?? [], 'lojistik.sefer_ucreti', 3500) }}" min="0" oninput="calc()"><small style="color:#6b7280">Yükleme, boşaltma, şoför ve bekleme.</small></div></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">TOPLAM SEFER SAYISI VE MALİYET</div><div class="result-val" id="res" data-wc-price>0 TL</div><div style="font-size:13px; color:#101318; margin-top:5px;" id="res2"></div></div>
<a href="/maliyet-hesapla?service=hafriyat" class="cta">Hafriyat ve Lojistik Hizmeti İçin Fiyat Al →</a></div>

    <script>
        const fmt = (v, d=0) => new Intl.NumberFormat('tr-TR', {maximumFractionDigits: d}).format(v);
        function pick() { const o = document.getElementById('vtype').selectedOptions[0]; document.getElementById('cap').value = o.dataset.ton; document.getElementById('km').value = o.dataset.km; calc(); }
        function calc() {
            const num = id => parseFloat(document.getElementById(id).value);
            const t = num('ton'), c = num('cap'), d = num('dist'), kmRate = num('km'), fee = num('fee');
            const res = document.getElementById('res'), res2 = document.getElementById('res2');
            if (!(t > 0) || !(c > 0)) { res.innerHTML = '<span class="wc-warn">Tonaj ve kapasite için pozitif bir değer girin.</span>'; res2.innerText = ''; return; }
            if (!(d >= 0) || !(kmRate >= 0) || !(fee >= 0)) { res.innerHTML = '<span class="wc-warn">Mesafe ve ücretler negatif olamaz.</span>'; res2.innerText = ''; return; }
            const trips = Math.ceil(t / c - 1e-9);
            const tripCost = fee + d * kmRate; // sefer başı: sabit + gidiş-dönüş km × km gideri
            const cost = trips * tripCost;
            res.innerText = fmt(cost) + ' TL';
            res2.innerText = 'Sefer: ' + trips + ' · Sefer başı ' + fmt(tripCost) + ' TL (' + fmt(fee) + ' sabit + ' + fmt(d, 1) + ' km × ' + fmt(kmRate, 2) + ' TL) · Ton başı ' + fmt(cost / t, 2) + ' TL';
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
