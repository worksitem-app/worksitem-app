<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duvar Örgü & Kaba Sıva Motoru | WORKSİTEM</title>
    <meta name="description" content="Tuğla, bims veya gazbeton duvar için gerekli malzeme adedini, kaba sıva ihtiyacını ve yaklaşık duvar örgü bütçesini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/duvar-orgu">
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
        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; }
        .ai-insight-text strong { color: var(--accent); font-size: 14px; display: block; margin-bottom: 4px;}
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
</head>
<body data-tool-slug="duvar-calculator">
<x-site.contech-header />
<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px;">🧱 Duvar Örgü ve Sıva Motoru</h1>
        <p style="color: var(--muted); font-size: 15px;">Tuğla, bims veya gazbeton (Ytong) duvar ihtiyacınız için kullanılacak adet miktarını ve örgü bütçesini çıkarın.</p>
    </div>
    <div class="card">
        <div class="form-grid">
            <div class="form-group"><label>Duvar Uzunluğu (m)</label><input type="number" id="w" class="form-control" value="10" min="1" step="0.5"></div>
            <div class="form-group"><label>Duvar Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="3" min="1" step="0.1"></div>
        </div>
        <div class="form-group">
            <label>Malzeme Seçimi</label>
            <select id="type" class="form-control">
                <option value="15_tugla">Yatay Delikli Tuğla (8.5 cm veya 13.5 cm) - m2'ye ~25 Adet</option>
                <option value="35_bims">Bims Blok (İzoleli) - m2'ye ~12 Adet</option>
                <option value="60_gazbeton" selected>Gazbeton (Ytong) - m2'ye ~7 Adet</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kaba Sıva İşlemi</label>
            <select id="plaster" class="form-control">
                <option value="0">Sıva Yok (Sadece Örgü)</option>
                <option value="250" selected>Tek Yüz Kaba Sıva Yapılacak (+Maliyet)</option>
            </select>
        </div>
        <button type="button" class="btn-calc" id="calcBtn">Duvar Metrajını Hesapla →</button>
    </div>

    <div class="result-box" id="resBox">
        <div style="font-size: 12px; color: #a1a1aa; font-weight: 700; letter-spacing: 1px;">MALZEME VE İŞÇİLİK BÜTÇESİ</div>
        <div class="result-price" id="resPrice">0 TL</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left;">
            <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <span style="display: block; font-size: 12px; color: #a1a1aa;">Toplam Duvar Alanı</span>
                <strong id="resArea" style="display: block; font-size: 18px; color: #fff; font-weight: 800;">0 m²</strong>
            </div>
            <div style="background: rgba(255,184,0,0.1); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,184,0,0.3);">
                <span style="display: block; font-size: 12px; color: #a1a1aa;">Gereken Blok/Tuğla (Fireli)</span>
                <strong id="resCount" style="display: block; font-size: 18px; color: var(--accent); font-weight: 800;">0 Adet</strong>
            </div>
        </div>
        <div class="ai-insight">
            <div style="font-size: 24px;">🧱</div>
            <div class="ai-insight-text"><strong id="aiTitle">Mimari Not</strong><p id="aiText"></p></div>
        </div>
    </div>
    
    <div class="budget-confirm-box" id="bConf"><div style="font-size: 18px; font-weight: 900;">Duvar ve kaba inşaat için teklif ister misiniz?</div><div class="budget-confirm-actions"><button class="budget-btn budget-yes" id="bYes">✓ Evet, teklif istiyorum.</button><button class="budget-btn" id="bNo">Sadece metraja baktım.</button></div></div>
    
    <div class="lead-box" id="leadForm">
        <h3>Profesyonel Kaba İnşaat Teklifi</h3>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf <input type="hidden" name="calculated_service" value="Duvar Örgü ve Sıva">
            <input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız" style="margin-bottom:15px;">
            <input type="tel" name="phone" class="form-control" required placeholder="Telefon Numaranız" style="margin-bottom:15px;">
            <input type="text" name="location" class="form-control" required placeholder="Şehir/İlçe" style="margin-bottom:15px;">
            <button type="submit" class="btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>
</div>
<script>
document.getElementById('calcBtn').onclick = () => {
    const w = parseFloat(document.getElementById('w').value)||0;
    const h = parseFloat(document.getElementById('h').value)||0;
    const val = document.getElementById('type').value.split('_');
    const unitPrice = parseFloat(val[0]); const key = val[1];
    const plaster = parseFloat(document.getElementById('plaster').value)||0;
    
    if(w<=0 || h<=0) return alert('Geçerli ölçü giriniz.');
    
    const area = w * h;
    let pcsPerM2 = 25; // Tuğla
    if(key==='bims') pcsPerM2 = 12;
    if(key==='gazbeton') pcsPerM2 = 7;
    
    // %10 kırılma fire payı
    const totalPcs = Math.ceil(area * pcsPerM2 * 1.10); 
    
    // İşçilik ve harç maliyetini hesaba katarak m2 üzerinden yuvarlak hesaplama (Piyasa Rayici)
    let m2Cost = 0;
    if(key==='tugla') m2Cost = 450;
    if(key==='bims') m2Cost = 550;
    if(key==='gazbeton') m2Cost = 650;
    
    const total = area * (m2Cost + plaster);
    
    document.getElementById('resPrice').innerText = new Intl.NumberFormat('tr-TR').format(total) + ' TL';
    document.getElementById('resArea').innerText = area.toFixed(1) + ' m²';
    document.getElementById('resCount').innerText = totalPcs + ' Adet';
    
    const ai = document.getElementById('aiText');
    if(key==='gazbeton') ai.innerText="Gazbeton (Ytong) bloklar yüksek ısı yalıtımı sağlar ve binanın statik yükünü oldukça hafifletir. Özel yapıştırıcısı ile örülmelidir.";
    else if(key==='bims') ai.innerText="Bims bloklar, volkanik pomza taşından üretildiği için ses yalıtımı konusunda üst düzey performans sunar.";
    else ai.innerText="Yatay delikli tuğla en ekonomik çözümdür, ancak ısı ve ses yalıtımı için üzerine mutlaka iyi bir sıva veya mantolama işlemi yapılması gerekir.";

    document.getElementById('resBox').style.display = 'block';
    document.getElementById('bConf').style.display = 'block';
    document.getElementById('leadForm').style.display = 'none';
};
document.getElementById('bYes').onclick = () => { document.getElementById('bConf').style.display='none'; document.getElementById('leadForm').style.display='block'; };
document.getElementById('bNo').onclick = () => { document.getElementById('bConf').innerHTML = '<p>Bizi tercih ettiğiniz için teşekkürler.</p>'; };
</script>
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body></html>
