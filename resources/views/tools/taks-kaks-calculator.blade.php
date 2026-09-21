<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAKS / KAKS (Emsal) Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/taks-kaks-hesaplayici">
    <meta name="description" content="Arsanızın imar durum belgesindeki değerlere göre emsal inşaat alanınızı, brüt inşaat alanınızı ve kat sayınızı hesaplayın.">
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
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tool-header p { color: var(--muted); font-size: 15px; }

        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; background: #fff; transition: .2s; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,184,0,0.15); }
        
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-box.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .result-label { font-size: 12px; color: #a1a1aa; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1px; }
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.highlight { background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.3); }
        .break-item.highlight strong { color: #38bdf8; }

        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(56,189,248,0.1); border-color: #38bdf8; color: #38bdf8; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #38bdf8; background: #f0f9ff; box-shadow: 0 0 0 3px rgba(56,189,248,.10); }

        .qualified-message { display: none; margin-top: 25px; padding: 20px; border-radius: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; text-align: center;}
        .qualified-message strong { display: block; font-size: 15px; margin-bottom: 5px; color: #166534; }
        .qualified-message span { display: block; color: #15803d; font-size: 12px; line-height: 1.5; }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .lead-box.active { display: block; animation: fadeIn 0.5s ease; }
        .lead-box h3 { font-size: 18px; font-weight: 900; margin-bottom: 8px; }
        .lead-box p { font-size: 13px; color: var(--muted); margin-bottom: 20px; line-height: 1.5; }
        
        .btn { width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: .2s; }
        .btn-calc { background: var(--text); color: #fff; margin-top: 10px; }
        .btn-calc:hover { background: #000; }
        .btn-submit { background: var(--accent); color: #000; margin-top: 10px; }
        .btn-submit:hover { background: #e5a400; }

        @media(max-width: 600px) {
            .form-grid, .breakdown-grid, .budget-confirm-actions { grid-template-columns: 1fr; }
            .card, .result-box, .lead-box, .budget-confirm-box { padding: 20px; }
            .result-price { font-size: 32px; }
            .cross-sell-links { flex-direction: column; }
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
<body data-tool-slug="taks-kaks-calculator">

<x-site.contech-header />

<div class="container">
    <div class="tool-header">
        <h1>🏢 TAKS / KAKS (Emsal) Hesaplayıcı</h1>
        <p>Arsanızın imar durum belgesindeki emsal değerlerini girerek taban oturumunu, net/brüt inşaat alanınızı ve kat sayınızı hesaplayın.</p>
    </div>

    <div class="card">
        <div class="form-group">
            <label>Net Arsa Alanı (m²)</label>
            <input type="number" id="plotArea" class="form-control" placeholder="Terkler yapılmış net parsel alanı. Örn: 1000" value="1000" min="50" step="10">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>TAKS (Taban Alanı Katsayısı)</label>
                <input type="number" id="taks" class="form-control" placeholder="Örn: 0.30" value="0.30" min="0.10" max="1.00" step="0.05">
            </div>
            <div class="form-group">
                <label>KAKS / Emsal (Kat Alanı Katsayısı)</label>
                <input type="number" id="kaks" class="form-control" placeholder="Örn: 1.50" value="1.50" min="0.10" step="0.10">
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">İmar ve Yapı Analizini Getir →</button>
    </div>

    <div class="result-box" id="resultBox">
        <div class="result-label">GERÇEKLEŞECEK BRÜT İNŞAAT ALANI YAKLAŞIK</div>
        <div class="result-price" id="grossArea">0 m²</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Planlı Alanlar İmar Yönetmeliği'ne göre emsale dahil edilmeyen (asansör, şaft, ortak alan vb.) %30 pay eklenerek hesaplanmıştır.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Maksimum Taban Alanı (TAKS)</span>
                <strong id="baseArea">0 m²</strong>
            </div>
            <div class="break-item">
                <span>Emsale Dahil Net İnşaat Alanı</span>
                <strong id="netArea">0 m²</strong>
            </div>
            <div class="break-item highlight">
                <span>Çıkarılabilecek Kat Sayısı</span>
                <strong id="floors">0 Kat</strong>
            </div>
            <div class="break-item">
                <span>Emsal Dışı Pay (%30 Yasal Artış)</span>
                <strong id="bonusArea">0 m²</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">🤖</div>
            <div class="ai-insight-text">
                <strong>Mimari Proje Notu</strong>
                <p>Emsal (KAKS) hesabına asansör boşlukları, yangın merdivenleri, tesisat şaftları ve açık çıkmalar yönetmelikteki sınırlar dahilinde girmez. Bu sebeple inşa edeceğiniz ve kaba inşaat maliyetine girecek olan gerçek brüt alanınız her zaman KAKS'tan daha büyük çıkacaktır.</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Arsanızın potansiyelini gördünüz, peki ya kaba inşaatı?</div>
            <div class="cross-sell-text">Hesaplanan brüt inşaat alanı üzerinden yapınızın temel hafriyatını ve saha beton maliyetini anında hesaplayabilirsiniz.</div>
            <div class="cross-sell-links">
                <a href="/araclar/hafriyat-kazi-hesabi">🚜 Hafriyat Maliyeti</a>
                <a href="/araclar/saha-betonu-hesaplayici">🏢 Beton & Demir Metrajı</a>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Mimari proje ve anahtar teslim inşaat teklifi ister misiniz?</div>
        <div class="budget-confirm-text">
            Arsanızın potansiyelini maksimum seviyede kullanacak mimari avan proje çizimleri ve anahtar teslim müteahhitlik hizmetleri için Kainta İnşaat yanınızda.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, mimari proje ve keşif teklifi istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece imar verilerimi hesapladım.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Doğru mimari planlama yatırımınızı katlar.</strong>
        <span>Aşağıdaki formu doldurduğunuzda mimari tasarım ekibimiz arsanıza özel fizibilite için sizinle iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Mimari Fizibilite ve Taahhüt</h3>
        <p>Arsanızın imar durum belgesini (çap) inceleyerek Kainta İnşaat güvencesiyle ruhsat projesi ve kaba/ince inşaat teklifleri hazırlayalım.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="TAKS / KAKS Mimari Analiz">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Firma, Müteahhit veya Arsa Sahibi">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Arsanın Bulunduğu Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: Antalya, Döşemealtı">
            </div>

            <div class="form-group">
                <label>Proje Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Konut mu yapılacak, ticari mi? Kat karşılığı anlaşma var mı?"></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Kainta İnşaat'tan Proje Desteği Al →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const plotArea = parseFloat(document.getElementById('plotArea').value) || 0;
    const taks = parseFloat(document.getElementById('taks').value) || 0;
    const kaks = parseFloat(document.getElementById('kaks').value) || 0;
    
    if (plotArea <= 0 || taks <= 0 || kaks <= 0) {
        alert('Lütfen geçerli arsa ve emsal değerleri giriniz.');
        return;
    }

    // 1. TAKS (Taban Alanı)
    const baseArea = plotArea * taks;
    
    // 2. KAKS / Emsal (Net İnşaat Alanı)
    const netArea = plotArea * kaks;
    
    // 3. Kat Sayısı Tahmini (KAKS / TAKS)
    // Örn: 1.5 / 0.3 = 5 Kat
    const floors = (kaks / taks).toFixed(1);

    // 4. Brüt İnşaat Alanı (+%30 Emsal Harici Pay Yönetmeliği)
    const bonusArea = netArea * 0.30;
    const grossArea = netArea + bonusArea;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 2 });

    document.getElementById('grossArea').innerText = money.format(grossArea) + ' m²';
    document.getElementById('baseArea').innerText = money.format(baseArea) + ' m²';
    document.getElementById('netArea').innerText = money.format(netArea) + ' m²';
    document.getElementById('floors').innerText = floors.replace('.0', '') + ' Kat';
    document.getElementById('bonusArea').innerText = '+ ' + money.format(bonusArea) + ' m²';

    document.getElementById('leadCalculatedData').value = `Arsa: ${plotArea}m2 | Taban(TAKS): ${baseArea}m2 | Net Emsal: ${netArea}m2 | Brüt(Satılabilir): ${grossArea}m2`;

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('leadBox').classList.remove('active');
    document.getElementById('qualifiedMessage').style.display = 'none';
    
    setTimeout(() => { document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
});

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Arsanızın mimari projesini çizdirmeye veya kaba inşaata karar verdiğinizde tekrar görüşmek dileğiyle.
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
