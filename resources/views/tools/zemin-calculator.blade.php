<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zemin, Seramik ve Parke Motoru | WORKSİTEM</title>
    <meta name="description" content="Parke, seramik, fayans veya mermer zemin kaplamalarında fire dahil malzeme metrajını ve yaklaşık uygulama bütçesini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/zemin-kaplama">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280; --line: #e4e7eb; --accent: #ffb800; --radius: 16px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); }
        .brand img { height: 30px; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; }
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1px; }
        .ai-insight { background: rgba(16,19,24,0.1); border: 1px solid rgba(16,19,24,0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; }
        .ai-insight-text strong { color: #ffb800; font-size: 14px; display: block; margin-bottom: 4px;}
        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top:15px; }
        .budget-btn { min-height: 52px; border-radius: 13px; font-weight: 800; cursor: pointer; border: 1px solid var(--line); background: #fff;}
        .budget-yes { color: #111; } .budget-yes:hover { border-color: var(--accent); background: #fffaf0; }
        .lead-box { display: none; background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; }
        .btn-calc, .btn-submit { background: var(--text); color: #fff; width: 100%; height: 50px; border:none; border-radius:12px; font-weight:800; cursor:pointer;}
        .btn-submit { background: var(--accent); color: #000; }
    </style>
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
<body class="wc" data-tool-slug="zemin-calculator">
<x-site.contech-header />
<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <x-calc.hero title="Zemin kaplama ve fayans maliyeti hesaplama" crumb="Zemin kaplama">Parke, seramik, fayans veya mermer kaplama ihtiyaçlarınızın fire payı dahil malzeme metrajını ve bütçesini çıkarın.</x-calc.hero>
    </div>
    <div class="card">
        <div class="form-grid">
            <div class="form-group"><label>Oda Genişliği (m)</label><input type="number" id="w" class="form-control" value="4" min="1" step="0.5"></div>
            <div class="form-group"><label>Oda Uzunluğu (m)</label><input type="number" id="l" class="form-control" value="5" min="1" step="0.5"></div>
        </div>
        <div class="form-group">
            <label>Kaplama Tipi (Birim Fiyat)</label>
            <select id="type" class="form-control">
                <option value="350_laminat" selected>Laminat Parke (8mm Standart)</option>
                <option value="600_seramik">Seramik / Fayans (60x60 veya 60x120)</option>
                <option value="1200_granit">Granit / Mermer Zemin Kaplama</option>
            </select>
        </div>
        <div class="form-group">
            <label>Zemin Durumu / Şap İhtiyacı</label>
            <select id="base" class="form-control">
                <option value="0" selected>Zemin Düzgün (Sadece Kaplama Yapılacak)</option>
                <option value="150">Zemin Bozuk (+Tesviye Şapı Atılacak)</option>
            </select>
        </div>
        <button type="button" class="btn-calc" id="calcBtn">Zemin Maliyetini Hesapla →</button>
    </div>

    <div class="result-box" id="resBox" data-wc-result>
        <div style="font-size: 12px; color: #a1a1aa; font-weight: 700; letter-spacing: 1px;">TAHMİNİ MALZEME VE İŞÇİLİK BÜTÇESİ</div>
        <div class="result-price" id="resPrice" data-wc-price>0 TL</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left;">
            <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <span style="display: block; font-size: 12px; color: #a1a1aa;">Net Alan</span>
                <strong id="resNet" style="display: block; font-size: 18px; color: #fff; font-weight: 800;">0 m²</strong>
            </div>
            <div style="background: rgba(16,19,24,0.1); padding: 15px; border-radius: 12px; border: 1px solid rgba(16,19,24,0.3);">
                <span style="display: block; font-size: 12px; color: #a1a1aa;">Gereken Sipariş (Fire Dahil)</span>
                <strong id="resGross" style="display: block; font-size: 18px; color: #ffb800; font-weight: 800;">0 m²</strong>
            </div>
        </div>
        <div class="ai-insight">
            <div style="font-size: 24px;">💡</div>
            <div class="ai-insight-text"><strong id="aiTitle">Mimari Not</strong><p id="aiText"></p></div>
        </div>
    </div>
    
    <div class="budget-confirm-box" id="bConf"><div style="font-size: 18px; font-weight: 900;">Kainta İnşaat'tan Dekorasyon Teklifi Alın</div><div class="budget-confirm-actions"><button class="budget-btn budget-yes" id="bYes">✓ Evet, teklif istiyorum.</button><button class="budget-btn" id="bNo">Sadece maliyete baktım.</button></div></div>
    
    <div class="lead-box" id="leadForm">
        <h3>Profesyonel Dekorasyon Teklifi</h3>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf <input type="hidden" name="calculated_service" value="Zemin ve Kaplama Hesabı">
            <input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız" style="margin-bottom:15px;">
            <input type="tel" name="phone" class="form-control" required placeholder="Telefon Numaranız" style="margin-bottom:15px;">
            <input type="text" name="location" class="form-control" required placeholder="Şehir/İlçe" style="margin-bottom:15px;">
            <x-lead-consent-fields />
            <button type="submit" class="btn-submit">Teklif Al →</button>
        </form>
    </div>
</div>
<script>
document.getElementById('calcBtn').onclick = () => {
    const w = parseFloat(document.getElementById('w').value)||0;
    const l = parseFloat(document.getElementById('l').value)||0;
    const val = document.getElementById('type').value.split('_');
    const price = parseFloat(val[0]); const key = val[1];
    const base = parseFloat(document.getElementById('base').value)||0;
    
    if(w<=0 || l<=0) return alert('Geçerli ölçü giriniz.');
    
    const netArea = w * l;
    let wasteRate = 1.10; // Parkede %10 fire
    if(key === 'seramik' || key === 'granit') wasteRate = 1.15; // Seramikte %15 fire (kesim payı)
    
    const grossArea = netArea * wasteRate;
    const total = grossArea * (price + base);
    
    document.getElementById('resPrice').innerText = new Intl.NumberFormat('tr-TR').format(total) + ' TL';
    document.getElementById('resNet').innerText = netArea.toFixed(1) + ' m²';
    document.getElementById('resGross').innerText = grossArea.toFixed(1) + ' m² (Kutu/Paket Siparişi)';
    
    const ai = document.getElementById('aiText');
    if(key==='seramik' || key==='granit') ai.innerText="Seramik ve mermer döşemelerinde köşe kesimleri fazla olduğu için sipariş miktarınıza %15 fire payı ve yapıştırıcı/derz dolgu maliyeti eklenmiştir.";
    else ai.innerText="Laminat parkelerde duvar kenarındaki kesimler için %10 fire payı, şilte ve süpürgelik maliyetleri tahmini olarak bütçeye yansıtılmıştır.";

    document.getElementById('resBox').style.display = 'block';
    document.getElementById('bConf').style.display = 'block';
    document.getElementById('leadForm').style.display = 'none';
};
document.getElementById('bYes').onclick = () => { document.getElementById('bConf').style.display='none'; document.getElementById('leadForm').style.display='block'; };
document.getElementById('bNo').onclick = () => { document.getElementById('bConf').innerHTML = '<p>Bizi tercih ettiğiniz için teşekkürler.</p>'; };
</script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
<script src="/js/p1-product-events.js" defer></script>

{{-- P1_SERAMIK_INTERNAL_LINK --}}
<div style="max-width:1120px;margin:24px auto;padding:0 20px;text-align:center">
    <a
        href="/araclar/seramik-maliyet-hesaplayici"
        data-ws-internal-link
        data-ws-target-tool="seramik_v4"
        style="font-weight:800;text-decoration:underline;text-underline-offset:4px"
    >Seramik zemin metrajı ve maliyetini hesapla →</a>
</div>
</body></html>
