<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yalıtım ve Mantolama Motoru | WORKSİTEM</title>
    <meta name="description" content="Dış cephe mantolama, su yalıtımı ve içten straforlu alçıpan yalıtım seçenekleri için yaklaşık metraj ve uygulama maliyetini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/mantolama-maliyeti">
    <meta property="og:url" content="https://worksitem.com/araclar/mantolama-maliyeti">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280; --line: #e4e7eb; --accent: #ffb800; --radius: 16px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .brand img { height: 30px; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .tool-header { margin-bottom: 30px; text-align: center; }
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; }
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1px; }
        .ai-insight { background: rgba(255,184,0,0.1); border: 1px solid rgba(255,184,0,0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; }
        .ai-insight-text strong { color: #8a6200; font-size: 14px; display: block; margin-bottom: 4px;}
        .ai-insight-text p { font-size: 12px; color: #e4e4e7; line-height: 1.5; }
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
<body data-tool-slug="yalitim-calculator">
<x-site.contech-header />
<div class="container">
    <div class="tool-header">
        <h1>Yalıtım ve Mantolama Motoru</h1>
        <p>Dış cephe mantolama, su yalıtımı veya içten straforlu alçıpan yalıtım ihtiyaçlarınızın maliyetini simüle edin.</p>
    </div>
    <div class="card">
        <div class="form-group"><label>Uygulama Yüzey Alanı (m²)</label><input type="number" id="area" class="form-control" value="200" min="10" step="10"></div>
        <div class="form-group">
            <label>Yalıtım Türü</label>
            <select id="type" class="form-control">
                <option value="650_dis">Dış Cephe Mantolama (EPS / Taşyünü + Boya)</option>
                <option value="250_su">Su Yalıtımı (Teras / Temel Sürme Membran)</option>
                <option value="450_ic" selected>Straforlu Alçıpan ile İçten Isı Yalıtımı</option>
            </select>
        </div>
        <div class="form-group">
            <label>İskele Kurulumu Gerekiyor Mu?</label>
            <select id="scaffold" class="form-control">
                <option value="0" selected>Hayır, Gerekmiyor (İç Cephe / Zemin)</option>
                <option value="120">Evet, Dış Cephe İskelesi Kurulacak (+İskele Bedeli)</option>
            </select>
        </div>
        <button type="button" class="btn-calc" id="calcBtn">Yalıtım Bütçesini Hesapla →</button>
    </div>

    <div class="result-box" id="resBox">
        <div style="font-size: 12px; color: #a1a1aa; font-weight: 700; letter-spacing: 1px;">ANAHTAR TESLİM İZOLASYON BÜTÇESİ</div>
        <div class="result-price" id="resPrice">0 TL</div>
        <div class="ai-insight">
            <div style="font-size: 24px;">🌡️</div>
            <div class="ai-insight-text"><strong id="aiTitle">Enerji Verimliliği Notu</strong><p id="aiText"></p></div>
        </div>
    </div>
    
    <div class="budget-confirm-box" id="bConf"><div style="font-size: 18px; font-weight: 900;">Kainta İnşaat'tan Enerji Kimlik Teklifi Alın</div><div class="budget-confirm-actions"><button class="budget-btn budget-yes" id="bYes">✓ Evet, teklif istiyorum.</button><button class="budget-btn" id="bNo">Sadece maliyete baktım.</button></div></div>
    
    <div class="lead-box" id="leadForm">
        <h3>Profesyonel Yalıtım & İzolasyon Teklifi</h3>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf <input type="hidden" name="calculated_service" value="Yalıtım ve Mantolama">
            <input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız" style="margin-bottom:15px;">
            <input type="tel" name="phone" class="form-control" required placeholder="Telefon Numaranız" style="margin-bottom:15px;">
            <input type="text" name="location" class="form-control" required placeholder="Şehir/İlçe" style="margin-bottom:15px;">
            <button type="submit" class="btn-submit">Teklif Al →</button>
        </form>
    </div>
</div>
<script>
document.getElementById('calcBtn').onclick = () => {
    const area = parseFloat(document.getElementById('area').value)||0;
    const val = document.getElementById('type').value.split('_');
    const price = parseFloat(val[0]); const key = val[1];
    const scaffold = parseFloat(document.getElementById('scaffold').value)||0;
    
    if(area<=0) return alert('Geçerli ölçü giriniz.');
    
    const total = area * (price + scaffold);
    
    document.getElementById('resPrice').innerText = new Intl.NumberFormat('tr-TR').format(total) + ' TL';
    
    const ai = document.getElementById('aiText');
    if(key==='dis') ai.innerText="Dış cephe mantolaması bina kabuğunu komple sararak ısı köprülerini yok eder. Kışın doğalgaz, yazın klima faturanızda %40'a varan kalıcı tasarruf sağlar.";
    else if(key==='ic') ai.innerText="Apartman veya site yönetimi dış cepheye izin vermiyorsa, odalarınızın dışarıya bakan iç duvarlarına Straforlu Alçıpan kaplayarak harika bir ısı yalıtımı sağlayabilirsiniz. Oda metrajınız sadece 5-6 cm daralır.";
    else ai.innerText="Su yalıtımı, betonarmenin demir donatılarını korozyondan (pastan) koruyan en hayati katmandır. Poliüretan veya çift kompenantlı sürme malzemelerle teras ve ıslak hacimleriniz koruma altına alınır.";

    document.getElementById('resBox').style.display = 'block';
    document.getElementById('bConf').style.display = 'block';
    document.getElementById('leadForm').style.display = 'none';
};
document.getElementById('bYes').onclick = () => { document.getElementById('bConf').style.display='none'; document.getElementById('leadForm').style.display='block'; };
document.getElementById('bNo').onclick = () => { document.getElementById('bConf').innerHTML = '<p>Enerji tasarrufu yolunda bizi tercih ettiğiniz için teşekkürler.</p>'; };
</script>
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body></html>
