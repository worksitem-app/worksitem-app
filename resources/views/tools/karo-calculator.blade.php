<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Destekli Karo & Seramik Optimizasyonu | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/karo-hesaplayici">
    <meta name="description" content="Mekan ölçülerinizi ve karo ebatlarını girin, yapay zeka en düşük fireli yerleşim senaryosunu ve maliyetini çıkarsın.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280;
            --line: #e4e7eb; --accent: #ffb800; --radius: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .back-btn:hover { color: var(--text); }
        .brand img { height: 30px; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .tool-header { margin-bottom: 30px; text-align: center; }
        .badge { display: inline-block; background: rgba(255,184,0,0.15); color: #d99a00; padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; margin-bottom: 15px; letter-spacing: 0.5px; text-transform: uppercase; }
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tool-header p { color: var(--muted); font-size: 15px; max-width: 600px; margin: 0 auto; }

        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; background: #fff; transition: .2s; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,184,0,0.15); }
        
        .btn { width: 100%; height: 52px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: .2s; }
        .btn-ai { background: linear-gradient(135deg, #111, #222); color: #fff; margin-top: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .btn-ai:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(0,0,0,0.2); }

        /* AI PROCESSING OVERLAY */
        .processing-box { display: none; text-align: center; padding: 40px 20px; }
        .processing-box.active { display: block; animation: fadeIn 0.3s ease; }
        .loader { width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .process-text { font-size: 16px; font-weight: 800; color: #111; margin-bottom: 8px; }
        .process-sub { font-size: 13px; color: var(--muted); }

        /* RESULT SECTION */
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; position: relative; overflow: hidden; }
        .result-box.active { display: block; animation: fadeIn 0.5s ease; }
        .result-box::before { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(255,184,0,0.05); border-radius: 50%; top: -50px; right: -50px; }
        
        .result-label { font-size: 12px; color: #a1a1aa; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; position: relative; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 0 0 15px; letter-spacing: -1px; position: relative; }
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; position: relative; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        
        .ai-insight { background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: #4ade80; display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #a1a1aa; font-size: 12px; margin: 0; line-height: 1.5; }

        /* B2B GATE & LEAD FORM (Mevcut Standart) */
        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: var(--accent); background: #fffaf0; box-shadow: 0 0 0 3px rgba(255,184,0,.10); }

        .qualified-message { display: none; margin-top: 25px; padding: 20px; border-radius: 16px; background: #fff8e1; border: 1px solid #fde68a; text-align: center;}
        .qualified-message strong { display: block; font-size: 15px; margin-bottom: 5px; color: #101318; }
        .qualified-message span { display: block; color: #101318; font-size: 12px; line-height: 1.5; }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .lead-box h3 { font-size: 18px; font-weight: 900; margin-bottom: 8px; }
        .lead-box p { font-size: 13px; color: var(--muted); margin-bottom: 20px; line-height: 1.5; }
        .btn-submit { background: var(--accent); color: #000; margin-top: 10px; width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: .2s; }
        .btn-submit:hover { background: #e5a500; }

        @media(max-width: 600px) {
            .form-grid, .breakdown-grid, .budget-confirm-actions { grid-template-columns: 1fr; }
        }
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
<body data-tool-slug="karo-calculator">

<x-site.contech-header />

<div class="container">
    <div class="tool-header">
        <div class="badge">AI Optimizasyon Motoru</div>
        <h1>Akıllı Karo Hesaplayıcı</h1>
        <p>Mekan ve karo ebatlarını girin, algoritmamız yatay ve dikey döşeme senaryolarını test ederek en düşük fire oranını ve maliyeti bulsun.</p>
    </div>

    <div class="card" id="inputCard">
        <h3 style="font-size: 15px; margin-bottom: 15px;">1. Mekan Ölçüleri</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Mekan Eni (Metre)</label>
                <input type="number" id="roomWidth" class="form-control" placeholder="Örn: 4" value="4" min="1" step="0.1">
            </div>
            <div class="form-group">
                <label>Mekan Boyu (Metre)</label>
                <input type="number" id="roomLength" class="form-control" placeholder="Örn: 5.5" value="5.5" min="1" step="0.1">
            </div>
        </div>

        <h3 style="font-size: 15px; margin-bottom: 15px; margin-top: 10px;">2. Karo / Seramik Ebatları</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Karo Eni (Santimetre)</label>
                <input type="number" id="tileWidth" class="form-control" placeholder="Örn: 60" value="60" min="10" step="5">
            </div>
            <div class="form-group">
                <label>Karo Boyu (Santimetre)</label>
                <input type="number" id="tileLength" class="form-control" placeholder="Örn: 120" value="120" min="10" step="5">
            </div>
        </div>

        <div class="form-group">
            <label>Malzeme Kalitesi (Birim Fiyat Çarpanı)</label>
            <select id="quality" class="form-control">
                <option value="550">Standart Sınıf Seramik</option>
                <option value="950" selected>Premium Sınıf (Rektifiyeli / Granit)</option>
                <option value="1800">Lüks Sınıf (Büyük Ebatlı İthal Porselen)</option>
            </select>
        </div>

        <button type="button" class="btn btn-ai" id="btnAnalyze">✨ AI ile Yerleşimi ve Bütçeyi Optimize Et</button>
    </div>

    <!-- AI PROCESSING SIMULATION -->
    <div class="card processing-box" id="processingBox">
        <div class="loader"></div>
        <div class="process-text" id="processText">Zemin gridi (ızgarası) oluşturuluyor...</div>
        <div class="process-sub">Yatay ve dikey döşeme senaryoları test ediliyor.</div>
    </div>

    <!-- RESULT SECTION -->
    <div class="result-box" id="resultBox">
        <div class="result-label">ANAHTAR TESLİM OPTİMİZE BÜTÇE</div>
        <div class="result-price" id="totalPrice">0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px; position:relative;">
            ⚠️ Bu değerler matematiksel optimizasyon modeliyle hazırlanmıştır. Kesin bütçe ve fire payı saha keşfiyle belirlenir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Net Mekan Alanı</span>
                <strong id="resNetArea">0 m²</strong>
            </div>
            <div class="break-item">
                <span>Gereken Minimum Adet</span>
                <strong id="resTileCount">0 Adet Karo</strong>
            </div>
            <div class="break-item">
                <span>Hesaplanan Fire Oranı</span>
                <strong id="resWaste">%0</strong>
            </div>
            <div class="break-item">
                <span>Brüt Malzeme İhtiyacı</span>
                <strong id="resGrossArea">0 m²</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">💡</div>
            <div class="ai-insight-text">
                <strong>Algoritma Analizi</strong>
                <p id="insightText">Karoları dikey döşemek fireyi minimize ediyor.</p>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Bu bütçe aralığı projeniz için uygun mu?</div>
        <div class="budget-confirm-text">
            Sistemin çıkardığı bu bütçe beklentinizle eşleşiyorsa, yerinde keşif ve net teklif sürecini başlatabilirsiniz.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Bütçe uygun, resmi teklif istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece maliyet hesaplamak istedim.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Projeniz Kainta İnşaat standartlarına uygun görünüyor.</strong>
        <span>Kainta İnşaat uzmanlarının sizinle iletişime geçmesi için bilgilerinizi doğrulayın.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Yerinde Keşif ve Profesyonel Teklif</h3>
        <p>Mühendislerimiz projenizi detaylı inceleyip net fiyatlandırma için sizinle iletişime geçecektir.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="AI Karo Optimizasyon Motoru">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Firma veya Kişi Adı">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Yapılacağı İl / İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: Ankara, Çankaya">
            </div>

            <button type="submit" class="btn btn-submit">Bilgileri Gönder ve Teklif Al →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('btnAnalyze').addEventListener('click', function() {
    const rw = parseFloat(document.getElementById('roomWidth').value);
    const rl = parseFloat(document.getElementById('roomLength').value);
    const tw = parseFloat(document.getElementById('tileWidth').value) / 100; // cm to m
    const tl = parseFloat(document.getElementById('tileLength').value) / 100; // cm to m
    const unitPrice = parseFloat(document.getElementById('quality').value);
    
    if (!rw || !rl || !tw || !tl) {
        alert('Lütfen geçerli ölçüler giriniz.');
        return;
    }

    // UI Değişimi: AI Simülasyonu
    document.getElementById('inputCard').style.display = 'none';
    document.getElementById('processingBox').classList.add('active');

    const texts = [
        "Zemin gridi oluşturuluyor...",
        "Yatay döşeme senaryosu hesaplanıyor...",
        "Dikey döşeme senaryosu test ediliyor...",
        "Fire payı optimize ediliyor...",
        "Kainta İnşaat birim fiyatlarıyla bütçe çıkarılıyor..."
    ];
    let i = 0;
    
    const interval = setInterval(() => {
        i++;
        if(i < texts.length) {
            document.getElementById('processText').innerText = texts[i];
        } else {
            clearInterval(interval);
            calculateOptimization(rw, rl, tw, tl, unitPrice);
        }
    }, 1000); 
});

function calculateOptimization(rw, rl, tw, tl, unitPrice) {
    // Senaryo 1: Standart Dizilim
    const tilesX1 = Math.ceil(rw / tw);
    const tilesY1 = Math.ceil(rl / tl);
    const totalTiles1 = tilesX1 * tilesY1;

    // Senaryo 2: Döndürülmüş Dizilim (Yatay/Dikey değişim)
    const tilesX2 = Math.ceil(rw / tl);
    const tilesY2 = Math.ceil(rl / tw);
    const totalTiles2 = tilesX2 * tilesY2;

    // Optimizasyon Kararı
    let bestTotalTiles = Math.min(totalTiles1, totalTiles2);
    let isRotated = bestTotalTiles === totalTiles2 && totalTiles1 !== totalTiles2;
    
    // Güvenlik Payı (Kırılma vb. için kutu bazlı %5 ekleme algoritması)
    bestTotalTiles = Math.ceil(bestTotalTiles * 1.05);

    const netArea = rw * rl;
    const tileArea = tw * tl;
    const grossArea = bestTotalTiles * tileArea;
    const wastePercent = (((grossArea - netArea) / netArea) * 100).toFixed(1);

    // Maliyet Hesabı (Malzeme + Ortalama İşçilik/Sarfiyat 350 TL)
    const laborAndConsumable = 350; 
    const grandTotal = grossArea * (unitPrice + laborAndConsumable);

    // Ekrana Basma
    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    
    document.getElementById('processingBox').style.display = 'none';
    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('resNetArea').innerText = netArea.toFixed(2) + ' m²';
    document.getElementById('resTileCount').innerText = bestTotalTiles + ' Adet';
    document.getElementById('resGrossArea').innerText = grossArea.toFixed(2) + ' m²';
    document.getElementById('resWaste').innerText = '%' + wastePercent;

    let insight = `Algoritmamız, karoların <strong>${isRotated ? 'Döndürülerek (Dikey/Şaşırtmalı)' : 'Standart (Yatay)'}</strong> döşenmesinin fire oranını düşürdüğünü tespit etti. Bütçeye olası kırılmalar için %5 güvenlik payı eklenmiştir.`;
    document.getElementById('insightText').innerHTML = insight;

    document.getElementById('leadCalculatedData').value = `Net: ${netArea.toFixed(1)}m2 | Gerekli Karo: ${bestTotalTiles} Adet | Fire: %${wastePercent} | Bütçe: ${money.format(grandTotal)} TL`;

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
}

document.getElementById('btnYes').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'block';
    document.getElementById('leadBox').style.display = 'block';
    document.getElementById('leadBox').classList.add('active');
    setTimeout(() => { document.getElementById('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
});

document.getElementById('btnNo').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').innerHTML = `
        <div class="budget-confirm-title" style="color: #101318;">Anlıyoruz.</div>
        <div class="budget-confirm-text" style="margin-bottom: 0;">
            Bu aşamada iletişim bilgilerinizi almıyoruz. Bütçeniz netleştiğinde tekrar teklif isteyebilirsiniz.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
